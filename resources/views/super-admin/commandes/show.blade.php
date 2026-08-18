@extends('layouts.super-admin')

@section('title', 'Détails de la commande')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-primary"><i class="ti ti-view-list-alt me-2"></i>Détails de la commande</h5>
                    <small class="text-muted">Commande #{{ $commande->id }} - {{ $commande->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <a href="{{ route('super-admin.commandes.index') }}" class="btn btn-outline-primary bg-label-primary">
                    <i class="ti ti-arrow-left me-1"></i>Toutes les commandes
                </a>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Détails de la commande #{{ $commande->id }}</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($commande->status === 'pending' || $commande->status === 'paid')
                                                <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="ti ti-check me-1"></i>Approuver
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super-admin.commandes.cancel', $commande) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir refuser cette commande ?')">
                                                        <i class="ti ti-close me-1"></i>Refuser
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="refunded">
                                                    <button type="submit" class="btn btn-outline-info"
                                                            onclick="return confirm('Marquer cette commande comme remboursée ?')">
                                                        <i class="ti ti-rotate me-1"></i>Rembourser
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="expired">
                                                    <button type="submit" class="btn btn-outline-secondary"
                                                            onclick="return confirm('Expirer cette commande ?')">
                                                        <i class="ti ti-clock-off me-1"></i>Expirer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-12 mb-4">
                                                <h6>Informations client</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>Nom:</strong></td>
                                                        <td>{{ $commande->user->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email:</strong></td>
                                                        <td>{{ $commande->user->email ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Téléphone:</strong></td>
                                                        <td>{{ $commande->user->phone ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-12">
                                                <h6>Informations commande</h6>
                                                <table class="table table-sm">
                                                        <td><strong>Date:</strong></td>
                                                        <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Statut:</strong></td>
                                                    <td>
                                                        @php $st = $commande->status; @endphp
                                                        <span class="badge bg-{{ $st==='paid' ? 'success' : ($st==='pending' ? 'warning' : ($st==='refunded' ? 'info' : ($st==='expired' ? 'secondary' : 'danger'))) }}">
                                                            {{ ucfirst($st) }}
                                                        </span>
                                                        @if($st === 'pending')
                                                            <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline ms-2">
                                                                @csrf
                                                                <input type="hidden" name="status" value="refunded">
                                                                <button type="submit" class="btn btn-sm btn-outline-info"
                                                                        onclick="return confirm('Marquer cette commande comme remboursée ?')">
                                                                    <i class="ti ti-rotate me-1"></i>Rembourser
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="expired">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                                        onclick="return confirm('Expirer cette commande ?')">
                                                                    <i class="ti ti-clock-off me-1"></i>Expirer
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Méthode de paiement:</strong></td>
                                                        <td>{{ ucfirst($commande->payment_method ?? 'N/A') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <h6>Détails du plan</h6>
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5>{{ $commande->plan->name ?? 'Plan inconnu' }}</h5>
                                                        <p class="text-muted">{{ $commande->plan->description ?? 'Aucune description' }}</p>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <h3 class="text-primary">{{ formatPrice($commande->amount, ['decimals'=>2]) }}</h3>
                                                        <small class="text-muted">{{ $commande->plan->formatted_duration ?? '' }}</small>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <p><i class="ti ti-users me-2"></i>Max utilisateurs: {{ $commande->plan->max_users ?? 'N/A' }}</p>
                                                        <p><i class="ti ti-user-check me-2"></i>Max employés: {{ $commande->plan->max_employees ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><i class="ti ti-device-desktop me-2"></i>Stockage: {{ $commande->plan->storage_limit ?? 'N/A' }} GB</p>
                                                        <p><i class="ti ti-file-text me-2"></i>Traitement: {{ $commande->plan->nbre_trait ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($commande->coupon)
                                            <div class="mt-3">
                                                <h6>Coupon appliqué</h6>
                                                <div class="alert alert-info">
                                                    <strong>{{ $commande->coupon->code }}</strong> -
                                                    @if($commande->coupon->type === 'percentage')
                                                        {{ $commande->coupon->value }}% de réduction
                                                    @else
                                                        {{ number_format($commande->coupon->value, 2) }} € de réduction
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($commande->notes)
                                            <div class="mt-3">
                                                <h6>Notes</h6>
                                                <div class="alert alert-light">
                                                    {{ $commande->notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary" onclick="printCommande()">
                                                <i class="ti ti-printer me-1"></i>Imprimer
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="sendEmail()">
                                                <i class="ti ti-mail me-1"></i>Envoyer par email
                                            </button>
                                            @if($commande->invoice_url)
                                                <a href="{{ $commande->invoice_url }}" class="btn btn-outline-info" target="_blank">
                                                    <i class="ti ti-file-text me-1"></i>Voir facture
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($commande->payment_details)
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h5 class="mb-0">Détails de paiement</h5>
                                        </div>
                                        <div class="card-body">
                                            <pre class="bg-light p-3 rounded">{{ json_encode($commande->payment_details, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
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

<script>
function printCommande() {
    window.print();
}

function sendEmail() {
    // Logique d'envoi d'email
    alert('Fonctionnalité d\'envoi d\'email à implémenter');
}
</script>
@endsection
