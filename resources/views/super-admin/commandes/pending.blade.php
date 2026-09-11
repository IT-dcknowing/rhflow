@extends('layouts.super-admin')

@section('title', 'Commandes en Attente')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-primary"><i class="ti ti-alarm-clock me-2"></i>Commandes en Attente</h5>
                    <small class="text-muted">Statistiques sur les commandes en attente</small>
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
                        @if(isset($stats))
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ $stats['pending'] ?? 0 }}</h4>
                                                <small>En attente</small>
                                            </div>
                                            <i class="ti ti-clock fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ formatPrice($stats['pending_amount'] ?? 0) }}</h4>
                                                <small>Montant en attente</small>
                                            </div>
                                            <i class="ti ti-currency-dollar fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1 text-white">Plus ancienne</h6>
                                                <small>{{ optional($stats['oldest_pending_at'])->format('d/m/Y H:i') ?? '—' }}</small>
                                            </div>
                                            <i class="ti ti-history fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <form method="GET" action="{{ route('super-admin.commandes.pending') }}" class="row g-2 mb-3">
                            <div class="col-md-6">
                                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Rechercher par client, plan ou ID...">
                            </div>
                            <div class="col-md-6 d-flex gap-2">
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="ti ti-filter me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('super-admin.commandes.pending') }}" class="btn btn-outline-danger">
                                    <i class="ti ti-x me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </form>
                        
                        @if($commandes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" id="pendingTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Client</th>
                                            <th>Plan</th>
                                            <th>Montant</th>
                                            <th>Date de commande</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($commandes as $commande)
                                            <tr>
                                                <td>{{ $commande->user->name ?? 'N/A' }}</td>
                                                <td>{{ $commande->plan->name ?? 'N/A' }}</td>
                                                <td>{{ formatPrice($commande->amount, ['decimals'=>2]) }}</td>
                                                <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('super-admin.commandes.show', $commande) }}"
                                                            class="btn btn-sm bg-primary text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('super-admin.commandes.update', $commande) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="ti ti-check"></i> 
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super-admin.commandes.cancel', $commande) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir refuser cette commande ?')">
                                                                <i class="ti ti-close"></i> 
                                                            </button>
                                                        </form> 
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $commandes->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-clock-off ti-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Aucune commande en attente</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Aucun script nécessaire pour le filtrage GET -->
@endpush
