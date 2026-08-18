@php
    // Les deux maquettes PDF attendent la variable $bulletin.
    $bulletin = $paySlip;
@endphp

<style>
    .payslip-preview-page {
        background: #f6f7f9;
        padding: 18px;
    }

    .payslip-preview-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .payslip-preview-sheet {
        background: #fff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .12);
        max-width: 900px;
        margin: 0 auto;
    }

    .payslip-preview-sheet .b1,
    .payslip-preview-sheet .b2 {
        padding: 16px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .payslip-preview-sheet,
        .payslip-preview-sheet * {
            visibility: visible;
        }

        .payslip-preview-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
        }

        .payslip-preview-actions,
        .nav {
            display: none !important;
        }
    }
</style>

<div class="payslip-preview-page">
    <div class="payslip-preview-actions">
        <ul class="nav nav-pills flex-row gap-2 mb-0">
            <li class="nav-item">
                <button type="button"
                        class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#bull1show">
                    <i class="ti ti-note ti-xs me-1"></i>
                    Bulletin 1
                </button>
            </li>

            <li class="nav-item">
                <button type="button"
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#bull2show">
                    <i class="ti ti-note ti-xs me-1"></i>
                    Bulletin 2
                </button>
            </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">
                Période :
                {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }}
                au
                {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}
            </span>

            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>
                Imprimer / PDF
            </button>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="bull1show">
            <div class="payslip-preview-sheet" id="payslipContentBull1">
                @include('declarations::pdf.bulk_bulletins', [
                    'bulletin' => $bulletin,
                    'company' => $company,
                    'periode' => $periode
                ])
            </div>
        </div>

        <div class="tab-pane fade" id="bull2show">
            <div class="payslip-preview-sheet" id="payslipContentBull2">
                @include('declarations::pdf.bulletin_reference', [
                    'bulletin' => $bulletin,
                    'company' => $company,
                    'periode' => $periode,
                    'variant' => 2
                ])
            </div>
        </div>
    </div>
</div>