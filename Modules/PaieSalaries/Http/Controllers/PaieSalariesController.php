<?php

namespace Modules\PaieSalaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Notification;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Designation;


use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\JobCategorie;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\PaymentType;
use Modules\Loans\Models\LoanPayment;
use App\Models\SecteurBatiment;
use App\Models\SecteurBatimentEmploye;
use App\Models\SecteurIndusAgent;
use App\Models\SecteurIndusCadre;
use App\Models\SecteurIndustrielEmploye;
use App\Models\SecteurIndustrielOuvrier;
use App\Models\SecIndusChauffeur;
use App\Models\SecteurAgriAutreChauffeur;
use App\Models\SecteurAgriAutreEmploye;
use App\Models\SecteurAgriAutreOuvrier;
use App\Models\SecteurAgriCcrcChauffeur;
use App\Models\SecteurAgriCcrcEmploye;
use App\Models\SecteurAgriCcrcOuvrier;
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
use App\Models\SecteurTourism;
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
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\PaieSalaries\Models\Retenue;
use Modules\PaieSalaries\Models\TypeRetenue;
use Modules\PaieSalaries\Models\SetSalarie;
use Modules\NatureAvantage\Models\Avantage;
use Modules\Ruptures\Models\Rupture;
use Modules\Loans\Models\Loan;
use Modules\Loans\Models\LoanOption;
use Modules\Time\Models\TimeSheet;
use Modules\Loans\Services\LoanCalculatorService;

class PaieSalariesController extends Controller
{
    /**
     * Répartition réelle des charges du mois, lue sur les bulletins émis.
     *
     * La carte du tableau de bord affichait auparavant la somme des salaires
     * contractuels de *tous* les salariés, inactifs compris, des primes filtrées
     * sur un champ month_paie souvent vide, et des cotisations forfaitaires à
     * 10 % de la masse — un chiffre qui n'était calculé nulle part.
     *
     * Ici tout vient des bulletins du mois : montants figés, donc exacts. Les
     * cotisations sont réparties par code, comme sur le bulletin lui-même :
     * 301 et 302 pour la part salariale, 307 et 308 pour la part employeur,
     * 409 à 412, 305 et 306 pour les taxes et charges patronales.
     *
     * @return array{base: float, primes: float, retenues: float, cotisations: float, bulletins: int}
     */
    private function getRepartitionChargesMois($companyId, $mois)
    {
        $vide = ['base' => 0.0, 'primes' => 0.0, 'retenues' => 0.0, 'patronales' => 0.0,
            'bulletins' => 0, 'mois' => $mois];

        $bulletins = PaySlip::where('company_id', $companyId)
            ->where('salary_month', $mois)
            ->get(['basic_salary', 'salary_brut', 'retenues']);

        // Le mois en cours n'est pas toujours traite. Plutot que d'afficher un
        // graphique vide, on se rabat sur le dernier mois reellement paye, sans
        // aller chercher dans le futur : la vue indique alors lequel.
        if ($bulletins->isEmpty()) {
            $dernier = PaySlip::where('company_id', $companyId)
                ->where('salary_month', '<', $mois)
                ->max('salary_month');

            if (!$dernier) {
                return $vide;
            }

            $mois = $dernier;
            $bulletins = PaySlip::where('company_id', $companyId)
                ->where('salary_month', $mois)
                ->get(['basic_salary', 'salary_brut', 'retenues']);
        }

        // Cote employeur : accident du travail, prestations familiales, CMU et
        // retraite patronales, contribution employeur, taxe expatrie,
        // apprentissage et FPC. Tout le reste est preleve sur le brut du
        // salarie : impot 403, CNPS 301, CMU 302, echeances de pret 500.
        $codesPatronaux = ['305', '306', '307', '308', '409', '410', '411', '412'];
        $codesIntermediaires = ['401', '402'];

        $repartition = $vide;
        $repartition['bulletins'] = $bulletins->count();
        $repartition['mois'] = $mois;

        foreach ($bulletins as $bulletin) {
            $base = (float) $bulletin->basic_salary;
            $repartition['base'] += $base;
            $repartition['primes'] += max(0, (float) $bulletin->salary_brut - $base);

            foreach (json_decode($bulletin->retenues, true) ?: [] as $poste) {
                $code = (string) ($poste['code'] ?? '');
                $montant = (float) ($poste['amount'] ?? 0);

                // 401 et 402 sont des étapes du calcul de l'impôt, pas des montants
                // prélevés : les compter reviendrait à doubler la 403.
                if (in_array($code, $codesIntermediaires, true)) {
                    continue;
                }

                if (in_array($code, $codesPatronaux, true)) {
                    $repartition['patronales'] += $montant;
                } else {
                    $repartition['retenues'] += $montant;
                }
            }
        }

        return $repartition;
    }

    /**
     * Dashboard principal de la paie
     */
    public function dashboard()
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $currentMonth = now()->format("Y-m");
        $exercices = PaieExercice::with([
            "periodes" => function ($query) {
                $query->orderBy("date_debut", "desc");
            },
        ])->where('company_id', $companyId)->get();
        // Statistiques principales
        $stats = [
            "total_employes" => Employee::where(
                "company_id",
                $companyId,
            )->count(),
            "salaire_moyen" =>
                Employee::where("company_id", $companyId)->avg("salary") ?? 0,
            "masse_salariale_mensuelle" => $this->getMasseSalarialeMensuelle(
                $companyId,
            ),
            "total_pret_en_cours" => Loan::where("company_id", $companyId)
                ->where("statut", "running")
                ->sum("amount"),
            "total_retenues_mois" => Retenue::where("company_id", $companyId)
                ->where("month_paie", "like", "%$currentMonth%")
                ->sum("amount"),
            "total_primes_mois" => Allowance::where("company_id", $companyId)
                ->where("month_paie", "like", "%$currentMonth%")
                ->sum("amount"),
            "repartition_charges" => $this->getRepartitionChargesMois($companyId, $currentMonth),
        ];

        // Derniers exercices
        $derniers_exercices = PaieExercice::with(['periodes' => function($query) {
            $query->where('date_fin', '>=', now())
                ->where('statut', '!=', 'cloture')
                ->orderBy('date_debut');
        }])->where("company_id", $companyId)
        ->orderBy("date_debut", "desc")
        ->take(5)
        ->get();

        // Dernières fiches de paie
        $dernieres_paies = PaySlip::with("employee")
            ->where("company_id", $companyId)
            ->orderBy("salary_month", "desc")
            ->take(5)
            ->get();

        // Échéances de prêts à venir (7 prochains jours)
        $echeances_pret = Loan::with("employee")
            ->where("company_id", $companyId)
            ->whereIn("statut", ["running", "pending"]) // Vérifier les statuts possibles
            ->orderBy("prochaine_echeance")
            ->take(5)
            ->get()
            ->map(function($loan) {
                // Initialiser le calculateur de prêt
                $calculator = new LoanCalculatorService();

                // Calculer les informations de remboursement
                $loan->total_paid = $calculator->calculateTotalPaid($loan);
                $loan->remaining_amount = $calculator->calculateRemainingAmount($loan);
                $loan->repayment_percentage = $calculator->calculateRepaymentPercentage($loan);
                $loan->next_payment = $calculator->getNextPayment($loan);

                return $loan;
            });

        // Si vous avez besoin d'accéder au calculateur plus tard dans le contrôleur
        $calculator = new LoanCalculatorService();
        $loanStats = [
            'total_active_loans' => Loan::where('company_id', $companyId)
                                    ->whereIn('statut', ['running', 'pending'])
                                    ->count(),
            'total_loans_amount' => Loan::where('company_id', $companyId)
                                    ->whereIn('statut', ['running', 'pending'])
                                    ->sum('amount')
        ];

        // Évolution de la masse salariale sur 6 mois
        $masse_salariale = $this->getEvolutionMasseSalariale($companyId);

