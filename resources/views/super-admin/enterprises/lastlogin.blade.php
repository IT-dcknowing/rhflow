@extends('layouts.super-admin')

@section('title', 'Dernières Connexions Entreprises')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <!-- En-tête avec navigation -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-primary">
                            <i class="ti ti-user me-2"></i>Dernières Connexions des Entreprises
                        </h5>
                        <small class="text-muted">
                            {{ $enterprises->count() }} entreprises
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-outline-primary bg-label-primary">
                            <i class="ti ti-arrow-left me-1"></i>Toutes les entreprises
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if($enterprises->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Entreprise</th>
                                                <th>Email</th>
                                                <th>Dernière connexion</th>
                                                <th>Statut</th>
                                                <th>Plan</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($enterprises as $enterprise)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-xs me-3">
                                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                                    {{ substr($enterprise->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $enterprise->name }}</h6>
                                                                <small class="text-muted">ID: {{ $enterprise->id }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $enterprise->email }}</td>
                                                    <td>
                                                        @if($enterprise->last_login_at)
                                                            <span title="{{ $enterprise->last_login_at->format('d/m/Y H:i:s') }}">
                                                                {{ $enterprise->last_login_at->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Jamais connecté</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $enterprise->is_active ? 'success' : 'danger' }}">
                                                            {{ $enterprise->is_active ? 'Actif' : 'Inactif' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $enterprise->userPlan->name == 'Gratuit' ? 'primary' : ($enterprise->userPlan->name == 'Basic' ? 'info' :($enterprise->userPlan->name == 'Pro' ? 'success' : 'danger')) }}">{{ $enterprise->userPlan->name ?? 'N/A' }}</span>
                                                    </td>
                                                    <td align="center">
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('super-admin.enterprises.show', $enterprise) }}"
                                                            class="btn btn-sm btn-outline-info">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                            <a href="{{ route('super-admin.enterprises.showactivity', $enterprise) }}" class="btn btn-sm btn-outline-danger" title="Voir l'activité">
                                                                <i class="ti ti-cloud-up"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $enterprises->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-users ti-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Aucune entreprise trouvée</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshData() {
    window.location.reload();
}
</script>
@endsection
