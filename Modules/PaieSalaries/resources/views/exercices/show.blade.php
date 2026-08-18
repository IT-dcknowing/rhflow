@extends('layouts.app')

@section('title', 'Détails de l\'exercice : ' . $exercice->nom)

@push('css')
    <style>
        .timeline {
            position: relative;
            padding-left: 2rem;
            border-left: 2px solid #e9ecef;
            margin-left: 1rem;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-badge {
            position: absolute;
            left: -2.2rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #7367f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .timeline-badge i {
            font-size: 0.8rem;
            color: #7367f0;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 {{ $exercice->nom }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.paiesalaries.exercices.index') }}">Exercices</a>
                            </li>
                            <li class="breadcrumb-item active">Détails</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('company.paiesalaries.periodes.create', $exercice->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle période
                    </a>
                    <a href="{{ route('company.paiesalaries.exercices.edit', $exercice->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de l'exercice -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informations</h5>
                    <span class="badge bg-label-{{ $exercice->statut === 'en_cours' ? 'success' : ($exercice->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($exercice->statut) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Code</h6>
                        <p class="mb-0">{{ $exercice->code }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Période</h6>
                        <p class="mb-0">
                            {{ $exercice->date_debut->format('d/m/Y') }} - {{ $exercice->date_fin->format('d/m/Y') }}
                            <span class="text-muted">({{ round($exercice->date_debut->diffInMonths($exercice->date_fin)) }} mois)</span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Créé par</h6>
                        <p class="mb-0">{{ $exercice->createdBy->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Date de création</h6>
                        <p class="mb-0">{{ $exercice->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($exercice->description)
                    <div class="mt-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p class="mb-0">{{ $exercice->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Périodes de l'exercice -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Périodes de paie</h5>
                    <span class="badge bg-label-primary">{{ $exercice->periodes->count() }} période(s)</span>
                </div>
                <div class="card-body">
                    @if($exercice->periodes->count() > 0)
                        <div class="timeline">
                            @foreach($exercice->periodes as $periode)
                            <div class="timeline-item">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" class="text-primary">{{ $periode->nom }}</a>
                                                </h5>
                                                <p class="mb-1">
                                                    <span class="text-muted">Du</span> 
                                                    {{ $periode->date_debut->format('d/m/Y') }} 
                                                    <span class="text-muted">au</span> 
                                                    {{ $periode->date_fin->format('d/m/Y') }}
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge bg-label-{{ $periode->statut === 'validee' ? 'success' : ($periode->statut === 'payee' ? 'info' : ($periode->statut === 'annulee' ? 'danger' : 'warning')) }}">
                                                        {{ ucfirst($periode->statut) }}
                                                    </span>
                                                    <span class="ms-2 text-muted">
                                                        Paiement prévu le {{ $periode->date_paiement->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button" id="periodeActions{{ $periode->id }}" 
                                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="periodeActions{{ $periode->id }}">
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Voir les détails
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.periodes.edit', $periode->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('company.paiesalaries.periodes.destroy', $periode->id) }}" 
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
                            <h5 class="mb-2">Aucune période de paie</h5>
                            <p class="text-muted mb-4">Commencez par ajouter une période de paie pour cet exercice</p>
                            <a href="{{ route('company.paiesalaries.periodes.create', $exercice->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Ajouter une période
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Confirmation de suppression d'une période
        $('.delete-periode').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush