@extends('layouts.app')

@section('title', 'Gestion des Récompenses - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Récompenses</h4>
                    <p class="text-muted mb-0">Gérez les récompenses et distinctions des employés</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div>
                    <a href="{{ route('company.evenements.awards.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle Récompense
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des Récompenses -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Aperçu des Récompenses</h5>
                    <span class="badge bg-label-primary">Vue d'ensemble</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Total Récompenses -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <i class="fas fa-award fa-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Total</h6>
                                        <p class="mb-0 fw-bold fs-4">{{ $stats['total'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Récompenses ce mois-ci -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="fas fa-calendar-alt fa-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Ce Mois</h6>
                                        <p class="mb-0 fw-bold fs-4">{{ $stats['this_month'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Types de récompenses -->
                        <div class="col-xl-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Répartition par Type</h6>
                                        <span class="badge bg-label-primary">Statistiques</span>
                                    </div>
                                    <div class="d-flex justify-content-around text-center">
                                        @foreach($awardTypes as $type)
                                        <div>
                                            <div class="avatar avatar-md mb-2">
                                                <div class="avatar-initial bg-label-{{ $type['color'] }} rounded">
                                                    <i class="{{ $type['icon'] }} fa-lg"></i>
                                                </div>
                                            </div>
                                            <h6 class="mb-0">{{ $type['count'] }}</h6>
                                            <small class="text-muted">{{ $type['name'] }}</small>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Récompenses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 Liste des Récompenses</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-label-primary">{{ $awards->total() }} récompenses</span>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($awards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>🏆 Récompense</th>
                                        <th>👤 Bénéficiaire</th>
                                        <th>📅 Date</th>
                                        <th>🏢 Département</th>
                                        <th>🏷️ Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($awards as $award)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-initial bg-label-{{ $award->type_color }} rounded">
                                                        <i class="{{ $award->type_icon }}"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $award->title }}</h6>
                                                    <small class="text-muted">{{ $award->description ? Str::limit($award->description, 30) : 'Aucune description' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="{{ $award->employee->avatar_url ?? asset('images/avatars/default-avatar.png') }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $award->employee->full_name ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $award->employee->position ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $award->award_date->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $award->award_date->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $award->employee->department->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $award->type_color }}">
                                                <i class="{{ $award->type_icon }} me-1"></i>
                                                {{ $award->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('company.evenements.awards.show', $award->id) }}" 
                                                   class="btn btn-icon btn-label-info"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.evenements.awards.edit', $award->id) }}" 
                                                   class="btn btn-icon btn-label-warning"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('company.evenements.awards.destroy', $award->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-icon btn-label-danger"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Supprimer"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette récompense ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="mb-0">
                                    Affichage de <strong>{{ $awards->firstItem() }}</strong> à 
                                    <strong>{{ $awards->lastItem() }}</strong> sur 
                                    <strong>{{ $awards->total() }}</strong> récompenses
                                </p>
                            </div>
                            <div>
                                {{ $awards->links() }}
                            </div>
                        </div>
                    @else
                        <!-- État Vide -->
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px;">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-award fa-3x"></i>
                                </div>
                            </div>
                            <h5 class="mb-2">Aucune récompense trouvée</h5>
                            <p class="text-muted mb-4">
                                Commencez par ajouter votre première récompense pour récompenser vos employés.
                            </p>
                            <a href="{{ route('company.evenements.awards.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Ajouter une récompense
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'Ajout Rapide -->
<div class="modal fade" id="quickAddAwardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Récompense Rapide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAwardForm" action="{{ route('company.evenements.awards.quick-create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employé</label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="" selected disabled>Sélectionner un employé</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->full_name }} ({{ $employee->matricule }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="award_type" class="form-label">Type de Récompense</label>
                        <select class="form-select" id="award_type" name="award_type" required>
                            <option value="" selected disabled>Sélectionner un type</option>
                            @foreach($awardTypes as $type => $details)
                                <option value="{{ $type }}" 
                                        data-icon="{{ $details['icon'] }}"
                                        data-color="{{ $details['color'] }}">
                                    {{ $details['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="award_date" class="form-label">Date d'attribution</label>
                        <input type="date" class="form-control" id="award_date" name="award_date" 
                               value="{{ now()->format('Y-m-d') }}" required>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Filtrage en temps réel
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }

        // Gestion du formulaire rapide
        const quickAwardForm = document.getElementById('quickAwardForm');
        if (quickAwardForm) {
            quickAwardForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Ici, vous pouvez ajouter la logique AJAX pour soumettre le formulaire
                // et gérer la réponse (affichage des messages de succès/erreur)
                this.submit();
            });
        }
    });
</script>
@endpush
@endsection
