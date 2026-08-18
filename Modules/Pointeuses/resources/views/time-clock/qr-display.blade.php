@extends('layouts.admin')

@section('page-title')
    {{ __('QR Code de pointage pour') }} {{ $location->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('qr-code.index') }}">{{ __('QR Codes') }}</a></li>
    <li class="breadcrumb-item">{{ $location->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('QR Code de pointage pour') }} {{ $location->name }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code de pointage" class="img-fluid" style="max-width: {{ $size }}px;">
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>{{ __('Lieu:') }}</strong> {{ \App\Models\Utility::getValByName('company_name') }}<br>
                        <strong>{{ __('Adresse:') }}</strong> {{ \App\Models\Utility::getValByName('company_address') }}, {{ \App\Models\Utility::getValByName('company_city') }}, {{ \App\Models\Utility::getValByName('company_country') }}<br>
                        <strong>{{ __('Généré le:') }}</strong> {{ date('d/m/Y H:i', $timestamp) }}
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-1"></i> 
                        {{ __('Ce QR Code permet aux employés de pointer leur présence sur ce lieu via l\'application mobile.') }}
                    </div>
                    
                    <div class="mt-4">
                        <form action="{{ route('qr-code.download') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="qr_string" value="{{ $qrString }}">
                            <input type="hidden" name="location_name" value="{{ $location->name }}">
                            <input type="hidden" name="size" value="{{ $size }}">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-download me-2"></i> {{ __('Télécharger le QR Code') }}
                            </button>
                        </form>
                        
                        <a href="{{ route('qr-code.index') }}" class="btn btn-secondary me-3">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('Retour') }}
                        </a>

                        <button type="button" class="btn btn-info" onclick="window.print();">
                            <i class="fas fa-print me-2"></i> {{ __('Imprimer') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
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
@endsection