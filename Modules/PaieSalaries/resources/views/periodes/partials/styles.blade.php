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

        /* ============================================================
           Vue hybride « modèle 1 » — grille de masse + tiroir chirurgical
           Reprend les jetons existants (var(--navy), var(--line)…) : la charte
           de l'application ne change pas, seule la mise en page évolue.
           ============================================================ */

        /* Barre d'outils : recherche, filtre de statut, mode expert */
        .pm1-tools { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 0 18px 12px; }
        .pm1-search { position: relative; flex: 1; min-width: 220px; max-width: 420px; }
        .pm1-search i { position: absolute; top: 50%; left: 12px; transform: translateY(-50%); font-size: 12px; color: var(--faint); pointer-events: none; }
        .pm1-search .form-control { padding-left: 32px; }
        .pm1-filter { width: auto; min-width: 190px; }
        .pm1-expert { display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface-2); font-size: 12.5px; font-weight: 600; color: var(--ink-2); }
        .pm1-expert i { color: var(--navy); font-size: 12px; }
        .pm1-expert b { font-weight: 700; }
        .pm1-expert[aria-pressed="true"] { border-color: var(--navy-line); background: var(--navy-tint); color: var(--navy-text); }
        .pm1-expert[aria-pressed="true"] b { text-decoration: underline; }

        /* Barre de traitement de masse */
        .pm1-mass { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin: 0 18px 12px; padding: 9px 14px; border: 1px solid var(--navy-line); border-radius: 9px; background: var(--navy-tint); font-size: 12.5px; color: var(--navy-text); }
        .pm1-mass[hidden] { display: none; }
        .pm1-mass b { font-weight: 700; }
        .pm1-mass-sum { flex: 1; color: var(--ink-2); }
        .pm1-mass-clear { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border: 1px solid var(--navy-line); border-radius: 7px; background: var(--surface); font-size: 12px; font-weight: 600; color: var(--navy-text); }
        .pm1-mass-clear:hover { background: var(--surface-2); }

        /* Grille */
        .pm1-table th { white-space: nowrap; }
        .pm1-table td.pm1-check, .pm1-table th.pm1-check { width: 42px; text-align: center; }
        .pm1-row { cursor: pointer; }
        .pm1-row td { vertical-align: middle; }
        .pm1-who b { display: inline-flex; align-items: center; gap: 8px; }
        .pm1-who b i { font-size: 12px; color: var(--navy); }
        .pm1-dept { font-size: 12.5px; color: var(--ink-2); }
        .pm1-primes { color: var(--ink-2); }

        /* En-têtes en capitales espacées, comme la maquette. */
        .pm1-table thead th { font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--ink-2); background: var(--surface-2); }
        .pm1-table thead th.pm1-check { letter-spacing: normal; }

        /* Un liseré navy à gauche signale l'anomalie sans recourir au rouge :
           une ligne à vérifier n'est pas une erreur. */
        .pm1-row.is-anomaly > td:first-child { box-shadow: inset 4px 0 0 var(--navy); }
        .pm1-row.is-anomaly { background: var(--surface-2); }
        .pm1-row.is-pending > td:first-child { box-shadow: inset 4px 0 0 var(--line); }

        .pm1-pill { display: inline-flex; align-items: center; gap: 5px; margin-top: 4px; padding: 1px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 600; }
        .pm1-pill.anomaly { background: var(--navy); color: #fff; }
        .pm1-pill.pending { border: 1px solid var(--line); background: var(--surface-2); color: var(--muted); }
        .pm1-pill i { font-size: 9.5px; }

        .pm1-net-wrap { display: inline-flex; align-items: center; justify-content: flex-end; gap: 7px; }
        .pm1-ico { font-size: 12px; }
        .pm1-ico.ok { color: var(--ok); }
        .pm1-ico.warn { color: var(--navy); }
        .pm1-ico.faint { color: var(--faint); }

        .pm1-totaux td { border-top: 2px solid var(--navy); background: var(--surface-2); font-weight: 600; font-size: 12.5px; color: var(--ink); }
        .pm1-total-net { font-size: 14px; font-weight: 700; color: var(--navy-text); }

        /* Bandeau d'audit sous la grille */
        .pm1-audit { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 13px 18px; border: 1px solid var(--line); border-radius: 12px; background: var(--surface-2); }
        .pm1-audit-txt { display: flex; align-items: center; gap: 9px; font-size: 12.5px; color: var(--ink-2); }
        .pm1-audit-txt i { color: var(--navy); }
        .pm1-audit-txt b { font-weight: 600; color: var(--navy-text); }
        .pm1-audit-net { font-size: 12.5px; color: var(--muted); }
        .pm1-audit-net b { margin: 0 3px; font-size: 15px; font-weight: 600; color: var(--navy-text); }

        /* Le bandeau d'audit porte maintenant la validation : il reste à portée
           quelle que soit la longueur de la grille. */
        .pm1-audit { position: sticky; bottom: 12px; z-index: 5; background: var(--surface); box-shadow: var(--shadow-pop); }
        .pm1-audit-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .pm1-valider { display: inline-flex; align-items: center; min-height: 42px; padding: 0 22px; font-size: 13px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
        .pm1-valider-montant { margin-left: 9px; padding: 1px 8px; border-radius: 20px; background: rgba(255, 255, 255, .18); font-family: var(--font-mono); font-size: 12px; }
        .pm1-valider:disabled { opacity: .55; }

        .pm1-verrou { display: inline-flex; align-items: center; gap: 7px; padding: 5px 12px; border: 1px solid var(--ok-line); border-radius: 20px; background: var(--ok-bg); font-size: 12px; font-weight: 600; color: var(--ok); }

        /* Saisies annexes, repliées : la grille garde le premier plan. */
        .pm1-annexes { border: 1px solid var(--line); border-radius: 12px; background: var(--surface); }
        .pm1-annexes > summary { display: flex; align-items: center; gap: 10px; padding: 13px 18px; font-size: 13px; font-weight: 600; color: var(--ink-2); cursor: pointer; list-style: none; }
        .pm1-annexes > summary::-webkit-details-marker { display: none; }
        .pm1-annexes > summary i { color: var(--navy); }
        .pm1-annexes > summary span:first-of-type { flex: 1; }
        .pm1-annexes > summary::after { content: "078"; font-family: "Font Awesome 5 Free"; font-weight: 900; font-size: 11px; color: var(--faint); transition: transform .18s ease; }
        .pm1-annexes[open] > summary { border-bottom: 1px solid var(--line-soft); }
        .pm1-annexes[open] > summary::after { transform: rotate(180deg); }
        .pm1-annexes-body { display: flex; flex-direction: column; gap: 18px; padding: 18px; }
        .pm1-annexes-body .pm-panel { border-color: var(--line-soft); }

        /* ---------- Tiroir latéral ---------- */
        .pm1-drawer-backdrop { position: fixed; inset: 0; z-index: 1045; background: rgba(10, 31, 68, .28); }
        .pm1-drawer-backdrop[hidden] { display: none; }

        .pm1-drawer { position: fixed; top: 0; right: 0; bottom: 0; z-index: 1046; display: flex; flex-direction: column; width: 50%; max-width: 680px; background: var(--surface); border-left: 1px solid var(--line); box-shadow: -12px 0 32px rgba(10, 31, 68, .14); }
        .pm1-drawer[hidden] { display: none; }
        .pm1-drawer.is-open { animation: pm1Slide .22s ease-out; }
        @keyframes pm1Slide { from { transform: translateX(28px); opacity: .4; } to { transform: translateX(0); opacity: 1; } }

        .pm1-drawer-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); }
        .pm1-drawer-ident { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .pm1-drawer-mat { padding: 2px 9px; border-radius: 6px; background: var(--navy-tint); font-size: 11.5px; font-weight: 600; color: var(--navy-text); }
        .pm1-drawer-head h2 { margin: 0; font-size: 17px; font-weight: 600; color: var(--ink); }
        .pm1-drawer-sub { margin: 4px 0 0; font-size: 12px; color: var(--muted); }
        .pm1-drawer-sub b { font-weight: 600; color: var(--ink-2); }
        .pm1-drawer-close { padding: 6px 9px; border: 0; border-radius: 8px; background: none; color: var(--muted); }
        .pm1-drawer-close:hover { background: var(--surface-2); color: var(--ink); }

        .pm1-drawer-body { flex: 1; overflow-y: auto; padding: 18px 22px; display: flex; flex-direction: column; gap: 20px; }

        .pm1-drawer-alert { display: flex; align-items: flex-start; gap: 10px; padding: 11px 13px; border: 1px solid var(--navy-line); border-radius: 10px; background: var(--navy-tint); }
        .pm1-drawer-alert[hidden] { display: none; }
        .pm1-drawer-alert i { margin-top: 2px; color: var(--navy); }
        .pm1-drawer-alert strong { display: block; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--navy-text); }
        .pm1-drawer-alert p { margin: 2px 0 0; font-size: 12.5px; color: var(--ink-2); }

        .pm1-sec h3 { display: flex; align-items: center; gap: 8px; margin: 0 0 9px; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
        .pm1-sec h3 i { font-size: 12px; color: var(--navy); }

        .pm1-card { padding: 14px 16px; border: 1px solid var(--line); border-radius: 11px; background: var(--surface-2); }
        .pm1-card-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .pm1-lbl { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink-2); }
        .pm1-hint { display: block; margin-top: 1px; font-size: 11.5px; color: var(--muted); }
        .pm1-card-line { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 9px; margin-top: 9px; border-top: 1px solid var(--line-soft); font-size: 12.5px; color: var(--ink-2); }
        .pm1-card-row + .pm1-card-line { }
        .pm1-card > .pm1-card-line:first-child { padding-top: 0; margin-top: 0; border-top: 0; }
        .pm1-card-line b { font-size: 13px; font-weight: 600; color: var(--ink); }
        .pm1-card-line b.pos { color: var(--ok); }
        .pm1-card-line b.neg { color: var(--ink-2); }
        .pm1-card-total { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 11px; margin-top: 11px; border-top: 1px solid var(--navy-line); font-size: 13px; font-weight: 600; color: var(--navy-text); }
        .pm1-card-total b { font-size: 17px; font-weight: 600; color: var(--navy-text); }

        /* Détail des éléments de paie dans le tiroir : une ligne par élément,
           avec son traitement fiscal et social, comme sur la maquette. */
        .pm1-elements { display: flex; flex-direction: column; gap: 7px; }
        .pm1-el-line { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 13px; border: 1px solid var(--line); border-radius: 10px; background: var(--surface); }
        .pm1-el-line.socle { background: var(--surface-2); }
        .pm1-el-main { min-width: 0; }
        .pm1-el-main b { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink); }
        .pm1-el-meta { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; margin-top: 3px; font-size: 11px; color: var(--muted); }
        .pm1-el-tag { padding: 1px 7px; border-radius: 5px; background: var(--surface-2); font-size: 10.5px; font-weight: 600; color: var(--muted); white-space: nowrap; }
        .pm1-el-tag.fisc { background: var(--warn-bg); color: var(--warn); }
        .pm1-el-tag.soc { background: var(--navy-tint); color: var(--navy-text); }
        .pm1-el-amt { flex-shrink: 0; font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap; }
        .pm1-el-amt small { font-size: 10.5px; font-weight: 400; color: var(--muted); }
        .pm1-el-amt.pos { color: var(--ok); }
        .pm1-el-amt.neg { color: var(--bad); }
        .pm1-el-vide { margin: 0; padding: 14px; border: 1px dashed var(--line); border-radius: 10px; font-size: 12.5px; color: var(--muted); text-align: center; }
        .pm1-el-totaux { margin-top: 10px; }

        .pm1-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }

        .pm1-actions-titre { width: 100%; margin-bottom: -2px; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }

        /* Accès aux autres natures d'éléments, depuis le tiroir. */
        .pm1-traitement { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .pm1-tr { display: flex; align-items: center; gap: 9px; padding: 11px 13px; border: 1px solid var(--line); border-radius: 10px; background: var(--surface-2); font-size: 12.5px; font-weight: 600; color: var(--ink-2); text-decoration: none; }
        .pm1-tr:hover { border-color: var(--navy-line); background: var(--navy-tint); color: var(--navy-text); }
        .pm1-tr i { width: 15px; text-align: center; font-size: 13px; color: var(--navy); }
        @media (max-width: 640px) { .pm1-traitement { grid-template-columns: minmax(0, 1fr); } }

        .pm1-drawer-foot { display: flex; gap: 10px; padding: 14px 22px; border-top: 1px solid var(--line); }
        .pm1-drawer-foot .btn { flex: 1; }
        .pm1-drawer-foot .btn-outline-secondary { flex: 0 0 auto; }

        @media (max-width: 992px) {
            .pm1-drawer { width: 100%; max-width: none; }
        }
        @media (max-width: 768px) {
            .pm1-tools { flex-direction: column; align-items: stretch; }
            .pm1-search { max-width: none; }
            .pm1-filter { width: 100%; }
            .pm1-audit { flex-direction: column; align-items: flex-start; }
        }

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
