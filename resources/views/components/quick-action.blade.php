{{--
    Bouton d'une barre <x-quick-actions>.

    variant : « primary » (bouton plein, actions principales),
              « outline » (contour, actions secondaires),
              « ghost »   (sans cadre, actions de service).
    color   : accent d'un bouton « outline » ou « ghost »
              (primary, success, warning, danger, info...).

    Rendu en <a> si « href » est fourni et l'action active, en <button> sinon
    (les actions d'un formulaire passent type="submit").
--}}
@props([
    'icon' => null,
    'label' => '',
    'href' => null,
    'variant' => 'primary',
    'color' => null,
    'disabled' => false,
    'type' => 'button',
])

@php
    $accent = $color ?: 'secondary';
    $classes = 'btn qa-btn qa-btn-' . $variant;

    if ($variant === 'primary') {
        $classes .= ' btn-' . ($color ?: 'primary');
    } elseif ($variant === 'outline') {
        $classes .= ' btn-outline-' . $accent;
    } else {
        $classes .= ' qa-accent-' . $accent;
    }

    if ($disabled) {
        $classes .= ' disabled';
    }

    $isLink = $href && !$disabled;
@endphp

@if ($isLink)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $label }}</span>{{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $label }}</span>{{ $slot }}
    </button>
@endif
