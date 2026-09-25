{{--
    Barre « Actions rapides » commune à tout le projet.
    Design moderne groupé avec séparateurs verticaux, en-têtes de sections et boutons profilés.
--}}
@props([
    'title' => null,
    'stacked' => false,
    'card' => true,
])

<div {{ $attributes->merge(['class' => ($card ? 'card qa-card mb-4 shadow-sm border-0' : 'qa-block mb-4')]) }}>
    <div class="{{ $card ? 'card-body p-3 p-md-4' : '' }}">
        @if ($title)
            <div class="qa-title mb-3 fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: .06em;">
                <i class="fas fa-bolt text-primary me-1"></i>{{ $title }}
            </div>
        @endif

        @if($stacked)
            <div class="qa-bar qa-bar-stacked d-flex flex-column gap-2">
                <div class="qa-row d-flex flex-column gap-2">{{ $slot }}</div>
                @isset($secondary)
                    <div class="qa-row d-flex flex-column gap-2 pt-2 border-top">{{ $secondary }}</div>
                @endisset
                @isset($end)
                    <div class="qa-row qa-end d-flex flex-column gap-2 pt-2 border-top">{{ $end }}</div>
                @endisset
            </div>
        @else
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 gap-xl-4 w-100 qa-segmented-bar">
                @if(isset($secondary) || isset($end))
                    {{-- Mode slots classiques converti automatiquement au design groupé --}}
                    <div class="d-flex flex-column flex-grow-1 qa-group" style="min-width: 220px;">
                        <small class="text-muted text-uppercase fw-bold mb-2 d-flex align-items-center gap-1" style="font-size: 0.72rem; letter-spacing: .06em;">
                            <i class="fas fa-star text-primary"></i> Actions principales
                        </small>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{ $slot }}
                        </div>
                    </div>

                    @isset($secondary)
                        <div class="vr d-none d-xl-block mx-1 opacity-25 qa-vr" style="height: 48px; align-self: center;"></div>
                        <div class="d-flex flex-column flex-grow-1 qa-group" style="min-width: 220px;">
                            <small class="text-muted text-uppercase fw-bold mb-2 d-flex align-items-center gap-1" style="font-size: 0.72rem; letter-spacing: .06em;">
                                <i class="fas fa-tasks text-warning"></i> Gestion
                            </small>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                {{ $secondary }}
                            </div>
                        </div>
                    @endisset

                    @isset($end)
                        <div class="vr d-none d-xl-block mx-1 opacity-25 qa-vr" style="height: 48px; align-self: center;"></div>
                        <div class="d-flex flex-column align-items-xl-end ms-xl-auto qa-group qa-group-end">
                            <small class="text-muted text-uppercase fw-bold mb-2 d-flex align-items-center gap-1" style="font-size: 0.72rem; letter-spacing: .06em;">
                                <i class="fas fa-cog text-secondary"></i> Outils
                            </small>
                            <div class="d-flex align-items-center gap-2">
                                {{ $end }}
                            </div>
                        </div>
                    @endisset
                @else
                    {{-- Mode direct (avec ou sans x-quick-action-group) --}}
                    {{ $slot }}
                @endif
            </div>
        @endif
    </div>
</div>
