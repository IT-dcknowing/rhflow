<?php

namespace Tests\Feature;

use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\PaiePeriode;
use App\Models\User;
use App\Services\SalaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use Tests\TestCase;

/**
 * Non-régression sur le cœur du calcul de paie : proratisation des jours,
 * prime d'ancienneté, et points d'entrée d'écriture de la paie du mois.
 *
 * Ces tests tournent sur la base de développement MySQL et non sur le SQLite
 * de phpunit.xml : le schéma du projet et son SQL (FIELD(), enums) ne passent
 * pas sur SQLite. Chaque test est enveloppé dans une transaction annulée à la
 * fin, donc rien n'est écrit durablement.
 */
class PaieProratisationTest extends TestCase
{
    // Transaction ouverte à la main plutôt que par DatabaseTransactions : le trait
    // se déclenche avant setUp() et tentait de l'ouvrir sur la connexion MySQL
    // même quand la suite tourne sur SQLite, ce qui la faisait échouer au lieu de
    // s'ignorer.
    private bool $transactionOuverte = false;

    private ?Employee $salarie = null;
    private ?PaiePeriode $periode = null;

    protected function setUp(): void
    {
        parent::setUp();

        // La suite par défaut tourne sur un SQLite en mémoire, où le schéma du
        // projet n'existe pas. Ces tests s'ignorent alors d'eux-mêmes plutôt que
        // de teinter la suite en rouge ; ils se lancent avec :
        //     php artisan test -c phpunit.paie.xml
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped(
                "Test de paie : nécessite la base MySQL du projet (php artisan test -c phpunit.paie.xml)."
            );
        }

        try {
            DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped("Base MySQL injoignable : " . $e->getMessage());
        }

        DB::connection('mysql')->beginTransaction();
        $this->transactionOuverte = true;

        // Une période ouverte et un salarié réels : on teste le calcul tel qu'il
        // tourne en production, pas un modèle reconstruit pour l'occasion.
        $this->periode = PaiePeriode::where('statut', 'brouillon')
            ->whereDoesntHave('bulletins')
            ->orderByDesc('id')
            ->first();

        if (!$this->periode) {
            $this->markTestSkipped("Aucune période ouverte en base pour servir de support.");
        }

        $this->salarie = Employee::where('company_id', $this->periode->company_id)
            ->where('is_active', 1)
            ->whereNotNull('start_date')
            ->where('salary', '>', 0)
            ->first();

        if (!$this->salarie) {
            $this->markTestSkipped("Aucun salarié actif rattaché à cette période.");
        }

        Auth::login(User::where('company_id', $this->periode->company_id)
            ->where('type', 'company')->first()
            ?? User::find($this->salarie->company->user_id));
    }

    protected function tearDown(): void
    {
        // Rien de ce que font ces tests ne doit survivre.
        if ($this->transactionOuverte) {
            DB::connection('mysql')->rollBack();
            $this->transactionOuverte = false;
        }

        parent::tearDown();
    }

    /** Fixe les jours travaillés du salarié pour la période. */
    private function poserJours(int $jours): void
    {
        $this->salarie->update(['tax_payer_id' => $jours]);
        Allowance::where('employee_id', $this->salarie->id)
            ->where('periode_id', $this->periode->id)
            ->update(['jours_work' => $jours]);
        $this->salarie->refresh();
    }

    public function test_les_jours_travailles_ne_sont_plus_ramenes_a_30_a_29_jours(): void
    {
        $this->poserJours(29);

        $this->assertSame(
            29,
            (int) $this->salarie->get_jours_work($this->periode->id),
            "29 jours travaillés doivent rester 29 : c'est une absence d'un jour, pas un mois complet."
        );
    }

    public function test_un_mois_de_28_ou_31_jours_calendaires_vaut_un_mois_complet(): void
    {
        $this->poserJours(28);
        $this->assertSame(30, (int) $this->salarie->get_jours_work($this->periode->id),
            "28 correspond à février : mois complet.");

        $this->poserJours(31);
        $this->assertSame(30, (int) $this->salarie->get_jours_work($this->periode->id),
            "31 correspond à un mois long : la paie reste assise sur 30 jours.");
    }

    public function test_les_jours_sont_bornes_a_30(): void
    {
        $this->poserJours(45);

        $this->assertLessThanOrEqual(
            30,
            (int) $this->salarie->get_jours_work($this->periode->id),
            "Aucune saisie ne doit produire plus de 30 jours de paie."
        );
    }

    public function test_le_salaire_de_base_est_proratise_a_29_jours(): void
    {
        $plein = (float) $this->salarie->salary;

        $this->poserJours(30);
        $baseMoisComplet = round($plein);

        $this->poserJours(29);
        $jours = (int) $this->salarie->get_jours_work($this->periode->id);
        $baseProratisee = round(($plein / 30) * $jours);

        $this->assertSame(29, $jours);
        $this->assertLessThan(
            $baseMoisComplet,
            $baseProratisee,
            "À 29 jours, la base doit être inférieure au mois complet."
        );
        $this->assertSame(round($plein * 29 / 30), $baseProratisee);
    }

    public function test_la_prime_danciennete_souvre_a_deux_ans_et_pas_avant(): void
    {
        $service = app(SalaryService::class);
        $reference = $this->periode->date_fin instanceof \Carbon\Carbon
            ? $this->periode->date_fin->copy()
            : \Carbon\Carbon::parse($this->periode->date_fin);

        $cas = [
            23 => 0.0,      // sous deux ans : aucun droit
            24 => 2.0,      // deux ans révolus : 2 %
            35 => 2.0,
            36 => 3.0,      // troisième année : 2 % + 1 %
            60 => 5.0,
            600 => 25.0,    // plafond légal
        ];

        foreach ($cas as $mois => $tauxAttendu) {
            $this->salarie->company_doj = $reference->copy()->subMonths($mois)->toDateString();
            $this->salarie->save();
            $this->salarie->refresh();

            $montant = $service->calculPrimeAnciennete($this->salarie, $this->periode);
            $tauxObtenu = round($montant / (float) $this->salarie->salary * 100, 2);

            $this->assertEqualsWithDelta(
                $tauxAttendu,
                $tauxObtenu,
                0.05,
                "À {$mois} mois d'ancienneté, le taux attendu est {$tauxAttendu} %."
            );
        }
    }

    public function test_la_prime_danciennete_est_posee_et_proratisee(): void
    {
        $service = app(SalaryService::class);
        $reference = $this->periode->date_fin instanceof \Carbon\Carbon
            ? $this->periode->date_fin->copy()
            : \Carbon\Carbon::parse($this->periode->date_fin);

        $this->salarie->company_doj = $reference->copy()->subMonths(40)->toDateString();
        $this->salarie->save();

        $this->poserJours(30);
        $this->assertTrue($service->appliquerPrimeAnciennete($this->salarie->refresh(), $this->periode));

        $plein = Allowance::where('employee_id', $this->salarie->id)
            ->where('periode_id', $this->periode->id)->where('code', '104')->first();

        $this->assertNotNull($plein, "La prime d'ancienneté doit être posée.");
        $this->assertEquals($plein->montant, $plein->amount, "Mois complet : pas de prorata.");

        $this->poserJours(15);
        $service->appliquerPrimeAnciennete($this->salarie->refresh(), $this->periode);

        $demi = Allowance::where('employee_id', $this->salarie->id)
            ->where('periode_id', $this->periode->id)->where('code', '104')->first();

        $this->assertEquals(
            round($demi->montant / 2),
            round($demi->amount),
            "À 15 jours, la prime doit valoir la moitié du droit plein."
        );
    }

    public function test_la_rubrique_danciennete_est_creee_si_elle_manque(): void
    {
        $service = app(SalaryService::class);
        $entreprise = $this->salarie->company_id;

        AllowanceOption::where('company_id', $entreprise)->where('code', '104')->delete();
        $this->assertSame(0, AllowanceOption::where('company_id', $entreprise)->where('code', '104')->count());

        $reference = $this->periode->date_fin instanceof \Carbon\Carbon
            ? $this->periode->date_fin->copy()
            : \Carbon\Carbon::parse($this->periode->date_fin);
        $this->salarie->company_doj = $reference->copy()->subMonths(40)->toDateString();
        $this->salarie->save();

        $service->appliquerPrimeAnciennete($this->salarie->refresh(), $this->periode);

        $option = AllowanceOption::where('company_id', $entreprise)->where('code', '104')->first();
        $this->assertNotNull($option, "La rubrique 104 doit être créée quand elle manque.");
        $this->assertSame('exo 0%', $option->param_fiscal);
        $this->assertSame('Soumis', $option->param_social);
    }

    public function test_le_traitement_en_masse_refuse_plus_de_30_jours(): void
    {
        $reponse = $this->postJson(
            "/company/paiesalaries/periodes/{$this->periode->id}/traitement-masse",
            ['action' => 'jours', 'valeur' => 45, 'employee_ids' => [$this->salarie->id]]
        );

        $reponse->assertStatus(422);
        $this->assertFalse($reponse->json('success'));
    }

    public function test_le_traitement_en_masse_ignore_les_salaries_dune_autre_entreprise(): void
    {
        $etranger = Employee::where('company_id', '!=', $this->salarie->company_id)
            ->where('is_active', 1)->first();

        if (!$etranger) {
            $this->markTestSkipped("Pas de salarié d'une autre entreprise pour ce contrôle.");
        }

        $reponse = $this->postJson(
            "/company/paiesalaries/periodes/{$this->periode->id}/traitement-masse",
            ['action' => 'base', 'valeur' => 1, 'employee_ids' => [$etranger->id]]
        );

        $reponse->assertStatus(422);
        $this->assertSame((float) $etranger->salary, (float) $etranger->refresh()->salary,
            "Le salaire d'une autre entreprise ne doit pas bouger.");
    }

    public function test_le_traitement_en_masse_applique_les_jours_a_toute_la_selection(): void
    {
        $lot = Employee::where('company_id', $this->periode->company_id)
            ->where('is_active', 1)->limit(2)->pluck('id')->all();

        $reponse = $this->postJson(
            "/company/paiesalaries/periodes/{$this->periode->id}/traitement-masse",
            ['action' => 'jours', 'valeur' => 20, 'employee_ids' => $lot]
        );

        $reponse->assertStatus(200);
        $this->assertTrue($reponse->json('success'));

        foreach ($lot as $id) {
            $this->assertSame(20, (int) Employee::find($id)->get_jours_work($this->periode->id),
                "Chaque salarié de la sélection doit porter 20 jours.");
        }
    }

    public function test_une_periode_verrouillee_refuse_toute_ecriture(): void
    {
        $verrouillee = PaiePeriode::whereHas('bulletins')->orderByDesc('id')->first();

        if (!$verrouillee) {
            $this->markTestSkipped("Aucune période avec bulletins générés en base.");
        }

        Auth::login(User::where('company_id', $verrouillee->company_id)
            ->where('type', 'company')->first());

        $reponse = $this->postJson(
            "/company/paiesalaries/periodes/{$verrouillee->id}/traitement-masse",
            ['action' => 'base', 'valeur' => 1, 'employee_ids' => [$this->salarie->id]]
        );

        $reponse->assertStatus(422);
        $this->assertStringContainsString('verrouill', mb_strtolower($reponse->json('message')));
    }

    public function test_la_recherche_de_salaries_est_cloisonnee_par_entreprise(): void
    {
        $reponse = $this->getJson('/company/employees/recherche?q=a');
        $reponse->assertStatus(200);

        $ids = collect($reponse->json('results'))->pluck('id');
        $horsPerimetre = Employee::whereIn('id', $ids)
            ->where('company_id', '!=', $this->periode->company_id)->count();

        $this->assertSame(0, $horsPerimetre,
            "La recherche ne doit jamais renvoyer un salarié d'une autre entreprise.");
        $this->assertLessThanOrEqual(20, $ids->count(), "Le nombre de résultats est plafonné à 20.");
    }

    public function test_enregistrer_un_salarie_ecrit_jours_base_et_primes(): void
    {
        $prime = Allowance::where('employee_id', $this->salarie->id)
            ->where('periode_id', $this->periode->id)
            ->where('code', '!=', '104')
            ->first();

        $charge = ['jours' => 15, 'base' => 120000, 'elements' => []];
        if ($prime) {
            $charge['elements'][] = ['id' => $prime->id, 'montant' => 50000];
        }

        $reponse = $this->postJson(
            "/company/paiesalaries/periodes/{$this->periode->id}/salarie/{$this->salarie->id}",
            $charge
        );

        $reponse->assertStatus(200);
        $this->assertTrue($reponse->json('success'));

        $this->salarie->refresh();
        $this->assertSame(120000.0, (float) $this->salarie->salary);
        $this->assertSame(15, (int) $this->salarie->get_jours_work($this->periode->id));

        if ($prime) {
            $prime->refresh();
            $this->assertSame(50000.0, (float) $prime->montant, "Le montant plein est celui saisi.");
            $this->assertSame(25000.0, (float) $prime->amount, "À 15 jours, la moitié est retenue.");
        }
    }

    public function test_aucune_ecriture_ne_survit_a_la_transaction(): void
    {
        // Garde-fou du dispositif lui-même : si DatabaseTransactions cessait de
        // couvrir la connexion mysql, les tests ci-dessus pollueraient la base.
        $this->assertTrue($this->transactionOuverte);
        $this->assertGreaterThan(0, DB::connection('mysql')->transactionLevel(),
            "Les tests doivent tourner à l'intérieur d'une transaction.");
    }
}
