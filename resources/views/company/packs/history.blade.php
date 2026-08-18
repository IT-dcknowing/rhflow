@extends('layouts.app')

@section('title', 'Historique des Commandes - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="ti ti-history me-2"></i>
                        Historique des Commandes
                    </h4>
                    <p class="text-muted mb-0">Consultez l'historique de vos abonnements et paiements</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.plan.pricing') }}" class="btn btn-outline-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour aux abonnements
                    </a>
                    <button class="btn btn-outline-secondary" onclick="refreshHistory()">
                        <i class="ti ti-refresh me-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>  
   
    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $orders->count() }}</h3>
                    <p class="mb-0">Total commandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $orders->where('status', 'paid')->count() }}</h3>
                    <p class="mb-0">Payées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $orders->where('status', 'pending')->count() }}</h3>
                    <p class="mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ number_format($orders->sum('total_amount'), 0, ',', ' ') }} FCFA</h3>
                    <p class="mb-0">Montant total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('company.packs.history') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirée</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Méthode de paiement</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Toutes les méthodes</option>
                                    <option value="wave" {{ request('payment_method') === 'wave' ? 'selected' : '' }}>Wave</option>
                                    <option value="orange" {{ request('payment_method') === 'orange' ? 'selected' : '' }}>Orange Money</option>
                                    <option value="mtn" {{ request('payment_method') === 'mtn' ? 'selected' : '' }}>MTN Mobile Money</option>
                                    <option value="moov" {{ request('payment_method') === 'moov' ? 'selected' : '' }}>Moov Money</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter me-2"></i>
                                    Filtrer
                                </button>
                                <a href="{{ route('company.packs.history') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-2"></i>
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Plan</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Expiration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <code>{{ $order->order_number }}</code>
                                    </td>
                                    <td>
                                        @if($order->plan)
                                            <div>
                                                <strong>{{ $order->plan->name }}</strong>
                                                @if($order->notes)
                                                <br><small class="text-muted">{{ $order->notes }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Plan supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
                                        @if($order->discount_amount > 0)
                                        <br><small class="text-success">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($order->payment_method)
                                            @case('wave')
                                                <i class="ti ti-brand-wave text-primary"></i> Wave
                                                @break
                                            @case('orange')
                                                <i class="ti ti-brand-orange text-warning"></i> Orange Money
                                                @break
                                            @case('mtn')
                                                <i class="ti ti-brand-mtn text-success"></i> MTN Mobile Money
                                                @break
                                            @case('moov')
                                                <i class="ti ti-brand-moov text-info"></i> Moov Money
                                                @break
                                            @default
                                                <span class="text-muted">{{ $order->payment_method }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($order->expires_at)
                                            @if($order->expires_at->isPast())
                                                <span class="text-danger">
                                                    <i class="ti ti-clock me-1"></i>
                                                    Expirée
                                                </span>
                                            @else
                                                <span class="text-warning">
                                                    <i class="ti ti-clock me-1"></i>
                                                    {{ $order->expires_at->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary" onclick="viewOrderDetails({{ $order->id }})">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            
                                            @if($order->isPending())
                                            <button class="btn btn-primary" onclick="payOrder({{ $order->id }})">
                                                <i class="ti ti-credit-card"></i>
                                            </button>
                                            @endif
                                            
                                            @if($order->isPending() || $order->isExpired())
                                            <button class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})">
                                                <i class="ti ti-x"></i>
                                            </button>
                                            @endif
                                            
                                            @if($order->isPaid())
                                            <button class="btn btn-outline-success" onclick="downloadInvoice({{ $order->id }})">
                                                <i class="ti ti-download"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Affichage de {{ $orders->firstItem() }} à {{ $orders->lastItem() }} 
                                sur {{ $orders->total() }} commandes
                            </small>
                        </div>
                        {{ $orders->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="ti ti-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">Aucune commande trouvée</h5>
                        <p class="text-muted">Vous n'avez pas encore de commande dans votre historique.</p>
                        <a href="{{ route('company.packs.index') }}" class="btn btn-primary">
                            <i class="ti ti-shopping-cart me-2"></i>
                            Voir les plans
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal détails commande -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewOrderDetails(orderId) {
        fetch(`/company/packs/orders/${orderId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOrderDetails(data.order);
                } else {
                    toastr.error('Erreur lors du chargement des détails');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                toastr.error('Erreur lors du chargement des détails');
            });
    }

    function displayOrderDetails(order) {
        let content = '<div class="row">';
        content += '<div class="col-md-6">';
        content += '<h6>Informations générales</h6>';
        content += '<table class="table table-sm">';
        content += '<tr><td><strong>Numéro:</strong></td><td><code>' + order.order_number + '</code></td></tr>';
        content += '<tr><td><strong>Date:</strong></td><td>' + order.created_at + '</td></tr>';
        content += '<tr><td><strong>Statut:</strong></td><td><span class="badge bg-' + order.status_color + '">' + order.status_label + '</span></td></tr>';
        content += '<tr><td><strong>Méthode:</strong></td><td>' + order.payment_method_display + '</td></tr>';
        content += '</table>';
        content += '</div>';
        content += '<div class="col-md-6">';
        content += '<h6>Plan et montant</h6>';
        content += '<table class="table table-sm">';
        content += '<tr><td><strong>Plan:</strong></td><td>' + (order.plan_name || 'N/A') + '</td></tr>';
        content += '<tr><td><strong>Montant:</strong></td><td>' + order.amount_formatted + '</td></tr>';
        content += '<tr><td><strong>Réduction:</strong></td><td>' + (order.discount_formatted || '-') + '</td></tr>';
        content += '<tr><td><strong>Total:</strong></td><td><strong>' + order.total_formatted + '</strong></td></tr>';
        content += '</table>';
        content += '</div>';
        content += '</div>';
        
        if (order.notes) {
            content += '<div class="mt-3"><h6>Notes</h6><p class="text-muted">' + order.notes + '</p></div>';
        }
        
        if (order.expires_at) {
            content += '<div class="mt-3"><h6>Expiration</h6><p class="text-muted">Expire le: ' + order.expires_at + '</p></div>';
        }
        
        if (order.payment_reference) {
            content += '<div class="mt-3"><h6>Référence de paiement</h6><p><code>' + order.payment_reference + '</code></p></div>';
        }
        
        document.getElementById('orderDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
    }

    function payOrder(orderId) {
        window.location.href = `/order/payment/${orderId}`;
    }

    function cancelOrder(orderId) {
        if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
            fetch(`/company/packs/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Commande annulée avec succès');
                    location.reload();
                } else {
                    toastr.error(data.message || 'Erreur lors de l\'annulation');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                toastr.error('Erreur lors de l\'annulation');
            });
        }
    }

    function downloadInvoice(orderId) {
        window.open(`/company/packs/orders/${orderId}/invoice`, '_blank');
    }

    function refreshHistory() {
        location.reload();
    }
</script>
@endpush


