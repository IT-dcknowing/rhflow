@extends('layouts.super-admin')

@section('title', 'Gestion des Modules - Pack: ' . $pack->name)

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
                        Modules pour le Pack: {{ $pack->name }}
                    </h5>
                    <small class="text-muted">
                        Gérez les modules associés à ce pack d'abonnement
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn bg-label-primary" href="{{ route('super-admin.packs.show', $pack) }}">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour au Pack
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('super-admin.packs.modules.update', $pack) }}">
                            @csrf
                            <div class="row">
                                @foreach($allModules as $module)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="modules[]" value="{{ $module->id }}"
                                                       id="module_{{ $module->id }}"
                                                       {{ $packModules->where('id', $module->id)->isNotEmpty() ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_{{ $module->id }}">
                                                    <strong>{{ $module->name }}</strong>
                                                    @if($module->description)
                                                    <br><small class="text-muted">{{ $module->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn bg-primary text-white">
                                    <i class="ti ti-check me-2"></i>
                                    Mettre à jour les Modules
                                </button>
                            </div>
                        </form>
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

    .form-check-input:checked {
        background-color: #263d88;
        border-color: #263d88;
    }
</style>
@endpush
