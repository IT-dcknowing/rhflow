<?php

namespace Modules\Time\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Modules\Time\Models\TimeSheet;
use Modules\Employees\Models\Employee;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use Carbon\Carbon;

class AbsenceController extends Controller
{  
    /**
     * Afficher la liste des absences  
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
       
        $periode = null;
        if ($request->has("periode_id")) {
            // Une période supprimée ne doit pas produire un 404 : on retombe sur
            // l'écran de sélection de période avec un message.
            $periode = PaiePeriode::with("exercice")
                ->where("company_id", Auth::user()->company_id)
                ->find($request->periode_id);

            if (!$periode) {
                session()->flash("error", "Cette période de paie n'existe plus ou n'appartient pas à votre entreprise.");
            }
            // Récupérer le mois sélectionné ou le mois en cours.
            // Sans période valide, on retombe sur le mois courant plutôt que de planter.
            $selectedMonth = $request->input(
                'month',
                $periode ? $periode->date_debut->format('Y-m') : date('Y-m')
            );
        }else{
            // Récupérer le mois sélectionné ou le mois en cours
            $selectedMonth = $request->input('month', date('Y-m'));
        }
        
        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Récupérer les employés en fonction des permissions
        $user = Auth::user();
        $employees = [];
        
        if ($user->type == 'employee') {
            // Si l'utilisateur est un employé, on ne récupère que ses propres absences
            $employee = $user->employee;
            $absences = TimeSheet::where('employee_id', $employee->id ?? 0)
                ->where('date', 'LIKE', "%$selectedMonth%")
                ->orderBy('date', 'desc')
                ->get();
        } else {
            // Si l'utilisateur est un administrateur ou un manager, on récupère tous les employés
            $employees = Employee::where('company_id', $user->company_id)
                ->get()
                ->pluck('name', 'id');
                
            // Récupérer les absences en fonction des filtres
            $absences = TimeSheet::query();
            
            // Filtrer par mois
            $absences->where('date', 'LIKE', "%$selectedMonth%");
            
            // Si l'utilisateur n'est pas un super admin, on filtre par entreprise
            if ($user->type != 'super admin') {
                $absences->where('company_id', $user->company_id);
            }
            
            $absences = $absences->orderBy('date', 'desc')->get();
        }
        
        // Variables pour le formulaire
        $workhours = 8; // Valeur par défaut
        $workdays = 1; // Valeur par défaut
        
        return view('time::absences.index', compact(
            'absences', 
            'employees', 
            'selectedMonth', 
            'currentYear',
            'currentMonth',
            'workhours',
            'workdays',
            'periode',
            'exercices'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'une absence
     */
    public function create()
    {
        // Cette méthode n'est plus utilisée car on utilise un modal
        return redirect()->route('absences.index');
    }
    
    /**
     * Enregistrer une nouvelle absence
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'periode_id' => 'required|exists:paie_periodes,id',
            'date' => 'required|date',
            'arrival_date' => 'required|date|after_or_equal:date',
            'hours' => 'required|numeric|min:0',
            'days' => 'required|numeric|min:0',
            'motif_justify' => 'required|in:Oui,Non',
            'type_permis' => 'required_if:motif_justify,Oui',
            'remark' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'month' => 'required|string',
        ]);
        
        try {
            // Vérifier si l'employé existe
            $employee = Employee::findOrFail($validatedData['employee_id']);
            
            // Gérer le téléchargement du document
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('documents/absences', 'public');
            }
            
            // Créer l'absence
            $absence = new TimeSheet();
            $absence->employee_id = $validatedData['employee_id'];
            $absence->periode_id = $validatedData['periode_id'];
            $absence->date = $validatedData['date'];
            $absence->arrival_date = $validatedData['arrival_date'];
            $absence->hours = $validatedData['hours'];
            $absence->retenue = $validatedData['days'];
            $absence->motif_justify = $validatedData['motif_justify'];
            $absence->type_permis = $validatedData['motif_justify'] == 'Oui' ? $validatedData['type_permis'] : null;
            $absence->remark = $validatedData['remark'] ?? null;
            $absence->document = $documentPath;
            $absence->monthpaie = $validatedData['month'];
            $absence->company_id = Auth::user()->company_id;
            $absence->statut = 'pending'; // Par défaut, l'absence est en attente de validation
            $absence->save();
            
            return redirect()->back()->with("success", "Absence enregistrée avec succès.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with(["success","Une erreur est survenue lors de l\'enregistrement de l\'absence." . ' ' . $e->getMessage()]);
        }
    }
    
    /**
     * Afficher les détails d'une absence
     */
    public function show($id)
    {
        $absence = TimeSheet::with('employee')->findOrFail($id);
        
        return view('time::absences.modals.show', [
            'absence' => $absence
        ]);
    }
    
