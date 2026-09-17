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
        <x-kpi-grid>
            <x-kpi icon="fas fa-users" color="primary" label="Employés actifs"
                sublabel="{{ number_format($stats['total_employees'], 0, ',', ' ') }} au total"
                :value="number_format($stats['active_employees'], 0, ',', ' ')" />

            <x-kpi icon="fas fa-file-invoice" color="info" label="Bulletins du mois"
                sublabel="{{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('MMMM YYYY')) }}"
                :value="number_format($stats['payslips_month'], 0, ',', ' ')" />

            <x-kpi icon="fas fa-inbox" color="warning" label="Demandes" sublabel="En attente"
                :value="number_format($stats['pending_requests'], 0, ',', ' ')" />

            <x-kpi icon="fas fa-calendar-alt" color="success" label="Événements" sublabel="À venir"
                :value="number_format($stats['upcoming_events'], 0, ',', ' ')" />
        </x-kpi-grid>

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
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-folder"></i>
                                <span>Dossiers du personnel</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('company.employees.demandes.index') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-inbox"></i>
                                <span>Demandes employés</span>
                            </a>
                        </div>
                    @endif

                    @if(in_array('paie', $sections) && isModuleActive('salary'))
                        <div class="col-md-4">
                            <a href="{{ route('company.paiesalaries.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-money-bill"></i>
                                <span>Paie et retenues</span>
                            </a>
                        </div>
                    @endif

                    @if(in_array('declarations', $sections) && isModuleActive('declaration'))
                        <div class="col-md-4">
                            <a href="{{ route('company.declarations.resume.index') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-file-alt"></i>
                                <span>Bulletins de paie</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('company.declarations.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-landmark"></i>
                                <span>Déclarations</span>
                            </a>
                        </div>
                    @endif

                    @if(in_array('evenements', $sections) && isModuleActive('event'))
                        <div class="col-md-4">
                            <a href="{{ route('company.evenements.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Événements</span>
                            </a>
                        </div>
                    @endif

                    @if(in_array('simulateur', $sections))
                        <div class="col-md-4">
                            <a href="{{ route('company.simulator.dashboard') }}"
                                class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                                <i class="fas fa-calculator"></i>
                                <span>Simulateur</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @endif
</div>
@endsection
