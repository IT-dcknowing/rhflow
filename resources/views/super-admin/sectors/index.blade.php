@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Messages de succès/erreur -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ session('error') }}
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-server me-2"></i>
                        Gestion des Secteurs d'Activité
                    </h5>
                    <small class="text-muted">
                        Gérer vos secteurs d'activités : actifs, inactifs
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.sectors.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Nouveau Secteur
                    </a>
                    <button class="btn btn-outline-primary bg-label-primary" onclick="refreshTable()">
                        <i class="ti ti-refresh me-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card"> 
                    <div class="card-body">
                        <!-- Statistiques rapides -->
                        @isset($stats)
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['total'] ?? 0 }}</h3>
                                        <small>Total Secteurs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['active'] ?? 0 }}</h3>
                                        <small>Secteurs Actifs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['inactive'] ?? 0 }}</h3>
                                        <small>Secteurs Inactifs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-2"><i class="ti ti-crown text-warning me-1"></i>Top Secteurs</h6>
                                        <ul class="list-unstyled mb-0 small">
                                            @forelse(($topSectors ?? []) as $ts)
                                                <li class="d-flex justify-content-between">
                                                    <span>{{ $ts->name }}</span>
                                                    <span class="badge bg-label-primary">{{ $ts->companies_count }} entr.</span>
                                                </li>
                                            @empty
                                                <li class="text-muted">Aucune donnée</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endisset

                        <!-- Filtres et recherche -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un secteur...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="actif">Actifs</option>
                                    <option value="inactif">Inactifs</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-success w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-download me-2"></i>
                                        Exporter
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="exportData('xlsx')">
                                            <i class="ti ti-file-spreadsheet me-2"></i>
                                            Excel (XLSX)
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="exportData('pdf')">
                                            <i class="ti ti-file-text me-2"></i>
                                            PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des secteurs -->
                        <div class="table-responsive mb-3">
                            <table class="table table-hover" id="sectorsTable">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Secteur</th>
                                        <th>Description</th>
                                        <th>Entreprises</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sectors ?? [] as $sector)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $sector->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial bg-primary rounded-circle">{{ substr($sector->slug, 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $sector->name }}</h6>
                                                    <small class="text-muted">{{ $sector->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span title="{{ $sector->description }}">
                                                {{ Str::limit($sector->description, 50) }}
                                            </span>
                                        </td>
                                        <td align="center">
                                            <span class="badge bg-info">{{ $sector->companies_count ?? 0 }}</span>
                                        </td>
                                        <td align="center">
                                            @if($sector->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="ti ti-menu"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('super-admin.sectors.show', $sector) }}">
                                                        <i class="ti ti-eye me-2"></i>
                                                        Voir détails
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('super-admin.sectors.edit', $sector) }}">
                                                        <i class="ti ti-marker-alt me-2"></i>
                                                        Modifier
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @if($sector->is_active)
                                                        <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="toggleSector({{ $sector->id }}, false)">
                                                            <i class="ti ti-control-pause me-2"></i>
                                                            Désactiver
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item text-success" href="javascript:void(0)" onclick="toggleSector({{ $sector->id }}, true)">
                                                            <i class="ti ti-control-play me-2"></i>
                                                            Activer
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteSector({{ $sector->id }})">
                                                        <i class="ti ti-trash me-2"></i>
                                                        Supprimer
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="ti ti-building-off fs-1 text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">Aucun secteur trouvé</h5>
                                            <p class="text-muted">Créez votre premier secteur pour commencer.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="justify-content-center align-items-center">
                            @if(isset($sectors) && $sectors->hasPages())
                            <nav aria-label="Pagination">
                                <ul class="pagination pagination-sm justify-content-center">
                                    {{-- Lien vers la page précédente --}}
                                    <li class="page-item {{ $sectors->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $sectors->previousPageUrl() }}" {{ $sectors->onFirstPage() ? 'tabindex="-1"' : '' }}>Précédent</a>
                                    </li>

                                    {{-- Liens vers les pages numérotées --}}
                                    @foreach($sectors->getUrlRange(1, $sectors->lastPage()) as $page => $url)
                                        <li class="page-item {{ $page == $sectors->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Lien vers la page suivante --}}
                                    <li class="page-item {{ $sectors->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $sectors->nextPageUrl() }}" {{ $sectors->hasMorePages() ? '' : 'tabindex="-1"' }}>Suivant</a>
                                    </li>
                                </ul>
                            </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss notifications after 5 seconds
    const notifications = document.querySelectorAll('.alert-dismissible');
    notifications.forEach(function(notification) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }, 5000); // 5 seconds
    });

    // Recherche en temps réel
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase(); // Convertir le statut en minuscules
        const rows = document.querySelectorAll('#sectorsTable tbody tr');

        rows.forEach(row => {
            const sectorName = row.cells[1].textContent.toLowerCase();
            const description = row.cells[2].textContent.toLowerCase();
            const status = row.cells[4].textContent.toLowerCase().trim();
            const matchesSearch = sectorName.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue; // Comparer directement

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Gestion des secteurs
    window.editSector = function(sectorId) {
        // Ici vous pouvez implémenter l'édition
        console.log('Édition du secteur:', sectorId);
        alert('Fonctionnalité d\'édition à implémenter');
    };

    window.toggleSector = function(sectorId, activate) {
        const action = activate ? 'activer' : 'désactiver';
        if (confirm(`Êtes-vous sûr de vouloir ${action} ce secteur ?`)) {
            // Créer un formulaire temporaire avec le token CSRF
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("super-admin/sectors") }}/' + sectorId + '/toggle';

            // Ajouter le token CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Ajouter le method spoofing pour PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    };

    window.deleteSector = function(sectorId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ? Cette action est irréversible.')) {
            // Créer un formulaire temporaire avec le token CSRF
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("super-admin/sectors") }}/' + sectorId;

            // Ajouter le token CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Ajouter le method spoofing pour DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    };

    // Actualisation de la page
    window.refreshTable = function() {
        location.reload();
    };

    // Export des données
    window.exportData = function(format = 'xlsx') {
        // Récupérer les filtres actuels
        const searchTerm = document.getElementById('searchInput').value;
        const statusValue = document.getElementById('statusFilter').value;

        // Construire l'URL d'export avec les paramètres actuels
        let exportUrl = '{{ url("super-admin/sectors/export/xlsx") }}';
        if (format === 'pdf') {
            exportUrl = '{{ url("super-admin/sectors/export/pdf") }}';
        }

        const params = [];

        if (searchTerm) {
            params.push('search=' + encodeURIComponent(searchTerm));
        }

        if (statusValue) {
            params.push('status=' + encodeURIComponent(statusValue));
        }

        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }

        // Ouvrir dans une nouvelle fenêtre pour le téléchargement
        window.open(exportUrl, '_blank');
    };  
});
</script>
@endpush
