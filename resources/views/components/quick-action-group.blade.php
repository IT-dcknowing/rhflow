@props([
    'title' => '',
    'label' => '',
    'icon' => null,
    'color' => 'primary',
    'minWidth' => '200px',
    'end' => false,
])

@php
    $headerText = $title ?: $label;
@endphp

<div class="d-flex flex-column {{ $end ? 'align-items-xl-end ms-xl-auto' : 'flex-grow-1' }} qa-group" 
     @if($minWidth && !$end) style="min-width: {{ $minWidth }};" @endif>
    @if($headerText || $icon)
        <small class="text-muted text-uppercase fw-bold mb-2 d-flex align-items-center gap-1" style="font-size: 0.72rem; letter-spacing: .06em;">
            @if($icon)
                <i class="{{ $icon }} text-{{ $color }}"></i>
            @endif
            <span>{{ $headerText }}</span>
        </small>
    @endif
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{ $slot }}
    </div>
</div>
