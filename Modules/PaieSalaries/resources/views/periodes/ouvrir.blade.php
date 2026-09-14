@extends('layouts.app')

@section('title', 'Paie du mois : ' . $nomMois)

@include('paiesalaries::periodes.partials.styles')

@php
    // « de » ou « d' » devant le nom du mois : d'avril, d'août, d'octobre
    $deMois = function ($libelle) {
        $libelle = lcfirst($libelle);
        return (preg_match('/^[aeiou]/i', $libelle) ? "d'" : 'de ') . $libelle;
    };

    $libellesStatut = [
        'brouillon' => ['warn', 'En préparation'],
        'en_cours' => ['warn', 'En préparation'],
        'validee' => ['navy', 'Bulletins générés'],
        'payee' => ['ok', 'Payée'],
        'cloture' => ['ok', 'Clôturée'],
        'annulee' => ['bad', 'Annulée'],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <form class="pm" id="pmFormOuvrir" method="POST" action="{{ route('company.paiesalaries.periodes.store', $exercice->id) }}">
            @csrf
            <input type="hidden" name="nom" value="{{ $nomMois }}">
            <input type="hidden" name="type_periode" value="mensuelle">

            {{-- En-tête --}}
            <div class="pm-head">
                <div>
                    <div class="pm-eyebrow">Paie mensuelle · Exercice {{ $exercice->nom }}</div>
                    <div class="pm-title">
                        <h1>{{ $nomMois }}</h1>
                        <span class="pm-chip faint">Non ouverte</span>
                    </div>
                    <div class="pm-meta">
                        <span class="pm-mono"><i class="fas fa-calendar-alt"></i>{{ $debut->format('d/m/Y') }} → {{ $fin->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @include('paiesalaries::periodes.partials.etapes', ['courante' => 0, 'faites' => 0, 'cliquables' => false])

            @if($errors->any())
                <div class="pm-alert danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <section class="pm-open">
                <div>
                    <h2>La paie {{ $deMois($nomMois) }} n'est pas encore ouverte</h2>
                    <p>
                        @if($dernierePeriode)
                            Les dates sont déjà remplies. En ouvrant la paie, les primes, retenues, échéances de prêt et
                            avantages de {{ $dernierePeriode->nom }} sont repris automatiquement : vous n'aurez qu'à ajuster
                            ce qui change.
                        @else
                            Les dates sont déjà remplies. C'est la première paie : vous ajouterez les primes et les retenues
                            à l'étape suivante.
                        @endif
                    </p>
                    <div class="pm-dates">
                        <div>
                            <label class="form-label" for="date_debut">Début</label>
                            <input type="date" class="form-control pm-mono" id="date_debut" name="date_debut" required
                                value="{{ old('date_debut', $debut->format('Y-m-d')) }}"
                                min="{{ $exercice->date_debut->format('Y-m-d') }}" max="{{ $exercice->date_fin->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="form-label" for="date_fin">Fin</label>
                            <input type="date" class="form-control pm-mono" id="date_fin" name="date_fin" required
                                value="{{ old('date_fin', $fin->format('Y-m-d')) }}"
                                min="{{ $exercice->date_debut->format('Y-m-d') }}" max="{{ $exercice->date_fin->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="form-label" for="date_paiement">Paiement</label>
                            <input type="date" class="form-control pm-mono" id="date_paiement" name="date_paiement"
                                value="{{ old('date_paiement', $fin->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="pm-last">
                    <div class="pm-eyebrow">Dernière paie</div>
                    @if($dernierePeriode)
                        @php $statutDernier = $libellesStatut[$dernierePeriode->statut] ?? ['faint', ucfirst($dernierePeriode->statut)]; @endphp
                        <div class="pm-row"><span>Période</span><b>{{ $dernierePeriode->nom }}</b></div>
                        <div class="pm-row"><span>Statut</span><b><span class="pm-chip {{ $statutDernier[0] }}">{{ $statutDernier[1] }}</span></b></div>
                        <div class="pm-row"><span>Bulletins</span><b class="pm-mono">{{ $dernierePeriode->bulletins_count }}</b></div>
                        <div class="pm-row"><span>Net versé</span><b class="pm-mono">{{ number_format((float) $dernierePeriode->bulletins_sum_net, 0, ',', ' ') }} FCFA</b></div>
                        <div class="pm-row">
                            <span></span>
                            <a class="pm-link" href="{{ route('company.paiesalaries.periodes.show', $dernierePeriode->id) }}">Voir cette paie</a>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune paie enregistrée pour le moment.</p>
                    @endif
                </div>
            </section>

            <div class="pm-actionbar">
                <div class="pm-ctx">
                    @if($dernierePeriode)
                        Reprise automatique de <b>{{ $dernierePeriode->nom }}</b> à l'ouverture
                    @else
                        Première paie de l'exercice <b>{{ $exercice->nom }}</b>
                    @endif
                </div>
                <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}">
                    Autre type de période
                </a>
                <button type="submit" class="btn btn-primary pm-btn-main" id="pmBtnOuvrir">
                    Ouvrir la paie {{ $deMois($nomMois) }} <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Un seul envoi : un double clic créait la même période deux fois
        document.getElementById('pmFormOuvrir').addEventListener('submit', function () {
            var bouton = document.getElementById('pmBtnOuvrir');
            bouton.disabled = true;
            bouton.textContent = 'Ouverture en cours…';
        });
    </script>
@endpush
