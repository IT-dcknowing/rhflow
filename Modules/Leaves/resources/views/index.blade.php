
@extends('layouts.app')

@section('title', 'Gestion des Congés - RH Flow')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.min.css') }}">
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    .filter-card {
        margin-bottom: 1.5rem;
    }
    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la Gestion des Congés -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏖️ Gestion des Congés
                        @if($periode && $periode->exercice) | Exercice :
                         {{ $periode->exercice->nom ?? 'N/A' }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}
                        {{ $periode->statut ? ucfirst($periode->statut) : 'Statut inconnu' }}
                        @endif
                    </h4>
                    <p class="text-muted mb-0">Gérez les demandes de congés et les absences des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Gestion des congés @if($periode) - Période : {{ $periode->nom ?? 'N/A' }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut ?? 'non défini') }}</span> @endif</li>
                        </ol>
                    </nav>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div class="d-flex gap-2"> 
                    @if($periode)
                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button" class="align-items-center btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la période
                    </a>
                    <a href="{{ route('company.leaves.calendar') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-1"></i>Vue Calendrier
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
                        <i class="fas fa-tags me-1"></i>Nouveau Type de Congé
                    </button>
                    <a href="{{ route('company.leaves.create') }}?periode_id={{$periode->id}}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouveau Congé
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($periode)
    <!-- Filtres -->
    <div class="card mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('company.leaves.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employé</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Tous les employés</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type de congé</label>
                    <select name="leave_type_id" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->title }}
                            </option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>En attente</option>
                        <option value="Approuvé" {{ request('status') == 'Approuvé' ? 'selected' : '' }}>Approuvé</option>
                        <option value="Rejeté" {{ request('status') == 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
                        <option value="Terminé" {{ request('status') == 'Terminé' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <input type="hidden" name="period" value="{{$periode->id}}">
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('company.leaves.index') }}?periode_id={{$periode->id}}" class="btn btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des congés -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des demandes de congé</h5>
            <span class="badge bg-label-primary">{{ $leaves->total() }} demande(s)</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="table_exercice">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employé</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Paie</th>
                        <th>Date de demande</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ $leave->employee && $leave->employee->avatar ? asset('storage/' . $leave->employee->avatar) : asset('img/avatars/1.png') }}" 
                                             alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $leave->employee ? $leave->employee->name : 'Employé supprimé' }}</h6>
                                        <small class="text-muted">{{ $leave->employee ? (\Auth::user()->employeeIdFormat($leave->employee->employee_id) ?? 'N/A') : 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $leave->leaveType ? $leave->leaveType->title : 'Type inconnu' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} 
                                <i class="fas fa-arrow-right mx-1"></i> 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $leave->total_leave_days }} {{ $leave->total_leave_days > 1 ? 'jours' : 'jour' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-label-warning',
                                        'Approuvé' => 'bg-label-success',
                                        'Rejeté' => 'bg-label-danger',
                                        'Terminé' => 'bg-label-secondary'
                                    ];
                                    $statusLabels = [
                                        'Pending' => 'En attente',
                                        'Approuvé' => 'Approuvé',
                                        'Rejeté' => 'Rejeté',
                                        'Terminé' => 'Terminé'
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$leave->status] ?? 'bg-label-secondary' }} status-badge">
                                    {{ $statusLabels[$leave->status] ?? $leave->status }}
                                </span>
                            </td>
                            <td>
                                @if($leave->is_active)
                                    <span class="badge bg-label-success status-badge">
                                        <i class="fas fa-toggle-on me-1"></i>Activé
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $leave->activatedPeriode->nom ?? 'Période inconnue' }}</small>
                                @elseif($leave->isActivable())
                                    <span class="badge bg-label-secondary status-badge">
                                        <i class="fas fa-toggle-off me-1"></i>Non activé
                                    </span>
                                @else
                                    {{-- Un congé non approuvé n'est pas activable : on dit pourquoi
                                         plutôt que de laisser la colonne muette. --}}
                                    <span class="badge bg-label-warning status-badge">
                                        <i class="fas fa-hourglass-half me-1"></i>À approuver
                                    </span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($leave->applied_on)->format('d/m/Y H:i') }}</td>
                            <td class="action-buttons">
                                <div class="d-flex">
                                    @if($leave->status === 'Pending')
                                        {{-- Sans approbation, aucun bouton d'activation n'apparaît :
                                             on rend l'étape accessible directement depuis la liste. --}}
                                        <a href="{{ route('company.leaves.show', $leave->id) }}"
                                           class="btn btn-icon btn-outline-info btn-sm me-1"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Traiter la demande (approuver / rejeter)">
                                            <i class="fas fa-gavel"></i>
                                        </a>
                                    @endif
                                    @if($leave->isActivable())
                                        @if($leave->is_active)
                                            <form action="{{ route('company.leaves.deactivate', $leave->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Désactiver ce congé ? L\'allocation sortira de la paie de la période. Les bulletins déjà générés ne sont pas modifiés.');">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-icon btn-outline-secondary btn-sm me-1"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Désactiver pour la paie">
                                                    <i class="fas fa-toggle-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                    class="btn btn-icon btn-outline-success btn-sm me-1 js-activate-leave"
                                                    data-url="{{ route('company.leaves.activateForm', $leave->id) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Activer pour la paie">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        @endif
                                    @endif
                                    <a href="{{ route('company.leaves.show', $leave->id) }}" 
                                       class="btn btn-icon btn-outline-primary btn-sm me-1"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('company.leaves.edit', $leave->id) }}" 
                                       class="btn btn-icon btn-outline-warning btn-sm me-1"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('company.leaves.destroy', $leave->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande de congé ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-icon btn-outline-danger btn-sm"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Supprimer"
                                                {{ !in_array($leave->status, ['Pending', 'Rejeté']) ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                    <h5 class="mb-1">Aucune demande de congé trouvée</h5>
                                    <p class="text-muted">Commencez par créer une nouvelle demande de congé</p>
                                    <a href="{{ route('company.leaves.create') }}?periode_id={{$periode->id}}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Nouvelle demande  
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
            <div class="card-footer">
                {{ $leaves->withQueryString()->links() }}
            </div>
        @endif
    </div>
    @else
    <div class="row mb-4">
        <!-- Périodes de l'exercice -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Exercice de paie</h5>
                    <span class="badge bg-label-primary">{{ $exercices->count() }} exercice(s)</span>
                </div>
                <div class="card-body">
                    @if($exercices->count() > 0)
                        <div class="timeline">
                            @foreach($exercices as $exercice)
                            <div class="timeline-item">
                                <div class="timeline-badge d-flex align-items-center">
                                    <h5 class="text-muted"> <i class="fas fa-calendar-alt me-2 text-primary"></i> Exercice : {{ $exercice->nom }}</h5>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}" class="text-primary">{{ $exercice->nom }}</a>
                                                </h5>
                                                <p class="mb-1">
                                                    <span class="text-muted">Du</span>
                                                    {{ $exercice->date_debut->format('d/m/Y') }}
                                                    <span class="text-muted">au</span>
                                                    {{ $exercice->date_fin->format('d/m/Y') }}
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge bg-label-{{ $exercice->statut === 'brouillon' ? 'warning' : ($exercice->statut === 'en_cours' ? 'info' : ($exercice->statut === 'cloture' ? 'success' : 'danger')) }}">
                                                        {{ ucfirst($exercice->statut) }}
                                                    </span>
                                                    <span class="ms-2 text-muted">
                                                        Nombre de périodes : {{ $exercice->periodes->count() }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button" id="exerciceActions">
                                                        <i class="fas fa-ellipsis-v"></i> 
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="exerciceActions">
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Voir les exercices
                                                    </a>    
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.exercices.edit', $exercice->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('company.paiesalaries.exercices.destroy', $exercice->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger delete-periode">
                                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                            </div>
                            <h5 class="mb-2">Aucun exercice</h5>
                            <p class="text-muted mb-4">Commencez par ajouter un exercice</p>
                            <a href="{{ route('company.paiesalaries.exercices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Ajouter un exercice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@if($periode)
<!-- Modal de confirmation de statut -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Changer le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Nouveau statut</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="approved">Approuver</option>
                            <option value="rejected">Rejeter</option>
                            <option value="cancelled">Annuler</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label for="remark" class="form-label">Commentaire (optionnel)</label>
                        <textarea name="remark" id="remark" rows="3" class="form-control" placeholder="Ajouter un commentaire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modale de création d'un type de congé (poste vers le CRUD existant du module Settings) -->
<div class="modal fade" id="createLeaveTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tags me-2"></i>Nouveau Type de Congé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('company.settings.leave-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-0">
                        <label for="leave_type_title" class="form-label">Nom du Type de Congé <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="leave_type_title"
                               name="title" value="{{ old('title') }}" required maxlength="255"
                               placeholder="Ex: Congé Annuel, Congé Maladie...">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">La durée d'un congé se calcule à partir de ses dates de début et de fin.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('company.settings.leave-types.index') }}" class="btn btn-link me-auto">
                        Gérer tous les types
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale d'activation d'un congé pour la paie (contenu chargé en AJAX) -->
<div class="modal fade" id="activateLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-toggle-on me-2"></i>Activer le congé pour la paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div id="activateLeaveModalBody">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>
<script>
    // Initialisation de DataTable
    var table = $('#table_exercice').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    });

    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion du modal de statut
        var statusModal = document.getElementById('statusModal');
        if (statusModal) {
            statusModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var leaveId = button.getAttribute('data-id');
                var form = document.getElementById('statusForm');
                form.action = '/company/leaves/' + leaveId + '/status';
            });
        }
    });
    
    // Activation d'un congé pour la paie : charge le formulaire (choix période + montant) dans la modale.
    // Délégué sur document car DataTable re-crée les lignes à chaque pagination.
    $(document).on('click', '.js-activate-leave', function () {
        var url = this.getAttribute('data-url');
        var body = document.getElementById('activateLeaveModalBody');
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('activateLeaveModal'));

        body.innerHTML = '<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
        modal.show();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) {
                if (!response.ok) { throw new Error('HTTP ' + response.status); }
                return response.text();
            })
            .then(function (html) { body.innerHTML = html; })
            .catch(function () {
                body.innerHTML = '<div class="modal-body"><div class="alert alert-danger mb-0">Impossible de charger le formulaire d\'activation.</div></div>';
            });
    });

    // La validation de storeLeaveType renvoie ici : on rouvre la modale sur l'erreur.
    @if($errors->has('title'))
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('createLeaveTypeModal');
            if (el) { new bootstrap.Modal(el).show(); }
        });
    @endif

    // Filtres avancés
    function toggleAdvancedFilters() {
        var advancedFilters = document.getElementById('advancedFilters');
        if (advancedFilters.style.display === 'none') {
            advancedFilters.style.display = 'block';
        } else {
            advancedFilters.style.display = 'none';
        }
    }
</script>
@endpush
