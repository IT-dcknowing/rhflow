<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 0.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; margin: 0; padding: 0; color: #333; }
        .page-break { page-break-after: always; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; line-height: 1.4; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-bold { font-weight: bold; }
        
        /* App Colors */
        .bg-success-light { background-color: #d1e7dd !important; color: #0f5132 !important; }
        .bg-primary-light { background-color: #cfe2ff !important; color: #084298 !important; }
        .bg-danger-light { background-color: #f8d7da !important; color: #842029 !important; }
        
        .bulletin-header { background-color: #d1e7dd; padding: 10px; margin-bottom: 10px; border: 1px solid #badbcc; }
        .bulletin-header h2 { margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-section { background-color: #cfe2ff; font-weight: bold; border: 1px solid #b6d4fe; }
        
        .recap-table { margin-top: 15px; }
        .net-pay-box { background-color: #cfe2ff; font-size: 14px; font-weight: bold; border: 1px solid #b6d4fe; }
        
        .footer-note { font-style: italic; font-size: 9px; color: #666; margin-top: 20px; }
        .signature-title { border-top: 2px solid #333; width: 200px; margin-left: auto; text-align: center; padding-top: 5px; font-weight: bold; }
    </style>
</head>
<body>
    @foreach($bulletins as $index => $bulletin)
        @if(in_array($bulletinType ?? 1, [4, 5]))
            @include('declarations::pdf.bulletin_reference', ['bulletin' => $bulletin, 'company' => $company, 'periode' => $periode, 'variant' => $bulletinType])
        @else
            @include('declarations::pdf.bulk_bulletins', ['bulletin' => $bulletin, 'company' => $company, 'exercice' => $exercice, 'periode' => $periode])
        @endif
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
