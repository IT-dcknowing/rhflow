@extends('layouts.error')

@section('title', 'Accès Refusé - 403')

@section('content')
    <div class="icon-container">
        <i class="fas fa-lock"></i>
    </div>
    
    <div class="error-code">403</div>
    <h1 class="error-title">Accès Interdit</h1>
    
    <p class="error-desc">
        Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette ressource. 
        Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur.
    </p>

    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
        <a href="{{ url('/') }}" class="btn-premium">
            <i class="fas fa-home"></i> Retour à l'accueil
        </a>
        <a href="{{ url()->previous() }}" class="btn-premium" style="background: rgba(255,255,255,0.1); box-shadow: none;">
            <i class="fas fa-arrow-left"></i> Page précédente
        </a>
    </div>
@endsection
