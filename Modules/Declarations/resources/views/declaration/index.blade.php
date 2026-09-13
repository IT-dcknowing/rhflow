@extends('layouts.app')

@section('title', 'Gestion des Livres de Paie')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des Bulletins de Paie</h4>
                        <p class="text-muted mb-0">Consultez et modifiez les bulletins de paie générés</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Tous les bulletins</a></li>
                                <li><a class="dropdown-item" href="#">Bulletins validés</a></li>
                                <li><a class="dropdown-item" href="#">Bulletins générés</a></li>
                                <li><a class="dropdown-item" href="#">Bulletins brouillons</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Mois en cours</a></li>
                                <li><a class="dropdown-item" href="#">Année {{ now()->year }}</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-primary"
                            onclick="window.location.href='{{ url('company/declarations/resume/create') }}'">
                            <i class="fas fa-plus me-1"></i>Nouveau bulletin
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')

@endpush