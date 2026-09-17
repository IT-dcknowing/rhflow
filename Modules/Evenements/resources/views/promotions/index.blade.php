@extends('layouts.app')

@section('title', 'Gestion des Promotions - RH Flow')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-approved {
            background-color: #198754;
            color: #fff;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: #fff;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        .action-btns .btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .filter-card {
            transition: all 0.3s ease;
        }

        .filter-card.collapsed .card-body {
            padding: 0;
            height: 0;
            overflow: hidden;
        }

        .filter-card .card-header {
            cursor: pointer;
        }

    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">📈 Gestion des Promotions</h4>
                        <p class="text-muted mb-0">Gérez les évolutions de carrière des employés</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="toggleFilters">
                            <i class="fas fa-filter me-1"></i>Filtres
                        </button>
                        <a href="{{ route('company.evenements.promotions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nouvelle Promotion
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres de recherche -->
        <div class="row mb-4 filter-card" id="filtersCard">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Filtres avancés</h5>
                        <button type="button" class="btn btn-sm btn-icon" id="toggleFiltersBtn">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.evenements.promotions.index') }}" method="GET" id="filtersForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Recherche</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Nom, prénom, poste..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En
                                            attente</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                            Approuvée</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            Rejetée</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="employee_id" class="form-label">Employé</label>
                                    <select class="form-select select2" id="employee_id" name="employee_id">
                                        <option value="">Tous les employés</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="department_id" class="form-label">Département</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">Tous les départements</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Date de début</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search me-1"></i>Appliquer
                                    </button>
                                    <a href="{{ route('company.evenements.promotions.index') }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des Promotions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 Aperçu des Promotions</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="statsDropdown" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statsDropdown">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('company.evenements.promotions.export') }}?{{ http_build_query(request()->all()) }}">
                                        <i class="fas fa-file-excel me-2"></i>Exporter en Excel
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Imprimer
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Total Promotions -->
                            <x-kpi icon="fas fa-trophy" color="primary" label="Total des Promotions"
                                :value="$stats['total'] ?? 0">
                                <x-slot:hint>
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> {{ $stats['monthly_increase'] ?? 0 }}% ce mois-ci
                                    </span>
                                </x-slot:hint>
                            </x-kpi>

                            <!-- Promotions cette année -->
                            <x-kpi icon="fas fa-calendar-check" color="info" label="Cette Année" sublabel="Promotions"
                                :value="$stats['this_year'] ?? 0" />

                            <!-- Promotions approuvées -->
                            <x-kpi icon="fas fa-check-circle" color="success" label="Approuvées" sublabel="Promotions"
                                :value="$approvedPromotions" />

                            <!-- Promotions rejetées -->
                            <x-kpi icon="fas fa-times-circle" color="danger" label="Rejetées" sublabel="Promotions"
                                :value="$rejectedPromotions" />

                            <!-- Augmentation moyenne -->
                            <x-kpi icon="fas fa-percentage" color="danger" label="Augmentation Moy."
                                sublabel="Sur les promotions" :value="($stats['avg_increase'] ?? '0') . '%'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Promotions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Liste des Promotions</h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-label-primary">{{ $promotions->total() }} promotion(s) trouvée(s)</span>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($promotions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>👤 Employé</th>
                                            <th>📊 Ancien Poste</th>
                                            <th>📈 Nouveau Poste</th>
                                            <th>📅 Date</th>
                                            <th>🏢 Département</th>
                                            <th>💼 Poste</th>
                                            <th>📈 Évolution</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($promotions as $promotion)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{ $promotion->employee->avatar_url ?? asset('images/avatars/default-avatar.png') }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $promotion->employee->full_name ?? 'N/A' }}</h6>
                                                            <small
                                                                class="text-muted">{{ $promotion->employee->employee_id ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $promotion->oldDesignation->name ?? 'N/A' }}</span>
                                                        <small
                                                            class="text-muted">{{ number_format($promotion->previous_salary, 0, ',', ' ') }}
                                                            {{ config('app.currency', 'FCFA') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $promotion->newDesignation->name ?? 'N/A' }}</span>
                                                        <small
                                                            class="text-success">{{ number_format($promotion->new_salary, 0, ',', ' ') }}
                                                            {{ config('app.currency', 'FCFA') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium">{{ $promotion->promotion_date->format('d/m/Y') }}</span>
                                                        <small
                                                            class="text-muted">{{ $promotion->promotion_date->diffForHumans() }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-info">
                                                        <i class="fas fa-building me-1"></i>
                                                        {{ $promotion->department->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium">{{ $promotion->new_position ?? 'N/A' }}</span>
                                                        @if($promotion->previous_position && $promotion->previous_position != $promotion->new_position)
                                                            <small class="text-muted">
                                                                <i class="fas fa-arrow-up text-success me-1"></i>
                                                                {{ $promotion->previous_position }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($promotion->salary_increase_percentage)
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress w-100 me-2" style="height: 6px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ min($promotion->salary_increase_percentage, 100) }}%"
                                                                    aria-valuenow="{{ $promotion->salary_increase_percentage }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <span class="text-nowrap">{{ $promotion->salary_increase_percentage }}%</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('company.evenements.promotions.show', $promotion->id) }}"
                                                            class="btn btn-icon btn-label-info" data-bs-toggle="tooltip"
                                                            title="Voir les détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('company.evenements.promotions.edit', $promotion->id) }}"
                                                            class="btn btn-icon btn-label-primary" data-bs-toggle="tooltip"
                                                            title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('company.evenements.promotions.destroy', $promotion->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-icon btn-label-danger"
                                                                data-bs-toggle="tooltip" title="Supprimer">
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

                            <!-- Pagination et infos -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center border-top pt-3">
                                <div class="mb-2 mb-md-0">
                                    <p class="mb-0 text-muted">
                                        Affichage de <span class="fw-semibold">{{ $promotions->firstItem() }}</span> à
                                        <span class="fw-semibold">{{ $promotions->lastItem() }}</span> sur
                                        <span class="fw-semibold">{{ $promotions->total() }}</span> promotion(s)
                                    </p>
                                </div>
                                <div>
                                    {{ $promotions->appends(request()->except('page'))->links() }}
                                </div>
                            </div>
                        @else
                            <!-- État Vide -->
                            <div class="text-center py-5">
                                <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px;">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-trophy fa-3x"></i>
                                    </div>
                                </div>
                                <h5 class="mb-2">Aucune promotion trouvée</h5>
                                <p class="text-muted mb-4">
                                    Aucune promotion ne correspond à vos critères de recherche.
                                    <a href="{{ route('company.evenements.promotions.create') }}" class="text-primary">Créer une
                                        nouvelle promotion</a>
                                    pour commencer.
                                </p>
                                <a href="{{ route('company.evenements.promotions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nouvelle Promotion
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div class="modal fade" id="deletePromotionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette promotion ? Cette action est irréversible.</p>
                        <p class="text-danger fw-bold">Attention : Si la promotion est déjà appliquée, cette action annulera
                            automatiquement ses effets sur l'employé concerné.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <form id="deletePromotionForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i>Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <!-- Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Afficher les messages flash avec SweetAlert2
            @if(session('success'))
                Swal.fire({
                    title: 'Succès',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Erreur',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

                // Initialisation des tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Gestion du formulaire de filtres
            $('#filtersForm').on('submit', function (e) {
                // Ne pas soumettre les champs vides
                $(this).find('select, input').each(function () {
                    if (!$(this).val() || $(this).val().length === 0) {
                        if (this.name) { // Vérifier si l'élément a un attribut name
                            $(this).prop('disabled', true);
                        }
                    }
                });
            });

            // Réinitialisation des filtres
            $('.btn-reset-filters').on('click', function () {
                window.location.href = "{{ route('company.evenements.promotions.index') }}";
            });

            // Initialisation du date range picker
            $('input[name="date_range"]').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Valider',
                    cancelLabel: 'Annuler',
                    fromLabel: 'Du',
                    toLabel: 'Au',
                    customRangeLabel: 'Personnalisé',
                    daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                    firstDay: 1
                },
                autoUpdateInput: false,
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'), 10) + 1,
                ranges: {
                    'Aujourd\'hui': [moment(), moment()],
                    'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 derniers jours': [moment().subtract(6, 'days'), moment()],
                    '30 derniers jours': [moment().subtract(29, 'days'), moment()],
                    'Ce mois-ci': [moment().startOf('month'), moment().endOf('month')],
                    'Mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('input[name="date_range"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('input[name="date_range"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            // Gestion du changement de nombre d'éléments par page
            $('#perPage').on('change', function () {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                window.location.href = url.toString();
            });

            // Gestion de la suppression avec confirmation
            const deleteButtons = document.querySelectorAll('.btn-delete-promotion');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = document.getElementById('deletePromotionForm');
                    form.action = this.dataset.url;
                    const modal = new bootstrap.Modal(document.getElementById('deletePromotionModal'));
                    modal.show();
                });
            });

            // Initialisation de Select2
            $('.select2').select2({
                placeholder: 'Sélectionner...',
                allowClear: true,
                width: '100%'
            });

            // Gestion de l'affichage/masquage des filtres
            const toggleFiltersBtn = document.getElementById('toggleFiltersBtn');
            const filtersCard = document.getElementById('filtersCard');

            if (toggleFiltersBtn && filtersCard) {
                toggleFiltersBtn.addEventListener('click', function () {
                    filtersCard.classList.toggle('collapsed');
                    const icon = this.querySelector('i');
                    if (filtersCard.classList.contains('collapsed')) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                });
            }
        });
    </script>
@endpush