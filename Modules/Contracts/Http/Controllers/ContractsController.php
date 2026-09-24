<?php

namespace Modules\Contracts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department; 
use App\Models\Designation;
use App\Models\JobCategorie;
use Modules\Employees\Models\Employee;
use Modules\Contracts\Models\Contract;
use Modules\Contracts\Models\ContractType;
use Modules\Contracts\Models\ContractAttechements;
use Modules\Contracts\Models\ContractAvenant;

class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        
        $query = Contract::where('company_id', $company_id);
        
        // Filtres
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->has('type_id') && $request->type_id != '') {
            // La colonne s'appelle « type_id » : « contract_type_id » n'existe pas dans
            // la table contracts, le filtre par type provoquait donc une erreur SQL.
            $query->where('type_id', $request->type_id);
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $contracts = $query->orderBy('created_at', 'desc')->paginate(10);
        $employees = Employee::where('company_id', $company_id)->get();
        $contractTypes = ContractType::where('company_id', $company_id)->orWhere('type','default')->where('is_active', 1)->get();
        
        return view('contracts::index', compact('contracts', 'employees', 'contractTypes'));
    }

    public function ContractEmployee($id){
        $user = Auth::user();
        $company_id = $user->company_id;
        
        $employee = Employee::where('company_id', $company_id)->where('id', $id)->first();
        $contractTypes = ContractType::where('company_id', $company_id)->orWhere('type','default')->where('is_active', 1)->get();
       
        return view('contracts::create_employee', compact('id','employee', 'contractTypes'));
    }

    /**
     * Show the form for creating a new resource. 
     */
    public function create()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        
        $employees = Employee::where('company_id', $company_id)->get();
        $contractTypes = ContractType::where('company_id', $company_id)->orWhere('type','default')->where('is_active', 1)->get();
        
        return view('contracts::create', compact('employees', 'contractTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        DB::beginTransaction();
        try {
            // Journalisation des données reçues
            \Log::info('Données reçues : ', $request->all());
            
            // Validation des données
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'employee_id' => 'required|exists:employees,id',
                'type_id' => 'required|exists:contract_types,id',
                'start_date' => 'required|date',
                'end_date' => [
                    $this->exigeDateDeFin($request->type_id) ? 'required' : 'nullable',
                    'date',
                    'after_or_equal:start_date',
                ],
                'duration' => 'nullable|string',
                'value' => 'nullable|numeric',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'attachments.*' => 'nullable|file|max:10240', // 10MB max
            ]);
            
            $user = Auth::user();
            
            // Création du contrat
            $contractData = [
                'subject' => $validated['subject'],
                'employee_id' => $validated['employee_id'],
                'type_id' => $validated['type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'duration' => $validated['duration'] ?? null,
                'value' => $validated['value'] ?? null,
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'accept',
                'company_id' => $user->company_id,
                'created_by' => $user->id,
            ];
            
            \Log::info('Données du contrat à enregistrer : ', $contractData);
            
            $contract = Contract::create($contractData);
            
            if (!$contract) {
                throw new \Exception('Échec de la création du contrat');
            }
            
            // Gestion des pièces jointes
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('contracts/' . $contract->id, 'public');
                    
                    $attachment = ContractAttechements::create([
                        'contract_id' => $contract->id,
                        'employee_id' => $validated['employee_id'],
                        'files' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'company_id' => $user->company_id,
                    ]);
                    
                    if (!$attachment) {
                        throw new \Exception('Échec de l\'enregistrement de la pièce jointe');
                    }
                }
            }

            // Mise à jour employé
            $employee = Employee::where('id', $validated['employee_id'])->first();
            $employee->contrat = $contract->id;
            $employee->start_date = $validated['start_date'] ?? null;
            $employee->end_date = $validated['end_date'] ?? null;
            $employee->company_doj = $validated['start_date'] ?? null;
            $employee->save();
            
            DB::commit();
            
            return redirect()->route('company.contracts.show', $contract->id)
                ->with('success', 'Contrat créé avec succès.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Erreur de validation : ', [
                'errors' => $e->validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du contrat : ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du contrat : ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $contract = Contract::with(['employee', 'type', 'attachments', 'avenants'])
            ->where('company_id', $companyId)
            ->findOrFail($id);
        $contractTypes = ContractType::where('company_id', $companyId)->orWhere('type','default')->where('is_active', 1)->get();
        $branches = Branch::where('company_id', $companyId)->where('is_active', 1)->get();
        $departments = Department::where('company_id', $companyId)->where('is_active', 1)->get();
        $designations = Designation::where('company_id', $companyId)->where('is_active', 1)->get();
        $company = Company::find($companyId);
        return view('contracts::show', compact('contract','branches','departments','designations','company','contractTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        $employees = Employee::where('company_id', $user->company_id)->get();
        $contractTypes = ContractType::where('company_id', $user->company_id)->orWhere('type','default')->where('is_active', 1)->get();
       
        return view('contracts::edit', compact('contract', 'employees', 'contractTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'type_id' => 'required|exists:contract_types,id',
            'start_date' => 'required|date',
            'end_date' => [
                $this->exigeDateDeFin($request->type_id) ? 'required' : 'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'duration' => 'nullable|string',
            'value' => 'nullable|numeric',
            'status' => 'required|in:pending,accept,expired',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        $contract->subject = $request->subject;
        $contract->employee_id = $request->employee_id;
        $contract->type_id = $request->type_id;
        $contract->start_date = $request->start_date;
        $contract->end_date = $request->end_date;
        $contract->duration = $request->duration;
        $contract->value = $request->value;
        $contract->status = $request->status;
        $contract->description = $request->description;
        $contract->notes = $request->notes;
        $contract->updated_by = $user->id;
        $contract->save();
        
        // Gestion des pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('contracts/' . $contract->id, 'public');
                
                $attachment = new ContractAttechements();
                $attachment->contract_id = $contract->id;
                $attachment->employee_id = $request->employee_id;
                $attachment->file_name = $file->getClientOriginalName();
                $attachment->files = $path;
                $attachment->save();
            }
        }

        // Mise à jour employé
        $employee = Employee::where('id', $contract->employee_id)->first();
        $employee->contrat = $contract->id;
        $employee->start_date = $request->start_date ?? null;
        $employee->end_date = $request->end_date ?? null;
        $employee->save();
        
        return redirect()->route('company.contracts.show', $contract->id)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        // Supprimer les pièces jointes
        foreach ($contract->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
        
        // Supprimer les avenants
        foreach ($contract->avenants as $avenant) {
            if ($avenant->file_path) {
                Storage::disk('public')->delete($avenant->file_path);
            }
            $avenant->delete();
        }
        
        $contract->delete();
        
        return redirect()->route('company.contracts.index')
            ->with('success', 'Contrat supprimé avec succès.');
    }
    
    /**
     * Ajouter une pièce jointe à un contrat
     */
    public function addAttachment(Request $request, $id)
    {
        $request->validate([
            'attachment' => 'required|file|max:10240', // 10MB max
        ], [
            'attachment.required' => 'Veuillez sélectionner un fichier avant d\'ajouter une pièce jointe.',
            'attachment.file' => 'Le fichier sélectionné n\'est pas valide.',
            'attachment.max' => 'La taille du fichier ne doit pas dépasser 10 Mo.',
        ]);
        
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        $file = $request->file('attachment');
        $path = $file->store('contracts/' . $contract->id, 'public');
        
        $attachment = new ContractAttechements();
        $attachment->contract_id = $contract->id;
        $attachment->file_name = $file->getClientOriginalName();
        $attachment->files = $path;
        $attachment->uploaded_by = $user->id;
        $attachment->save();
        
        return redirect()->back()->with('success', 'Pièce jointe ajoutée avec succès.');
    }
    
    /**
     * Télécharger une pièce jointe
     */
    public function downloadAttachment($id)
    {
        $user = Auth::user();
        $attachment = ContractAttechements::findOrFail($id);
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($attachment->contract_id);

        // Vérifier si file_path est valide
        if (is_null($attachment->file_path) || !Storage::disk('public')->exists($attachment->file_path)) {
            return response()->json(['error' => 'Le fichier demandé n\'existe pas.'], 404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
    
    /**
     * Supprimer une pièce jointe
     */
    public function deleteAttachment($id)
    {
        $user = Auth::user();
        $attachment = ContractAttechements::findOrFail($id);
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($attachment->contract_id);
        
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        
        return redirect()->back()->with('success', 'Pièce jointe supprimée avec succès.');
    }
    
    /**
     * Ajouter un avenant à un contrat
     */
    public function addAvenant(Request $request, $id)
    {
        $request->validate([
			'contrat_id'     => 'required',
			'amendment_type' => 'required',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        $avenant = new ContractAvenant();
        $avenant->contract_id = $request->contrat_id;
        $avenant->employee_id = $request->emp_id;
		$avenant->type_avenant = $request->amendment_type;
        $avenant->duration = $request->duration ?? null;
		$avenant->amount    = $request->salaire_minima_mensuel;
		$avenant->description = $request->description;
		$avenant->company_id = $user->company_id;
		$avenant->save();
        
        // Gestion des pièces jointes
        if ($request->hasFile('attachment')) {           
            foreach ($request->file('attachment') as $file) {
                $path = $file->store('contracts/' . $contract->id . '/avenants', 'public');
                
                $attachment = ContractAttechements::create([
                    'contract_id' => $avenant->id,
                    'employee_id' => $request->emp_id,
                    'files' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'company_id' => $user->company_id,
                ]);
                
                if (!$attachment) {
                    throw new \Exception('Échec de l\'enregistrement de la pièce jointe');
                }
            }
        }

        if ($request->amendment_type == 'Reconduction'){
            $contrat = Contract::where('id','=', $request->contrat_id)->first();
            $contrat->type_id = $request->contract_types_id;
            $contrat->start_date = $request->contrat_start;
            $contrat->end_date = $request->contrat_end;
            $contrat->value = $request->value;
            $contrat->duration = $request->duration;
            $contrat->status  = 'accept';
            $contrat->description = $request->avenant_description;
            $contrat->save();
 
            $employees = Employee::where('id','=', $request->emp_id)->first();
            $employees->start_date = $request->contrat_start;
            $employees->end_date = $request->contrat_end;
            $employees->categorie = $request->category_job_id;
            $employees->branch_id = $request->branch_id;
            $employees->department_id = $request->department_id;
            $employees->designation_id = $request->designation_id; 
            $employees->contrat = $request->contract_types_id;
            $employees->sous_categorie = $request->category_id;
            $employees->salary_horaire = $request->salaire_minima_horaire;
            $employees->salary = $request->salaire_minima_mensuel;
            $employees->is_active = 1;
            $employees->save();
        } else if ($request->amendment_type === 'Augmentation de salaire' || $request->amendment_type === 'Réduction de salaire'){
            $contrat = Contract::where('id','=', $request->contrat_id)->first();
            $contrat->type_id = $request->contract_types_id;
            $contrat->value = $request->value;
            $contrat->duration = $request->duration;
            $contrat->description = $request->avenant_description;
            $contrat->save();
 
            $employees = Employee::where('id','=',$request->emp_id)->first();
            $employees->categorie = $request->category_job_id;
            $employees->sous_categorie = $request->category_id;
            $employees->salary_horaire = $request->salaire_minima_horaire;
            $employees->salary = $request->salaire_minima_mensuel;
            $employees->save();
        } 
        
        return redirect()->back()->with('success', 'Avenant ajouté avec succès.');
    }

    public function editAvenant($id)
    {
        $user = Auth::user();
        $avenant = ContractAvenant::where('company_id', $user->company_id)->findOrFail($id);
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($avenant->contract_id);
        
        return view('contracts::avenant.edit', compact('avenant', 'contract'));
    }

    public function destroyAvenant($id)
    {
        $user = Auth::user();
        $avenant = ContractAvenant::where('company_id', $user->company_id)->findOrFail($id);
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($avenant->contract_id);
        
        $avenant->delete();
        
        return redirect()->back()->with('success', 'Avenant supprimé avec succès.');
    }
    
    /**
     * Télécharger un avenant
     */
    public function downloadAvenant($id)
    {
        $user = Auth::user();
        $avenant = ContractAvenant::findOrFail($id);
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($avenant->contract_id);
        
        return Storage::disk('public')->download($avenant->file_path, $avenant->file_name);
    }
    
    /**
     * Page de gestion des signatures
     */
    public function signature($id)
    {
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        return view('contracts::signature', compact('contract'));
    }
    
    /**
     * Enregistrer une signature
     */
    /**
     * Un contrat à durée déterminée ne peut pas être enregistré sans date de fin.
     * On se fie au libellé du type plutôt qu'à son identifiant, qui varie d'une
     * entreprise à l'autre.
     */
    private function exigeDateDeFin($typeId): bool
    {
        $nom = mb_strtolower((string) optional(ContractType::find($typeId))->name);

        if ($nom === '' || str_contains($nom, 'indétermin') || str_contains($nom, 'indetermin') || str_contains($nom, 'cdi')) {
            return false;
        }

        return str_contains($nom, 'détermin') || str_contains($nom, 'determin') || str_contains($nom, 'cdd');
    }

    public function saveSignature(Request $request, $id)
    {
        // Seul le salarié signe le contrat.
        $request->validate([
            'signature_type' => 'required|in:employee',
            'signature_data' => 'required|string',
        ]);
        
        $user = Auth::user();
        $contract = Contract::where('company_id', $user->company_id)->findOrFail($id);
        
        // Convertir la signature base64 en fichier
        $image_parts = explode(";base64,", $request->signature_data);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        // Même convention que les logos, signatures et cachets de l'entreprise :
        // on écrit directement dans public/storage, qui est servi tel quel.
        $signature_dir = public_path('storage/contracts/' . $contract->id . '/signatures');

        if (!is_dir($signature_dir)) {
            mkdir($signature_dir, 0755, true);
        }

        $signature_name = 'employee_' . time() . '.' . $image_type;
        file_put_contents($signature_dir . DIRECTORY_SEPARATOR . $signature_name, $image_base64);

        // La table contracts ne porte pas de colonne de date de signature.
        $contract->employee_signature = 'contracts/' . $contract->id . '/signatures/' . $signature_name;
        $contract->save();
        
        return redirect()->route('company.contracts.show', $contract->id)
            ->with('success', 'Signature enregistrée avec succès.');
    } 
    
    /**
     * Afficher la page des types de contrat
     */
    public function contractTypes()
    {
        $user = Auth::user();
        $contracts = Contract::where('company_id', $user->company_id)->where('status', 'accept')->get();
        $contractTypes = ContractType::where('company_id', $user->company_id)->orWhere('type','default')->where('is_active', 1)
        ->paginate(10);
        
        return view('contracts::contract-types', compact('contractTypes', 'contracts'));
    }
    
    /**
     * Ajouter un type de contrat
     */
    public function storeContractType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $user = Auth::user();
        
        $contractType = new ContractType();
        $contractType->name = $request->name;
        $contractType->type = 'created';
        $contractType->is_active = 1;
        $contractType->company_id = $user->company_id;
        $contractType->save();
        
        return redirect()->route('company.contracts.types')
            ->with('success', 'Type de contrat ajouté avec succès.');
    }
    
    /**
     * Mettre à jour un type de contrat
     */
    public function updateContractType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|in:1,0',
        ]);
        
        $user = Auth::user();
        $contractType = ContractType::where('company_id', $user->company_id)->findOrFail($id);
        
        $contractType->name = $request->name;
        $contractType->is_active = $request->is_active;
        $contractType->save();
        
        return redirect()->route('company.contracts.types')
            ->with('success', 'Type de contrat mis à jour avec succès.');
    }
    
    /**
     * Supprimer un type de contrat
     */
    public function destroyContractType($id)
    {
        $user = Auth::user();

        // Les types « default » sont communs à toutes les entreprises (company_id à NULL).
        // Le filtre sur company_id ne les trouvait pas : la suppression échouait sur un 404
        // brut au lieu d'expliquer le refus. On les écarte explicitement.
        $contractType = ContractType::findOrFail($id);

        if ($contractType->type === 'default') {
            return redirect()->route('company.contracts.types')
                ->with('error', 'Les types de contrat par défaut ne peuvent pas être supprimés.');
        }

        if ($contractType->company_id !== $user->company_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier si des contrats utilisent ce type
        $contractCount = $contractType->contracts()->count();
        if ($contractCount > 0) {
            return redirect()->route('company.contracts.types')
                ->with('error', 'Ce type de contrat ne peut pas être supprimé car il est utilisé par ' . $contractCount . ' contrat(s).');
        }
        
        $contractType->delete();
        
        return redirect()->route('company.contracts.types')
            ->with('success', 'Type de contrat supprimé avec succès.');
    }   
}
