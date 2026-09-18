{{--
    Tuile KPI unique : avatar coloré et libellé en haut, grand chiffre de la même
    couleur en dessous. Le chiffre est poussé en bas de la tuile (mt-auto), si
    bien qu'une rangée de tuiles aligne ses montants même quand les libellés
    tiennent sur un nombre de lignes différent.

    À placer dans un <x-kpi-grid>, qui fournit la carte et la rangée.

    Exemple :
        <x-kpi icon="fas fa-users" color="primary" label="Total"
               sublabel="Utilisateurs" :value="$stats['total']" />

    La valeur peut aussi être passée en contenu, pour du balisage :
        <x-kpi icon="fas fa-coins" color="success" label="Masse salariale">
            {{ number_format($masse, 0, ',', ' ') }} <small>FCFA</small>
        </x-kpi>
--}}
@props([
    'icon' => 'fas fa-chart-simple',
    'color' => 'primary',
    'label' => '',
    'sublabel' => null,
    'value' => null,
    'col' => 'col-xl-3 col-md-6',
])

{{-- Les attributs restants (id, data-*) vont sur la colonne : certains écrans y accrochent le guide ou du JS. --}}
<div {{ $attributes->merge(['class' => $col . ' mb-4']) }}>
    <div class="d-flex flex-column p-3 border rounded h-100">
        {{-- En-tête : l'icône et le libellé, sur une seule ligne. --}}
        <div class="d-flex align-items-center">
            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                <div class="avatar-initial bg-label-{{ $color }} rounded">
                    <i class="{{ $icon }} fa-24px"></i>
                </div>
            </div>
            <div style="min-width: 0;">
                <h6 class="mb-0">{{ $label }}</h6>
                @if ($sublabel)
                    <small class="text-muted">{{ $sublabel }}</small>
                @endif
            </div>
        </div>

        {{-- Le montant, sous le libellé et calé en bas de la tuile. --}}
        <div class="mt-auto pt-3">
            {{-- trim() et non $slot->isEmpty() : les retours à la ligne autour d'un slot nommé rendent le slot par défaut non vide. --}}
            <h3 class="mb-0 text-{{ $color }}">
                @if (trim($slot) !== '')
                    {{ $slot }}
                @else
                    {{ $value }}
                @endif
            </h3>
            {{-- « hint » : ligne libre sous le montant (évolution, lien de renvoi...). --}}
            @isset($hint)
                <div class="mt-1" style="font-size: .75rem;">{{ $hint }}</div>
            @endisset
        </div>
    </div>
</div>
