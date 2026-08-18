@extends('layouts.app')

@section('title', 'Détails du Prêt')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">📅 Détails du Prêt #{{ $loan->id }} - {{ $loan->title }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.loans.index') }}">Prêts</a>
                                </li>
                                <li class="breadcrumb-item active">Prêts</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('company.loans.edit', $loan->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger" id="delete-loan">
                            <i class="fas fa-trash me-1"></i> Supprimer
                        </button>
                        <form id="delete-form" action="{{ route('company.loans.destroy', $loan->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informations Générales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom">
                                        <h6 class="col-form-label fw-bold">Employé : </h6>
                                        <span class="form-control-static">{{ $loan->employee->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom">
                                        <h6 class="col-form-label fw-bold">Type de Prêt : </h6>
                                        <span class="form-control-static">{{ $loan->loanOption->name ?? 'Non défini' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom">
                                        <h6 class="col-form-label fw-bold">Succursale : </h6>
                                        <span class="form-control-static">{{ $loan->branch->name ?? 'Non défini' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom">
                                        <h6 class="col-form-label fw-bold">Titre : </h6>
                                        <span class="form-control-static">{{ $loan->title }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="col-form-label fw-bold">Motif : </h6>
                                        {{ $loan->reason ?? 'Non spécifié' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Période : {{ $loan->periode->nom }} - Statut : {{ ucfirst($loan->periode->statut) }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Date de Début : </h6>
                                        <span class="form-control-static">{{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Date de Fin : </h6>
                                        <span class="form-control-static">{{ \Carbon\Carbon::parse($loan->end_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Statut : </h6>
                                        <span class="form-control-static">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-label-warning',
                                                    'running' => 'bg-label-info',
                                                    'completed' => 'bg-label-success',
                                                    'cancelled' => 'bg-label-danger'
                                                ];
                                                $statusText = [
                                                    'pending' => 'En Attente',
                                                    'running' => 'Octroyé',
                                                    'completed' => 'Terminé',
                                                    'cancelled' => 'Annulé'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClass[$loan->statut] ?? 'bg-label-secondary' }}">
                                                {{ $statusText[$loan->statut] ?? $loan->statut }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Créé le : </h6>
                                        <span class="form-control-static">{{ $loan->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Mis à jour le : </h6>
                                        <span class="form-control-static">{{ $loan->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Détails Financiers</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Montant Total:</h6>
                                        <span class="form-control-static">{{ number_format($loan->amount, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Mensualité:</h6>
                                        <span class="form-control-static">{{ number_format($loan->amount_deduc, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Type de Déduction:</h6>
                                        <span class="form-control-static">{{ $loan->type == 'fixe' ? 'Montant Fixe' : 'Pourcentage' }}</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Nombre de Mois:</h6>
                                        <span class="form-control-static">{{ $loan->nbre_mois }}</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Montant Remboursé:</h6>
                                        <span class="form-control-static">{{ number_format($loan->payments->sum('amount') ?? 0, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom">
                                        <h6 class="col-form-label fw-bold">Reste à Payer:</h6>
                                        <span class="form-control-static">{{ number_format($loan->amount - ($loan->payments->sum('amount') ?? 0), 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Historique des Remboursements</h5>
                                    <a href="{{ route('company.loans.payments.create', $loan->id) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Nouveau Paiement
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($loan->payments && $loan->payments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th>Note</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($loan->payments as $payment)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ $payment->note ?? '-' }}</td>
                                                            <td>
                                                                <div class="d-flex justify-content-center">
                                                                    <a href="{{ route('company.loans.payments.edit',[$loan->id, $payment->id]) }}" class="btn btn-primary btn-icon me-2">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-danger btn-icon delete-payment" data-id="{{ $payment->id }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                    <form id="delete-payment-{{ $payment->id }}" action="{{ route('company.loans.payments.destroy', [$loan->id, $payment->id]) }}" method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center p-3">
                                            <p>Aucun remboursement enregistré pour ce prêt.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Échéancier de Remboursement</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Mois</th>
                                                    <th>Date</th>
                                                    <th>Montant</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $startDate = \Carbon\Carbon::parse($loan->start_date);
                                                    $monthlyAmount = $loan->amount_deduc;
                                                    $totalPaid = $loan->payments->sum('amount') ?? 0;
                                                    $remainingAmount = $loan->amount;
                                                @endphp

                                                @for($i = 0; $i < $loan->nbre_mois; $i++)
                                                    @php
                                                        $paymentDate = (clone $startDate)->addMonths($i);
                                                        $isPaid = $totalPaid >= ($i + 1) * $monthlyAmount;
                                                        $remainingAmount -= $isPaid ? $monthlyAmount : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $paymentDate->format('d/m/Y') }}</td>
                                                        <td>{{ number_format($monthlyAmount, 0, ',', ' ') }} FCFA</td>
                                                        <td>
                                                            @if($isPaid)
                                                                <span class="badge bg-label-success">Payé</span>
                                                            @elseif($paymentDate->isPast())
                                                                <span class="badge bg-label-danger">En retard</span>
                                                            @else
                                                                <span class="badge bg-label-warning">À venir</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('company.loans.index') }}?periode_id={{ $loan->periode_id }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Confirmation de suppression du prêt
        $('#delete-loan').on('click', function () {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });

        // Confirmation de suppression d'un paiement
        $('.delete-payment').on('click', function () {
            var paymentId = $(this).data('id');
            
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-payment-' + paymentId).submit();
                }
            });
        });
    });
</script>
@endpush