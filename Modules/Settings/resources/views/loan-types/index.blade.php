@extends('layouts.app')

@section('title', 'Types de Prêts - RH Flow')

@push('styles')
    <style>
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #566a7f;
            border: none;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
        }

        .bg-label-primary {
            background-color: rgba(105, 110, 255, 0.1) !important;
            color: #253e87 !important;
        }

        .bg-label-success {
            background-color: rgba(40, 200, 72, 0.1) !important;
            color: #28c848 !important;
        }

        .bg-label-danger {
            background-color: rgba(255, 73, 97, 0.1) !important;
            color: #ff4961 !important;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 2.5rem;
        }

        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">

                            Types de Prêts
                        </h4>
                        <p class="text-muted mb-0">Gérez les différents types de prêts disponibles pour les employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.settings.config') }}">Paramètres</a>
                                </li>
                                <li class="breadcrumb-item active">Types de Prêts</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.settings.loan-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nouveau Type
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('company.settings.loan-types.index') }}">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Rechercher un type de prêt..." value="{{ $search }}">
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">
                            {{ $loanTypes->total() }} type(s) trouvé(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des types de prêts -->
        <div class="card">
            <div class="card-body">
                @if($loanTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Montant Maximum</th>
                                    <th>Taux d'Intérêt</th>
                                    <th>Période de Remboursement</th>
                                    <th>Garantie Requise</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loanTypes as $loanType)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $loanType->name }}</h6>
                                                @if($loanType->description)
                                                    <small class="text-muted">{{ Str::limit($loanType->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $loanType->formatted_max_amount }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-primary">{{ $loanType->formatted_interest_rate }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $loanType->formatted_repayment_period }}</small>
                                        </td>
                                        <td>
                                            @if($loanType->requires_guarantor)
                                                <span class="badge bg-label-warning">
                                                    <i class="fas fa-shield-alt me-1"></i>Oui
                                                </span>
                                            @else
                                                <span class="badge bg-label-success">
                                                    <i class="fas fa-times me-1"></i>Non
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loanType->is_active)
                                                <span class="badge bg-label-success">
                                                    <i class="fas fa-check me-1"></i>Actif
                                                </span>
                                            @else
                                                <span class="badge bg-label-danger">
                                                    <i class="fas fa-times me-1"></i>Inactif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('company.settings.loan-types.show', $loanType->id) }}"
                                                    class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                    title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.settings.loan-types.edit', $loanType->id) }}"
                                                    class="btn btn-icon btn-outline-warning btn-sm" data-bs-toggle="tooltip"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form
                                                    action="{{ route('company.settings.loan-types.toggle-status', $loanType->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-icon btn-outline-{{ $loanType->is_active ? 'secondary' : 'success' }} btn-sm"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ $loanType->is_active ? 'Désactiver' : 'Activer' }}">
                                                        <i class="fas fa-{{ $loanType->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('company.settings.loan-types.destroy', $loanType->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de prêt ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-outline-danger btn-sm"
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

                    <!-- Pagination -->
                    @if($loanTypes->hasPages())
                        <div class="card-footer">
                            {{ $loanTypes->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                        <h5 class="mb-2">Aucun type de prêt trouvé</h5>
                        <p class="text-muted mb-4">
                            @if($search)
                                Aucun résultat pour "{{ $search }}"
                            @else
                                Commencez par créer votre premier type de prêt
                            @endif
                        </p>
                        <a href="{{ route('company.settings.loan-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer un Type de Prêt
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialiser les tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Gestion de la recherche en temps réel
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }
        });
    </script>
@endpush