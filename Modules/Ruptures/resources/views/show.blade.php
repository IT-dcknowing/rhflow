@extends('layouts.app')

@section('title', 'Détails de la rupture')

@push('styles')
<link rel="stylesheet" href="{{asset('vendor/libs/dropzone/dropzone.css')}}" />
<style>
    .watermark {
        position: relative; /* Permet de positionner le texte du filigrane */
        width: 100%;
        height: 100%;
    }

    .watermark-text {
        position: absolute; /* Positionnement absolu par rapport au conteneur parent */
        top: 30%;
        left: 25%;
        transform: rotate(-40deg); /* Pour centrer le texte horizontalement et verticalement */
        font-size: 10em; /* Taille de la police du filigrane */
        opacity: 0.5; /* Opacité du filigrane */
        pointer-events: none; /* Empêcher le clic sur le filigrane */
    }

    .draft {
        color: orangered; /* Couleur du filigrane pour "Brouillon" */
    }

    .validated {
        color: green; /* Couleur du filigrane pour "Validé" */
    }

    .rejected {
        color: red; /* Couleur du filigrane pour "Rejeté" */
    }

    .completed {
        color: blue; /* Couleur du filigrane pour "Terminée" */
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">❌ Détails de la rupture</h4>
                    <p class="text-muted mb-0">Gérez les ruptures de contrat et sanctions des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Détails de la rupture</li>
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
                    <a href="{{ route('company.ruptures.index') }}?periode_id={{$rupture->periode_id}}" class="btn bg-label-primary">
                        <i class="ti ti-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">        
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rupture #{{ $rupture->id }}</h5>
                    <div class="card-text">
                        @if($rupture->status == 'pending')
                            <span class="badge bg-label-warning">En attente</span>
                        @elseif($rupture->status == 'approved')
                            <span class="badge bg-label-success">Approuvée</span>
                        @elseif($rupture->status == 'rejected')
                            <span class="badge bg-label-danger">Rejetée</span>
                        @elseif($rupture->status == 'completed')
                            <span class="badge bg-label-info">Terminée</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Vérification du statut pour déterminer le filigrane -->
                    <div class="watermark">
                        <!-- Condition pour afficher le filigrane "Brouillon" ou "Validé" -->
                        @if($rupture->status == 'pending')
                            <div class="watermark-text draft">Brouillon</div>
                        @elseif($rupture->status == 'approved')   
                            <div class="watermark-text validated">Validé</div>
                        @elseif($rupture->status == 'rejected')   
                            <div class="watermark-text rejected">Rejeté</div>
                        @elseif($rupture->status == 'completed')   
                            <div class="watermark-text completed">Terminée</div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-md me-2">
                                        <img src="{{ $rupture->employee->avatar ? asset('storage/'.$rupture->employee->avatar) : asset('img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rupture->employee->name }}</h6>
                                        <small class="text-muted">{{ $rupture->employee->designation->name }}</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong>Type de rupture:</strong> {{ $rupture->ruptureType->name }}
                                </div>
                                <div class="mb-3">
                                    <strong>Date de demande:</strong> {{ \Carbon\Carbon::parse($rupture->notice_date)->locale('fr')->isoFormat('DD/MM/YYYY') }}
                                </div>
                                <div class="mb-3">
                                    <strong>Date effective:</strong> {{ \Carbon\Carbon::parse($rupture->termination_date)->locale('fr')->isoFormat('DD/MM/YYYY') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Période de préavis:</strong> {{ (int) \Carbon\Carbon::parse($rupture->notice_date)->diffInDays($rupture->termination_date) ?? 'N/A' }} jours
                                </div>
                                <div class="mb-3">
                                    <strong>Montant de l'indemnité:</strong> {{ $rupture->solde ? number_format($rupture->solde, 0, ',', ' ') . ' XOF' : 'N/A' }}
                                </div>
                                <div class="mb-3">
                                    <strong>Créé par:</strong> {{ $rupture->company->name ?? 'Système' }}
                                </div>
                                <div class="mb-3">
                                    <strong>Date de création:</strong> {{ $rupture->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-header">Droits de la rupture</h5>
                                <div class="card-body">
                                    <table class="table table-sm" id="pc-dt-simple">
                                        <tr role="row">
                                            <th colspan="3"><strong class="text-danger">I- Les droits légaux :</strong></th>
                                        </tr>
                                        <tr role="row">
                                            <th>{{ __('Indemnité compensatrice de Gratification') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->indem_comp,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Indemnité compensatrice de congé 2021') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->indem_comp_cong,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr role="row">
                                            <th colspan="3"><strong class="text-danger">II- Les droits spécifiques :</strong></th>
                                        </tr>
                                        <tr>  
                                            <th>{{ __('Indemnité de préavis') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->imdem_prea,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Aggravation de l\'indemnité compensatrice de préavis') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->indem_licence,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Indemnité de licenciement') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->aggravation,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Dommages et intérêts') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->dom_inter,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>Total Droits Brut :	 </th>
                                            <td align="right" colspan="2"><strong>{{number_format(($rupture->indem_comp+$rupture->indem_comp_cong+$rupture->imdem_prea+$rupture->indem_licence+$rupture->aggravation+$rupture->dom_inter),'0','.',' ')}} FCFA</strong></td>
                                        </tr>
                                        <tr role="row">
                                            <th colspan="3"><strong class="text-danger">III- Les retenues :</strong></th>
                                        </tr>
                                        <tr>
                                            <th>{{ __('CNPS') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->amount_cnps,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('ITS') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->amount_its,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Prêts') }} : </th>
                                            <td align="right" colspan="2">{{ number_format($rupture->amount_loan,'0','.',' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>Total Droits Retenues :	</th>
                                            <td align="right" colspan="2"><strong>{{number_format(($rupture->amount_cnps+$rupture->amount_its+$rupture->amount_loan),'0','.',' ')}} FCFA</strong></strong></td>
                                        </tr>
                                        @php
                                        $total = 0;
                                        $total = round(($rupture->dom_inter+$rupture->aggravation+$rupture->indem_licence+$rupture->imdem_prea+$rupture->indem_comp_cong+$rupture->indem_comp)-($rupture->amount_cnps+$rupture->amount_its+$rupture->amount_loan));
                                        @endphp
                                        <tr>
                                            <th>{{ __('Total Droits') }} : </th>
                                            <td align="right" colspan="2"><strong>{{ number_format($total,'0','.',' ') }} FCFA</strong></td>
                                        </tr>
                                        @if($rupture->status == 'approved' || $rupture->status == 'completed')
                                            <tr role="row">
                                                <th colspan="3"><strong class="text-danger">IV- Les documents :</strong></th>
                                            </tr>
                                            <tr>
                                                <td align="center" width="35%">
                                                    <div class="dt-buttons btn-group flex-wrap">
                                                        <div class="btn-group">
                                                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti ti-chevron-down me-1"></i>{{ __('Decompte des droits de rupture') }}
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.decompte.download.pdf', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('PDF') }}</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.decompte.download.doc', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('DOC') }}</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td align="center" width="35%">
                                                    <div class="dt-buttons btn-group flex-wrap">
                                                        <div class="btn-group">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti ti-chevron-down me-1"></i>{{ __('Relevé nominatif des salaires') }}
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.releve.download.pdf', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('PDF') }}</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.releve.download.doc', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('DOC') }}</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td align="center">
                                                    <div class="dt-buttons btn-group flex-wrap">
                                                        <div class="btn-group">
                                                            <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti ti-chevron-down me-1"></i>{{ __('Solde de compte') }}
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.solde.download.pdf', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('PDF') }}</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('company.ruptures.solde.download.doc', $rupture->id) }}" class=" btn-icon dropdown-item"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top" target="_blanks"><i
                                                                            class="ti ti-download ">&nbsp;</i>{{ __('DOC') }}</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <h5 class="card-header">Motif de la rupture</h5>
                                <div class="card-body">
                                    {!! $rupture->description !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-header">Actions</h5>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <a href="{{ route('company.ruptures.edit', $rupture->id) }}" class="btn btn-primary {{ $rupture->status == 'approved' || $rupture->status == 'completed' ? 'disabled' : '' }}">
                                            <i class="ti ti-pencil me-1"></i> Modifier
                                        </a>
                                        @if($rupture->status == 'pending')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="ti ti-check me-1"></i> Approuver
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="ti ti-close me-1"></i> Rejeter
                                        </button>
                                        @endif
                                        @if($rupture->status == 'approved')
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#completeModal">
                                            <i class="ti ti-check me-1"></i> Marquer comme terminée
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-label-danger {{ $rupture->status == 'completed' ? 'disabled' : '' }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="ti ti-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approuver -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approuver la rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approveForm" action="{{ route('company.ruptures.change-status', $rupture->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="status" value="approved">
                        <label for="description" class="form-label">Commentaire (optionnel)</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="approveForm" class="btn btn-success">Approuver</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejeter -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter la rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm" action="{{ route('company.ruptures.change-status', $rupture->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="status" value="rejected">
                        <label for="description" class="form-label">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="rejectForm" class="btn btn-danger">Rejeter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Terminer -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marquer la rupture comme terminée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="completeForm" action="{{ route('company.ruptures.change-status', $rupture->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="status" value="completed">
                        <label for="termination_date" class="form-label">Date de fin effective <span class="text-danger">*</span></label>
                        <input type="date" id="termination_date" name="termination_date" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Notes (optionnel)</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="completeForm" class="btn btn-info">Terminer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supprimer la rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deleteForm" action="{{ route('company.ruptures.destroy', $rupture->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <p>Êtes-vous sûr de vouloir supprimer cette rupture ? Cette action est irréversible.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deleteForm" class="btn btn-danger">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-script')
<script src="{{asset('vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endpush

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Initialisation de Flatpickr
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            locale: 'fr'
        });
    });
</script>
@endpush