<?php

namespace Modules\Employees\Models;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\SalaryService;
use Modules\PaieSalaries\Models\PaySlip;
use PhpParser\Node\Stmt\Return_;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'nationality',
        'phone',
        'martalstatu_id',
        'enfant',
        'personneinf',
        'parts',
        'cmu',
        'contrat',
        'charge_expat',
        'num_cnps',
        'num_secu_soc',
        'start_date',
        'end_date',
        'categorie',
        'end_leave',
        'address',
        'email',
        'device_token',
        'platform',
        'last_login_at',
        'password',
        'employee_id',
        'branch_id',
        'department_id',
        'designation_id',
        'company_doj',
        'documents',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'orange_money',
        'mtn_money',
        'moov_money',
        'wave_money',
        'tax_payer_id',
        'salary_type',
        'sous_categorie',
        'salary_horaire',
        'salary',
        'paytype',
        'id_secteur',
        'charge_its',
        'charge_cnps',
        'charge_cmu',
        'is_active',
        'secteur_id',
        'statut_emp',
        'company_id'
    ];

    protected $casts = [
        'dob' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'end_leave' => 'date',
        'last_login_at' => 'datetime',
        'salary_horaire' => 'decimal:2',
        'salary' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relations
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function situation()
    {
        return $this->belongsTo(\App\Models\MaritalStatus::class, 'martalstatu_id');
    }

    public function categorieEmp()
    {
        return $this->belongsTo(\App\Models\JobCategorie::class, 'categorie');
    }

    public function paytypeEmp()
    {
        return $this->belongsTo(\App\Models\PaymentType::class, 'paytype');
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function contracts()
    {
        return $this->hasMany(\Modules\Contracts\Models\Contract::class, 'employee_id', 'id');
    }

    public function absences()
    {
        return $this->hasMany(\Modules\Time\Models\TimeSheet::class, 'employee_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'nationality');
    }

    public function overtimes()
    {
        return $this->hasMany(\Modules\Time\Models\Overtime::class, 'employee_id', 'id');
    }

    public function leaves()
    {
        return $this->hasMany(\Modules\Leaves\Models\Leave::class, 'employee_id', 'id');
    }

    public function loans()
    {
        return $this->hasMany(\Modules\Loans\Models\Loan::class, 'employee_id', 'id');
    }

    public function retenues()
    {
        return $this->hasMany(\Modules\PaieSalaries\Models\Retenue::class, 'employee_id', 'id');
    }

    public function ruptures()
    {
        return $this->hasMany(\Modules\Ruptures\Models\Rupture::class, 'employee_id', 'id');
    }

    public function avantages()
    {
        return $this->hasMany(\Modules\NatureAvantage\Models\Avantage::class, 'employee_id', 'id');
    }

    /**
     * Récupère les allocations de l'employé
     */
    public function allowances()
    {
        return $this->hasMany(\App\Models\Allowance::class, 'employee_id', 'id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Calcule le salaire brut de l'employé
     */
    public function get_brut_salary($periode_id = null)
    {
        $nbre_jours = intval($this->get_jours_work($periode_id));
        $base_salary = 0;

        if ($nbre_jours == 30) {
            $base_salary = $this->salary;
        } elseif ($this->statut_emp == 'Stagiaire' || $this->statut_emp == 'Apprenti' || $this->statut_emp == 'Prestataire') {
            $base_salary = 0;
        } else {
            // Toujours utiliser le salaire de base mensuel pour éviter la double-proratisation
            $base_salary = $this->salary;
            $base_salary = ($base_salary / 30) * $nbre_jours;
        }

        //allowances
        $allowancesQuery = \App\Models\Allowance::where('employee_id', '=', $this->id);
        if ($periode_id) {
            $allowancesQuery->where('periode_id', '=', $periode_id);
        }
        $allowances = $allowancesQuery->get();
        $total_allowance = 0;

        foreach ($allowances as $allowance) {
            $base_amount = (float) ($allowance->montant ?: $allowance->amount);
            $current_amount = ($nbre_jours == 30) ? $base_amount : (($base_amount / 30) * $nbre_jours);
            $total_allowance = $total_allowance + $current_amount;
        }

        //avatnage en nature
        $avantagesQuery = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id)->where('is_active', 1);
        if ($periode_id) {
            $avantagesQuery->where('periode_id', '=', $periode_id);
        }
        $avantages = $avantagesQuery->get();
        $total_avantages_real = 0;
        foreach ($avantages as $avantage) {
            $total_avantages_real = $total_avantages_real + $avantage->amount_reel;
        }

        $salary_brut_total = ((float) $base_salary + (float) $total_allowance + (float) $total_avantages_real);

        return $salary_brut_total;
    }

    /**
     * Calcule le salaire brut imposable de l'employé
     */

    public function get_salary_imposable($periode_id = null)
    {
        // -------------------------------------------------------
        // Plafond transport localisé — Art. 116 al.1 CGI
        // Abidjan: 30 000 | Bouaké: 24 000 | Autres: 20 000
        // -------------------------------------------------------
        $transport_cap = 30000; // défaut = Abidjan
        try {
            // Vérifier la ville de la succursale, puis de la compagnie
            $branch_city = strtolower(trim($this->branch->city ?? $this->company->city ?? $this->branch->name ?? ''));
            if (str_contains($branch_city, 'bouak')) {
                $transport_cap = 24000;
            } elseif (!empty($branch_city) && !str_contains($branch_city, 'abidjan') && !str_contains($branch_city, 'siège')) {
                $transport_cap = 20000;
            }
        } catch (\Throwable $e) {
            $transport_cap = 30000; // fallback sécurisé
        }

        $allowancesQuery = \App\Models\Allowance::where('employee_id', '=', $this->id);
        if ($periode_id) {
            $allowancesQuery->where('periode_id', '=', $periode_id);
        }
        $allowances = $allowancesQuery->get();
        $total_allowance = 0;
        $total_exo = 0;
        $totalbase = 0;
        $mont_cant = 0;
        $total_assurance_imposable = 0; // Part imposable des assurances dépassant la double limite
        $nbre_jours = intval($this->get_jours_work($periode_id));

        foreach ($allowances as $allowance) {
            // On utilise le 'montant' (base 100%) avec un repli automatique sur 'amount' s'il est vide
            $base_amount = (float) ($allowance->montant ?: $allowance->amount);
            $current_amount = (intval($nbre_jours) >= 30) ? $base_amount : (($base_amount / 30) * $nbre_jours);
            $title = trim($allowance->title ?? '');

            if (strpos($allowance->trait_fisc, 'exo 100%') === 0) {
                // Transport : plafond localisé (Abidjan / Bouaké / Autres)
                if ($allowance->code == '111' || stripos($title, 'transport') !== false) {
                    $total_exo += min($current_amount, $transport_cap);
                    // La partie > plafond reste imposable (elle sera dans total_allowance)
                } else {
                    $total_exo = $total_exo + $current_amount;
                }
            }

            if (strpos($allowance->trait_fisc, 'exo 10%') === 0) {
                if (stripos($title, 'Frais de restauration') !== false || stripos($title, 'Cantine') !== false) {
                    if ($current_amount > 30000) {
                        $mont_cant = $current_amount - 30000;
                    } else {
                        $mont_cant = 0;
                    }
                    $total_allowance = $total_allowance + $mont_cant;
                } else {
                    $total_allowance = $total_allowance + $current_amount;
                }
            }

            if ($allowance->code == '126' || $allowance->code == '111') {
                $totalbase += $current_amount;
            }
        }

        //avatnage en nature
        $avantagesQuery = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id)->where('is_active', 1);
        if ($periode_id) {
            $avantagesQuery->where('periode_id', '=', $periode_id);
        }
        $avantages = $avantagesQuery->get();
        $total_avantages_real = 0;
        $total_avantages_bare = 0;
        $total_avantages_real1 = 0;
        $total_avantages_bare1 = 0;
        $total_avantages_real2 = 0;
        $total_avantages_bare2 = 0;
        foreach ($avantages as $avantage) {
            $total_avantages_bare = $total_avantages_bare + $avantage->amount;
            $total_avantages_real = $total_avantages_real + $avantage->amount_reel;
            if ($avantage->type_avantage == 'avantage_en_nature') {
                $total_avantages_bare1 = $total_avantages_bare1 + $avantage->amount;
                $total_avantages_real1 = $total_avantages_real1 + $avantage->amount_reel;
            } else {
                $total_avantages_bare2 = $total_avantages_bare2 + $avantage->amount;
                $total_avantages_real2 = $total_avantages_real2 + $avantage->amount_reel;
            }
        }
        if ($total_avantages_real > 0) {
            $salary_brut_total = ($this->get_brut_salary($periode_id) - $total_avantages_real);
        } else {
            $salary_brut_total = $this->get_brut_salary($periode_id);
        }

        $exo_ten = (($salary_brut_total - $totalbase) * 10) / 100;

        if ($total_allowance > $exo_ten) {
            $net_salary_imposable = round($salary_brut_total - ($total_exo + $exo_ten)) + $total_avantages_bare1 + $total_avantages_real2;
        } else {
            $net_salary_imposable = round($salary_brut_total - ($total_exo + $total_allowance)) + $total_avantages_bare1 + $total_avantages_real2;
        }

        return round($net_salary_imposable);
    }

    /**
     * Calcule le salaire brut social de l'employé
     */
   /**
 * Calcule le salaire brut social de l'employé
 */
public function get_salary_social($periode_id = null)
{
    // -------------------------------------------------------
    // Plafonds d'exonération CNPS réglementaires (SMIG 75 000 / 173,33)
    // Art. 54, 60, 61 CCI juillet 1977
    // -------------------------------------------------------
    $transport_cap = 30000; // défaut = Abidjan
    try {
        $branch_city = strtolower(trim($this->branch->city ?? $this->company->city ?? $this->branch->name ?? ''));
        if (str_contains($branch_city, 'bouak')) {
            $transport_cap = 24000;
        } elseif (!empty($branch_city) && !str_contains($branch_city, 'abidjan') && !str_contains($branch_city, 'siège')) {
            $transport_cap = 20000;
        }
    } catch (\Throwable $e) {
        $transport_cap = 30000;
    }

    $smig_horaire = 75000 / 173.33;
    $plafond_social = [
        'Prime de panier'             => round(3  * $smig_horaire),  // 1 298 FCFA
        'Prime de salissure'          => round(13 * $smig_horaire),  // 5 625 FCFA
        "Prime d'outillage"           => round(10 * $smig_horaire),  // 4 327 FCFA
        'Prime de tenue de travail'   => round(7  * $smig_horaire),  // 3 029 FCFA
        'Prime de transport légale'   => $transport_cap,             // Synchronisé avec le plafond fiscal
    ];

    // -------------------------------------------------------
    // Mots-clés reconnus comme EXONÉRATION explicite de CNPS.
    // Tout le reste (y compris "Soumis", vide, ou une valeur imprévue)
    // est considéré SOUMIS par défaut — c'est le comportement
    // sécurisé légalement : on ne doit jamais sous-déclarer la base
    // sociale par accident de saisie.
    // -------------------------------------------------------
    $exemption_keywords = ['exo', 'exonere', 'exonéré', 'non soumis', 'non-soumis', 'hors cnps'];

    $allowancesQuery = \App\Models\Allowance::where('employee_id', '=', $this->id);
    if ($periode_id) {
        $allowancesQuery->where('periode_id', '=', $periode_id);
    }
    $allowances = $allowancesQuery->get();
    $total_allowance = 0;
    $total_exo = 0;
    $total_exo2 = 0;

    $nbre_jours = intval($this->get_jours_work($periode_id));

    foreach ($allowances as $allowance) {
        $base_amount = (float) ($allowance->montant ?: $allowance->amount);
        $current_amount = ($nbre_jours == 30) ? $base_amount : (($base_amount / 30) * $nbre_jours);
        $title = trim($allowance->title ?? '');

        // --- Plafonnement automatique pour primes réglementées CCI ---
        $plafond_applicable = null;
        foreach ($plafond_social as $prime_name => $cap) {
            if (stripos($title, $prime_name) !== false || stripos($prime_name, $title) !== false) {
                $plafond_applicable = $cap;
                break;
            }
        }

        if ($plafond_applicable !== null) {
            // Partie exonérée = min(montant, plafond légal)
            $exo_part = min($current_amount, $plafond_applicable);
            // Partie soumise = max(0, montant - plafond)
            $soumis_part = max(0, $current_amount - $plafond_applicable);
            $total_exo    += $exo_part;
            $total_allowance += $soumis_part;
        } else {
            // Normalisation robuste : trim + minuscule, insensible aux variantes
            $trait_cnps_normalized = mb_strtolower(trim($allowance->trait_cnps ?? ''));

            $is_exempt = false;
            foreach ($exemption_keywords as $keyword) {
                if ($trait_cnps_normalized !== '' && str_contains($trait_cnps_normalized, $keyword)) {
                    $is_exempt = true;
                    break;
                }
            }

            if ($is_exempt) {
                $total_exo += $current_amount;
            } else {
                // Par défaut : SOUMIS à CNPS (couvre "Soumis", valeurs vides,
                // fautes de frappe, ou toute valeur non reconnue)
                $total_allowance += $current_amount;
            }
        }

        if (strpos($allowance->trait_fisc, 'exo 100%') === 0) {
            if ($allowance->code == '111' && $current_amount > 30000) {
                $total_exo2 = $total_exo2 + 30000;
            } else {
                $total_exo2 = $total_exo2 + $current_amount;
            }
        }
    }

    //avatnage en nature
    $avantagesQuery = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id)->where('is_active', 1);
    if ($periode_id) {
        $avantagesQuery->where('periode_id', '=', $periode_id);
    }
    $avantages = $avantagesQuery->get();
    $total_avantages_real = 0;
    $total_avantages_bare = 0;
    $total_avantages_real1 = 0;
    $total_avantages_bare1 = 0;
    $total_avantages_real2 = 0;
    $total_avantages_bare2 = 0;
    foreach ($avantages as $avantage) {
        $total_avantages_bare = $total_avantages_bare + $avantage->amount;
        $total_avantages_real = $total_avantages_real + $avantage->amount_reel;
        if ($avantage->type_avantage == 'avantage_en_nature') {
            $total_avantages_bare1 = $total_avantages_bare1 + $avantage->amount;
            $total_avantages_real1 = $total_avantages_real1 + $avantage->amount_reel;
        } else {
            $total_avantages_bare2 = $total_avantages_bare2 + $avantage->amount;
            $total_avantages_real2 = $total_avantages_real2 + $avantage->amount_reel;
        }
    }

    if ($total_avantages_real > 0) {
        $salary_brut_total = ($this->get_brut_salary($periode_id) - $total_avantages_real);
    } else {
        $salary_brut_total = $this->get_brut_salary($periode_id);
    }

    $net_salary_social = round($salary_brut_total - $total_exo) + $total_avantages_bare1 + $total_avantages_real2;

    if ($net_salary_social > 3375000) {
        $net_salary_social = 3375000;
    }

    return round($net_salary_social);
}
    /**
     * Calcule le salaire net de l'employé
     */
    public function get_net_salary($periode_id = null)
    {
        $allowances = \App\Models\Allowance::where('employee_id', '=', $this->id);
        if ($periode_id) {
            $allowances->where('periode_id', '=', $periode_id);
        }
        $allowances = $allowances->get();

        $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id);
        if ($periode_id) {
            $retenues->where('periode_id', '=', $periode_id);
        }
        $retenues = $retenues->get();
        $nbre_jours = intval($this->get_jours_work($periode_id));
        $brut_total = $this->get_brut_salary($periode_id);

        //calculons les charges des employés
        $autreretenue = 0;
        $loan = 0;
        $rembourssement = 0;
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 403 || $retenue->code == 301 || $retenue->code == 302) {
                $totalretenues += $retenue->amount;
            }
            if ($retenue->code == 601) {
                $rembourssement += $retenue->amount;
            }
            if ($retenue->code == 501 && $retenue->type == 'add') {
                $autreretenue += $retenue->amount;
            }
            if ($retenue->code == 500) {
                $loan += $retenue->amount;
            }
        }

        //avatnage en nature
        $avantagesQuery = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id)->where('is_active', 1);
        if ($periode_id) {
            $avantagesQuery->where('periode_id', '=', $periode_id);
        }
        $avantages = $avantagesQuery->get();
        $total_avantages_real = 0;
        $total_avantages_bare = 0;
        foreach ($avantages as $avantage) {
            $total_avantages_bare += $avantage->amount;
            $total_avantages_real += $avantage->amount_reel;
        }
        $mont_cant = 0;
        foreach ($allowances as $allowance) {
            if ($allowance->code == '131') {
                $mont_cant = $allowance->amount;
            }
        }

        //Net Salary Calculate 
        $salarynet = ($brut_total + $rembourssement) - ($totalretenues + $total_avantages_real + $mont_cant + $autreretenue + $loan);
        if ($nbre_jours <= 0) {
            return 0;
        } else {
            return $salarynet;
        }
    }

    public function get_net_salary_alltime()
    {
        // -------------------------------------------------------
        // Calcul du net théorique mensuel (base 30 jours)
        // Utilisé pour les affichages cumulatifs / statistiques
        // RICF 2024 : montants FIXES (Ordonnance n°2023-719)
        // -------------------------------------------------------
        $tax_payer_id = (intval($this->tax_payer_id) > 0) ? intval($this->tax_payer_id) : 30;

        // Bases imposable et sociale ramenées à 30 jours
        $brut_total = ($tax_payer_id == 30)
            ? $this->get_brut_salary(null)
            : round(($this->get_brut_salary(null) / $tax_payer_id) * 30);

        $brut = ($tax_payer_id == 30)
            ? $this->get_salary_imposable(null)
            : round(($this->get_salary_imposable(null) / $tax_payer_id) * 30);

        $brut_cnps = ($tax_payer_id == 30)
            ? $this->get_salary_social(null)
            : round(($this->get_salary_social(null) / $tax_payer_id) * 30);

        // CMU — correction : $this->cmu (pas $employees->cmu)
        $cmu = intval($this->cmu);
        if ($cmu < 7) {
            $coticmu = round($cmu * 500);
        } else {
            $coticmu = 3000 + round(($cmu - 6) * 1000);
        }

        // CNPS salariale (6,3%) — plafonnée à 45 × SMIG = 3 375 000 FCFA
        $brut_cnps_plaf = min($brut_cnps, 3375000);
        $cnps = round($brut_cnps_plaf * 6.3 / 100);

        // IRS brut — Barème 6 tranches (Ordonnance n°2023-719 / jan. 2024)
        if ($brut <= 75000) {
            $irs_brut = 0;
        } elseif ($brut <= 240000) {
            $irs_brut = round(($brut - 75000) * 0.16);
        } elseif ($brut <= 800000) {
            $irs_brut = round((165000 * 0.16) + (($brut - 240000) * 0.21));
        } elseif ($brut <= 2400000) {
            $irs_brut = round((165000 * 0.16) + (560000 * 0.21) + (($brut - 800000) * 0.24));
        } elseif ($brut <= 8000000) {
            $irs_brut = round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (($brut - 2400000) * 0.28));
        } else {
            $irs_brut = round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + (($brut - 8000000) * 0.32));
        }

        // RICF 2024 — Montants FIXES par nombre de parts (pas de pourcentage)
        $p = (float) str_replace(',', '.', (string) $this->parts);
        $fixedTable = [
            1.0 => 0,     1.5 => 5500,  2.0 => 11000, 2.5 => 16500,
            3.0 => 22000, 3.5 => 27500, 4.0 => 33000, 4.5 => 38500, 5.0 => 44000,
        ];
        $ricf = 0;
        foreach ($fixedTable as $key => $val) {
            if (abs($p - $key) < 0.01) { $ricf = $val; break; }
        }
        if ($p > 5.0) { $ricf = 44000; } // plafond légal

        $irs_net = max(0, $irs_brut - $ricf);

        // Total impôts salarié
        $impots = $irs_net + $cnps + $coticmu;

        // Avantages en nature (sans période → tous les actifs)
        $total_avantages_real = \Modules\NatureAvantage\Models\Avantage
            ::where('employee_id', $this->id)
            ->where('is_active', 1)
            ->sum('amount_reel');

        // Net = Brut - Impôts - Avantages en nature
        $salarynet = max(0, $brut_total - $impots - $total_avantages_real);

        // Cas Stagiaire / Apprenti / Prestataire
        if (in_array($this->statut_emp, ['Stagiaire', 'Apprenti', 'Prestataire'])) {
            return (float) $this->salary;
        }

        return round($salarynet);
    }


    public function getTotalSalarybrut()
    {
        // Obtenez la somme du salaire brut par employé pour le mois spécifié
        $totalSalaryNet = 0;

        // Récupérer l'année la plus ancienne des fiches de paie de cet employé
        $firstPaySlip = PaySlip::where('employee_id', $this->id)
            ->orderBy('salary_month', 'asc')
            ->first();

        if (!$firstPaySlip) {
            return 0;
        }

        $year = substr($firstPaySlip->salary_month, 0, 4);

        $salarynet = PaySlip::where('employee_id', $this->id)
            ->whereBetween('salary_month', [$year . '-01', date('Y-m')])
            ->get();
        foreach ($salarynet as $net) {
            $totalSalaryNet = $totalSalaryNet + $net->salary_brut;
        }

        return $totalSalaryNet;
    }

    public function getTotalSalaryRetenue()
    {
        // Obtenez la somme du salaire brut par employé jusqu'au mois en cours
        $totalSalaryNet = 0;
        $salarynet = PaySlip::where('employee_id', '=', $this->id)
            ->whereBetween('salary_month', [date('Y-01'), date('Y-m')])
            ->get();
        foreach ($salarynet as $net) {
            $totalSalaryNet = $totalSalaryNet + $net->total_retenue;
        }

        return $totalSalaryNet;
    }

    public function getTotalSalaryPatronale()
    {
        // Obtenez la somme du salaire brut par employé jusqu'au mois en cours
        $totalSalaryNet = 0;
        $salarynet = PaySlip::where('employee_id', '=', $this->id)
            ->whereBetween('salary_month', [date('Y-01'), date('Y-m')])
            ->get();
        foreach ($salarynet as $net) {
            $totalSalaryNet = $totalSalaryNet + $net->total_patronale;
        }

        return $totalSalaryNet;
    }

    public function getTotalSalaryImposable()
    {
        // Obtenez la somme du salaire brut par employé jusqu'au mois en cours
        $totalSalaryNet = 0;
        $salarynet = PaySlip::where('employee_id', '=', $this->id)
            ->whereBetween('salary_month', [date('Y-01'), date('Y-m')])
            ->get();
        foreach ($salarynet as $net) {
            $totalSalaryNet = $totalSalaryNet + $net->net_imposable;
        }

        return $totalSalaryNet;
    }

    public function getTotalSalaryNet()
    {
        // Obtenez la somme du salaire brut par employé jusqu'au mois en cours
        $totalSalaryNet = 0;
        $salarynet = PaySlip::where('employee_id', '=', $this->id)
            ->whereBetween('salary_month', [date('Y-01'), date('Y-m')])
            ->get();
        foreach ($salarynet as $net) {
            $totalSalaryNet = $totalSalaryNet + $net->net_payble;
        }

        return $totalSalaryNet;
    }

    public static function allowance($id, $periodeId)
    {
        //allowance
        $allowances = \App\Models\Allowance::where('employee_id', '=', $id)->where('periode_id', '=', $periodeId)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        $allowance_json = json_encode($allowances);

        return $allowance_json;
    }

    public static function retenue($id, $periodeId)
    {
        //retenue
        $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $id)->where('periode_id', '=', $periodeId)->get();
        $total_retenue = 0;
        foreach ($retenues as $retenue) {
            $total_retenue = $retenue->amount + $total_retenue;
        }

        $retenue_json = json_encode($retenues);

        return $retenue_json;
    }

    public static function employee_id()
    {
        $employee = Employee::latest()->first();

        return !empty($employee) ? $employee->id + 1 : 1;
    }

    /**
     * Génère un ID employé unique pour une entreprise donnée.
     * 
     * @param int $companyId
     * @return string
     */
    public static function generateUniqueId($companyId)
    {
        $company = \App\Models\Company::find($companyId);
        $prefix = $company->employee_prefix ?? 'EMP';

        // Récupérer tous les employee_id qui commencent par le préfixe pour cette entreprise
        $existingIds = self::where('company_id', $companyId)
            ->where('employee_id', 'LIKE', $prefix . '%')
            ->pluck('employee_id')
            ->toArray();

        $maxNum = 0;
        foreach ($existingIds as $id) {
            // Extraire la partie numérique après le préfixe
            $numPart = str_replace($prefix, '', $id);
            if (is_numeric($numPart)) {
                $maxNum = max($maxNum, (int) $numPart);
            }
        }

        // On commence par maxNum + 1, ou max(id) + 1 si c'est plus grand
        // (pour garder une certaine cohérence avec les anciens IDs basés sur id)
        $dbMaxId = self::where('company_id', $companyId)->max('id') ?? 0;
        $nextNum = max($maxNum + 1, $dbMaxId + 1);

        $employeeId = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Sécurité supplémentaire : boucle si jamais l'ID existe quand même
        while (self::where('company_id', $companyId)->where('employee_id', $employeeId)->exists()) {
            $nextNum++;
            $employeeId = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        }

        return $employeeId;
    }

    public function get_Telephone()
    {
        return $this->phone . ' / ' . $this->email;
    }

    public function salaryType()
    {
        return $this->hasOne('App\Models\PayslipType', 'id', 'salary_type');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function paySlip()
    {
        return $this->hasOne(\Modules\PaieSalaries\Models\PaySlip::class, 'id', 'employee_id');
    }

    public function present_status($employee_id, $data)
    {
        return AttendanceEmployee::where('employee_id', $employee_id)->where('date', $data)->first();
    }

    public static function employee_name($name)
    {

        $employee = Employee::where('id', $name)->first();
        if (!empty($employee)) {
            return $employee->name;
        }
    }

    public function employeeName()
    {
        return $this->name;
    }

    public static function login_user($name)
    {
        $user = User::where('id', $name)->first();
        return $user->name;
    }

    public static function employee_salary($salary)
    {

        $employee = Employee::where("salary", $salary)->first();
        if ($employee->salary == '0' || $employee->salary == '0.0') {
            return "-";
        } else {
            return $employee->salary;
        }
    }

    public function get_retenue($periodeId = null)
    {
        //impots
        $total_retenue = 0;
        $mont_cant = 0;
        $allowances = \App\Models\Allowance::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();

        foreach ($allowances as $allowance) {
            if ($allowance->code == '131') {
                $mont_cant = $allowance->amount;
            }
        }
        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 403 || $retenue->code == 301 || $retenue->code == 302) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues + $mont_cant;

        return $total_retenue;
    }

    public function get_patronale($periodeId = null)
    {
        //impots
        $parpatronal = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }

        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if (($retenue->code >= 409 && $retenue->code <= 412) || ($retenue->code >= 305 && $retenue->code <= 306)) {
                $totalretenues += $retenue->amount;
            }
        }

        $parpatronal = $totalretenues;

        return $parpatronal;
    }
    // modifier le 06/05/2026
    // public function get_jours_work($periodeId = null)
    // {

    //     $allowance = \App\Models\Allowance::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->first();

    //     $jours_work = $allowance ? $allowance->jours_work : 0;

    //     return $jours_work;
    // }
    public function get_jours_work($periodeId = null)
    {
        if ($periodeId) {
            $allowance = \App\Models\Allowance::where('employee_id', $this->id)
                ->where('periode_id', $periodeId)
                ->first();

            if ($allowance && $allowance->jours_work > 0) {
                $j = intval($allowance->jours_work);
                // Normalisation : si c'est un mois complet (28 = fév, 31 = mois long), on traite comme 30 jours
                // 29 jours n'est PAS normalisé : c'est une absence d'un jour → proratisation (29/30)
                // Borne haute : la paie est assise sur 30 jours, au-delà on majorerait le
                // salaire au lieu de le proratiser. Aucune donnée n'est dans ce cas
                // aujourd'hui, c'est un garde-fou contre une saisie aberrante.
                return ($j == 28 || $j == 31) ? 30 : min($j, 30);
            }
        }

        // Repli sur la fiche du salarié, borné de la même façon.
        return $this->tax_payer_id ? min(intval($this->tax_payer_id), 30) : 30;
    }
    public function get_avantage_bareme($periode_id = null)
    {
        $avtg_bareme = 0;
        $query = \Modules\NatureAvantage\Models\Avantage::where('employee_id', $this->id)
            ->where('type_avantage', 'avantage_en_nature')
            ->where('is_active', 1);

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        $somme_montant_bare = $query->sum('amount');

        if ($somme_montant_bare) {
            $avtg_bareme = $somme_montant_bare;
            return $avtg_bareme;
        } else {
            return 0;
        }
    }

    public function get_avantage_reel2($periode_id = null)
    {
        $avtg_reat = 0;
        $query = \Modules\NatureAvantage\Models\Avantage::where('employee_id', $this->id)
            ->where('type_avantage', 'avantage_en_argent')
            ->where('is_active', 1);

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        $somme_amount_reel = $query->sum('amount_reel');

        if ($somme_amount_reel) {
            $avtg_reat += $somme_amount_reel;
            return $avtg_reat;
        } else {
            return 0;
        }
    }

    public function get_brut_salary_base_sup($periodeId = null)
    {
        //impots
        $nbre_jours = $this->tax_payer_id;
        if (intval($this->tax_payer_id) == 30) {
            $base_salary = $this->salary;
        } else {
            $base_salary = $this->salary;
        }

        //allowances
        $allowances = \App\Models\Allowance::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            if ((strpos($allowance->trait_fisc, 'exo 10%') === 0) || $allowance->allowance_option == '1' || $allowance->base_heures == '1') {
                $total_allowance = $total_allowance + $allowance->amount;
            }
        }

        //avatnage en nature
        $avantages = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id);
        if ($periodeId) {
            $avantages->where('periode_id', '=', $periodeId);
        }
        $avantages = $avantages->get();
        $total_avantages_real = 0;
        foreach ($avantages as $avantage) {
            $total_avantages_real = $total_avantages_real + $avantage->amount_reel;
        }

        //Brut Salary Calculate

        $salary_brut_total = ($base_salary + $total_allowance + $total_avantages_real);

        return $salary_brut_total;
    }

    public function get_loan_retenue($periodeId = null)
    {
        // Vérifie si des prêts actifs existent pour l'employé sans retenue
        $totalDeduction = \Modules\Loans\Models\Loan::where('employee_id', $this->id)
            ->where('periode_id', '=', $periodeId)
            ->where('statut', 'running') // Prêts en cours
            ->sum('amount_deduc'); // Calcule directement le total des déductions

        return $totalDeduction ?? 0; // Retourne le montant total des déductions
    }

    public function get_Autre_retenue($periodeId = null)
    {
        // Vérifie si des prêts actifs existent pour l'employé sans retenue
        $totalDeduction = \Modules\PaieSalaries\Models\Retenue::where('employee_id', $this->id)
            ->whereBetween('type_retenue_id', [1, 4])
            ->where('periode_id', '=', $periodeId)
            ->where('type', 'add') // Prêts en cours
            ->sum('amount'); // Calcule directement le total des déductions

        return $totalDeduction ?? 0;
    }

    public function get_Rembourssement($periodeId = null)
    {
        // Vérifie si des Rembourssement actifs existent pour l'employé sans retenue
        $totalRembourssement = \Modules\PaieSalaries\Models\Retenue::where('employee_id', $this->id)
            ->where('type_retenue_id', 5)
            ->where('periode_id', '=', $periodeId)
            ->where('type', 'add') // Rembourssement en cours
            ->sum('amount'); // Calcule directement le total des déductions

        return $totalRembourssement ?? 0;
    }

    public function get_Adress_Emp()
    {
        $address = $this->address;

        return $address;
    }

    public function get_Situation()
    {
        // Situation non renseignée (ou valeur inconnue) : on n'interrompt pas la génération du bulletin
        $situation = '-';

        if ($this->martalstatu_id == '1') {
            $situation = 'Célibataire';
        } else if ($this->martalstatu_id == '2') {
            $situation = 'Marié(e)';
        } else if ($this->martalstatu_id == '3') {
            $situation = 'Divorcé(e)';
        } else if ($this->martalstatu_id == '4') {
            $situation = 'Veuf(ve)';
        }

        return $situation;
    }

    public function get_Enfants()
    {
        $enfant = $this->enfant;

        return $enfant;
    }

    public function get_Num_Cnps()
    {
        $num_cnps = $this->num_cnps;

        return $num_cnps;
    }

    public function get_Anciennete($date_reference = null)
    {
        $date_embauche = new \DateTime($this->start_date);
        
        if ($date_reference) {
            $date_actuelle = new \DateTime($date_reference);
        } else {
            $date_actuelle = new \DateTime(date('Y-m-d'));
        }

        // Ajouter 1 jour à la date de référence pour que le dernier jour du mois
        // soit compté comme un mois complet lors du calcul de la différence.
        $date_actuelle->modify('+1 day');

        $difference = $date_embauche->diff($date_actuelle);
        $date_pa = $difference->format('%y');
        $date_m = $difference->format('%m');

        $anciennete = $date_pa . ' an(s) et ' . $date_m . ' mois';

        return $anciennete;
    }

    public function get_Categorie()
    {
        $postevalue = $this->categorieEmp->title ?? '-';
        $valueposte = $this->sous_categorie;

        $categories = $postevalue . ' / ' . $valueposte;

        return $categories;
    }

    public function get_Emploi()
    {
        $emploi = $this->Designation->name ?? '-';

        return $emploi;
    }

    public function get_Nombre_parts()
    {
        return $this->parts;
    }

    public function get_Nom_Etp()
    {
        return $this->company->name ?? '-';
    }

    public function get_Adresse_Etp()
    {
        return ($this->company->city ?? '') . ', ' . ($this->company->address ?? '');
    }

    public function get_Telephone_Etp()
    {
        return $this->company->phone ?? '-';
    }

    public function get_Boite_postale()
    {
        return $this->company->postal_code ?? '-';
    }

    public function get_Salary_base($periodeId = null)
    {
        // Meme source de jours que get_brut_salary() pour eviter toute divergence
        $nbre_jours = intval($this->get_jours_work($periodeId));
        if ($nbre_jours <= 0) {
            $nbre_jours = 30;
        }

        // Proratisation du salaire categoriel sur une base de 30 jours
        if ($nbre_jours == 30) {
            $base_salary = $this->salary;
        } else {
            $base_salary = round(($this->salary / 30) * $nbre_jours);
        }

        return (float) ($base_salary ?: 0);
    }

    public function get_brut_salary_base_leave($periodeId = null)
    {
        //impots
        $nbre_jours = $this->tax_payer_id;
        if (intval($this->tax_payer_id) == 30) {
            $base_salary = $this->salary;
        } else {
            $base_salary = $this->salary;
        }

        //allowances
        $allowances = \App\Models\Allowance::where('employee_id', '=', $this->id)->where('periode_id', $periodeId)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            if ((strpos($allowance->trait_fisc, 'exo 10%') === 0) || $allowance->allowance_option == '1' || $allowance->base_heures == '1') {
                $total_allowance = $total_allowance + $allowance->amount;
            }
        }

        //avatnage en nature
        $avantages = \Modules\NatureAvantage\Models\Avantage::where('employee_id', '=', $this->id);
        if ($periodeId) {
            $avantages->where('periode_id', '=', $periodeId);
        }
        $avantages = $avantages->get();
        $total_avantages_real = 0;
        foreach ($avantages as $avantage) {
            $total_avantages_real = $total_avantages_real + $avantage->amount_reel;
        }

        //Brut Salary Calculate

        $salary_brut_total = ($base_salary + $total_allowance + $total_avantages_real);

        return $salary_brut_total;
    }

    public function get_contrat()
    {
        $employees = Employee::where('id', '=', $this->id)->first();

    }

    public function get_pay_type()
    {
        $employee = Employee::where('id', '=', $this->id)->first();

        $payType = $employee->paytypeEmp->name ?? '-';

        return $payType;
    }

    // Ajout de la relation pointeuses
    public function pointeuses($periodeId = null)
    {
        // Remplacez 'Pointeuse' par le nom correct du modèle si différent
        return $this->hasMany(\Modules\Time\Models\Pointeuse::class, 'employee_id', 'id');
    }

    public function get_imp_net($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 403) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_cnps_sal($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 301) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_cmu_sal($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 302) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_loan($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 500) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_ce_emp($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 409) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_ce_exp_emp($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 410) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_taxe_appr($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 411) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_taxe_fpc($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 412) {
                $totalretenues += $retenue->amount;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_cmu_emp($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 302) {
                $totalretenues += $retenue->patronale;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }

    public function get_cnps_emp($periodeId = null)
    {
        //impots
        $total_retenue = 0;

        if ($periodeId) {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->where('periode_id', '=', $periodeId)->get();
        } else {
            $retenues = \Modules\PaieSalaries\Models\Retenue::where('employee_id', '=', $this->id)->get();
        }
        //calculons les charges des employés
        $totalretenues = 0;
        foreach ($retenues as $retenue) {
            if ($retenue->code == 301) {
                $totalretenues += $retenue->patronale;
            }
        }

        $total_retenue = $totalretenues;

        return $total_retenue;
    }
}
