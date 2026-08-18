<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company; 

class DashboardApiController extends Controller
{
    public function getCompanies(Request $request)
    {
        $companies = Company::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }

      public function getKpis(Request $request)
    {
        // 1. Calcul de l'effectif
        // Remplacez 'App\Models\Employee' par le nom de votre modèle d'employé
        $effectif_total = \App\Models\Employee::count(); 

        // 2. Calcul des absences en cours aujourd'hui
        // Remplacez 'App\Models\Leave' par votre modèle de congés/absences
        $absents = \App\Models\Leave::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('status', 'approved')
            ->count();
        
        // 3. Calcul du taux
        $taux_absenteisme = $effectif_total > 0 ? round(($absents / $effectif_total) * 100, 2) : 0;

        return response()->json([
            'demographie' => [
                'effectif_total' => $effectif_total,
                'taux_absenteisme' => $taux_absenteisme,
                'turnover' => 0
            ],
            'financier' => [
                'masse_salariale' => 0, // Idem, vous pouvez faire un sum() de vos salaires ici
                'cout_moyen_employe' => 0,
                'heures_sup_cout' => 0
            ],
            'operationnel' => [
                'retards_mois' => 0,
                'heures_sup_volume' => 0,
                'paie_retard' => 0
            ],
            'climat' => [
                'conges_en_cours' => [],
                'demandes_en_attente' => 0,
                'taux_satisfaction' => 0
            ],
            'conformite' => [
                'visites_medicales_retard' => 0,
                'contrats_expirant' => 0
            ]
        ]);
    }

}
