<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Dashboard employé
     */
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->type !== 'employee') {
            abort(403, 'Accès non autorisé');
        }

        $user = Auth::user();

        // Statistiques de base pour l'employé
        $stats = [
            'my_tasks' => 0, // À implémenter selon la logique métier
            'pending_requests' => 0,
            'upcoming_events' => 0,
        ];

        return view('employee.dashboard', compact('user', 'stats'));
    }
}
