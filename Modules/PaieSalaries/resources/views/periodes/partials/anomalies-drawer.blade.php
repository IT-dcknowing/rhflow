{{-- Tiroir latéral récapitulant les salariés ayant des anomalies --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="pm1AnomaliesDrawer" aria-labelledby="pm1AnomaliesTitle"
     data-bs-backdrop="false" data-bs-scroll="true"
     style="width: 520px; max-width: 95vw; z-index: 1060; box-shadow: -4px 0 24px rgba(0,0,0,0.15);">
    <div class="offcanvas-header border-bottom py-3 px-4" style="background: #FFF1F2;">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; background: #FFE4E6; color: #E11D48; font-size: 16px;">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            <div>
                <h5 class="offcanvas-title mb-0 fw-bold" id="pm1AnomaliesTitle" style="color: #9F1239;">
                    Salariés avec anomalies
                </h5>
                <small style="color: #BE123C;">
                    <span class="badge bg-danger me-1">{{ count($lignesAnomalies ?? []) }}</span>
                    salarié(s) bloquant la génération des bulletins
                </small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>

    <div class="offcanvas-body p-4" style="background: #F8F9FA;">
        @if(empty($lignesAnomalies) || count($lignesAnomalies) === 0)
            <div class="text-center py-5">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-label-success" style="width: 60px; height: 60px; font-size: 28px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </span>
                </div>
                <h6 class="fw-bold text-success mb-1">Aucune anomalie détectée</h6>
                <p class="text-muted small">Tous les salariés sont conformes. Vous pouvez valider et générer les bulletins de paie.</p>
            </div>
        @else
            <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 mb-3 small border-0 shadow-sm" style="background: #FFF5F5; border-left: 4px solid #E11D48 !important;">
                <i class="fas fa-info-circle mt-1 text-danger"></i>
                <div style="color: #4B1218;">
                    La génération et validation des bulletins est <strong>bloquée</strong> tant que ces anomalies subsistent. Cliquez sur <strong>« Traiter »</strong> pour ouvrir le tiroir et corriger la situation.
                </div>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($lignesAnomalies as $lAno)
                    @php
                        $eAno = $lAno['emp'];
                        $mots = explode(' ', trim($eAno->name));
                        $initiales = strtoupper(mb_substr($mots[0] ?? 'E', 0, 1) . mb_substr($mots[1] ?? '', 0, 1));
                    @endphp
                    <div class="card border border-danger shadow-sm rounded-3 overflow-hidden bg-white">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px;">
                                    {{ $initiales }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold fs-7 text-dark">{{ $eAno->name }}</h6>
                                    <small class="text-muted font-monospace">{{ $eAno->employee_id ?: '—' }} · {{ $lAno['dept'] }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 js-traiter-anomalie-depuis-tiroir"
                                data-employee-id="{{ $eAno->id }}"
                                title="Ouvrir la fiche de {{ $eAno->name }}">
                                <i class="fas fa-sliders-h"></i> Traiter
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border">
                                    Base : <strong>{{ number_format($lAno['base'], 0, ',', ' ') }} FCFA</strong>
                                </span>
                                <span class="badge bg-light text-dark border">
                                    Net : <strong class="{{ $lAno['net'] <= 0 ? 'text-danger' : 'text-success' }}">{{ number_format($lAno['net'], 0, ',', ' ') }} FCFA</strong>
                                </span>
                                <span class="badge bg-light text-dark border">
                                    Jours : <strong class="{{ $lAno['jours'] != 30 ? 'text-danger' : 'text-dark' }}">{{ $lAno['jours'] }} / 30 j</strong>
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-1">
                                @foreach($lAno['anomalies_motifs'] as $motif)
                                    <div class="p-2 rounded bg-label-danger text-danger small d-flex align-items-start gap-2">
                                        <i class="fas fa-times-circle mt-1"></i>
                                        <div>
                                            <strong>{{ $motif['titre'] }}</strong>
                                            @if(!empty($motif['description']))
                                                <div class="text-muted" style="font-size: 11px;">{{ $motif['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
