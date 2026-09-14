{{-- Styles de l'écran « Paie du mois » (ouverture et traitement d'une période). Jetons : public/css/rhflow-design.css --}}
@push('styles')
    <style>
        .pm { display: flex; flex-direction: column; gap: 18px; }
        .pm-mono { font-family: var(--font-mono); font-variant-numeric: tabular-nums; }

        /* En-tête */
        .pm-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .pm-eyebrow { font-size: 10.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
        .pm-title { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 2px; }
        .pm .pm-title h1 { margin: 0; font-size: 26px; font-weight: 600; letter-spacing: -.01em; color: var(--ink); }
        .pm-meta { display: flex; flex-wrap: wrap; gap: 4px 16px; margin-top: 3px; font-size: 12.5px; color: var(--muted); }
        .pm-meta i { margin-right: 5px; font-size: 11.5px; }
        .pm-chip { display: inline-flex; align-items: center; gap: 5px; padding: 2px 10px; border: 1px solid; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .pm-chip.warn { background: var(--warn-bg); border-color: var(--warn-line); color: var(--warn); }
        .pm-chip.ok { background: var(--ok-bg); border-color: var(--ok-line); color: var(--ok); }
        .pm-chip.navy { background: var(--navy-tint); border-color: var(--navy-line); color: var(--navy-text); }
        .pm-chip.bad { background: var(--bad-bg); border-color: var(--bad-line); color: var(--bad); }
        .pm-chip.faint { background: var(--surface-2); border-color: var(--line); color: var(--muted); }

        /* Étapes */
        .pm-steps { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); background: var(--surface); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
        .pm-step { position: relative; }
        .pm-step + .pm-step { border-left: 1px solid var(--line-soft); }
        .pm-step button { display: flex; align-items: center; gap: 11px; width: 100%; padding: 12px 16px; border: 0; background: none; text-align: left; }
        .pm-step button:disabled { cursor: default; }
        .pm-step-num { display: grid; place-items: center; flex-shrink: 0; width: 28px; height: 28px; border: 1.5px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--faint); font-family: var(--font-mono); font-size: 12.5px; }
        .pm-step-num .fa-check { display: none; font-size: 12px; }
        .pm-step-label { display: block; font-family: var(--font-title); font-size: 13.5px; font-weight: 600; color: var(--muted); }
        .pm-step-sub { display: block; font-size: 11.5px; color: var(--faint); }
        .pm-step.done .pm-step-num { background: var(--ok-bg); border-color: var(--ok-line); color: var(--ok); }
        .pm-step.done .pm-step-num .fa-check { display: block; }
        .pm-step.done .pm-step-num span { display: none; }
        .pm-step.done .pm-step-label { color: var(--ink-2); }
        .pm-step.done .pm-step-sub { color: var(--ok); }
        .pm-step.current { background: var(--navy-tint-2); }
        .pm-step.current::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: var(--navy); }
        .pm-step.current .pm-step-num { background: var(--navy); border-color: var(--navy); color: #fff; }
        .pm-step.current .pm-step-label { color: var(--navy-text); }
        .pm-step.current .pm-step-sub { color: var(--ink-2); }

        /* Blocs */
        .pm-grid-2 { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); gap: 18px; align-items: start; }
        .pm-panel { background: var(--surface); border: 1px solid var(--line); border-radius: 12px; }
        .pm-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; padding: 14px 18px 10px; }
        .pm .pm-panel-head h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--ink); }
        .pm-panel-head p { margin: 1px 0 0; font-size: 12px; color: var(--muted); }
        .pm-panel-body { padding: 0 18px 16px; }

        .pm-list { list-style: none; margin: 0; padding: 0; }
        .pm-list > li { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-top: 1px solid var(--line-soft); }
        .pm-bx { display: grid; place-items: center; flex-shrink: 0; width: 32px; height: 32px; border-radius: 8px; background: var(--navy-tint); color: var(--navy-text); font-size: 13px; }
        .pm-bx.ok { background: var(--ok-bg); color: var(--ok); }
        .pm-bx.bad { background: var(--bad-bg); color: var(--bad); }
        .pm-bx.warn { background: var(--warn-bg); color: var(--warn); }
        .pm-list .pm-txt { flex: 1; min-width: 0; }
        .pm-list .pm-txt b { display: block; font-weight: 600; color: var(--ink); }
        .pm-list .pm-txt small { font-size: 12px; color: var(--muted); }
        .pm-amt { font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-size: 12.5px; color: var(--ink-2); white-space: nowrap; }
        .pm-link { border: 0; background: none; padding: 4px 6px; border-radius: 6px; font-size: 12.5px; font-weight: 600; color: var(--navy-text); white-space: nowrap; text-decoration: none; }
        .pm-link:hover { background: var(--navy-tint); color: var(--navy-text); }

        .pm-alerts { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
        .pm-alert { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border: 1px solid; border-radius: 9px; font-size: 12.5px; }
        .pm-alert i { margin-top: 3px; }
        .pm-alert p { flex: 1; margin: 0; color: var(--ink-2); }
        .pm-alert.warning { background: var(--warn-bg); border-color: var(--warn-line); color: var(--warn); }
        .pm-alert.danger { background: var(--bad-bg); border-color: var(--bad-line); color: var(--bad); }
        .pm-alert.info { background: var(--navy-tint-2); border-color: var(--navy-line); color: var(--navy-text); }
        .pm-alert.ok { background: var(--ok-bg); border-color: var(--ok-line); color: var(--ok); }

        .pm-vars { display: grid; grid-template-columns: repeat(auto-fill, minmax(165px, 1fr)); gap: 8px; }
        .pm-var { display: flex; flex-direction: column; gap: 2px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 9px; background: var(--surface-2); text-decoration: none; }
        .pm-var:hover { border-color: var(--navy-line); background: var(--navy-tint-2); }
        .pm-var-top { display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 600; color: var(--ink-2); }
        .pm-var-top i { width: 14px; text-align: center; color: var(--muted); }
        .pm-var-val { font-size: 12px; color: var(--muted); }
        .pm-var.has .pm-var-val { font-weight: 600; color: var(--navy-text); }

        /* Chiffres clés */
        .pm-figures { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); background: var(--surface); border: 1px solid var(--line); border-radius: 12px; }
        .pm-fig { padding: 13px 18px; }
        .pm-fig + .pm-fig { border-left: 1px solid var(--line-soft); }
        .pm-fig span { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
        .pm-fig b { display: block; margin-top: 2px; font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-size: 19px; font-weight: 500; color: var(--ink); }
        .pm-fig b small { margin-left: 3px; font-size: 11px; font-weight: 400; color: var(--muted); }
        .pm-fig.net b { color: var(--navy-text); }

        /* Tableau des salariés */
        .pm-tools { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .pm-tools .form-control { width: 220px; max-width: 100%; }
        .pm-filter[aria-pressed="true"] { background: var(--warn-bg) !important; border-color: var(--warn-line) !important; color: var(--warn) !important; }
        .pm td.pm-num, .pm th.pm-num { text-align: right; white-space: nowrap; }
        .pm td.pm-num { font-family: var(--font-mono); font-variant-numeric: tabular-nums; }
        .pm td.pm-net { font-weight: 600; color: var(--navy-text); }
        .pm-who b { display: block; font-weight: 600; color: var(--ink); }
        .pm-who small { font-size: 11.5px; color: var(--muted); }
        .pm-days { display: inline-flex; align-items: center; gap: 6px; padding: 2px 9px; border: 1px solid var(--line); border-radius: 7px; background: var(--surface); font-family: var(--font-mono); font-size: 12.5px; color: var(--ink-2); }
        .pm-days:hover { border-color: var(--navy-line); color: var(--navy-text); }
        .pm-days.warn { border-color: var(--warn-line); background: var(--warn-bg); color: var(--warn); }
        .pm-tag { margin-left: 6px; padding: 1px 6px; border-radius: 5px; background: var(--navy-tint); color: var(--navy-text); font-size: 10.5px; font-weight: 600; white-space: nowrap; }
        .pm-table-foot { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; padding: 10px 18px; border-top: 1px solid var(--line-soft); font-size: 12px; color: var(--muted); }

        /* Ouverture */
        .pm-open { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 300px); gap: 26px; align-items: center; padding: 24px 26px; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; }
        .pm .pm-open h2 { margin: 0; font-size: 20px; font-weight: 600; color: var(--ink); }
        .pm-open p { max-width: 62ch; margin: 6px 0 0; color: var(--ink-2); }
        .pm-dates { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-top: 18px; }
        .pm-last { display: flex; flex-direction: column; gap: 10px; padding-left: 26px; border-left: 1px solid var(--line-soft); }
        .pm-last .pm-row { display: flex; justify-content: space-between; gap: 10px; font-size: 12.5px; }
        .pm-last .pm-row span { color: var(--muted); }
        .pm-last .pm-row b { font-weight: 600; text-align: right; color: var(--ink); }

        /* Paiement et documents */
        .pm-banner { display: flex; align-items: center; gap: 12px; padding: 13px 16px; border: 1px solid; border-radius: 12px; }
        .pm-banner > i { font-size: 18px; }
        .pm-banner div { flex: 1; }
        .pm-banner b { display: block; font-family: var(--font-title); font-size: 14px; font-weight: 600; }
        .pm-banner p { margin: 0; font-size: 12.5px; color: var(--ink-2); }
        .pm-banner.ok { background: var(--ok-bg); border-color: var(--ok-line); color: var(--ok); }
        .pm-banner.navy { background: var(--navy-tint); border-color: var(--navy-line); color: var(--navy-text); }
        .pm-banner.bad { background: var(--bad-bg); border-color: var(--bad-line); color: var(--bad); }
        .pm-docs > li { padding: 0; }
        .pm-docs a { display: flex; align-items: center; gap: 12px; width: 100%; padding: 11px 0; text-decoration: none; }
        .pm-docs a:hover .pm-txt b { color: var(--navy-text); }
        .pm-docs .fa-chevron-right { font-size: 11px; color: var(--faint); }

        /* Barre d'action : un seul bouton principal */
        .pm-actionbar { position: sticky; bottom: 12px; z-index: 5; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 10px 12px 10px 18px; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; box-shadow: var(--shadow-pop); }
        .pm-actionbar[hidden] { display: none; }
        .pm-actionbar .pm-ctx { flex: 1; min-width: 200px; font-size: 12.5px; color: var(--muted); }
        .pm-actionbar .pm-ctx b { font-weight: 600; color: var(--ink); }
        .pm .pm-btn-main { min-height: 40px; padding: 0 20px; font-size: 13.5px; }

        @media (max-width: 1100px) {
            .pm-grid-2 { grid-template-columns: minmax(0, 1fr); }
        }
        @media (max-width: 768px) {
            .pm-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .pm-step:nth-child(3) { border-left: 0; }
            .pm-step:nth-child(n+3) { border-top: 1px solid var(--line-soft); }
            .pm-figures { grid-template-columns: minmax(0, 1fr); }
            .pm-fig + .pm-fig { border-left: 0; border-top: 1px solid var(--line-soft); }
            .pm-open { grid-template-columns: minmax(0, 1fr); padding: 18px; }
            .pm-dates { grid-template-columns: minmax(0, 1fr); }
            .pm-last { padding-left: 0; padding-top: 16px; border-left: 0; border-top: 1px solid var(--line-soft); }
            .pm-actionbar .pm-btn-main { flex: 1; }
        }
    </style>
@endpush
