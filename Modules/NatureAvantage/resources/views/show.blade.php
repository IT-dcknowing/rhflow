@extends('layouts.app')

@section('title', 'Détails de la nature d\'avantage')

@push('styles')
    <link rel="stylesheet" href="{{asset('vendor/libs/sweetalert2/sweetalert2.css')}}">
@endpush

@push('scripts')
    <script src="{{asset('vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Détails de la nature d'avantage
                            @if($avantage->periode) | Exercice :
                            {{ $avantage->periode->exercice->nom }} - <span class="badge bg-label-{{ $avantage->periode->statut === 'en_cours' ? 'success' : ($avantage->periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($avantage->periode->exercice->statut) }}
                            @endif
                        </h4>
                        <p class="text-muted mb-0">Gérez les avantages en natures des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item active">Gestion avantages en natures @if($avantage->periode) - Période : {{ $avantage->periode->nom }} - <span class="badge bg-label-{{ $avantage->periode->statut === 'en_cours' ? 'success' : ($avantage->periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($avantage->periode->statut) }}</span> @endif</li>
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
                        <a href="{{ route('company.avantages.edit', $avantage->id) }}" class="btn bg-label-primary me-2">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('company.avantages.index') }}?periode_id={{$avantage->periode_id}}" class="btn btn-label-info me-2">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $avantage->libelle }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-none bg-label-secondary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Informations générales</h6>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Employé:</div>
                                            <div class="col-7">{{ $avantage->employee->name }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Type d'avantage:</div>
                                            <div class="col-7">
                                                @if($avantage->type_avantage == "avantage_en_nature")
                                                    <span class="badge bg-label-success">Avantage en Nature</span>
                                                @elseif($avantage->type_avantage == "avantage_en_argent")
                                                    <span class="badge bg-label-info">Avantage en Argent</span>
                                                @elseif($avantage->type_avantage == "assurance_vie_complementaire")
                                                    <span class="badge bg-label-warning">Assurance-vie complémentaire</span>
                                                @else
                                                    <span class="badge bg-label-danger">Assurance santé</span>  
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Montant réel:</div>
                                            <div class="col-7">{{ number_format($avantage->amount_reel, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Montant imposable:</div>
                                            <div class="col-7">{{ number_format($avantage->amount, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Traitement:</div>
                                            <div class="col-7">{{ $avantage->traitement == 'mensuel' ? 'Mensuel' : 'Annuel' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-none bg-label-secondary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Activation & Statut</h6>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Statut:</div>
                                            <div class="col-7">
                                                @if($avantage->is_active)
                                                    <span class="badge bg-label-success">Actif</span>
                                                @else
                                                    <span class="badge bg-label-danger">Inactif</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Date de création:</div>
                                            <div class="col-7">{{ $avantage->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-5 text-muted">Dernière mise à jour:</div>
                                            <div class="col-7">{{ $avantage->updated_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            @if($avantage->is_active)
                                                <button type="button" class="btn btn-warning toggle-status-btn" data-id="{{ $avantage->id }}" data-action="suspend">
                                                    <i class="fas fa-pause me-1"></i> Suspendre
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-info toggle-status-btn" data-id="{{ $avantage->id }}" data-action="resume">
                                                    <i class="fas fa-play me-1"></i> Reprendre
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="card shadow-none bg-label-secondary">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Détails</h6>
                                        <p>{{ $avantage->details ?: 'Aucun détail fourni.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-danger" id="delete-avantage" data-id="{{ $avantage->id }}">
                                <i class="ti ti-trash me-1"></i> Supprimer cet avantage
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette nature d'avantage ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" action="{{ route('company.avantages.destroy', $avantage->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Gestion de la suppression
        $('#delete-avantage').on('click', function() {
            $('#deleteModal').modal('show');
        });

        // Gestion de la suspension/reprise
        $('.toggle-status-btn').on('click', function() {
            const avantageId = $(this).data('id');
            const action = $(this).data('action');
            const isSuspend = action === 'suspend';
            
            const title = isSuspend ? 'Suspendre cet avantage ?' : 'Reprendre cet avantage ?';
            const message = isSuspend 
                ? 'Êtes-vous sûr de vouloir suspendre cette nature d\'avantage ? L\'employé ne bénéficiera plus de cet avantage.' 
                : 'Êtes-vous sûr de vouloir reprendre cette nature d\'avantage ? L\'employé bénéficiera à nouveau de cet avantage.';
            const confirmButtonText = isSuspend ? 'Oui, suspendre' : 'Oui, reprendre';
            const confirmButtonColor = isSuspend ? '#ff9800' : '#17a2b8';

            Swal.fire({
                title: title,
                html: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Annuler',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Créer et soumettre un formulaire POST
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': '{{ route("company.avantages.toggle-status", ":id") }}'.replace(':id', avantageId)
                    });
                    
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));
                    
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'PUT'
                    }));
                    
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'status',
                        'value': isSuspend ? 'suspend' : 'resume'
                    }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush