@extends('layouts.super-admin')

@section('content')
<div class="row">
    <!-- Informations principales du secteur -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial bg-primary rounded-circle fs-4">{{ substr($sector->name, 0, 1) }}</span>
                    </div>
                    <div class="">
                        <h5 class="card-title text-primary m-0">{{ $sector->name }}</h5>
                        <p class="text-muted text-primary mb-0">Secteur d'activité #{{ $sector->id }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.sectors.edit', $sector) }}" class="btn btn-primary">
                        <i class="ti ti-marker-alt me-2"></i>
                        Modifier
                    </a>
                    <a href="{{ route('super-admin.sectors.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                @if($sector->description)
                                <div class="mb-4">
                                    <h6 class="section-title">Description</h6>
                                    <p class="text-muted">{{ $sector->description }}</p>
                                </div>
                                @endif

                                <!-- Statistiques -->
                                <div class="row g-4 mb-4">
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial bg-info rounded-circle">
                                                    <i class="ti ti-bag text-white"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $sector->companies_count }}</h6>
                                                <small class="text-muted">Entreprises</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial {{ $sector->is_active ? 'bg-success' : 'bg-danger' }} rounded-circle">
                                                    <i class="ti ti-{{ $sector->is_active ? 'check' : 'x' }} text-white"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $sector->is_active ? 'Actif' : 'Inactif' }}</h6>
                                                <small class="text-muted">Statut</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial bg-secondary rounded-circle">
                                                    <i class="ti ti-list text-white"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $sector->sort_order }}</h6>
                                                <small class="text-muted">Ordre</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial bg-primary rounded-circle">
                                                    <i class="ti ti-tag text-white"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-monospace">{{ $sector->slug }}</h6>
                                                <small class="text-muted">Slug</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border bg-dark text-white">
                                    <div class="card-body">
                                        <h6 class="section-title text-white">
                                            <i class="ti ti-calendar me-2"></i>
                                            Informations temporelles
                                        </h6>

                                        <div class="mb-3">
                                            <strong>Créé le:</strong><br>
                                            <span>{{ $sector->created_at->format('d/m/Y à H:i:s') }}</span>
                                        </div>

                                        @if($sector->updated_at != $sector->created_at)
                                        <div class="mb-3">
                                            <strong>Dernière modification:</strong><br>
                                            <span>{{ $sector->updated_at->format('d/m/Y à H:i:s') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des entreprises dans ce secteur -->
    @if($sector->companies_count > 0)
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">
                    <i class="ti ti-building me-2"></i>
                    Entreprises dans ce secteur ({{ $sector->companies_count }})
                </h5>
            </div>
            <hr>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Entreprise</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Ville</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sector->companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial bg-primary rounded-circle">
                                                {{ substr($company->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $company->name }}</h6>
                                            @if($company->industry_label)
                                            <small class="text-muted">{{ $company->industry_label }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                        {{ $company->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($company->phone)
                                    <a href="tel:{{ $company->phone }}" class="text-decoration-none">
                                        {{ $company->phone }}
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $company->city ?? '-' }}</td>
                                <td>
                                    <small>{{ $company->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('super-admin.enterprises.show', $company) }}">
                                                <i class="ti ti-eye me-2"></i>
                                                Voir détails
                                            </a>
                                            <a class="dropdown-item" href="{{ route('super-admin.enterprises.edit', $company) }}">
                                                <i class="ti ti-edit me-2"></i>
                                                Modifier
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="ti ti-building-off fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Aucune entreprise trouvée dans ce secteur.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .section-title {
        color: #263d88;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .avatar {
        width: 2.5rem;
        height: 2.5rem;
    }

    .avatar-lg {
        width: 4rem;
        height: 4rem;
        font-size: 1.5rem;
    }

    .avatar-sm {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(38, 61, 136, 0.15);
        border: none;
    }

    .card.border {
        border: 1px solid #e9ecef !important;
    }

    .card-title {
        color: #495057;
        font-size: 1rem;
    }

    .font-monospace {
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }
</style>
@endpush
