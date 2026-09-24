@extends('layouts.app')

@section('title', 'Gestion des Contrats - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête de la Gestion des Contrats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"> Gestion des Contrats</h4>
                    <p class="text-muted mb-0">Gérez tous les contrats de votre entreprise</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.employees.dashboard') }}">Employés</a>
                            </li>
                            <li class="breadcrumb-item active">Tableau de bord</li>
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
                    <a href="{{ route('company.contracts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouveau Contrat
                    </a>
                    <a href="{{ route('company.contracts.types') }}" class="btn btn-outline-info">
                        <i class="fas fa-tags me-1"></i>Types de Contrats
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('company.contracts.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employé</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Tous les employés</option>
                        @foreach(\Modules\Employees\Models\Employee::where('company_id', auth()->user()->company_id)->get() as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type de contrat</label>
                    <select name="type_id" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($contractTypes as $type)
                            <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="accept" {{ request('status') == 'accept' ? 'selected' : '' }}>Actif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Refusé</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('company.contracts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des contrats -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des contrats </h5>
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle" type="button" 
                        id="exportDropdown" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport"
                        aria-expanded="false">
                    <i class="fas fa-download me-1"></i>Exporter
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('company.contracts.index', array_merge(request()->all(), ['export' => 'excel'])) }}"><i class="fas fa-file-excel me-1 text-success"></i>Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('company.contracts.index', array_merge(request()->all(), ['export' => 'pdf'])) }}"><i class="fas fa-file-pdf me-1 text-danger"></i>PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" id="table_contract">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Contrat</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($contracts as $contract)
                        <tr>
                            <td>
                                @if($contract->employee)
                                    {{ $contract->employee->name }}
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </td>
                            <td>
                                @if($contract->type)
                                    <span class="badge bg-label-info">{{ $contract->type->name }}</span>
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($contract->end_date)
                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Indéterminé</span>
                                @endif
                            </td>
                            <td>
                                @switch($contract->status)
                                    @case('pending')
                                        <span class="badge bg-label-secondary">Brouillon</span>
                                        @break
                                    @case('accept')
                                        <span class="badge bg-label-success">Actif</span>
                                        @break
                                    @case('expired')
                                        <span class="badge bg-label-warning">Expiré</span>
                                        @break
                                    @case('reject')
                                        <span class="badge bg-label-danger">Résilié</span>
                                        @break
                                    @default
                                        <span class="badge bg-label-secondary">{{ $contract->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                {{-- Boutons directs plutôt qu'un menu déroulant : une action visible
                                     coûte un clic, contre deux auparavant. --}}
                                <div class="d-flex">
                                    <a href="{{ route('company.contracts.show', $contract->id) }}"
                                        class="btn btn-icon btn-sm btn-label-info me-1" data-bs-toggle="tooltip"
                                        title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('company.contracts.edit', $contract->id) }}"
                                        class="btn btn-icon btn-sm btn-label-warning me-1" data-bs-toggle="tooltip"
                                        title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-icon btn-sm btn-label-danger"
                                        data-bs-toggle="tooltip" title="Supprimer"
                                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?')) document.getElementById('delete-form-{{ $contract->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $contract->id }}"
                                        action="{{ route('company.contracts.destroy', $contract->id) }}" method="POST"
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('img/illustrations/empty.svg') }}" alt="Aucun contrat" width="120" class="mb-3">
                                    <h6 class="mb-1">Aucun contrat trouvé</h6>
                                    <p class="text-muted mb-3">Commencez par créer un nouveau contrat</p>
                                    <a href="{{ route('company.contracts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Nouveau Contrat
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if($contracts->count() > 0)
    // Initialisation de DataTable (seulement s'il y a des contrats)
    var table = $('#table_contract').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs avec Select2
        if ($.fn.select2) {
            $('select').select2({
                placeholder: 'Sélectionner une option',
                allowClear: true
            });
        }
    });
</script>   
@endpush