<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;  
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Service pour la gestion des calculs de temps de travail
 *
 * @package App\Services
 */
class TimeTrackingService
{
    public function calculateWorkingTime(Collection $pointages) {
        $entree = $pointages->firstWhere('type', 'entree');
        $sortie = $pointages->firstWhere('type', 'sortie');

        if (!$entree || !$sortie) {
            return '--:--';
        }

        $start = Carbon::parse("{$entree->auth_date} {$entree->auth_time}");
        $end = Carbon::parse("{$sortie->auth_date} {$sortie->auth_time}");

        $diff = $start->diff($end);
        return sprintf('%02d:%02d', $diff->h, $diff->i);
    }

    /**
     * Calcule le temps de travail total pour une semaine donnée
     *
     * @param Model $employee L'employé avec sa relation pointages
     * @param array $days Tableau des jours à calculer (objets Carbon)
     * @return string Format HH:MM du temps total
     */
    public function calculateWeeklyTotal(Model $employee, array $days): string
    {
        if (empty($days) || !$employee) {
            return '00:00';
        }

        $totalMinutes = 0;

        // Optimisation : récupérer tous les pointages de la semaine en une seule requête
        $weekDates = array_map(fn($day) => $day->format('Y-m-d'), $days);
        $weekPointages = $employee->pointeuses()
            ->whereIn('auth_date', $weekDates)
            ->orderBy('auth_date')
            ->orderBy('auth_time')
            ->get()
            ->groupBy('auth_date');

        foreach ($days as $day) {
            $dateString = $day->format('Y-m-d');
            $dayPointages = $weekPointages->get($dateString, collect());

            $dailyMinutes = $this->calculateDailyWorkTime($dayPointages, $dateString);
            $totalMinutes += $dailyMinutes;
        }

        return $this->formatMinutesToTime($totalMinutes);
    }

    /**
     * Calcule le temps de travail total pour un mois donné
     *
     * @param Model $employee L'employé avec sa relation pointages
     * @param int $month Le mois (1-12)
     * @param int $year L'année
     * @return string Format HH:MM du temps total
     */
    public function calculateMonthlyTotal(Model $employee, int $month, int $year): string
    {
        if (!$employee || $month < 1 || $month > 12 || $year < 1900) {
            return '00:00';
        }

        // Optimisation : filtrer directement en base de données
        $monthlyPointages = $employee->pointeuses()
            ->whereYear('auth_date', $year)
            ->whereMonth('auth_date', $month)
            ->orderBy('auth_date')
            ->orderBy('auth_time')
            ->get()
            ->groupBy('auth_date');

        $totalMinutes = 0;

        foreach ($monthlyPointages as $dateString => $dayPointages) {
            $dailyMinutes = $this->calculateDailyWorkTime($dayPointages, $dateString);
            $totalMinutes += $dailyMinutes;
        }

        return $this->formatMinutesToTime($totalMinutes);
    }

    /**
     * Calcule le temps de travail pour une période personnalisée
     *
     * @param Model $employee L'employé
     * @param string $startDate Date de début (Y-m-d)
     * @param string $endDate Date de fin (Y-m-d)
     * @return string Format HH:MM du temps total
     */
    public function calculatePeriodTotal(Model $employee, string $startDate, string $endDate): string
    {
        if (!$employee) {
            return '00:00';
        }

        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            if ($end->lessThan($start)) {
                return '00:00';
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du parsing des dates: ' . $e->getMessage());
            return '00:00';
        }

        $pointages = $employee->pointeuses()
            ->whereBetween('auth_date', [$startDate, $endDate])
            ->orderBy('auth_date')
            ->orderBy('auth_time')
            ->get()
            ->groupBy('auth_date');

        $totalMinutes = 0;

        foreach ($pointages as $dateString => $dayPointages) {
            $dailyMinutes = $this->calculateDailyWorkTime($dayPointages, $dateString);
            $totalMinutes += $dailyMinutes;
        }

        return $this->formatMinutesToTime($totalMinutes);
    }

    /**
     * Calcule les statistiques détaillées pour une période
     *
     * @param Model $employee L'employé
     * @param string $startDate Date de début (Y-m-d)
     * @param string $endDate Date de fin (Y-m-d)
     * @return array Statistiques détaillées
     */
    public function calculateDetailedWorkStats(Model $employee, string $startDate, string $endDate): array
    {
        if (!$employee) {
            return $this->getEmptyStats();
        }

        $pointages = $employee->pointeuses()
            ->whereBetween('auth_date', [$startDate, $endDate])
            ->orderBy('auth_date')
            ->orderBy('auth_time')
            ->get()
            ->groupBy('auth_date');

        $totalMinutes = 0;
        $workingDays = 0;
        $detailsByDay = [];

        foreach ($pointages as $dateString => $dayPointages) {
            $dailyMinutes = $this->calculateDailyWorkTime($dayPointages, $dateString);

            if ($dailyMinutes > 0) {
                $totalMinutes += $dailyMinutes;
                $workingDays++;
            }

            $detailsByDay[$dateString] = [
                'time' => $this->formatMinutesToTime($dailyMinutes),
                'entries_count' => $dayPointages->where('type', 'entree')->count(),
                'exits_count' => $dayPointages->where('type', 'sortie')->count(),
                'first_entry' => $this->getFirstEntry($dayPointages),
                'last_exit' => $this->getLastExit($dayPointages)
            ];
        }

        $averageDailyMinutes = $workingDays > 0 ? intdiv($totalMinutes, $workingDays) : 0;

        return [
            'total_time' => $this->formatMinutesToTime($totalMinutes),
            'total_minutes' => $totalMinutes,
            'working_days' => $workingDays,
            'average_daily_time' => $this->formatMinutesToTime($averageDailyMinutes),
            'average_daily_minutes' => $averageDailyMinutes,
            'details_by_day' => $detailsByDay
        ];
    }

