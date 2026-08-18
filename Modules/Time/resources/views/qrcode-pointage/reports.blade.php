@php
    use Carbon\Carbon;
@endphp
@extends('layouts.app')

@section('page-title')
    {{ __('Rapports & Statistiques') }}
@stop

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📊 Rapports & Statistiques des Présences</h4>
                    <p class="text-muted mb-0">Analysez l'assiduité, les retards et les absences de vos employés.</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.times.qrcode-pointage.index') }}">Présences</a>
                            </li>
                            <li class="breadcrumb-item active">Rapports</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <form method="GET" action="{{ route('company.times.qrcode-pointage.reports') }}" class="d-flex gap-2">
                        <select name="period" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="current_month" {{ $period == 'current_month' ? 'selected' : '' }}>Mois en cours ({{ Carbon::now()->locale('fr')->isoFormat('MMMM YYYY') }})</option>
                            <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Mois précédent ({{ Carbon::now()->subMonth()->locale('fr')->isoFormat('MMMM YYYY') }})</option>
                            <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Année en cours ({{ Carbon::now()->year }})</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Absences -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-top-danger border-top-3">
                <div class="card-header bg-white pb-0">
                    <h5 class="card-title text-danger mb-0"><i class="fas fa-user-times me-2"></i>Top 10 Absences</h5>
                    <small class="text-muted">Jours ouvrés sans pointage</small>
                </div>
                <div class="card-body mt-3">
                    @if(count($topAbsences) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topAbsences as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-danger rounded-circle d-flex justify-content-center align-items-center">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $item['employee']->name }}</span>
                                            <small class="text-muted">{{ $item['employee']->designation->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">{{ $item['count'] }} jour(s)</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted mt-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                            <p>Aucune absence injustifiée sur cette période.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Retards -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-top-warning border-top-3">
                <div class="card-header bg-white pb-0">
                    <h5 class="card-title text-warning mb-0"><i class="fas fa-clock me-2"></i>Top 10 Retards</h5>
                    <small class="text-muted">Arrivées après l'heure limite du site</small>
                </div>
                <div class="card-body mt-3">
                    @if(count($topLates) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topLates as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-warning rounded-circle d-flex justify-content-center align-items-center">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $item['employee']->name }}</span>
                                            <small class="text-muted">{{ $item['employee']->designation->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning rounded-pill">{{ $item['count'] }} retard(s)</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted mt-4">
                            <i class="fas fa-medal fa-3x text-success mb-2"></i>
                            <p>Aucun retard enregistré sur cette période.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Heures Travaillées -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-top-success border-top-3">
                <div class="card-header bg-white pb-0">
                    <h5 class="card-title text-success mb-0"><i class="fas fa-star me-2"></i>Top 10 Assiduité</h5>
                    <small class="text-muted">Total des heures travaillées</small>
                </div>
                <div class="card-body mt-3">
                    @if(count($topWorkers) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topWorkers as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-success rounded-circle d-flex justify-content-center align-items-center">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $item['employee']->name }}</span>
                                            <small class="text-muted">{{ $item['employee']->designation->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success rounded-pill">{{ $item['time'] }}h</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted mt-4">
                            <i class="fas fa-bed fa-3x text-secondary mb-2"></i>
                            <p>Aucune heure enregistrée sur cette période.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-top-3 {
        border-top-width: 3px !important;
    }
    .border-top-danger { border-top-color: #ff3e1d !important; }
    .border-top-warning { border-top-color: #ffab00 !important; }
    .border-top-success { border-top-color: #71dd37 !important; }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-weight: bold;
    }
</style>
@endpush
