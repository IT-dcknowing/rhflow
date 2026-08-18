<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 0.5cm; }
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; color: #111; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @foreach($bulletins as $bulletin)
        {{-- Bulletin 1 = modèle encadré | Bulletin 2 = modèle bleu épuré --}}
        @if(in_array($bulletinType ?? 1, [2, 4, 5]))
            @include('declarations::pdf.bulletin_reference', [
                'bulletin' => $bulletin,
                'company' => $company,
                'periode' => $periode,
                'variant' => $bulletinType
            ])
        @else
            @include('declarations::pdf.bulk_bulletins', [
                'bulletin' => $bulletin,
                'company' => $company,
                'exercice' => $exercice,
                'periode' => $periode
            ])
        @endif

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>