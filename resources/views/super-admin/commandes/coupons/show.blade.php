@extends('layouts.super-admin')

@section('title', 'Détails du Coupon')

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
              <h5 class="mb-0 text-primary"><i class="ti ti-ticket me-2"></i>Détails du Coupon : {{ $coupon->code }}</h5>
              <small class="text-muted">Information sur le coupon créé le {{ $coupon->created_at->format('d/m/Y') }}</small>
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
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Code</label>
                <div class="form-control">{{ $coupon->code }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Nom</label>
                <div class="form-control">{{ $coupon->name }}</div>
              </div>
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <div class="form-control" style="min-height: 46px;">{{ $coupon->description ?? '—' }}</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Type</label>
                <div class="form-control">{{ $coupon->type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Valeur</label>
                <div class="form-control">
                  @if($coupon->type === 'percentage')
                    {{ $coupon->value }}%
                  @else
                    {{ formatPrice($coupon->value, ['include_symbol' => false, 'decimals' => 2]) }}
                  @endif
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Montant minimum</label>
                <div class="form-control">{{ $coupon->min_amount ? formatPrice($coupon->min_amount, ['include_symbol'=>false,'decimals'=>2]) : '—' }}</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Utilisations</label>
                <div class="form-control">{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Expiration</label>
                <div class="form-control">{{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Jamais' }}</div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Statut</label>
                <div class="form-control">
                  <span class="badge bg-{{ $coupon->is_active ? 'success' : 'danger' }}">{{ $coupon->is_active ? 'Actif' : 'Inactif' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Actions</h6>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('super-admin.commandes.coupons.toggle', $coupon) }}" class="mb-2">
              @csrf
              <button type="submit" class="btn w-100 btn-{{ $coupon->is_active ? 'warning' : 'success' }}">
                <i class="ti ti-{{ $coupon->is_active ? 'ban' : 'check' }} me-1"></i>
                {{ $coupon->is_active ? 'Désactiver' : 'Activer' }}
              </button>
            </form>
            <form method="POST" action="{{ route('super-admin.commandes.coupons.delete', $coupon) }}" onsubmit="return confirm('Supprimer ce coupon ?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger w-100">
                <i class="ti ti-trash me-1"></i>Supprimer
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
@endsection
