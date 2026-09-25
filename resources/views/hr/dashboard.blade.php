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
        <x-quick-actions>
            @if(in_array('employes', $sections) && isModuleActive('employee'))
                <x-quick-action-group title="EMPLOYÉS" icon="fas fa-users" color="primary" minWidth="220px">
                    <x-quick-action icon="fas fa-folder" label="Dossiers personnel"
                        :href="route('company.employees.dossiers.index')" variant="primary" />
                    <x-quick-action icon="fas fa-inbox" label="Demandes"
                        :href="route('company.employees.demandes.index')" variant="outline" color="primary" />
                </x-quick-action-group>
            @endif

            @if(in_array('paie', $sections) && isModuleActive('salary'))
                <x-quick-action-divider />
                <x-quick-action-group title="PAIE & RETENUES" icon="fas fa-dollar" color="success" minWidth="180px">
                    <x-quick-action icon="fas fa-money-bill-wave" label="Paie et retenues"
                        :href="route('company.paiesalaries.dashboard')" variant="primary" color="success" />
                </x-quick-action-group>
            @endif

            @if(in_array('declarations', $sections) && isModuleActive('declaration'))
                <x-quick-action-divider />
                <x-quick-action-group title="DÉCLARATIONS" icon="fas fa-tasks" color="warning" minWidth="240px">
                    <x-quick-action icon="fas fa-file-alt" label="Bulletins de paie"
                        :href="route('company.declarations.resume.index')" variant="outline" color="warning" />
                    <x-quick-action icon="fas fa-landmark" label="Déclarations"
                        :href="route('company.declarations.dashboard')" variant="outline" color="secondary" />
                </x-quick-action-group>
            @endif

            @if(in_array('evenements', $sections) || in_array('simulateur', $sections))
                <x-quick-action-divider />
                <x-quick-action-group title="OUTILS" icon="fas fa-cog" color="secondary" end>
                    @if(in_array('evenements', $sections) && isModuleActive('event'))
                        <x-quick-action icon="fas fa-calendar-alt" label="Événements"
                            :href="route('company.evenements.dashboard')" variant="outline" color="secondary" />
                    @endif
                    @if(in_array('simulateur', $sections))
                        <x-quick-action icon="fas fa-calculator" label="Simulateur"
                            :href="route('company.simulator.dashboard')" variant="outline" color="secondary" />
                    @endif
                </x-quick-action-group>
            @endif
        </x-quick-actions>

    @endif
</div>
@endsection
