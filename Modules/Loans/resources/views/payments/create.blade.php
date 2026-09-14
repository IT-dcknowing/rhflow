@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Nouveau Remboursement de Prêt</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.loans.show', $loan->id) }}">Détails du Prêt</a>
                            </li>
                            <li class="breadcrumb-item active">Nouveau Remboursement</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Détails du Remboursement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.loans.payments.store', $loan->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant du Prêt</label>
                                <input type="text" class="form-control" value="{{ number_format($loan->amount, 0, ',', ' ') }} FCFA" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant déjà remboursé</label>
                                <input type="text" class="form-control" value="{{ number_format($loan->payments->sum('amount'), 0, ',', ' ') }} FCFA" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reste à payer</label>
                                <input type="text" class="form-control" value="{{ number_format($loan->amount - $loan->payments->sum('amount'), 0, ',', ' ') }} FCFA" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Montant du remboursement <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" required 
                                           value="{{ $loan->amount_deduc - $loan->payments->sum('amount_deduc') }}"
                                           min="1" max="{{ $loan->amount - $loan->payments->sum('amount') }}"
                                           step="0.01">
                                    <span class="input-group-text">FCFA</span>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">Date de paiement <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" 
                                          id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('company.loans.show', $loan->id) }}" class="btn btn-label-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des sélecteurs si nécessaire
    });
</script>
@endpush