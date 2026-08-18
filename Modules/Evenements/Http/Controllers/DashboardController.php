<?php

namespace Modules\Evenements\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Evenements\Models\Announcement;
use Modules\Evenements\Models\Event;
use Modules\Evenements\Models\Meeting;
use Modules\Evenements\Models\Award;
use Modules\Evenements\Models\Promotion;
use Modules\Evenements\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $period = $request->get('period', 'month'); // month, quarter, year
        
        // Récupérer les statistiques avec tendances
        $stats = $this->getStatsWithTrends($companyId, $period);
        
        // Statistiques d'activité par mois pour le graphique
        $activityStats = $this->getActivityStats($companyId);

        // Prochains événements (7 prochains jours)
        $upcomingEvents = Event::with('branch')
            ->where('company_id', $companyId)
            ->whereBetween('start_date', [now(), now()->addDays(7)])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Dernières annonces
        $recentAnnouncements = Announcement::with(['branch', 'department'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(5)
            ->get();
            
        // Prochaines réunions
        $upcomingMeetings = Meeting::with('organizer')
            ->where('company_id', $companyId)
            ->where('date', '>=', now())
            ->orderBy('date')
            ->limit(5)
            ->get();
            
        // Dernières récompenses
        $recentAwards = Award::with(['employee', 'awardType'])
            ->where('company_id', $companyId)
            ->latest('date')
            ->limit(5)
            ->get();
            
        // Événements du mois en cours pour le mini-calendrier
        $currentMonthEvents = Event::where('company_id', $companyId)
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->select('id', 'title', 'start_date', 'end_date', 'color')
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->toDateString(),
                    'end' => $event->end_date ? $event->end_date->addDay()->toDateString() : null,
                    'color' => $event->color ?: '#3b82f6',
                    'url' => route('company.evenements.events.show', $event->id)  
                ];
            });
            
        // Récupérer les types d'événements avec leur nombre d'occurrences
        $eventTypes = \Modules\Evenements\Models\EventType::withCount(['events' => function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                }])
                ->where('company_id', $companyId)
                ->orWhere('is_default', true)
                ->get()
                ->map(function($type) {
                    return [
                        'name' => $type->name,
                        'color' => $type->color,
                        'count' => $type->events_count,
                        'icon' => $type->icon
                    ];
                })
                ->toArray();

        // Ajouter un compteur pour les événements sans type
        $untaggedCount = \Modules\Evenements\Models\Event::where('company_id', $companyId)
                ->whereNull('event_type_id')
                ->count();

            if ($untaggedCount > 0) {
                $eventTypes[] = [
                    'name' => 'Non classé',
                    'color' => '#6b7280',
                    'count' => $untaggedCount,
                    'icon' => 'tag-off'
                ];
            }

        return view('evenements::dashboard', compact(
            'stats',
            'upcomingEvents',
            'recentAnnouncements',
            'upcomingMeetings',
            'recentAwards',
            'activityStats',
            'currentMonthEvents',
            'eventTypes',
            'untaggedCount',
            'period'
        ));
    }
    
    /**
     * Récupère les statistiques avec les tendances
     */
    private function getStatsWithTrends($companyId, $period = 'month')
    {
        $endDate = now();
        $startDate = clone $endDate;
        $compareStartDate = clone $startDate;
        
        switch ($period) {
            case 'quarter':
                $startDate->subMonths(3);
                $compareStartDate->subMonths(6);
                break;
            case 'year':
                $startDate->subYear();
                $compareStartDate->subYears(2);
                break;
            default: // month
                $startDate->subMonth();
                $compareStartDate->subMonths(2);
                break;
        }
        
        // Fonction pour calculer la tendance
        $calculateTrend = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };
        
        // Annonces
        $currentAnnouncements = Announcement::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->count();
            
        $previousAnnouncements = Announcement::where('company_id', $companyId)
            ->whereBetween('created_at', [$compareStartDate, $startDate])
            ->count();
            
        // Événements
        $currentEvents = Event::where('company_id', $companyId)
            ->where('start_date', '>=', $startDate)
            ->count();
            
        $previousEvents = Event::where('company_id', $companyId)
            ->whereBetween('start_date', [$compareStartDate, $startDate])
            ->count();
            
        // Réunions
        $currentMeetings = Meeting::where('company_id', $companyId)
            ->where('date', '>=', $startDate)
            ->count();
            
        $previousMeetings = Meeting::where('company_id', $companyId)
            ->whereBetween('date', [$compareStartDate, $startDate])
            ->count();
            
        // Récompenses
        $currentAwards = Award::where('company_id', $companyId)
            ->where('date', '>=', now()->subYear())
            ->count();
            
        $previousAwards = Award::where('company_id', $companyId)
            ->whereBetween('date', [now()->subYears(2), now()->subYear()])
            ->count();

        return [
            'total_announcements' => [
                'value' => $currentAnnouncements,
                'trend' => $calculateTrend($currentAnnouncements, $previousAnnouncements),
                'trend_type' => $currentAnnouncements >= $previousAnnouncements ? 'up' : 'down'
            ],
            'upcoming_events' => [
                'value' => Event::where('company_id', $companyId)
                    ->where('start_date', '>=', now())
                    ->count(),
                'trend' => $calculateTrend($currentEvents, $previousEvents),
                'trend_type' => $currentEvents >= $previousEvents ? 'up' : 'down'
            ],
            'total_meetings' => [
                'value' => $currentMeetings,
                'trend' => $calculateTrend($currentMeetings, $previousMeetings),
                'trend_type' => $currentMeetings >= $previousMeetings ? 'up' : 'down'
            ],
            'recent_awards' => [
                'value' => $currentAwards,
                'trend' => $calculateTrend($currentAwards, $previousAwards),
                'trend_type' => $currentAwards >= $previousAwards ? 'up' : 'down'
            ],
            'recent_promotions' => [
                'value' => Promotion::where('company_id', $companyId)
                    ->where('promotion_date', '>=', now()->subYear())
                    ->count(),
                'trend' => 0, // À implémenter si nécessaire
                'trend_type' => 'neutral'
            ],
            'recent_transfers' => [
                'value' => Transfer::where('company_id', $companyId)
                    ->where('transfer_date', '>=', now()->subYear())
                    ->count(),
                'trend' => 0, // À implémenter si nécessaire
                'trend_type' => 'neutral'
            ],
        ];
    }
    
    /**
     * Récupère les statistiques d'activité pour le graphique
     */
    private function getActivityStats($companyId, $months = 6)
    {
        $endDate = now()->endOfMonth();
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        
        // Générer les mois pour assurer qu'ils apparaissent même sans données
        $months = collect();
        $current = clone $startDate;
        
        while ($current <= $endDate) {
            $months->push([
                'month' => $current->format('Y-m'),
                'label' => $current->translatedFormat('M Y'),
                'events' => 0,
                'meetings' => 0,
                'announcements' => 0,
                'awards' => 0
            ]);
            $current->addMonth();
        }
        
        // Récupérer les événements par mois
        $events = Event::select(
                DB::raw('DATE_FORMAT(start_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
            
        // Récupérer les réunions par mois
        $meetings = Meeting::select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
            
        // Récupérer les annonces par mois
        $announcements = Announcement::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
            
        // Récupérer les récompenses par mois
        $awards = Award::select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Fusionner les données
        return $months->map(function($item) use ($events, $meetings, $announcements, $awards) {
            $month = $item['month'];
            return [
                'month' => $month,
                'label' => $item['label'],
                'events' => $events[$month] ?? 0,
                'meetings' => $meetings[$month] ?? 0,
                'announcements' => $announcements[$month] ?? 0,
                'awards' => $awards[$month] ?? 0
            ];
        });
    }
}
