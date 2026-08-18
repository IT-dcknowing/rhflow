@extends('layouts.error')

@section('title', 'Page Non Trouvée - 404')

@section('content')
    <div class="icon-container">
        <i class="fas fa-search"></i>
    </div>
    
    <div class="error-code">404</div>
    <h1 class="error-title">Page Introuvable</h1>
    
    <p class="error-desc">
        Désolé, la page que vous recherchez semble avoir disparu ou l'adresse saisie n'est plus valide. 
        Utilisez le bouton ci-dessous pour retourner en lieu sûr.
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
