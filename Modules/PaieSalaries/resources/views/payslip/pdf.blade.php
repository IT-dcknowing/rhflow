<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de paie - {{ $employee->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .info { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { font-weight: bold; }
        .page-break { page-break-after: always; }
        .signature { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BULLETIN DE PAIE</h2>
        <p>Période: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
        <p>Matricule: {{ $employee->employee_id }}</p>
    </div>

    <div class="info">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Nom:</strong> {{ $employee->name }}<br>
                    <strong>Poste:</strong> {{ $employee->designation->name ?? 'Non défini' }}<br>
                    <strong>Département:</strong> {{ $employee->department->name ?? 'Non défini' }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Date d'embauche:</strong> {{ \Carbon\Carbon::parse($employee->company_doj)->format('d/m/Y') }}<br>
                    <strong>Banque:</strong> {{ $employee->bank_name ?? 'Non défini' }}<br>
                    <strong>N° de compte:</strong> {{ $employee->account_number ?? 'Non défini' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Récapitulatif des gains -->
    <table class="table">
        <thead>
            <tr>
                <th colspan="3" class="text-center">RÉCAPITULATIF DES GAINS</th>
            </tr>
            <tr>
                <th>Libellé</th>
                <th>Base</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de base</td>
                <td class="text-right">{{ number_format($employee->salary, 0, ',', ' ') }} FCFA</td>
                <td class="text-right">{{ number_format($employee->salary, 0, ',', ' ') }} FCFA</td>
            </tr>
            
            @foreach($employee->allowances as $allowance)
            <tr>
                <td>{{ $allowance->allowanceOption->name ?? $allowance->title ?? 'Prime' }}</td>
                <td class="text-right">{{ number_format($allowance->amount, 0, ',', ' ') }} FCFA</td>
                <td class="text-right">{{ number_format($allowance->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td colspan="2"><strong>Total des gains</strong></td>
                <td class="text-right"><strong>{{ number_format($employee->salary + $totalAllowances, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Récapitulatif des retenues -->
    <table class="table">
        <thead>
            <tr>
                <th colspan="2" class="text-center">RÉCAPITULATIF DES RETENUES</th>
            </tr>
            <tr>
                <th>Libellé</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>CNPS (6.3%)</td>
                <td class="text-right">{{ number_format(($employee->salary * 6.3) / 100, 0, ',', ' ') }} FCFA</td>
            </tr>
            
            @foreach($employee->deductions as $deduction)
            <tr>
                <td>{{ $deduction->deductionOption->name ?? $deduction->libelle ?? 'Retenue' }}</td>
                <td class="text-right">{{ number_format($deduction->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td><strong>Total des retenues</strong></td>
                <td class="text-right"><strong>{{ number_format((($employee->salary * 6.3) / 100) + $totalDeductions, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>

    @if($employee->loans->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th colspan="3" class="text-center">AVANCES ET PRÊTS</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Libellé</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->loans as $loan)
            <tr>
                <td>{{ \Carbon\Carbon::parse($loan->date)->format('d/m/Y') }}</td>
                <td>{{ $loan->description }}</td>
                <td class="text-right">{{ number_format($loan->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="2"><strong>Total des prêts</strong></td>
                <td class="text-right"><strong>{{ number_format($totalLoans, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Récapitulatif final -->
    <table class="table" style="width: 50%; margin-left: auto;">
        <tr>
            <th>Salaire Brut</th>
            <td class="text-right">{{ number_format($employee->get_brut_salary(), 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <th>Total des retenues</th>
            <td class="text-right">{{ number_format((($employee->salary * 6.3) / 100) + $totalDeductions, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <th>Total des prêts</th>
            <td class="text-right">{{ number_format($totalLoans, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total">
            <th>Net à payer</th>
            <th class="text-right">{{ number_format($employee->get_net_salary(), 0, ',', ' ') }} FCFA</th>
        </tr>
    </table>

    <div class="signature">
        <p>Le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p>Signature</p>
    </div>
</body>
</html>
