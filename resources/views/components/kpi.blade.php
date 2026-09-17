{{--
    Tuile KPI unique. Reprend le design de la liste des utilisateurs
    (Modules/Settings/resources/views/users.blade.php) : avatar coloré et libellé
    à gauche, grand chiffre de la même couleur à droite.

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
    <div class="d-flex align-items-center justify-content-between p-3 border rounded h-100">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                <div class="avatar-initial bg-label-{{ $color }} rounded">
                    <i class="{{ $icon }} fa-24px"></i>
                </div>
            </div>
            <div>
                <h6 class="mb-0">{{ $label }}</h6>
                @if ($sublabel)
                    <small class="text-muted">{{ $sublabel }}</small>
                @endif
                {{-- « hint » : ligne libre sous le libellé (évolution, lien de renvoi...). --}}
                @isset($hint)
                    <div class="mt-1" style="font-size: .75rem;">{{ $hint }}</div>
                @endisset
            </div>
        </div>
        <div class="text-end">
            {{-- trim() et non $slot->isEmpty() : les retours à la ligne autour d'un slot nommé rendent le slot par défaut non vide. --}}
            <h3 class="mb-0 text-{{ $color }}">
                @if (trim($slot) !== '')
                    {{ $slot }}
                @else
                    {{ $value }}
                @endif
            </h3>
        </div>
    </div>
</div>
