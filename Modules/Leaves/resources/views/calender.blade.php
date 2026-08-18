
@extends('layouts.app')

@section('title', 'Gestion des Congés - RH Flow')

@push('styles')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la Gestion des Congés -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏖️ Gestion des Congés
                        @if($periode) | Exercice :
                         {{ $periode->exercice->nom }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($periode->exercice->statut) }}
                        @endif
                    </h4>
                    <p class="text-muted mb-0">Gérez les demandes de congés et les absences des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Gestion des congés @if($periode) - Période : {{ $periode->nom }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span> @endif</li>
                        </ol>
                    </nav>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div class="d-flex gap-2"> 
                    @if($periode)
                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button" class="align-items-center btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Période
                    </a>
                    <!--<a href="{{ route('company.leaves.calendar') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-1"></i>Vue Calendrier
                    </a>-->
                    <a href="{{ route('company.leaves.create') }}?periode_id={{$periode->id}}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>
@endpush