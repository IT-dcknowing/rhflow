<?php

namespace Modules\Evenements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Evenements\Models\Meeting;
use Modules\Evenements\Models\EventType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Affiche la liste des réunions
     */
    public function index(Request $request)
    {
        // Récupération des paramètres de filtrage
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Construction de la requête
        $query = Meeting::with(['type', 'participants'])
            ->where('company_id', auth()->user()->company_id);

        // Filtrage par recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($status) {
            $query->where('status', $status);
        }

        // Filtrage par type
        if ($type) {
            $query->where('event_type_id', $type);
        }

        // Filtrage par date
        if ($startDate) {
            $query->whereDate('time', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('time', '<=', $endDate);
        }

        // Tri par date de début (du plus récent au plus ancien)
        $query->orderBy('time', 'desc');

        // Pagination
        $meetings = $query->paginate(15)->withQueryString();

        // Calcul des statistiques
        $totalMeetings = Meeting::where('company_id', auth()->user()->company_id)->count();
        $completedMeetings = Meeting::where('company_id', auth()->user()->company_id)
            ->where('status', 'completed')
            ->count();
        $upcomingMeetings = Meeting::where('company_id', auth()->user()->company_id)
            ->where('time', '>', now())
            ->count();
        
        // Calcul du nombre total de participants
        $totalParticipants = DB::table('event_participants')
            ->join('meetings', 'meetings.id', '=', 'event_participants.event_id')
            ->where('meetings.company_id', auth()->user()->company_id)
            ->count();

        // Récupération des types de réunion pour le filtre
        $meetingTypes = EventType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->get();

        return view('evenements::meetings.index', [
            'meetings' => $meetings,
            'meetingTypes' => $meetingTypes,
            'totalMeetings' => $totalMeetings,
            'completedMeetings' => $completedMeetings,
            'upcomingMeetings' => $upcomingMeetings,
            'totalParticipants' => $totalParticipants,
        ]);
    }

    /**
     * Affiche le formulaire de création d'une réunion
     */
    public function create()
    {
        $meetingTypes = EventType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->get();
            
        return view('evenements::meetings.create', compact('meetingTypes'));
    }

    /**
     * Enregistre une nouvelle réunion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'start_time' => 'required|date',
            'location' => 'nullable|string|max:255',
            'is_online' => 'boolean',
            'meeting_url' => 'nullable|url|required_if:is_online,1',
            'description' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
        ]);

        // Création de la réunion
        $meeting = Meeting::create([
            'title' => $validated['title'],
            'meeting_type_id' => $validated['meeting_type_id'],
            'time' => $validated['start_time'],
            'location' => $validated['location'],
            'is_online' => $validated['is_online'] ?? false,
            'meeting_url' => $validated['meeting_url'] ?? null,
            'description' => $validated['description'],
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
            'status' => 'scheduled',
        ]);

        // Ajout des participants
        $meeting->participants()->attach($validated['participants']);

        // Envoi des notifications (à implémenter)
        // $meeting->notifyParticipants();

        return redirect()->route('company.evenements.meetings.index')
            ->with('success', 'Réunion créée avec succès.');
    }

    /**
     * Affiche les détails d'une réunion
     */
    public function show(Meeting $meeting)
    {
        $this->authorize('view', $meeting);
        
        $meeting->load(['type', 'participants', 'createdBy']);
        
        return view('evenements::meetings.show', compact('meeting'));
    }

    /**
     * Affiche le formulaire de modification d'une réunion
     */
    public function edit(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $meetingTypes = MeetingType::where('company_id', auth()->user()->company_id)
            ->orWhereNull('company_id')
            ->get();
            
        $selectedParticipants = $meeting->participants->pluck('id')->toArray();
        
        return view('evenements::meetings.edit', compact('meeting', 'meetingTypes', 'selectedParticipants'));
    }

    /**
     * Met à jour une réunion existante
     */
    public function update(Request $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'is_online' => 'boolean',
            'meeting_url' => 'nullable|url|required_if:is_online,1',
            'description' => 'nullable|string',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
        ]);

        // Mise à jour de la réunion
        $meeting->update([
            'title' => $validated['title'],
            'meeting_type_id' => $validated['meeting_type_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'is_online' => $validated['is_online'] ?? false,
            'meeting_url' => $validated['meeting_url'] ?? null,
            'description' => $validated['description'],
        ]);

        // Mise à jour des participants
        $meeting->participants()->sync($validated['participants']);

        // Envoi des notifications de mise à jour (à implémenter)
        // $meeting->notifyParticipantsAboutUpdate();

        return redirect()->route('company.evenements.meetings.show', $meeting)
            ->with('success', 'Réunion mise à jour avec succès.');
    }

    /**
     * Supprime une réunion
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorize('delete', $meeting);
        
        // Envoi des notifications de suppression (à implémenter)
        // $meeting->notifyParticipantsAboutDeletion();
        
        $meeting->delete();
        
        return redirect()->route('company.evenements.meetings.index')
            ->with('success', 'Réunion supprimée avec succès.');
    }
    
    /**
     * Affiche le calendrier des réunions
     */
    public function calendar()
    {
        return view('evenements::meetings.calendar');
    }
    
    /**
     * Récupère les événements pour le calendrier (format JSON)
     */
    public function getCalendarEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        
        $events = Meeting::where('company_id', auth()->user()->company_id)
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                      });
            })
            ->with('type')
            ->get()
            ->map(function($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'start' => $meeting->start_time->toIso8601String(),
                    'end' => $meeting->end_time->toIso8601String(),
                    'url' => route('company.evenements.meetings.show', $meeting),
                    'className' => 'bg-' . ($meeting->type->color ?? 'primary'),
                    'extendedProps' => [
                        'type' => $meeting->type->name ?? 'Non spécifié',
                        'location' => $meeting->location,
                        'is_online' => $meeting->is_online,
                    ]
                ];
            });
            
        return response()->json($events);
    }
    
    /**
     * Marque une réunion comme démarrée
     */
    public function start(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $meeting->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
        
        // Envoi de la notification (à implémenter)
        // $meeting->notifyParticipantsAboutStart();
        
        return redirect()->back()
            ->with('success', 'La réunion a été marquée comme démarrée.');
    }
    
    /**
     * Marque une réunion comme terminée
     */
    public function complete(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $meeting->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
        
        // Envoi de la notification (à implémenter)
        // $meeting->notifyParticipantsAboutCompletion();
        
        return redirect()->back()
            ->with('success', 'La réunion a été marquée comme terminée.');
    }
    
    /**
     * Annule une réunion
     */
    public function cancel(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $meeting->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);
        
        // Envoi de la notification (à implémenter)
        // $meeting->notifyParticipantsAboutCancellation();
        
        return redirect()->back()
            ->with('success', 'La réunion a été annulée.');
    }
    
    /**
     * Exporte les réunions au format Excel
     */
    public function export(Request $request)
    {
        // À implémenter : export Excel des réunions
        return redirect()->back()
            ->with('error', 'Fonctionnalité d\'export non implémentée.');
    }
}
