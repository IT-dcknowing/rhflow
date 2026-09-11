@extends('layouts.super-admin')

@section('title', 'Gestion des Coupons')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Messages de succès/erreur -->
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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-primary"><i class="ti ti-ticket me-2"></i>Gestion des Coupons</h5>
                    <small class="text-muted">Statistiques sur les coupons</small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCouponModal">
                    <i class="ti ti-plus me-1"></i>Créer un coupon
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @isset($stats)
                        <div class="row mb-3 g-3">
                            <div class="col-md-3 col-sm-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="mb-0 text-white">{{ $stats['total'] ?? 0 }}</h5>
                                                <small>Total coupons</small>
                                            </div>
                                            <i class="ti ti-ticket fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="mb-0 text-white">{{ $stats['active'] ?? 0 }}</h5>
                                                <small>Actifs</small>
                                            </div>
                                            <i class="ti ti-check fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="mb-0 text-white">{{ $stats['inactive'] ?? 0 }}</h5>
                                                <small>Inactifs</small>
                                            </div>
                                            <i class="ti ti-na fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="mb-0 text-white">{{ $stats['expiring_soon'] ?? 0 }}</h5>
                                                <small>Expirant (7j)</small>
                                            </div>
                                            <i class="ti ti-close fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endisset

                        <form method="GET" action="{{ route('super-admin.commandes.coupons.index') }}" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Rechercher par code ou nom...">
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="percentage" {{ request('type')==='percentage' ? 'selected' : '' }}>Pourcentage</option>
                                    <option value="fixed_amount" {{ request('type')==='fixed_amount' ? 'selected' : '' }}>Montant fixe</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-select">
                                    <option value="">Tous statuts</option>
                                    <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="expiring" class="form-select">
                                    <option value="">Expiration</option>
                                    <option value="soon" {{ request('expiring')==='soon' ? 'selected' : '' }}>Dans 7 jours</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="ti ti-filter me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('super-admin.commandes.coupons.index') }}" class="btn btn-outline-danger w-100">
                                    <i class="ti ti-x me-1"></i>Reset
                                </a>
                            </div>
                        </form>
                        @if($coupons->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Type</th>
                                            <th>Valeur</th>
                                            <th>Utilisations</th>
                                            <th>Statut</th>
                                            <th>Date d'expiration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($coupons as $coupon)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('super-admin.commandes.coupons.show', $coupon) }}">
                                                        <strong>{{ $coupon->code }}</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($coupon->type === 'percentage')
                                                        Pourcentage
                                                    @else
                                                        Montant fixe
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($coupon->type === 'percentage')
                                                        {{ $coupon->value }}%
                                                    @else
                                                        {{ formatPrice($coupon->value) }}
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $coupon->is_active ? 'success' : 'danger' }}">
                                                        {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Jamais' }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a class="btn btn-sm btn-info" title="Modifier"
                                                            href="{{ route('super-admin.commandes.coupons.edit', $coupon) }}">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('super-admin.commandes.coupons.show', $coupon) }}" class="btn btn-sm btn-primary bg-primary text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('super-admin.commandes.coupons.toggle', $coupon) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-{{ $coupon->is_active ? 'warning' : 'success' }}">
                                                                <i class="ti ti-{{ $coupon->is_active ? 'close' : 'check' }}"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super-admin.commandes.coupons.delete', $coupon) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce coupon ?')">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $coupons->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-ticket-off ti-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Aucun coupon créé</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création de coupon -->
<div class="modal fade" id="createCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('super-admin.commandes.coupons.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code du coupon</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code" name="code" placeholder="EX: SAVE10XY" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnGenerateCode">
                                <i class="ti ti-wand"></i> Générer
                            </button>
                        </div>
                        <small class="text-muted">8 caractères alphanumériques. Unicité vérifiée à l'enregistrement.</small>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="percentage">Pourcentage</option>
                            <option value="fixed_amount">Montant fixe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">Valeur</label>
                        <input type="number" class="form-control" id="value" name="value" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="min_amount" class="form-label">Montant minimum (par défaut 0)</label>
                        <input type="number" class="form-control" id="min_amount" name="min_amount" step="0.01" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="max_uses" class="form-label">Nombre d'utilisations maximum (laisser vide pour illimité)</label>
                        <input type="number" class="form-control" id="max_uses" name="max_uses">
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Date d'expiration</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at">
                    </div>
                    <input type="hidden" name="is_active" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-dismiss notifications after 5 seconds
    const notifications = document.querySelectorAll('.alert-dismissible');
    notifications.forEach(function(notification) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }, 5000); // 5 seconds
    });

  document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnGenerateCode');
    const input = document.getElementById('code');
    function generateCouponCode(len = 8) {
      const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // sans O/0/I/1
      let out = '';
      for (let i = 0; i < len; i++) {
        out += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      return out.toUpperCase();
    }
    btn?.addEventListener('click', function() {
      input.value = generateCouponCode(8);
      input.focus();
      input.select();
    });
  });
</script>
@endpush
