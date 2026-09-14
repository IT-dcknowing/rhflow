@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <title>@yield('title', 'RH Flow - Gestion RH')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <!-- font css -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Driver.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css" />

    <!-- Themify Icons Fonts -->
    <link rel="stylesheet" href="{{ asset('themify-icons/themify-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rh-custom-colors.css') }}">

    @vite(['resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/fullcalendar/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .menu-icon {
            font-size: 14px;
        }

        .menu-link {
            font-size: 15px;
        }

        .stretched-link {
            text-decoration: none;
            color: inherit;
        }

        /* ============================================
           UNIFIED SIDEBAR NAVIGATION - Thème Blanc
        ============================================ */

        body {
            background-color: #ffffff !important;
        }

        /* Wrapper principal de l'application */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            background-color: #ffffff;
        }

        /* Sidebar unifiée */
        .unified-sidebar {
            width: 260px;
            min-width: 260px;
            background: #ffffff;
            border-right: 1px solid #eef2f7;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow: hidden;
            /* pas de scroll global */
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 4px 0 15px rgba(37, 62, 135, 0.04);
        }

        .unified-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .unified-sidebar::-webkit-scrollbar-track {
            background: #f5f6fa;
        }

        .unified-sidebar::-webkit-scrollbar-thumb {
            background: #d0d5e8;
            border-radius: 2px;
        }

        /* Logo dans la sidebar */
        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid #eef0f6;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Navigation principale */
        .sidebar-nav {
            flex: 1;
            padding: 12px 0;
            position: relative;
            overflow-y: auto;
            /* scroll uniquement sur les menus */
            overflow-x: hidden;
        }

        .sidebar-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a0a8c0;
            padding: 24px 20px 10px;
        }

        /* Vue principale des menus */
        .sidebar-main-view {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            width: 100%;
        }

        /* Vue sous-menu (cachée par défaut) */
        .sidebar-submenu-view {
            display: none;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
            transform: translateX(30px) scale(0.98);
        }

        /* États d'animation - Slide Drill-Down */
        .unified-sidebar.submenu-open .sidebar-main-view {
            transform: translateX(-30px) scale(0.98);
            opacity: 0;
            pointer-events: none;
        }

        .unified-sidebar.submenu-open .sidebar-submenu-view.active {
            display: block;
            opacity: 1;
            transform: translateX(0) scale(1);
            pointer-events: auto;
        }

        /* En-tête du sous-menu avec bouton retour */
        .sidebar-submenu-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-bottom: 1px solid #eef0f6;
            margin-bottom: 8px;
        }

        .sidebar-back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: #f0f3ff;
            border: none;
            border-radius: 6px;
            color: #253e87;
            cursor: pointer;
            transition: all 0.18s ease;
            flex-shrink: 0;
        }

        .sidebar-back-btn:hover {
            background: #253e87;
            color: #fff;
        }

        .sidebar-submenu-title {
            font-size: 13px;
            font-weight: 700;
            color: #253e87;
            flex: 1;
        }

        /* Élément de menu principal */
        .sidebar-main-item {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border-left: 3px solid transparent;
            gap: 12px;
            font-size: 16.5px;
            font-weight: 500;
            text-decoration: none;
            user-select: none;
            margin: 8px 12px;
            border-radius: 10px;
        }

        .sidebar-main-item:hover {
            background: #f8faff;
            color: #253e87;
            transform: translateX(4px);
            text-decoration: none;
        }

        .sidebar-main-item.active {
            color: #253e87;
            border-left-color: #253e87;
            /* Pas de fond bleu pour les menus principaux (souvent des parents) */
            background: transparent !important;
            box-shadow: none !important;
        }

        .sidebar-main-item.active .item-icon {
            background: #253e87;
            color: #fff;
        }

        .sidebar-main-item .item-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8ecff;
            border-radius: 8px;
            font-size: 15px;
            flex-shrink: 0;
            transition: all 0.2s;
            color: #253e87;
        }

        .sidebar-main-item:hover .item-icon,
        .sidebar-main-item.active .item-icon {
            background: #253e87;
            color: #fff;
        }

        .sidebar-main-item .item-label {
            flex: 1;
            font-size: 15.5px;
            line-height: 1.3;
        }

        .sidebar-main-item .item-arrow {
            font-size: 10px;
            color: #b0bbd5;
            transition: transform 0.25s ease;
        }

        .sidebar-main-item:hover .item-arrow,
        .sidebar-main-item.active .item-arrow {
            color: #253e87;
        }

        /* Éléments de sous-menu */
        .submenu-group-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #a0a8c0;
            padding: 10px 20px 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .submenu-group-title i {
            font-size: 8px;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 18px;
            color: #4a5568;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 8px;
            margin: 2px 12px;
            border-left: 2px solid transparent;
        }

        .submenu-item:hover {
            background: #f8faff;
            color: #253e87;
            transform: translateX(4px);
            text-decoration: none;
        }

        .submenu-item.active {
            background: #eef1ff;
            color: #253e87;
            border-left-color: #253e87;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(37, 62, 135, 0.04);
            text-decoration: none;
        }

        .submenu-item i {
            font-size: 14.5px;
            width: 20px;
            text-align: center;
            color: #9aadcc;
            flex-shrink: 0;
            transition: color 0.18s;
        }

        .submenu-item:hover i,
        .submenu-item.active i {
            color: #253e87;
        }

        /* Contenu principal (à droite de la sidebar) */
        .app-main-content {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            transition: margin-left 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Bouton toggle mobile */
        .sidebar-mobile-toggle {
            display: none;
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 1100;
            background: #253e87;
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 991px) {
            .unified-sidebar {
                transform: translateX(-100%);
            }

            .unified-sidebar.mobile-open {
                transform: translateX(0);
            }

            .app-main-content {
                margin-left: 0;
            }

            .sidebar-mobile-toggle {
                display: flex;
            }
        }

        .main-content {
            transition: all 0.3s ease;
        }

        /* Amélioration du dropdown de raccourcis */
        .dropdown-shortcuts-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdown-shortcuts-item {
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-shortcuts-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .dropdown-shortcuts-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-primary);
            color: white;
        }

        .dropdown-shortcuts-item.col:hover .dropdown-shortcuts-icon {
            background-color: var(--bs-primary-dark, #0056b3);
        }

        /* Timeline styles */
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #253e87, #03c3ec);
            border-radius: 1px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -17px;
            top: 15px;
            width: 8px;
            height: 8px;
            background: #253e87;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px rgba(105, 110, 255, 0.3);
        }

        /* Card hover effects */
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(105, 110, 255, 0.05) 0%, rgba(3, 195, 236, 0.05) 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0 !important;
            margin-bottom: 1rem;
        }

        .card-header h5 {
            color: #566a7f;
            font-weight: 600;
        }

        /* Avatar improvements */
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Progress bars */
        .progress {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Alert improvements */
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
        }

        .alert-danger {
            border-left-color: #ff4d4f;
            background: linear-gradient(135deg, rgba(255, 77, 77, 0.05) 0%, rgba(255, 77, 77, 0.1) 100%);
        }

        .alert-warning {
            border-left-color: #ffcd07;
            background: linear-gradient(135deg, rgba(255, 205, 7, 0.05) 0%, rgba(255, 205, 7, 0.1) 100%);
        }

        .alert-info {
            border-left-color: #03c3ec;
            background: linear-gradient(135deg, rgba(3, 195, 236, 0.05) 0%, rgba(3, 195, 236, 0.1) 100%);
        }

        .alert-success {
            border-left-color: #28c848;
            background: linear-gradient(135deg, rgba(40, 200, 72, 0.05) 0%, rgba(40, 200, 72, 0.1) 100%);
        }

        .alert-secondary {
            border-left-color: #8592a3;
            background: linear-gradient(135deg, rgba(133, 146, 163, 0.05) 0%, rgba(133, 146, 163, 0.1) 100%);
        }

        /* Button improvements */
        .btn-primary {
            background-color: #253e87;
            border-color: #253e87;
            color: white;
        }

        /* Style pour les éléments de menu actifs */
        .menu-item.active>.menu-link {
            background-color: #253e87 !important;
            color: white !important;
            border-radius: 0.375rem;
        }

        .menu-item.active>.menu-link i,
        .menu-item.active>.menu-link div {
            color: white !important;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary {
            border-color: rgb(3, 61, 236);
            color: rgb(3, 61, 236);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, rgb(14, 87, 246) 0%, #03c3ec 100%);
            border-color: transparent;
        }

        .btn-outline-success:hover {
            background: linear-gradient(135deg, rgb(14, 87, 246) 0%, #03c3ec 100%);
            border-color: transparent;
        }

        /* Chart containers */
        .chart-container {
            position: relative;
            height: 100%;
            width: 100%;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px 10px;
            border: 1px solid #d9dee3;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            h3 {
                font-size: 1.5rem;
            }

            h5 {
                font-size: 1.125rem;
            }
        }

        /* Loading animation */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Custom scrollbar for timeline */
        .timeline::-webkit-scrollbar {
            width: 6px;
        }

        .timeline::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        .timeline::-webkit-scrollbar-thumb {
            background: rgba(105, 183, 255, 0.5);
            border-radius: 3px;
        }

        .timeline::-webkit-scrollbar-thumb:hover {
            background: rgba(105, 158, 255, 0.7);
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
        }

        .bg-label-primary {
            background-color: rgba(105, 110, 255, 0.1) !important;
            color: #253e87 !important;
        }

        .bg-label-success {
            background-color: rgba(40, 200, 72, 0.1) !important;
            color: #28c848 !important;
        }

        .bg-label-warning {
            background-color: rgba(255, 205, 7, 0.1) !important;
            color: #ffcd07 !important;
        }

        .bg-label-info {
            background-color: rgba(3, 195, 236, 0.1) !important;
            color: #03c3ec !important;
        }

        .bg-label-danger {
            background-color: rgba(255, 77, 77, 0.1) !important;
            color: #ff4d4f !important;
        }

        .bg-label-secondary {
            background-color: rgba(133, 146, 163, 0.1) !important;
            color: #8592a3 !important;
        }

        .bg-label-dark {
            background-color: rgba(86, 106, 127, 0.1) !important;
            color: #566a7f !important;
        }

        /* Assurez-vous que SweetAlert2 apparaît toujours au-dessus des autres éléments */
        .swal2-container {
            z-index: 99999 !important;
        }

        /* Si vous utilisez Bootstrap */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        /* Pour les modales Bootstrap */
        .modal {
            z-index: 1050 !important;
        }
    </style>

    <!-- Nouveau design : polices + cadre commun (chargé après les anciens styles pour les remplacer) -->
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/rhflow-design.css') }}?v={{ filemtime(public_path('css/rhflow-design.css')) }}">
    @stack('styles')
</head>

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- Bouton toggle mobile -->
    <button class="sidebar-mobile-toggle" id="mobileSidebarToggle"
        onclick="document.querySelector('.unified-sidebar').classList.toggle('mobile-open')">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Wrapper principal -->
    <div class="app-wrapper">

        <!-- ============================================
             SIDEBAR UNIFIÉE
        ============================================ -->
        @php
            $currentRoute = Request::route() ? Request::route()->getName() : '';

            // Détection du menu actif pour le drill-down
            $activeSubmenuId = null;

            if (
                Str::contains($currentRoute, 'company.employees') || Str::contains($currentRoute, 'company.demandes') ||
                Str::contains($currentRoute, 'company.contracts') || Str::contains($currentRoute, 'company.ruptures') ||
                Str::contains($currentRoute, 'company.times') || Str::contains($currentRoute, 'company.leaves')
            ) {
                $activeSubmenuId = 'submenu-view-employees';
            } elseif (
                Str::contains($currentRoute, 'company.paiesalaries') || Str::contains($currentRoute, 'company.avantages') ||
                Str::contains($currentRoute, 'company.loans') || Str::contains($currentRoute, 'company.settings.loan-types')
            ) {
                $activeSubmenuId = 'submenu-view-salary';
            } elseif (Str::contains($currentRoute, 'company.declarations')) {
                $activeSubmenuId = 'submenu-view-declarations';
            } elseif (Str::contains($currentRoute, 'company.evenements')) {
                $activeSubmenuId = 'submenu-view-events';
            } elseif (Str::contains($currentRoute, 'company.settings') || Str::contains($currentRoute, 'company.settings.users')) {
                $activeSubmenuId = 'submenu-view-config';
            }

            $isSubmenuOpen = !is_null($activeSubmenuId);

            // ----- Sections visibles selon le type d'utilisateur -----
            // La correspondance type -> sections est définie dans
            // config/menu_sections.php : c'est le seul fichier à modifier
            // pour ouvrir ou fermer une section à un rôle.
            $typeUtilisateur = auth()->user()->type;
            $sectionsAutorisees = config('menu_sections.' . $typeUtilisateur, []);

            $estEntreprise = $typeUtilisateur === 'company';
            $estRh = $typeUtilisateur === 'hr';
            $estPaie = in_array($typeUtilisateur, ['paie', 'payroll']);

            $voitConfiguration = in_array('configuration', $sectionsAutorisees);
            $voitEmployes = in_array('employes', $sectionsAutorisees);
            $voitPaie = in_array('paie', $sectionsAutorisees);
            $voitDeclarations = in_array('declarations', $sectionsAutorisees);
            $voitEvenements = in_array('evenements', $sectionsAutorisees);
            $voitSimulateur = in_array('simulateur', $sectionsAutorisees);
        @endphp
        <div class="unified-sidebar {{ $isSubmenuOpen ? 'submenu-open' : '' }}" id="unifiedSidebar">

            <!-- Logo -->
            <div class="sidebar-logo">
                <div style="display:flex;align-items:center;gap:10px;">
                    <img src="{{ asset('img/logos/logo.png') }}" alt="RH Flow" style="height:40px;">
                </div>
            </div>

            <!-- Navigation -->
            <div class="sidebar-nav" id="sidebarNav">

                <!-- ====== VUE PRINCIPALE DES MENUS ====== -->
                <div class="sidebar-main-view" id="sidebar-main-view">
                    <div class="sidebar-section-title">Menu Principal</div>

                    {{-- Dashboard : une destination par type d'utilisateur --}}
                    @if($estEntreprise)
                        <a href="{{ route('company.dashboard') }}"
                            class="sidebar-main-item {{ Request::route()->getName() == 'company.dashboard' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-home"></i></div>
                            <span class="item-label">Tableau de bord Entreprise</span>
                        </a>
                    @elseif($estRh || $estPaie)
                        <a href="{{ route('hr.dashboard') }}"
                            class="sidebar-main-item {{ Request::route()->getName() == 'hr.dashboard' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-home"></i></div>
                            <span class="item-label">Tableau de bord RH/Paie</span>
                        </a>
                    @elseif($typeUtilisateur === 'employee')
                        <a href="{{ route('employee.dashboard') }}"
                            class="sidebar-main-item {{ Request::route()->getName() == 'employee.dashboard' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-home"></i></div>
                            <span class="item-label">Tableau de bord Employé</span>
                        </a>
                    @endif

                    @if($voitConfiguration)
                        <!-- Configuration Entreprise -->
                        <a href="{{ route('company.settings.config') }}"
                            class="sidebar-main-item {{ $activeSubmenuId === 'submenu-view-config' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-cog"></i></div>
                            <span class="item-label">Configuration Entreprise</span>
                            <i class="fas fa-chevron-right item-arrow"></i>
                        </a>
                    @endif

                    @if(isModuleActive('employee') && $voitEmployes)
                        <!-- Gestion des Employés -->
                        <a href="{{ route('company.employees.dashboard') }}"
                            class="sidebar-main-item {{ $activeSubmenuId === 'submenu-view-employees' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-users"></i></div>
                            <span class="item-label">Gestion des Employés</span>
                            <i class="fas fa-chevron-right item-arrow"></i>
                        </a>
                    @endif

                    @if(isModuleActive('salary') && $voitPaie)
                        <!-- Gestion de Paie et Retenues -->
                        <a href="{{ route('company.paiesalaries.dashboard') }}"
                            class="sidebar-main-item {{ $activeSubmenuId === 'submenu-view-salary' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-money-bill"></i></div>
                            <span class="item-label">Gestion de Paie et Retenues</span>
                            <i class="fas fa-chevron-right item-arrow"></i>
                        </a>
                    @endif

                    @if(isModuleActive('declaration') && $voitDeclarations)
                        <!-- Gestion des Etats et Déclarations -->
                        <a href="{{ route('company.declarations.dashboard') }}"
                            class="sidebar-main-item {{ $activeSubmenuId === 'submenu-view-declarations' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-file-alt"></i></div>
                            <span class="item-label">Gestion des Bulletins et Déclarations</span>
                            <i class="fas fa-chevron-right item-arrow"></i>
                        </a>
                    @endif

                    @if(isModuleActive('event') && $voitEvenements)
                        <!-- Événements -->
                        <a href="{{ route('company.evenements.dashboard') }}"
                            class="sidebar-main-item {{ $activeSubmenuId === 'submenu-view-events' ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-calendar-alt"></i></div>
                            <span class="item-label">Événements</span>
                            <i class="fas fa-chevron-right item-arrow"></i>
                        </a>
                    @endif

                    @if($voitSimulateur)
                        <!-- Simulateur -->
                        <a href="{{ route('company.simulator.dashboard') }}"
                            class="sidebar-main-item {{ Str::contains(Request::route()->getName(), 'company.simulator') ? 'active' : '' }}">
                            <div class="item-icon"><i class="fas fa-calculator"></i></div>
                            <span class="item-label">Simulateur</span>
                        </a>
                    @endif

                    {{-- Assistant IA déplacé en bas fixe de la sidebar --}}
                </div><!-- /sidebar-main-view -->

                <!-- ====== PANELS DE SOUS-MENUS ====== -->

                @if(isModuleActive('employee') && $voitEmployes)
                    <!-- Panel Employés -->
                    <div class="sidebar-submenu-view {{ $activeSubmenuId === 'submenu-view-employees' ? 'active' : '' }}"
                        id="submenu-view-employees">
                        <div class="sidebar-submenu-header">
                            <button class="sidebar-back-btn" onclick="closeSubmenu()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <span class="sidebar-submenu-title">Gestion des Employés</span>
                        </div>
                        <a href="{{ route('company.employees.dashboard') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.employees.dashboard' ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Accueil Employés
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Gérer le personnel</div>
                        <a href="{{ route('company.employees.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.employees.index' || Request::route()->getName() == 'company.employees.create' || Request::route()->getName() == 'company.employees.edit' || Request::route()->getName() == 'company.employees.show' ? 'active' : '' }}">
                            <i class="fas fa-user"></i> Listing des employés
                        </a>
                        <a href="{{ route('company.employees.dossiers.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.employees.dossiers.index' || Request::route()->getName() == 'company.employees.dossiers.profile' ? 'active' : '' }}">
                            <i class="fas fa-folder"></i> Dossier du personnel
                        </a>
                        <a href="{{ route('company.employees.demandes.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.employees.demandes.index' || Request::route()->getName() == 'company.employees.demandes.show' ? 'active' : '' }}">
                            <i class="fas fa-user-times"></i> Demandes Employés
                        </a>
                        <a href="{{ route('company.employees.grille.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.employees.grille') ? 'active' : '' }}">
                            <i class="fas fa-dollar"></i> Grille Salariale
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Contrats &amp; Ruptures</div>
                        <a href="{{ route('company.contracts.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.contracts') ? 'active' : '' }}">
                            <i class="fas fa-file-text"></i> Gestion des Contrats
                        </a>
                        <!-- <a href="{{ route('company.ruptures.index') }}"
                                                    class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.ruptures') ? 'active' : '' }}">
                                                    <i class="fas fa-exclamation-triangle"></i> Sanction &amp; Ruptures
                                                </a> -->
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Gérer le temps</div>
                        @if(Auth::user()->attendance_type == 'manuel')
                            <a href="{{ route('company.times.absences.index') }}"
                                class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.times.absences.') ? 'active' : '' }}">
                                <i class="fas fa-calendar"></i> Absences &amp; Présences
                            </a>
                        @else
                            <a href="{{ route('company.times.qrcode-pointage.index') }}"
                                class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.times.qrcode-pointage.') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check"></i> Gestion des présences
                            </a>
                        @endif
                        <!-- <a href="{{ route('company.times.overtime.index') }}"
                                                    class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.times.overtime.') ? 'active' : '' }}">
                                                    <i class="fas fa-clock"></i> Heures Supplémentaires
                                                </a> -->
                        <!-- <a href="{{ route('company.leaves.index') }}"
                                                    class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.leaves') ? 'active' : '' }}">
                                                    <i class="fas fa-plane"></i> Congés
                                                </a> -->
                    </div><!-- /submenu-view-employees -->
                @endif

                @if(isModuleActive('salary') && $voitPaie)
                    <!-- Panel Paie -->
                    <div class="sidebar-submenu-view {{ $activeSubmenuId === 'submenu-view-salary' ? 'active' : '' }}"
                        id="submenu-view-salary">
                        <div class="sidebar-submenu-header">
                            <button class="sidebar-back-btn" onclick="closeSubmenu()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <span class="sidebar-submenu-title">Gestion de Paie</span>
                        </div>
                        <a href="{{ route('company.paiesalaries.dashboard') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.paiesalaries.dashboard' ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Tableau de bord
                        </a>
                        <a href="{{ route('company.paiesalaries.exercices.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.paiesalaries.exercices') || (Str::contains(Request::route()->getName(), 'company.paiesalaries.periodes') && Request::route()->getName() != 'company.paiesalaries.periodes.show') ? 'active' : '' }}">
                            <i class="fas fa-history"></i> Exercices et périodes
                        </a>
                        <a href="{{ route('company.paiesalaries.paie-du-mois') }}"
                            class="submenu-item {{ in_array(Request::route()->getName(), ['company.paiesalaries.paie-du-mois', 'company.paiesalaries.periodes.show']) ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i> Paie du mois
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Paramètres de paie</div>
                        <a href="{{ route('company.paiesalaries.allowance.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.paiesalaries.allowance') ? 'active' : '' }}">
                            <i class="fas fa-outdent"></i> Eléments du brut
                        </a>
                        <a href="{{ route('company.avantages.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.avantages') ? 'active' : '' }}">
                            <i class="fas fa-plus"></i> Avantage en nature
                        </a>
                        <a href="{{ route('company.paiesalaries.retenues.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.paiesalaries.retenues') ? 'active' : '' }}">
                            <i class="fas fa-arrow-circle-left"></i> Retenues salaire
                        </a>
                        <a href="{{ route('company.loans.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.loans') && !Str::contains(Request::route()->getName(), 'loan-types') ? 'active' : '' }}">
                            <i class="fas fa-chevron-circle-left"></i> Prêts
                        </a>
                        <a href="{{ route('company.settings.loan-types.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.settings.loan-types') ? 'active' : '' }}">
                            <i class="fas fa-list-ul"></i> Types de Prêts
                        </a>
                        <a href="{{ route('company.paiesalaries.remboursements') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.paiesalaries.remboursements') ? 'active' : '' }}">
                            <i class="fas fa-minus-square"></i> Remboursements de frais
                        </a>
                    </div><!-- /submenu-view-salary -->
                @endif

                @if(isModuleActive('declaration') && $voitDeclarations)
                    <!-- Panel Déclarations -->
                    <div class="sidebar-submenu-view {{ $activeSubmenuId === 'submenu-view-declarations' ? 'active' : '' }}"
                        id="submenu-view-declarations">
                        <div class="sidebar-submenu-header">
                            <button class="sidebar-back-btn" onclick="closeSubmenu()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <span class="sidebar-submenu-title">Etats et Déclarations</span>
                        </div>
                        <a href="{{ route('company.declarations.dashboard') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.declarations.dashboard' ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Accueil Déclaration
                        </a>
                        <a href="{{ route('company.declarations.resume.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.resume') ? 'active' : '' }}">
                            <i class="fas fa-spinner"></i> Gestion des bulletins de paie
                        </a>
                        <a href="{{ route('company.declarations.livrepaie.mensuel') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.livrepaie.mensuel') ? 'active' : '' }}">
                            <i class="fas fa-book"></i> Livre de paie mensuel
                        </a>
                        <a href="{{ route('company.declarations.livrepaie.annuel') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.livrepaie.annuel') ? 'active' : '' }}">
                            <i class="fas fa-book"></i> Livre de paie annuel
                        </a>
                        <a href="{{ route('company.declarations.livrepaie.individuel') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.livrepaie.individuel') ? 'active' : '' }}">
                            <i class="fas fa-book"></i> Livre de paie individuel
                        </a>
                        <a href="{{ route('company.declarations.cotisation.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.cotisation.') ? 'active' : '' }}">
                            <i class="fas fa-file"></i> Etat des cotisations
                        </a>
                        <a href="{{ route('company.declarations.declaration.mensuelle') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.declarations.declaration.mensuelle') ? 'active' : '' }}">
                            <i class="fas fa-file-code"></i> Déclarations Mensuelles
                        </a>
                        {{-- Déclarations Annuelles : non disponible --}}
                        <span class="submenu-item disabled">
                            <i class="fas fa-file-code"></i> Déclarations Annuelles
                            <span class="soon">Bientôt</span>
                        </span>
                    </div><!-- /submenu-view-declarations -->
                @endif

                @if(isModuleActive('event') && $voitEvenements)
                    <!-- Panel Événements -->
                    <div class="sidebar-submenu-view {{ $activeSubmenuId === 'submenu-view-events' ? 'active' : '' }}"
                        id="submenu-view-events">
                        <div class="sidebar-submenu-header">
                            <button class="sidebar-back-btn" onclick="closeSubmenu()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <span class="sidebar-submenu-title">Événements</span>
                        </div>
                        <a href="{{ route('company.evenements.dashboard') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.dashboard' ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Gérer vos events</div>
                        <a href="{{ route('company.evenements.annonces.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.annonces.index' || Request::route()->getName() == 'company.evenements.annonces.create' || Request::route()->getName() == 'company.evenements.annonces.edit' || Request::route()->getName() == 'company.evenements.annonces.show' ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i> Annonces
                        </a>
                        <a href="{{ route('company.evenements.transfers.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.transfers.index' || Request::route()->getName() == 'company.evenements.transfers.create' || Request::route()->getName() == 'company.evenements.transfers.edit' || Request::route()->getName() == 'company.evenements.transfers.show' ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i> Affectations
                        </a>
                        <a href="{{ route('company.evenements.events.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.events.index' || Request::route()->getName() == 'company.evenements.events.create' || Request::route()->getName() == 'company.evenements.events.edit' || Request::route()->getName() == 'company.evenements.events.show' ? 'active' : '' }}">
                            <i class="fas fa-calendar"></i> Événements
                        </a>
                        <a href="{{ route('company.evenements.meetings.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.meetings.index' || Request::route()->getName() == 'company.evenements.meetings.create' || Request::route()->getName() == 'company.evenements.meetings.edit' || Request::route()->getName() == 'company.evenements.meetings.show' ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Réunions
                        </a>
                        <a href="{{ route('company.evenements.promotions.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.promotions.index' || Request::route()->getName() == 'company.evenements.promotions.create' || Request::route()->getName() == 'company.evenements.promotions.edit' || Request::route()->getName() == 'company.evenements.promotions.show' ? 'active' : '' }}">
                            <i class="fas fa-arrow-up"></i> Promotions
                        </a>
                        <a href="{{ route('company.evenements.awards.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.evenements.awards.index' || Request::route()->getName() == 'company.evenements.awards.create' || Request::route()->getName() == 'company.evenements.awards.edit' || Request::route()->getName() == 'company.evenements.awards.show' ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i> Recompenses
                        </a>
                    </div><!-- /submenu-view-events -->
                @endif

                @if(auth()->user()->type === 'company')
                    <!-- Panel Configuration -->
                    <div class="sidebar-submenu-view {{ $activeSubmenuId === 'submenu-view-config' ? 'active' : '' }}"
                        id="submenu-view-config">
                        <div class="sidebar-submenu-header">
                            <button class="sidebar-back-btn" onclick="closeSubmenu()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <span class="sidebar-submenu-title">Configuration</span>
                        </div>
                        <a href="{{ route('company.settings.config') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.config' ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Configuration</div>
                        <a href="{{ route('company.settings.settings') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.settings' ? 'active' : '' }}">
                            <i class="fas fa-building"></i> Paramètres Entreprise
                        </a>
                        <a href="{{ route('company.settings.branches.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.branches.index' ? 'active' : '' }}">
                            <i class="fas fa-bank"></i> succursales
                        </a>
                        <a href="{{ route('company.settings.departments.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.departments.index' ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Services
                        </a>
                        <a href="{{ route('company.settings.designations.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.designations.index' ? 'active' : '' }}">
                            <i class="fas fa-user"></i> Postes
                        </a>
                        <a href="{{ route('company.settings.leave-types.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.leave-types.index' ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> Types de Congés
                        </a>
                        <a href="{{ route('company.settings.work-locations.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.work-locations.index' ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i> Emplacements
                        </a>
                        <a href="{{ route('company.settings.attendance-system.index') }}"
                            class="submenu-item {{ Str::contains(Request::route()->getName(), 'company.settings.attendance-system') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Système de présence
                        </a>
                        <a href="{{ route('company.settings.documents') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.documents' ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Documents Société
                        </a>
                        <div class="submenu-group-title"><i class="fas fa-minus"></i> Utilisateurs</div>
                        <a href="{{ route('company.settings.users.index') }}"
                            class="submenu-item {{ Request::route()->getName() == 'company.settings.users.index' || Request::route()->getName() == 'company.settings.users.create' || Request::route()->getName() == 'company.settings.users.edit' || Request::route()->getName() == 'company.settings.users.details' ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Listing des utilisateurs
                        </a>
                    </div><!-- /submenu-view-config -->
                @endif

            </div><!-- /sidebar-nav -->

        </div><!-- /unified-sidebar -->


        <!-- ============================================
             CONTENU PRINCIPAL
        ============================================ -->
        <div class="app-main-content">

            <!-- NAVBAR SUPÉRIEURE -->
            <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
                <div class="container-xxl">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="fas fa-menu-2 ti-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Quick links  -->
                            <li class="nav-item dropdown">
                                <a href="{{ route('company.plan.pricing') }}" class="btn nb-sub me-3"
                                    id="quickLinksDropdown">
                                    <span>Abonnement</span> <i class="ti-layout-grid2"></i>
                                </a>
                            </li>
                            <!-- Quick links -->

                            <!-- Télécharger l'App (offres payantes) -->
                            @php
                                $hasPaidPlan = false;
                                if (auth()->check()) {
                                    $user = auth()->user();
                                    if ($user->userPlan && $user->userPlan->price > 0) {
                                        $hasPaidPlan = true;
                                    } elseif ($user->creator && $user->creator->userPlan && $user->creator->userPlan->price > 0) {
                                        $hasPaidPlan = true;
                                    }
                                }
                            @endphp
                            @if($hasPaidPlan)
                                <li class="nav-item">
                                    <a href="{{ asset('downloads/rhflow.apk') }}" download="RH_Flow_Mobile.apk"
                                        class="nb-card nb-app me-3" title="Télécharger l'App — APK Android disponible">
                                        <span class="bx"><i class="fas fa-download"></i></span>
                                        <span class="nb-card-text">
                                            <span class="nb-card-title">Télécharger l'App</span>
                                            <span class="nb-card-sub">APK Android disponible</span>
                                        </span>
                                    </a>
                                </li>
                            @endif

                            <!-- Assistant IA -->
                            <li class="nav-item">
                                <button type="button" id="navbar-ai-btn" onclick="toggleChatbot()"
                                    class="nb-card nb-ia me-3" title="Assistant IA — Expert RH & Paie">
                                    <span class="bx"><i class="fas fa-robot"></i></span>
                                    <span class="nb-card-text">
                                        <span class="nb-card-title">Assistant IA</span>
                                        <span class="nb-card-sub">Expert RH &amp; Paie</span>
                                    </span>
                                </button>
                            </li>

                            <!-- Sélecteur de période -->
                            @if(auth()->user()->type === 'company' && isModuleActive('salary'))
                                @include('includes.period-selector')
                            @endif
                            <!-- Sélecteur de période -->

                            <!-- Notifications -->
                            <li class="nav-item dropdown">
                                <button class="btn p-0 dropdown-toggle hide-arrow me-3" data-bs-toggle="dropdown"
                                    id="notificationDropdown">
                                    <i class="fas fa-bell text-primary" style="font-size: 20px;"></i>
                                    <span class="badge bg-danger rounded-pill badge-notifications"
                                        id="notificationBadge" style="display: none;">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" id="notificationDropdownMenu">
                                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span class="me-4">Notifications</span>
                                        <a href="{{ route('notifications.index') }}"
                                            class="btn btn-sm bg-primary text-white">Voir tout</a>
                                    </div>
                                    <div class="dropdown-notifications-list" id="notificationList">
                                        <div class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <small class="text-muted d-block mt-1">Chargement...</small>
                                        </div>
                                    </div>
                                    <div class="dropdown-footer">
                                        <a href="{{ route('notifications.unread') }}" class="dropdown-item">
                                            <i class="fas fa-eye me-2"></i>Voir les non lues
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Aide / Guide Premium -->
                            <li class="nav-item me-3 d-flex align-items-center">
                                <button class="btn guide-btn" onclick="openGuideModal()"
                                    title="Guide d'utilisation complet">
                                    <i class="fas fa-book-open"></i>
                                    <span>Guide d'Utilisation</span>
                                </button>
                            </li>

                            <!-- Notifications -->

                            <!-- Profil utilisateur -->
                            <li class="nav-item dropdown">
                                <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @if(auth()->user()->avatar_url && file_exists(public_path(auth()->user()->avatar_url)))
                                            <img src="{{ asset(auth()->user()->avatar_url) }}" alt="Avatar"
                                                class="w-px-40 h-auto rounded-circle" />
                                        @else
                                            <div
                                                class="avatar-initial bg-primary rounded-circle w-px-40 h-px-40 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="dropdown-header">
                                        <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                                        <small class="text-muted">{{ auth()->user()->type_label }}</small>
                                    </div>
                                    {{-- Pas de "Mon Profil" / "Paramètres" ici : ces pages sont
                                    reservees au super-admin et renvoyaient un 403. --}}
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            <span>Déconnexion</span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            <!-- Profil utilisateur -->
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- / Navbar -->

            <!-- Contenu de la page -->
            <div class="container-xxl flex-grow-1 container-p-y">

                {{-- Les alertes de session sont maintenant gérées par SweetAlert2 en bas du fichier --}}

                @yield('content')
            </div><!-- /container-xxl -->

            @include('layouts.footer')

        </div><!-- /app-main-content -->
    </div><!-- /app-wrapper -->

    @php
        /* Chatbot widget — chargement robuste : fonctionne même si le module
           n'est pas encore formellement activé dans modules_statuses.json */
        try {
            echo view('chatbot::widget')->render();
            $chatbotLoaded = true;
        } catch (\Exception $e) {
            $chatbotLoaded = false;
        }
    @endphp

    @if(!$chatbotLoaded)
        {{-- Fallback : fonction vide pour éviter JS error si widget absent --}}
        <script>
            function toggleChatbot() {
                alert('Le module Assistant IA n\'est pas encore activé sur ce serveur. Contactez votre administrateur.');
            }
        </script>
    @endif

    <!-- Scripts -->

    <script>
        // Fonction toggle sous-menu sidebar - Mode Drill-Down Premium
        function openSubmenu(panelId, title) {
            const sidebar = document.getElementById('unifiedSidebar');

            // Masquer les classes actives des autres sous-menus d'abord
            document.querySelectorAll('.sidebar-submenu-view').forEach(function (el) {
                if (el.id !== panelId) {
                    el.classList.remove('active');
                    el.style.display = 'none';
                }
            });

            // Afficher le panneau demandé
            var panel = document.getElementById(panelId);
            if (panel) {
                panel.style.display = 'block';
                // Laisser un micro-délai pour que le display:block soit pris en compte avant l'anim CSS
                setTimeout(() => {
                    panel.classList.add('active');
                    sidebar.classList.add('submenu-open');
                }, 10);
            }
        }

        function closeSubmenu() {
            const sidebar = document.getElementById('unifiedSidebar');
            sidebar.classList.remove('submenu-open');

            // On attend la fin de l'animation CSS (0.35s) pour passer en display:none
            setTimeout(() => {
                if (!sidebar.classList.contains('submenu-open')) {
                    document.querySelectorAll('.sidebar-submenu-view').forEach(function (el) {
                        el.classList.remove('active');
                        el.style.display = 'none';
                    });
                }
            }, 350);
        }

        // Au chargement: l'état est géré par les classes injectées par Blade
        document.addEventListener('DOMContentLoaded', function () {
            // Si un sous-menu est marqué actif par le serveur, on s'assure qu'il est affiché
            var activePanel = document.querySelector('.sidebar-submenu-view.active');
            if (activePanel) {
                activePanel.style.display = 'block';
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.js') }}"></script>

    {{-- Rappel d'échéance d'abonnement --}}
    @php $rappelAbonnement = app(\App\Services\SubscriptionService::class)->resume(); @endphp
    @if($rappelAbonnement['alerter'])
        <script>
            (function () {
                // Le rappel ne se réaffiche pas avant le délai configuré, pour ne pas
                // réapparaître à chaque changement de page.
                var cle = 'rhflow_rappel_abo_{{ auth()->id() }}_{{ optional($rappelAbonnement['echeance'])->format('Ymd') }}';
                var delai = {{ (int) config('subscription.frequence_rappel_heures', 24) }} * 3600 * 1000;
                var dernier = 0;

                try {
                    dernier = parseInt(window.localStorage.getItem(cle) || '0', 10);
                } catch (e) {
                    dernier = 0;
                }

                if (Date.now() - dernier < delai) {
                    return;
                }

                var jours = {{ (int) $rappelAbonnement['jours_restants'] }};
                var expire = {{ $rappelAbonnement['expire'] ? 'true' : 'false' }};
                var proprietaire = {{ $rappelAbonnement['est_proprietaire'] ? 'true' : 'false' }};
                var echeance = @json(optional($rappelAbonnement['echeance'])->format('d/m/Y'));
                var offre = @json($rappelAbonnement['plan']);

                var titre, texte, icone;

                if (expire) {
                    titre = 'Abonnement échu';
                    texte = 'Votre abonnement a expiré le <strong>' + echeance + '</strong>'
                        + (jours < 0 ? ' (il y a ' + Math.abs(jours) + ' jour' + (Math.abs(jours) > 1 ? 's' : '') + ')' : '')
                        + '.<br>L\'accès sera suspendu tant qu\'il n\'est pas renouvelé.';
                    icone = 'error';
                } else {
                    titre = jours === 0 ? 'Votre abonnement expire aujourd\'hui' : 'Votre abonnement arrive à échéance';
                    texte = (jours === 0
                        ? 'Votre abonnement expire <strong>aujourd\'hui</strong>'
                        : 'Il reste <strong>' + jours + ' jour' + (jours > 1 ? 's' : '') + '</strong> avant l\'échéance du <strong>' + echeance + '</strong>')
                        + '.<br>Pensez à le renouveler pour éviter toute interruption.';
                    icone = 'warning';
                }

                if (offre) {
                    texte += '<br><small class="text-muted">Offre en cours : ' + offre + '</small>';
                }

                if (!proprietaire) {
                    texte += '<br><br><small>Rapprochez-vous de l\'administrateur de votre entreprise pour le renouvellement.</small>';
                }

                Swal.fire({
                    title: titre,
                    html: texte,
                    icon: icone,
                    // Deux boutons et deux seulement, toujours en français.
                    showConfirmButton: true,
                    showCancelButton: true,
                    showDenyButton: false,
                    confirmButtonText: proprietaire ? 'Renouveler maintenant' : 'J\'ai compris',
                    cancelButtonText: 'Plus tard',
                    denyButtonText: 'Non',
                    confirmButtonColor: '#253e87',
                    cancelButtonColor: '#8592a3',
                    // Abonnement échu : le rappel ne se ferme qu'avec l'un des deux boutons.
                    allowOutsideClick: !expire,
                    allowEscapeKey: !expire,
                    didOpen: function (popup) {
                        // Filet de sécurité : aucun bouton « Non » résiduel ne doit rester affiché.
                        var deny = popup.querySelector('.swal2-deny');
                        if (deny) {
                            deny.remove();
                        }
                    }
                }).then(function (resultat) {
                    try {
                        window.localStorage.setItem(cle, Date.now().toString());
                    } catch (e) { }

                    if (resultat.isConfirmed && proprietaire) {
                        window.location.href = '{{ route('company.packs.index') }}';
                    }
                });
            })();
        </script>
    @endif

    <script src="{{asset('libs/select2/select2.js')}}"></script>
    <script src="{{asset('libs/flatpickr/flatpickr.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Moteur SmartGuard — Assistant Intelligent & Intercepteur d'Erreurs -->
    <script>
            const SmartGuard = {
                rules: [
            {
                keywords: ['exercice de paie n\'est pas actif', 'exercice non actif', 'aucun exercice', 'exercice de paie requis', 'exercice requis'],
            title: '⚠️ Configuration d\'Exercice Requise',
            html: `<div style="text-align: left; font-size: 14px; line-height: 1.6; color: #4f5d75; font-family: 'Inter', sans-serif;">
                <p><strong>Oups !</strong> Pour pouvoir manipuler ou calculer les salaires, vous devez d'abord créer et activer l'exercice annuel de paie.</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Accédez à la configuration de la paie.</li>
                    <li>Créez l'exercice pour l'année en cours.</li>
                    <li>Marquez cet exercice comme <strong>Actif</strong>.</li>
                </ul>
            </div>`,
            buttonText: 'Créer l\'exercice maintenant',
            redirectUrl: "{{ Route::has('company.paiesalaries.exercices.create') ? route('company.paiesalaries.exercices.create') : '#' }}"
                },
            {
                keywords: ['contrat actif', 'sans contrat', 'contrat de travail'],
            title: '📋 Contrat de Travail Manquant',
            html: `<div style="text-align: left; font-size: 14px; line-height: 1.6; color: #4f5d75; font-family: 'Inter', sans-serif;">
                <p><strong>Attention !</strong> Certains salariés actifs n'ont pas encore de contrat de travail associé pour la période sélectionnée.</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Un contrat actif est obligatoire pour inclure un salarié dans le calcul de la paie.</li>
                    <li>Associez un contrat (CDI, CDD, Stage) pour pouvoir continuer.</li>
                </ul>
            </div>`,
            buttonText: 'Gérer les contrats',
            redirectUrl: "{{ Route::has('company.contracts.create') ? route('company.contracts.create') : '#' }}"
                },
            {
                keywords: ['période est clôturée', 'période clôturée', 'période fermée'],
            title: '🔒 Période de Paie Clôturée',
            html: `<div style="text-align: left; font-size: 14px; line-height: 1.6; color: #4f5d75; font-family: 'Inter', sans-serif;">
                <p>Cette période de paie est clôturée et archivée. Toutes les modifications ou recalculs de salaires y sont impossibles.</p>
                <p>Pour effectuer des régularisations, veuillez ajouter un élément de <strong>rappel</strong> sur la période active suivante.</p>
            </div>`,
            buttonText: 'Aller aux contrats',
            redirectUrl: "{{ Route::has('company.contracts.index') ? route('company.contracts.index') : '#' }}"
                }
            ],

            analyzeError: function(rawMessage) {
                const messageLower = rawMessage.toLowerCase();
                const matchedRule = this.rules.find(rule => 
                    rule.keywords.some(keyword => messageLower.includes(keyword))
            );

            if (matchedRule) {
                Swal.fire({
                    icon: 'info',
                    title: matchedRule.title,
                    html: matchedRule.html,
                    showCancelButton: true,
                    confirmButtonColor: '#253e87',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: matchedRule.buttonText,
                    cancelButtonText: 'Fermer',
                    background: '#ffffff',
                    iconColor: '#253e87',
                    customClass: {
                        popup: 'animate__animated animate__fadeInUp'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = matchedRule.redirectUrl;
                    }
                });
            return true;
                }
            return false;
            },

            init: function() {
                const firstInvalidField = document.querySelector('.is-invalid, .invalid-feedback');
            if (firstInvalidField) {
                    const formGroup = firstInvalidField.closest('.mb-3, .form-group, .col-12, .col-md-6') || firstInvalidField.parentElement;
            if (formGroup) {
                formGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const input = formGroup.querySelector('input, select, textarea');
            if (input) {
                setTimeout(() => input.focus(), 800);
                        }
                    }

            Swal.fire({
                icon: 'warning',
            title: '💡 Saisie Incomplète',
            text: 'Certains champs requis sont manquants ou incorrects. L\'assistant a surligné les erreurs en rouge pour vous aider à corriger la saisie.',
            confirmButtonColor: '#253e87',
            background: '#ffffff'
                    });
                }

            this.renderWorkflowWidget();
            },

            renderWorkflowWidget: function() {
                const path = window.location.pathname;
            let steps = [];
            let currentStepIndex = -1;

            if (path.includes('/employees') || path.includes('/salariés')) {
                steps = [
                    { label: 'Création Fiche', path: '/employees/create' },
                    { label: 'Associer Contrat', path: '/contracts/create' },
                    { label: 'Calcul Paie', path: '/paie' }
                ];
            currentStepIndex = path.includes('/create') ? 0 : 1;
                } else if (path.includes('/paie') || path.includes('/calcul') || path.includes('/bulletins')) {
                steps = [
                    { label: 'Ouvrir Exercice', path: '/exercices' },
                    { label: 'Variables Brut', path: '/elements-brut' },
                    { label: 'Lancer Calcul', path: '/calcul' }
                ];
            currentStepIndex = path.includes('/calcul') ? 2 : 1;
                }

                if (steps.length > 0) {
                    const widgetHtml = `
            <div id="smartguard-workflow" style="position: fixed; bottom: 20px; left: 20px; background: white; border-radius: 30px; box-shadow: 0 10px 30px rgba(37,62,135,0.15); border: 1px solid rgba(37,62,135,0.15); padding: 10px 20px; z-index: 9999; display: flex; align-items: center; gap: 12px; font-family: 'Inter', sans-serif; transition: all 0.3s ease;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #28c848; animation: pulse 2s infinite;"></div>
                <span style="font-size: 11px; font-weight: 700; color: #253e87; text-transform: uppercase; letter-spacing: 0.5px;">Assistant Workflow :</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    ${steps.map((step, idx) => {
                        const isActive = idx === currentStepIndex;
                        const isDone = idx < currentStepIndex;
                        const color = isActive ? '#253e87' : (isDone ? '#28c848' : '#9ca3af');
                        const weight = isActive ? '700' : '500';
                        const icon = isDone ? '✓' : (idx + 1);
                        return `
                                        <div style="display: flex; align-items: center; gap: 4px; font-size: 12px;">
                                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: ${isActive ? '#253e87' : (isDone ? '#28c848' : '#e2e5ef')}; color: white; font-size: 10px; font-weight: bold;">${icon}</span>
                                            <span style="color: ${color}; font-weight: ${weight};">${step.label}</span>
                                            ${idx < steps.length - 1 ? '<span style="color: #cbd5e1;">➔</span>' : ''}
                                        </div>
                                    `;
                    }).join('')}
                </div>
            </div>
            <style>
                @keyframes pulse {
                    0 % { transform: scale(0.95); box- shadow: 0 0 0 0 rgba(40,200,72,0.7); }
                70% {transform: scale(1); box-shadow: 0 0 0 6px rgba(40,200,72,0); }
                100% {transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40,200,72,0); }
                            }
            </style>
                    `;
                    document.body.insertAdjacentHTML('beforeend', widgetHtml);
                }
            }
        };

        window.addEventListener('DOMContentLoaded', () => SmartGuard.init());
    </script>

    <script>
        // Gestion des messages de session via SweetAlert2
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: "{{ session('success') }}",
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: '#ffffff',
                iconColor: '#28c848',
                customClass: {
                    popup: 'animate__animated animate__fadeInRight'
                }
            });
        @endif

        @if(session('error'))
            if (!SmartGuard.analyzeError("{{ session('error') }}")) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#253e87',
                    background: '#ffffff',
                    iconColor: '#ff4d4f',
                    customClass: {
                        popup: 'animate__animated animate__shakeX'
                    }
                });
            }
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                html: '<ul style="text-align: left; list-style: none; padding: 0;">' +
                    '@foreach($errors->all() as $error)' +
                        '<li><i class="fas fa-exclamation-circle text-warning me-2"></i> {{ $error }}</li>' +
                    '@endforeach' +
                    '</ul>',
                confirmButtonColor: '#253e87',
                background: '#ffffff'
            });
        @endif
    </script>
    <script>
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner une option',
        allowClear: true,
        width: '100%'
        });

        // Fonction pour changer de thème
        function setTheme(themeName) {
            document.documentElement.setAttribute('data-theme', themeName);
        localStorage.setItem('selectedTheme', themeName);
        document.body.className = document.body.className.replace(/theme-\w+/g, '');
        document.body.classList.add(`theme-${themeName}`);
        }

        // Fonction pour toggle dark mode
        function toggleDarkMode() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('darkMode', newTheme);
        }

        // Charger les préférences sauvegardées
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('selectedTheme');
        const savedDarkMode = localStorage.getItem('darkMode');

        if (savedTheme) setTheme(savedTheme);
        if (savedDarkMode) {
            document.documentElement.setAttribute('data-bs-theme', savedDarkMode);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        });

        // Charger les préférences sauvegardées
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('selectedTheme');
        const savedDarkMode = localStorage.getItem('darkMode');

        if (savedTheme) {
            setTheme(savedTheme);
            }

        if (savedDarkMode) {
            document.documentElement.setAttribute('data-bs-theme', savedDarkMode);
            } else {
                // Détection automatique du mode sombre du système
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
                }
            }
        });

        // Notifications
        function loadNotifications() {
            fetch('{{ url("/notifications/ajax/unread?limit=5") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationList = document.getElementById('notificationList');
                        const notificationBadge = document.getElementById('notificationBadge');

                        // Vider la liste
                        notificationList.innerHTML = '';

                        if (data.notifications.length === 0) {
                            notificationList.innerHTML = `
                            <div class="text-center py-3">
                                <i class="fas fa-bell-off text-muted" style="font-size: 2rem;"></i>
                                <small class="text-muted d-block mt-1">Aucune notification</small>
                            </div>
                        `;
                        } else {
                            data.notifications.forEach(notification => {
                                const notificationItem = document.createElement('a');
                                notificationItem.href = `{{ url('/notifications') }}/${notification.id}`;
                                notificationItem.className = 'dropdown-item';
                                notificationItem.innerHTML = `
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-${notification.color} rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="${notification.icon || 'ti-bell'}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${notification.title}</h6>
                                        <p class="mb-0 text-sm">${notification.message.length > 50 ? notification.message.substring(0, 50) + '...' : notification.message}</p>
                                        <small class="text-muted">${new Date(notification.created_at).toLocaleDateString()}</small>
                                    </div>
                                </div>
                            `;
                                notificationList.appendChild(notificationItem);
                            });
                        }

                        // Mettre à jour le badge
                        if (data.total_unread > 0) {
                            notificationBadge.textContent = data.total_unread > 99 ? '99+' : data.total_unread;
                            notificationBadge.style.display = 'inline';
                        } else {
                            notificationBadge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Erreur lors du chargement des notifications:', error));
        }

        // Charger les notifications au démarrage
        document.addEventListener('DOMContentLoaded', function () {
            loadNotifications();

        // Actualiser les notifications toutes les 30 secondes
        setInterval(loadNotifications, 30000);
        });

        // Auto-dismiss notifications after 5 seconds
        const notifications = document.querySelectorAll('.alert-dismissible');
        notifications.forEach(function (notification) {
            setTimeout(function () {
                const bsAlert = new bootstrap.Alert(notification);
                bsAlert.close();
            }, 5000); // 5 seconds
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- XLSX Library for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF Library for PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    @stack('scripts-external')
    @stack('scripts')
    <!-- Modal Guide Utilisateur -->
    <div class="modal fade" id="modalGuide" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95%;">
            <div class="modal-content"
                style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2); height: 90vh;">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #253e87 0%, #1a2d64 100%); color: white; padding: 20px 24px; flex-shrink: 0;">
                    <h5 class="modal-title"
                        style="color: white; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-book-open"></i> Guide d'Utilisation RH Flow
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="background: #f8faff; flex-grow: 1; overflow: hidden;">
                    <iframe id="guideIframe" data-src="{{ route('guide-utilisateur') }}"
                        style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                <div class="modal-footer"
                    style="background: #ffffff; border-top: 1px solid #eef2f7; padding: 15px 24px; flex-shrink: 0;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('guideIframe').contentWindow.print()">Imprimer le
                        Guide</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts pour le Guide -->
    <script>
        function openGuideModal() {
            const modalEl = document.getElementById('modalGuide');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
            }

        const iframe = document.getElementById('guideIframe');
        if (iframe && !iframe.getAttribute('src')) {
            iframe.setAttribute('src', iframe.getAttribute('data-src'));
            }
        modal.show();
        }
    </script>
</body>

</html>