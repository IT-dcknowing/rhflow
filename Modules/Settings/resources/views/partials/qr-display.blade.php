@extends('layouts.app')

@section('title', 'Système de Présence - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">⏰ Système de Présence</h4>
                    <p class="text-muted mb-0">Affichage du code Qr pour la méthode de gestion des présences de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.attendance-system.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>   

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('QR Code de pointage pour') }} {{ $location->name }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code de pointage" class="img-fluid" style="max-width: {{ $size }}px;">
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>{{ __('Lieu:') }}</strong> {{ $company->name }}<br>
                        <strong>{{ __('Adresse:') }}</strong> {{ $company->address }}, {{ $company->city }}, {{ $company->country }}<br>
                        <strong>{{ __('Généré le:') }}</strong> {{ date('d/m/Y H:i', $timestamp) }}
                    </div>
                    
                    @if(isset($qrType) && $qrType === 'web_portal')
                    <div class="alert alert-success">
                        <i class="fas fa-globe mr-1"></i> 
                        {{ __('Ce QR Code est le Portail Web Collectif. Scannez-le avec un smartphone pour pointer N\'IMPORTE QUEL employé sans smartphone.') }}
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-1"></i> 
                        {{ __('Ce QR Code permet aux employés de pointer leur présence sur ce lieu via l\'application mobile.') }}
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <form action="{{ route('company.settings.attendance-system.qr-code-download') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="qr_string" value="{{ $qrString }}">
                            <input type="hidden" name="location_name" value="{{ $location->name }}">
                            <input type="hidden" name="size" value="{{ $size }}">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-download me-2"></i> {{ __('Télécharger le QR Code') }}
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-info" onclick="printQRCode();">
                            <i class="fas fa-print me-2"></i> {{ __('Imprimer') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .breadcrumb, .header-navbar, .main-menu, .btn, .alert-warning, form {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            background: none !important;
            border-bottom: 1px solid #ddd !important;
        }
        .alert-info {
            border: 1px solid #ddd !important;
            background: none !important;
            color: #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function printQRCode() {
    window.print();
}
</script>
@endpush