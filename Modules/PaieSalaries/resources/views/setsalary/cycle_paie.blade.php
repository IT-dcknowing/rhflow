@php
    use Carbon\Carbon;
    // Définir la locale en français
    setlocale(LC_TIME, 'fr_FR.utf8');
    $date = Carbon::parse($monthpaie);
@endphp

@extends('layouts.admin')

@section('page-title')
    {{ __('Cycle du traitement de paie') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Traitement de paie') }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!--<div class="row mb-4">
            <div class="col-md-9">
                <a href="{{ route('payslip.payroll') }}" class="btn btn-outline-primary">
                    <i class="ti ti-arrow-left me-1"></i>{{ __('Retour') }}
                </a>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('payslip.payroll') }}" class="btn btn-warning">
                    {{ __('Changer de mois') }} <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
        </div>-->

        <div class="alert alert-info">
            <h6 class="card-subtitle mb-4 text-muted fst-italic">
                Choisissez le type d'employés que vous souhaitez gérer. Chaque catégorie a ses propres spécificités en termes de gestion de paie et de suivi.
            </h6>
        </div>
        <div class="row">
            <!--<div class="col-md-12 mb-4">
                <div class="card bg-success text-white" style="padding:10px;">
                    <h5 class="mb-0" style="color:#fff;">{{ __('Mois de traitement : ') }} <strong>{{$date->formatLocalized('%B %Y')}}</strong></h5>
                </div>
            </div>-->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-4">Gestion des Journaliers</h5>
                        <img src="{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="Journaliers" class="img-fluid rounded mb-4" style="max-height: 200px;"><br>
                        <a href="{{ route('payslip.payrolldays') }}" class="btn btn-primary">Gérer</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-4">Gestion des Mensuels</h5>
                        <img src="{{ asset('assets/img/illustrations/boy-with-laptop-light.png') }}" alt="Mensuels" class="img-fluid rounded mb-4" style="max-height: 200px;"><br>
                        <a href="{{ route('payslip.payroll') }}" class="btn btn-primary">Gérer</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-img mb-4 text-end">
            @for ($i = 1; $i <= 9; $i++)
                <div id="step{{ $i }}" class="step">
                    <i class="quote mb-3"></i>
                    <!--img src="" alt="Image aléatoire" class="random-image card-img-top rounded">-->
            </div>
            @endfor
        </div>
    </div>
@endsection
<script src="http://thecodeplayer.com/uploads/js/prefixfree-1.0.7.js" type="text/javascript" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    $(document).ready(function(){
        var steps = ['#step1', '#step2', '#step3', '#step4', '#step5', '#step6', '#step7', '#step8', '#step9'];
        var i = 0;
        var quotes = [
            "La seule façon de faire du bon travail est d'aimer ce que vous faites.\n- Steve Jobs",
            "Le succès n'est pas la clé du bonheur. Le bonheur est la clé du succès.\n- Albert Schweitzer",
            "La réussite, c'est un peu de savoir, un peu de savoir-faire et beaucoup de faire-savoir.\n- Jean Nohain",
            "Ne jugez pas chaque jour à la récolte que vous faites, mais aux graines que vous plantez.\n- Robert Louis Stevenson",
            "Le seul endroit où le succès vient avant le travail, c'est dans le dictionnaire.\n- Vidal Sassoon",
            "La vie est ce que vous en faites.\n- Eleanor Roosevelt",
            "La meilleure façon de prédire l'avenir est de le créer.\n- Peter Drucker"
        ];

        function showStep() {
            $(steps[i]).fadeIn(1000, function() {
                setTimeout(function() {
                    $(steps[i]).fadeOut(1000, function() {
                        i = (i + 1) % steps.length; // Boucler à travers les étapes
                        showStep();
                    });
                }, 6000);
            });

            // Mettre à jour l'image aléatoire et la citation
            updateRandomImageAndQuote(steps[i]);
        }

        function updateRandomImageAndQuote(step) {
            var randomNumber = Math.floor(Math.random() * 1000); // Générer un nombre aléatoire
            $(step).find('.random-image').attr('src', 'https://picsum.photos/1000/250?random=' + randomNumber);

            var randomQuoteIndex = Math.floor(Math.random() * quotes.length); // Générer un index aléatoire pour la citation
            var randomQuote = quotes[randomQuoteIndex];

            $(step).find('.quote').fadeOut(1000, function() {
                $(this).text(randomQuote).fadeIn(1000);
            });
        }

        showStep();
    });
</script>

