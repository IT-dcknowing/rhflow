<?php
namespace App\Jobs;
use App\Models\User;
use Modules\Employees\Models\Employee;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\PaieSalaries\Models\SetSalarie;
use App\Models\PaiePeriode;
use Modules\Ruptures\Models\Rupture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Notification; // Import Custom Notification Model
use App\Models\PaieExercice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateBulletinsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //public $timeout = 3600; // 1h timeout
    protected $periodeId;
    protected $companyId;
    protected $total_avtg;
    protected $userId;
    
    /**
     * Create a new job instance.
     */
    public function __construct($periodeId, $companyId, $total_avtg, $userId)
    {
        $this->periodeId = $periodeId;
        $this->companyId = $companyId;
        $this->total_avtg = $total_avtg;
        $this->userId = $userId;
    }
    
    public function handle()
    {
        try{
            $periode = PaiePeriode::findOrFail($this->periodeId);
            
            $month = Carbon::parse($periode->date_debut)->format('Y-m');
            $year = Carbon::parse($periode->date_debut)->year;


            $employees = Employee::active()
                ->where('company_id', $this->companyId)
                ->where('start_date', '<=', $periode->date_fin)
                ->where(function ($query) use ($periode) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $periode->date_debut);
                })
                ->get();

            if ($employees->count() > 0) {
               foreach ($employees as $employee) {
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
                    $payslipEmployee->basic_salary = !empty($employee->get_Salary_base($periode->id)) ? $employee->get_Salary_base($periode->id) : 0;
                    $payslipEmployee->total_retenue = $employee->get_retenue($periode->id);
                    $payslipEmployee->total_patronale = $employee->get_patronale($periode->id);
                    $payslipEmployee->allowances = Employee::allowance($employee->id, $periode->id);
                    $payslipEmployee->retenues = Employee::retenue($employee->id, $periode->id);
                    $payslipEmployee->avtg_real = !empty($this->total_avtg) ? $this->total_avtg : 0;
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
                    $payslipEmployee->company_id = $this->companyId;
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
                if ($this->userId) {
                    Notification::create([
                        'type' => 'App\Notifications\BulletinGenerated', // Dummy type or reuse existing
                        'title' => 'Génération terminée',
                        'message' => 'La génération des bulletins de paie est terminée.',
                        'icon' => 'fas fa-check-circle',
                        'color' => 'success',
                        'user_id' => $this->userId,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                \Log::info('GenerateBulletinsJob completed successfully', [
                    'periode_id' => $this->periodeId,
                    'company_id' => $this->companyId,
                    'bulletins_count' => $missing_payslips->count(),
                ]);
            }
        } catch (\Throwable $e) {
            // Notification d'erreur
            if ($this->userId) {
                Notification::create([
                    'type' => 'App\Notifications\BulletinGenerationFailed',
                    'title' => 'Erreur de génération',
                    'message' => 'Une erreur est survenue lors de la génération des bulletins : ' . $e->getMessage(),
                    'icon' => 'fas fa-exclamation-circle',
                    'color' => 'danger',
                    'user_id' => $this->userId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            \Log::error('GenerateBulletinsJob failed', [
                'periode_id' => $this->periodeId,
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}