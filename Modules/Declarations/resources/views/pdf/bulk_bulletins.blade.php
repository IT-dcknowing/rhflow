@php 
    $totalAllowances = 0;
    $totalretenuessal = 0;
    $totalretenuesemp = 0;
    
    // On utilise les données du bulletin déjà calculées
    $base_salary = $bulletin->basic_salary ?? 0;
    $allowances = json_decode($bulletin->allowances ?? '[]', true);
    $retenues = json_decode($bulletin->retenues ?? '[]', true);
    $amount_avtg = $bulletin->avtg_real ?? 0;

    // Calculer l'ancienneté
    $date_embauche = new DateTime($bulletin->employee->start_date ?? date('Y-m-d'));
    $date_actuelle = new DateTime(date('Y-m-d'));
    $difference = $date_embauche->diff($date_actuelle);
    $date_pa = $difference->format('%y');
    $date_m  = $difference->format('%m');
    
    // Logo Path for DomPDF - Conversion en Base64 pour fiabilité maximale
    $logoBase64 = null;
    if ($company->logo) {
        $logoPath = public_path('storage/logos/' . $company->logo);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }

    // Stamp Base64
    $stampBase64 = null;
    if ($company->electronic_stamp) {
        $stampPath = public_path('storage/stamps/' . $company->electronic_stamp);
        if (file_exists($stampPath)) {
            $type = pathinfo($stampPath, PATHINFO_EXTENSION);
            $data = file_get_contents($stampPath);
            $stampBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }

    // Signature Base64
    $signatureBase64 = null;
    if ($company->electronic_signature) {
        $signaturePath = public_path('storage/signatures/' . $company->electronic_signature);
        if (file_exists($signaturePath)) {
            $type = pathinfo($signaturePath, PATHINFO_EXTENSION);
            $data = file_get_contents($signaturePath);
            $signatureBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
@endphp

<div class="bulletin-container">
    <div style="margin-bottom: 10px;">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo" width="80px">
        @endif
    </div>

    <!-- En-tête du bulletin -->
    <div class="bulletin-header text-center">
        <h2><strong>BULLETIN DE PAIE</strong></h2>
        <p>Période: {{ $periode->nom }} | {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>
    </div>

    <table class="table table-sm">
        <!-- Informations employé -->
        <tr class="info-section">
            <td colspan="3">EMPLOYEUR</td>
            <td colspan="6">MATRICULE DU SALARIE: {{ $bulletin->employee->employee_id }}</td>
        </tr>
        <tr>
            <td colspan="3" style="vertical-align: top;">
                <strong>{{ $company->name }}</strong><br>
                {{ $company->address }}<br>
                {{ $company->city }}<br>
                Tel: {{ $company->phone }}<br>
                Horaire mensuelle : 173,33<br>
                Grille salariale : {{ $company->sector->name ?? 'N/A' }}<br>
            </td>
            <td colspan="6" style="vertical-align: top;">
                <strong>{{ $bulletin->employee->name }}</strong><br>
                {{ $bulletin->employee->address }}<br>
                Sit. matrimoniale : {{ $bulletin->employee->situation->name ?? 'N/A' }}<br>
                Enfants : {{ $bulletin->employee->enfant }} | CNPS : {{ $bulletin->employee->num_cnps }}<br>
                Ancienneté : {{$date_pa}} an(s) et {{$date_m}} mois<br>
                Emploi : {{ $bulletin->emploi ?? ($bulletin->employee->designation->name ?? 'N/A') }}<br>
            </td>
        </tr>     
        
        <tr class="bg-success-light">
            <th width="5%" rowspan="2" class="text-center align-middle">N°</th>
            <th width="35%" rowspan="2" class="text-center align-middle">DÉSIGNATION</th>
            <th width="5%" rowspan="2" class="text-center align-middle">NB</th>
            <th width="10%" rowspan="2" class="text-center align-middle">BASE JOURNALIÈRE</th>
            <th colspan="3" class="text-center">PART SALARIALE</th>
            <th colspan="2" class="text-center">PART PATRONALE</th>
        </tr>
        <tr class="bg-success-light">
            <th class="text-center">TAUX</th>
            <th class="text-center">GAIN</th>
            <th class="text-center">RETENUES</th>
            <th class="text-center">TAUX</th>
            <th class="text-center">MONTANT</th>
        </tr>

        <tbody>
            @if($base_salary > 0)
            <tr>
                <td class="text-end">100</td>
                <td>Salaire de base</td>
                <td class="text-end">{{ $bulletin->nbre_jour ?? 30 }}</td>
                <td class="text-end">{{ number_format(($bulletin->employee->salary/30), 0, '.', ' ') }}</td>
                <td></td>
                <td class="text-end">{{ number_format($base_salary, 0, '.', ' ') }}</td>                       
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            @foreach($allowances as $allowance)
            @php $totalAllowances += $allowance['amount']; @endphp
            <tr>
                <td class="text-end">{{ $allowance['code'] ?? '-' }}</td>
                <td>{{ $allowance['title'] ?? 'Indemnité' }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td></td>
                <td class="text-end">{{ number_format($allowance['amount'], 0, '.', ' ') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach

            @if($amount_avtg > 0)
            <tr>
                <td class="text-end">150</td>
                <td>Avantages en nature</td>
                <td class="text-end">{{ $bulletin->nbre_jour ?? 30 }}</td>
                <td class="text-end"></td>
                <td></td>
                <td class="text-end">{{ number_format($amount_avtg, 0, '.', ' ') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            <tr class="bg-success-light text-bold">
                <td colspan="5" class="text-center">Total Brut</td>
                <td class="text-end">{{ number_format($bulletin->salary_brut, 0, '.', ' ') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @foreach($retenues as $retenue)
            @php 
                $totalretenuessal += ($retenue['amount'] ?? 0);
                $totalretenuesemp += ($retenue['patronale'] ?? 0);
            @endphp
            <tr>
                <td class="text-end">{{ $retenue['code'] ?? '-' }}</td>
                <td>{{ $retenue['libelle'] ?? 'Cotisation' }}</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format($retenue['base'] ?? 0, 0, '.', ' ') }}</td>
                <td class="text-end">{{ $retenue['taux'] ?? '' }}</td>
                <td></td>
                <td class="text-end">{{ number_format($retenue['amount'] ?? 0, 0, '.', ' ') }}</td>
                <td class="text-end">{{ $retenue['salariale'] ?? '' }}</td>
                <td class="text-end">{{ number_format($retenue['patronale'] ?? 0, 0, '.', ' ') }}</td>
            </tr>
            @endforeach

            <tr class="bg-danger-light text-bold">
                <td colspan="6" class="text-center">Total Cotisations</td>
                <td class="text-end">{{ number_format($totalretenuessal, 0, '.', ' ') }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totalretenuesemp, 0, '.', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table recap-table">
        <tr class="bg-success-light text-center text-bold">
            <td>Récapitulatif</td>
            <td>Salaire brut</td>
            <td>Retenues</td>
            <td>Avantages</td>
            <td class="net-pay-box">NET À PAYER</td>
        </tr>
        <tr class="text-center">
            <td>PÉRIODE</td>
            <td class="text-end">{{ number_format($bulletin->salary_brut, 0, '.', ' ') }}</td>
            <td class="text-end">{{ number_format($totalretenuessal, 0, '.', ' ') }}</td>
            <td class="text-end">{{ number_format($amount_avtg, 0, '.', ' ') }}</td>
            <td class="text-end net-pay-box">
                {{ number_format($bulletin->net_payble, 0, '.', ' ') }} FCFA
            </td>
        </tr>
    </table>

    <div class="row" style="margin-top: 20px;">
        <div style="width: 60%; float: left;">
            <p class="footer-note">
                Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.
            </p>
        </div>
        <div style="width: 35%; float: right; text-align: center;">
            <p class="text-bold">LA DIRECTION</p>
            <div style="margin-top: 30px; position: relative; height: 80px;">
                @if($stampBase64)
                    <img src="{{ $stampBase64 }}" alt="Cachet" width="70px" style="position: absolute; left: 50%; transform: translateX(-50%);">
                @endif
                
                @if($signatureBase64)
                    <img src="{{ $signatureBase64 }}" alt="Signature" width="70px" style="position: absolute; left: 50%; transform: translateX(-50%);">
                @endif
            </div>
            <div class="signature-title" style="margin-top: 10px;">Signature</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
