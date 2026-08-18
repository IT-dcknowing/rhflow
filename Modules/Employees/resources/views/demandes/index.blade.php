@extends('layouts.app')

@section('page-title')
    {{ __(' Gestion des demandes ') }}
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Gestion des demandes
                    </h4>
                    <p class="text-muted mb-0">Traitez les requêtes des employés soumises via l'application mobile..</p>  
                    <nav aria-label="breadcrumb"> 
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.employees.dashboard') }}">Employés</a>
                            </li>
                            <li class="breadcrumb-item active">Demandes</li>
                        </ol>
                    </nav>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(\Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Liste des demandes par type</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-secondary{{-- session('status') == 'Approved' ? 'success' : (session('status') == 'Pending' ? 'info' : 'danger') --}}" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(count($demandesParType) > 0)
                        <!-- Onglets de navigation -->
                        <ul class="nav nav-tabs mb-3" id="demandesTabs" role="tablist">
                            @foreach($demandesParType as $typeDemande => $demandes)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="tab-{{ $typeDemande }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#content-{{ $typeDemande }}"
                                            type="button"
                                            role="tab"
                                            aria-controls="content-{{ $typeDemande }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ getCategorieLabel($typeDemande) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Contenu des onglets -->
                        <div class="tab-content" id="demandesTabsContent">
                            @foreach($demandesParType as $typeDemande => $demandes)
                                @php
                                    $cpte = 1;
                                @endphp
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="content-{{ $typeDemande }}"
                                    role="tabpanel"
                                    aria-labelledby="tab-{{ $typeDemande }}">

                                    <div class="type-demande-group">
                                        <div class="table-responsive">
                                            <table class="table table-sm" id="demandeEmployees">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Employés</th>
                                                        <th>Catégorie</th>
                                                        <th>Période</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($demandes as $demande)
                                                        <tr>
                                                            <td><span class="badge bg-info">{{ \Auth::user()->employeeIdFormat($demande->employee?->employee_id ?? 'N/A') }}</span></td>
                                                            <td>{{ $demande->employee->name }}</td>
                                                            <td>{{ getCategorieLabel($demande->categorie_demandes) }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($demande->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($demande->end_date)->format('d/m/Y') }}</td>
                                                            <td>
                                                                @php
                                                                    $statusClass = ' ';
                                                                    switch($demande->status) {
                                                                        case 'Pending':
                                                                            $statusClass = 'info';
                                                                            $statusText = 'En attente';
                                                                            break;
                                                                        case 'Approved':
                                                                            $statusClass = 'success';
                                                                            $statusText = 'Approuvée';
                                                                            break;
                                                                        case 'Rejected':
                                                                            $statusClass = 'danger';
                                                                            $statusText = 'Rejetée';
                                                                            break;
                                                                        default:
                                                                            $statusClass = 'info';
                                                                            $statusText = $demande->status;
                                                                    }
                                                                @endphp
                                                                <span class="badge bg-{{ $statusClass }}">
                                                                    {{ $statusText }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('company.employees.demandes.show', $demande->id) }}" class="btn-sm btn btn-primary" data-title="{{ __('Détails de la demande') }}">
                                                                        <i class="fa fa-eye me-1"></i> Voir
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucune demande trouvée.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@php
    function getCategorieLabel($categorie_id) {
        switch ($categorie_id) {
            case 'pret': return "Prêt";
            case 'absence': return "Absence";
            case 'autre': return "Autre";
            default: return "Demande";
        }
    }

    function getDemandeTypeLabel($demande_type_id) {
        switch ($demande_type_id) {
            case 'maladie': return "Maladie";
            case 'rdv_medical': return "Rendez-vous médical";
            case 'urgence_familiale': return "Urgence familiale";
            case 'formation': return "Formation";
            case 'pret_person': return "Prêt personnel";
            case 'avance_salaire': return "Avance sur salaire";
            case 'pret_immobilier': return "Prêt immobilier";
            case 'materiel': return "Matériel de travail";
            case 'formation_demande': return "Demande de formation";
            case 'amenagement_horaire': return "Aménagement horaire";
            case 'autre': return "Autre";
            default: return "Demande Type";
        }
    }
@endphp
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        var table = $('#demandeEmployees').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });
    });

    function validateDemande(id) {
        Swal.fire({
            title: 'Valider la demande',
            text: "Êtes-vous sûr de vouloir valider cette demande ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, valider',
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`{{ route('company.employees.demandes.validate', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Erreur lors de la validation')
                    }
                    return data
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        error.message || 'Erreur lors de la validation'
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Succès!',
                    text: 'La demande a été validée avec succès',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    willClose: () => {
                        location.reload()
                    }
                })
            }
        })
    }
    
    function rejectDemande(id) {
        Swal.fire({
            title: 'Rejeter la demande',
            text: "Êtes-vous sûr de vouloir rejeter cette demande ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, rejeter',
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`{{ route('company.employees.demandes.reject', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Erreur lors du rejet')
                    }
                    return data
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        error.message || 'Erreur lors du rejet'
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Rejetée!',
                    text: 'La demande a été rejetée avec succès',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    willClose: () => {
                        location.reload()
                    }
                })
            }
        })
    }
</script>
@endpush
