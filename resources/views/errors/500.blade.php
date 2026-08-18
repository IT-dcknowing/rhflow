@extends('layouts.error')

@section('title', 'Erreur Serveur - 500')

@section('content')
    <div class="icon-container">
        <i class="fas fa-server"></i>
    </div>
    
    <div class="error-code">500</div>
    <h1 class="error-title">Incident Serveur Interne</h1>
    
    <p class="error-desc">
        Oups ! Une erreur inattendue s'est produite sur nos serveurs. 
        Notre équipe technique a été alertée et travaille déjà à la résolution du problème. 
        Merci de votre patience.
    </p>

    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
        <a href="{{ url()->previous() }}" class="btn-premium">
            <i class="fas fa-arrow-left"></i> Retour en arrière
        </a>
        <a href="{{ url('/') }}" class="btn-premium" style="background: rgba(255,255,255,0.1); box-shadow: none;">
            <i class="fas fa-home"></i> Accueil
        </a>
    </div>

    @if(config('app.debug'))
        <div class="debug-info">
            <div class="debug-header" onclick="toggleDebug()">
                <span><i class="fas fa-bug me-2"></i> Informations Techniques (Mode Débogage)</span>
                <i class="fas fa-chevron-down" id="debugIcon" style="transition: transform 0.3s;"></i>
            </div>
            <div class="debug-content" id="debugContent">
                <div class="mb-3">
                    <strong class="text-white d-block mb-1">Message d'erreur :</strong>
                    {{ $exception->getMessage() }}
                </div>
                <div class="mb-3">
                    <strong class="text-white d-block mb-1">Fichier :</strong>
                    {{ $exception->getFile() }} (Ligne {{ $exception->getLine() }})
                </div>
                <div>
                    <strong class="text-white d-block mb-1">Trace :</strong>
                    <pre style="white-space: pre-wrap; font-size: 0.75rem; color: #94a3b8;">{{ $exception->getTraceAsString() }}</pre>
                </div>
            </div>
        </div>
    @endif
@endsection
