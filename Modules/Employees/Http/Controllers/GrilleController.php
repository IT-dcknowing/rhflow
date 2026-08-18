<?php

namespace Modules\Employees\Http\Controllers;




use App\Http\Controllers\Controller;
use Modules\Employees\Models\Employee;
use App\Models\Plan;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\JobCategorie;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\PaymentType;
use App\Models\Sector;
use App\Models\SecteurBatiment;
use App\Models\SecteurBatimentEmploye;
use App\Models\SecteurIndusAgent;
use App\Models\SecteurIndusCadre;
use App\Models\SecteurIndustrielEmploye;
use App\Models\SecteurIndustrielOuvrier;
use App\Models\SecteurIndusCauffeur;
use App\Models\SecteurAgriAutreChauffeur;
use App\Models\SecteurAgriAutreEmploye;
use App\Models\SecteurAgriAutreOuvrier;
use App\Models\SecteurAgriCrcChauffeur;
use App\Models\SecteurAgriCrcEmploye;
use App\Models\SecteurAgriCrcOuvrier;
use App\Models\SecteurAssurancesAgent;
use App\Models\SecteurAssurancesEmploye;
use App\Models\SecteurBanqueAgent;
use App\Models\SecteurBanqueEmploye;
use App\Models\SecteurBatimentAgent;
use App\Models\SecteurBatimentCadre;
use App\Models\SecteurBatimentChauffeur;
use App\Models\SecteurCommerceAgent;
use App\Models\SecteurCommerceCadre;
use App\Models\SecteurCommerceEmploye;
use App\Models\SecteurDockersEmploye;
use App\Models\SecteurElevageChauffeur;
use App\Models\SecteurElevageEmploye;
use App\Models\SecteurElevageOuvrier;
use App\Models\SecteurForestierChauffeur;
use App\Models\SecteurForestierEmploye;
use App\Models\SecteurForestierOuvrier;
use App\Models\SecteurHotelleri;
use App\Models\SecteurHotellerieMaitrise;
use App\Models\SecteurHotellerisCadre;
use App\Models\SecteurIndustrielAgriAgent;
use App\Models\SecteurIndustrielAgriCadre;
use App\Models\SecteurIndustrielAgriChauffeur;
use App\Models\SecteurIndustrielAgriEmploye;
use App\Models\SecteurIndustrielAgriOuvrier;
use App\Models\SecteurIndustrielBoisAgent;
use App\Models\SecteurIndustrielBoisCadre;
use App\Models\SecteurIndustrielBoisChauffeur;
use App\Models\SecteurIndustrielBoisEmploye;
use App\Models\SecteurIndustrielBoisOuvrier;
use App\Models\SecteurIndustrielPolyAgent;
use App\Models\SecteurIndustrielPolyCadre;
use App\Models\SecteurIndustrielPolyEmploye;
use App\Models\SecteurIndustrielPolyOuvrier;
use App\Models\SecteurIndustrielSucreAgent;
use App\Models\SecteurIndustrielSucreCadre;
use App\Models\SecteurIndustrielSucreChauffeur;
use App\Models\SecteurIndustrielSucreEmploye;
use App\Models\SecteurIndustrielSucreOuvrier;
use App\Models\SecteurIndustrielTextAgent;
use App\Models\SecteurIndustrielTextCadre;
use App\Models\SecteurIndustrielTextChauffeur;
use App\Models\SecteurIndustrielTextEmploye;
use App\Models\SecteurIndustrielTextOuvrier;
use App\Models\SecteurIndustrielThonAgent;
use App\Models\SecteurIndustrielThonCadre;
use App\Models\SecteurIndustrielThonChauffeur;
use App\Models\SecteurIndustrielThonEmploye;
use App\Models\SecteurIndustrielThonOuvrier;
use App\Models\SecteurMaisonEmploye;
use App\Models\SecteurMaritimeCapit;
use App\Models\SecteurMaritimeChefMeca;
use App\Models\SecteurMaritimeMachine;
use App\Models\SecteurMaritimeMaitre;
use App\Models\SecteurMaritimeMatelo;
use App\Models\SecteurMaritimePoly;
use App\Models\SecteurMaritimeSecondCapit;
use App\Models\SecteurMaritimeSecondMeca;
use App\Models\SecteurNettoyageChauffeur;
use App\Models\SecteurNettoyageEmploye;
use App\Models\SecteurNettoyageOuvrier;
use App\Models\SecteurPechesBosc;
use App\Models\SecteurPechesBrevet;
use App\Models\SecteurPechesCapit;
use App\Models\SecteurPechesChefMoteur;
use App\Models\SecteurPechesCotiereBosco;
use App\Models\SecteurPechesCotiereCapi;
use App\Models\SecteurPechesCotiereCapisCapa;
use App\Models\SecteurPechesCotiereEleve;
use App\Models\SecteurPechesCotiereMatlotSimpl;
use App\Models\SecteurPechesCotiereMatlot;
use App\Models\SecteurPechesCotiereMeca;
use App\Models\SecteurPechesCotiereNoviece;
use App\Models\SecteurPechesCotiereSecondBoco;
use App\Models\SecteurPechesEleve;
use App\Models\SecteurPechesGraisseur;
use App\Models\SecteurPechesLargesBoscoElec;
use App\Models\SecteurPechesLargesCuisto;
use App\Models\SecteurPechesLargesEleve;
use App\Models\SecteurPechesLargesGraisse;
use App\Models\SecteurPechesLargesMatlot;
use App\Models\SecteurPechesLargesMatlotsSimple;
use App\Models\SecteurPechesLargesNovice;
use App\Models\SecteurPechesLargesOffPont;
use App\Models\SecteurPechesLargesSecondBosco;
use App\Models\SecteurPechesMecani;
use App\Models\SecteurPechesNovice;
use App\Models\SecteurPechesOffPon;
use App\Models\SecteurPechesSbrevet;
use App\Models\SecteurPechesSceonBosco;
use App\Models\SecteurPechesSconCapit;
use App\Models\SecteurPechesSconMeca;
use App\Models\SecteurPetroDistAgent;
use App\Models\SecteurPetroDistCadre;
use App\Models\SecteurPetroDistChauffeur;
use App\Models\SecteurPetroDistEmploye;
use App\Models\SecteurPetroProdAgent;
use App\Models\SecteurPetroProdCadre;
use App\Models\SecteurPetroProdChauffeur;
use App\Models\SecteurPetroProdEmploye;
use App\Models\SecteurPetroProdOuvrier;
use App\Models\SecteurSecuriteChauffeur;
use App\Models\SecteurSecuriteEmploye;
use App\Models\SecteurTourisme;
use App\Models\SecteurTourismsMatrise;
use App\Models\SecteurTourismsCadre;
use App\Models\SecteurTransportAgent;
use App\Models\SecteurTransportCadre;
use App\Models\SecteurTransportChauffeur;
use App\Models\SecteurTransportEmploye;
use App\Models\SecteurTransportOuvrier;
use App\Models\SecteurTrpsAerienAgent;
use App\Models\SecteurTrpsAerienCadre;
use App\Models\SecteurTrpsAerienCadresSup;
use App\Models\SecteurTrpsAerienOuvrier;
use App\Models\SecteurTrpsFondAgent;
use App\Models\SecteurTrpsFondCadre;
use App\Models\SecteurTrpsFondEmploye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class GrilleController extends Controller
{
    public function index()
    {
        $tables = [
            'SecteurBatiment',
            'SecteurBatimentEmploye',
            'SecteurIndusAgent',
            'SecteurIndusCadre',
            'SecteurIndustrielEmploye',
            'SecteurIndustrielOuvrier',
            'SecteurIndusCauffeur',
            'SecteurAgriAutreChauffeur',
            'SecteurAgriAutreEmploye',
            'SecteurAgriAutreOuvrier',
            'SecteurAgriCrcChauffeur',
            'SecteurAgriCrcEmploye',
            'SecteurAgriCrcOuvrier',
            'SecteurAssurancesAgent',
            'SecteurAssurancesEmploye',
            'SecteurBanqueAgent',
            'SecteurBanqueEmploye',
            'SecteurBatimentAgent',
            'SecteurBatimentCadre',
            'SecteurBatimentChauffeur',
            'SecteurCommerceAgent',
            'SecteurCommerceCadre',
            'SecteurCommerceEmploye',
            'SecteurDockersEmploye',
            'SecteurElevageChauffeur',
            'SecteurElevageEmploye',
            'SecteurElevageOuvrier',
            'SecteurForestierChauffeur',
            'SecteurForestierEmploye',
            'SecteurForestierOuvrier',
            'SecteurHotelleri',
            'SecteurHotellerieMaitrise',
            'SecteurHotellerisCadre',
            'SecteurIndustrielAgriAgent',
            'SecteurIndustrielAgriCadre',
            'SecteurIndustrielAgriChauffeur',
            'SecteurIndustrielAgriEmploye',
            'SecteurIndustrielAgriOuvrier',
            'SecteurIndustrielBoisAgent',
            'SecteurIndustrielBoisCadre',
            'SecteurIndustrielBoisChauffeur',
            'SecteurIndustrielBoisEmploye',
            'SecteurIndustrielBoisOuvrier',
            'SecteurIndustrielPolyAgent',
            'SecteurIndustrielPolyCadre',
            'SecteurIndustrielPolyEmploye',
            'SecteurIndustrielPolyOuvrier',
            'SecteurIndustrielSucreAgent',
            'SecteurIndustrielSucreCadre',
            'SecteurIndustrielSucreChauffeur',
            'SecteurIndustrielSucreEmploye',
            'SecteurIndustrielSucreOuvrier',
            'SecteurIndustrielTextAgent',
            'SecteurIndustrielTextCadre',
            'SecteurIndustrielTextChauffeur',
            'SecteurIndustrielTextEmploye',
            'SecteurIndustrielTextOuvrier',
            'SecteurIndustrielThonAgent',
            'SecteurIndustrielThonCadre',
            'SecteurIndustrielThonChauffeur',
            'SecteurIndustrielThonEmploye',
            'SecteurIndustrielThonOuvrier',
            'SecteurMaisonEmploye',
            'SecteurMaritimeCapit',
            'SecteurMaritimeChefMeca',
            'SecteurMaritimeMachine',
            'SecteurMaritimeMaitre',
            'SecteurMaritimeMatelo',
            'SecteurMaritimePoly',
            'SecteurMaritimeSecondCapit',
            'SecteurMaritimeSecondMeca',
            'SecteurNettoyageChauffeur',
            'SecteurNettoyageEmploye',
            'SecteurNettoyageOuvrier',
            'SecteurPechesBosc',
            'SecteurPechesBrevet',
            'SecteurPechesCapit',
            'SecteurPechesChefMoteur',
            'SecteurPechesCotiereBosco',
            'SecteurPechesCotiereCapi',
            'SecteurPechesCotiereCapisCapa',
            'SecteurPechesCotiereEleve',
            'SecteurPechesCotiereMatlotSimpl',
            'SecteurPechesCotiereMatlot',
            'SecteurPechesCotiereMeca',
            'SecteurPechesCotiereNoviece',
            'SecteurPechesCotiereSecondBoco',
            'SecteurPechesEleve',
            'SecteurPechesGraisseur',
            'SecteurPechesLargesBoscoElec',
            'SecteurPechesLargesCuisto',
            'SecteurPechesLargesEleve',
            'SecteurPechesLargesGraisse',
            'SecteurPechesLargesMatlot',
            'SecteurPechesLargesMatlotsSimple',
            'SecteurPechesLargesNovice',
            'SecteurPechesLargesOffPont',
            'SecteurPechesLargesSecondBosco',
            'SecteurPechesMecani',
            'SecteurPechesNovice',
            'SecteurPechesOffPon',
            'SecteurPechesSbrevet',
            'SecteurPechesSceonBosco',
            'SecteurPechesSconCapit',
            'SecteurPechesSconMeca',
            'SecteurPetroDistAgent',
            'SecteurPetroDistCadre',
            'SecteurPetroDistChauffeur',
            'SecteurPetroDistEmploye',
            'SecteurPetroProdAgent',
            'SecteurPetroProdCadre',
            'SecteurPetroProdChauffeur',
            'SecteurPetroProdEmploye',
            'SecteurPetroProdOuvrier',
            'SecteurSecuriteChauffeur',
            'SecteurSecuriteEmploye',
            'SecteurTourisme',
            'SecteurTourismsMatrise',
            'SecteurTourismsCadre',
            'SecteurTransportAgent',
            'SecteurTransportCadre',
            'SecteurTransportChauffeur',
            'SecteurTransportEmploye',
            'SecteurTransportOuvrier',
            'SecteurTrpsAerienAgent',
            'SecteurTrpsAerienCadre',
            'SecteurTrpsAerienCadresSup',
            'SecteurTrpsAerienOuvrier',
            'SecteurTrpsFondAgent',
            'SecteurTrpsFondCadre',
            'SecteurTrpsFondEmploye',
        ];

        // Récupérez les informations sur le secteur
        $secteur_id = Company::where('user_id', Auth::user()->id)->first()->industry;
        $secteurs = Sector::where('slug', $secteur_id)->first();

        $data = [];
        $dateCreate = [];

        foreach ($tables as $table) {
            $model = app("App\\Models\\$table");
            $result = $model->where(function ($query) use ($secteurs) {
                $query->where('id_secteur', $secteurs->id)
                    ->where('company_id', NULL);
            })->orderby('categorie')->get();
            // Stockez les données dans un tableau associatif
            $data[$table] = $result;
        }

        foreach ($tables as $table) {
            $model = app("App\\Models\\$table");
            $result = $model->where(function ($query) use ($secteurs) {
                $query->where('company_id', Auth::user()->company_id);
            })->orderby('categorie')->get();
            // Stockez les données dans un tableau associatif
            $dateCreate[$table] = $result;
        }

        $typeposte = JobCategorie::where('id_secteur', $secteurs->id)->get();

        return view('employees::grille.index', compact('data', 'dateCreate', 'secteurs', 'typeposte'));
    }

    public function create($id)
    {
        $tables = [
            'SecteurBatiment',
            'SecteurBatimentEmploye',
            'SecteurIndusAgent',
            'SecteurIndusCadre',
            'SecteurIndustrielEmploye',
            'SecteurIndustrielOuvrier',
            'SecteurIndusCauffeur',
            'SecteurAgriAutreChauffeur',
            'SecteurAgriAutreEmploye',
            'SecteurAgriAutreOuvrier',
            'SecteurAgriCrcChauffeur',
            'SecteurAgriCrcEmploye',
            'SecteurAgriCrcOuvrier',
            'SecteurAssurancesAgent',
            'SecteurAssurancesEmploye',
            'SecteurBanqueAgent',
            'SecteurBanqueEmploye',
            'SecteurBatimentAgent',
            'SecteurBatimentCadre',
            'SecteurBatimentChauffeur',
            'SecteurCommerceAgent',
            'SecteurCommerceCadre',
            'SecteurCommerceEmploye',
            'SecteurDockersEmploye',
            'SecteurElevageChauffeur',
            'SecteurElevageEmploye',
            'SecteurElevageOuvrier',
            'SecteurForestierChauffeur',
            'SecteurForestierEmploye',
            'SecteurForestierOuvrier',
            'SecteurHotelleri',
            'SecteurHotellerieMaitrise',
            'SecteurHotellerisCadre',
            'SecteurIndustrielAgriAgent',
            'SecteurIndustrielAgriCadre',
            'SecteurIndustrielAgriChauffeur',
            'SecteurIndustrielAgriEmploye',
            'SecteurIndustrielAgriOuvrier',
            'SecteurIndustrielBoisAgent',
            'SecteurIndustrielBoisCadre',
            'SecteurIndustrielBoisChauffeur',
            'SecteurIndustrielBoisEmploye',
            'SecteurIndustrielBoisOuvrier',
            'SecteurIndustrielPolyAgent',
            'SecteurIndustrielPolyCadre',
            'SecteurIndustrielPolyEmploye',
            'SecteurIndustrielPolyOuvrier',
            'SecteurIndustrielSucreAgent',
            'SecteurIndustrielSucreCadre',
            'SecteurIndustrielSucreChauffeur',
            'SecteurIndustrielSucreEmploye',
            'SecteurIndustrielSucreOuvrier',
            'SecteurIndustrielTextAgent',
            'SecteurIndustrielTextCadre',
            'SecteurIndustrielTextChauffeur',
            'SecteurIndustrielTextEmploye',
            'SecteurIndustrielTextOuvrier',
            'SecteurIndustrielThonAgent',
            'SecteurIndustrielThonCadre',
            'SecteurIndustrielThonChauffeur',
            'SecteurIndustrielThonEmploye',
            'SecteurIndustrielThonOuvrier',
            'SecteurMaisonEmploye',
            'SecteurMaritimeCapit',
            'SecteurMaritimeChefMeca',
            'SecteurMaritimeMachine',
            'SecteurMaritimeMaitre',
            'SecteurMaritimeMatelo',
            'SecteurMaritimePoly',
            'SecteurMaritimeSecondCapit',
            'SecteurMaritimeSecondMeca',
            'SecteurNettoyageChauffeur',
            'SecteurNettoyageEmploye',
            'SecteurNettoyageOuvrier',
            'SecteurPechesBosc',
            'SecteurPechesBrevet',
            'SecteurPechesCapit',
            'SecteurPechesChefMoteur',
            'SecteurPechesCotiereBosco',
            'SecteurPechesCotiereCapi',
            'SecteurPechesCotiereCapisCapa',
            'SecteurPechesCotiereEleve',
            'SecteurPechesCotiereMatlotSimpl',
            'SecteurPechesCotiereMatlot',
            'SecteurPechesCotiereMeca',
            'SecteurPechesCotiereNoviece',
            'SecteurPechesCotiereSecondBoco',
            'SecteurPechesEleve',
            'SecteurPechesGraisseur',
            'SecteurPechesLargesBoscoElec',
            'SecteurPechesLargesCuisto',
            'SecteurPechesLargesEleve',
            'SecteurPechesLargesGraisse',
            'SecteurPechesLargesMatlot',
            'SecteurPechesLargesMatlotsSimple',
            'SecteurPechesLargesNovice',
            'SecteurPechesLargesOffPont',
            'SecteurPechesLargesSecondBosco',
            'SecteurPechesMecani',
            'SecteurPechesNovice',
            'SecteurPechesOffPon',
            'SecteurPechesSbrevet',
            'SecteurPechesSceonBosco',
            'SecteurPechesSconCapit',
            'SecteurPechesSconMeca',
            'SecteurPetroDistAgent',
            'SecteurPetroDistCadre',
            'SecteurPetroDistChauffeur',
            'SecteurPetroDistEmploye',
            'SecteurPetroProdAgent',
            'SecteurPetroProdCadre',
            'SecteurPetroProdChauffeur',
            'SecteurPetroProdEmploye',
            'SecteurPetroProdOuvrier',
            'SecteurSecuriteChauffeur',
            'SecteurSecuriteEmploye',
            'SecteurTourisme',
            'SecteurTourismsMatrise',
            'SecteurTourismsCadre',
            'SecteurTransportAgent',
            'SecteurTransportCadre',
            'SecteurTransportChauffeur',
            'SecteurTransportEmploye',
            'SecteurTransportOuvrier',
            'SecteurTrpsAerienAgent',
            'SecteurTrpsAerienCadre',
            'SecteurTrpsAerienCadresSup',
            'SecteurTrpsAerienOuvrier',
            'SecteurTrpsFondAgent',
            'SecteurTrpsFondCadre',
            'SecteurTrpsFondEmploye',
        ];

        // Récupérez les informations sur le secteur
        $secteur_id = Company::where('user_id', Auth::user()->id)->first()->industry;
        $secteurs = Sector::where('slug', $secteur_id)->first();

        $datas = [];

        foreach ($tables as $table) {
            $model = app("App\\Models\\$table");
            $result = $model->where('type_poste', $id)->orderby('categorie')->get();
            // Stockez les données dans un tableau associatif
            $datas[$table] = $result;
        }

        $employees = Employee::where('company_id', Auth::user()->company_id)->get()->pluck('name', 'id');
        $categories = JobCategorie::where('id', $id)->select('*')->orderby('title')->get();

        return view('employees::grille.create', compact('datas', 'employees', 'secteurs', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'category_name' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Assurez-vous que la clé de la table est présente dans la demande
        if (!$request->filled('table_key')) {
            return redirect()->back()->with('error', __("La grille cible n'a pas pu être déterminée (table_key manquant). Rouvrez le formulaire et réessayez."));
        }

        // Récupérez le nom de la table correspondant à la clé donnée
        $tableName = $request->table_key;
        $modelClass = "App\\Models\\$tableName";

        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', __("Grille inconnue : ") . $tableName);
        }

        try {
            // Get the last record's ID from the table
            $lastRecord = $modelClass::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $request->merge(['idposte' => $lastId]);

            // Insérez les données dans la table appropriée
            $model = new $modelClass();
            $table = $model->getTable();

            $model->categorie = $request->category_name;

            // Certaines grilles n'ont pas toutes les colonnes : on n'écrit que celles qui existent
            if (Schema::hasColumn($table, 'definition')) {
                $model->definition = $request->category_name;
            }
            if (Schema::hasColumn($table, 'salaire_minima_horaire')) {
                $model->salaire_minima_horaire = $request->filled('salaire_horaire') ? $request->salaire_horaire : 0;
            }
            if (Schema::hasColumn($table, 'salaire_minima_mensuel')) {
                $model->salaire_minima_mensuel = $request->filled('salaire_mensuel') ? $request->salaire_mensuel : 0;
            }

            $model->type_poste = $request->id_poste;
            $model->id_secteur = $request->id_secteur;
            $model->company_id = Auth::user()->company_id;
            $model->save();
        } catch (\Throwable $e) {
            Log::error('Grille store failed', [
                'table_key' => $tableName,
                'payload' => $request->except('_token'),
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', __("Enregistrement impossible : ") . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Catégorie suggérée insérée avec succès..'));
    }

    public function show($id)
    {
        // Logique pour afficher les détails d'une catégorie
        // Pour l'instant, rediriger vers l'index
        return redirect()->route('company.employees.grille.index');
    }

    public function edit($id)
    {
        // Récupérez les informations sur le secteur
        $secteur_id = Company::where('user_id', Auth::user()->id)->first()->industry;
        $secteurs = Sector::where('slug', $secteur_id)->first();

        return view('employees::grille.edit', compact('id', 'secteurs'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:255',
                'salaire_horaire' => 'required|numeric|min:0',
                'salaire_mensuel' => 'required|numeric|min:0',
                'table_key' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Récupérer toutes les tables possibles
            $tables = [
                'SecteurBatiment',
                'SecteurBatimentEmploye',
                'SecteurIndusAgent',
                'SecteurIndusCadre',
                'SecteurIndustrielEmploye',
                'SecteurIndustrielOuvrier',
                'SecteurIndusCauffeur',
                'SecteurAgriAutreChauffeur',
                'SecteurAgriAutreEmploye',
                'SecteurAgriAutreOuvrier',
                'SecteurAgriCrcChauffeur',
                'SecteurAgriCrcEmploye',
                'SecteurAgriCrcOuvrier',
                'SecteurAssurancesAgent',
                'SecteurAssurancesEmploye',
                'SecteurBanqueAgent',
                'SecteurBanqueEmploye',
                'SecteurBatimentAgent',
                'SecteurBatimentCadre',
                'SecteurBatimentChauffeur',
                'SecteurCommerceAgent',
                'SecteurCommerceCadre',
                'SecteurCommerceEmploye',
                'SecteurDockersEmploye',
                'SecteurElevageChauffeur',
                'SecteurElevageEmploye',
                'SecteurElevageOuvrier',
                'SecteurForestierChauffeur',
                'SecteurForestierEmploye',
                'SecteurForestierOuvrier',
                'SecteurHotelleri',
                'SecteurHotellerieMaitrise',
                'SecteurHotellerisCadre',
                'SecteurIndustrielAgriAgent',
                'SecteurIndustrielAgriCadre',
                'SecteurIndustrielAgriChauffeur',
                'SecteurIndustrielAgriEmploye',
                'SecteurIndustrielAgriOuvrier',
                'SecteurIndustrielBoisAgent',
                'SecteurIndustrielBoisCadre',
                'SecteurIndustrielBoisChauffeur',
                'SecteurIndustrielBoisEmploye',
                'SecteurIndustrielBoisOuvrier',
                'SecteurIndustrielPolyAgent',
                'SecteurIndustrielPolyCadre',
                'SecteurIndustrielPolyEmploye',
                'SecteurIndustrielPolyOuvrier',
                'SecteurIndustrielSucreAgent',
                'SecteurIndustrielSucreCadre',
                'SecteurIndustrielSucreChauffeur',
                'SecteurIndustrielSucreEmploye',
                'SecteurIndustrielSucreOuvrier',
                'SecteurIndustrielTextAgent',
                'SecteurIndustrielTextCadre',
                'SecteurIndustrielTextChauffeur',
                'SecteurIndustrielTextEmploye',
                'SecteurIndustrielTextOuvrier',
                'SecteurIndustrielThonAgent',
                'SecteurIndustrielThonCadre',
                'SecteurIndustrielThonChauffeur',
                'SecteurIndustrielThonEmploye',
                'SecteurIndustrielThonOuvrier',
                'SecteurMaisonEmploye',
                'SecteurMaritimeCapit',
                'SecteurMaritimeChefMeca',
                'SecteurMaritimeMachine',
                'SecteurMaritimeMaitre',
                'SecteurMaritimeMatelo',
                'SecteurMaritimePoly',
                'SecteurMaritimeSecondCapit',
                'SecteurMaritimeSecondMeca',
                'SecteurNettoyageChauffeur',
                'SecteurNettoyageEmploye',
                'SecteurNettoyageOuvrier',
                'SecteurPechesBosc',
                'SecteurPechesBrevet',
                'SecteurPechesCapit',
                'SecteurPechesChefMoteur',
                'SecteurPechesCotiereBosco',
                'SecteurPechesCotiereCapi',
                'SecteurPechesCotiereCapisCapa',
                'SecteurPechesCotiereEleve',
                'SecteurPechesCotiereMatlotSimpl',
                'SecteurPechesCotiereMatlot',
                'SecteurPechesCotiereMeca',
                'SecteurPechesCotiereNoviece',
                'SecteurPechesCotiereSecondBoco',
                'SecteurPechesEleve',
                'SecteurPechesGraisseur',
                'SecteurPechesLargesBoscoElec',
                'SecteurPechesLargesCuisto',
                'SecteurPechesLargesEleve',
                'SecteurPechesLargesGraisse',
                'SecteurPechesLargesMatlot',
                'SecteurPechesLargesMatlotsSimple',
                'SecteurPechesLargesNovice',
                'SecteurPechesLargesOffPont',
                'SecteurPechesLargesSecondBosco',
                'SecteurPechesMecani',
                'SecteurPechesNovice',
                'SecteurPechesOffPon',
                'SecteurPechesSbrevet',
                'SecteurPechesSceonBosco',
                'SecteurPechesSconCapit',
                'SecteurPechesSconMeca',
                'SecteurPetroDistAgent',
                'SecteurPetroDistCadre',
                'SecteurPetroDistChauffeur',
                'SecteurPetroDistEmploye',
                'SecteurPetroProdAgent',
                'SecteurPetroProdCadre',
                'SecteurPetroProdChauffeur',
                'SecteurPetroProdEmploye',
                'SecteurPetroProdOuvrier',
                'SecteurSecuriteChauffeur',
                'SecteurSecuriteEmploye',
                'SecteurTourisme',
                'SecteurTourismsMatrise',
                'SecteurTourismsCadre',
                'SecteurTransportAgent',
                'SecteurTransportCadre',
                'SecteurTransportChauffeur',
                'SecteurTransportEmploye',
                'SecteurTransportOuvrier',
                'SecteurTrpsAerienAgent',
                'SecteurTrpsAerienCadre',
                'SecteurTrpsAerienCadresSup',
                'SecteurTrpsAerienOuvrier',
                'SecteurTrpsFondAgent',
                'SecteurTrpsFondCadre',
                'SecteurTrpsFondEmploye',
            ];

            $updated = false;
            $foundRecord = null;

            // Parcourir toutes les tables pour trouver l'enregistrement
            foreach ($tables as $table) {
                $model = app("App\\Models\\$table");
                $record = $model->find($id);

                if ($record && $record->company_id == Auth::user()->company_id) {
                    // Vérifier que l'enregistrement appartient bien à l'utilisateur
                    $record->categorie = $request->category_name;
                    $record->salaire_minima_horaire = $request->salaire_horaire;
                    $record->salaire_minima_mensuel = $request->salaire_mensuel;
                    $record->save();

                    $updated = true;
                    $foundRecord = $record;
                    break;
                }
            }

            if ($updated) {
                return redirect()->route('company.employees.grille.index')
                    ->with('success', __('Catégorie mise à jour avec succès.'));
            } else {
                return redirect()->route('company.employees.grille.index')
                    ->with('error', __('Catégorie non trouvée ou vous n\'avez pas la permission de la modifier.'));
            }

        } catch (\Exception $e) {
            return redirect()->route('company.employees.grille.index')
                ->with('error', __('Une erreur est survenue lors de la mise à jour : ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Récupérer toutes les tables possibles
            $tables = [
                'SecteurBatiment',
                'SecteurBatimentEmploye',
                'SecteurIndusAgent',
                'SecteurIndusCadre',
                'SecteurIndustrielEmploye',
                'SecteurIndustrielOuvrier',
                'SecteurIndusCauffeur',
                'SecteurAgriAutreChauffeur',
                'SecteurAgriAutreEmploye',
                'SecteurAgriAutreOuvrier',
                'SecteurAgriCrcChauffeur',
                'SecteurAgriCrcEmploye',
                'SecteurAgriCrcOuvrier',
                'SecteurAssurancesAgent',
                'SecteurAssurancesEmploye',
                'SecteurBanqueAgent',
                'SecteurBanqueEmploye',
                'SecteurBatimentAgent',
                'SecteurBatimentCadre',
                'SecteurBatimentChauffeur',
                'SecteurCommerceAgent',
                'SecteurCommerceCadre',
                'SecteurCommerceEmploye',
                'SecteurDockersEmploye',
                'SecteurElevageChauffeur',
                'SecteurElevageEmploye',
                'SecteurElevageOuvrier',
                'SecteurForestierChauffeur',
                'SecteurForestierEmploye',
                'SecteurForestierOuvrier',
                'SecteurHotelleri',
                'SecteurHotellerieMaitrise',
                'SecteurHotellerisCadre',
                'SecteurIndustrielAgriAgent',
                'SecteurIndustrielAgriCadre',
                'SecteurIndustrielAgriChauffeur',
                'SecteurIndustrielAgriEmploye',
                'SecteurIndustrielAgriOuvrier',
                'SecteurIndustrielBoisAgent',
                'SecteurIndustrielBoisCadre',
                'SecteurIndustrielBoisChauffeur',
                'SecteurIndustrielBoisEmploye',
                'SecteurIndustrielBoisOuvrier',
                'SecteurIndustrielPolyAgent',
                'SecteurIndustrielPolyCadre',
                'SecteurIndustrielPolyEmploye',
                'SecteurIndustrielPolyOuvrier',
                'SecteurIndustrielSucreAgent',
                'SecteurIndustrielSucreCadre',
                'SecteurIndustrielSucreChauffeur',
                'SecteurIndustrielSucreEmploye',
                'SecteurIndustrielSucreOuvrier',
                'SecteurIndustrielTextAgent',
                'SecteurIndustrielTextCadre',
                'SecteurIndustrielTextChauffeur',
                'SecteurIndustrielTextEmploye',
                'SecteurIndustrielTextOuvrier',
                'SecteurIndustrielThonAgent',
                'SecteurIndustrielThonCadre',
                'SecteurIndustrielThonChauffeur',
                'SecteurIndustrielThonEmploye',
                'SecteurIndustrielThonOuvrier',
                'SecteurMaisonEmploye',
                'SecteurMaritimeCapit',
                'SecteurMaritimeChefMeca',
                'SecteurMaritimeMachine',
                'SecteurMaritimeMaitre',
                'SecteurMaritimeMatelo',
                'SecteurMaritimePoly',
                'SecteurMaritimeSecondCapit',
                'SecteurMaritimeSecondMeca',
                'SecteurNettoyageChauffeur',
                'SecteurNettoyageEmploye',
                'SecteurNettoyageOuvrier',
                'SecteurPechesBosc',
                'SecteurPechesBrevet',
                'SecteurPechesCapit',
                'SecteurPechesChefMoteur',
                'SecteurPechesCotiereBosco',
                'SecteurPechesCotiereCapi',
                'SecteurPechesCotiereCapisCapa',
                'SecteurPechesCotiereEleve',
                'SecteurPechesCotiereMatlotSimpl',
                'SecteurPechesCotiereMatlot',
                'SecteurPechesCotiereMeca',
                'SecteurPechesCotiereNoviece',
                'SecteurPechesCotiereSecondBoco',
                'SecteurPechesEleve',
                'SecteurPechesGraisseur',
                'SecteurPechesLargesBoscoElec',
                'SecteurPechesLargesCuisto',
                'SecteurPechesLargesEleve',
                'SecteurPechesLargesGraisse',
                'SecteurPechesLargesMatlot',
                'SecteurPechesLargesMatlotsSimple',
                'SecteurPechesLargesNovice',
                'SecteurPechesLargesOffPont',
                'SecteurPechesLargesSecondBosco',
                'SecteurPechesMecani',
                'SecteurPechesNovice',
                'SecteurPechesOffPon',
                'SecteurPechesSbrevet',
                'SecteurPechesSceonBosco',
                'SecteurPechesSconCapit',
                'SecteurPechesSconMeca',
                'SecteurPetroDistAgent',
                'SecteurPetroDistCadre',
                'SecteurPetroDistChauffeur',
                'SecteurPetroDistEmploye',
                'SecteurPetroProdAgent',
                'SecteurPetroProdCadre',
                'SecteurPetroProdChauffeur',
                'SecteurPetroProdEmploye',
                'SecteurPetroProdOuvrier',
                'SecteurSecuriteChauffeur',
                'SecteurSecuriteEmploye',
                'SecteurTourisme',
                'SecteurTourismsMatrise',
                'SecteurTourismsCadre',
                'SecteurTransportAgent',
                'SecteurTransportCadre',
                'SecteurTransportChauffeur',
                'SecteurTransportEmploye',
                'SecteurTransportOuvrier',
                'SecteurTrpsAerienAgent',
                'SecteurTrpsAerienCadre',
                'SecteurTrpsAerienCadresSup',
                'SecteurTrpsAerienOuvrier',
                'SecteurTrpsFondAgent',
                'SecteurTrpsFondCadre',
                'SecteurTrpsFondEmploye',
            ];

            $deleted = false;
            $foundTable = null;

            // Parcourir toutes les tables pour trouver l'enregistrement
            foreach ($tables as $table) {
                $model = app("App\\Models\\$table");
                $record = $model->find($id);

                if ($record && $record->company_id == Auth::user()->company_id) {
                    // Vérifier que l'enregistrement appartient bien à l'utilisateur
                    $record->delete();
                    $deleted = true;
                    $foundTable = $table;
                    break;
                }
            }

            if ($deleted) {
                return redirect()->route('company.employees.grille.index')
                    ->with('success', __('Catégorie supprimée avec succès.'));
            } else {
                return redirect()->route('company.employees.grille.index')
                    ->with('error', __('Catégorie non trouvée ou vous n\'avez pas la permission de la supprimer.'));
            }

        } catch (\Exception $e) {
            return redirect()->route('company.employees.grille.index')
                ->with('error', __('Une erreur est survenue lors de la suppression : ') . $e->getMessage());
        }
    }
}

