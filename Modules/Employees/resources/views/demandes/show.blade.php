@php
    if (!function_exists('getCategorieLabel')) {
        function getCategorieLabel($categorie_id) {
            switch ($categorie_id) {
                case 'pret': return "Prêt";
                case 'absence': return "Absence";
                case 'autre': return "Autre";
                default: return "Demande";
            }
        }
    }

    if (!function_exists('getDemandeTypeLabel')) {
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
    }
    $statusClass = '';
    switch($demande->status) {
        case 'Pending':
            $statusClass = 'warning';
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
@extends('layouts.app')

@section('page-title')
    {{ __(' Détails de la demande ') }}
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 affichage des demandes
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
                <div class="card-header">
                    <h5 class="mb-0">Détails de la demande #{{ $demande->id }}</h5>
                </div>

                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informations générales</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><strong class="text-info">#DEM-{{ $demande->id }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Catégorie:</strong></td>
                                    <td>
                                        @switch($demande->categorie_demandes)
                                            @case('pret')
                                                <span class="badge bg-primary">Prêt</span>
                                                @break
                                            @case('absence')
                                                <span class="badge bg-warning">Absence</span>
                                                @break
                                            @case('autre')
                                                <span class="badge bg-secondary">Autre</span>
                                                @break
                                            @default
                                                <span class="badge bg-info">{{ $demande->categorie_demandes }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>{{ getDemandeTypeLabel($demande->demande_types) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut:</strong></td>
                                    <td>
                                        @switch($demande->status)
                                            @case('Pending')
                                                <span class="badge bg-info">En attente</span>
                                                @break
                                            @case('Approved')
                                                <span class="badge bg-success">Approuvée</span>
                                                @break
                                            @case('Rejected')
                                                <span class="badge bg-danger">Rejetée</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $demande->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date de création:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Employé concerné</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nom:</strong></td>
                                    <td>{{ $demande->employee?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $demande->employee?->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Matricule:</strong></td>
                                    <td>{{ \Auth::user()->employeeIdFormat($demande->employee?->employee_id ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Periode:</strong></td>
                                    @if($demande->periode_id)
                                        <td>{{ $demande->periode->nom }} {{ $demande->periode->exercice->nom }}</td>
                                    @else
                                        <td id="td-periode">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mx-2">
                                                    <div class="btn-box">
                                                        <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                                            @foreach ($exercices as $exercice)
                                                                <option value="{{ $exercice->id }}">
                                                                    {{ $exercice->nom }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mx-2">
                                                    <div class="btn-box">
                                                        <select name="periodes" id="periodes" class="form-select">
                                                        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Détails spécifiques -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Détails de la demande</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Période:</strong></td>
                                    <td>
                                        Du {{ \Carbon\Carbon::parse($demande->start_date)->format('d/m/Y') }} 
                                        au {{ \Carbon\Carbon::parse($demande->end_date)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                
                                @if($demande->montant)
                                    <tr>
                                        <td><strong>Montant:</strong></td>
                                        <td>{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <td><strong>Motif:</strong></td>
                                    <td>{{ $demande->demande_reason }}</td>
                                </tr>
                                
                                @if($demande->file_path)
                                    <tr>
                                        <td><strong>Document:</strong></td>
                                        <td>
                                            <a href="{{ asset($demande->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-file"></i> Voir le document
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('company.employees.demandes.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left me-1"></i> Retour à la liste
                                </a>
                                
                                @if($demande->status === 'Pending')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm me-2" onclick="validateDemande('{{ $demande->id }}')">
                                            <i class="fa fa-check me-1"></i> Valider
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="rejectDemande('{{ $demande->id }}')">
                                            <i class="fa fa-times me-1"></i> Rejeter
                                        </button>
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
@endsection

@push('scripts')
<script>
    // Charger les périodes quand on change d'exercice
    function callperiodepaie() {
        var exerciceId = $("#exercice").val();
        if (exerciceId) {
            $.ajax({
                url: '{{ route("company.declarations.get_periodes") }}',
                type: 'GET',
                data: { exercice: exerciceId },
                success: function(data) {
                    var periodesSelect = $("#periodes");
                    periodesSelect.empty();
                    
                    $.each(data, function(key, periode) {
                        var dateDebut = new Date(periode.date_debut);
                        var dateFin = new Date(periode.date_fin);
                        var libelle = 'Période du ' + dateDebut.toLocaleDateString('fr-FR') + ' au ' + dateFin.toLocaleDateString('fr-FR');
                        
                        periodesSelect.append('<option value="' + periode.id + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                    });
                },
                error: function() {
                    console.error('Erreur lors du chargement des périodes');
                }
            });
        }else{
            var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible") }}</div>';
            document.getElementById('td-periode').innerHTML = tableHTML;
        }
    }

    function validateDemande(id) {
        // Vérifier si une période est sélectionnée
        var periodeId = $("#periodes").val();
        if (!periodeId && !{{ $demande->periode_id ? 'true' : 'false' }}) {
            Swal.fire({
                title: 'Période requise',
                text: 'Veuillez sélectionner une période avant de valider la demande',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
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
                    },
                    body: JSON.stringify({
                        periode_id: periodeId || {{ $demande->periode_id ?? 'null' }}
                    })
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
                        window.location.href = '{{ route('company.employees.demandes.index') }}'
                    }
                })
            }
        })
    }
    
    function rejectDemande(id) {
        // Vérifier si une période est sélectionnée
        var periodeId = $("#periodes").val();
        if (!periodeId && !{{ $demande->periode_id ? 'true' : 'false' }}) {
            Swal.fire({
                title: 'Période requise',
                text: 'Veuillez sélectionner une période avant de rejeter la demande',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
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
                    },
                    body: JSON.stringify({
                        periode_id: periodeId || {{ $demande->periode_id ?? 'null' }}
                    })
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
                        window.location.href = '{{ route('company.employees.demandes.index') }}'
                    }
                })
            }
        })
    }
    
    // Initialiser la fonction au chargement de la page
    $(document).ready(function() {
        callperiodepaie();
    });
</script>
@endpush