    /**
     * Calcule le temps de travail pour la semaine courante
     *
     * @param Model $employee L'employé
     * @return string Format HH:MM du temps total
     */
    public function calculateCurrentWeekTotal(Model $employee): string
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->copy()->addDays($i);
        }

        return $this->calculateWeeklyTotal($employee, $days);
    }

    /**
     * Calcule le temps de travail pour le mois courant
     *
     * @param Model $employee L'employé
     * @return string Format HH:MM du temps total
     */
    public function calculateCurrentMonthTotal(Model $employee): string
    {
        $now = Carbon::now();
        return $this->calculateMonthlyTotal($employee, $now->month, $now->year);
    }

    /**
     * Calcule le temps de retard/avance pour un employé sur une période
     *
     * @param Model $employee L'employé
     * @param string $startDate Date de début
     * @param string $endDate Date de fin
     * @param int $expectedDailyMinutes Temps de travail attendu par jour en minutes
     * @return array Détails des retards/avances
     */
    public function calculateTimeDeviations(Model $employee, string $startDate, string $endDate, int $expectedDailyMinutes = 480): array
    {
        $stats = $this->calculateDetailedWorkStats($employee, $startDate, $endDate);
        $totalExpectedMinutes = $stats['working_days'] * $expectedDailyMinutes;
        $deviationMinutes = $stats['total_minutes'] - $totalExpectedMinutes;

        return [
            'expected_total' => $this->formatMinutesToTime($totalExpectedMinutes),
            'actual_total' => $stats['total_time'],
            'deviation' => $this->formatMinutesToTime(abs($deviationMinutes)),
            'deviation_type' => $deviationMinutes >= 0 ? 'overtime' : 'undertime',
            'deviation_minutes' => $deviationMinutes
        ];
    }

    /**
     * Calcule le temps de travail pour une journée donnée
     *
     * @param Collection $dayPointages Pointages du jour
     * @param string $dateString Date au format Y-m-d
     * @return int Nombre de minutes travaillées
     */
    private function calculateDailyWorkTime(Collection $dayPointages, string $dateString): int
    {
        if ($dayPointages->isEmpty()) {
            return 0;
        }

        $entrees = $dayPointages->where('type', 'entree')->sortBy('auth_time');
        $sorties = $dayPointages->where('type', 'sortie')->sortBy('auth_time');

        // Gestion des cas multiples entrées/sorties dans la journée
        $totalMinutes = 0;
        $entreeIndex = 0;

        foreach ($sorties as $sortie) {
            // Chercher l'entrée correspondante (la plus récente avant cette sortie)
            $entree = null;
            while ($entreeIndex < $entrees->count()) {
                $currentEntree = $entrees->values()[$entreeIndex];
                if ($currentEntree->auth_time <= $sortie->auth_time) {
                    $entree = $currentEntree;
                    $entreeIndex++;
                } else {
                    break;
                }
            }

            if ($entree) {
                try {
                    $start = Carbon::parse("{$entree->auth_date} {$entree->auth_time}");
                    $end = Carbon::parse("{$sortie->auth_date} {$sortie->auth_time}");

                    // Vérification de cohérence : la sortie doit être après l'entrée
                    if ($end->greaterThan($start)) {
                        $totalMinutes += $start->diffInMinutes($end);
                    }
                } catch (\Exception $e) {
                    Log::warning("Erreur lors du calcul du temps pour {$dateString}: " . $e->getMessage());
                    continue;
                }
            }
        }

        return $totalMinutes;
    }

    /**
     * Formate les minutes en format HH:MM
     *
     * @param int $totalMinutes Nombre total de minutes
     * @return string Format HH:MM
     */
    private function formatMinutesToTime(int $totalMinutes): string
    {
        if ($totalMinutes < 0) {
            return '00:00';
        }

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Récupère la première entrée du jour
     *
     * @param Collection $dayPointages Pointages du jour
     * @return string|null Heure de la première entrée
     */
    private function getFirstEntry(Collection $dayPointages): ?string
    {
        $firstEntry = $dayPointages->where('type', 'entree')->sortBy('auth_time')->first();
        return $firstEntry ? $firstEntry->auth_time : null;
    }

    /**
     * Récupère la dernière sortie du jour
     *
     * @param Collection $dayPointages Pointages du jour
     * @return string|null Heure de la dernière sortie
     */
    private function getLastExit(Collection $dayPointages): ?string
    {
        $lastExit = $dayPointages->where('type', 'sortie')->sortByDesc('auth_time')->first();
        return $lastExit ? $lastExit->auth_time : null;
    }

    /**
     * Retourne les statistiques vides
     *
     * @return array
     */
    private function getEmptyStats(): array
    {
        return [
            'total_time' => '00:00',
            'total_minutes' => 0,
            'working_days' => 0,
            'average_daily_time' => '00:00',
            'average_daily_minutes' => 0,
            'details_by_day' => []
        ];
    }
}
