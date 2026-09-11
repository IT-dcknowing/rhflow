<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#1A2B5C">
  <meta name="description"
    content="Guide d'utilisation complet du logiciel RH Flow : gestion des employés, contrats, paie, congés, déclarations fiscales et pointage pour les entreprises ivoiriennes.">
  <title>RH Flow — Guide d'utilisation complet</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
    rel="stylesheet">
  <style>
    /* ============================================================
   RH FLOW — GUIDE D'UTILISATEUR
   Palette : Deep Navy + Slate + Gold
   ============================================================ */
    :root {
      --primary: #1A2B5C;
      --primary-mid: #2543A0;
      --primary-bright: #3B62D4;
      --primary-light: rgba(26, 43, 92, 0.07);
      --primary-glow: rgba(59, 98, 212, 0.15);

      --gold: #C9922A;
      --gold-light: rgba(201, 146, 42, 0.12);

      --success: #0D9268;
      --success-bg: #ECFDF5;
      --success-border: #A7F3D0;
      --warning: #B45309;
      --warning-bg: #FFFBEB;
      --warning-border: #FCD34D;
      --error: #B91C1C;
      --error-bg: #FEF2F2;
      --error-border: #FECACA;
      --info: #0369A1;
      --info-bg: #F0F9FF;
      --info-border: #BAE6FD;

      --bg-page: #F7F8FC;
      --bg-sidebar: #FFFFFF;
      --bg-header: rgba(255, 255, 255, 0.95);
      --bg-card: #FFFFFF;
      --bg-hover: #F1F3F9;
      --bg-code: #0F172A;

      --text-main: #0F1623;
      --text-body: #1F2937;
      --text-muted: #6B7280;
      --text-subtle: #9CA3AF;
      --text-inverse: #FFFFFF;

      --border: #E2E5EF;
      --border-strong: #C8CEDF;

      --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      --mono: 'JetBrains Mono', 'Courier New', monospace;

      --sidebar-w: 280px;
      --header-h: 60px;
      --radius-sm: 6px;
      --radius-md: 10px;
      --radius-lg: 14px;

      --shadow-sm: 0 1px 3px rgba(15, 22, 35, 0.06);
      --shadow-md: 0 4px 14px rgba(15, 22, 35, 0.08);
      --shadow-lg: 0 10px 40px rgba(15, 22, 35, 0.12);
      --shadow-focus: 0 0 0 3px rgba(59, 98, 212, 0.25);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--font);
      color: var(--text-body);
      background: var(--bg-page);
      font-size: 15px;
      line-height: 1.65;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    ::selection {
      background: var(--primary-glow);
      color: var(--primary);
    }

    /* Focus visible pour accessibilité clavier */
    :focus-visible {
      outline: 2px solid var(--primary-bright);
      outline-offset: 2px;
      border-radius: 4px;
    }

    /* Skip link pour lecteurs d'écran */
    .skip-link {
      position: absolute;
      top: -50px;
      left: 8px;
      background: var(--primary);
      color: #fff;
      padding: 10px 16px;
      border-radius: var(--radius-sm);
      z-index: 2000;
      font-weight: 600;
      text-decoration: none;
    }

    .skip-link:focus {
      top: 8px;
    }

    /* ===== HEADER ===== */
    header[role="banner"] {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: var(--header-h);
      background: var(--bg-header);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 20px;
      gap: 16px;
      z-index: 1000;
      box-shadow: var(--shadow-sm);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-main);
    }

    .brand-mark {
      width: 34px;
      height: 34px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-mid) 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 8px rgba(26, 43, 92, 0.25);
      color: #fff;
      font-weight: 800;
      font-size: 14px;
      letter-spacing: -0.5px;
    }

    .brand-name {
      font-size: 16px;
      font-weight: 700;
      letter-spacing: -0.2px;
    }

    .brand-ci {
      background: var(--gold-light);
      color: var(--gold);
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 4px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .header-search {
      flex: 1;
      max-width: 380px;
      position: relative;
      margin-left: 8px;
    }

    .header-search input {
      width: 100%;
      padding: 9px 14px 9px 38px;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      font-family: inherit;
      font-size: 13px;
      background: var(--bg-hover);
      color: var(--text-body);
      outline: none;
      transition: all 0.2s;
    }

    .header-search input:focus {
      border-color: var(--primary-bright);
      background: #fff;
      box-shadow: var(--shadow-focus);
    }

    .header-search svg {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 15px;
      height: 15px;
      color: var(--text-muted);
      pointer-events: none;
    }

    .header-version {
      margin-left: auto;
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
      font-family: var(--mono);
      flex-shrink: 0;
    }

    .menu-toggle {
      display: none;
      background: transparent;
      border: 1px solid var(--border);
      padding: 8px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      color: var(--text-body);
    }

    /* ===== LAYOUT ===== */
    .layout {
      display: flex;
      margin-top: var(--header-h);
      min-height: calc(100vh - var(--header-h));
    }

    /* ===== SIDEBAR ===== */
    nav[role="navigation"] {
      width: var(--sidebar-w);
      flex-shrink: 0;
      position: fixed;
      top: var(--header-h);
      left: 0;
      bottom: 0;
      overflow-y: auto;
      background: var(--bg-sidebar);
      border-right: 1px solid var(--border);
      padding: 16px 0 48px;
      z-index: 900;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    nav::-webkit-scrollbar {
      width: 4px;
    }

    nav::-webkit-scrollbar-thumb {
      background: var(--border);
      border-radius: 4px;
    }

    .nav-group {
      margin-bottom: 4px;
    }

    .nav-group-title {
      padding: 14px 22px 6px;
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--text-subtle);
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 22px;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-body);
      text-decoration: none;
      border-left: 3px solid transparent;
      transition: all 0.15s;
    }

    .nav-link:hover {
      background: var(--bg-hover);
      color: var(--text-main);
    }

    .nav-link.active {
      color: var(--primary);
      background: var(--primary-light);
      border-left-color: var(--primary);
      font-weight: 600;
    }

    .nav-link .num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 22px;
      height: 22px;
      padding: 0 6px;
      font-size: 11px;
      font-weight: 700;
      font-family: var(--mono);
      background: var(--bg-hover);
      border-radius: 4px;
      color: var(--text-muted);
    }

    .nav-link.active .num {
      background: var(--primary);
      color: #fff;
    }

    /* ===== MAIN ===== */
    main {
      margin-left: var(--sidebar-w);
      flex: 1;
      padding: 32px 48px 100px;
      max-width: 1080px;
      width: 100%;
    }

    section {
      padding-top: 24px;
      margin-bottom: 48px;
      scroll-margin-top: 80px;
    }

    /* ===== TYPOGRAPHIE ===== */
    h1,
    h2,
    h3,
    h4 {
      color: var(--text-main);
      letter-spacing: -0.3px;
      line-height: 1.3;
    }

    h1 {
      font-size: 34px;
      font-weight: 800;
      margin-bottom: 12px;
      letter-spacing: -0.8px;
    }

    h2 {
      font-size: 26px;
      font-weight: 700;
      margin: 40px 0 16px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--border);
    }

    h3 {
      font-size: 19px;
      font-weight: 700;
      margin: 28px 0 12px;
      color: var(--primary);
    }

    h4 {
      font-size: 16px;
      font-weight: 600;
      margin: 20px 0 8px;
    }

    p {
      margin-bottom: 14px;
    }

    a {
      color: var(--primary-bright);
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: border-color 0.15s;
    }

    a:hover {
      border-bottom-color: var(--primary-bright);
    }

    ul,
    ol {
      margin: 0 0 16px 22px;
    }

    li {
      margin-bottom: 6px;
    }

    code {
      font-family: var(--mono);
      background: var(--primary-light);
      color: var(--primary);
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 0.88em;
      font-weight: 500;
    }

    pre {
      background: var(--bg-code);
      color: #E2E8F0;
      padding: 16px 18px;
      border-radius: var(--radius-md);
      font-family: var(--mono);
      font-size: 13px;
      overflow-x: auto;
      margin: 16px 0;
      line-height: 1.6;
    }

    pre code {
      background: transparent;
      color: inherit;
      padding: 0;
      font-size: inherit;
    }

    /* ===== HERO ===== */
    .hero {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-mid) 100%);
      color: #fff;
      padding: 44px 40px;
      border-radius: var(--radius-lg);
      margin-bottom: 32px;
      position: relative;
      overflow: hidden;
    }

    .hero::after {
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(201, 146, 42, 0.18) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-eyebrow {
      display: inline-block;
      background: var(--gold-light);
      color: #F4C770;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 4px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-bottom: 16px;
    }

    .hero h1 {
      color: #fff;
      font-size: 38px;
      margin-bottom: 14px;
    }

    .hero-sub {
      color: rgba(255, 255, 255, 0.85);
      font-size: 16px;
      max-width: 640px;
      line-height: 1.65;
    }

    .hero-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 24px;
      font-size: 13px;
      color: rgba(255, 255, 255, 0.8);
    }

    .hero-meta strong {
      color: #fff;
      font-weight: 600;
    }

    /* ===== GRID STATS ===== */
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
      margin: 24px 0;
    }

    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 18px;
    }

    .stat-label {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .stat-value {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-main);
    }

    /* ===== CALLOUTS ===== */
    .callout {
      display: flex;
      gap: 12px;
      padding: 14px 18px;
      border-radius: var(--radius-md);
      margin: 16px 0;
      border: 1px solid;
      border-left-width: 4px;
      font-size: 14.5px;
    }

    .callout-icon {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      margin-top: 2px;
    }

    .callout-body {
      flex: 1;
    }

    .callout-title {
      font-weight: 700;
      margin-bottom: 4px;
      color: var(--text-main);
    }

    .callout.info {
      background: var(--info-bg);
      border-color: var(--info-border);
      color: var(--info);
    }

    .callout.info .callout-title {
      color: var(--info);
    }

    .callout.success {
      background: var(--success-bg);
      border-color: var(--success-border);
      color: var(--success);
    }

    .callout.success .callout-title {
      color: var(--success);
    }

    .callout.warning {
      background: var(--warning-bg);
      border-color: var(--warning-border);
      color: var(--warning);
    }

    .callout.warning .callout-title {
      color: var(--warning);
    }

    .callout.error {
      background: var(--error-bg);
      border-color: var(--error-border);
      color: var(--error);
    }

    .callout.error .callout-title {
      color: var(--error);
    }

    /* ===== TABLE ===== */
    .table-wrap {
      overflow-x: auto;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      margin: 18px 0;
      background: var(--bg-card);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    thead {
      background: var(--bg-hover);
    }

    th {
      text-align: left;
      padding: 12px 14px;
      font-weight: 700;
      font-size: 12.5px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-main);
      border-bottom: 1px solid var(--border);
    }

    td {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      color: var(--text-body);
      vertical-align: top;
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    tbody tr:hover {
      background: var(--bg-hover);
    }

    /* ===== STEPS ===== */
    .steps {
      list-style: none;
      margin: 20px 0;
      padding: 0;
      counter-reset: step;
    }

    .steps li {
      position: relative;
      padding: 16px 18px 16px 56px;
      margin-bottom: 12px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      counter-increment: step;
    }

    .steps li::before {
      content: counter(step);
      position: absolute;
      left: 16px;
      top: 16px;
      width: 28px;
      height: 28px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-mid) 100%);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 13px;
      box-shadow: 0 2px 6px rgba(26, 43, 92, 0.25);
    }

    .steps li strong {
      display: block;
      color: var(--text-main);
      margin-bottom: 4px;
      font-size: 15px;
    }

    /* ===== CARDS GRID ===== */
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 16px;
      margin: 20px 0;
    }

    .card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 20px;
      transition: all 0.2s;
    }

    .card:hover {
      border-color: var(--primary-bright);
      box-shadow: var(--shadow-md);
      transform: translateY(-2px);
    }

    .card-icon {
      width: 40px;
      height: 40px;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      font-size: 18px;
      font-weight: 700;
    }

    .card h4 {
      margin-top: 0;
      margin-bottom: 6px;
    }

    .card p {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 0;
    }

    /* ===== BADGE ===== */
    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .badge.primary {
      background: var(--primary-light);
      color: var(--primary);
    }

    .badge.success {
      background: var(--success-bg);
      color: var(--success);
    }

    .badge.warning {
      background: var(--warning-bg);
      color: var(--warning);
    }

    .badge.error {
      background: var(--error-bg);
      color: var(--error);
    }

    /* ===== TOC INLINE ===== */
    .toc-inline {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 20px 24px;
      margin: 24px 0;
    }

    .toc-inline h4 {
      margin-top: 0;
      margin-bottom: 12px;
      color: var(--text-main);
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .toc-inline ol {
      margin-left: 18px;
      columns: 2;
      column-gap: 32px;
    }

    .toc-inline li {
      break-inside: avoid;
      margin-bottom: 6px;
      font-size: 14px;
    }

    /* ===== FAQ ===== */
    details {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 14px 18px;
      margin-bottom: 10px;
    }

    details[open] {
      box-shadow: var(--shadow-sm);
    }

    summary {
      cursor: pointer;
      font-weight: 600;
      color: var(--text-main);
      list-style: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }

    summary::-webkit-details-marker {
      display: none;
    }

    summary::after {
      content: '+';
      font-size: 20px;
      color: var(--primary);
      font-weight: 400;
      transition: transform 0.2s;
    }

    details[open] summary::after {
      transform: rotate(45deg);
    }

    details>p,
    details>ul {
      margin-top: 12px;
      font-size: 14.5px;
    }

    /* ===== FOOTER ===== */
    footer {
      margin-top: 60px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
      color: var(--text-muted);
      font-size: 13px;
      text-align: center;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 960px) {
      .menu-toggle {
        display: inline-flex;
      }

      .header-search {
        display: none;
      }

      nav[role="navigation"] {
        transform: translateX(-100%);
        box-shadow: var(--shadow-lg);
      }

      nav.open {
        transform: translateX(0);
      }

      main {
        margin-left: 0;
        padding: 24px 20px 80px;
      }

      .hero {
        padding: 30px 24px;
      }

      .hero h1 {
        font-size: 28px;
      }

      h1 {
        font-size: 26px;
      }

      h2 {
        font-size: 22px;
      }

      .toc-inline ol {
        columns: 1;
      }
    }

    @media (max-width: 560px) {
      .brand-name {
        display: none;
      }

      .hero-meta {
        flex-direction: column;
        gap: 8px;
      }
    }

    /* Overlay pour mobile */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: var(--header-h) 0 0 0;
      background: rgba(15, 22, 35, 0.5);
      z-index: 899;
    }

    .sidebar-overlay.show {
      display: block;
    }

    /* Print */
    @media print {

      header,
      nav,
      .menu-toggle,
      .sidebar-overlay {
        display: none !important;
      }

      main {
        margin-left: 0;
        max-width: 100%;
        padding: 0;
      }

      body {
        background: #fff;
        font-size: 11pt;
      }

      .hero {
        background: #fff;
        color: #000;
        border: 2px solid #000;
      }

      .hero h1,
      .hero-sub {
        color: #000;
      }

      section {
        page-break-inside: avoid;
      }
    }
  </style>
