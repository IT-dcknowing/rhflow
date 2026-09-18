@extends('layouts.super-admin')

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
                        <i class="ti ti-bag me-2"></i>
                        Gestion des Entreprises
                    </h5>
                    <small class="text-muted">
                        Gérer vos entreprises : suspendues, expirées, annulées, en attente, inactives, actives, en période d'essai
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.enterprises.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Nouvelle Entreprise
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">   
                    <div class="card-body">

                        <!-- Statistiques rapides -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['total_enterprises'] ?? 0 }}</h3>
                                        <small>Entreprises Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['active_enterprises'] ?? 0 }}</h3>
                                        <small>Entreprises Actives</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['trial_enterprises'] ?? 0 }}</h3>
                                        <small>En Période d'Essai</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1 text-white">{{ $stats['expired_enterprises'] ?? 0 }}</h3>
                                        <small>Abonnements Expirés</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres et recherche -->
                        <form method="GET" action="{{ route('super-admin.enterprises.index') }}" class="row mb-4 g-2">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Rechercher (nom, email, téléphone, ville)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="" {{ request('status')=='' ? 'selected' : '' }}>Tous les statuts</option>
                                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Actives</option>
                                    <option value="trial" {{ request('status')=='trial' ? 'selected' : '' }}>Essai</option>
                                    <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspendues</option>
                                    <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>Expirées</option>
                                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Annulées</option>
                                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactives</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select" name="plan">
                                    <option value="" {{ request('plan')=='' ? 'selected' : '' }}>Tous les plans</option>
                                    @foreach($packs as $pack)
                                        <option value="{{ $pack->id }}" {{ request('plan')==$pack->id ? 'selected' : '' }}>{{ $pack->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="ti ti-filter me-2"></i>Filtrer
                                </button>
                            </div>
                            <div class="col-md-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-success w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-download me-2"></i>
                                        Exporter
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('super-admin.enterprises.export.xlsx', request()->query()) }}">
                                            <i class="ti ti-file-spreadsheet me-2"></i>Excel (CSV)
                                        </a>
                                        <a class="dropdown-item" href="{{ route('super-admin.enterprises.export.pdf', request()->query()) }}">
                                            <i class="ti ti-file-text me-2"></i>PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Tableau des entreprises -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="enterprisesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Entreprise</th>
                                        <th>Contact</th>
                                        <th>Taille</th>
                                        <th>Pack</th>
                                        <th>Statut</th>
                                        <th>Employés</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enterprises ?? [] as $enterprise)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    @if($enterprise->logo)
                                                        <img src="{{ asset('storage/logos/' . $enterprise->logo) }}" alt="Logo" class="rounded-circle">
                                                    @else
                                                        <span class="avatar-initial bg-primary rounded-circle">{{ substr($enterprise->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $enterprise->name }}</h6>
                                                    Secteur : <span class="badge bg-info">{{ $enterprise->sector?->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $enterprise->email }}</strong><br>
                                                <small class="text-muted">{{ $enterprise->phone ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $enterprise->size_label }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $planName = $enterprise->companyPlan->name ?? '—';
                                                $planColor = $planName == 'Gratuit' ? 'primary' : ($planName == 'Basic' ? 'info' : ($planName == 'Pro' ? 'success' : 'danger'));
                                            @endphp
                                            <button type="button"
                                                    class="badge bg-{{ $planColor }} border-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#packInfoModal-{{ $enterprise->user_id ?? $enterprise->id }}"
                                                    data-pack="{{ $planName }}"
                                                    data-start="{{ optional($enterprise->subscription_start_date)->toIso8601String() }}"
                                                    data-end="{{ optional($enterprise->subscription_end_date)->toIso8601String() }}"
                                                    data-status="{{ $enterprise->subscription_status ?? ($enterprise->hasActiveSubscription() ? 'active' : 'inactive') }}"
                                                    data-manage-url="{{ route('super-admin.enterprises.subscription', $enterprise->user_id ?? $enterprise) }}">
                                                {{ $planName }}
                                            </button>
                                            <!-- Modal: Infos Pack / Abonnement (unique per row) -->
                                            <div class="modal fade" id="packInfoModal-{{ $enterprise->user_id ?? $enterprise->id }}" tabindex="-1" aria-labelledby="packInfoLabel-{{ $enterprise->user_id ?? $enterprise->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-primary" id="packInfoLabel-{{ $enterprise->user_id ?? $enterprise->id }}">
                                                                <i class="ti ti-package me-2"></i>
                                                                Pack :  <span  class="badge bg-{{ $planColor }} modalPackName">{{ $planName }}</span>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span class="me-2">Statut:</span>
                                                                @php
                                                                    $st = $enterprise->subscription_status ?? ($enterprise->hasActiveSubscription() ? 'active' : 'inactive');
                                                                    $stColor = match($st){
                                                                        'active' => 'success',
                                                                        'trial' => 'info',
                                                                        'expired' => 'danger',
                                                                        'pending' => 'warning',
                                                                        default => 'secondary'
                                                                    };
                                                                    $stLabel = match($st){
                                                                        'active' => 'Actif',
                                                                        'trial' => 'Essai',
                                                                        'expired' => 'Expiré',
                                                                        'pending' => 'En attente',
                                                                        default => 'Inactif'
                                                                    };
                                                                @endphp
                                                                <span class="badge bg-{{ $stColor }} modalStatusBadge">{{ $stLabel }}</span>
                                                            </div>
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Début</small>
                                                                    <span class="modalStart">{{ $enterprise->subscription_start_date ? formatDate($enterprise->subscription_start_date) : '—' }}</span>
                                                                </div>
                                                                <div class="col-6 text-end">
                                                                    <small class="text-muted d-block">Fin</small>
                                                                    <span class="modalEnd">{{ $enterprise->subscription_end_date ? formatDate($enterprise->subscription_end_date) : '—' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <small class="text-muted">Progression</small>
                                                                    <small class="modalProgressLabel">0%</small>
                                                                </div>
                                                                <div class="progress">
                                                                    <div class="progress-bar modalProgressBar" role="progressbar" style="width: 0%"></div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <i class="ti ti-clock me-1"></i>
                                                                <span class="modalRemaining">—</span>
                                                                <span class="badge bg-danger ms-2 modalExpireSoon d-none">Expire bientôt</span>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-outline-primary bg-primary text-white modalManageLink" href="{{ route('super-admin.enterprises.subscription', $enterprise->user_id ?? $enterprise) }}">
                                                                <i class="ti ti-settings me-1"></i>Gérer l'abonnement
                                                            </a>
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td align="center">
                                            @if($enterprise->is_active && $enterprise->hasActiveSubscription())
                                                <span class="badge bg-success">Actif</span>
                                            @elseif($enterprise->subscription_status === 'trial')
                                                <span class="badge bg-info">Essai</span>
                                            @elseif($enterprise->subscription_status === 'expired')
                                                <span class="badge bg-danger">Expiré</span>
                                            @else
                                                <span class="badge bg-warning">Inactif</span>
                                            @endif
                                        </td>
                                        <td align="center">
                                            <span class="fw-semibold">{{ $enterprise->employees_count ?? 0 }}</span>
                                            @if($enterprise->max_employees)
                                                <small class="text-muted">/ {{ $enterprise->max_employees }}</small>
                                            @endif
                                        </td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="ti ti-menu"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="position: fixed; z-index: 1050;">
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.show', $enterprise->user_id ?? $enterprise) }}">
                                                        <i class="ti ti-eye me-2"></i>
                                                        <span>Voir détails</span>
                                                    </a>
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.edit', $enterprise->user_id ?? $enterprise) }}">
                                                        <i class="ti ti-pencil-alt2 me-2"></i>
                                                        <span>Modifier</span>
                                                    </a>
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.users', $enterprise->user_id) }}">
                                                        <i class="ti ti-user me-2"></i>
                                                        <span>Gérer employés</span>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @if($enterprise->is_active == true)
                                                    <a class="dropdown-item bg-label-warning" href="{{ route('super-admin.enterprises.suspend', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette entreprise ?')">
                                                        <i class="ti ti-control-pause me-2"></i>
                                                        <span>Suspendre</span>
                                                    </a>
                                                    @else
                                                    <a class="dropdown-item bg-label-success" href="{{ route('super-admin.enterprises.activate', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir activer cette entreprise ?')">
                                                        <i class="ti ti-control-play me-2"></i>
                                                        <span>Activer</span>
                                                    </a>
                                                    @endif
                                                    <a class="dropdown-item bg-label-danger" href="{{ route('super-admin.enterprises.delete', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')">
                                                        <i class="ti ti-trash me-2"></i>
                                                        <span>Supprimer</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="ti ti-building-off fs-1 text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">Aucune entreprise trouvée</h5>
                                            <p class="text-muted">Créez votre première entreprise pour commencer.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(isset($enterprises) && $enterprises->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $enterprises->links() }}
                        </div>
                        @endif
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
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .badge {
        font-size: 0.75rem;
    }

    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 50%;
    }

    .progress {
        height: 6px;
    }

    .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(38, 61, 136, 0.15);
        border: none;
    }

    .btn-outline-secondary:hover {
        background-color: #263d88;
        border-color: #263d88;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche en temps réel (sélection robuste des contrôles)
    const searchInput = document.getElementById('searchInput') || document.querySelector('input[name="q"]');
    const statusFilter = document.getElementById('statusFilter') || document.querySelector('select[name="status"]');
    const planFilter = document.getElementById('planFilter') || document.querySelector('select[name="plan"]');

    function filterTable() {
        const searchTerm = (searchInput?.value || '').toLowerCase();
        const statusValue = statusFilter?.value || '';
        const planValue = planFilter?.value || '';
        const rows = document.querySelectorAll('#enterprisesTable tbody tr');

        rows.forEach(row => {
            if (row.cells.length < 10) return; // Skip empty row

            const companyName = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            const status = row.cells[5].textContent.toLowerCase().trim();
            const plan = row.cells[4].textContent.toLowerCase().trim();

            const matchesSearch = companyName.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesPlan = !planValue || plan.includes(planValue);

            if (matchesSearch && matchesStatus && matchesPlan) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    if (planFilter) planFilter.addEventListener('change', filterTable);

    // Gestion des entreprises
    window.suspendEnterprise = function(enterpriseId) {
        if (confirm('Êtes-vous sûr de vouloir suspendre cette entreprise ?')) {
            fetch(`/super-admin/enterprises/${enterpriseId}/suspend`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suspension de l\'entreprise');
            });
        }
    };

    window.activateEnterprise = function(enterpriseId) {
        if (confirm('Êtes-vous sûr de vouloir activer cette entreprise ?')) {
            fetch(`/super-admin/enterprises/${enterpriseId}/activate`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'activation de l\'entreprise');
            });
        }
    };

    window.deleteEnterprise = function(enterpriseId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')) {
            fetch(`/super-admin/enterprises/${enterpriseId}/ajax`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression de l\'entreprise');
            });
        }
    };

    // Helpers
    function safeParseDate(val) {
        if (!val) return null;
        // Accept ISO, ISO with offset, and 'YYYY-MM-DD HH:MM:SS'
        const iso = String(val).replace(' ', 'T');
        const d = new Date(iso);
        return isNaN(d.getTime()) ? null : d;
    }

    function computeProgress(startIso, endIso) {
        const now = new Date();
        const start = safeParseDate(startIso);
        const end = safeParseDate(endIso);
        let pct = 0;
        let remainingText = 'Aucune information d\'échéance';
        let remainingDays = null;
        if (start && end && end > start) {
            const totalMs = end - start;
            const elapsedMs = Math.max(0, now - start);
            const remainingMs = Math.max(0, end - now);
            const day = 24 * 60 * 60 * 1000;
            remainingDays = Math.ceil(remainingMs / day);
            pct = Math.min(100, Math.round((elapsedMs / totalMs) * 100));
            remainingText = remainingDays > 0 ? `${remainingDays} jour(s) restant(s)` : 'Expiré';
        }
        return { pct, remainingText, remainingDays };
    }

    function updatePackModal(modalEl, triggerBtn) {
        const ds = triggerBtn.dataset || {};
        const pack = ds.pack || triggerBtn.getAttribute('data-pack') || '—';
        const startIso = ds.start || triggerBtn.getAttribute('data-start');
        const endIso = ds.end || triggerBtn.getAttribute('data-end');
        const status = (ds.status || triggerBtn.getAttribute('data-status') || 'inactive').toLowerCase();
        const manageUrl = ds.manageUrl || triggerBtn.getAttribute('data-manage-url') || '{{ route('super-admin.enterprises.index') }}';

        const elPack = modalEl.querySelector('.modalPackName');
        const elStart = modalEl.querySelector('.modalStart');
        const elEnd = modalEl.querySelector('.modalEnd');
        const elRemain = modalEl.querySelector('.modalRemaining');
        const elBadge = modalEl.querySelector('.modalStatusBadge');
        const elProg = modalEl.querySelector('.modalProgressBar');
        const elProgLbl = modalEl.querySelector('.modalProgressLabel');
        const elManage = modalEl.querySelector('.modalManageLink');

        if (elPack) elPack.textContent = pack;

        const fmt = (d) => {
            const parsed = safeParseDate(d);
            return parsed ? parsed.toLocaleDateString('fr-FR', { year:'numeric', month:'2-digit', day:'2-digit' }) : '—';
        };
        if (elStart) elStart.textContent = fmt(startIso);
        if (elEnd) elEnd.textContent = fmt(endIso);

        const { pct, remainingText, remainingDays } = computeProgress(startIso, endIso);
        if (elProg) elProg.style.width = pct + '%';
        if (elProgLbl) elProgLbl.textContent = pct + '%';
        if (elRemain) elRemain.textContent = remainingText;

        // Progress bar color (danger < 20, warning < 60, success otherwise)
        if (elProg) {
            elProg.classList.remove('bg-success', 'bg-warning', 'bg-danger');
            if (pct < 20) elProg.classList.add('bg-danger');
            else if (pct < 60) elProg.classList.add('bg-warning');
            else elProg.classList.add('bg-success');
            elProg.setAttribute('aria-valuenow', String(pct));
            elProg.setAttribute('aria-valuemin', '0');
            elProg.setAttribute('aria-valuemax', '100');
        }

        // Expire soon badge
        const elSoon = modalEl.querySelector('.modalExpireSoon');
        if (elSoon) {
            if (remainingDays !== null && remainingDays <= 7 && remainingDays > 0) {
                elSoon.classList.remove('d-none');
            } else {
                elSoon.classList.add('d-none');
            }
        }

        // Badge color by status
        if (elBadge) {
            let color = 'secondary', label = 'Inactif';
            if (status === 'active') { color = 'success'; label = 'Actif'; }
            else if (status === 'trial') { color = 'info'; label = 'Essai'; }
            else if (status === 'expired') { color = 'danger'; label = 'Expiré'; }
            else if (status === 'pending') { color = 'warning'; label = 'En attente'; }
            elBadge.className = 'badge bg-' + color;
            elBadge.textContent = label;
        }

        if (elManage) elManage.setAttribute('href', manageUrl);
    }

    // Bind to all per-row modals (one per ligne)
    document.querySelectorAll('[id^="packInfoModal-"]').forEach(function(modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
            const triggerBtn = event.relatedTarget;
            if (!triggerBtn) return;
            updatePackModal(modalEl, triggerBtn);
        });
    });

    // Also bind on click of pack buttons to prefill immediately
    document.querySelectorAll('button[data-bs-target^="#packInfoModal-"]').forEach(function(btn){
        btn.addEventListener('click', function(){
            const targetSel = btn.getAttribute('data-bs-target');
            const modalEl = document.querySelector(targetSel);
            if (modalEl) {
                updatePackModal(modalEl, btn);
            }
        });
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
</script>
@endpush
