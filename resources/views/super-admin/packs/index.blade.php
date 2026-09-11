@extends('layouts.super-admin')

@section('title', 'Gestion des Packs - RH Flow')

@section('content')

<div class="row">
    <div class="col-12">
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
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-package me-2"></i>
                        Gestion des Packs d'Abonnement
                    </h5>
                    <small class="text-muted">
                        Gérer vos packs d'abonnement : actifs, inactifs, expirés, annulés, en attente, suspendus
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-primary" href="{{ route('super-admin.packs.create') }}">
                        <i class="ti ti-plus me-2"></i>
                        Nouveau Pack
                    </a>
                    <button class="btn btn-outline-primary bg-label-primary" onclick="refreshStats()">
                        <i class="ti ti-refresh me-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>
         
        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">      
                    <div class="card-body">
                        <!-- Statistiques des packs -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="w-100 h-100">
                                    <div class="row">
                                        <div class="col-md-12 mb-1">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-1 text-white">{{ collect($packs)->sum('company_count') }}</h3>
                                                    <small>Abonnements Actifs</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-1">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-1 text-white">{{ collect($packs)->sum('active_company_count') }}</h3>
                                                    <small>Entreprises Utilisatrices</small>
                                                </div>
                                            </div>
                                        </div>      
                                        <div class="col-md-12 mb-1">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-1 text-white">{{ collect($packs)->where('popular', true)->count() }}</h3>
                                                    <small>Packs Populaires</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-1 text-white">{{ isset($kpis['mrr']) ? formatPrice($kpis['mrr']) : '—' }}</h3>
                                                    <small>MRR (normalisé)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Graphique de répartition des abonnements -->
                                <div class="row w-100 h-100">
                                    <div class="col-md-12">
                                        <div class="card h-100 justify-content-center align-items-center">
                                            <div class="card-header">
                                                <h6 class="card-title m-0">
                                                    <i class="ti ti-chart-pie me-2"></i>
                                                    Répartition des Abonnements
                                                </h6>
                                            </div>
                                            <div class="card-body w-100 d-flex justify-content-center align-items-center">
                                                <canvas id="subscriptionChart" height="300"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--
                        @isset($kpis)
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ formatPrice($kpis['cash_month'] ?? 0) }}</h3>
                                        <small>CA du mois (encaissé)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ formatPrice(($kpis['accrual_month'] ?? 0)) }}</h3>
                                        <small>CA reconnu (mois)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-dark text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ isset($kpis['arr']) ? formatPrice($kpis['arr']) : '—' }}</h3>
                                        <small>ARR</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endisset
                        --}}

                        <!-- Grille des packs -->
                        <div class="row">
                            @foreach($packs as $pack)
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card h-100 {{ $pack['popular'] ? 'border-primary' : '' }} {{ $pack['enterprise'] ? 'border-warning' : '' }}">
                                    @if($pack['popular'])
                                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                                        <span class="badge bg-primary">Populaire</span>
                                    </div>
                                    @endif

                                    @if($pack['enterprise'])
                                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                                        <span class="badge bg-warning">Entreprise</span>
                                    </div>
                                    @endif

                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ $pack['name'] }}</h5>

                                        @if(!$pack['enterprise'])
                                        <div class="mb-3">
                                            <span class="display-6 fw-bold text-primary">{{ formatPrice($pack['price']) }}</span><br>
                                            <small class="text-muted">/ Mois</small>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                {{ formatPrice($pack['price_yearly'], $currencyDetails) }}/an
                                                <span class="badge bg-success ms-2">Économisez 2 mois</span>
                                            </small>
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <span class="display-6 fw-bold text-warning">Sur mesure</span>
                                        </div>
                                        @endif

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <i class="ti ti-users me-2 text-muted"></i>
                                                <span>{{ $pack['users_limit'] === -1 ? 'Illimité' : 'Jusqu\'à ' . $pack['users_limit'] }} employés</span>
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <i class="ti ti-database me-2 text-muted"></i>
                                                <span>{{ $pack['storage_limit'] }} GB stockage</span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <h6 align="left">Fonctionnalités incluses :</h6>
                                            <ul class="list-unstyled text-start" style="margin-left: 20px;">
                                                @if($pack['features'] && count($pack['features']) > 0)
                                                    @foreach($pack['features'] as $feature)
                                                    <li class="mb-2">
                                                        <i class="ti ti-check text-success me-2"></i>
                                                        {{ $feature }}
                                                    </li>
                                                    @endforeach
                                                @else
                                                    <li class="mb-2 text-muted">
                                                        <i class="ti ti-minus text-muted me-2"></i>
                                                        Aucune fonctionnalité définie
                                                    </li>
                                                @endif
                                            </ul>
                                        </div> 

                                        <div class="mb-3">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <strong>{{ $pack['company_count'] }}</strong>
                                                    <br><small class="text-muted">Abonnements</small>
                                                </div>
                                                <div class="col-6">
                                                    <strong>{{ $pack['active_company_count'] }}</strong>
                                                    <br><small class="text-muted">Actifs</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="showPack({{ $pack['id'] }})">
                                                <i class="ti ti-eye me-1"></i>
                                            </button>
                                            <button class="btn btn-outline-info btn-sm" onclick="editPack({{ $pack['id'] }})">
                                                <i class="ti ti-marker-alt me-1"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deletePack({{ $pack['id'] }}, '{{ $pack['name'] }}')">
                                                <i class="ti ti-trash me-1"></i>
                                            </button>
                                            @if($pack['is_active'])
                                            <button class="btn btn-outline-warning btn-sm" onclick="togglePack({{ $pack['id'] }})">
                                                <i class="ti ti-power-off me-1"></i>
                                            </button>
                                            @else
                                            <button class="btn btn-outline-success btn-sm" onclick="togglePack({{ $pack['id'] }})">
                                                <i class="ti ti-control-play me-1"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>          
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(38, 61, 136, 0.1);
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(38, 61, 136, 0.15);
    }

    .display-6 {
        font-size: 2.5rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .position-absolute {
        z-index: 10;
    }

    .badge {
        font-size: 0.75rem;
    }

    .list-unstyled li {
        padding: 0.25rem 0;
    }

    .section-title {
        color: #263d88;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    .modal-content {
        border: none;
        box-shadow: 0 1rem 3rem rgba(38, 61, 136, 0.15);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .btn-outline-primary:hover, .btn-outline-warning:hover, .btn-outline-success:hover {
        background-color: #263d88;
        border-color: #263d88;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Soumission du formulaire de création de pack
    const createPlanForm = document.getElementById('createPlanForm');
    if (createPlanForm) {
        createPlanForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Conversion des fonctionnalités en tableau
            if (data.features) {
                data.features = data.features.split('\n').filter(f => f.trim());
            }

            console.log('Création du pack:', data);
            // Ici vous feriez un appel AJAX pour créer le pack

            // Fermer la modal et afficher un message de succès
            const modal = bootstrap.Modal.getInstance(document.getElementById('createPlanModal'));
            modal.hide();

            alert('Pack créé avec succès ! (Fonctionnalité à implémenter)');
        });
    }

    // Actualisation des statistiques
    window.refreshStats = function() {
        console.log('Actualisation des statistiques...');
        // Ici vous pourriez faire un appel AJAX pour mettre à jour les stats
    };

    // Affichage d'un pack
    window.showPack = function(packId) {
        window.location.href = '{{ route("super-admin.packs.show", ":id") }}'.replace(':id', packId);
    };

    // Édition d'un pack
    window.editPack = function(packId) {
        window.location.href = '{{ route("super-admin.packs.edit", ":id") }}'.replace(':id', packId);
    };

    // Activation/désactivation d'un pack
    window.togglePack = function(packId) {
        if (confirm('Êtes-vous sûr de vouloir changer le statut de ce pack ?')) {
            // Désactiver le bouton pendant le traitement
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="ti ti-loader me-1"></i> Traitement...';

            fetch('{{ route("super-admin.packs.toggle", ":id") }}'.replace(':id', packId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    location.reload();
                } else {
                    alert('Erreur lors de la modification du statut du pack');
                    // Réactiver le bouton
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la modification du statut du pack');
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }
    };

    // Suppression d'un pack
    window.deletePack = function(packId, packName) {
        if (confirm('Êtes-vous sûr de vouloir supprimer le pack "' + packName + '" ?\n\nCette action est irréversible.')) {
            // Désactiver le bouton pendant le traitement
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="ti ti-loader me-1"></i> Suppression...';

            fetch('{{ route("super-admin.packs.delete", ":id") }}'.replace(':id', packId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    location.reload();
                } else {
                    alert(data.message || 'Erreur lors de la suppression du pack');
                    // Réactiver le bouton
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression du pack');
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }
    };

    // Charger Chart.js si non présent puis dessiner le graphique
    function ensureChartJs(cb){
        if (typeof Chart !== 'undefined') return cb();
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        s.onload = cb;
        document.head.appendChild(s);
    }

    ensureChartJs(function(){
        const ctx = document.getElementById('subscriptionChart');
        if (!ctx) return;

        // Construire les labels et données depuis $packStats (nom du pack et nb actifs)
        const packStats = @json($packStats ?? []);
        const labels = packStats.map(p => p.name ?? 'Pack');
        const values = packStats.map(p => (p.active_company_count ?? p.company_count ?? 0));

        // Génération d'une palette variée (HSL) selon le nombre de segments
        const colors = labels.map((_, i) => {
            const hue = Math.round((360 / Math.max(1, labels.length)) * i);
            return `hsl(${hue} 60% 55%)`;
        });

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.length ? labels : ['Aucune donnée'],
                datasets: [{
                    data: values.length ? values : [1],
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });

    // Auto-dismiss notifications after 5 seconds
	const notifications = document.querySelectorAll('.alert-dismissible');
	notifications.forEach(function(notification) {
		setTimeout(function() {
			const bsAlert = new bootstrap.Alert(notification);
			bsAlert.close();
		}, 5000); // 5 seconds
	});

});
</script>
@endpush