</head>

<body>

  <a href="#content" class="skip-link">Aller au contenu principal</a>

  <header role="banner">
    <button class="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" id="menuToggle">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
      </svg>
    </button>
    <a href="#intro" class="brand" aria-label="RH Flow — Accueil du guide">
      <span class="brand-mark" aria-hidden="true">RH</span>
      <span class="brand-name">RH Flow</span>
      <span class="brand-ci">Côte d'Ivoire</span>
    </a>
    <div class="header-search" role="search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
      </svg>
      <label for="searchInput" class="sr-only"
        style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Rechercher dans le
        guide</label>
      <input type="search" id="searchInput" placeholder="Rechercher une rubrique…"
        aria-label="Rechercher dans le guide">
    </div>
    <span class="header-version" aria-label="Version du guide">v3.0 · 2026</span>
  </header>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="layout">

    <nav role="navigation" aria-label="Sommaire du guide" id="sidebar">
      <div class="nav-group">
        <div class="nav-group-title">Démarrer</div>
        <a class="nav-link" href="#intro"><span class="num">01</span>Introduction</a>
        <a class="nav-link" href="#demarrage"><span class="num">02</span>Démarrage rapide</a>
        <a class="nav-link" href="#connexion"><span class="num">03</span>Connexion &amp; profils</a>
        <a class="nav-link" href="#dashboard"><span class="num">04</span>Tableaux de bord</a>
      </div>
      <div class="nav-group">
        <div class="nav-group-title">Ressources Humaines</div>
        <a class="nav-link" href="#employes"><span class="num">05</span>Gestion des employés</a>
        <a class="nav-link" href="#famille-cmu"><span class="num">06</span>Famille &amp; CMU</a>
        <a class="nav-link" href="#contrats"><span class="num">07</span>Contrats &amp; avenants</a>
        <a class="nav-link" href="#pointage"><span class="num">08</span>Pointage &amp; présences</a>
        <a class="nav-link" href="#conges"><span class="num">09</span>Congés</a>
        <a class="nav-link" href="#heures-sup"><span class="num">10</span>Heures supplémentaires</a>
        <a class="nav-link" href="#ruptures"><span class="num">11</span>Sanctions &amp; ruptures</a>
      </div>
      <div class="nav-group">
        <div class="nav-group-title">Paie</div>
        <a class="nav-link" href="#cycle-paie"><span class="num">12</span>Cycle mensuel de paie</a>
        <a class="nav-link" href="#elements-brut"><span class="num">13</span>Éléments du brut</a>
        <a class="nav-link" href="#retenues-prets"><span class="num">14</span>Retenues &amp; prêts</a>
        <a class="nav-link" href="#calcul-bulletin"><span class="num">15</span>Calcul &amp; bulletins</a>
        <a class="nav-link" href="#formules"><span class="num">16</span>Formules &amp; taux</a>
      </div>
      <div class="nav-group">
        <div class="nav-group-title">Déclarations</div>
        <a class="nav-link" href="#declarations-mensuelles"><span class="num">17</span>Déclarations mensuelles</a>
        <a class="nav-link" href="#declarations-annuelles"><span class="num">18</span>Déclarations annuelles</a>
      </div>
      <div class="nav-group">
        <div class="nav-group-title">Aller plus loin</div>
        <a class="nav-link" href="#parametres"><span class="num">19</span>Paramètres &amp; sécurité</a>
        <a class="nav-link" href="#assistant"><span class="num">20</span>Assistant IA</a>
        <a class="nav-link" href="#faq"><span class="num">21</span>FAQ &amp; dépannage</a>
        <a class="nav-link" href="#support"><span class="num">22</span>Support</a>
      </div>
    </nav>

    <main id="content" role="main">

      <!-- ============ HERO ============ -->
      <div class="hero">
        <span class="hero-eyebrow">Guide d'utilisation · Édition 2026</span>
        <h1>RH Flow — Le guide complet</h1>
        <p class="hero-sub">
          Maîtrisez la gestion RH et la paie de votre entreprise ivoirienne : des employés aux bulletins, des contrats
          aux déclarations CNPS, CMU et ITS. Ce guide couvre <strong>l'intégralité du logiciel</strong> avec procédures
          pas-à-pas, règles métier et formules de calcul conformes SYSCOHADA.
        </p>
        <div class="hero-meta">
          <span>📦 <strong>6 modules</strong> métier</span>
          <span>🇨🇮 <strong>Fiscalité ivoirienne</strong> intégrée</span>
          <span>💱 <strong>Franc CFA</strong> (XOF)</span>
          <span>🔐 <strong>Multi-société</strong> sécurisé</span>
        </div>
      </div>

      <!-- ============ INTRODUCTION ============ -->
      <section id="intro">
        <h2>1. Introduction</h2>
        <p>
          <strong>RH Flow</strong> est une solution complète de gestion des ressources humaines et de la paie, conçue
          pour les entreprises opérant en Côte d'Ivoire. L'application automatise l'ensemble du cycle de vie du salarié
          : de l'embauche à la sortie, en passant par le contrat, la paie mensuelle, les déclarations fiscales et
          sociales, et le pilotage des effectifs.
        </p>

        <div class="stats">
          <div class="stat-card">
            <div class="stat-label">Modules</div>
            <div class="stat-value">6</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Types de contrats</div>
            <div class="stat-value">4</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Tranches ITS</div>
            <div class="stat-value">6</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Méthodes de pointage</div>
            <div class="stat-value">3</div>
          </div>
        </div>

        <h3>À qui s'adresse ce guide ?</h3>
        <div class="cards">
          <div class="card">
            <div class="card-icon" aria-hidden="true">👔</div>
            <h4>Dirigeants &amp; DG</h4>
            <p>Pilotage des effectifs, masse salariale et indicateurs RH depuis le Tableau de bord Entreprise.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">📋</div>
            <h4>Responsables RH</h4>
            <p>Administration du personnel, contrats, congés, sanctions et événements RH.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">💰</div>
            <h4>Gestionnaires paie</h4>
            <p>Traitement mensuel de la paie, bulletins, déclarations CNPS, CMU, ITS.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">🧑💼</div>
            <h4>Salariés</h4>
            <p>Consultation des bulletins, demandes de congés, pointage QR Code depuis l'espace salarié.</p>
          </div>
        </div>

        <h3>Principes fondamentaux</h3>
        <ul>
          <li><strong>Multi-société</strong> : toutes les données sont isolées par entreprise (<code>company_id</code>).
            Un utilisateur ne voit jamais les données d'une autre société.</li>
          <li><strong>Quota d'abonnement</strong> : chaque plan impose un nombre maximum de salariés actifs
            (<code>max_employees</code>). Toute création au-delà est bloquée.</li>
          <li><strong>Exercice &amp; période actifs</strong> : la paie s'opère toujours dans le contexte d'un exercice
            et d'une période actifs. Aucun calcul n'est possible hors période ouverte.</li>
          <li><strong>Soft delete</strong> : la « suppression » d'un salarié désactive son compte
            (<code>is_active = 0</code>) sans perte d'historique paie.</li>
        </ul>
      </section>

      <!-- ============ DEMARRAGE ============ -->
      <section id="demarrage">
        <h2>2. Démarrage rapide</h2>
        <p>Voici le <strong>parcours recommandé</strong> pour configurer RH Flow et traiter votre première paie en moins
          de 2 heures.</p>

        <div class="toc-inline" aria-label="Plan de prise en main">
          <h4>Plan de prise en main</h4>
          <ol>
            <li>Configurer l'entreprise (raison sociale, logo, NCC, IDU)</li>
            <li>Créer les services et les postes</li>
            <li>Définir les types de congés</li>
            <li>Saisir les éléments de paie (primes, indemnités)</li>
            <li>Créer les salariés (Mensuels / Journaliers)</li>
            <li>Associer un contrat à chaque salarié</li>
            <li>Ouvrir la période de paie du mois en cours</li>
            <li>Générer les bulletins &amp; déclarations</li>
          </ol>
        </div>

        <h3>Onboarding en une seule fois</h3>
        <ol class="steps">
          <li>
            <strong>Configuration de l'entreprise</strong>
            Renseignez la raison sociale, le numéro de compte contribuable (NCC), l'IDU (CNPS), l'adresse, le logo, la
            devise (XOF) et la couleur principale des bulletins. Ces informations apparaîtront sur tous les documents
            officiels.
          </li>
          <li>
            <strong>Référentiels organisationnels</strong>
            Déclarez vos <em>branches</em>, <em>services</em>, <em>postes</em> et <em>catégories professionnelles</em>.
            Ces référentiels sont obligatoires pour créer un salarié.
          </li>
          <li>
            <strong>Types de congés</strong>
            Configurez les motifs (annuel, maladie, maternité, exceptionnel…) avec leurs règles d'acquisition et soldes
            initiaux.
          </li>
          <li>
            <strong>Création des salariés</strong>
            Distinguez les <em>Mensuels</em> (salaire fixe) et les <em>Journaliers</em> (rémunération à l'heure/jour).
            Un compte utilisateur est créé automatiquement pour l'accès à l'espace salarié.
          </li>
          <li>
            <strong>Contrats de travail</strong>
            Associez immédiatement un contrat (CDI, CDD, Stage, Consultant) à chaque salarié après sa création. Sans
            contrat actif, aucune paie n'est possible.
          </li>
        </ol>

        <div class="callout success" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Astuce</div>
            Une fois l'onboarding terminé, vous n'aurez plus qu'à répéter le <strong>cycle mensuel de paie en 4
              étapes</strong> (voir §12) chaque mois.
          </div>
        </div>
      </section>

      <!-- ============ CONNEXION ============ -->
      <section id="connexion">
        <h2>3. Connexion &amp; profils utilisateurs</h2>
        <p>
          La page de connexion accepte indifféremment une <strong>adresse email</strong> ou un <strong>nom
            d'utilisateur</strong>. Si la première tentative échoue, le système tente automatiquement l'autre méthode.
        </p>

        <h3>Orientation selon le profil</h3>
        <div class="table-wrap">
          <table>
            <caption style="text-align:left;padding:12px 14px;font-weight:600;color:var(--text-main)">Redirection après
              authentification</caption>
            <thead>
              <tr>
                <th scope="col">Type d'utilisateur</th>
                <th scope="col">Espace d'arrivée</th>
                <th scope="col">Droits</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><code>super_admin</code></td>
                <td>Tableau de bord Super Admin</td>
                <td>Toutes les sociétés, facturation</td>
              </tr>
              <tr>
                <td><code>entreprise</code> (DG)</td>
                <td>Tableau de bord Entreprise</td>
                <td>Pilotage global, paramètres</td>
              </tr>
              <tr>
                <td><code>hr</code> / <code>paie</code></td>
                <td>Tableau de bord RH</td>
                <td>Salariés, contrats, paie</td>
              </tr>
              <tr>
                <td><code>employee</code></td>
                <td>Espace salarié</td>
                <td>Bulletins, congés, pointage</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="16" x2="12" y2="12" />
            <line x1="12" y1="8" x2="12.01" y2="8" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Règles de sécurité</div>
            Mot de passe : minimum 8 caractères · Session : 30 min d'inactivité · 5 tentatives de connexion maximum
            avant blocage temporaire.
          </div>
        </div>
      </section>

      <!-- ============ DASHBOARD ============ -->
      <section id="dashboard">
        <h2>4. Tableaux de bord</h2>
        <p>RH Flow propose deux vues complémentaires selon votre rôle.</p>

        <div class="cards">
          <div class="card">
            <div class="card-icon" aria-hidden="true">🏢</div>
            <h4>Tableau de bord Entreprise</h4>
            <p>Vision directionnelle : effectifs totaux, <strong>masse salariale</strong>, charges patronales, éléments
              de paie agrégés, alertes fiscales.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">👥</div>
            <h4>Tableau de bord RH</h4>
            <p>Vision opérationnelle : répartition par service, statut matrimonial, nouveaux entrants, demandes en
              attente.</p>
          </div>
        </div>

        <h3>Widgets clés</h3>
        <ul>
          <li><strong>Effectifs</strong> : total, mensuels vs journaliers, actifs vs inactifs.</li>
          <li><strong>Masse salariale mensuelle</strong> (brut, net, coût employeur).</li>
          <li><strong>Répartition par service / poste / genre / statut matrimonial</strong> (graphiques en donut).</li>
          <li><strong>Alertes</strong> : échéances fiscales (ITS, CNPS), contrats CDD arrivant à terme, anniversaires,
            congés en attente.</li>
          <li><strong>Événements RH</strong> : calendrier des anniversaires, départs, formations.</li>
        </ul>

        <div class="callout warning" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Bandeaux d'échéance</div>
            Un bandeau rouge apparaît dès qu'une échéance fiscale approche. Ne l'ignorez pas : des pénalités
            s'appliquent en cas de retard de déclaration CNPS ou ITS.
          </div>
        </div>
      </section>

      <!-- ============ EMPLOYES ============ -->
      <section id="employes">
        <h2>5. Gestion des employés</h2>
        <p>La fiche employé est structurée en <strong>4 étapes guidées</strong> lors de la création.</p>

        <h3>Création d'un salarié</h3>
        <ol class="steps">
          <li>
            <strong>Informations personnelles</strong>
            Nom, prénoms, genre, date de naissance, situation matrimoniale, nombre d'enfants, nombre de personnes à
            charge, statut local/expatrié. Les <strong>parts fiscales</strong> sont calculées automatiquement (voir
            encart ci-dessous).
          </li>
          <li>
            <strong>Données du poste</strong>
            Matricule (unique par entreprise, auto-généré au format <code>EMP{année}{mois}{séquence}</code>), branche,
            service, poste, catégorie, date d'embauche.
          </li>
          <li>
            <strong>Documents</strong>
            CV, pièce d'identité, diplômes, certificats. Formats acceptés : PDF, JPG, PNG (max 10 Mo).
          </li>
          <li>
            <strong>Informations financières</strong>
            Salaire mensuel (ou horaire pour les journaliers), banque, RIB, numéro CNPS. Le <strong>taux
              horaire</strong> est calculé automatiquement : <code>salaire_mensuel / 173.33</code>.
          </li>
        </ol>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Calcul des parts fiscales</div>
            <code>Parts = 1 + (0.5 × marié) + (0.5 × nombre d'enfants) + (0.5 × personnes à charge infirmes)</code>. Ce
            nombre détermine la réduction d'impôt ITS applicable.
          </div>
        </div>

        <h3>Règles d'unicité</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Champ</th>
                <th scope="col">Règle</th>
                <th scope="col">Message d'erreur</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><code>employee_id</code></td>
                <td>Unique par entreprise</td>
                <td>« L'ID Employé est déjà attribué. Rafraîchissez la page. »</td>
              </tr>
              <tr>
                <td><code>username</code></td>
                <td>Unique global</td>
                <td>Format <code>prenom.nom</code> avec suffixe numérique si doublon</td>
              </tr>
              <tr>
                <td>Effectifs</td>
                <td>≤ <code>max_employees</code> du plan</td>
                <td>Redirection vers la page d'abonnement</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Mensuel vs Journalier</h3>
        <div class="cards">
          <div class="card">
            <div class="card-icon" aria-hidden="true">📅</div>
            <h4>Employé Courant (Mensuel)</h4>
            <p>Salaire fixe mensuel, bulletin chaque fin de mois, intégré au cycle de paie standard. Typique : CDI, CDD
              long.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">⏱️</div>
            <h4>Employé Journalier</h4>
            <p>Rémunération à la journée ou à l'heure, pointage quotidien obligatoire, paie calculée sur présences
              réelles.</p>
          </div>
        </div>

        <div class="callout success" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Automatisations à la création</div>
            Si la date de naissance est renseignée, un <strong>événement d'anniversaire</strong> est publié
            automatiquement. Un <strong>compte utilisateur</strong> de type <code>employee</code> est créé pour l'accès
            à l'espace salarié.
          </div>
        </div>

        <h3>Modification et désactivation</h3>
        <ul>
          <li>La suppression n'est jamais physique : <code>is_active = 0</code> conserve l'historique paie.</li>
          <li>Le bouton de bascule Actif/Inactif est protégé par une vérification d'appartenance à l'entreprise.</li>
          <li>Lors d'une mise à jour, le mot de passe n'est modifié que s'il est explicitement saisi.</li>
        </ul>
      </section>

      <!-- ============ FAMILLE CMU ============ -->
      <section id="famille-cmu">
        <h2>6. Famille &amp; Couverture Maladie Universelle</h2>
        <p>Chaque salarié peut déclarer ses membres de famille rattachés à la CMU.</p>

        <h3>Ajouter un membre de famille</h3>
        <ol>
          <li>Ouvrez la fiche du salarié, onglet <strong>Famille</strong>.</li>
          <li>Cliquez sur <em>Ajouter un membre</em> et renseignez : nom, prénoms, genre, type (conjoint, enfant,
            ascendant), statut CMU (<code>Oui</code> / <code>Non</code>).</li>
          <li>Si <code>CMU = Oui</code>, joignez le document justificatif (PDF, JPG, PNG — max 2 Mo).</li>
        </ol>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Synchronisation automatique CMU</div>
            Lorsque <code>CMU = Oui</code>, une ligne de rattachement familial est créée dans
            <code>employee_cmus</code>. Si le statut repasse à <code>Non</code>, la ligne est automatiquement supprimée.
          </div>
        </div>

        <h3>Documents du salarié</h3>
        <ul>
          <li>Chaque document requiert un <strong>libellé</strong> et un fichier.</li>
          <li>La suppression d'un document entraîne la suppression physique du fichier sur le stockage.</li>
          <li>Seuls les documents rattachés à la société de l'utilisateur sont modifiables.</li>
        </ul>
      </section>

      <!-- ============ CONTRATS ============ -->
      <section id="contrats">
        <h2>7. Contrats &amp; avenants</h2>
        <p>Tout salarié doit être lié à un <strong>contrat actif</strong> pour apparaître dans le cycle de paie.</p>

        <h3>Types de contrats pris en charge</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Type</th>
                <th scope="col">Code</th>
                <th scope="col">Durée</th>
                <th scope="col">Spécificités</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>CDI</td>
                <td><code>CDI</code></td>
                <td>Indéterminée</td>
                <td>Aucune date de fin</td>
              </tr>
              <tr>
                <td>CDD</td>
                <td><code>CDD</code></td>
                <td>Déterminée</td>
                <td>Date de fin + motif de recours obligatoires</td>
              </tr>
              <tr>
                <td>Stage</td>
                <td><code>STAGE</code></td>
                <td>24 mois max.</td>
                <td>Établissement + niveau d'étude requis</td>
              </tr>
              <tr>
                <td>Consultant</td>
                <td><code>CONSULT</code></td>
                <td>Prestation</td>
                <td>Facturation — hors déclarations CNPS salarié</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Création d'un contrat</h3>
        <ol class="steps">
          <li>
            <strong>Informations générales</strong>
            Sujet du contrat, salarié, type, date de début, date de fin (optionnelle, mais postérieure à la date de
            début).
          </li>
          <li>
            <strong>Pièces jointes</strong>
            Téléversez le document PDF signé (max 10 Mo par fichier).
          </li>
          <li>
            <strong>Signatures numériques</strong>
            Capture des signatures du salarié et de l'entreprise (données base64) avec horodatage.
          </li>
          <li>
            <strong>Activation</strong>
            Le statut initial est <code>accept</code>. La fiche salarié est automatiquement mise à jour avec la date
            d'embauche et la date de fin de relation.
          </li>
        </ol>

        <h3>Avenants</h3>
        <p>Deux types d'avenants sont disponibles :</p>
        <ul>
          <li><strong>Reconduction</strong> : met à jour dates, type de contrat, catégorie, poste, branche et réactive
            le salarié si nécessaire.</li>
          <li><strong>Modification salariale</strong> : ajuste la rémunération sans rompre le contrat.</li>
        </ul>

        <div class="callout warning" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Types de contrat non supprimables</div>
            Un type de contrat utilisé par au moins un contrat actif ou archivé ne peut pas être supprimé — seulement
            désactivé.
          </div>
        </div>
      </section>

      <!-- ============ POINTAGE ============ -->
      <section id="pointage">
        <h2>8. Pointage &amp; présences</h2>
        <p>RH Flow gère trois méthodes de pointage, utilisables simultanément selon les sites.</p>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Méthode</th>
                <th scope="col">Équipement</th>
                <th scope="col">Processus</th>
                <th scope="col">Validation</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>QR Code</td>
                <td>Smartphone</td>
                <td>Scan entrée/sortie</td>
                <td>Automatique</td>
              </tr>
              <tr>
                <td>Biométrique</td>
                <td>Pointeuse dédiée</td>
                <td>Empreinte digitale</td>
                <td>Automatique</td>
              </tr>
              <tr>
                <td>Manuel</td>
                <td>Navigateur web</td>
                <td>Formulaire hebdomadaire</td>
                <td>Manager / RH</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Tableau de pointage hebdomadaire</h3>
        <p>
          Le tableau présente une vue semaine par semaine avec filtres par <em>employé</em>, <em>type de période</em>
          (semaine / quinzaine / mois) et <em>mois</em>. Chaque cellule représente un jour ouvré et peut contenir :
          présence, absence justifiée, absence non justifiée, congé, jour férié, repos hebdomadaire.
        </p>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Impact sur la paie</div>
            Les absences non justifiées entraînent une retenue sur salaire calculée au prorata temporis
            (<code>jours absents × salaire_journalier</code>).
          </div>
        </div>
      </section>

      <!-- ============ CONGES ============ -->
      <section id="conges">
        <h2>9. Gestion des congés</h2>
        <p>Le workflow de validation des congés se déroule en <strong>3 étapes hiérarchiques</strong>.</p>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Étape</th>
                <th scope="col">Acteur</th>
                <th scope="col">Action</th>
                <th scope="col">Condition</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Salarié</td>
                <td>Soumission de la demande</td>
                <td>Solde suffisant + dates valides</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Manager N+1</td>
                <td>Validation hiérarchique</td>
                <td>Autorisation niveau 1</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Service RH</td>
                <td>Validation finale</td>
                <td>Vérification administrative + impact paie</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Types de congés</h3>
        <ul>
          <li><strong>Congé annuel</strong> : 2,2 jours ouvrés acquis par mois de présence (27 jours/an — Code du
            Travail ivoirien).</li>
          <li><strong>Congé maladie</strong> : sur présentation d'un certificat médical.</li>
          <li><strong>Congé maternité</strong> : 14 semaines, pris en charge partiellement par la CNPS.</li>
          <li><strong>Congé exceptionnel</strong> : mariage, décès, naissance.</li>
        </ul>

        <div class="callout success" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Solde automatique</div>
            Le solde de congés est recalculé chaque mois en fonction de la présence effective. Vous pouvez l'ajuster
            manuellement depuis la fiche salarié (onglet Congés).
          </div>
        </div>
      </section>

      <!-- ============ HEURES SUP ============ -->
      <section id="heures-sup">
        <h2>10. Heures supplémentaires</h2>
        <p>Les heures supplémentaires sont soumises aux majorations légales ivoiriennes.</p>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Plage horaire</th>
                <th scope="col">Majoration</th>
                <th scope="col">Calcul</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>41<sup>e</sup> à 46<sup>e</sup> heure/semaine</td>
                <td>+15 %</td>
                <td><code>taux_horaire × 1,15</code></td>
              </tr>
              <tr>
                <td>À partir de la 47<sup>e</sup> heure</td>
                <td>+50 %</td>
                <td><code>taux_horaire × 1,50</code></td>
              </tr>
              <tr>
                <td>Heures de nuit (21h–5h)</td>
                <td>+75 %</td>
                <td><code>taux_horaire × 1,75</code></td>
              </tr>
              <tr>
                <td>Dimanche &amp; jours fériés</td>
                <td>+100 %</td>
                <td><code>taux_horaire × 2,00</code></td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Saisie des heures supplémentaires</h3>
        <ol>
          <li>Accédez au menu <em>Heures Supplémentaires</em>.</li>
          <li>Sélectionnez la période de paie active et le salarié.</li>
          <li>Saisissez le nombre d'heures par catégorie (jour / nuit / dimanche).</li>
          <li>Validez — le montant est automatiquement injecté dans le salaire brut du mois.</li>
        </ol>
      </section>

      <!-- ============ RUPTURES ============ -->
      <section id="ruptures">
        <h2>11. Sanctions &amp; ruptures de contrat</h2>
        <p>RH Flow gère l'ensemble des événements disciplinaires et des fins de contrat.</p>

        <h3>Types de sanctions</h3>
        <ul>
          <li>Avertissement écrit</li>
          <li>Blâme</li>
          <li>Mise à pied (avec ou sans salaire)</li>
          <li>Licenciement pour faute simple, grave ou lourde</li>
        </ul>

        <h3>Motifs de rupture</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Motif</th>
                <th scope="col">Indemnités dues</th>
                <th scope="col">Préavis</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Démission</td>
                <td>Congés payés non pris</td>
                <td>Selon ancienneté</td>
              </tr>
              <tr>
                <td>Licenciement économique</td>
                <td>Indemnité légale + congés</td>
                <td>1 à 3 mois</td>
              </tr>
              <tr>
                <td>Licenciement faute grave</td>
                <td>Aucune</td>
                <td>Non</td>
              </tr>
              <tr>
                <td>Fin de CDD</td>
                <td>Indemnité de fin de contrat (si prévue)</td>
                <td>Non</td>
              </tr>
              <tr>
                <td>Retraite</td>
                <td>Indemnité de départ</td>
                <td>Selon convention</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ============ CYCLE PAIE ============ -->
      <section id="cycle-paie">
        <h2>12. Cycle mensuel de paie</h2>
        <p>
          C'est le <strong>cœur opérationnel</strong> de RH Flow. Chaque mois, vous suivez un cycle en <strong>4
            étapes</strong> accessibles depuis la page <em>Période en cours</em>.
        </p>

        <div class="callout warning" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Avant de commencer</div>
            Assurez-vous qu'un <strong>exercice</strong> (ex. 2026) is actif, et qu'une <strong>période</strong> (ex.
            Avril 2026) is ouverte. Sans cela, tous les écrans de paie sont en lecture seule.
          </div>
        </div>

        <ol class="steps">
          <li>
            <strong>Étape 1 — Configuration du salaire brut</strong>
            Éléments constitutifs du brut : salaire de base, primes (transport, performance, responsabilité),
            indemnités, avantages en nature, heures supplémentaires.
          </li>
          <li>
            <strong>Étape 2 — Configuration des retenues</strong>
            Retenues légales (CNPS, ITS, CMU), prêts en cours, acomptes, remboursements de frais, saisies-arrêts.
          </li>
          <li>
            <strong>Étape 3 — Absences et congés</strong>
            Intégration automatique des jours d'absence, congés pris, arrêts maladie et ruptures du mois.
          </li>
          <li>
            <strong>Étape 4 — Calcul &amp; génération</strong>
            Lancement du calcul pour tous les salariés actifs, génération des bulletins PDF, export comptable.
          </li>
        </ol>

        <h3>Clôture de la période</h3>
        <p>
          Une fois les bulletins validés, la période est <strong>clôturée</strong>. Elle devient alors en lecture seule
          et les <strong>déclarations mensuelles</strong> (CNPS, ITS) deviennent disponibles (§17).
        </p>
      </section>

      <!-- ============ ELEMENTS BRUT ============ -->
      <section id="elements-brut">
        <h2>13. Éléments du brut</h2>
        <p>Les éléments du brut se configurent de manière <strong>globale</strong> (tous les salariés) ou
          <strong>individuelle</strong>.</p>

        <h3>Catégories d'éléments</h3>
        <div class="cards">
          <div class="card">
            <h4>Salaire de base</h4>
            <p>Rémunération principale définie dans le contrat. Modifiable uniquement par avenant.</p>
          </div>
          <div class="card">
            <h4>Primes</h4>
            <p>Performance, ancienneté, responsabilité, rendement, 13<sup>e</sup> mois. Imposables.</p>
          </div>
          <div class="card">
            <h4>Indemnités</h4>
            <p>Transport, logement, téléphone, représentation. Certaines sont exonérées sous plafond.</p>
          </div>
          <div class="card">
            <h4>Avantages en nature</h4>
            <p>Logement, véhicule, repas. Évalués forfaitairement et ajoutés au salaire brut imposable.</p>
          </div>
          <div class="card">
            <h4>Heures supplémentaires</h4>
            <p>Calculées automatiquement depuis le module Heures Supplémentaires (§10).</p>
          </div>
          <div class="card">
            <h4>Rappels</h4>
            <p>Régularisations sur mois antérieurs (augmentation rétroactive, prime exceptionnelle).</p>
          </div>
        </div>

        <h3>Affecter un élément à un salarié</h3>
        <ol>
          <li>Ouvrez <em>Gestion de paie → Éléments du brut</em>.</li>
          <li>Sélectionnez l'élément (ex. Prime de transport).</li>
          <li>Cochez les salariés concernés ou utilisez <em>Tout sélectionner</em>.</li>
          <li>Saisissez le montant (fixe ou en pourcentage du salaire de base).</li>
          <li>Cliquez sur <em>Enregistrer les éléments sélectionnés</em>.</li>
        </ol>
      </section>

      <!-- ============ RETENUES PRETS ============ -->
      <section id="retenues-prets">
        <h2>14. Retenues &amp; prêts</h2>

        <h3>Retenues légales (automatiques)</h3>
        <p>Calculées automatiquement à chaque génération de bulletin, elles ne nécessitent aucune saisie manuelle :</p>
        <ul>
          <li><strong>CNPS salarié</strong> : 6,3 % du salaire brut plafonné</li>
          <li><strong>ITS</strong> : barème progressif à 6 tranches (§16)</li>
          <li><strong>CMU</strong> : 1 000 FCFA par personne couverte</li>
        </ul>

        <h3>Retenues volontaires</h3>
        <ul>
          <li>Prêts internes remboursables par mensualités</li>
          <li>Acomptes</li>
          <li>Saisies-arrêts (décision de justice)</li>
          <li>Cotisations syndicales ou mutuelles</li>
        </ul>

        <h3>Gestion d'un prêt</h3>
        <ol class="steps">
          <li>
            <strong>Création</strong>
            Sélectionnez le salarié, saisissez le montant total, le nombre de mensualités, la date de premier
            prélèvement. Le système calcule automatiquement la mensualité.
          </li>
          <li>
            <strong>Prélèvement automatique</strong>
            À chaque cycle de paie, la mensualité est déduite du net à payer jusqu'à extinction du solde.
          </li>
          <li>
            <strong>Suivi</strong>
            L'onglet <em>Prêts</em> affiche à tout moment : capital restant dû, nombre de mensualités restantes, date de
            fin prévisionnelle.
          </li>
        </ol>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Quotité saisissable</div>
            La somme des retenues volontaires ne peut excéder la <strong>quotité saisissable légale</strong> (1/10 à 1/3
            du salaire selon les tranches).
          </div>
        </div>
      </section>

      <!-- ============ CALCUL BULLETIN ============ -->
      <section id="calcul-bulletin">
        <h2>15. Calcul &amp; génération des bulletins</h2>

        <h3>Lancement du calcul</h3>
        <ol>
          <li>Vérifiez que les étapes 1 à 3 du cycle de paie sont terminées pour tous les salariés.</li>
          <li>Ouvrez <em>Gestion de paie → Calcul salaire</em>.</li>
          <li>Cliquez sur <strong>Lancer le calcul</strong>. Le système applique toutes les règles métier et génère les
            bulletins.</li>
          <li>Consultez le récapitulatif : <em>Total bruts</em>, <em>Total cotisations</em>, <em>Total nets</em>,
            <em>Coût employeur</em>.</li>
        </ol>

        <h3>Structure d'un bulletin</h3>
        <ul>
          <li><strong>En-tête</strong> : logo de l'entreprise, période, salarié, matricule, contrat.</li>
          <li><strong>Corps</strong> : tableau des éléments du brut, base imposable, cotisations salariales, retenues,
            net à payer.</li>
          <li><strong>Pied</strong> : cumuls annuels, mentions légales, signature employeur.</li>
        </ul>

        <div class="callout error" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Messages d'erreur fréquents</div>
            <ul style="margin-bottom:0">
              <li><strong>« L'exercice de paie n'est pas actif »</strong> → créez et activez l'exercice annuel.</li>
              <li><strong>« L'employé n'a pas de contrat actif »</strong> → vérifiez les dates du contrat.</li>
              <li><strong>« La période est clôturée »</strong> → rouvrez la période ou créez-en une nouvelle.</li>
            </ul>
          </div>
        </div>

        <h3>Diffusion des bulletins</h3>
        <ul>
          <li><strong>Téléchargement PDF</strong> individuel ou en lot (ZIP).</li>
          <li><strong>Envoi par email</strong> automatique à chaque salarié.</li>
          <li><strong>Publication dans l'espace salarié</strong> : accessible 24/7 depuis le compte personnel.</li>
        </ul>
      </section>

      <!-- ============ FORMULES ============ -->
      <section id="formules">
        <h2>16. Formules &amp; taux de référence</h2>
        <p>Toutes les formules ci-dessous sont appliquées automatiquement par le moteur de calcul.</p>

        <h3>CNPS (Caisse Nationale de Prévoyance Sociale)</h3>
        <pre><code>// Cotisation salariale
cnps_salarie = round(total_salaire_brut_soumis × 6,3) / 100

// Cotisation patronale
cnps_employeur = round(total_salaire_brut_soumis × 7,7) / 100</code></pre>

        <h3>Accident du travail</h3>
        <p>Base de calcul : 75 000 FCFA. Taux variable selon la branche d'activité :</p>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Taux</th>
                <th scope="col">Cotisation mensuelle</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>0,02</td>
                <td>1 500 FCFA</td>
              </tr>
              <tr>
                <td>0,03</td>
                <td>2 250 FCFA</td>
              </tr>
              <tr>
                <td>0,04</td>
                <td>3 000 FCFA</td>
              </tr>
              <tr>
                <td>0,05</td>
                <td>3 750 FCFA</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>CMU</h3>
        <pre><code>// Moins de 7 personnes couvertes
if (nb_personnes &lt; 7) cotisation_cmu = nb_personnes × 500

// À partir de 7 personnes
else cotisation_cmu = 3 000 + (nb_personnes − 6) × 1 000</code></pre>

        <h3>ITS — Impôt sur Traitements et Salaires</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Tranche (FCFA)</th>
                <th scope="col">Taux</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>0 — 75 000</td>
                <td>0 %</td>
              </tr>
              <tr>
                <td>75 001 — 240 000</td>
                <td>16 %</td>
              </tr>
              <tr>
                <td>240 001 — 800 000</td>
                <td>21 %</td>
              </tr>
              <tr>
                <td>800 001 — 2 400 000</td>
                <td>24 %</td>
              </tr>
              <tr>
                <td>2 400 001 — 8 000 000</td>
                <td>28 %</td>
              </tr>
              <tr>
                <td>Plus de 8 000 000</td>
                <td>32 %</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Réduction pour charges de famille (parts fiscales)</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Nombre de parts</th>
                <th scope="col">Réduction mensuelle</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1 part</td>
                <td>0 FCFA</td>
              </tr>
              <tr>
                <td>1,5 part</td>
                <td>5 500 FCFA</td>
              </tr>
              <tr>
                <td>2 parts</td>
                <td>11 000 FCFA</td>
              </tr>
              <tr>
                <td>2,5 parts</td>
                <td>16 500 FCFA</td>
              </tr>
              <tr>
                <td>3 parts</td>
                <td>22 000 FCFA</td>
              </tr>
              <tr>
                <td>Plus de 3 parts</td>
                <td>+5 500 FCFA par demi-part</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Taux horaire</h3>
        <pre><code>taux_horaire = salaire_mensuel / 173,33
// 173,33 = 40 heures × 52 semaines / 12 mois</code></pre>
      </section>

      <!-- ============ DECLARATIONS MENSUELLES ============ -->
      <section id="declarations-mensuelles">
        <h2>17. Déclarations mensuelles</h2>
        <p>Après clôture de la période, les déclarations mensuelles deviennent disponibles.</p>

        <h3>Déclarations générées</h3>
        <div class="cards">
          <div class="card">
            <div class="card-icon" aria-hidden="true">🏥</div>
            <h4>CNPS</h4>
            <p>Déclaration mensuelle des cotisations sociales. Export au format officiel, à déposer avant le <strong>15
                du mois suivant</strong>.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">📊</div>
            <h4>ITS / DGI</h4>
            <p>Déclaration d'Impôt sur Traitements et Salaires. Dépôt avant le <strong>15 du mois suivant</strong> à la
              DGI.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">⚕️</div>
            <h4>CMU</h4>
            <p>Bordereau mensuel CMU des salariés et ayants droit.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">🚜</div>
            <h4>FDFP</h4>
            <p>Contribution au Fonds de Développement de la Formation Professionnelle.</p>
          </div>
        </div>

        <div class="callout warning" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Pénalités de retard</div>
            Tout retard de déclaration entraîne des pénalités calculées sur les montants dus. Configurez les rappels
            automatiques depuis <em>Paramètres → Notifications</em>.
          </div>
        </div>
      </section>

      <!-- ============ DECLARATIONS ANNUELLES ============ -->
      <section id="declarations-annuelles">
        <h2>18. Déclarations annuelles</h2>
        <p><span class="badge warning">Bientôt</span> Module en cours de finalisation.</p>

        <p>Les déclarations annuelles couvriront :</p>
        <ul>
          <li><strong>DISA</strong> — Déclaration Individuelle des Salaires Annuels</li>
          <li><strong>État 301</strong> — Récapitulatif annuel des salaires et cotisations</li>
          <li><strong>Bilan social</strong> pour les entreprises de plus de 300 salariés</li>
          <li><strong>Taxe d'apprentissage</strong></li>
        </ul>
      </section>

      <!-- ============ PARAMETRES ============ -->
      <section id="parametres">
        <h2>19. Paramètres &amp; sécurité</h2>

        <h3>Paramètres entreprise</h3>
        <ul>
          <li>Raison sociale, logo, adresse, NCC, IDU CNPS</li>
          <li>Devise (XOF) et Arrondi supérieur</li>
          <li>Couleur principale des bulletins (optez pour une teinte <strong>à fort contraste</strong> pour garantir la
            lisibilité à l'impression)</li>
          <li>Email d'envoi automatique des bulletins</li>
        </ul>

        <h3>Gestion des utilisateurs</h3>
        <ul>
          <li>Création de comptes RH, Paie, DG avec droits différenciés</li>
          <li>Réinitialisation de mot de passe par email</li>
          <li>Historique des connexions et audit trail</li>
        </ul>

        <h3>Sécurité</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Paramètre</th>
                <th scope="col">Valeur</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Longueur minimale du mot de passe</td>
                <td>8 caractères</td>
              </tr>
              <tr>
                <td>Timeout de session</td>
                <td>30 minutes d'inactivité</td>
              </tr>
              <tr>
                <td>Tentatives de connexion</td>
                <td>5 maximum</td>
              </tr>
              <tr>
                <td>Format email accepté</td>
                <td><code>^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$</code></td>
              </tr>
              <tr>
                <td>Format téléphone</td>
                <td><code>^\+225[0-9]{8}$</code></td>
              </tr>
              <tr>
                <td>Montant maximum</td>
                <td>999 999 999 FCFA</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h3>Sauvegarde &amp; export</h3>
        <ul>
          <li>Sauvegarde automatique des brouillons (toutes les 30 secondes)</li>
          <li>Export Excel des salariés, bulletins, cotisations</li>
          <li>Export PDF groupé des bulletins d'une période</li>
        </ul>
      </section>

      <!-- ============ ASSISTANT IA ============ -->
      <section id="assistant">
        <h2>20. Assistant IA RH &amp; Paie</h2>
        <p>L'assistant IA intégré répond aux questions métier et génère des rapports personnalisés.</p>

        <h3>Cas d'usage</h3>
        <ul>
          <li>« Quel est le taux de CNPS salarié ? »</li>
          <li>« Comment calculer l'indemnité de licenciement pour un CDI de 5 ans ? »</li>
          <li>« Génère-moi un rapport de masse salariale des 6 derniers mois. »</li>
          <li>« Quelle est la date limite de déclaration ITS ? »</li>
        </ul>

        <div class="callout info" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Bon à savoir</div>
            L'assistant utilise uniquement les données de votre société ; aucune information ne sort du périmètre de
            votre entreprise.
          </div>
        </div>
      </section>

      <!-- ============ FAQ ============ -->
      <section id="faq">
        <h2>21. FAQ &amp; dépannage</h2>

        <details>
          <summary>Je n'arrive pas à créer un nouveau salarié : « Limite atteinte »</summary>
          <p>Votre plan d'abonnement impose un <code>max_employees</code>. Passez à un plan supérieur depuis
            <em>Paramètres → Abonnement</em>, ou désactivez un salarié inactif depuis la fiche employé.</p>
        </details>

        <details>
          <summary>Pourquoi le calcul de paie me renvoie « Exercice non actif » ?</summary>
          <p>Vous devez d'abord créer l'exercice annuel (ex. 2026) et le marquer comme actif depuis <em>Gestion de paie
              → Listing des exercices</em>. Puis créez une période mensuelle dans cet exercice.</p>
        </details>

        <details>
          <summary>Les montants du bulletin semblent incorrects</summary>
          <p>Vérifiez dans l'ordre : (1) le salaire de base contractuel, (2) les éléments du brut affectés pour la
            période, (3) les parts fiscales du salarié, (4) les retenues volontaires en cours (prêts, acomptes). Un
            recalcul force la prise en compte de toutes les modifications.</p>
        </details>

        <details>
          <summary>Un salarié n'apparaît pas dans le cycle de paie</summary>
          <p>Deux causes possibles : (1) il n'a pas de contrat actif couvrant la période — vérifiez les dates du contrat
            ; (2) il est désactivé (<code>is_active = 0</code>) — réactivez-le depuis la fiche.</p>
        </details>

        <details>
          <summary>Comment corriger un bulletin déjà validé ?</summary>
          <p>Si la période n'est pas clôturée, vous pouvez modifier les éléments et relancer le calcul. Si la période
            est clôturée, créez un élément de <em>rappel</em> sur la période suivante pour régulariser.</p>
        </details>

        <details>
          <summary>Le salarié ne reçoit pas son bulletin par email</summary>
          <p>Vérifiez l'adresse email dans la fiche salarié, puis que l'email d'envoi automatique est activé dans
            <em>Paramètres → Notifications</em>. Consultez les logs d'envoi pour identifier un éventuel rejet.</p>
        </details>

        <details>
          <summary>Comment gérer un salarié qui travaille sur plusieurs sites ?</summary>
          <p>RH Flow ne permet actuellement qu'un seul rattachement <em>branche/service/poste</em> par salarié. Pour une
            mobilité inter-sites, créez un avenant lors du transfert.</p>
        </details>

        <details>
          <summary>Puis-je importer mes salariés en masse depuis Excel ?</summary>
          <p>Oui, depuis <em>Gestion des employés → Importer</em>. Téléchargez le modèle Excel, remplissez-le, puis
            importez-le. Les erreurs de validation sont signalées ligne par ligne.</p>
        </details>
      </section>

      <!-- ============ SUPPORT ============ -->
      <section id="support">
        <h2>22. Support &amp; contact</h2>
        <div class="cards">
          <div class="card">
            <div class="card-icon" aria-hidden="true">📧</div>
            <h4>Email</h4>
            <p><a href="mailto:support@rhflow.ci">support@rhflow.ci</a><br>Réponse sous 24h ouvrées.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">📞</div>
            <h4>Téléphone</h4>
            <p>+225 27 00 00 00 00<br>Lundi–Vendredi, 8h–18h (GMT).</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">💬</div>
            <h4>Chat intégré</h4>
            <p>Cliquez sur le bouton de chat en bas à droite de l'application pour un support en temps réel.</p>
          </div>
          <div class="card">
            <div class="card-icon" aria-hidden="true">📚</div>
            <h4>Base de connaissances</h4>
            <p>Tutoriels vidéo, articles pratiques et cas d'usage sur <a
                href="https://rhflow.dc-knowing.com/aide">rhflow.dc-knowing.com/aide</a>.</p>
          </div>
        </div>

        <div class="callout success" role="note">
          <svg class="callout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          <div class="callout-body">
            <div class="callout-title">Conformité SYSCOHADA</div>
            RH Flow est conforme aux normes comptables OHADA et au Code du Travail ivoirien. Les mises à jour fiscales
            sont intégrées automatiquement.
          </div>
        </div>
      </section>

      <footer>
        <p>© 2026 RH Flow · Guide d'utilisation v3.0 — Dernière mise à jour : avril 2026</p>
        <p>Architecture conforme SYSCOHADA · Fiscalité Côte d'Ivoire · Multi-société</p>
      </footer>

    </main>
  </div>

  <script>
    (function () {
      // Menu mobile
      const toggle = document.getElementById('menuToggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');

      function closeMenu() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
      }
      function openMenu() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
      }
      toggle.addEventListener('click', function () {
        if (sidebar.classList.contains('open')) closeMenu(); else openMenu();
      });
      overlay.addEventListener('click', closeMenu);

      // Fermer le menu au clic sur un lien (mobile)
      document.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
          if (window.innerWidth <= 960) closeMenu();
        });
      });

      // Active link on scroll (scrollspy)
      const sections = document.querySelectorAll('main section[id]');
      const navLinks = document.querySelectorAll('.nav-link');

      function updateActive() {
        const scrollPos = window.scrollY + 120;
        let current = '';
        sections.forEach(function (sec) {
          if (sec.offsetTop <= scrollPos) current = sec.id;
        });
        navLinks.forEach(function (link) {
          link.classList.toggle('active', link.getAttribute('href') === '#' + current);
        });
      }
      window.addEventListener('scroll', updateActive, { passive: true });
      updateActive();

      // Search filter
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function () {
          const q = this.value.trim().toLowerCase();
          navLinks.forEach(function (link) {
            const txt = link.textContent.toLowerCase();
            link.style.display = !q || txt.indexOf(q) !== -1 ? '' : 'none';
          });
        });

        // Raccourci Ctrl+K / Cmd+K
        document.addEventListener('keydown', function (e) {
          if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
          }
        });
      }
    })();
  </script>

</body>

</html>