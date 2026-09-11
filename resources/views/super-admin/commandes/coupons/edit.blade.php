@extends('layouts.super-admin')

@section('title', 'Modifier le Coupon')

@section('content') 
<div class="row">
  <div class="col-lg-12">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif 

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif      
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          <div>
              <h5 class="mb-0 text-primary"><i class="ti ti-ticket me-2"></i>Modifier le Coupon : {{ $coupon->code }}</h5>
              <small class="text-muted">Modifier les informations du coupon</small>
          </div>
          <a href="{{ route('super-admin.commandes.coupons.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Retour
          </a>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <form method="POST" action="{{ route('super-admin.commandes.coupons.update', $coupon) }}">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Code</label>
                  <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nom</label>
                  <input type="text" name="name" class="form-control" value="{{ old('name', $coupon->name) }}" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3">{{ old('description', $coupon->description) }}</textarea>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Type</label>
                  <select name="type" class="form-select" required>
                    <option value="percentage" {{ old('type', $coupon->type)==='percentage' ? 'selected' : '' }}>Pourcentage</option>
                    <option value="fixed_amount" {{ old('type', $coupon->type)==='fixed_amount' ? 'selected' : '' }}>Montant fixe</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Valeur</label>
                  <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Montant minimum</label>
                  <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ old('min_amount', $coupon->min_amount) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nombre d'utilisations max</label>
                  <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses', $coupon->max_uses) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Expiration</label>
                  <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Statut</label>
                  <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', $coupon->is_active) ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ !old('is_active', $coupon->is_active) ? 'selected' : '' }}>Inactif</option>
                  </select>
                </div>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                  <i class="ti ti-device-floppy me-1"></i>Enregistrer
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card bg-dark text-white">
          <div class="card-header">
            <h6 class="mb-0 text-white">
              <i class="ti ti-info-alt me-2"></i>Aide</h6>
          </div>
          <div class="card-body small text-white">
            <p>• Pourcentage: applique une réduction en % sur le total.</p>
            <p>• Montant fixe: déduit un montant fixe.</p>
            <p>• Laissez "Nombre d'utilisations max" vide pour illimité.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
