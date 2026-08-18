<?php

namespace Modules\Evenements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Evenements\Models\Transfer;
use Modules\Employees\Models\Employee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Department;
use Modules\Settings\app\Models\WorkLocation;

class TransferController extends Controller
{
    /**
     * Affiche la liste des transferts
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Transfer::with(['employee', 'fromDepartment', 'toDepartment', 'fromLocation', 'toLocation'])
                ->latest();
                
            return DataTables::of($query)
                ->addColumn('action', function($transfer) {
                    return view('evenements::transfers.partials.actions', compact('transfer'));
                })
                ->addColumn('employee_name', function($transfer) {
                    return $transfer->employee ? $transfer->employee->full_name : 'N/A';
                })
                ->addColumn('transfer_details', function($transfer) {
                    $details = [];
                    if ($transfer->from_department_id) {
                        $details[] = 'Département: ' . ($transfer->fromDepartment->name ?? 'N/A') . ' → ' . ($transfer->toDepartment->name ?? 'N/A');
                    }
                    if ($transfer->from_location_id) {
                        $details[] = 'Localisation: ' . ($transfer->fromLocation->name ?? 'N/A') . ' → ' . ($transfer->toLocation->name ?? 'N/A');
                    }
                    if ($transfer->from_position) {
                        $details[] = 'Poste: ' . $transfer->from_position . ' → ' . $transfer->to_position;
                    }
                    return implode('<br>', $details);
                })
                ->rawColumns(['action', 'transfer_details'])
                ->make(true);
        }

        $branches = Branch::where('company_id', auth()->user()->company_id)->get();

        return view('evenements::transfers.index', compact('branches'));
    }

    /**
     * Affiche le formulaire de création d'un transfert
     */
    public function create()
    {
        $employees = Employee::where('company_id', auth()->user()->company_id)->get()
            ->pluck('name', 'employee_id')
            ->sort();
        $departments = Department::where('company_id', auth()->user()->company_id)->get();
        $locations = WorkLocation::where('company_id', auth()->user()->company_id)->get();            
        return view('evenements::transfers.create', compact('employees', 'departments', 'locations'));
    }

    /**
     * Enregistre un nouveau transfert
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_months,employee_id',
            'transfer_date' => 'required|date',
            'transfer_type' => 'required|in:department,location,position',
            'from_department_id' => 'required_if:transfer_type,department|nullable|exists:departments,id',
            'to_department_id' => 'required_if:transfer_type,department|nullable|exists:departments,id',
            'from_location_id' => 'required_if:transfer_type,location|nullable|exists:locations,id',
            'to_location_id' => 'required_if:transfer_type,location|nullable|exists:locations,id',
            'from_position' => 'required_if:transfer_type,position|string|max:255|nullable',
            'to_position' => 'required_if:transfer_type,position|string|max:255|nullable',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'effective_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:pending,approved,rejected,completed',
        ]);

        try {
            DB::beginTransaction();
            
            $transfer = Transfer::create($validated);
            
            // Si le transfert est approuvé, mettre à jour les informations de l'employé
            if ($validated['status'] === 'approved') {
                $this->applyTransfer($transfer);
            }
            
            DB::commit();
            
            return redirect()->route('company.transfers.show', $transfer->id)
                ->with('success', 'Transfert créé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un transfert
     */
    public function show(Transfer $transfer)
    {
        $transfer->load([
            'employee', 
            'fromDepartment', 
            'toDepartment', 
            'fromLocation', 
            'toLocation'
        ]);
        
        return view('evenements::transfers.show', compact('transfer', 'employees', 'departments', 'locations'));
    }

    /**
     * Affiche le formulaire de modification d'un transfert
     */
    public function edit(Transfer $transfer)
    {
        if ($transfer->status === 'completed') {
            return redirect()->route('company.transfers.show', $transfer->id)
                ->with('warning', 'Impossible de modifier un transfert déjà effectué.');
        }
        
        $employees = Employee::active()->with('employee')->get()
            ->pluck('employee.full_name', 'employee_id')
            ->sort();
            
        return view('evenements::transfers.edit', compact('transfer', 'employees'));
    }

    /**
     * Met à jour un transfert existant
     */
    public function update(Request $request, Transfer $transfer)
    {
        if ($transfer->status === 'completed') {
            return back()->with('error', 'Impossible de modifier un transfert déjà effectué.');
        }
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_months,employee_id',
            'transfer_date' => 'required|date',
            'transfer_type' => 'required|in:department,location,position',
            'from_department_id' => 'required_if:transfer_type,department|nullable|exists:departments,id',
            'to_department_id' => 'required_if:transfer_type,department|nullable|exists:departments,id',
            'from_location_id' => 'required_if:transfer_type,location|nullable|exists:locations,id',
            'to_location_id' => 'required_if:transfer_type,location|nullable|exists:locations,id',
            'from_position' => 'required_if:transfer_type,position|string|max:255|nullable',
            'to_position' => 'required_if:transfer_type,position|string|max:255|nullable',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'effective_date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected,completed',
        ]);

        try {
            DB::beginTransaction();
            
            $previousStatus = $transfer->status;
            $transfer->update($validated);
            
            // Si le statut passe à approuvé, appliquer le transfert
            if ($previousStatus !== 'approved' && $validated['status'] === 'approved') {
                $this->applyTransfer($transfer);
            }
            
            DB::commit();
            
            return redirect()->route('company.transfers.show', $transfer->id)
                ->with('success', 'Transfert mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un transfert
     */
    public function destroy(Transfer $transfer)
    {
        if ($transfer->status === 'completed') {
            return back()->with('error', 'Impossible de supprimer un transfert déjà effectué.');
        }
        
        try {
            $transfer->delete();
            return redirect()->route('company.transfers.index')
                ->with('success', 'Transfert supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression du transfert: ' . $e->getMessage());
        }
    }
    
    /**
     * Applique un transfert en mettant à jour les informations de l'employé
     */
    protected function applyTransfer(Transfer $transfer)
    {
        $employee = $transfer->employee;
        
        if (!$employee) {
            throw new \Exception("Employé non trouvé pour ce transfert.");
        }
        
        switch ($transfer->transfer_type) {
            case 'department':
                $employee->department_id = $transfer->to_department_id;
                break;
                
            case 'location':
                $employee->location_id = $transfer->to_location_id;
                break;
                
            case 'position':
                $employee->position = $transfer->to_position;
                break;
        }
        
        $employee->save();
        
        // Marquer le transfert comme complété
        $transfer->update(['status' => 'completed']);
    }
    
    /**
     * Approuve un transfert
     */
    public function approve(Transfer $transfer)
    {
        if ($transfer->status === 'completed') {
            return back()->with('warning', 'Ce transfert a déjà été effectué.');
        }
        
        try {
            DB::beginTransaction();
            
            $this->applyTransfer($transfer);
            
            DB::commit();
            
            return back()->with('success', 'Transfert approuvé et appliqué avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'approbation du transfert: ' . $e->getMessage());
        }
    }
    
    /**
     * Rejette un transfert
     */
    public function reject(Transfer $transfer)
    {
        if ($transfer->status === 'completed') {
            return back()->with('warning', 'Impossible de rejeter un transfert déjà effectué.');
        }
        
        $transfer->update(['status' => 'rejected']);
        
        return back()->with('success', 'Transfert rejeté avec succès.');
    }
}