        return view(
            "paiesalaries::dashboard",
            compact(
                "stats",
                "exercices",
                "derniers_exercices",
                "dernieres_paies",
                "echeances_pret",
                "loanStats",
                "masse_salariale",
            ),
        );
    }

    /**
     * Calcule la masse salariale mensuelle
     */
    private function getMasseSalarialeMensuelle($companyId)
    {
        return Employee::where("company_id", $companyId)->sum(
            DB::raw(
                'salary + IFNULL((SELECT SUM(amount) FROM allowances WHERE employee_id = employees.id AND month_paie = "'.now()->format("Y-m").'"), 0)',
            ),
        );
    }

    /**
     * Récupère l'évolution de la masse salariale sur 6 mois
     */
    private function getEvolutionMasseSalariale($companyId)
    {
        $data = [];
        $now = now();
        $currentYear = $now->year;

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $mois = $date->format("Y-m");
            $anneeMois = $date->format('Y-m');

            // Récupération des salaires de base
            $salaires = DB::table('employees')
                ->where('company_id', $companyId)
                ->where('created_at', '<=', $date->endOfMonth())
                ->sum('salary');

            // Récupération des primes du mois
            $primes = DB::table('allowances')
                ->where('company_id', $companyId)
                ->where('month_paie', $anneeMois)
                ->sum('amount');

            // Récupération des retenues du mois
            $retenues = DB::table('retenues')
                ->where('company_id', $companyId)
                ->where('type', 'default')
                ->where('month_paie', $anneeMois)
                ->where('is_active', true)
                ->sum('amount');

            $retenuesCreated = DB::table('retenues')
                ->where('company_id', $companyId)
                ->where('type_retenue_id','!=', 5)
                ->where('type', 'add')
                ->where('month_paie', $anneeMois)
                ->where('is_active', true)
                ->sum('amount');

            $remboursement = DB::table('retenues')
                ->where('company_id', $companyId)
                ->where('type_retenue_id', 5)
                ->where('type', 'add')
                ->where('month_paie', $anneeMois)
                ->where('is_active', true)
                ->sum('amount');

            // Calcul de la masse salariale (salaires + primes - retenues)
            $masseSalariale = ($salaires + $primes + $remboursement) - ($retenues + $retenuesCreated);

            $data["labels"][] = $date->locale("fr")->monthName . " " . $date->year;
            $data["data"][] = round($masseSalariale, 2); // Arrondi à 2 décimales
        }

        return $data;
    }

    /**
     * Affiche la liste des exercices
     */
    public function indexExercice()
    {
        $companyId = Auth::user()->company_id;
        $exercices = PaieExercice::where("company_id", $companyId)
            ->orderBy("date_debut", "desc")
            ->paginate(10);

        return view("paiesalaries::exercices.index", compact("exercices"));
    }

    /**
     * Affiche le formulaire de création d'un nouvel exercice
     */
    public function createExercice()
    {
        return view("paiesalaries::exercices.create");
    }

    /**
     * Enregistre un nouvel exercice
     */
    public function storeExercice(Request $request)
    {
        $validated = $request->validate([
            // Un même exercice ne peut être créé deux fois dans l'entreprise. Le code ne
            // l'empêchait pas : il est généré aléatoirement et diffère à chaque saisie.
            // Les exercices supprimés sont ignorés (SoftDeletes), sinon un exercice effacé
            // interdirait de recréer la même année.
            "nom" => [
                "required",
                "string",
                "max:255",
                \Illuminate\Validation\Rule::unique('paie_exercices', 'nom')
                    ->where(fn($query) => $query
                        ->where('company_id', Auth::user()->company_id)
                        ->whereNull('deleted_at')),
            ],
            "date_debut" => "required|date",
            "date_fin" => "required|date|after:date_debut",
            "description" => "nullable|string",
        ], [
            "nom.unique" => "Un exercice porte déjà ce nom.",
        ]);

        $exercice = new PaieExercice();
        $exercice->nom = $validated["nom"];
        $exercice->code = PaieExercice::genererCode();
        $exercice->date_debut = $validated["date_debut"];
        $exercice->date_fin = $validated["date_fin"];
        $exercice->description = $validated["description"] ?? null;
        $exercice->company_id = Auth::user()->company_id;
        $exercice->created_by = auth()->id();
        $exercice->save();

        // Créé depuis « Paie du mois » : on y revient pour ouvrir directement le mois suivant
        if ($request->input('retour') === 'paie-du-mois') {
            return redirect()
                ->route("company.paiesalaries.paie-du-mois", ['nouvelle' => 1])
                ->with("success", "Exercice créé avec succès.");
        }

        return redirect()
            ->route("company.paiesalaries.exercices.show", $exercice->id)
            ->with("success", "Exercice créé avec succès.");
    }

    /**
     * Affiche les détails d'un exercice
     */
    public function showExercice($id)
    {
        $exercice = PaieExercice::with([
            "periodes" => function ($query) {
                $query->orderBy("date_debut", "desc");
            },
        ])->findOrFail($id);

        return view("paiesalaries::exercices.show", compact("exercice"));
    }

    /**
     * Affiche le formulaire de modification d'un exercice
     */
    public function editExercice($id)
    {
        $exercice = PaieExercice::findOrFail($id);
        return view("paiesalaries::exercices.edit", compact("exercice"));
    }

    /**
     * Met à jour un exercice
     */
    public function updateExercice(Request $request, $id)
    {
        $exercice = PaieExercice::findOrFail($id);

        $validated = $request->validate([
            // Même unicité qu'à la création, en ignorant l'exercice en cours de modification.
            "nom" => [
                "required",
                "string",
                "max:255",
                \Illuminate\Validation\Rule::unique('paie_exercices', 'nom')
                    ->ignore($exercice->id)
                    ->where(fn($query) => $query
                        ->where('company_id', $exercice->company_id)
                        ->whereNull('deleted_at')),
            ],
            "date_debut" => "required|date",
            "date_fin" => "required|date|after:date_debut",
            "description" => "nullable|string",
            "statut" => "required|in:brouillon,en_cours,cloture",
        ], [
            "nom.unique" => "Un exercice porte déjà ce nom.",
        ]);

        $exercice->update($validated);

        return redirect()
            ->route("company.paiesalaries.exercices.show", $exercice->id)
            ->with("success", "Exercice mis à jour avec succès.");
    }

    /**
     * Supprime un exercice
     */
    public function destroyExercice($id)
    {
        // Cadré sur l'entreprise : un exercice d'une autre société ne doit pas être supprimable.
        $exercice = PaieExercice::where("company_id", Auth::user()->company_id)->findOrFail($id);

        // Vérifier s'il y a des périodes associées.
        // La clé étrangère est en cascade : sans ce garde-fou, supprimer l'exercice
        // emporterait silencieusement toutes ses périodes de paie.
        $nbPeriodes = $exercice->periodes()->count();
        if ($nbPeriodes > 0) {
            return back()->with(
                "error",
                "Impossible de supprimer cet exercice : il contient " . $nbPeriodes . " période(s) de paie. "
                    . "Supprimez-les d'abord.",
            );
        }

        try {
            $exercice->delete();
        } catch (\Throwable $e) {
            \Log::error("Suppression de l'exercice impossible", ['exercice_id' => $id, 'error' => $e->getMessage()]);
            return back()->with(
                "error",
                "Impossible de supprimer cet exercice : il est encore référencé par d'autres données.",
            );
        }

        return redirect()
            ->route("company.paiesalaries.exercices.index")
            ->with("success", "Exercice supprimé avec succès.");
    }

    /**
     * Affiche le formulaire de création d'une nouvelle période
     */
    public function createPeriode($exerciceId)
    {
        $exercice = PaieExercice::findOrFail($exerciceId);
        return view("paiesalaries::periodes.create", compact("exercice"));
    }

    /**
     * Enregistre une nouvelle période
     */
    public function storePeriode(Request $request, $exerciceId)
    {
        $exercice = PaieExercice::findOrFail($exerciceId);

        $validated = $request->validate([
            "nom" => "required|string|max:255",
            "date_debut" =>
                "required|date|after_or_equal:" .
                $exercice->date_debut->format("Y-m-d"),
            "date_fin" =>
                "required|date|after:date_debut|before_or_equal:" .
                $exercice->date_fin->format("Y-m-d"),
            "date_paiement" => "nullable|date|after_or_equal:date_fin",
            "type_periode" =>
                "required|in:mensuelle,quinzaine,hebdomadaire,autre",
            "notes" => "nullable|string",
        ]);

        // Verrou sur l'exercice : deux envois simultanés (double clic) ne créent qu'une seule période
        $doublon = null;
        $periode = DB::transaction(function () use ($exercice, $validated, &$doublon) {
            PaieExercice::whereKey($exercice->id)->lockForUpdate()->first();

            $doublon = PaiePeriode::where('company_id', Auth::user()->company_id)
                ->where('type_periode', $validated["type_periode"])
                ->whereDate('date_debut', $validated["date_debut"])
                ->whereDate('date_fin', $validated["date_fin"])
                ->orderBy('id', 'desc')
                ->first();

            if ($doublon) {
                return null;
            }

            $periode = new PaiePeriode();
            $periode->nom = $validated["nom"];
            $periode->code = PaiePeriode::genererCode(
                $exercice,
                $validated["type_periode"],
            );
            $periode->exercice_id = $exercice->id;
            $periode->date_debut = $validated["date_debut"];
            $periode->date_fin = $validated["date_fin"];
            $periode->date_paiement = $validated["date_paiement"];
            $periode->type_periode = $validated["type_periode"];
            $periode->notes = $validated["notes"] ?? null;
            $periode->company_id = Auth::user()->company_id;
            $periode->created_by = auth()->id();
            $periode->save();

            return $periode;
        });

        // La période existe déjà (second envoi du même formulaire) : on l'affiche au lieu d'en créer une autre
        if (!$periode) {
            return redirect()
                ->route("company.paiesalaries.periodes.show", $doublon->id)
                ->with("success", __("La période :nom est déjà ouverte.", ['nom' => $doublon->nom]));
        }

        // DEBUT DE L'AUTOMATISATION DU REPORT (Retenues, Primes, Prêts)
        try {
            $companyId = Auth::user()->company_id;

            // Récupérer la période précédente chronologiquement (même d'un exercice précédent)
            $previousPeriode = PaiePeriode::where('company_id', $companyId)
                ->where('date_fin', '<=', $periode->date_debut)
                ->orderBy('date_fin', 'desc')
                ->first();

            if ($previousPeriode) {
                // 1. Dupliquer les Retenues (sauf les prêts qui sont gérés séparément)
                $retenues = Retenue::where('periode_id', $previousPeriode->id)
                    ->where('company_id', $companyId)
                    ->where('code', '!=', '500')
                    ->get();

                foreach ($retenues as $originalRetenue) {
                    Retenue::create([
                        'periode_id' => $periode->id,
                        'employee_id' => $originalRetenue->employee_id,
                        'type_retenue_id' => $originalRetenue->type_retenue_id,
                        'code' => $originalRetenue->code,
                        'ordre' => $originalRetenue->ordre,
                        'salariale' => $originalRetenue->salariale,
                        'patronale' => $originalRetenue->patronale,
                        'base' => $originalRetenue->base,
                        'taux' => $originalRetenue->taux,
                        'libelle' => $originalRetenue->libelle,
                        'amount' => $originalRetenue->amount,
                        'jours_work' => $originalRetenue->jours_work,
                        'date_application' => now(),
                        'month_paie' => $periode->date_debut->format('Y-m'),
                        'is_active' => 1,
                        'type' => $originalRetenue->type,
                        'company_id' => $companyId,
                    ]);
                }

                // 2. Dupliquer les Prêts (Loans)
                $loans = Loan::where('periode_id', $previousPeriode->id)
                    ->where('company_id', $companyId)
                    ->get();

                foreach ($loans as $originalLoan) {
                    $lastOrder = Employee::where("employees.is_active", 1)
                        ->where("employees.company_id", $companyId)
                        ->join("retenues", "employees.id", "=", "retenues.employee_id")
                        ->orderBy("retenues.ordre", "desc")
                        ->first();

                    LoanPayment::create([
                        'loan_id' => $originalLoan->id,
                        'periode_id' => $periode->id,
                        'amount' => $originalLoan->amount_deduc,
                        'payment_date' => now(),
                        'note' => $originalLoan->reason,
                        'company_id' => $companyId,
                    ]);

                    Retenue::create([
                        'periode_id' => $periode->id,
                        'employee_id' => $originalLoan->employee_id,
                        'loan_id' => $originalLoan->id, // trace le prêt d'origine : sans lui, impossible de retirer la retenue quand le prêt est désactivé
                        'type_retenue_id' => 30, // Code typique pour les prêts
                        'code' => 500,
                        'ordre' => $lastOrder ? $lastOrder->ordre + 1 : 1,
                        'patronale' => 0,
                        'salariale' => 1,
                        'base' => $originalLoan->amount,
                        'taux' => $originalLoan->nbre_mois,
                        'libelle' => $originalLoan->title,
                        'amount' => $originalLoan->amount_deduc,
                        'date_application' => now(),
                        'month_paie' => $periode->date_debut->format('Y-m'),
                        'is_active' => 1,
                        'type' => 'add',
                        'company_id' => $companyId,
                    ]);
                }

                // 3. Dupliquer les Allocations (Primes)
                // Need to use the full namespace if it's not imported.
                // It was used as Allowance:: in duplicateElements
                $allowances = \App\Models\Allowance::where('periode_id', $previousPeriode->id)
                    ->where('company_id', $companyId)
                    ->get();

                foreach ($allowances as $originalAllowance) {
                    \App\Models\Allowance::create([
                        'periode_id' => $periode->id,
                        'employee_id' => $originalAllowance->employee_id,
                        'code' => $originalAllowance->code,
                        'code_compta' => $originalAllowance->code_compta,
                        'allowance_option_id' => $originalAllowance->allowance_option_id,
                        'title' => $originalAllowance->title,
                        'trait_fisc' => $originalAllowance->trait_fisc,
                        'trait_cnps' => $originalAllowance->trait_cnps,
                        'base_heures' => $originalAllowance->base_heures,
                        'amount' => $originalAllowance->amount,
                        'amount_imp' => $originalAllowance->amount_imp,
                        'montant' => $originalAllowance->montant,
                        'jours_work' => $originalAllowance->jours_work,
                        'jours_leave' => $originalAllowance->jours_leave,
                        'type' => $originalAllowance->type,
                        'type_amount' => $originalAllowance->type_amount,
                        'details' => $originalAllowance->details,
                        'month_paie' => $periode->date_debut->format('Y-m'),
                        'is_active' => 1,
                        'company_id' => $companyId,
                        'created_by' => auth()->id(),
                    ]);
                }

                // 4. Dupliquer les Avantages
                $avantages = \Modules\NatureAvantage\Models\Avantage::where('periode_id', $previousPeriode->id)
                    ->where('company_id', $companyId)
                    ->get();

                foreach ($avantages as $originalAvantage) {
                    \Modules\NatureAvantage\Models\Avantage::create([
                        'periode_id' => $periode->id,
                        'employee_id' => $originalAvantage->employee_id,
                        'libelle' => $originalAvantage->libelle,
                        'amount' => $originalAvantage->amount,
                        'amount_reel' => $originalAvantage->amount_reel,
                        'type_avantage' => $originalAvantage->type_avantage,
                        'is_active' => 1,
                        'company_id' => $companyId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la duplication automatique des éléments pour la nouvelle période: ' . $e->getMessage());
        }
        // FIN DE L'AUTOMATISATION DU REPORT

        return redirect()
            ->route("company.paiesalaries.periodes.show", $periode->id)
            ->with("success", "Période de paie créée avec succès.");
    }

    /**
     * Affiche les détails d'une période
     */
    public function showPeriode($id)
    {
        $companyId = Auth::user()->company_id;
        $totalPaid = 0;
        $remainingAmount = 0;
        $repaymentPercentage = 0;
        $repaymentSchedule = 0;

        $periode = PaiePeriode::with([
                        "exercice",
                    ])->findOrFail($id);

        // Retenues légales appliquées à tous les salariés tant que les bulletins ne sont pas générés
        // Prime d'ancienneté (Art. 55 CCI) appliquée d'office, avant les retenues.
        app(\App\Services\SalaryService::class)->appliquerPrimeAncienneteperiode($periode);
        app(\App\Services\SalaryService::class)->appliquerRetenuesLegalesPeriode($periode);
        $retenues = Retenue::whereHas('periode', function($query) use ($periode) {
                        $query->where('id', $periode->id);
                    })->where('type', 'add')->where('code', '!=', '500')->get();
        $allowances = Allowance::whereHas('periode', function($query) use ($periode) {
                        $query->where('id', $periode->id);
                    })->get();
        $avantages = Avantage::whereHas('periode', function($query) use ($periode) {
                        $query->where('id', $periode->id);
                    })->get();
        $loans = Loan::whereHas('periode', function($query) use ($periode) {
                        $query->where('id', $periode->id);
                    })->whereIn('statut', ['running', 'pending'])->get();
        $retenuesLoan = Retenue::whereHas('periode', function($query) use ($periode) {
                $query->where('id', $periode->id);
            })->where('type', 'add')->where('code', '500')->get();

        // Récupérer les échéanciers à venir pour les prêts actifs (calculés pour la période suivante)
        $activeLoans = Loan::whereIn('statut', ['running', 'pending'])
                        ->where('company_id', Auth::user()->company_id)
                        ->with(['employee', 'loanOption', 'payments'])
                        ->get();

        $loanPayments = collect();

        foreach ($activeLoans as $loan) {
            $calculator = new LoanCalculatorService();

            $totalPaid = $calculator->calculateTotalPaid($loan);
            $remainingAmount = $calculator->calculateRemainingAmount($loan);
            $repaymentPercentage = $calculator->calculateRepaymentPercentage($loan);
            $repaymentSchedule = $calculator->generateRepaymentSchedule($loan);

            // Si le prêt n'est pas terminé, préparer son échéance pour cette période
            if ($remainingAmount > 0) {
                // Calculer le montant de l'échéance (mensuel ou selon la configuration du prêt)
                $monthlyAmount = $loan->amount_deduc ?: ($loan->amount / max((int) $loan->nbre_mois, 1));

                // Vérifier s'il y a déjà un paiement enregistré pour cette période
                $existingPayment = $loan->payments()
                    ->where('periode_id', $periode->id)
                    ->first();

                $echeance = new \stdClass();
                $echeance->id = $existingPayment->id ?? null;
                $echeance->loan = $loan;
                $echeance->payment_date = $periode->date_paiement;
                $echeance->amount = $existingPayment->amount ?? min($monthlyAmount, $remainingAmount);
                $echeance->periode_id = $existingPayment ? $periode->id : null;
                $echeance->note = $existingPayment->note ?? null;
                $echeance->is_virtual = !$existingPayment;

                // Données d'affichage de l'échéancier
                $echeance->total_paid = $totalPaid;
                $echeance->remaining_amount = $remainingAmount;
                $echeance->repayment_percentage = $repaymentPercentage;
                $echeance->echeances_payees = $loan->payments()->count();
                $echeance->nbre_mois = (int) $loan->nbre_mois;
                $echeance->applied = (bool) $existingPayment;

                // Le prêt court-il sur cette période ? Un prêt peut être rattaché à une période
                // sans rapport avec son échéancier réel : on le signale sans bloquer.
                // start_date / end_date ne sont pas castés sur le modèle Loan, d'où le parse explicite.
                $echeance->hors_periode =
                    ($loan->start_date && Carbon::parse($periode->date_fin)->lt(Carbon::parse($loan->start_date)))
                    || ($loan->end_date && Carbon::parse($periode->date_debut)->gt(Carbon::parse($loan->end_date)));

                $loanPayments->push($echeance);
            }
        }

        // Période précédente dans le temps : c'est d'elle que storePeriode a repris les éléments à l'ouverture
        $previousPeriode = PaiePeriode::where('company_id', $companyId)
            ->where('id', '!=', $periode->id)
            ->where('date_fin', '<', $periode->date_debut)
            ->orderBy('date_fin', 'desc')
            ->first();

        // Salariés de la période : même sélection que la page « Calcul salaire »
        $employees = Employee::active()
            ->where('company_id', $companyId)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        // La période affichée devient la période active (sélecteur du haut, livre de paie)
        session(['active_periode_id' => $periode->id, 'active_exercice_id' => $periode->exercice_id]);

        // Catalogue des primes proposées dans le traitement en masse. La 104 en est
        // exclue : la prime d'ancienneté est posée automatiquement, la saisir à la
        // main entrerait en conflit avec le calcul légal.
        $optionsPrimes = AllowanceOption::where("company_id", $companyId)
            ->where(function ($requete) {
                $requete->where("code", "!=", "104")->orWhereNull("code");
            })
            ->orderBy("name")
            ->get(["id", "name", "code", "param_fiscal", "param_social"]);

        // Options pour les tiroirs latéraux de saisie des variables
        $typesRetenues = \Modules\PaieSalaries\Models\TypeRetenue::where('is_active', true)->get(['id', 'libelle']);
        $optionsPrets = \Modules\Loans\Models\LoanOption::where('company_id', $companyId)->get(['id', 'name']);
        $typesConges = \Modules\Leaves\Models\LeaveType::where('company_id', $companyId)->get(['id', 'title']);

        return view("paiesalaries::periodes.show", compact("periode", "activeLoans", "loans",  "totalPaid", "remainingAmount", "repaymentPercentage", "repaymentSchedule", "retenuesLoan", "loanPayments", "retenues", "allowances", "avantages", "previousPeriode", "employees", "optionsPrimes", "typesRetenues", "optionsPrets", "typesConges"));
    }

    /**
     * Traitement en masse depuis la grille de la paie du mois.
     *
     * Sert aussi à l'édition d'une seule ligne : un salarié est une sélection de un.
     * Deux actions seulement, celles que la grille affiche : le salaire de base et
     * les jours travaillés.
     *
     * Volontairement écrit à côté de update(), qui recalcule les retenues à la main
     * avec des bases en dur. Ici on délègue à SalaryService, seul endroit où le
     * barème est tenu à jour.
     */
    public function traitementMasse(Request $request, $periodeId)
    {
        $periode = PaiePeriode::where('company_id', Auth::user()->company_id)->find($periodeId);

        if (!$periode) {
            return response()->json(['success' => false, 'message' => "Période introuvable."], 404);
        }

        // Même garde-fou que les retenues légales : un bulletin généré ne se recalcule
        // pas, modifier les éléments ici le désalignerait sans qu'il bouge.
        if (in_array($periode->statut, ['payee', 'cloture', 'annulee'], true) || $periode->bulletins()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Période verrouillée : les bulletins sont déjà générés.",
            ], 422);
        }

        // Validation menée à la main plutôt que par $request->validate() : le
        // gestionnaire d'exceptions de l'application renvoie les ValidationException
        // en 500 avec la clé de traduction brute sur les requêtes JSON. Ici on garde
        // la main sur le code et sur le texte.
        $validateur = \Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'integer',
            'action' => 'required|in:base,jours,prime',
            'valeur' => 'required|numeric|min:0',
            'allowance_option_id' => 'required_if:action,prime|nullable|integer',
        ], [
            'employee_ids.required' => 'Aucun salarié sélectionné.',
            'employee_ids.min' => 'Aucun salarié sélectionné.',
            'action.required' => 'Action manquante.',
            'action.in' => 'Action inconnue.',
            'valeur.required' => 'Saisissez une valeur.',
            'valeur.numeric' => 'La valeur doit être un nombre.',
            'valeur.min' => 'La valeur ne peut pas être négative.',
            'allowance_option_id.required_if' => 'Choisissez la rubrique de prime à appliquer.',
        ]);

        if ($validateur->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validateur->errors()->first(),
                'errors' => $validateur->errors(),
            ], 422);
        }

        $valide = $validateur->validated();

        if ($valide['action'] === 'jours' && $valide['valeur'] > 30) {
            return response()->json([
                'success' => false,
                'message' => "Les jours travaillés sont comptés sur une base de 30.",
            ], 422);
        }

        // Le filtre sur company_id est la barrière : des identifiants d'une autre
        // entreprise ressortent simplement de la sélection.
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->whereIn('id', $valide['employee_ids'])
            ->get();

        if ($employees->isEmpty()) {
            return response()->json(['success' => false, 'message' => "Aucun salarié concerné."], 422);
        }

        $service = app(\App\Services\SalaryService::class);
        $valeur = (float) $valide['valeur'];
        $option = null;

        if ($valide['action'] === 'prime') {
            $option = AllowanceOption::where('company_id', Auth::user()->company_id)
                ->find($valide['allowance_option_id']);

            if (!$option) {
                return response()->json(['success' => false, 'message' => "Rubrique de prime introuvable."], 422);
            }
        }

        try {
            \DB::transaction(function () use ($employees, $periode, $valide, $valeur, $service, $option) {
                foreach ($employees as $employee) {

                    if ($valide['action'] === 'prime') {
                        // Prorata identique aux autres primes conventionnelles : montant
                        // = droit plein, amount = part du mois travaillée.
                        $jours = (int) $employee->get_jours_work($periode->id);
                        $jours = (in_array($jours, [28, 31], true) || $jours <= 0) ? 30 : $jours;
                        $proratise = $jours === 30 ? $valeur : round(($valeur / 30) * $jours);

                        Allowance::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'periode_id' => $periode->id,
                                'allowance_option_id' => $option->id,
                            ],
                            [
                                'code' => $option->code,
                                'code_compta' => $option->code_compta,
                                'title' => $option->name,
                                'trait_fisc' => $option->param_fiscal,
                                'trait_cnps' => $option->param_social,
                                'base_heures' => 0,
                                'amount' => $proratise,
                                'amount_imp' => $proratise,
                                'montant' => $valeur,
                                'jours_work' => $jours,
                                'jours_leave' => 0,
                                'type' => 'fixed',
                                'type_amount' => 1,
                                'details' => 'Appliquée en masse depuis la paie du mois',
                                'month_paie' => Carbon::parse($periode->date_debut)->format('Y-m'),
                                'is_active' => 1,
                                'company_id' => $employee->company_id,
                                'created_by' => auth()->id() ?? 0,
                                'updated_by' => auth()->id(),
                            ]
                        );
                    } elseif ($valide['action'] === 'base') {
                        $employee->update(['salary' => $valeur]);
                    } else {
                        $jours = (int) $valeur;
                        $employee->update(['tax_payer_id' => $jours]);

                        // get_jours_work() lit d'abord allowances.jours_work : sans cette
                        // reprise, la grille afficherait encore l'ancien prorata.
                        foreach (Allowance::where('employee_id', $employee->id)
                            ->where('periode_id', $periode->id)->get() as $allowance) {

                            $montantPlein = (float) ($allowance->montant ?: $allowance->amount);
                            $allowance->amount = ($allowance->type_amount == 1 && $jours != 30)
                                ? round(($montantPlein / 30) * $jours)
                                : $montantPlein;
                            $allowance->jours_work = $jours;
                            $allowance->save();
                        }
                    }

                    // Le brut a changé : prime d'ancienneté puis retenues légales, dans
                    // cet ordre, la prime entrant dans l'assiette des secondes.
                    $employee->refresh();
                    $service->appliquerPrimeAnciennete($employee, $periode);
                    $service->appliquerRetenuesLegales($employee, $periode);
                }
            });
        } catch (\Throwable $e) {
            \Log::error('Traitement en masse de la paie', [
                'periode_id' => $periode->id,
                'action' => $valide['action'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => "Le traitement a échoué : " . $e->getMessage(),
            ], 500);
        }

        $nombre = $employees->count();
        $pluriel = $nombre > 1 ? 's' : '';

        $message = $valide['action'] === 'prime'
            ? $option->name . ' appliquée à ' . $nombre . ' salarié' . $pluriel . '.'
            : ($valide['action'] === 'base' ? 'Salaire de base' : 'Jours travaillés')
                . ' mis à jour pour ' . $nombre . ' salarié' . $pluriel . '.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'traites' => $nombre,
        ]);
    }

    /**
     * Enregistre le détail d'un salarié depuis le tiroir de la paie du mois :
     * jours travaillés, salaire de base et montants des primes, en une fois.
     *
     * C'est le « Enregistrer & recalculer » de la maquette. Comme le traitement en
     * masse, le calcul est confié à SalaryService, et non refait à la main.
     */
    public function enregistrerSalarie(Request $request, $periodeId, $employeeId)
    {
        $periode = PaiePeriode::where('company_id', Auth::user()->company_id)->find($periodeId);

        if (!$periode) {
            return response()->json(['success' => false, 'message' => "Période introuvable."], 404);
        }

        if (in_array($periode->statut, ['payee', 'cloture', 'annulee'], true) || $periode->bulletins()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Période verrouillée : les bulletins sont déjà générés.",
            ], 422);
        }

        $employee = Employee::where('company_id', Auth::user()->company_id)->find($employeeId);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => "Salarié introuvable."], 404);
        }

        $validateur = \Validator::make($request->all(), [
            'jours' => 'required|integer|min:0|max:30',
            'base' => 'required|numeric|min:0',
            'elements' => 'array',
            'elements.*.id' => 'required|integer',
            'elements.*.montant' => 'required|numeric|min:0',
        ], [
            'jours.required' => 'Indiquez les jours travaillés.',
            'jours.max' => 'Les jours travaillés sont comptés sur une base de 30.',
            'base.required' => 'Indiquez le salaire de base.',
            'base.min' => 'Le salaire de base ne peut pas être négatif.',
        ]);

        if ($validateur->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validateur->errors()->first(),
                'errors' => $validateur->errors(),
            ], 422);
        }

        $valide = $validateur->validated();
        $jours = (int) $valide['jours'];
        $service = app(\App\Services\SalaryService::class);

        try {
            \DB::transaction(function () use ($employee, $periode, $valide, $jours, $service) {

                $employee->update([
                    'salary' => (float) $valide['base'],
                    'tax_payer_id' => $jours,
                ]);

                // Montants saisis dans le tiroir. montant porte le droit plein,
                // amount la part du mois travaillée : c'est la convention des primes
                // conventionnelles, et get_salary_imposable() lit montant.
                $saisis = collect($valide['elements'] ?? [])->keyBy('id');

                foreach (Allowance::where('employee_id', $employee->id)
                    ->where('periode_id', $periode->id)->get() as $allowance) {

                    $plein = $saisis->has($allowance->id)
                        ? (float) $saisis[$allowance->id]['montant']
                        : (float) ($allowance->montant ?: $allowance->amount);

                    $allowance->montant = $plein;
                    $allowance->amount = ($allowance->type_amount == 1 && $jours != 30)
                        ? round(($plein / 30) * $jours)
                        : $plein;
                    $allowance->amount_imp = $allowance->amount;
                    $allowance->jours_work = $jours;
                    $allowance->updated_by = auth()->id();
                    $allowance->save();
                }

                $employee->refresh();
                $service->appliquerPrimeAnciennete($employee, $periode);
                $service->appliquerRetenuesLegales($employee, $periode);
            });
        } catch (\Throwable $e) {
            \Log::error('Enregistrement du détail salarié', [
                'periode_id' => $periode->id,
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => "L'enregistrement a échoué : " . $e->getMessage(),
            ], 500);
        }

        $employee->refresh();

        return response()->json([
            'success' => true,
            'message' => $employee->name . ' : montants recalculés.',
            'net' => (float) $employee->get_net_salary($periode->id),
        ]);
    }

    /**
     * Ajoute une variable du mois (retenue, prêt, avantage, heures sup, congé, absence)
     * directement depuis le panneau latéral de la paie, sans changer de page.
     */
    public function ajouterVariableSalarie(Request $request, $periodeId, $employeeId)
    {
        $companyId = Auth::user()->company_id;
        $periode = PaiePeriode::where('company_id', $companyId)->find($periodeId);

        if (!$periode) {
            return response()->json(['success' => false, 'message' => "Période introuvable."], 404);
        }

        if (in_array($periode->statut, ['payee', 'cloture', 'annulee'], true) || $periode->bulletins()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Période verrouillée : les bulletins sont déjà générés.",
            ], 422);
        }

        $employee = Employee::where('company_id', $companyId)->find($employeeId);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => "Salarié introuvable."], 404);
        }

        $theme = $request->input('theme');
        $service = app(\App\Services\SalaryService::class);

        try {
            \DB::transaction(function () use ($request, $periode, $employee, $theme, $companyId) {
                switch ($theme) {
                    case 'retenue':
                        $request->validate([
                            'libelle' => 'required|string|max:255',
                            'amount' => 'required|numeric|min:1',
                        ]);
                        Retenue::create([
                            'employee_id' => $employee->id,
                            'periode_id' => $periode->id,
                            'company_id' => $companyId,
                            'libelle' => $request->input('libelle'),
                            'type_retenue_id' => $request->input('type_retenue_id') ?: null,
                            'amount' => (float) $request->input('amount'),
                            'is_active' => 1,
                            'type' => 'created',
                            'month_paie' => Carbon::parse($periode->date_debut)->format('Y-m'),
                            'date_application' => now(),
                        ]);
                        break;

                    case 'pret':
                        $request->validate([
                            'title' => 'required|string|max:255',
                            'amount' => 'required|numeric|min:1',
                            'amount_deduc' => 'required|numeric|min:1',
                            'nbre_mois' => 'required|integer|min:1',
                        ]);
                        $loanOptionId = $request->input('loan_option');
                        if (!$loanOptionId) {
                            $loanOptionId = \Modules\Loans\Models\LoanOption::where('company_id', $companyId)->value('id') ?: 1;
                        }
                        $nbreMois = (int) $request->input('nbre_mois');
                        $loan = \Modules\Loans\Models\Loan::create([
                            'employee_id' => $employee->id,
                            'periode_id' => $periode->id,
                            'company_id' => $companyId,
                            'loan_option' => $loanOptionId,
                            'title' => $request->input('title'),
                            'type' => 'fixe',
                            'amount' => (float) $request->input('amount'),
                            'amount_deduc' => (float) $request->input('amount_deduc'),
                            'nbre_mois' => $nbreMois,
                            'start_date' => $periode->date_debut,
                            'end_date' => Carbon::parse($periode->date_debut)->addMonths($nbreMois)->format('Y-m-d'),
                            'statut' => 'running',
                            'is_active' => 1,
                            'month_paie' => Carbon::parse($periode->date_debut)->format('Y-m'),
                        ]);

                        \Modules\Loans\Models\LoanPayment::create([
                            'loan_id' => $loan->id,
                            'periode_id' => $periode->id,
                            'amount' => (float) $request->input('amount_deduc'),
                            'payment_date' => $periode->date_paiement ?: now(),
                            'company_id' => $companyId,
                            'note' => 'Échéance appliquée depuis la paie du mois',
                        ]);
                        break;

                    case 'avantage':
                        $request->validate([
                            'libelle' => 'required|string|max:255',
                            'amount' => 'required|numeric|min:1',
                        ]);
                        $montant = (float) $request->input('amount');
                        $avantage = new \Modules\NatureAvantage\Models\Avantage();
                        $avantage->employee_Id = $employee->id;
                        $avantage->periode_id = $periode->id;
                        $avantage->type_avantage = $request->input('type_avantage', 'avantage_en_nature');
                        $avantage->libelle = $request->input('libelle');
                        $avantage->amount_reel = $montant;
                        $avantage->amount = $montant;
                        $avantage->taxe_its = 0;
                        $avantage->taxe_cnps = 0;
                        $avantage->traitement = 'non_soumis';
                        $avantage->status = 'pending';
                        $avantage->is_active = 1;
                        $avantage->company_id = $companyId;
                        $avantage->save();
                        break;

                    case 'heures_sup':
                        $h15 = (float) $request->input('quar_heure', 0);
                        $h50 = (float) $request->input('heure_audd', 0);
                        $h75a = (float) $request->input('heure_nuit_ferie', 0);
                        $h75b = (float) $request->input('heure_dim_ferie', 0);
                        $h100 = (float) $request->input('heure_nuit_dim_ferie', 0);
                        $baseSalaire = (float) ($employee->salary ?: 0);
                        $taux = (float) ($request->input('taux_hour') ?: round($baseSalaire / 173.33, 2));
                        $montantCalc = round(
                            ($h15 * $taux * 1.15) +
                            ($h50 * $taux * 1.50) +
                            ($h75a * $taux * 1.75) +
                            ($h75b * $taux * 1.75) +
                            ($h100 * $taux * 2.00)
                        );
                        $montant = (float) ($request->input('montant') ?: $montantCalc);

                        $overtime = new \Modules\Time\Models\Overtime();
                        $overtime->employee_id = $employee->id;
                        $overtime->periode_id = $periode->id;
                        $overtime->start_date = $request->input('start_date') ?: ($periode->date_debut . ' 08:00:00');
                        $overtime->end_date = $request->input('end_date') ?: ($periode->date_fin . ' 18:00:00');
                        $overtime->quar_heure = $h15;
                        $overtime->heure_audd = $h50;
                        $overtime->heure_nuit_ferie = $h75a;
                        $overtime->heure_dim_ferie = $h75b;
                        $overtime->heure_nuit_dim_ferie = $h100;
                        $overtime->taux_hour = $taux;
                        $overtime->montant = $montant > 0 ? $montant : $montantCalc;
                        $overtime->statut = 'approved';
                        $overtime->company_id = $companyId;
                        $overtime->save();
                        break;

                    case 'conge':
                        $request->validate([
                            'leave_type_id' => 'required',
                            'start_date' => 'required|date',
                            'end_date' => 'required|date',
                        ]);
                        $leave = new \Modules\Leaves\Models\Leave();
                        $leave->employee_id = $employee->id;
                        $leave->periode_id = $periode->id;
                        $leave->leave_type_id = $request->input('leave_type_id');
                        $leave->start_date = $request->input('start_date');
                        $leave->end_date = $request->input('end_date');
                        $leave->leave_back = $request->input('end_date');
                        $leave->applied_on = now();
                        $leave->total_leave_days = (int) $request->input('days', 1);
                        $leave->amount_leave = (float) $request->input('amount_leave', 0);
                        $leave->status = 'Approuvé';
                        $leave->is_active = 1;
                        $leave->company_id = $companyId;
                        $leave->created_by = Auth::id() ?: 1;
                        $leave->save();
                        break;

                    case 'absence':
                        $request->validate([
                            'start_date' => 'required|date',
                            'end_date' => 'required|date',
                        ]);
                        $sheet = new \Modules\Time\Models\TimeSheet();
                        $sheet->employee_id = $employee->id;
                        $sheet->periode_id = $periode->id;
                        $sheet->date = $request->input('start_date');
                        $sheet->arrival_date = $request->input('end_date');
                        $sheet->hours = (float) $request->input('hours', 0);
                        $sheet->retenue = (float) $request->input('days', 0);
                        $sheet->motif_justify = $request->input('motif_justify', 'Non');
                        $sheet->type_permis = $request->input('motif_justify') === 'Oui' ? $request->input('type_permis') : null;
                        $sheet->remark = $request->input('remark');
                        $sheet->monthpaie = Carbon::parse($periode->date_debut)->format('m');
                        $sheet->company_id = $companyId;
                        $sheet->statut = 'approved';
                        $sheet->save();
                        break;

                    default:
                        throw new \Exception("Thématique inconnue : " . $theme);
                }
            });

            // Recalculer légal
            $employee->refresh();
            $service->appliquerPrimeAnciennete($employee, $periode);
            $service->appliquerRetenuesLegales($employee, $periode);

            return response()->json([
                'success' => true,
                'message' => 'Élément ajouté et paie recalculée avec succès.',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Ajout variable salarie', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "L'enregistrement a échoué : " . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Supprime ou retire une variable du mois pour ce salarié et recalcule la paie.
     */
    public function retirerVariableSalarie(Request $request, $periodeId, $employeeId)
    {
        $companyId = Auth::user()->company_id;
        $periode = PaiePeriode::where('company_id', $companyId)->find($periodeId);

        if (!$periode) {
            return response()->json(['success' => false, 'message' => "Période introuvable."], 404);
        }

        if (in_array($periode->statut, ['payee', 'cloture', 'annulee'], true) || $periode->bulletins()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Période verrouillée : les bulletins sont déjà générés.",
            ], 422);
        }

        $employee = Employee::where('company_id', $companyId)->find($employeeId);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => "Salarié introuvable."], 404);
        }

        $type = $request->input('type');
        $id = $request->input('id');
        $service = app(\App\Services\SalaryService::class);

        try {
            switch ($type) {
                case 'retenue':
                    Retenue::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                case 'pret':
                    \Modules\Loans\Models\LoanPayment::where('periode_id', $periode->id)
                        ->where('loan_id', $id)
                        ->delete();
                    $loan = \Modules\Loans\Models\Loan::where('company_id', $companyId)->find($id);
                    if ($loan && $loan->periode_id == $periode->id && $loan->payments()->count() == 0) {
                        $loan->delete();
                    }
                    break;
                case 'avantage':
                    \Modules\NatureAvantage\Models\Avantage::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                case 'overtime':
                    \Modules\Time\Models\Overtime::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                case 'conge':
                    \Modules\Leaves\Models\Leave::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                case 'absence':
                    \Modules\Time\Models\TimeSheet::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                case 'allowance':
                    Allowance::where('company_id', $companyId)->where('id', $id)->delete();
                    break;
                default:
                    throw new \Exception("Type d'élément inconnu : " . $type);
            }

            // Recalculer légal
            $employee->refresh();
            $service->appliquerPrimeAnciennete($employee, $periode);
            $service->appliquerRetenuesLegales($employee, $periode);

            return response()->json([
                'success' => true,
                'message' => 'Élément retiré et paie recalculée avec succès.',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Retrait variable salarie', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Le retrait a échoué : " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Paie du mois : affiche la période à traiter, ou l'écran d'ouverture du mois suivant
     */
    public function paieDuMois(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $dernierePeriode = PaiePeriode::where('company_id', $companyId)
            ->orderBy('date_debut', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // La dernière période n'est pas terminée : on la reprend directement
        if (!$request->query('nouvelle') && $dernierePeriode
            && !in_array($dernierePeriode->statut, ['payee', 'cloture', 'annulee'])) {
            return redirect()->route('company.paiesalaries.periodes.show', $dernierePeriode->id);
        }

        // Mois à ouvrir : celui qui suit la dernière période, sinon le mois en cours
        $debut = $dernierePeriode
            ? Carbon::parse($dernierePeriode->date_fin)->addDay()->startOfMonth()
            : now()->startOfMonth();
        $fin = $debut->copy()->endOfMonth();

        // Une période couvre déjà ce mois : on l'affiche au lieu d'en créer une seconde
        $existante = PaiePeriode::where('company_id', $companyId)
            ->where('date_debut', '<=', $fin->toDateString())
            ->where('date_fin', '>=', $debut->toDateString())
            ->orderBy('date_debut', 'desc')
            ->first();

        if ($existante) {
            if (!in_array($existante->statut, ['payee', 'cloture', 'annulee'])) {
                return redirect()->route('company.paiesalaries.periodes.show', $existante->id);
            }

            return redirect()->route('company.paiesalaries.exercices.show', $existante->exercice_id)
                ->with('error', __('Une période existe déjà pour ce mois. Créez la période suivante depuis l\'exercice.'));
        }

        $moisFr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $nomMois = ucfirst($moisFr[$debut->month - 1]) . ' ' . $debut->year;

        $exercice = PaieExercice::where('company_id', $companyId)
            ->where('date_debut', '<=', $debut->toDateString())
            ->where('date_fin', '>=', $fin->toDateString())
            ->orderBy('date_debut', 'desc')
            ->first();

        if (!$exercice) {
            return redirect()->route('company.paiesalaries.exercices.create', ['retour' => 'paie-du-mois', 'annee' => $debut->year])
                ->with('error', __('Aucun exercice ne couvre :mois. Créez l\'exercice :annee pour ouvrir cette paie.', ['mois' => $nomMois, 'annee' => $debut->year]));
        }

        return view('paiesalaries::periodes.ouvrir', compact('exercice', 'debut', 'fin', 'nomMois', 'dernierePeriode'));
    }

    /**
     * Affiche le formulaire de modification d'une période
     */
    public function editPeriode($id)
    {
        $periode = PaiePeriode::with("exercice")->findOrFail($id);
        return view("paiesalaries::periodes.edit", compact("periode"));
    }

    /**
     * Met à jour une période
     */
    public function updatePeriode(Request $request, $id)
    {
        $periode = PaiePeriode::with("exercice")->findOrFail($id);

        $validated = $request->validate([
            "nom" => "required|string|max:255",
            "date_debut" =>
                "required|date|after_or_equal:" .
                $periode->exercice->date_debut->format("Y-m-d"),
            "date_fin" =>
                "required|date|after:date_debut|before_or_equal:" .
                $periode->exercice->date_fin->format("Y-m-d"),
            "date_paiement" => "nullable|date|after_or_equal:date_fin",
            "type_periode" =>
                "required|in:mensuelle,quinzaine,hebdomadaire,autre",
            "statut" => "required|in:brouillon,en_cours,validee,payee,annulee",
            "notes" => "nullable|string",
        ]);

        $periode->update($validated);

        return redirect()
            ->route("company.paiesalaries.periodes.show", $periode->id)
            ->with("success", "Période de paie mise à jour avec succès.");
    }

    /**
     * Supprime une période
     */
    public function destroyPeriode($id)
    {
        $periode = PaiePeriode::findOrFail($id);
        $exerciceId = $periode->exercice_id;

        // Supprimer en cascade les données associées
        \DB::beginTransaction();
        try {
            // 1. Supprimer les bulletins
            $periode->bulletins()->delete();

            // 2. Supprimer les retenues
            \Modules\PaieSalaries\Models\Retenue::where('periode_id', $periode->id)->delete();

            // 3. Supprimer les primes (Allocations)
            \App\Models\Allowance::where('periode_id', $periode->id)->delete();

            // 4. Supprimer les paiements de prêts
            \Modules\Loans\Models\LoanPayment::where('periode_id', $periode->id)->delete();

            // 5. Supprimer les avantages en nature
            \Modules\NatureAvantage\Models\Avantage::where('periode_id', $periode->id)->delete();

            // 6. Supprimer la période
            $periode->delete();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la suppression de la période : ' . $e->getMessage());
            return back()->with("error", "Une erreur est survenue lors de la suppression de la période.");
        }

        return redirect()
            ->route("company.paiesalaries.exercices.show", $exerciceId)
            ->with("success", "Période de paie supprimée avec succès.");
    }

    /**
     * Affichage des jours travaillés.
     */
    public function show($id, $periode)
    {
        $timesheets = TimeSheet::where('employee_id', $id)
                                ->where('motif_justify', '=', 'Non')
                                ->where('statut', '=', 'approved')
                                ->where('periode_id', '=', $periode)
                                ->sum('retenue');
        $nbre_jour = $timesheets;
        $employee = Employee::findOrFail($id);
        $paytype = PaymentType::all();
        $periodePaie = PaiePeriode::find($periode);
        return view(
            "paiesalaries::payslip.modals.days",
            compact("employee", "nbre_jour", "paytype", "periodePaie"),
        );
    }

    /**
     * Mise à jour des jours travaillés.
     */
    public function update($id, Request $request)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                "employee_id" => "required|exists:employees,id",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Validation error",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            $employee = Employee::findOrFail($id);
            $periode_id = $request->periode_id ?? null;

            // IMPORTANT : on enregistre d'abord les jours travailles et on reproratise
            // les primes AVANT de calculer les charges. Sinon get_salary_social() et
            // get_salary_imposable() renvoient des bases calculees sur l'ANCIEN nombre
            // de jours, et les retenues stockees ne correspondent pas au brut affiche.
            $employee->update([
                "tax_payer_id" => $request->tax_payer_id,
                "paytype" => $request->paytype,
                "branch_location" => (float)($request->branch_location ?: 0),
            ]);

            if ($periode_id) {
                $allowances = Allowance::where('employee_id', $employee->id)
                                        ->where('periode_id', $periode_id)
                                        ->get();
                $retenues = Retenue::where('employee_id', $employee->id)
                                   ->where('periode_id', $periode_id)
                                   ->get();
            } else {
                $allowances = Allowance::where('employee_id', $employee->id)
                                        ->get();
                $retenues = Retenue::where('employee_id', $employee->id)
                                   ->get();
            }

            if(!empty($allowances)){
                foreach($allowances as $allowance){
                    if($allowance->type_amount == 1){
                        if($request->input('tax_payer_id') == 30){
                            $allowance->amount     = $allowance->montant;
                            $allowance->jours_work = $request->input('tax_payer_id');
                            $allowance->save();
                        }else{
                            $allowance->amount     = (($allowance->montant/30) * $request->input('tax_payer_id'));
                            $allowance->jours_work = $request->input('tax_payer_id');
                            $allowance->save();
                        }
                    }else{
                        $allowance->amount     = $allowance->montant;
                        $allowance->jours_work = $request->input('tax_payer_id');
                        $allowance->save();
                    }
                }
            }

            // Recharger l'employe pour que les calculs ci-dessous partent des nouveaux jours
            $employee->refresh();

            //calculons les charges des employés
            $cmu = $employee->cmu;
            $total_sbs = $employee->get_salary_social($periode_id);
            $total_sbi = $employee->get_salary_imposable($periode_id);
            $resultricf = 0;
            $nbre_jours = 0;
            if($cmu < 7){
                $coticmu = ($cmu*500);
                $coticmuemp = ($cmu*500);
            }else{
                $coticmu = 3000+round(($cmu-6)*1000);
                $coticmuemp = 3000;
            }
            $cnps = round($total_sbs*6.3)/100;
            $cnpsemp = round(($total_sbs*7.7)/100);

            $resultcmu =$coticmu;
            $resultcnps = $cnps;
            $resultimpricf = 0;
            $tauxact = 0;
            $act = $employee->company->accident_taux;
            $prt = 0;
            $local = $employee->charge_expat;
            if($act=='0,03'){
                $tauxact= 75000*0.03;
                $prt = 100*0.03;
            }else if($act=='0,02'){
                $tauxact= 75000*0.02;
                $prt = 100*0.02;
            } else if($act=='0,04'){
                $tauxact= 75000*0.04;
                $prt = 100*0.04;
            } else if($act=='0,05'){
                $tauxact= 75000*0.05;
                $prt = 100*0.05;
            }

            $pf=0;
            $pf=((75000*5.75)/100);
            $tt_cnpsemp = $cnpsemp+$tauxact+((75000*5.75)/100)+$pf;

            $tax1 = ($total_sbi*1.2)/100;
            $tax2 = ($total_sbi*9.2)/100;
            $tax3 = ($total_sbi*1.6)/100;
            $tax4 = ($total_sbi*0.4)/100;
            $tax5 = ($total_sbi*1.2)/100;
            $resultcmu = round($coticmu);
            $resultcnps = round($cnps);
            $resulttax1 = round($tax1);
            $resulttax2 = round($tax2);
            $resulttax3 = round($tax3);
            if($local == 'local'){
                $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax3;
            }else{
                $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax2+$tax3;
            }

            $cnps = round($total_sbs*6.3)/100;
            $cnpsemp = round(($total_sbs*7.7)/100);

            if($employee->tax_payer_id =='30'){
                if($total_sbi >= 0 && $total_sbi <= 75000){
                    $tot = ($total_sbi*0)/100;
                    $resultimpricf = round($tot);
                }else if($total_sbi > 75000 && $total_sbi <= 240000){
                    $mt1 = $total_sbi-75000;
                    $tot1 = (((75000*0)/100)+(($mt1*16)/100));
                    $resultimpricf = round($tot1);
                }else if($total_sbi > 240000 && $total_sbi <= 800000){
                    $mt2 = $total_sbi-240000;
                    $tot2 = (((75000*0)/100)+((165000*16)/100)+(($mt2*21)/100));
                    $resultimpricf = round($tot2);
                }else if($total_sbi > 800000 && $total_sbi <= 2400000){
                    $mt3 = $total_sbi-800000;
                    $tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+(($mt3*24)/100));
                    $resultimpricf = round($tot3);
                }else if($total_sbi > 2400000 && $total_sbi <= 8000000){
                    $mt4 = $total_sbi-2400000;
                    $tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+(($mt4*28)/100));
                    $resultimpricf = round($tot4);
                }else if($total_sbi > 8000000){
                    $mt5 = $total_sbi-8000000;
                    $tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+(($mt5*32)/100));
                    $resultimpricf = round($tot5);
                }
                //nombre de part en FCFA
                $nbre = $employee->parts;
                if($nbre==1){
                    $resultricf = 0;
                }else if($nbre==1.5){
                    $resultricf = 5500;
                }else if($nbre==2){
                    $resultricf = 11000;
                }else if($nbre==2.5){
                    $resultricf = 16500;
                }else if($nbre==3){
                    $resultricf = 22000;
                }else if($nbre==3.5){
                    $resultricf = 27500;
                }else if($nbre==4){
                    $resultricf = 33000;
                }else if($nbre==4.5){
                    $resultricf = 38500;
                }else if($nbre==5){
                    $resultricf = 44000;
                }
                $impots = 0;

                $retenue2 = ($resultimpricf - $resultricf);

                if($retenue2>0){
                    $totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
                    $impots = $resultimpricf - $resultricf;
                }else{
                    $totalimpots = $resultcnps + $resultcmu;
                    $impots = 0;
                }
            }elseif($employee->tax_payer_id =='0' && $total_sbi > 0){
                if($total_sbi >= 0 && $total_sbi <= 75000){
                    $tot = ($total_sbi*0)/100;
                    $resultimpricf = round($tot);
                }else if($total_sbi > 75000 && $total_sbi <= 240000){
                    $mt1 = $total_sbi-75000;
                    $tot1 = (((75000*0)/100)+(($mt1*16)/100));
                    $resultimpricf = round($tot1);
                }else if($total_sbi > 240000 && $total_sbi <= 800000){
                    $mt2 = $total_sbi-240000;
                    $tot2 = (((75000*0)/100)+((165000*16)/100)+(($mt2*21)/100));
                    $resultimpricf = round($tot2);
                }else if($total_sbi > 800000 && $total_sbi <= 2400000){
                    $mt3 = $total_sbi-800000;
                    $tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+(($mt3*24)/100));
                    $resultimpricf = round($tot3);
                }else if($total_sbi > 2400000 && $total_sbi <= 8000000){
                    $mt4 = $total_sbi-2400000;
                    $tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+(($mt4*28)/100));
                    $resultimpricf = round($tot4);
                }else if($total_sbi > 8000000){
                    $mt5 = $total_sbi-8000000;
                    $tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+(($mt5*32)/100));
                    $resultimpricf = round($tot5);
                }

                //nombre de part en FCFA
                $nbre = $employee->parts;
                if($nbre==1){
                    $resultricf = (0*$nbre_jours);
                }else if($nbre==1.5){
                    $resultricf = (183*$nbre_jours);
                }else if($nbre==2){
                    $resultricf = (367*$nbre_jours);
                }else if($nbre==2.5){
                    $resultricf = (550*$nbre_jours);
                }else if($nbre==3){
                    $resultricf = (733*$nbre_jours);
                }else if($nbre==3.5){
                    $resultricf = (917*$nbre_jours);
                }else if($nbre==4){
                    $resultricf = (1100*$nbre_jours);
                }else if($nbre==4.5){
                    $resultricf = (1283*$nbre_jours);
                }else if($nbre==5){
                    $resultricf = (1467*$nbre_jours);
                }
                $impots = 0;

                $retenue2 = ($resultimpricf - $resultricf);

                if($retenue2>0){
                    $totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
                    $impots = $resultimpricf - $resultricf;
                }else{
                    $totalimpots = $resultcnps + $resultcmu;
                    $impots = 0;
                }
            }else{
                // $total_sbi est DEJA proratise sur les jours travailles : pour obtenir
                // le salaire journalier il faut diviser par ces jours, pas par 30.
                // Diviser par 30 sous-evaluait le journalier et faisait tomber le
                // salaire dans des tranches trop basses (impot fortement sous-estime).
                // On lit les jours via get_jours_work() : c'est la MEME source que celle
                // qui a servi a proratiser $total_sbi. Utiliser tax_payer_id directement
                // desynchroniserait l'impot du brut pour 28, 29 et 31 jours, que
                // get_jours_work() normalise a 30 (mois complet).
                $nbre_jours = intval($employee->get_jours_work($periode_id));
                if($nbre_jours <= 0){
                    $nbre_jours = 30;
                }
                $day_salary = $total_sbi/$nbre_jours;
                if($day_salary >= 0 && $day_salary <= 2500){
                    $tot = ($day_salary*0)/100;
                    $resultimpricf = round($tot*$nbre_jours);
                }else if($day_salary > 2500 && $day_salary <= 8000){
                    $mt1 = $day_salary-2500;
                    $tot1 = (((2500*0)/100)+(($mt1*16)/100));
                    $resultimpricf = round($tot1*$nbre_jours);
                }else if($day_salary > 8000 && $day_salary <= 26667){
                    $mt2 = $day_salary-8000;
                    $tot2 = (((2500*0)/100)+((5500*16)/100)+(($mt2*21)/100));
                    $resultimpricf = round($tot2*$nbre_jours);
                }else if($day_salary > 26667 && $day_salary <= 80000){
                    $mt3 = $day_salary-26667;
                    $tot3 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+(($mt3*24)/100));
                    $resultimpricf = round($tot3*$nbre_jours);
                }else if($day_salary > 80000 && $day_salary <= 266667){
                    $mt4 = $day_salary-80000;
                    $tot4 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+((53333*24)/100)+(($mt4*28)/100));
                    $resultimpricf = round($tot4*$nbre_jours);
                }else if($day_salary > 266667){
                    $mt5 = $day_salary-266667;
                    $tot5 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+((53333*24)/100)+((186667*28)/100)+(($mt5*32)/100));
                    $resultimpricf = round($tot5*$nbre_jours);
                }
                //nombre de part en FCFA
                $nbre = $employee->parts;
                if($nbre==1){
                    $resultricf = (0*$nbre_jours);
                }else if($nbre==1.5){
                    $resultricf = (183*$nbre_jours);
                }else if($nbre==2){
                    $resultricf = (367*$nbre_jours);
                }else if($nbre==2.5){
                    $resultricf = (550*$nbre_jours);
                }else if($nbre==3){
                    $resultricf = (733*$nbre_jours);
                }else if($nbre==3.5){
                    $resultricf = (917*$nbre_jours);
                }else if($nbre==4){
                    $resultricf = (1100*$nbre_jours);
                }else if($nbre==4.5){
                    $resultricf = (1283*$nbre_jours);
                }else if($nbre==5){
                    $resultricf = (1467*$nbre_jours);
                }
                $impots = 0;

                $retenue2 = ($resultimpricf - $resultricf);

                if($retenue2>0){
                    $totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
                    $impots = $resultimpricf - $resultricf;
                }else{
                    $totalimpots = $resultcnps + $resultcmu;
                    $impots = 0;
                }
            }

            // NB : la mise a jour de l'employe et la reproratisation des primes ont
            // deja ete faites en debut de methode, avant le calcul des charges.

            if(!empty($retenues)) {
                foreach($retenues as $retenue){
                    if( $retenue->code == 401 ){
                        // Impôts bruts avant RICF
                        $retenue->amount = $resultimpricf;
                    }
                    if( $retenue->code == 402 ){
                        // Réduction pour Charges de Famille
                        $retenue->amount = $resultricf;
                    }
                    if( $retenue->code == 403 ){
                        // Impôts Nets
                        $retenue->amount = $impots;
                    }
                    if( $retenue->code == 301 ){
                        // Cotisation Retraite CNPS
                        $retenue->base = $total_sbs;
                        $retenue->amount = $resultcnps;
                    }
                    if( $retenue->code == 302 ){
                        // Couverture Maladie Universelle
                        $retenue->base = 1000;
                        $retenue->amount = $resultcmu;
                        // Le bulletin affiche jours_work dans la colonne NOMBRE :
                        // on le resynchronise sur le nombre de beneficiaires, sinon
                        // le montant change mais le nombre affiche reste l'ancien.
                        $retenue->jours_work = $cmu;
                    }
                    if( $retenue->code == 410 ){
                        // Contribution employeur (Expatrié)
                        if($local == 'local'){
                            $retenue->amount = $resulttax2;
                        }else{
                            $retenue->amount = 0;
                        }
                    }
                    if( $retenue->code == 409 ){
                        // Contribution Employeur
                        $retenue->base = $total_sbi;
                        $retenue->amount = $resulttax1;
                    }
                    if( $retenue->code == 411 ){
                        // Taxe d’Apprentissage
                        $retenue->base = $total_sbs;
                        $retenue->amount = round($tax4);
                    }
                    if( $retenue->code == 412 ){
                        // Taxe.F.P.C
                        $retenue->base = $total_sbs;
                        $retenue->amount = round($tax5);
                    }
                    if( $retenue->code == 305 ){
                        // Accident de travail
                        $retenue->amount = round($tauxact);
                    }
                    if( $retenue->code == 306 ){
                        // Prestation Familiale
                        $retenue->amount = round($pf);
                    }
                    if( $retenue->code == 308 ){
                        // Cotisation retraite employeur
                        $retenue->amount = $cnpsemp;
                    }
                    if( $retenue->code == 307 ){
                        // Autres
                        $retenue->amount = $coticmuemp;
                    }
                    $retenue->save();
                }
            }

            // Retenues légales recalculées (et créées si absentes) avec le calcul de référence
            if ($periode_id && ($periodeRetenues = PaiePeriode::find($periode_id))) {
                app(\App\Services\SalaryService::class)->appliquerRetenuesLegales($employee->refresh(), $periodeRetenues);
            }

            \DB::commit();

            return response()->json([
                "success" => true,
                "message" =>
                    'Nombre de jours travaillés de l\'employé mis à jour avec succès',
                "data" => $employee,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error(
                'Erreur lors de la mise à jour du nombre de jours travaillés de l\'employé: ' .
                    $e->getMessage(),
            );

            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        'Une erreur est survenue lors de la mise à jour du nombre de jours travaillés de l\'employé',
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Recalcule les retenues d'un salarié après une modification de ses primes.
     */
    private function recalculateEmployeeRetenues($employeeId, $periodeId): void
    {
        $employee = Employee::findOrFail($employeeId);

        $response = $this->update(
            $employeeId,
            new Request([
                "employee_id" => $employeeId,
                "periode_id" => $periodeId,
                "tax_payer_id" => $employee->tax_payer_id,
                "paytype" => $employee->paytype,
                "branch_location" => $employee->branch_location,
            ]),
        );

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException(
                "Le recalcul automatique des retenues a échoué.",
            );
        }
    }

    /**
     * Stockage d'une option de prime.
     */
    public function storeOption(Request $request)
    {
        try {
            $validated = $request->validate([
                "allowance_option" => "required|string|max:255",
                "trait_fisc" => "required|string|max:255",
                "trait_cnps" => "required|string|max:255",
                "code_compta" => "nullable|string|max:255",
                "type_montant" => "required|in:1,0",
                "alinea" => "nullable|in:100%-Art 116-2,100%-Art 116-10,100%-Art 116-12",
                "amount_imp" => "nullable|numeric",
                "amount_imp_fisc" => "nullable|numeric",
            ]);

            $lastOption = AllowanceOption::where('company_id', Auth::user()->company_id)->orderBy('code', 'desc')->first();
            $option = new AllowanceOption();
            $option->code = $lastOption->code + 1;
            $option->name = $validated["allowance_option"];
            if (isset($validated["alinea"]) && $validated["alinea"]) {
                $option->param_fiscal =
                    $validated["trait_fisc"] . " - " . $validated["alinea"] . " - Montant exo: " . ($validated["amount_imp_fisc"] ?? 'N/A');
            } else {
                $option->param_fiscal = $validated["trait_fisc"];
            }
            if (isset($validated["amount_imp"]) && $validated["amount_imp"]) {
                $option->param_social =
                    $validated["trait_cnps"] .
                    " - MT : " .
                    $validated["amount_imp"];
            } else {
                $option->param_social = $validated["trait_cnps"];
            }
            $option->type_montant = $validated["type_montant"];
            $option->type = "created";
            $option->company_id = Auth::user()->company_id;
            $option->created_by = auth()->id();
            $option->code_compta = $validated["code_compta"];
            $option->save();

            return redirect()
                ->route("company.paiesalaries.allowance.index")
                ->with("success", "Option de prime ajoutée avec succès");
        } catch (\Throwable $th) {
            \Log::error('Error creating allowance option: ' . $th->getMessage());
            return redirect()
                ->route("company.paiesalaries.allowance.index")
                ->with("error", "Option de prime non ajoutée");
        }
    }

    /**
     * Enregistrer les éléments par défaut sélectionnés pour l'entreprise
     */
    public function saveDefaults(Request $request)
    {
        \DB::beginTransaction();
        try {
            $elements = $request->input('elements', []);

            // Si les éléments sont envoyés en JSON, les décoder
            if (is_string($elements)) {
                $elements = json_decode($elements, true) ?: [];
            }

            if (empty($elements)) {
                return redirect()
                    ->route('company.paiesalaries.allowance.index')
                    ->with('error', 'Aucun élément sélectionné.');
            }

            $companyId = Auth::user()->company_id;
            $savedCount = 0;
            try {
                foreach ($elements as $defaultElementId) {
                    // Récupérer l'élément par défaut
                    $defaultElement = AllowanceOption::where('id', $defaultElementId)
                        ->where('type', 'default')
                        ->where('company_id', 1)
                        ->first();

                    if ($defaultElement) {
                        // Vérifier si l'élément n'existe pas déjà pour cette entreprise
                        $existingElement = AllowanceOption::where('company_id', $companyId)
                            ->where('name', $defaultElement->name)
                            ->first();

                        if (!$existingElement) {
                            // Créer une copie pour l'entreprise
                            $newElement = new AllowanceOption();
                            $newElement->name = $defaultElement->name;
                            $newElement->code = $defaultElement->code;
                            $newElement->param_fiscal = $defaultElement->param_fiscal;
                            $newElement->param_social = $defaultElement->param_social;
                            $newElement->type_montant = $defaultElement->type_montant;
                            $newElement->type = 'default'; // Marquer comme créé pour cette entreprise
                            $newElement->company_id = $companyId;
                            $newElement->created_by = auth()->id();
                            $newElement->code_compta = $defaultElement->code_compta;
                            $newElement->is_active = 1;
                            $newElement->save();
                            $savedCount++;
                        }
                    }
                }
            }catch (\Throwable $th) {
                \DB::rollBack();
                \Log::error(
                    'Erreur lors de la mise à jour des primes par defaut' .
                        $th->getMessage(),
                );
            }

            \DB::commit();
            return redirect()
                ->route('company.paiesalaries.allowance.index')
                ->with('success', "{$savedCount} élément(s) ont été enregistrés avec succès pour votre entreprise.");

        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error(
                'Erreur lors de la mise à jour des primes par defaut' .
                    $th->getMessage(),
            );
            return redirect()
                ->route('company.paiesalaries.allowance.index')
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des éléments.');
        }
    }

    /**
     * Affichage de la modale de modification d'une option de prime.
     */
    public function editOption($id)
    {
        $option = AllowanceOption::findOrFail($id);
        return view(
            "paiesalaries::allowance.modals.editElement-form",
            compact("option"),
        );
    }

    /**
     * Mise à jour d'une option de prime.
     */
    public function updateOption(Request $request, $id)
    {
        $option = AllowanceOption::findOrFail($id);
        $option->update($request->all());
        return redirect()
            ->route("company.paiesalaries.allowance.index")
            ->with("success", "Option de prime mise à jour avec succès");
    }

    /**
     * Affichage de la modale de suppression d'une option de prime.
     */
    public function showOption($id)
    {
        $option = AllowanceOption::findOrFail($id);
        return view("paiesalaries::allowance.show", compact("option"));
    }

    /**
     * Suppression d'une option de prime.
     */
    public function destroyOption($id)
    {
        $option = AllowanceOption::findOrFail($id);
        $option->delete();
        return redirect()
            ->route("company.paiesalaries.allowance.index")
            ->with("success", "Option de prime supprimée avec succès");
    }

    /**
     * Liste des primes.
     */
    public function allowance(Request $request)
    {
        $periode = null;
        if ($request->has("periode_id")) {
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        } else {
            $periode = $this->getActivePeriode();
        }

        $elements = AllowanceOption::where("company_id", Auth::user()->company_id)
            ->where("is_active", 1)
            ->orderBy("id", "desc")
            ->get();

        $company = Auth::user()->company_id;

        $elementsDefault = AllowanceOption::where("type", "default")
            ->where("company_id", 1)
            ->where("is_active", 1)
            ->orderBy("id", "desc")
            ->get();

        $periodes = PaiePeriode::orderBy("date_debut", "desc")->get();
        $employees = Employee::active()
            ->where("company_id", $company)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        return view("paiesalaries::allowance.index", compact("elements", "periodes", "periode", "company", "employees", "elementsDefault"));
    }

    /**
     * Affichage de la modale d'ajout de prime au salarié.
     */
    public function createAllowance($employeeId, $periodeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $periode = PaiePeriode::findOrFail($periodeId);
        $company = Company::findOrFail(Auth::user()->company_id);
        $allowances = Allowance::where("company_id", $company->id)
            ->where("employee_id", $employee->id)
            ->where("periode_id", $periode->id)
            ->pluck("allowance_option_id")
            ->toArray();
        $countAllowances = Allowance::where("company_id", $company->id)
            ->where("employee_id", $employee->id)
            ->where("periode_id", $periode->id)
            ->pluck("allowance_option_id")
            ->count();
        $allowancesDefault = AllowanceOption::where("type", "default")->where("company_id", $company->id)->get();
        $allowancesCreated = AllowanceOption::where("type", "created")
            ->where("company_id", $company->id)
            ->get();
        return view(
            "paiesalaries::payslip.modals.create",
            compact(
                "periode",
                "employee",
                "allowancesDefault",
                "allowancesCreated",
                "company",
                "allowances",
                "countAllowances",
            ),
        );
    }

    /**
     * Stockage des primes ajoutées au salarié.
     */
    public function storeAllowance(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                "employee_id" => "required",
                "periode_id" => "required",
                "cpte" => "required|integer|min:1",
            ]);

            \Log::info("Request data:", [
                "employee_id" => $request->employee_id,
                "periode_id" => $request->periode_id,
                "cpte" => $request->cpte,
                "all_keys" => array_keys($request->all()),
            ]);

            if ($validator->fails()) {
                \Log::error("Validation failed", [
                    "errors" => $validator->errors(),
                ]);
                return redirect()
                    ->back()
                    ->with(
                        "error",
                        "Vous devez sélectionner au moins un élément à enregistrer.",
                    );
            }

            $cpte = (int) $request->cpte;
            $nbre_jours = $request->input("nbre_jour", 30);
            $employee_id = $request->employee_id;
            $periode_id = $request->periode_id;

            \Log::info("Starting allowance creation", [
                "employee_id" => $employee_id,
                "periode_id" => $periode_id,
                "item_count" => $cpte,
            ]);

            $savedCount = 0;

            // Parcourir tous les champs de la requête pour trouver les item_brut
            foreach ($request->all() as $key => $value) {
                if (strpos($key, "item_brut") === 0 && !empty($value)) {
                    // Extraire l'ID du champ (ex: item_brut4 -> 4)
                    $id = str_replace("item_brut", "", $key);

                    // Vérifier que l'amount existe et est supérieur à 0
                    $amount = $request->input("amount" . $id, 0);

                    \Log::info("Processing item", [
                        "id" => $id,
                        "item_brut" => $value,
                        "amount" => $amount,
                        "amount_key" => "amount" . $id,
                    ]);

                    if ($amount > 0) {
                        $allowance = new Allowance();
                        $allowance->code = $request->input("code" . $id, "");
                        $allowance->code_compta = $request->input(
                            "code_compta" . $id,
                            "",
                        );
                        $allowance->employee_id = $employee_id;
                        $allowance->allowance_option_id = $value; // Utiliser $value qui contient item_brut
                        $allowance->periode_id = $periode_id;
                        $allowance->title = $request->input(
                            "allowance_name" . $id,
                            "",
                        );
                        $allowance->trait_fisc = $request->input(
                            "trait_fisc" . $id,
                            "",
                        );
                        $allowance->trait_cnps = $request->input(
                            "trait_cnps" . $id,
                            "",
                        );
                        $allowance->base_heures = 0;
                        $allowance->jours_leave = 0;
                        $allowance->amount_imp = $request->input(
                            "amount_imp" . $id,
                            0,
                        );
                        $allowance->amount = $amount;
                        $allowance->montant = $request->input(
                            "montant" . $id,
                            0,
                        );
                        $allowance->details = $request->input(
                            "details" . $id,
                            "",
                        );
                        $allowance->jours_work = $nbre_jours;
                        $allowance->type_amount = $request->input(
                            "jours_work" . $id,
                            0,
                        );
                        $allowance->type = "fixed";
                        $allowance->company_id = \Auth::user()->company_id;
                        $allowance->created_by = \Auth::user()->id;

                        if ($allowance->save()) {
                            $savedCount++;
                            \Log::info("Allowance saved successfully", [
                                "id" => $allowance->id,
                                "employee_id" => $employee_id,
                                "allowance_option_id" => $value,
                                "amount" => $amount,
                            ]);
                        }
                    }
                }
            }

            if ($savedCount > 0) {
                $this->recalculateEmployeeRetenues($employee_id, $periode_id);
            }

            \DB::commit();

            if ($savedCount > 0) {
                \Log::info("Successfully saved allowances", [
                    "count" => $savedCount,
                ]);
                return redirect()
                    ->back()
                    ->with(
                        "success",
                        $savedCount . " prime(s) ajoutée(s) avec succès.",
                    );
            } else {
                \Log::warning("No allowances were saved");
                return redirect()
                    ->back()
                    ->with(
                        "warning",
                        "Aucune prime valide à enregistrer. Vérifiez que les montants sont supérieurs à 0.",
                    );
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving allowances: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return redirect()
                ->back()
                ->with(
                    "error",
                    'Une erreur est survenue lors de l\'enregistrement: ' .
                        $e->getMessage(),
                );
        }
    }

    /**
     * Affichage de la modale de visualisation d'une prime.
     */
    public function showAllowance($id, $periode)
    {
        $employee = Employee::with("allowances")->findOrFail($id);
        return view("paiesalaries::payslip.modals.show", compact("employee", "periode"));
    }

    /**
     * Affichage de la modale de modification d'une prime.
     */
    public function editAllowance($employeeId, $periode)
    {
        $employee = Employee::findOrFail($employeeId);
        $company = Company::findOrFail(Auth::user()->company_id);
        $allowances = Allowance::where("company_id", $company->id)
            ->where("employee_id", $employee->id)
            ->where("periode_id", $periode)
            ->pluck("allowance_option_id")
            ->toArray();
        $countAllowances = Allowance::where("company_id", $company->id)
            ->where("employee_id", $employee->id)
            ->where("periode_id", $periode)
            ->pluck("allowance_option_id")
            ->count();
        $allowanceEmployee = Allowance::with("allowanceOption")
            ->where("company_id", $company->id)
            ->where("employee_id", $employee->id)
            ->where("periode_id", $periode)
            ->get();
        $allowancesDefault = AllowanceOption::where("type", "default")->get();
        $allowancesCreated = AllowanceOption::where("type", "created")
            ->where("company_id", $company->id)
            ->get();
        return view(
            "paiesalaries::payslip.modals.edit",
            compact(
                "periode",
                "employee",
                "allowancesDefault",
                "allowancesCreated",
                "company",
                "allowances",
                "countAllowances",
                "allowanceEmployee",
            ),
        );
    }

    /**
     * Met à jour une allocation existante
     */
    public function updateAllowance(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                "employee_id" => "required|exists:employees,id",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Validation error",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            $allowance = Allowance::where("id", $id)
                ->where("employee_id", $request->employee_id)
                ->where("company_id", Auth::user()->company_id)
                ->firstOrFail();

            // Vérifier si l'allocation appartient bien à l'employé spécifié
            if ($allowance->employee_id != $request->employee_id) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Cette allocation ne correspond pas à cet employé.",
                    ],
                    403,
                );
            }

            $allowanceOptionId = $request->allowance_option_id;

            // Mettre à jour l'allocation
            $allowance->update([
                "montant" => $request->montant . "" . $allowanceOptionId,
                "amount" => $request->amount . "" . $allowanceOptionId,
                "type_amount" => $request->jours_work . "" . $allowanceOptionId,
                "updated_by" => auth()->id(),
            ]);

            $this->recalculateEmployeeRetenues(
                $allowance->employee_id,
                $allowance->periode_id,
            );

            \DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Allocation mise à jour avec succès",
                "data" => $allowance,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error(
                'Erreur lors de la mise à jour de l\'allocation: ' .
                    $e->getMessage(),
            );

            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        'Une erreur est survenue lors de la mise à jour de l\'allocation',
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Supprime une allocation
     */
    public function destroyAllowance($id)
    {
        \DB::beginTransaction();
        try {
            $user = Auth::user();
            $companyId = $user->company_id ?: ($user->creatorId() ?? $user->id);

            $allowance = Allowance::where("id", $id)
                ->where(function ($q) use ($companyId, $user) {
                    $q->where("company_id", $companyId)
                      ->orWhere("company_id", $user->id)
                      ->orWhereNull("company_id");
                })
                ->firstOrFail();

            $employeeId = $allowance->employee_id;
            $periodeId = $allowance->periode_id;

            $allowance->delete();

            if ($employeeId && $periodeId) {
                $this->recalculateEmployeeRetenues(
                    $employeeId,
                    $periodeId,
                );
            }

            \DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Prime retirée avec succès",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Prime non trouvée ou déjà supprimée",
                ],
                404,
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error(
                'Erreur lors de la suppression de l\'allocation: ' .
                    $e->getMessage(),
            );

            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        'Une erreur est survenue lors de la suppression de l\'allocation',
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Affichage de la liste des retenues
     */
    public function retenues(Request $request)
    {
        $periode = null;
        if ($request->has("periode_id")) {
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        } else {
            $periode = $this->getActivePeriode();
        }

        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $lastOrder = Employee::where("employees.is_active", 1)
            ->where("employees.company_id", Auth::user()->company_id)
            ->join("retenues", "employees.id", "=", "retenues.employee_id")
            ->orderBy("retenues.ordre", "desc")
            ->first();

        $retenues = Retenue::with(
            "typeRetenue",
            "employees",
            "periode",
            "company",
        )
            ->where("company_id", Auth::user()->company_id)
            ->where("type", "created")
            ->where("periode_id", $periode->id)
            ->where("type_retenue_id",'!=', 5)
            ->get();

        $typesRetenues = TypeRetenue::where("id",'!=', 5)
            ->get();

        // Retenues légales toujours appliquées : plus d'action manuelle sur cette page
        // Prime d'ancienneté (Art. 55 CCI) appliquée d'office, avant les retenues.
        app(\App\Services\SalaryService::class)->appliquerPrimeAncienneteperiode($periode);
        app(\App\Services\SalaryService::class)->appliquerRetenuesLegalesPeriode($periode);

        $employees = Employee::active()
            ->with("retenues")
            ->where("company_id", Auth::user()->company_id)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        $periodes = PaiePeriode::with("exercice")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        return view(
            "paiesalaries::retenues.index",
            compact(
                "retenues",
                "typesRetenues",
                "periode",
                "employees",
                "periodes",
                "lastOrder",
                "exercices"
            ),
        );
    }

    public function applyRetenue($id, $periode_id)
    {
        $company = Company::findOrFail(Auth::user()->company_id);
        $employee = Employee::with(
            "overtimes",
            "avantages",
            "leaves",
            "allowances",
            "ruptures",
            "loans",
            "retenues",
        )->findOrFail($id);
        $periode = PaiePeriode::where("id", $periode_id)->first();
        $employeeBaseDix = Allowance::where("employee_id", $id)
            ->where("trait_fisc", "exo 100%")
            ->sum("amount");
        $total_cantine = Allowance::where("employee_id", $id)
            ->where("title", "Frais de restauration (Cantine)")
            ->sum("amount");
        return view(
            "paiesalaries::retenues.modals.apply",
            compact("employee", "employeeBaseDix", "total_cantine", "periode", "company"),
        );
    }

    public function storeRetenue(Request $request)
    {
        \DB::beginTransaction();
        try {
            if ($request->employee_id) {
                $validator = \Validator::make($request->all(), [
                    "employee_id" => "required",
                    "periode_id" => "required",
                ]);

                if ($validator->fails()) {
                    \Log::error("Validation failed", [
                        "errors" => $validator->errors(),
                    ]);
                    return redirect()
                        ->back()
                        ->with(
                            "error",
                            "Vous devez sélectionner au moins un élément à enregistrer.",
                        );
                }

                $cpte = 14;
                $employee_id = $request->employee_id;
                $type = "default";
            } else {
                $validator = \Validator::make($request->all(), [
                    "periode_id" => "required",
                ]);

                if ($validator->fails()) {
                    \Log::error("Validation failed", [
                        "errors" => $validator->errors(),
                    ]);
                    return redirect()
                        ->back()
                        ->with(
                            "error",
                            "Vous devez sélectionner au moins une période enregistrée.",
                        );
                }

                $employee_id = null;
                $cpte = 1;
                $type = "created";
            }

            $periode_id = $request->periode_id;

            \Log::info("Starting retenues creation", [
                "employee_id" => $employee_id,
                "periode_id" => $periode_id,
                "item_brut" => $cpte,
            ]);

            $savedCount = 0;


            foreach ($request->all() as $key => $value) {
                if (strpos($key, "item_brut") === 0 && !empty($value)) {
                    // Extraire l'ID du champ (ex: item_brut4 -> 4)
                    $id = str_replace("item_brut", "", $key);


                    $amount = $request->input("amount" . $id, 0);

                    \Log::info("Processing item", [
                        "id" => $id,
                        "item_brut" => $value,
                        "amount" => $amount,
                    ]);

                    $code = $request->input("code" . $id, "");

                    $retenue = Retenue::updateOrCreate(
                        [
                            "employee_id" => $employee_id,
                            "periode_id" => $periode_id,
                            "code" => $code,
                        ],
                        [
                            "libelle" => $request->input("libelle" . $id, ""),
                            "type_retenue_id" => $request->has("type_retenue_id" . $id)
                                ? $request->input("type_retenue_id" . $id, "")
                                : null,
                            "ordre" => $request->input("ordre" . $id, ""),
                            "salariale" => $request->input("salariale" . $id, ""),
                            "patronale" => $request->input("patronale" . $id, ""),
                            "base" => $request->input("base" . $id, ""),
                            "taux" => $request->input("taux" . $id, ""),
                            "amount" => $request->input("amount" . $id, ""),
                            "jours_work" => $request->input("jours_work" . $id, ""),
                            "date_application" => now(),
                            "is_active" => 1,
                            "type" => $type,
                            "month_paie" => now()->format("Y-m"),
                            "company_id" => \Auth::user()->company_id,
                        ]
                    );

                    if ($retenue) {
                        $savedCount++;
                        \Log::info("Retenue saved/updated successfully", [
                            "id" => $retenue->id,
                            "employee_id" => $employee_id,
                            "amount" => $amount,
                        ]);
                    }

                }
            }

            \DB::commit();

            if ($savedCount > 0) {
                \Log::info("Successfully saved allowances", [
                    "count" => $savedCount,
                ]);
                return redirect()->back()->with("success", $savedCount . " retenues ajoutées avec succès.");
            } else {
                \Log::warning("No allowances were saved");
                return redirect()->back()->with("warning", "Aucune retenue valide à enregistrer. Vérifiez que les montants sont supérieurs à 0.");
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving allowances: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return redirect()->back()->with("error", 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function storeRetenueApply(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                "employee_id" => "required",
                "periode_id" => "required",
            ]);

            if ($validator->fails()) {
                \Log::error("Validation failed", [
                    "errors" => $validator->errors(),
                ]);
                return redirect()
                    ->back()
                    ->with(
                        "error",
                        "Vous devez sélectionner au moins un élément à enregistrer.",
                    );
            }

            $cpte = 14;
            $employee_id = $request->employee_id;
            $type = "default";

            $periode_id = $request->periode_id;

            \Log::info("Starting retenues creation", [
                "employee_id" => $employee_id,
                "periode_id" => $periode_id,
                "item_brut" => $cpte,
            ]);

            $savedCount = 0;

            // Parcourir tous les champs de la requête pour trouver les item_brut
            foreach ($request->all() as $key => $value) {
                if (strpos($key, "item_brut") === 0 && !empty($value)) {
                    // Extraire l'ID du champ (ex: item_brut4 -> 4)
                    $id = str_replace("item_brut", "", $key);

                    // Vérifier que l'amount existe et est supérieur à 0
                    $amount = $request->input("amount" . $id, 0);

                    \Log::info("Processing item", [
                        "id" => $id,
                        "item_brut" => $value,
                        "amount" => $amount,
                    ]);

                    $code = $request->input("code" . $id, "");

                    $retenue = Retenue::updateOrCreate(
                        [
                            "employee_id" => $employee_id,
                            "periode_id" => $periode_id,
                            "code" => $code,
                        ],
                        [
                            "libelle" => $request->input("libelle" . $id, ""),
                            "type_retenue_id" => $request->has("type_retenue_id" . $id)
                                ? $request->input("type_retenue_id" . $id, "")
                                : null,
                            "ordre" => $request->input("ordre" . $id, ""),
                            "salariale" => $request->input("salariale" . $id, ""),
                            "patronale" => $request->input("patronale" . $id, ""),
                            "base" => $request->input("base" . $id, ""),
                            "taux" => $request->input("taux" . $id, ""),
                            "amount" => $request->input("amount" . $id, ""),
                            "jours_work" => $request->input("jours_work" . $id, ""),
                            "date_application" => now(),
                            "is_active" => 1,
                            "type" => $type,
                            "month_paie" => now()->format("Y-m"),
                            "company_id" => \Auth::user()->company_id,
                        ]
                    );

                    if ($retenue) {
                        $savedCount++;
                        \Log::info("Retenue saved/updated successfully", [
                            "id" => $retenue->id,
                            "employee_id" => $employee_id,
                            "amount" => $amount,
                        ]);
                    }

                }
            }

            \DB::commit();

            if ($savedCount > 0) {
                \Log::info("Successfully saved allowances", [
                    "count" => $savedCount,
                ]);
                return redirect()->back()->with("success", $savedCount . " retenues ajoutées avec succès.");
            } else {
                \Log::warning("No allowances were saved");
                return redirect()->back()->with("warning", "Aucune retenue valide à enregistrer. Vérifiez que les montants sont supérieurs à 0.");
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving allowances: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return redirect()->back()->with("error", 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function editRetenue($id)
    {
        $retenue = Retenue::findOrFail($id);
        $typesRetenues = TypeRetenue::where("id",'!=', 5)
            ->get();
        $lastOrder = Employee::where("employees.is_active", 1)
            ->where("employees.company_id", Auth::user()->company_id)
            ->join("retenues", "employees.id", "=", "retenues.employee_id")
            ->orderBy("retenues.ordre", "desc")
            ->first();
        return view(
            "paiesalaries::retenues.modals.edit-retenue-form",
            compact("retenue", "typesRetenues", "lastOrder"),
        );
    }

    public function updateRetenue(Request $request, $id)
    {
        $validated = $request->validate([
            "libelle" => "required|string|max:255",
            "type_retenue_id" => "required|exists:type_retenues,id",
            "amount" => "nullable|numeric|min:0",
            "taux" => "nullable|numeric|min:0|max:100",
            "ordre" => "required|integer",
            "salariale" => "nullable|integer|min:0",
            "patronale" => "nullable|integer|min:0",
        ]);

        $retenue = Retenue::findOrFail($id);
        $retenue->ordre = $validated["ordre"];
        $retenue->salariale = $validated["salariale"];
        $retenue->patronale = $validated["patronale"];
        $retenue->libelle = $validated["libelle"];
        $retenue->amount = $validated["amount"];
        $retenue->taux = $validated["taux"];
        $retenue->is_active = $request->has("statut");
        $retenue->type_retenue_id = $validated["type_retenue_id"];
        $retenue->save();

        return response()->json([
            "success" => true,
            "message" => "Retenue mise à jour avec succès",
        ]);
    }

    public function destroyRetenue($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->delete();

        return response()->json([
            "success" => true,
            "message" => "Retenue supprimée avec succès",
        ]);
    }

    /**
     * Active une retenue
     */
    public function activateRetenue($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => true]);

        return response()->json([
            'success' => 'Retenue activée avec succès',
            'is_active' => true
        ]);
    }

    /**
     * Désactive une retenue
     */
    public function deactivateRetenue($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => false]);

        return response()->json([
            'success' => 'Retenue désactivée avec succès',
            'is_active' => false
        ]);
    }

    /**
     * Ajoute une retenue à un employé
     */
    public function addEmployeeRetenue($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenueCount = Retenue::where("company_id", Auth::user()->company_id)
            ->where("periode_id", $retenue->periode_id)
            ->where("libelle", $retenue->libelle)
            ->where("type", 'add')
            ->count();
        $employees = Employee::active()
            ->where("company_id", Auth::user()->company_id)
            ->get();
        return view("paiesalaries::retenues.modals.add-retenue", compact("retenue", "employees", "retenueCount"));
    }

    public function storeEmployeeRetenue(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'employees' => 'required|array|min:1',
                'employees.*' => 'exists:employees,id',
                'periode_id' => 'required|exists:paie_periodes,id',
            ], [
                'employees.required' => 'Veuillez sélectionner au moins un employé.',
                'employees.*.exists' => 'Un ou plusieurs employés sélectionnés sont invalides.',
                'periode_id.required' => 'La période est requise.',
                'periode_id.exists' => 'La période sélectionnée est invalide.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $originalRetenue = Retenue::findOrFail($id);
            $savedCount = 0;
            $employeeIds = $request->input('employees', []);

            foreach ($employeeIds as $employeeId) {
                // Vérifier si la retenue existe déjà pour cet employé et cette période
                $existingRetenue = Retenue::where('employee_id', $employeeId)
                    ->where('periode_id', $request->periode_id)
                    ->where('type_retenue_id', $originalRetenue->type_retenue_id)
                    ->first();

                if ($existingRetenue) {
                    continue; // Passe à l'employé suivant si une retenue existe déjà
                }

                $newRetenue = new Retenue();
                $newRetenue->fill([
                    'libelle' => $originalRetenue->libelle,
                    'type_retenue_id' => $originalRetenue->type_retenue_id,
                    'employee_id' => $employeeId,
                    'periode_id' => $request->periode_id,
                    'code' => $originalRetenue->code,
                    'ordre' => $originalRetenue->ordre,
                    'patronale' => $originalRetenue->patronale,
                    'salariale' => $originalRetenue->salariale,
                    'base' => $originalRetenue->base,
                    'taux' => $originalRetenue->taux,
                    'amount' => $originalRetenue->amount,
                    'jours_work' => $originalRetenue->jours_work,
                    'date_application' => now(),
                    'is_active' => $originalRetenue->is_active,
                    'type' => 'add',
                    'month_paie' => now()->format('Y-m'),
                    'company_id' => Auth::user()->company_id,
                ]);

                if ($newRetenue->save()) {
                    $savedCount++;
                    \Log::info('Retenue ajoutée avec succès', [
                        'employee_id' => $employeeId,
                        'retenue_id' => $newRetenue->id
                    ]);
                }
            }

            \DB::commit();

            if ($savedCount > 0) {
                return redirect()->back()->with([
                    'success' => 'Retenue appliquée avec succès à ' . $savedCount . ' employé(s).',
                    'count' => $savedCount
                ]);
            } else {
                return redirect()->back()->with([
                    'success' => 'Aucune nouvelle retenue n\'a été ajoutée. Les employés sélectionnés ont peut-être déjà cette retenue pour cette période.'
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving allowances: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return redirect()
                ->back()
                ->with(
                    "error",
                    'Une erreur est survenue lors de l\'enregistrement: ' .
                        $e->getMessage(),
                );
        }
    }

    public function destroyRetenueAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->delete();

        return redirect()->back()->with([
            "success" => "Retenue supprimée avec succès pour l'employé",
        ]);
    }

    public function activateRetenueAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => true]);

        return redirect()->back()->with([
            'success' => 'Retenue activée avec succès pour l\'employé',
            'is_active' => true
        ]);
    }

    public function deactivateRetenueAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => false]);

        return redirect()->back()->with([
            'success' => 'Retenue désactivée avec succès pour l\'employé',
            'is_active' => false
        ]);
    }

    public function showRetenueAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenueEmployees = Retenue::where('retenues.libelle', $retenue->libelle)
                                ->join('employees', 'employees.id', '=', 'retenues.employee_id')
                                ->get();
        return view("paiesalaries::retenues.modals.show-retenue", compact("retenue", "retenueEmployees"));
    }

    /**
     * Calcul des fiches de paie.
     */
    public function calcule(Request $request)
    {
        $periode = null;
        if ($request->has("periode_id")) {
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        } else {
            $periode = $this->getActivePeriode();
        }

        // Retenues légales à jour avant l'aperçu des salaires
        if ($periode) {
            // Prime d'ancienneté (Art. 55 CCI) appliquée d'office, avant les retenues.
            app(\App\Services\SalaryService::class)->appliquerPrimeAncienneteperiode($periode);
            app(\App\Services\SalaryService::class)->appliquerRetenuesLegalesPeriode($periode);
        }

        $employees = Employee::active()
            ->where("company_id", Auth::user()->company_id)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        $periodes = PaiePeriode::with("exercice")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        return view(
            "paiesalaries::payslip.calcul",
            compact("periodes", "periode", "employees", "exercices"),
        );
    }

    public function previewBulletin($id, $exerciceId, $periodeId)
    {
        $employee = Employee::findOrFail($id);

        $allowances = Allowance::where('employee_id', $id)
                            ->where('periode_id', $periodeId)
                            ->orderBy('code','asc')
                            ->get();

        $retenuesEmp = Retenue::where('employee_id', $id)
                            ->where('periode_id', $periodeId)
                            ->orderBy('ordre','asc')
                            ->get();

        $company = Company::findOrFail(Auth::user()->company_id);
        $exercice = PaieExercice::findOrFail($exerciceId);
        $periode = PaiePeriode::findOrFail($periodeId);

        return view(
            "paiesalaries::payslip.modals.preview",
            compact("employee", "company", "exercice", "periode", 'retenuesEmp', 'allowances'),
        );
    }

    public function downloadPreviewBulletin($id, $exerciceId, $periodeId)
    {
        $employee = Employee::with("allowances")->findOrFail($id);
        $exercice = PaieExercice::findOrFail($exerciceId);
        $periode = PaiePeriode::findOrFail($periodeId);
        $retenuesEmp = Retenue::where('employee_id', $id)->where('periode_id', $periodeId)->orderBy('ordre','asc')->get();
        $mois = strtolower(\Carbon\Carbon::parse($periode->date_debut)->locale('fr')->translatedFormat('F'));
        $annee = \Carbon\Carbon::parse($periode->date_debut)->format('y');
        $filename = "bulletin_" . $mois . $annee . "_" . $employee->name . ".pdf";

        return response()->download(
            view(
                "paiesalaries::payslip.modals.preview",
                compact("employee", "exercice", "periode", 'retenuesEmp'),
            ),
            $filename
        );
    }

    public function remboursements(Request $request)
    {
        $periode = null;
        if ($request->has("periode_id")) {
            $periode = PaiePeriode::with("exercice")->findOrFail(
                $request->periode_id,
            );
        } else {
            $periode = $this->getActivePeriode();
        }

        $exercices = PaieExercice::with("periodes")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        $lastOrder = Employee::where("employees.is_active", 1)
            ->where("employees.company_id", Auth::user()->company_id)
            ->join("retenues", "employees.id", "=", "retenues.employee_id")
            ->orderBy("retenues.ordre", "desc")
            ->first();

        $retenues = Retenue::with(
            "typeRetenue",
            "employees",
            "periode",
            "company",
        )
            ->where("company_id", Auth::user()->company_id)
            ->where("type", "created")
            ->where("type_retenue_id", 5)
            ->where('periode_id', $periode->id)
            ->get();

        $typesRetenues = TypeRetenue::all();

        $employees = Employee::active()
            ->with("retenues")
            ->where("company_id", Auth::user()->company_id)
            ->where('start_date', '<=', $periode->date_fin)
            ->where(function ($query) use ($periode) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $periode->date_debut);
            })
            ->get();

        $periodes = PaiePeriode::with("exercice")
            ->where("company_id", Auth::user()->company_id)
            ->orderBy("date_debut", "desc")
            ->get();

        return view(
            "paiesalaries::retenues.remboursement",
            compact(
                "retenues",
                "typesRetenues",
                "periode",
                "employees",
                "periodes",
                "lastOrder",
                "exercices"
            ),
        );

    }

    // public function storeRemboursement(Request $request)
    // {
    //     \DB::beginTransaction();
    //     try {
    //         if ($request->employee_id) {
    //             $validator = \Validator::make($request->all(), [
    //                 "employee_id" => "required",
    //                 "periode_id" => "required",
    //             ]);

    //             if ($validator->fails()) {
    //                 \Log::error("Validation failed", [
    //                     "errors" => $validator->errors(),
    //                 ]);
    //                 return redirect()
    //                     ->back()
    //                     ->with(
    //                         "error",
    //                         "Vous devez sélectionner au moins un élément à enregistrer.",
    //                     );
    //             }

    //             $cpte = 14;
    //             $employee_id = $request->employee_id;
    //             $type = "default";
    //         } else {
    //             $validator = \Validator::make($request->all(), [
    //                 "periode_id" => "required",
    //             ]);

    //             if ($validator->fails()) {
    //                 \Log::error("Validation failed", [
    //                     "errors" => $validator->errors(),
    //                 ]);
    //                 return redirect()
    //                     ->back()
    //                     ->with(
    //                         "error",
    //                         "Vous devez sélectionner au moins une période enregistrée.",
    //                     );
    //             }

    //             $employee_id = null;
    //             $cpte = 1;
    //             $type = "created";
    //         }

    //         $periode_id = $request->periode_id;

    //         \Log::info("Starting retenues creation", [
    //             "employee_id" => $employee_id,
    //             "periode_id" => $periode_id,
    //             "item_brut" => $cpte,
    //         ]);

    //         $savedCount = 0;


    //         foreach ($request->all() as $key => $value) {
    //             if (strpos($key, "item_brut") === 0 && !empty($value)) {
    //                 // Extraire l'ID du champ (ex: item_brut4 -> 4)
    //                 $id = str_replace("item_brut", "", $key);

    //                 // Vérifier que l'amount existe et est supérieur à 0
    //                 $amount = $request->input("amount" . $id, 0);

    //                 \Log::info("Processing item", [
    //                     "id" => $id,
    //                     "item_brut" => $value,
    //                     "amount" => $amount,
    //                 ]);

    //                 $retenue = new Retenue();
    //                 $retenue->libelle = $request->input(
    //                     "libelle" . $id,
    //                     "",
    //                 );
    //                 if ($request->has("type_retenue_id" . $id)) {
    //                     $retenue->type_retenue_id = $request->input(
    //                         "type_retenue_id" . $id,
    //                         "",
    //                     );
    //                 } else {
    //                     $retenue->type_retenue_id = null;
    //                 }

    //                 $retenue->employee_id = $employee_id;
    //                 $retenue->periode_id = $periode_id;
    //                 $retenue->code = $request->input("code" . $id, "");
    //                 $retenue->ordre = $request->input("ordre" . $id, "");
    //                 $retenue->salariale = $request->input("salariale" . $id, "");
    //                 $retenue->base = $request->input("base" . $id, "");
    //                 $retenue->taux = $request->input("tauxRetenue" . $id, "");
    //                 $retenue->amount = $request->input("amount" . $id, "");
    //                 $retenue->jours_work = $request->input(
    //                     "jours_work" . $id,
    //                     "",
    //                 );
    //                 $retenue->date_application = now();
    //                 $retenue->is_active = 1;
    //                 $retenue->type = $type;
    //                 $retenue->month_paie = now()->format("Y-m");
    //                 $retenue->company_id = \Auth::user()->company_id;

    //                 if ($retenue->save()) {
    //                     $savedCount++;
    //                     \Log::info("Retenue saved successfully", [
    //                         "id" => $retenue->id,
    //                         "employee_id" => $employee_id,
    //                         "amount" => $amount,
    //                     ]);
    //                 }

    //             }
    //         }

    //         \DB::commit();

    //         if ($savedCount > 0) {
    //             \Log::info("Successfully saved allowances", [
    //                 "count" => $savedCount,
    //             ]);
    //             return redirect()
    //                 ->back()
    //                 ->with(
    //                     "success",
    //                     $savedCount . " remboursement ajouté avec succès.",
    //                 );
    //         } else {
    //             \Log::warning("No allowances were saved");
    //             return redirect()
    //                 ->back()
    //                 ->with(
    //                     "warning",
    //                     "Aucun remboursement valide à enregistrer. Vérifiez que les montants sont supérieurs à 0.",
    //                 );
    //         }
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         \Log::error("Error saving allowances: " . $e->getMessage(), [
    //             "trace" => $e->getTraceAsString(),
    //             "line" => $e->getLine(),
    //             "file" => $e->getFile(),
    //         ]);
    //         return redirect()
    //             ->back()
    //             ->with(
    //                 "error",
    //                 'Une erreur est survenue lors de l\'enregistrement: ' .
    //                     $e->getMessage(),
    //             );
    //     }
    // }

public function storeRemboursement(Request $request)
{
    \DB::beginTransaction();
    try {
        if ($request->employee_id) {
            $validator = \Validator::make($request->all(), [
                "employee_id" => "required",
                "periode_id" => "required",
            ]);

            if ($validator->fails()) {
                \Log::error("Validation failed", [
                    "errors" => $validator->errors(),
                ]);
                return redirect()
                    ->back()
                    ->with(
                        "error",
                        "Vous devez sélectionner au moins un élément à enregistrer.",
                    );
            }

            $cpte = 14;
            $employee_id = $request->employee_id;
            $type = "default";
        } else {
            $validator = \Validator::make($request->all(), [
                "periode_id" => "required",
            ]);

            if ($validator->fails()) {
                \Log::error("Validation failed", [
                    "errors" => $validator->errors(),
                ]);
                return redirect()
                    ->back()
                    ->with(
                        "error",
                        "Vous devez sélectionner au moins une période enregistrée.",
                    );
            }

            $employee_id = null;
            $cpte = 1;
            $type = "created";
        }

        $periode_id = $request->periode_id;

        \Log::info("Starting retenues creation", [
            "employee_id" => $employee_id,
            "periode_id" => $periode_id,
            "item_brut" => $cpte,
        ]);

        $savedCount = 0;

        // Obtenir le dernier ordre existant
        $lastOrder = Retenue::where("company_id", \Auth::user()->company_id)
            ->orderBy("ordre", "desc")
            ->first();
        $nextOrdre = $lastOrder ? $lastOrder->ordre + 1 : 1;

        foreach ($request->all() as $key => $value) {
            if (strpos($key, "item_brut") === 0 && !empty($value)) {
                $id = str_replace("item_brut", "", $key);
                $amount = $request->input("amount" . $id, 0);

                \Log::info("Processing item", [
                    "id" => $id,
                    "item_brut" => $value,
                    "amount" => $amount,
                ]);

                // Récupérer et valider l'ordre
                $ordre = $request->input("ordre" . $id);
                if (empty($ordre) || !is_numeric($ordre)) {
                    $ordre = $nextOrdre;
                    $nextOrdre++;
                }

                $retenue = new Retenue();
                $retenue->libelle = $request->input("libelle" . $id, "");

                if ($request->has("type_retenue_id" . $id)) {
                    $retenue->type_retenue_id = $request->input("type_retenue_id" . $id, "");
                } else {
                    $retenue->type_retenue_id = null;
                }

                $retenue->employee_id = $employee_id;
                $retenue->periode_id = $periode_id;
                $retenue->code = $request->input("code" . $id, "");
                $retenue->ordre = (int)$ordre; // Forcer la conversion en entier
                $retenue->salariale = $request->input("salariale" . $id, 0) ?: 0;
                $retenue->patronale = $request->input("patronale" . $id, 0) ?: 0;
                $retenue->base = $request->input("base" . $id, 0) ?: 0;
                $retenue->taux = $request->input("tauxRetenue" . $id, 0) ?: 0;
                $retenue->amount = $request->input("amount" . $id, 0) ?: 0;
                $retenue->jours_work = $request->input("jours_work" . $id, 30) ?: 30;
                $retenue->date_application = now();
                $retenue->is_active = 1;
                $retenue->type = $type;
                $retenue->month_paie = now()->format("Y-m");
                $retenue->company_id = \Auth::user()->company_id;

                if ($retenue->save()) {
                    $savedCount++;
                    \Log::info("Retenue saved successfully", [
                        "id" => $retenue->id,
                        "employee_id" => $employee_id,
                        "amount" => $amount,
                        "ordre" => $ordre,
                    ]);
                }
            }
        }

        \DB::commit();

        if ($savedCount > 0) {
            \Log::info("Successfully saved retenues", [
                "count" => $savedCount,
            ]);
            return redirect()
                ->back()
                ->with(
                    "success",
                    $savedCount . " remboursement(s) ajouté(s) avec succès.",
                );
        } else {
            \Log::warning("No retenues were saved");
            return redirect()
                ->back()
                ->with(
                    "warning",
                    "Aucun remboursement valide à enregistrer.",
                );
        }
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error("Error saving retenues: " . $e->getMessage(), [
            "trace" => $e->getTraceAsString(),
            "line" => $e->getLine(),
            "file" => $e->getFile(),
        ]);
        return redirect()
            ->back()
            ->with(
                "error",
                'Une erreur est survenue lors de l\'enregistrement: ' .
                    $e->getMessage(),
            );
    }
}
    public function editRemboursement($id)
    {
        $retenue = Retenue::findOrFail($id);
        $typesRetenues = TypeRetenue::all();
        $lastOrder = Employee::where("employees.is_active", 1)
            ->where("employees.company_id", Auth::user()->company_id)
            ->join("retenues", "employees.id", "=", "retenues.employee_id")
            ->orderBy("retenues.ordre", "desc")
            ->first();
        return view(
            "paiesalaries::retenues.partials.edit-retenue-form",
            compact("retenue", "typesRetenues", "lastOrder"),
        );
    }

    public function updateRemboursement(Request $request, $id)
    {
        $validated = $request->validate([
            "libelle" => "required|string|max:255",
            "type_retenue_id" => "required|exists:type_retenues,id",
            "amount" => "nullable|numeric|min:0",
            "taux" => "nullable|numeric|min:0|max:100",
            "ordre" => "required|integer",
            "salariale" => "nullable|integer|min:0",
        ]);

        $retenue = Retenue::findOrFail($id);
        $retenue->libelle = $validated["libelle"];
        $retenue->ordre = $validated["ordre"];
        $retenue->salariale = $validated["salariale"];
        $retenue->amount = $validated["amount"];
        $retenue->taux = $validated["taux"];
        $retenue->is_active = $request->has("statut");
        $retenue->type_retenue_id = $validated["type_retenue_id"];
        $retenue->save();

        return response()->json([
            "success" => true,
            "message" => "Remboursement mis à jour avec succès",
        ]);
    }

    public function destroyRemboursement($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->delete();

        return response()->json([
            "success" => true,
            "message" => "Remboursement supprimé avec succès",
        ]);
    }

    public function activateRemboursement($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => true]);

        return response()->json([
            'success' => 'Remboursement activé avec succès',
            'is_active' => true
        ]);
    }

    public function deactivateRemboursement($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => false]);

        return response()->json([
            'success' => 'Remboursement désactivé avec succès',
            'is_active' => false
        ]);
    }

    public function addEmployeeRemboursement($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenueCount = Retenue::where("company_id", Auth::user()->company_id)
            ->where("periode_id", $retenue->periode_id)
            ->where("libelle", $retenue->libelle)
            ->where("type", 'add')
            ->count();
        $employees = Employee::active()
            ->where("company_id", Auth::user()->company_id)
            ->get();
        return view("paiesalaries::retenues.partials.add-retenue", compact("retenue", "employees", "retenueCount"));
    }

    public function storeEmployeeRemboursement(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'employees' => 'required|array|min:1',
                'employees.*' => 'exists:employees,id',
                'periode_id' => 'required|exists:paie_periodes,id',
            ], [
                'employees.required' => 'Veuillez sélectionner au moins un employé.',
                'employees.*.exists' => 'Un ou plusieurs employés sélectionnés sont invalides.',
                'periode_id.required' => 'La période est requise.',
                'periode_id.exists' => 'La période sélectionnée est invalide.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $originalRetenue = Retenue::findOrFail($id);
            $savedCount = 0;
            $employeeIds = $request->input('employees', []);

            foreach ($employeeIds as $employeeId) {
                // Vérifier si le remboursement existe déjà pour cet employé et cette période
                $existingRetenue = Retenue::where('employee_id', $employeeId)
                    ->where('periode_id', $request->periode_id)
                    ->where('type_retenue_id', $originalRetenue->type_retenue_id)
                    ->first();

                if ($existingRetenue) {
                    continue; // Passe à l'employé suivant si un remboursement existe déjà
                }

                $newRetenue = new Retenue();
                $newRetenue->fill([
                    'libelle' => $originalRetenue->libelle,
                    'type_retenue_id' => $originalRetenue->type_retenue_id,
                    'employee_id' => $employeeId,
                    'periode_id' => $request->periode_id,
                    'code' => $originalRetenue->code,
                    'ordre' => $originalRetenue->ordre,
                    'salariale' => $originalRetenue->salariale,
                    'base' => $originalRetenue->base,
                    'taux' => $originalRetenue->taux,
                    'amount' => $originalRetenue->amount,
                    'jours_work' => $originalRetenue->jours_work,
                    'date_application' => now(),
                    'is_active' => $originalRetenue->is_active,
                    'type' => 'add',
                    'month_paie' => now()->format('Y-m'),
                    'company_id' => Auth::user()->company_id,
                ]);

                if ($newRetenue->save()) {
                    $savedCount++;
                    \Log::info('Remboursement ajouté avec succès', [
                        'employee_id' => $employeeId,
                        'retenue_id' => $newRetenue->id
                    ]);
                }
            }

            \DB::commit();

            if ($savedCount > 0) {
                return redirect()->back()->with([
                    'success' => 'Remboursement appliqué avec succès à ' . $savedCount . ' employé(s).',
                    'count' => $savedCount
                ]);
            } else {
                return redirect()->back()->with([
                    'success' => 'Aucun remboursement n\'a été ajouté. Les employés sélectionnés ont peut-être déjà ce remboursement pour cette période.'
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving allowances: " . $e->getMessage(), [
                "trace" => $e->getTraceAsString(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return redirect()
                ->back()
                ->with(
                    "error",
                    'Une erreur est survenue lors de l\'enregistrement: ' .
                        $e->getMessage(),
                );
        }
    }

    public function destroyRemboursementAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->delete();

        return redirect()->back()->with([
            "success" => "Remboursement supprimé avec succès pour l'employé",
        ]);
    }

    public function activateRemboursementAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => true]);

        return redirect()->back()->with([
            'success' => 'Remboursement activé avec succès pour l\'employé',
            'is_active' => true
        ]);
    }
    public function deactivateRemboursementAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenue->update(['is_active' => false]);

        return redirect()->back()->with([
            'success' => 'Remboursement désactivé avec succès pour l\'employé',
            'is_active' => false
        ]);
    }

    public function showRemboursementAdd($id)
    {
        $retenue = Retenue::findOrFail($id);
        $retenueEmployees = Retenue::where('retenues.libelle', $retenue->libelle)
                                ->join('employees', 'employees.id', '=', 'retenues.employee_id')
                                ->get();
        return view("paiesalaries::retenues.partials.show-retenue", compact("retenue", "retenueEmployees"));
    }

    public function downloadPayslip($employeeId)
    {
        $employee = Employee::with([
            "allowances",
            "loans",
            "deductions",
        ])->findOrFail($employeeId);

        // Calculer les totaux
        $totalAllowances = $employee->allowances->sum("amount");
        $totalDeductions = $employee->deductions->sum("amount");
        $totalLoans = $employee->loans->sum("amount");

        $data = [
            "employee" => $employee,
            "totalAllowances" => $totalAllowances,
            "totalDeductions" => $totalDeductions,
            "totalLoans" => $totalLoans,
        ];

        $pdf = PDF::loadView("paiesalaries::payslip.pdf", $data);

        $mois = strtolower(now()->locale('fr')->translatedFormat('F'));
        $annee = now()->format('y');

        return $pdf->download(
            "bulletin_" .
                $mois . $annee .
                "_" .
                $employee->name .
                ".pdf",
        );
    }

    public function genererBulletins(Request $request, $periodeId)
    {
        $periode = PaiePeriode::findOrFail($periodeId);
        $companyId = Auth::user()->company_id;
        $total_avtg = 0;
        $validator = \Validator::make(
            $request->all(),
            [
                'periode_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        try{
            $month = Carbon::parse($periode->date_debut)->format('Y-m');
            $year = Carbon::parse($periode->date_debut)->year;

            $employees = Employee::active()
                ->where('company_id', $companyId)
                ->where('start_date', '<=', $periode->date_fin)
                ->where(function ($query) use ($periode) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $periode->date_debut);
                })
                ->get();

            $avantages = Avantage::whereHas('periode', function($query) use ($periode) {
                    $query->where('id', $periode->id);
                })->where('is_active', 1)->sum('amount_reel');

            $total_avtg = $avantages;
            $user = User::where('company_id', $companyId)->first();

            if ($employees->count() > 0) {
                foreach ($employees as $employee) {
                    // Aucun bulletin sans ITS, CNPS et CMU : retenues légales appliquées juste avant le calcul
                    // Prime d'ancienneté posée avant les retenues : elle entre dans le brut.
                    app(\App\Services\SalaryService::class)->appliquerPrimeAnciennete($employee, $periode);
                    app(\App\Services\SalaryService::class)->appliquerRetenuesLegales($employee, $periode);

                    $payslipEmployee = PaySlip::firstOrNew([
                        'employee_id' => $employee->id,
                        'periode_id' => $periode->id,
                    ]);
                    $payslipEmployee->net_payble = $employee->get_net_salary($periode->id);
                    $payslipEmployee->salary_month = $month;
                    $payslipEmployee->status = 0;
                    $payslipEmployee->salary_brut = $employee->get_brut_salary($periode->id);
                    $payslipEmployee->net_imposable = $employee->get_salary_imposable($periode->id);
                    $payslipEmployee->net_sociale = $employee->get_salary_social($periode->id);
                    $payslipEmployee->basic_salary = (float)($employee->get_Salary_base($periode->id) ?: 0);
                    $payslipEmployee->total_retenue = $employee->get_retenue($periode->id);
                    $payslipEmployee->total_patronale = $employee->get_patronale($periode->id);
                    $payslipEmployee->allowances = Employee::allowance($employee->id, $periode->id);
                    $payslipEmployee->retenues = Employee::retenue($employee->id, $periode->id);
                    $payslipEmployee->avtg_real = !empty($total_avtg) ? $total_avtg : 0;
                    $payslipEmployee->avtg_real2 = $employee->get_avantage_reel2();
                    $payslipEmployee->avtg_bareme = $employee->get_avantage_bareme();
                    $payslipEmployee->pay_type = $employee->get_pay_type();
                    $payslipEmployee->nbre_jour = $employee->get_jours_work($periode->id);
                    $payslipEmployee->address_emp = $employee->get_Adress_Emp();
                    $payslipEmployee->situation_emp = $employee->get_Situation();
                    $payslipEmployee->enfant_emp = $employee->get_Enfants();
                    $payslipEmployee->num_cnps_emp = $employee->get_Num_Cnps();
                    $payslipEmployee->anciennete_emp = $employee->get_Anciennete();
                    $payslipEmployee->categories_emp = $employee->get_Categorie();
                    $payslipEmployee->emploi = $employee->get_Emploi();
                    $payslipEmployee->phone_emp = $employee->get_Telephone($periode->id);
                    $payslipEmployee->parts_emp = $employee->get_Nombre_parts($periode->id);
                    $payslipEmployee->nom_etp = $employee->get_Nom_Etp($periode->id);
                    $payslipEmployee->adresse_etp = $employee->get_Adresse_Etp($periode->id);
                    $payslipEmployee->phone_etp = $employee->get_Telephone_Etp();
                    $payslipEmployee->btp_etp = $employee->get_Boite_postale();
                    $payslipEmployee->company_id = $companyId;
                    $payslipEmployee->save();

                    // Traiter les terminaisons et les heures supplémentaires
                    $termination = Rupture::where(['employee_id' => $employee->id, 'periode_id' => $periode->id, 'status' => 'approved'])->first();
                    if ($termination) {
                        $termination->status = 'completed';
                        $termination->traiter = 1;
                        $termination->save();

                        $emp = Employee::find($termination->employee_id);
                        $emp->is_active = false;
                        $emp->statut_emp = 'Fin de contrat';
                        $emp->save();
                    }

                    $leave = \Modules\Leaves\Models\Leave::where(['employee_id' => $employee->id, 'status' => 'Approuvé'])->first();
                    if ($leave) {
                        $leave->leave_sit = 2;
                        $leave->save();
                    }
                }

                // Mettre à jour le statut de la période
                $PayslipMonth = PaiePeriode::where('id', $periode->id)->first();
                if ($PayslipMonth) {
                    $PayslipMonth->statut = 'validee';
                    $PayslipMonth->save();
                }
                // Mettre à jour l'exercice
                $payslipExercie = PaieExercice::find($periode->exercice_id);
                if ($payslipExercie) {
                    $payslipExercie->statut = 'en_cours';
                    $payslipExercie->save();
                }

                // Notification de succès
                if ($user->id) {
                    Notification::create([
                        'type' => 'App\Notifications\BulletinGenerated', // Dummy type or reuse existing
                        'title' => 'Génération terminée',
                        'message' => 'La génération des bulletins de paie est terminée.',
                        'icon' => 'fas fa-check-circle',
                        'color' => 'success',
                        'user_id' => $user->id,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Mise à jour du nombre de traitements restants
                $user->plan_cpte_trait -= 1;
                if($user->plan_cpte_trait > 0){
                    $user->save();
                    return redirect()->back()->with('success', __('La génération des bulletins a été lancée. Veuillez patienter...'));
                }else{
                    return redirect()->route('company.plan.pricing')->with('error', __('Vous avez atteint le nombre maximum de bulletins de paie pour cette période. Réabonnez vous pour pouvoir générer à nouveau.'));
                }
            } else {
                return redirect()->back()->with('error', __('Les bulletins de paie pour cette période ont déjà été générés.'));
            }
        } catch (\Exception $e) {
            \Log::error('GenerateBulletinsJob failed', [
                'periode_id' => $periodeId,
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function validerPaiement(Request $request, $periodeId)
    {
        \DB::beginTransaction();
        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date_paiement_effectif' => 'required|date',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $periode = PaiePeriode::findOrFail($periodeId);

            // Vérifier que la période a des bulletins générés
            if ($periode->bulletins()->count() === 0) {
                return redirect()->back()->with('error', __('Aucun bulletin généré pour cette période.'));
            }

            // Mettre à jour le statut de la période
            $periode->update([
                'statut' => 'payee',
                'date_paiement' => $request->date_paiement_effectif,
            ]);

            // Mettre à jour le statut des bulletins
            $periode->bulletins()->update([
                'status' => 1, // Marqué comme payé
            ]);

            // Envoyer les notifications si demandé
            if ($request->has('envoyer_notifications') && $request->envoyer_notifications) {
                // TODO: Implémenter l'envoi de notifications aux employés
                // Exemple: Envoyer un email ou SMS à chaque employé
            }

            \DB::commit();

            return redirect()->back()->with('success', __('La période a été marquée comme payée avec succès.'));
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', __('Une erreur est survenue : ' . $e->getMessage()));
        }
    }

    public function getPeriodeBulletins($periodeId)
    {
        $periode = PaiePeriode::findOrFail($periodeId);
        $bulletins = $periode->bulletins()->with('employee')->get();

        return response()->json([
            'periode' => $periode,
            'bulletins' => $bulletins
        ]);
    }

    /**
     * Dupliquer les éléments (retenues, allocations, avantages) de la période précédente
     */
    public function duplicateElements(Request $request, $periodeId)
    {
        try {
            Log::info('duplicateElements appelé', [
                'periodeId' => $periodeId,
                'elements' => $request->input('elements', []),
                'all_request_data' => $request->all()
            ]);

            $periode = PaiePeriode::findOrFail($periodeId);
            $companyId = Auth::user()->company_id;

            // Récupérer la période précédente
            $previousPeriode = PaiePeriode::where('exercice_id', $periode->exercice_id)
                ->where('id', '!=', $periode->id)
                ->orderBy('date_fin', 'desc')
                ->first();

            if (!$previousPeriode) {
                return redirect()->back()->with('warning', __('Aucune période précédente trouvée.'));
            }

            $elements = $request->input('elements', []);
            $duplicatedCount = 0;

            Log::info('Traitement des éléments', [
                'elements_count' => count($elements),
                'elements' => $elements
            ]);

            // Debug temporaire
            if (empty($elements)) {
                Log::warning('Aucun élément reçu dans la requête');
                // Afficher toutes les données de la requête pour debug
                Log::info('Toutes les données de la requête:', $request->all());
            }

            // Traiter les éléments individuels sélectionnés
            foreach ($elements as $element) {
                if (strpos($element, 'retenue_') === 0) {
                    $retenueId = str_replace('retenue_', '', $element);
                    $originalRetenue = Retenue::where('id', $retenueId)
                        ->where('periode_id', $previousPeriode->id)
                        ->where('company_id', $companyId)
                        ->first();

                    if ($originalRetenue) {
                        Retenue::create([
                            'periode_id' => $periode->id,
                            'employee_id' => $originalRetenue->employee_id,
                            'type_retenue_id' => $originalRetenue->type_retenue_id,
                            'code' => $originalRetenue->code,
                            'ordre' => $originalRetenue->ordre,
                            'salariale' => $originalRetenue->salariale,
                            'patronale' => $originalRetenue->patronale,
                            'base' => $originalRetenue->base,
                            'taux' => $originalRetenue->taux,
                            'libelle' => $originalRetenue->libelle,
                            'amount' => $originalRetenue->amount,
                            'jours_work' => $originalRetenue->jours_work,
                            'date_application' => now(),
                            'month_paie' => $periode->date_debut->format('Y-m'),
                            'type' => $originalRetenue->type,
                            'company_id' => $companyId,
                        ]);
                        $duplicatedCount++;
                    }

                } elseif (strpos($element, 'loan_') === 0) {
                    $loanId = str_replace('loan_', '', $element);
                    $originalLoan = Loan::where('id', $loanId)
                        ->where('periode_id', $previousPeriode->id)
                        ->where('company_id', $companyId)
                        ->first();

                    $lastOrder = Employee::where("employees.is_active", 1)
                        ->where("employees.company_id", Auth::user()->company_id)
                        ->join("retenues", "employees.id", "=", "retenues.employee_id")
                        ->orderBy("retenues.ordre", "desc")
                        ->first();

                    if($originalLoan){
                        LoanPayment::create([
                            'loan_id' => $originalLoan->id,
                            'periode_id' => $periode->id,
                            'amount' => $request->input('mt_retenue_' . $loanId),
                            'payment_date' => now(),
                            'note' => $originalLoan->reason,
                            'company_id' => $companyId,
                        ]);
                        $duplicatedCount++;
                    }

                    if ($originalLoan) {
                        Retenue::create([
                            'periode_id' => $periode->id,
                            'employee_id' => $originalLoan->employee_id,
                            'loan_id' => $originalLoan->id, // trace le prêt d'origine : sans lui, impossible de retirer la retenue quand le prêt est désactivé
                            'type_retenue_id' => 30,
                            'code' => 500,
                            'ordre' => $lastOrder ? $lastOrder->ordre + 1 : 1,
                            'patronale' => 0,
                            'salariale' => 1,
                            'base' => $originalLoan->amount,
                            'taux' => $originalLoan->nbre_mois,
                            'libelle' => $originalLoan->title,
                            'amount' => $request->input('mt_retenue_' . $loanId),
                            'date_application' => now(),
                            'month_paie' => $periode->date_debut->format('Y-m'),
                            'is_active' => true,
                            'type' => 'add',
                            'company_id' => $companyId,
                        ]);
                        $duplicatedCount++;
                    }

                } elseif (strpos($element, 'allowance_') === 0) {
                    $allowanceId = str_replace('allowance_', '', $element);
                    $originalAllowance = Allowance::where('id', $allowanceId)
                        ->where('periode_id', $previousPeriode->id)
                        ->where('company_id', $companyId)
                        ->first();

                    if ($originalAllowance) {
                        Allowance::create([
                            'periode_id' => $periode->id,
                            'employee_id' => $originalAllowance->employee_id,
                            'code' => $originalAllowance->code,
                            'code_compta' => $originalAllowance->code_compta,
                            'allowance_option_id' => $originalAllowance->allowance_option_id,
                            'title' => $originalAllowance->title,
                            'trait_fisc' => $originalAllowance->trait_fisc,
                            'trait_cnps' => $originalAllowance->trait_cnps,
                            'base_heures' => $originalAllowance->base_heures,
                            'amount' => $originalAllowance->amount,
                            'amount_imp' => $originalAllowance->amount_imp,
                            'montant' => $originalAllowance->montant,
                            'jours_work' => $originalAllowance->jours_work,
                            'jours_leave' => $originalAllowance->jours_leave,
                            'type' => $originalAllowance->type,
                            'type_amount' => $originalAllowance->type_amount,
                            'details' => $originalAllowance->details,
                            'month_paie' => $originalAllowance->month_paie,
                            'is_active' => $originalAllowance->is_active,
                            'company_id' => $companyId,
                            'created_by' => $originalAllowance->created_by,
                        ]);
                        $duplicatedCount++;
                    }
                } elseif (strpos($element, 'avantage_') === 0) {
                    $avantageId = str_replace('avantage_', '', $element);
                    $originalAvantage = \Modules\NatureAvantage\Models\Avantage::where('id', $avantageId)
                        ->where('periode_id', $previousPeriode->id)
                        ->where('company_id', $companyId)
                        ->first();

                    if ($originalAvantage) {
                        \Modules\NatureAvantage\Models\Avantage::create([
                            'periode_id' => $periode->id,
                            'employee_id' => $originalAvantage->employee_id,
                            'libelle' => $originalAvantage->libelle,
                            'amount' => $originalAvantage->amount,
                            'amount_reel' => $originalAvantage->amount_reel,
                            'type_avantage' => $originalAvantage->type_avantage,
                            'is_active' => $originalAvantage->is_active,
                            'company_id' => $companyId,
                        ]);
                        $duplicatedCount++;
                    }
                }
            }

            Log::info('Résultat du traitement', [
                'duplicatedCount' => $duplicatedCount,
                'elements_processed' => count($elements)
            ]);

            return redirect()->back()->with('success', __('✅ ' . $duplicatedCount . ' élément(s) ont été reporté(s) avec succès !'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la duplication des éléments: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Une erreur est survenue : ' . $e->getMessage()));
        }
    }

    public function loanPaiement(Request $request, $loanId)
    {
        try {
            $companyId = Auth::user()->company_id;

            // Récupérer le prêt
            $loan = Loan::where('id', $loanId)
                    ->where('company_id', $companyId)
                    ->first();

            if (!$loan) {
                return $this->loanPaiementError($request, 'Prêt non trouvé', 404);
            }

            // Récupérer la période depuis le formulaire
            $periodeId = $request->input('periode_id');
            $periode = PaiePeriode::where('id', $periodeId)
                        ->where('company_id', $companyId)
                        ->first();

            if (!$periode) {
                return $this->loanPaiementError($request, 'Période non trouvée', 404);
            }

            // Une paie déjà générée ou validée ne doit pas être modifiée.
            if (in_array($periode->statut, ['validee', 'payee', 'cloture', 'annulee'])) {
                return $this->loanPaiementError(
                    $request,
                    'La période « ' . ($periode->nom ?? $periode->id) . ' » est ' . $periode->statut
                        . ' : sa paie ne peut plus être modifiée.',
                    400
                );
            }

            $payslipExists = PaySlip::where('company_id', $companyId)
                ->where('employee_id', $loan->employee_id)
                ->where('periode_id', $periode->id)
                ->exists();

            if ($payslipExists) {
                return $this->loanPaiementError(
                    $request,
                    'Le bulletin de cet employé est déjà généré pour cette période : la paie émise ne peut plus être modifiée.',
                    400
                );
            }

            // Vérifier si un paiement existe déjà pour ce prêt et cette période
            $existingPayment = LoanPayment::where('loan_id', $loanId)
                                        ->where('periode_id', $periodeId)
                                        ->first();

            if ($existingPayment) {
                return $this->loanPaiementError($request, 'Une échéance est déjà appliquée pour ce prêt sur cette période.', 400);
            }

            // Obtenir le dernier ordre pour les retenues
            $lastOrder = Retenue::where('company_id', $companyId)
                              ->orderBy('ordre', 'desc')
                              ->first();

            $amount = $request->input('amount', $loan->amount_deduc);

            // Le paiement et la retenue sont indissociables : sans transaction, un échec sur la
            // retenue laisserait un LoanPayment orphelin, affiché comme "Appliquée" alors que
            // rien n'est retenu sur le bulletin.
            \DB::transaction(function () use ($loan, $periode, $amount, $lastOrder, $companyId, $request) {
                // Créer le paiement du prêt
                LoanPayment::create([
                    'loan_id' => $loan->id,
                    'periode_id' => $periode->id,
                    'amount' => $amount,
                    'payment_date' => now(),
                    'note' => $request->input('note', 'Paiement échéance'),
                    'company_id' => $companyId,
                ]);

                // Créer la retenue correspondante
                Retenue::create([
                    'periode_id' => $periode->id,
                    'employee_id' => $loan->employee_id,
                    'loan_id' => $loan->id,
                    'type_retenue_id' => 30, // Type pour prêt
                    'code' => 500, // Code pour prêt
                    'ordre' => $lastOrder ? $lastOrder->ordre + 1 : 1,
                    'patronale' => 0,
                    'salariale' => 1,
                    'base' => $loan->amount,
                    'taux' => $loan->nbre_mois,
                    'libelle' => 'Prêt : ' . ($loan->title ?? 'Non spécifié'),
                    'amount' => $amount,
                    'date_application' => now(),
                    'month_paie' => $periode->date_debut->format('Y-m'),
                    'is_active' => true,
                    'type' => 'add',
                    'company_id' => $companyId,
                ]);
            });

            $message = 'Échéance de ' . number_format($amount, 0, ',', ' ') . ' FCFA appliquée sur '
                . ($periode->nom ?? $periode->id) . ' pour ' . ($loan->employee->name ?? 'l\'employé') . '.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du paiement du prêt: ' . $e->getMessage());

            // Cas typique d'un déploiement où les migrations n'ont pas été jouées :
            // le message générique masquait la vraie cause.
            if (str_contains($e->getMessage(), "Unknown column") || str_contains($e->getMessage(), "1054")) {
                return $this->loanPaiementError(
                    $request,
                    "La base de données n'est pas à jour (colonne manquante). Exécutez « php artisan migrate » sur ce serveur.",
                    500
                );
            }

            return $this->loanPaiementError($request, 'Une erreur est survenue lors du traitement du paiement.', 500);
        }
    }

    /**
     * Retire l'échéance d'un prêt sur une période : le prêt n'est pas retenu ce mois-là.
     * Le prêt reste actif, seule la mensualité du mois est annulée — elle pourra être
     * réappliquée plus tard sur cette période ou reportée sur la suivante.
     */
    public function loanPaiementRetirer(Request $request, $loanId)
    {
        try {
            $companyId = Auth::user()->company_id;

            $loan = Loan::where('id', $loanId)->where('company_id', $companyId)->first();
            if (!$loan) {
                return $this->loanPaiementError($request, 'Prêt non trouvé', 404);
            }

            $periode = PaiePeriode::where('id', $request->input('periode_id'))
                ->where('company_id', $companyId)
                ->first();
            if (!$periode) {
                return $this->loanPaiementError($request, 'Période non trouvée', 404);
            }

            // Même règle que pour l'application : une paie émise ne se modifie pas.
            if (in_array($periode->statut, ['validee', 'payee', 'cloture', 'annulee'])) {
                return $this->loanPaiementError(
                    $request,
                    'La période « ' . ($periode->nom ?? $periode->id) . ' » est ' . $periode->statut
                        . ' : sa paie ne peut plus être modifiée.',
                    400
                );
            }

            $payslipExists = PaySlip::where('company_id', $companyId)
                ->where('employee_id', $loan->employee_id)
                ->where('periode_id', $periode->id)
                ->exists();

            if ($payslipExists) {
                return $this->loanPaiementError(
                    $request,
                    'Le bulletin de cet employé est déjà généré pour cette période : la paie émise ne peut plus être modifiée.',
                    400
                );
            }

            $payment = LoanPayment::where('loan_id', $loan->id)
                ->where('periode_id', $periode->id)
                ->first();

            if (!$payment) {
                return $this->loanPaiementError($request, "Aucune échéance appliquée pour ce prêt sur cette période.", 400);
            }

            \DB::beginTransaction();
            try {
                // loan_id cible le bon prêt même si l'employé en a plusieurs sur la période.
                // Les retenues antérieures à la colonne loan_id retombent sur le libellé.
                Retenue::where('company_id', $companyId)
                    ->where('periode_id', $periode->id)
                    ->where('employee_id', $loan->employee_id)
                    ->where('code', 500)
                    ->where(function ($query) use ($loan) {
                        $query->where('loan_id', $loan->id)
                              ->orWhere(function ($sub) use ($loan) {
                                  $sub->whereNull('loan_id')
                                      ->whereIn('libelle', [$loan->title, 'Prêt : ' . $loan->title]);
                              });
                    })
                    ->delete();

                $payment->delete();

                // Un prêt marqué terminé par cette échéance redevient en cours.
                if ($loan->statut === 'completed') {
                    $loan->statut = 'running';
                    $loan->save();
                }

                \DB::commit();
            } catch (\Throwable $e) {
                \DB::rollBack();
                throw $e;
            }

            $message = 'Échéance retirée : le prêt « ' . $loan->title . ' » ne sera pas retenu sur '
                . ($periode->nom ?? $periode->id) . '.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du retrait de l\'échéance de prêt: ' . $e->getMessage());
            return $this->loanPaiementError($request, "Une erreur est survenue lors du retrait de l'échéance.", 500);
        }
    }

    /**
     * Réponse d'erreur de loanPaiement : JSON pour les appels AJAX, redirection pour un formulaire.
     */
    private function loanPaiementError(Request $request, $message, $status = 400)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Définit l'exercice actif en session
     */
    public function setActiveExercice(Request $request)
    {
        $validated = $request->validate([
            'exercice_id' => 'required|exists:paie_exercices,id'
        ]);

        $exercice = PaieExercice::where('id', $validated['exercice_id'])
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (!$exercice) {
            return response()->json([
                'success' => false,
                'message' => 'Exercice non trouvé'
            ], 404);
        }

        session(['active_exercice_id' => $exercice->id]);

        // Réinitialiser la période active car elle doit appartenir au nouvel exercice
        session()->forget('active_periode_id');

        return response()->json([
            'success' => true,
            'message' => 'Exercice sélectionné avec succès',
            'exercice' => [
                'id' => $exercice->id,
                'nom' => $exercice->nom,
                'code' => $exercice->code
            ]
        ]);
    }

    /**
     * Définit la période active en session
     */
    public function setActivePeriode(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:paie_periodes,id'
        ]);

        $periode = PaiePeriode::where('id', $validated['periode_id'])
            ->where('company_id', Auth::user()->company_id)
            ->with('exercice')
            ->first();

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Période non trouvée'
            ], 404);
        }

        session(['active_periode_id' => $periode->id]);
        session(['active_exercice_id' => $periode->exercice_id]);

        return response()->json([
            'success' => true,
            'message' => 'Période sélectionnée avec succès',
            'periode' => [
                'id' => $periode->id,
                'nom' => $periode->nom,
                'code' => $periode->code
            ],
            'exercice' => [
                'id' => $periode->exercice->id,
                'nom' => $periode->exercice->nom,
                'code' => $periode->exercice->code
            ]
        ]);
    }

    /**
     * Récupère l'exercice actif depuis la session
     */
    public function getActiveExercice()
    {
        $companyId = Auth::user()->company_id;
        $exerciceId = session('active_exercice_id');

        if ($exerciceId) {
            $exercice = PaieExercice::where('id', $exerciceId)
                ->where('company_id', $companyId)
                ->first();

            if ($exercice) {
                return $exercice;
            }
        }

        // Si aucun exercice en session, sélectionner l'exercice en cours
        $exercice = PaieExercice::where('company_id', $companyId)
            ->where('statut', 'en_cours')
            ->orderBy('date_debut', 'desc')
            ->first();

        if ($exercice) {
            session(['active_exercice_id' => $exercice->id]);
            return $exercice;
        }

        // Sinon, prendre le dernier exercice créé
        $exercice = PaieExercice::where('company_id', $companyId)
            ->orderBy('date_debut', 'desc')
            ->first();

        if ($exercice) {
            session(['active_exercice_id' => $exercice->id]);
        }

        return $exercice;
    }

    /**
     * Récupère la période active depuis la session
     */
    public function getActivePeriode()
    {
        $companyId = Auth::user()->company_id;
        $periodeId = session('active_periode_id');

        if ($periodeId) {
            $periode = PaiePeriode::where('id', $periodeId)
                ->where('company_id', $companyId)
                ->with('exercice')
                ->first();

            if ($periode) {
                return $periode;
            }
        }

        // Si aucune période en session, prendre la dernière période de l'exercice actif
        $exercice = $this->getActiveExercice();

        if ($exercice) {
            $periode = PaiePeriode::where('exercice_id', $exercice->id)
                ->where('company_id', $companyId)
                ->orderBy('date_debut', 'desc')
                ->first();

            if ($periode) {
                session(['active_periode_id' => $periode->id]);
                return $periode;
            }
        }

        return null;
    }

    /**
     * Retourne la liste des exercices pour le dropdown (API)
     */
    public function getExercicesForDropdown()
    {
        $companyId = Auth::user()->company_id;

        $exercices = PaieExercice::where('company_id', $companyId)
            ->orderBy('date_debut', 'desc')
            ->get(['id', 'nom', 'code', 'statut', 'date_debut', 'date_fin']);

        $activeExercice = $this->getActiveExercice();

        return response()->json([
            'success' => true,
            'exercices' => $exercices,
            'active_exercice_id' => $activeExercice ? $activeExercice->id : null
        ]);
    }

    /**
     * Retourne les périodes d'un exercice pour le dropdown (API)
     */
    public function getPeriodesForDropdown($exerciceId)
    {
        $companyId = Auth::user()->company_id;

        $periodes = PaiePeriode::where('exercice_id', $exerciceId)
            ->where('company_id', $companyId)
            ->orderBy('date_debut', 'desc')
            ->get(['id', 'nom', 'code', 'statut', 'date_debut', 'date_fin', 'date_paiement']);

        $activePeriode = $this->getActivePeriode();

        return response()->json([
            'success' => true,
            'periodes' => $periodes,
            'active_periode_id' => $activePeriode ? $activePeriode->id : null
        ]);
    }
}
