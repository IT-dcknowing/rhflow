{{--
    Bouton d'une barre <x-quick-actions>.

    variant : « primary » (bouton plein),
              « outline » (contour),
              « ghost »   (sans cadre / texte épuré),
              « icon »    (bouton icône rond).
    color   : primary, success, warning, danger, info, secondary...
--}}
@props([
    'icon' => null,
    'label' => '',
    'href' => null,
    'variant' => 'primary',
    'color' => null,
    'disabled' => false,
    'type' => 'button',
    'title' => null,
])

@php
    $accent = $color ?: 'secondary';
    $tooltip = $title ?: ($label ?: null);

    if ($variant === 'icon' || ($variant === 'ghost' && empty($label))) {
        $classes = 'btn btn-outline-' . $accent . ' btn-icon rounded-circle shadow-sm qa-btn qa-btn-icon';
        $style = 'width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;';
    } elseif ($variant === 'outline') {
        $classes = 'btn btn-outline-' . $accent . ' d-inline-flex align-items-center justify-content-center gap-2 px-3 qa-btn qa-btn-outline';
        $style = '';
    } elseif ($variant === 'ghost') {
        $classes = 'btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center gap-2 px-2 qa-btn qa-btn-ghost';
        $style = '';
    } else {
        // primary
        $btnColor = $color ?: 'primary';
        $classes = 'btn btn-' . $btnColor . ' d-inline-flex align-items-center justify-content-center gap-2 px-3 shadow-sm qa-btn qa-btn-primary';
        $style = '';
    }

    if ($disabled) {
        $classes .= ' disabled';
    }

    $isLink = $href && !$disabled;
@endphp

@if ($isLink)
    <a href="{{ $href }}" 
       @if($tooltip) title="{{ $tooltip }}" @endif
       @if(!empty($style)) style="{{ $style }}" @endif
       {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        @if(!empty($label) && $variant !== 'icon')
            <span>{{ $label }}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
            @disabled($disabled) 
            @if($tooltip) title="{{ $tooltip }}" @endif
            @if(!empty($style)) style="{{ $style }}" @endif
            {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        @if(!empty($label) && $variant !== 'icon')
            <span>{{ $label }}</span>
        @endif
        {{ $slot }}
    </button>
@endif
