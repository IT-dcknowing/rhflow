<?php

namespace Modules\Evenements\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Modules\Evenements\Models\Event;
use Modules\Evenements\Models\EventType;
use Modules\Evenements\Models\EventParticipant;
use App\Http\Controllers\Controller;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeDay;
use App\Models\Branch;
use App\Models\Department;
use App\Notifications\EventInvitation;
use App\Notifications\EventReminder;
use Illuminate\Support\Facades\Notification;

class EventController extends Controller
{
    protected $googleService;
    protected $outlookService;
    
    public function __construct()
    {      
        // Initialisation des services de calendrier (à implémenter)
        // $this->googleService = new GoogleCalendarService();
        // $this->outlookService = new OutlookCalendarService();
    }

    public function index(Request $request)
    {
        $query = Event::with(['branch', 'type', 'participants'])
            ->where('company_id', auth()->user()->company_id);
            
        // Filtres
        if ($request->has('type') && $request->type) {
            $query->where('event_type_id', $request->type);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->where('start_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('end_date', '<=', $request->end_date);
        }
        
        $events = $query->latest()->paginate(15);
        $eventTypes = EventType::forCompany(auth()->user()->company_id)->get();
        
        return view('evenements::events.index', compact('events', 'eventTypes'));
    }

    public function calendar(Request $request)
    {
        $view = $request->get('view', 'dayGridMonth');
        $eventTypes = EventType::forCompany(auth()->user()->company_id)->get();
        
        // Si c'est une requête AJAX pour les événements du calendrier
        if ($request->ajax()) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
            
            $events = Event::with(['type', 'participants'])
                ->where('company_id', auth()->user()->company_id)
                ->where('status', 'published')
                ->where(function($query) use ($start, $end) {
                    $query->whereBetween('start_date', [$start, $end])
                          ->orWhereBetween('end_date', [$start, $end])
                          ->orWhere(function($q) use ($start, $end) {
                              $q->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                          });
                })
                ->get()
                ->map(function($event) {
                    return $event->getCalendarEventData();
                });
                
            return response()->json($events);
        }
        
        return view('evenements::events.calendar', compact('eventTypes', 'view'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $branches = Branch::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $eventTypes = EventType::forCompany($companyId)->get();
        
        // Récupérer les employés avec leurs départements
        $employees = Employee::with('department')
            ->where('company_id', $companyId)
            ->get()
            ->groupBy('department.name');
            
        return view('evenements::events.create', compact(
            'branches', 
            'employees', 
            'eventTypes',
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);
        $validated['company_id'] = auth()->user()->company_id;
        
        // Gestion des dates et récurrence
        $validated['start_date'] = Carbon::parse($validated['start_date']);
        $validated['end_date'] = !empty($validated['end_date']) 
            ? Carbon::parse($validated['end_date']) 
            : null;
            
        // Si c'est un événement sur toute la journée
        if ($request->has('all_day')) {
            $validated['end_date'] = $validated['start_date']->copy()->endOfDay();
        }
        
        // Création de l'événement
        $event = Event::create($validated);
        
        // Gestion des participants
        $this->syncParticipants($event, $request);
        
        // Envoi des invitations si demandé
        if ($request->has('send_invitations') && $request->send_invitations) {
            $this->sendInvitations($event);
        }
        
        // Synchronisation avec les calendriers externes
        $this->syncWithExternalCalendars($event, 'create');
        
        return redirect()
            ->route('company.evenements.show', $event->id)
            ->with('success', 'Événement créé avec succès.');
    }

    public function show(Event $event)
    {
        $event->load(['branch', 'employees', 'participants.participant', 'type']);
        
        // Vérifier si l'utilisateur est un participant
        $userParticipation = null;
        if (auth()->check()) {
            $userParticipation = $event->participants()
                ->where('participant_id', auth()->id())
                ->where('participant_type', get_class(auth()->user()))
                ->first();
        }
        
        return view('evenements::events.show', compact('event', 'userParticipation'));
    }
    public function edit(Event $event)
    {
        
        $companyId = auth()->user()->company_id;
        $branches = Branch::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $eventTypes = EventType::forCompany($companyId)->get();
        
        // Récupérer les employés avec leurs départements
        $employees = Employee::with('department')
            ->where('company_id', $companyId)
            ->get()
            ->groupBy('department.name');
            
        // Participants existants
        $selectedParticipants = [
            'employees' => $event->participants()
                ->where('participant_type', 'App\\Models\\User')
                ->pluck('participant_id')
                ->toArray(),
            'departments' => $event->participants()
                ->where('participant_type', 'App\\Models\\Department')
                ->pluck('participant_id')
                ->toArray(),
            'external' => $event->participants()
                ->where('participant_type', 'App\\Models\\ExternalContact')
                ->get()
                ->map(function($p) {
                    return [
                        'id' => $p->participant_id,
                        'email' => $p->email,
                        'name' => $p->name
                    ];
                })
                ->toArray()
        ];
        
        return view('evenements::events.edit', compact(
            'event',
            'branches',
            'departments',
            'employees',
            'eventTypes',
            'selectedParticipants'
        ));
        
        return view('evenements::events.edit', compact(
            'event', 'branches', 'employees', 'selectedEmployees'
        ));
    }

    public function update(Request $request, Event $event)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'color' => 'required|string',
            'description' => 'nullable|string',
            'employees' => 'required|array',
            'employees.*' => 'exists:employee_months,id',
        ]);

        $event->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'branch_id' => $validated['branch_id'],
            'color' => $validated['color'],
            'description' => $validated['description'],
        ]);

        $event->employees()->syncWithPivotValues(
            $validated['employees'], 
            ['company_id' => auth()->user()->company_id]
        );

        return redirect()->route('events.calendar')
            ->with('success', 'Événement mis à jour avec succès.');
    }

    public function cancel(Event $event)
    {
        $event->update(['status' => 'cancelled']);

        return redirect()
            ->route('company.evenements.events.index')
            ->with('success', 'Événement annulé avec succès.');
    }

    public function destroy(Event $event)
    {
        
        $event->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Événement supprimé avec succès.'
        ]);
    }

    public function getEvents()
    {
        $events = Event::where('company_id', auth()->user()->company_id)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d'),
                    'end' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                    'color' => $event->color,
                    'description' => $event->description,
                    'url' => route('events.show', $event->id)
                ];
            });

        return response()->json($events);
    }
}
