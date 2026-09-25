{{--
    Tuile KPI : grand chiffre en haut, libelle + sous-libelle juste en dessous.
    L icone est affichee en petit en haut a droite pour garder le contexte visuel.

    A placer dans un <x-kpi-grid>, qui fournit la carte et la rangee.

    Exemple :
        <x-kpi icon="fas fa-users" color="primary" label="Total"
               sublabel="Utilisateurs" :value="$stats['total']" />

    La valeur peut aussi etre passee en contenu, pour du balisage :
        <x-kpi icon="fas fa-coins" color="success" label="Masse salariale">
            {{ number_format($masse, 0, ',', ' ') }} <small>FCFA</small>
        </x-kpi>
--}}
@props([
    'icon'     => 'fas fa-chart-simple',
    'color'    => 'primary',
    'label'    => '',
    'sublabel' => null,
    'value'    => null,
    'col'      => 'col-xl-3 col-md-6',
])

<div {{ $attributes->merge(['class' => $col . ' mb-4']) }}>
    <div class="d-flex flex-column p-3 border rounded h-100">

        {{-- Ligne du haut : chiffre principal + icone a droite.
             Le chiffre etait coupe par une ellipse des qu il depassait la largeur
             restante : une masse salariale a huit chiffres s affichait « 76 411 ... ».
             Il se reduit maintenant plutot que d etre tronque, et peut passer a la
             ligne en dernier recours. L icone reste, pour ne pas depareiller les
             autres tuiles. --}}
        <div class="d-flex align-items-start justify-content-between gap-2">
            <h3 class="mb-0 text-{{ $color }} fw-bold kpi-value">
                @if (trim($slot) !== '')
                    {{ $slot }}
                @else
                    {{ $value }}
                @endif
            </h3>
            <div class="avatar avatar-sm" style="width:36px;height:36px;flex-shrink:0;">
                <div class="avatar-initial bg-label-{{ $color }} rounded">
                    <i class="{{ $icon }}" style="font-size:.85rem;"></i>
                </div>
            </div>
        </div>

        {{-- Label + sublabel juste en dessous --}}
        <div class="mt-2">
            <h6 class="mb-0">{{ $label }}</h6>
            @if ($sublabel)
                <small class="text-muted">{{ $sublabel }}</small>
            @endif
        </div>

        {{-- hint : ligne libre sous le label --}}
        @isset($hint)
            <div class="mt-1" style="font-size:.75rem;">{{ $hint }}</div>
        @endisset

    </div>
</div>