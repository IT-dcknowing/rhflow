@extends('layouts.error')

@section('title', 'Session Expirée - 419')

@section('content')
    <div class="icon-container">
        <i class="fas fa-clock"></i>
    </div>
    
    <div class="error-code">419</div>
    <h1 class="error-title">Session Expirée</h1>
    
    <p class="error-desc">
        Désolé, votre session semble avoir expiré pour des raisons de sécurité. 
        Veuillez actualiser la page et vous reconnecter si nécessaire.
    </p>

    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
        <a href="{{ url()->current() }}" class="btn-premium">
            <i class="fas fa-sync"></i> Actualiser la page
        </a>
        <a href="{{ url('/') }}" class="btn-premium" style="background: rgba(255,255,255,0.1); box-shadow: none;">
            <i class="fas fa-home"></i> Accueil
        </a>
    </div>
@endsection
