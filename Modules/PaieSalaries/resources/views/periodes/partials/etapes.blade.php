{{--
    Barre des 4 étapes de la paie.
    $courante   : index de l'étape en cours (0 à 3)
    $faites     : nombre d'étapes terminées
    $cliquables : true quand on peut passer de « Préparer » à « Vérifier » sur la même page
--}}
@php
    $etapesPaie = [
        ['Préparer', 'Éléments du mois', 'Éléments prêts'],
        ['Vérifier', 'Montants par salarié', 'Montants vérifiés'],
        ['Générer', 'Bulletins de paie', 'Bulletins générés'],
        ['Payer', 'Paiement et documents', 'Paiement validé'],
    ];
@endphp
<ol class="pm-steps" aria-label="Étapes de la paie">
    @foreach($etapesPaie as $i => $etapePaie)
        @php $etatEtape = $i < $faites ? 'done' : ($i === $courante ? 'current' : ''); @endphp
        <li class="pm-step {{ $etatEtape }}">
            <button type="button" data-step="{{ $i }}" {{ ($cliquables && $i <= 1) ? '' : 'disabled' }}
                @if($etatEtape === 'current') aria-current="step" @endif>
                <span class="pm-step-num"><span>{{ $i + 1 }}</span><i class="fas fa-check"></i></span>
                <span>
                    <span class="pm-step-label">{{ $etapePaie[0] }}</span>
                    <span class="pm-step-sub" data-todo="{{ $etapePaie[1] }}" data-done="{{ $etapePaie[2] }}">{{ $etatEtape === 'done' ? $etapePaie[2] : $etapePaie[1] }}</span>
                </span>
            </button>
        </li>
    @endforeach
</ol>
