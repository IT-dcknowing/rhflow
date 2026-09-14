<?php

namespace Modules\PaieSalaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaySlip extends Model
{
    use HasFactory;

    protected $table = 'pay_slips';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
		'exercice_id',
        'employee_id',
		'periode_id',
		'net_payble',
		'salary_month',
		'status',
		'salary_brut',
		'net_imposable',
		'net_sociale',
		'basic_salary',
		'total_retenue',
		'total_patronale',
		'allowances',
		'retenues',
		'avtg_real',
		'avtg_real2',
		'avtg_bareme',
		'pay_type',
		'nbre_jour',
		'address_emp',
		'situation_emp',
		'enfant_emp',
		'num_cnps_emp',
		'anciennete_emp',
		'categories_emp',
		'emploi',
		'phone_emp',
		'parts_emp',
		'nom_etp',
		'adresse_etp',
		'phone_etp',
		'btp_etp',
		'code',
		'company_id', 
    ];

	public function employee(){
        return $this->belongsTo(\Modules\Employees\Models\Employee::class, 'employee_id');
    }

    public function periode(){
		return $this->belongsTo(\App\Models\PaiePeriode::class, 'periode_id');
	}

	public function exercice(){
		return $this->belongsTo(\App\Models\PaieExercice::class, 'exercice_id');
	}

	/**
	 * Charges du bulletin, lues dans ses lignes de retenue (JSON) :
	 * - salariales = ITS net (403) + CNPS (301) + CMU (302)
	 * - patronales = CNPS employeur (308, sinon part patronale portée par la 301)
	 *   + CMU employeur (307, sinon celle de la 302) + 409 à 412 + accident du travail (305) + prestations familiales (306)
	 */
	public function cotisations(): array
	{
		$lignes = collect(json_decode($this->retenues ?? '[]', true) ?: [])
			->filter(fn ($r) => trim((string) ($r['libelle'] ?? '')) !== 'TOTAL FDFP')
			->unique(fn ($r) => (string) ($r['code'] ?? ''))
			->keyBy(fn ($r) => (string) ($r['code'] ?? ''));

		$nombre = fn ($valeur) => (float) str_replace([' ', ','], ['', '.'], (string) ($valeur ?? 0));
		$montant = fn ($code) => $nombre($lignes->get((string) $code)['amount'] ?? 0);
		$partPatronale = fn ($code) => $nombre($lignes->get((string) $code)['patronale'] ?? 0);

		$salariales = $montant(403) + $montant(301) + $montant(302);

		$patronales = ($lignes->has('308') ? $montant(308) : $partPatronale(301))
			+ ($lignes->has('307') ? $montant(307) : $partPatronale(302));
		foreach ([409, 410, 411, 412, 305, 306] as $code) {
			$patronales += $montant($code);
		}

		return ['salariales' => round($salariales), 'patronales' => round($patronales)];
	}

	/**
	 * Cumuls de l'exercice (ligne « Année » du bulletin) : somme des bulletins du salarié dont la période
	 * appartient au même exercice et commence au plus tard à la date de la période de ce bulletin.
	 * Novembre additionne janvier à novembre, décembre y ajoute décembre, un nouvel exercice repart de zéro.
	 */
	public function cumulsAnnee(): array
	{
		$periode = \App\Models\PaiePeriode::withTrashed()->find($this->periode_id);

		$bulletins = static::where('employee_id', $this->employee_id)
			->where('company_id', $this->company_id)
			->whereNull('deleted_at')
			->where(function ($q) use ($periode) {
				// Le bulletin affiché compte toujours, même si sa période a été supprimée depuis
				$q->where('id', $this->id);
				if ($periode) {
					$q->orWhereIn('periode_id', \App\Models\PaiePeriode::where('exercice_id', $periode->exercice_id)
						->where('date_debut', '<=', $periode->date_debut)
						->pluck('id'));
				}
			})
			->get();

		$cumuls = ['nombre' => 0, 'brut' => 0, 'salariales' => 0, 'patronales' => 0, 'avantages' => 0, 'net_imposable' => 0, 'net' => 0];
		foreach ($bulletins as $bulletin) {
			$charges = $bulletin->cotisations();
			$cumuls['nombre']++;
			$cumuls['brut'] += (float) $bulletin->salary_brut;
			$cumuls['salariales'] += $charges['salariales'];
			$cumuls['patronales'] += $charges['patronales'];
			$cumuls['avantages'] += (float) $bulletin->avtg_real;
			$cumuls['net_imposable'] += (float) $bulletin->net_imposable;
			$cumuls['net'] += (float) $bulletin->net_payble;
		}

		return $cumuls;
	}
}
