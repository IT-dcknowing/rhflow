{{-- Documents de la paie : $documents est préparé dans periodes/show --}}
<section class="pm-panel">
    <div class="pm-panel-head">
        <div>
            <h2>Documents de la paie</h2>
            <p>Les bulletins, le livre de paie et les cotisations s'ouvrent sur {{ $periode->nom }}.</p>
        </div>
    </div>
    <div class="pm-panel-body">
        @if(count($documents) > 0)
            <ul class="pm-list pm-docs">
                @foreach($documents as $document)
                    <li>
                        <a href="{{ $document[3] }}">
                            <span class="pm-bx"><i class="fas {{ $document[0] }}"></i></span>
                            <span class="pm-txt"><b>{{ $document[1] }}</b><small>{{ $document[2] }}</small></span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mb-0">Le module Déclarations n'est pas activé : les bulletins et les états ne sont pas disponibles ici.</p>
        @endif
    </div>
</section>