    /**
     * Afficher le formulaire de modification d'une absence
     */
    public function edit($id)
    {
        $absence = TimeSheet::with('employee')->findOrFail($id);
        
        // Récupérer la liste des employés pour le select
        $employees = [];
        if (Auth::user()->type != 'employee') {
            $employees = Employee::where('company_id', Auth::user()->company_id)
                ->get()
                ->pluck('name', 'id');
        }
        
        return view('time::absences.modals.edit', [
            'absence' => $absence,
            'employees' => $employees
        ]);
    }
    
    /**
     * Mettre à jour une absence
     */
    public function update(Request $request, $id)
    {
        $absence = TimeSheet::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($absence);
        
        // Validation des données
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'arrival_date' => 'required|date|after_or_equal:date',
            'hours' => 'required|numeric|min:0',
            'days' => 'required|numeric|min:0',
            'motif_justify' => 'required|in:Oui,Non',
            'type_permis' => 'required_if:motif_justify,Oui',
            'remark' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        try {
            // Gérer le téléchargement du document s'il est fourni
            if ($request->hasFile('document')) {
                // Supprimer l'ancien document s'il existe
                if ($absence->document && Storage::disk('public')->exists($absence->document)) {
                    Storage::disk('public')->delete($absence->document);
                }
                
                // Enregistrer le nouveau document
                $documentPath = $request->file('document')->store('documents/absences', 'public');
                $validatedData['document'] = $documentPath;
            }
            
            // Mettre à jour l'absence
            $validatedData['type_permis'] = $validatedData['motif_justify'] == 'Oui' ? $validatedData['type_permis'] : null;
            $absence->retenue = $validatedData['days'];
            $absence->update($validatedData);
            
            return redirect()->back()->with("success","Absence mise à jour avec succès.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with(["success","Une erreur est survenue lors de la mise à jour de l\'absence." . ' ' . $e->getMessage()]);
        }
    }
    
    /**
     * Supprimer une absence
     */
    public function destroy($id)
    {
        $absence = TimeSheet::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($absence);
        
        try {
            // Supprimer le document associé s'il existe
            if ($absence->document && Storage::disk('public')->exists($absence->document)) {
                Storage::disk('public')->delete($absence->document);
            }
            
            // Supprimer l'absence
            $absence->delete();
            
            return redirect()->back()->with('success','Absence supprimée avec succès.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('success','Une erreur est survenue lors de la suppression de l\'absence.' . ' ' . $e->getMessage());
        }
    }
    
    /**
     * Supprimer le document d'une absence
     */
    public function removeDocument($id)
    {
        $absence = TimeSheet::findOrFail($id);
        
        // Vérifier les autorisations
        $this->checkPermissions($absence);
        
        try {
            // Vérifier si un document existe
            if ($absence->document && Storage::disk('public')->exists($absence->document)) {
                // Supprimer le fichier
                Storage::disk('public')->delete($absence->document);
                
                // Mettre à jour l'enregistrement
                $absence->document = null;
                $absence->save();
                
                return redirect()->back()->with('success','Document supprimé avec succès.');
            }
            
            return redirect()->back()->with(['success','Aucun document trouvé pour cette absence.']);
            
        } catch (\Exception $e) {
            return redirect()->back()->with(['success','Une erreur est survenue lors de la suppression du document.' . ' ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $absence = TimeSheet::findOrFail($id);
            $status = $request->input('status');
            
            // Validation du statut
            if (!in_array($status, ['pending', 'approved', 'rejected'])) {
                return redirect()->back()->with('error', 'Statut invalide');
            }

            $absence->statut = $status;
            $absence->save();

            // Ici vous pouvez ajouter une notification par email si nécessaire

            return redirect()->back()->with('success', 'Statut mis à jour avec succès');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    /**
     * Vérifier les autorisations de l'utilisateur
     */
    private function checkPermissions($absence)
    {
        $user = Auth::user();
        
        // Si l'utilisateur est un employé, il ne peut accéder qu'à ses propres absences
        if ($user->type == 'employee') {
            $employeeId = $user->employee->id ?? 0;
            if ($absence->employee_id != $employeeId) {
                abort(403, 'Accès non autorisé.');
            }
        }
        // Si l'utilisateur est un manager ou admin, il ne peut accéder qu'aux absences de son entreprise
        elseif ($user->type != 'super admin') {
            if ($absence->company_id != $user->company_id) {
                abort(403, 'Accès non autorisé.');
            }
        }
    }
}
