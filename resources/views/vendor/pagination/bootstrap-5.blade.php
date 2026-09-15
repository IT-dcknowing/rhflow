{{--
    Pagination RH Flow (remplace le gabarit bootstrap-5 de Laravel, activé par Paginator::useBootstrapFive()).
    Une seule liste numérotée : le gabarit d'origine en affichait deux (version mobile + version écran)
    ainsi qu'un compteur anglais « Showing … results » en doublon du compteur des pages.
--}}
@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul class="pagination flex-wrap mb-0">
            {{-- Page précédente --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-label="Page précédente">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente">&lsaquo;</a>
                </li>
            @endif

            {{-- Numéros de page --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Page suivante --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-label="Page suivante">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
