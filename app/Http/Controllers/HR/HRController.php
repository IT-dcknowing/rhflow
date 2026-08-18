<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\Demande;
use Modules\Evenements\Models\Event;
use Modules\PaieSalaries\Models\PaySlip;

class HRController extends Controller
{
    /**
     * Dashboard RH
     */
    public function dashboard()
    {
        if (!Auth::check() || !in_array(Auth::user()->type, ['hr', 'paie', 'payroll'])) {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        // Sans rattachement à une entreprise, aucune donnée n'est consultable.
        // On le signale à la vue plutôt que d'afficher des compteurs à zéro.
        $rattache = !empty($companyId);

        $stats = [
            'total_employees' => 0,
            'active_employees' => 0,
            'pending_requests' => 0,
            'upcoming_events' => 0,
            'payslips_month' => 0,
        ];

        if ($rattache) {
            $stats['total_employees'] = Employee::where('company_id', $companyId)->count();
            $stats['active_employees'] = Employee::where('company_id', $companyId)->where('is_active', 1)->count();
            $stats['pending_requests'] = Demande::where('company_id', $companyId)->where('status', 'pending')->count();
            $stats['upcoming_events'] = Event::where('company_id', $companyId)
                ->whereDate('start_date', '>=', now()->toDateString())
                ->count();
            $stats['payslips_month'] = PaySlip::where('company_id', $companyId)
                ->where('salary_month', now()->format('Y-m'))
                ->count();
        }

        // Le rôle Paie et le rôle RH n'ouvrent pas les mêmes sections.
        // La correspondance est centralisée dans config/menu_sections.php,
        // pour que le dashboard et la barre latérale restent cohérents.
        $estPaie = in_array($user->type, ['paie', 'payroll']);
        $sections = config('menu_sections.' . $user->type, []);

        return view('hr.dashboard', compact('user', 'stats', 'rattache', 'estPaie', 'sections'));
    }
}
