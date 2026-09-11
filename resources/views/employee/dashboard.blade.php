@extends('layouts.app')

@section('title', 'Tableau de bord Employé')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Mon tableau de bord</h4>
                    <p class="text-muted mb-0">Bienvenue, {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-bell me-1"></i>Notifications
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
