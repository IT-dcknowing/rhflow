@extends('layouts.app')

@section('title', $estPaie ? 'Tableau de bord Paie' : 'Tableau de bord RH')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $estPaie ? '💰 Tableau de bord Paie' : '👥 Tableau de bord RH' }}</h4>
                    <p class="text-muted mb-0">Bienvenue, {{ $user->name }}</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-bell me-1"></i>Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
    <hr>

    @if(!$rattache)
        <div class="alert alert-warning">
            <h6 class="alert-heading mb-1"><i class="fas fa-exclamation-triangle me-1"></i>Compte non rattaché à une entreprise</h6>
            <p class="mb-0">
                Ce compte n'a pas d'entreprise associée, aucune donnée ne peut donc être affichée.
                Demandez à un administrateur de rouvrir votre fiche dans
                <strong>Paramètres &rsaquo; Utilisateurs</strong> et de l'enregistrer : le rattachement sera rétabli.
            </p>
        </div>
    @else

        <!-- Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="fas fa-users"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['active_employees'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">Employés actifs</small>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ number_format($stats['total_employees'], 0, ',', ' ') }} au total
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info"><i class="fas fa-file-invoice"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['payslips_month'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">Bulletins du mois</small>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('MMMM YYYY')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="fas fa-inbox"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['pending_requests'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">Demandes en attente</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['upcoming_events'], 0, ',', ' ') }}</h4>
                            <small class="text-muted">Événements à venir</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accès rapides, selon le rôle -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Accès rapides</h5>
            </div>
            <div class="card-body">
                {{-- Les sections proposées suivent config/menu_sections.php --}}
                <div class="row g-3">
                    @if(in_array('employes', $sections) && isModuleActive('employee'))
                        <div class="col-md-4">
                            <a href="{{ route('company.employees.dossiers.index') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-folder me-2"></i>Dossiers du personnel
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('company.employees.demandes.index') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-inbox me-2"></i>Demandes employés
                            </a>
                        </div>
                    @endif

                    @if(in_array('paie', $sections) && isModuleActive('salary'))
                        <div class="col-md-4">
                            <a href="{{ route('company.paiesalaries.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-money-bill me-2"></i>Paie et retenues
                            </a>
                        </div>
                    @endif

                    @if(in_array('declarations', $sections) && isModuleActive('declaration'))
                        <div class="col-md-4">
                            <a href="{{ route('company.declarations.resume.index') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-file-alt me-2"></i>Bulletins de paie
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('company.declarations.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-landmark me-2"></i>Déclarations
                            </a>
                        </div>
                    @endif

                    @if(in_array('evenements', $sections) && isModuleActive('event'))
                        <div class="col-md-4">
                            <a href="{{ route('company.evenements.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-calendar-alt me-2"></i>Événements
                            </a>
                        </div>
                    @endif

                    @if(in_array('simulateur', $sections))
                        <div class="col-md-4">
                            <a href="{{ route('company.simulator.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-calculator me-2"></i>Simulateur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @endif
</div>
@endsection
