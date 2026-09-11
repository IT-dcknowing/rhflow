<!DOCTYPE html>
<html lang="fr">

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <!-- Themify Icons Fonts -->
    <link rel="stylesheet" href="{{ asset('themify-icons/themify-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rh-custom-colors.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper.css') }}" />
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

        /* Style pour les sidebars modulaires */
        .module-sidebar {
            display: none;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .module-sidebar.active {
            display: block;
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
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #253e87;
            border-color: #253e87;
            color: white;
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

        .result-source-table {
            position: absolute;
            left: -10000px;
            top: auto;
            width: auto !important;
            height: auto;
            opacity: 0;
            pointer-events: none;
        }

        .split-result-tables {
            display: grid;
            gap: 14px;
        }

        .split-result-block {
            overflow-x: auto;
            border: 1px solid #e5e8ef;
            border-radius: 8px;
            background: #fff;
        }

        .split-result-title {
            margin: 0;
            padding: 8px 10px;
            background: #f5f7fb;
            color: #566a7f;
            font-size: 12px;
            font-weight: 700;
        }

        .split-result-table {
            width: 100%;
            min-width: 780px;
            margin: 0;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .split-result-table th,
        .split-result-table td {
            padding: 7px 6px !important;
            border: 1px solid #e5e8ef !important;
            color: #2f4054;
            font-size: 11px;
            line-height: 1.2;
            text-align: center;
            white-space: normal;
            word-break: break-word;
            vertical-align: middle;
        }

        .split-result-table th {
            background: #ddcd08;
            color: #111827;
            font-weight: 700;
        }

        .split-result-table tr:last-child td {
            background: #fffdf0;
            font-weight: 700;
        }

        .pay-summary-wrapper {
            overflow-x: visible !important;
        }

        .pay-summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 0;
        }

        .pay-summary-table thead th {
            border: 0;
            padding: 0 0 8px;
        }

        .pay-summary-table tbody,
        .pay-summary-table tr {
            display: block;
        }

        .pay-summary-table tr:not(.thead-dark) {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 8px;
            margin-bottom: 8px;
        }

        .pay-summary-table td {
            border: 1px solid #d9dee3;
            padding: 8px 10px !important;
            background: #fff;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            line-height: 1.2;
        }

        .pay-summary-table td[style*="background-color:black"] {
            background: #000;
            color: #fff;
            justify-content: flex-end;
            font-size: 13px;
        }

        .pay-summary-table strong {
            width: 100%;
        }

        .pay-summary-table center,
        .pay-summary-table h5 {
            margin: 0;
        }

        #tableau.hide-old-regime .old-regime-col,
        #tableau.hide-old-regime thead tr:not(.thead-dark)> :nth-child(8),
        #tableau.hide-old-regime thead tr:not(.thead-dark)> :nth-child(9),
        #tableau.hide-old-regime thead tr:not(.thead-dark)> :nth-child(10),
        #tableau.hide-old-regime thead tr:not(.thead-dark)> :nth-child(11),
        #tableau.hide-old-regime tbody tr> :nth-child(8),
        #tableau.hide-old-regime tbody tr> :nth-child(9),
        #tableau.hide-old-regime tbody tr> :nth-child(10),
        #tableau.hide-old-regime tbody tr> :nth-child(11) {
            display: none;
        }

        /* Header styles */
        .main-header {
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .clignoter {
            animation: clignoter 2s infinite;
        }

        @keyframes clignoter {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* Mobile menu animation */
        #mobileMenu {
            transition: all 0.3s ease-in-out;
        }

        #mobileMenu.show {
            display: block;
        }
    </style>
    @stack('styles')
</head>

<body>
    <section class="monDivimage">
        <!-- [ Header ] start -->
        <header class="main-header bg-gradient-to-r from-primary to-primary/80 shadow-lg">
            <div class="bg-primary container mx-auto px-4 ">
                <div class="flex items-center justify-between h-16 ">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{route('landingpage')}}" class="flex items-center space-x-3">
                            <img src="{{ asset('img/logos/logo-light.png') }}" width="120px" alt="RH-Flow Logo"
                                class="h-8 w-auto">
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="{{route('landingpage')}}"
                            class="text-white hover:text-white/80 transition-colors font-medium flex items-center space-x-2">
                            <i class="ri-home-4-line"></i>
                            <span>Accueil</span>
                        </a>
                        <a href="{{route('simulateur')}}"
                            class="text-white hover:text-white/80 transition-colors font-medium flex items-center space-x-2">
                            <i class="ri-calculator-line"></i>
                            <span>Simulateur</span>
                        </a>
                    </nav>

                    <!-- Mobile menu button -->
                    <button class="md:hidden text-white hover:text-white/80 transition-colors"
                        onclick="toggleMobileMenu()">
                        <i class="ri-menu-line text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="container mx-auto px-4">
                <!-- Title Banner -->
                <div class="py-6 text-center">
                    <h1 class="text-2xl md:text-3xl font-bold text-primary mb-2">
                        <i class="ri-calculator-line mr-2"></i>
                        SIMULATEUR DE LIVRE DE PAIE RHFLOW
                    </h1>
                    <p class="text-primary text-sm md:text-base max-w-2xl mx-auto">
                        Outil d'aide à l'application de la réforme fiscale relative à l'impôt sur les traitements et
                        salaires (ITS)
                    </p>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden bg-primary/95 border-t border-white/20">
                <div class="container mx-auto px-4 py-4 space-y-3">
                    <a href="{{route('landingpage')}}"
                        class="block text-white hover:text-white/80 transition-colors font-medium py-2">
                        <i class="ri-home-4-line mr-2"></i>Accueil
                    </a>
                    <a href="{{route('simulateur')}}"
                        class="block text-white hover:text-white/80 transition-colors font-medium py-2">
                        <i class="ri-calculator-line mr-2"></i>Simulateur
                    </a>
                </div>
            </div>
        </header>
        <!-- [ Header ] End -->
        <br>
        <br>
        <br>
        <!-- [ Banner ] start -->
        <section class="container" id="home">
            <div id="BrutNet" style="display: block;">
                <div class="container">
                    <div class="row align-items-center" align="center" style="margin-top: -35px;">
                        <h2><b class="fw-bold">Bienvenue sur le simulateur de votre livre de paie</b></h2>
                        <h6>Outil d’aide à l’application de la réforme fiscale relative à l’impôt sur les traitements et
                            salaires (ITS)</h6>
                    </div>
                    <br>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-md-8">
                            <br>
                            <!-- Titre avec puce cliquable -->
                            <h5 style="cursor: pointer;">
                                <a onclick="toggleParagraphe()"><span>&#8227;</span></a> <!-- Puce cliquable -->
                                <strong> Qu’est-ce que c’est ? </strong>
                            </h5>
                            <div id="monParagraphe" style="display:none; padding: 0.5em;" class="card shadow-none">
                                <p>Le simulateur du livre de paie vient répondre au besoin <b>d’évaluation de l’impact
                                        de la nouvelle disposition
                                        des Impôts sur Traitements et Salaires (ITS) </b>sur l’ensemble des salariés de
                                    votre entreprise.
                                    Il se positionne comme un <b>outil d’aide à l’application du nouveau dispositif des
                                        ITS</b> par une
                                    appréciation du coût salarial supporté par l’entreprise. Lequel coût ne devrait en
                                    principe pas augmenter. Car la réforme vise pour la plupart des salariés, la baisse
                                    des retenues
                                    fiscales sur salaire pour une augmentation du salaire net. Selon le gouvernement
                                    ivoirien, cette nouvelle approche des
                                    ITS a pour objectif principal l’augmentation du pouvoir d’achat des salariés du
                                    secteur privé. En pratique,
                                    le coût salarial pourrait augmenter dans les cas suivants :</p>

                                <ul>
                                    <li>L’application de la prime d’ancienneté</li>
                                    <li>La tranche des salaires qui subissent une perte en raison de la réforme. Il est
                                        recommandé aux employeurs, un effort d’ajustement pour cette catégorie de
                                        salariés.</li>
                                </ul>
                            </div>
                            <h5 style="cursor: pointer;">
                                <a onclick="toggleParagraphe2()"><span>&#8227;</span></a> <!-- Puce cliquable -->
                                Comment utiliser ce simulateur ?
                            </h5>
                            <div id="monParagraphe2" style="display:none; padding: 0.5em;" class="card shadow-none">
                                <p>Il est recommandé de se munir du plus récent livre de paie (excepté celui de
                                    décembre) qui contient les informations sur les salariés telles que :</p>

                                <ul>
                                    <li><b>Type d’employé :</b> local ou expatrié</li>
                                    <li><b>La situation de famille :</b> situation matrimoniale, nombre d’enfants et
                                        nombre de bénéficiaires à la CMU</li>
                                    <li><b>Le Salaire brut total (y compris toutes les primes) :</b> ce total cumule
                                        tout le salaire brut tel que le salaire de base, le sursalaire, la prime
                                        d’ancienneté, la prime de transport, la prime de responsabilité, les autres
                                        rémunérations et primes</li>
                                    <li><b>Le salaire brut imposable : </b>la partie du salaire brut soumis à impôt
                                        (sans l’abattement de 20%)</li>
                                    <li><b>Le salaire brut social </b>soumis à la cotisation CNPS</li>
                                </ul>

                                <p> Puis à l’aide du bouton « Ajouter », insérer les employés. Dans un souci de
                                    confidentialité,
                                    il ne vous sera demandé ni les informations sur votre société ni l’identité de vos
                                    salariés,
                                    qui seront nommés par une numérotation automatisée de l’employé 1 à l’employé n
                                    selon le nombre de salariés de votre entreprise.
                                    Une fois terminé,
                                    cliquez sur le bouton <b>« Simuler »</b> pour le traitement de vos informations.
                                </p>
                                <!-- Partie 1 - Télécharger le Modèle de Fichier Excel -->
                                <h6><b>Simulation par importation</b></h6>
                                <section>
                                    <p><b>Télécharger le Modèle de Fichier Excel</b></p>
                                    <p>
                                        Avant de commencer, assurez-vous de suivre ces étapes simples pour télécharger
                                        le modèle Excel nécessaire :
                                    </p>
                                    <ol>
                                        <li>Cliquez sur le bouton ci-dessous pour télécharger le modèle Excel.</li>
                                        <li>Le fichier téléchargé contient la structure appropriée pour l'importation
                                            des données.</li>
                                        <li>Enregistrez le fichier sur votre ordinateur dans un emplacement facilement
                                            accessible.</li>
                                    </ol>

                                    <p>
                                        Assurez-vous de ne pas modifier la structure du fichier pour garantir une
                                        importation réussie.
                                    </p>
                                </section>
                                <!-- Partie 2 - Importer le Fichier Excel -->
                                <section>
                                    <p><b>Importer le Fichier Excel</b></p>
                                    <p>
                                        Maintenant que vous avez téléchargé le modèle Excel, suivez ces étapes pour
                                        importer vos données :
                                    </p>
                                    <ol>
                                        <li>Rassemblez toutes les informations nécessaires que vous souhaitez importer.
                                        </li>
                                        <li>Remplissez le modèle Excel téléchargé avec vos données.</li>
                                        <li>Assurez-vous que toutes les cellules obligatoires sont remplies
                                            correctement.</li>
                                        <li>Revenez sur notre plateforme et utilisez l'option d'importation.</li>
                                        <li>Sélectionnez le fichier Excel que vous avez rempli.</li>
                                        <li>Soumettez le fichier pour finaliser le processus d'importation.</li>
                                    </ol>
                                    <p>
                                        Si vous rencontrez des difficultés ou si vous avez des questions, n'hésitez pas
                                        à nous contacter <a
                                            href="https://dc-knowing.com/GRH/pages/%C3%80-propos_de#contact">ici</a>
                                        pour obtenir de l'aide.
                                    </p>
                                </section>
                            </div>
                            <h5 style="cursor: pointer;">
                                <a onclick="toggleParagraphe3()"><span>&#8227;</span></a> <!-- Puce cliquable -->
                                Quel traitement le simulateur effectue-t-il ?
                            </h5>
                            <div id="monParagraphe3" style="display:none; padding: 0.5em;" class="card shadow-none">
                                <p>Après avoir entré vos informations, le simulateur met à votre disposition :</p>

                                <ul>
                                    <li><b>Le traitement des ITS selon l’ancienne formule par salarié </b>à comparer
                                        avec les informations de votre livre de paie qui a servi au renseignement du
                                        simulateur</li>
                                    <li><b>Le nouveau traitement des ITS par salarié </b>avec les composantes des
                                        nouvelles retenues fiscales</li>
                                    <li><b>La différence de traitement des ITS par salarié</b> qui ressort un gain ou
                                        une perte pour le salarié en raison de l’application de la réforme</li>
                                    <li><b>Les anciens salaires nets</b> à vérifier sur le livre de paie détenu</li>
                                    <li><b>Les nouveaux salaires nets à payer </b>à compter du mois de janvier (veillez
                                        à l’application de la prime d’ancienneté)</li>
                                    <li><b>La différence des salaires nets</b> qui devrait être identique à la
                                        différence des retenues fiscales</li>
                                    <li><b>Le rangement des salariés</b> par catégorie de <b>gain</b> ou de <b>perte</b>
                                        sur salaire</li>
                                    <li><b>Le total du traitement</b> de l’ensemble des salariés</li>
                                </ul>
                            </div>
                            <h5 style="cursor: pointer;">
                                <a onclick="toggleParagraphe4()"><span>&#8227;</span></a> <!-- Puce cliquable -->
                                Recommandations :
                            </h5>
                            <!-- Paragraphe caché initialement -->
                            <div id="monParagraphe4" style="display:none; padding: 0.5em;" class="card shadow-none">
                                <p>A travers ce simulateur, les entreprises sont sensibilisées à :</p>

                                <ul>
                                    <li>Payer effectivement les nouveaux salaires nets qui présentent un gain. Ils
                                        pourraient connaître une augmentation supplémentaire en raison de la prime
                                        d’ancienneté.</li>
                                    <li>Entreprendre une politique d’ajustement des salaires qui subissent une perte en
                                        raison de la réforme. Cet ajustement pourrait consister à garantir au moins
                                        l’ancien net.</li>
                                </ul>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-xl-12 align-items-center ">
                        <div class="row">
                            <div class="card card-body">
                                <h2 align="center">Exclusif : Découvrez notre tout nouveau calculateur de salaire net au
                                    brut ! <br />Essayer-le dès maintenant et simplifiez vos calculs de
                                    rémunération.<br /><br />
                                    <button class="btn-submit btn btn-primary" id="boutonChanger"
                                        onclick="afficherDiv()">Net au Brut</button>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="brutaunet" style="display: block;">
                    <div class="row gy-3 ">
                        <div class="col-xl-12 align-items-center ">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Démarrer votre simulation</h5>
                                </div>
                                <div class="card-body">
                                    <div class="col-xl-12">
                                        <p>Cher utilisateur,</p>
                                        <p>Nous sommes ravis de vous offrir la flexibilité de choisir la méthode qui
                                            vous convient le mieux pour effectuer des simulations. Vous avez deux
                                            options pratiques :</p>
                                    </div>
                                    <div class="row">
                                        <div class=" col-xl-6">
                                            <ul>
                                                <li><strong>1- Simulation Employé par Employé :</strong> Pour une
                                                    approche plus détaillée ou pour des ajustements spécifiques,
                                                    choisissez de simuler employé par employé. Cela vous permettra de
                                                    personnaliser chaque simulation selon les besoins individuels.</li>
                                            </ul>
                                            <button class="btn-submit btn btn-primary" id="nbr" onclick="MyNbre()">
                                                {{ __('Démarrer') }}
                                            </button>
                                        </div>
                                        <div class="col-xl-6">
                                            <ul>
                                                <li><strong>2- Simulation par Importation :</strong> Si vous préférez
                                                    gagner du temps et traiter plusieurs employés en une seule fois,
                                                    utilisez la fonction d'importation. Téléchargez simplement le
                                                    fichier de simulation, suivez les instructions, et vous serez
                                                    prêt(e) en un rien de temps.</li>
                                            </ul>
                                            <button class="btn btn-info" id="simulerimport"
                                                onclick="affichedivimport()">Simuler par importation (Fichier
                                                Excel)</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="form_input" class="card bg-dark shadow-none mb-4" style="display: block;">
                                <div class="card-header">
                                    <div class="row align-items-center" align="center">
                                        <marquee behavior="" direction="">
                                            <h4 style="color: #fff;">Le simulateur est régulièrement mis à jour par nos
                                                équipes grâce à vos contributions et observations.</h4>
                                        </marquee>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h5 style="color: white;">{{ __('Calcul de Paie') }}</h5>
                                        </div>
                                        <div class="col-md-4" align="center">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <strong><label style="color: white;">Taux d'accident de travail
                                                            (CNPS) :</label></strong>
                                                    <select type="text" name="atravail" id="atravail">
                                                        <option value="0,02">2%</option>
                                                        <option value="0,03" selected>3%</option>
                                                        <option value="0,04">4%</option>
                                                        <option value="0,05">5%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" align="right">

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="form" class="rowdiv mb-4">

                                    </div>
                                    <div class="col-md-12 text-start">
                                        <button class="btn-submit btn btn-primary" id="ajout" onclick="Ajout()"
                                            disabled>{{ __('Ajouter') }}</button>
                                        <button class="btn-submit btn btn-primary" id="ajout2" onclick="Ajout2()"
                                            hidden>{{ __('Ajouter') }}</button>
                                        <button class="btn-submit btn btn-danger" id="retrait" onclick="Retrait()"
                                            disabled hidden>{{ __('Supprimer') }}</button>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn-submit btn btn-primary" id="update" onclick="updatetab()"
                                        disabled>
                                        {{ __('Modifier') }}
                                    </button>
                                    <button class="btn-submit btn btn-primary" id="addSig" onclick="Resultat()"
                                        disabled>
                                        {{ __('Simuler') }}
                                    </button>
                                    <button class="btn-submit btn btn-danger" id="reset" onclick="reset()">
                                        {{ __('Réinitialiser') }}
                                    </button>
                                </div>
                            </div>
                            <div id="etape" class="card bg-dark shadow-none mb-4" style="display: none;">
                                <div class="row" style="margin: 10px;">
                                    <div class="col-md-6" style=" padding:10px;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="badge" align="left">1</div>
                                            </div>
                                            <div class="col-md-10">
                                                <!-- Partie 1 - Télécharger le Modèle de Fichier Excel -->
                                                <section>
                                                    <h2 style="color: #fff;">Télécharger le Modèle de Fichier Excel</h2>
                                                    <p>
                                                        Avant de commencer, assurez-vous de suivre ces étapes simples
                                                        pour télécharger le modèle Excel nécessaire :
                                                    </p>
                                                    <ol>
                                                        <li>Cliquez sur le bouton ci-dessous pour télécharger le modèle
                                                            Excel.</li>
                                                        <li>Le fichier téléchargé contient la structure appropriée pour
                                                            l'importation des données.</li>
                                                        <li>Enregistrez le fichier sur votre ordinateur dans un
                                                            emplacement facilement accessible.</li>
                                                    </ol>

                                                    <p>
                                                        Assurez-vous de ne pas modifier la structure du fichier pour
                                                        garantir une importation réussie.
                                                    </p>
                                                </section>
                                                <button id="dawonload" class="btn btn-success"
                                                    onclick="telechargerModele()" disabled>Télécharger le modèle
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style=" padding:10px;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="badge" align="left">2</div>
                                            </div>
                                            <div class="col-md-10">
                                                <!-- Partie 2 - Importer le Fichier Excel -->
                                                <section>
                                                    <h2 style="color: #fff;">Importer le Fichier Excel</h2>
                                                    <p>
                                                        Maintenant que vous avez téléchargé le modèle Excel, suivez ces
                                                        étapes pour importer vos données :
                                                    </p>
                                                    <ol>
                                                        <li>Rassemblez toutes les informations nécessaires que vous
                                                            souhaitez importer.</li>
                                                        <li>Remplissez le modèle Excel téléchargé avec vos données.</li>
                                                        <li>Assurez-vous que toutes les cellules obligatoires sont
                                                            remplies correctement.</li>
                                                        <li>Revenez sur notre plateforme et utilisez l'option
                                                            d'importation.</li>
                                                        <li>Sélectionnez le fichier Excel que vous avez rempli.</li>
                                                        <li>Soumettez le fichier pour finaliser le processus
                                                            d'importation.</li>
                                                    </ol>
                                                    <p>
                                                        Si vous rencontrez des difficultés ou si vous avez des
                                                        questions, n'hésitez pas à nous contacter <a
                                                            href="https://dc-knowing.com/GRH/pages/%C3%80-propos_de#contact">ici</a>
                                                        pour obtenir de l'aide.
                                                    </p>
                                                </section>
                                                <button class="btn btn-success" id="importetape"
                                                    onclick="importerFichierXLSX()" disabled>Importer votre
                                                    fichier</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>{{ __('Resultat de Paie') }}</h5>
                                                </div>
                                                <div class="col-md-6" align="right">
                                                    <button class="btn btn-success" id="exporterExcel"
                                                        onclick="exportToExcel()" disabled>Exporter en Excel</button>
                                                    <button class="btn-submit btn btn-danger" id="reset2"
                                                        onclick="reset()" hidden>
                                                        {{ __('Réinitialiser') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered hide-old-regime result-source-table"
                                                    id="tableau" style="max-height: 200px;">
                                                    <thead style="position: sticky;">
                                                        <tr class="thead-dark">
                                                            <th colspan="7" style="border: solid 2px #fff;">
                                                                <center>Infos employés</center>
                                                            </th>
                                                            <th colspan="4" class="old-regime-col"
                                                                style="border: solid 2px #fff;">
                                                                <center>ITS Ancien régime</center>
                                                            </th>
                                                            <th colspan="3" style="border: solid 2px #fff;">
                                                                <center>ITS nouveau régime</center>
                                                            </th>
                                                            <th style="border: solid 2px #fff;">
                                                                <center> retenues fiscales</center>
                                                            </th>
                                                            <th colspan="2" style="border: solid 2px #fff;">
                                                                <center>Retenues sociales</center>
                                                            </th>
                                                            <th colspan="3" style="border: solid 2px #fff;">
                                                                <center> salaires nets</center>
                                                            </th>
                                                            <th style="border: solid 2px #fff;">
                                                                <center>Observations</center>
                                                            </th>
                                                            <th colspan="3" style="border: solid 2px #fff;">
                                                                <center>Parts patronales fiscales</center>
                                                            </th>
                                                            <th colspan="2" style="border: solid 2px #fff;">
                                                                <center>Parts patronales sociales</center>
                                                            </th>
                                                            <th style="border: solid 2px #fff;">
                                                                <center>Total patronales</center>
                                                            </th>
                                                            <th style="border: solid 2px #fff;">
                                                                <center>Coût salarial</center>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>N° Emp</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Type</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Nb Parts</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Brut Total</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>SBI</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>SBS</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Bénéf. CMU</center>
                                                            </th>
                                                            <th class="old-regime-col"
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>IS</center>
                                                            </th>
                                                            <th class="old-regime-col"
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>CN</center>
                                                            </th>
                                                            <th class="old-regime-col"
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>IGR</center>
                                                            </th>
                                                            <th class="old-regime-col"
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>total retenues</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Impôts Brut</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>RICF</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>total retenues</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Différence</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>cnps</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>cmu</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Net Ancien</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Net Nouveau</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Différence</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Impact du nouveau régime</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>CN</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>CE</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>FDFP</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>PF / AT / CNPS</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>CMU</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>Somme des parts</center>
                                                            </th>
                                                            <th
                                                                style="background-color: #ddcd08; border: solid 2px #fff;">
                                                                <center>-</center>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                <div class="split-result-tables" id="paySplitTables"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-none mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>{{ __('Resumé de Paie') }}</h5>
                                        </div>
                                        <div class="col-md-6" align="right">
                                            <button type="button" onclick="exportSummaryToExcel('resume')" id="exportExcelResume"
                                                class="btn btn-success">Exporter en Excel</button>
                                            <button type="button" onclick="generate()" id="exportpdf" target="_blanks"
                                                class="btn btn-info">Exporter en PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="resume">
                                    <div class="pay-summary-wrapper">
                                        <div style="margin: 0,5em;">
                                            <table class="table table-bordered pay-summary-table">
                                                <div class="row">
                                                    <div id="logo" class="col-md-2" style="display: none;">
                                                        <div class="footer-logo mb-3">
                                                            <a class="navbar-brand bg-transparent" href="#">
                                                                <img src="{{ asset('assets/images/Logo.png') }}"
                                                                    alt="logo">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div id="merci" class="col-md-9"
                                                        style="margin-top: 15px; margin:1em; display: none;">
                                                        <p>
                                                            Nous vous remercions d'avoir utilisé notre simulateur de
                                                            paie.
                                                            Votre confiance est précieuse, et nous espérons avoir
                                                            répondu à vos attentes.
                                                            N'hésitez pas à revenir pour d'autres simulations et
                                                            services.
                                                        </p>
                                                    </div>
                                                </div>
                                                <thead>
                                                    <tr class="thead-dark">
                                                        <th colspan="8">
                                                            <center>
                                                                <h5 style="color: #fff;">
                                                                    {{ __('RESUME - SIMULATION DE PAIE') }}
                                                                </h5>
                                                            </center>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td><strong>Total Employé :</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalemp" style="color:yellow;"></span><span
                                                                id="totalimpan" style="display:none;"></span><span
                                                                id="totalvarmasse"
                                                                style="display:none;"></span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Gain Salarial </strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalgainsal" style="color:green;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total Impôts Nouveau Régime</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalimpnv" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total Parts Patronales </strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalpatron" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Perte Salariale </strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalpertsal" style="color:red;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total Salaires Nets </strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalnet" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total Coût Salarial </strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalcout" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Brut</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalbrut" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total SBI</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalsbiresume" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total SBS</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalsbsresume" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total CNPS Salariale</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalcnpssal" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total CMU Salariale</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalcmusal" style="color:yellow;"></span> <span
                                                                style="color: white;">FCFA</span></strong></td>
                                                    <td><strong>Total Retenues SalariÃ©</strong></td>
                                                    <td style="background-color:black; text-align: right;"><strong><span
                                                                id="totalretenuessal" style="color:yellow;"></span>
                                                            <span style="color: white;">FCFA</span></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="NetBrut" class="container" style="display: none;">
                <div class="row mb-4">
                    <div class="card bg-dark shadow-none">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 style="color: white;">{{ __('Calcul Salaire Brut') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="net_pay" class="col-form-label">Salaire Net à Atteindre <span
                                            class="text-denger">*</span></label>
                                    <input type="number" name="net_pay" class="form-control" id="net_pay"
                                        required="required" min="75000" placeholder="Net à Atteindre">
                                    @error('net_pay')
                                        <span class="invalid-net_pay" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label id="transport_brut" class="col-form-label">Prime de transport</label>
                                    <input type="number" id="tp_brut" class="form-control" name="tp_brut" required=""
                                        min="0" placeholder="Entrez la prime de transport">
                                    <!-- <select type="text" name="tp_brut" class="form-control" id="tp_brut">
                                <option value="30000">Abidjan</option>
                                <option value="25000">Bouaké</option>
                                <option value="22000">Autres villes</option>
                            </select>
                            <p></p>-->
                                    <input type="number" id="tp_brut_libre" class="form-control" name="tp_brut_libre"
                                        required="" min="0" placeholder="Entrez la prime de transport" hidden>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="cmu" class="col-form-label">Cotisation CMU</label>
                                    <select type="text" name="cmu_brut" class="form-control" id="cmu_brut">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="5">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="situation_mat2" class="col-form-label">Situation matrimoniale</label>
                                    <select type="text" name="situation_mat2" class="form-control" id="situation_mat2"
                                        onchange="statusbrut()">
                                        <option value="1">Célibataire</option>
                                        <option value="2">Marié(e)</option>
                                        <option value="3">Divorcé(e)</option>
                                        <option value="4">Veuf(ve)</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="emp_enfts2" class="col-form-label">Enfants à charge</label>
                                    <select type="text" name="emp_enfts2" class="form-control" id="emp_enfts2"
                                        onchange="statusbrut()">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="emp_personne2" class="col-form-label">Personnes infirmes à
                                        charge</label>
                                    <select type="text" name="emp_personne2" class="form-control" id="emp_personne2"
                                        onchange="statusbrut()">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="part_igr" class="col-form-label">Nombre de parts</label>
                                    <input type="number" name="part_igr" value="1" class="form-control" id="part_igr"
                                        placeholder="Nombre de parts" readonly="readonly">
                                </div>

                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn-submit btn btn-primary" id="addSig" onclick="trouverBrutTotal()">
                                {{ __('Simuler') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="card shadow-none">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>{{ __('Resultat Salaire Brut') }} Selon le nouveau régime</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive brut-result-wrapper">
                                <table class="table table-bordered brut-result-table result-source-table"
                                    id="tableauBrut">
                                    <thead>
                                        <tr class="thead-dark">
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Net à atteindre</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Prime de transport</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Nombre de parts</center>
                                            </th>
                                            <!--<th style="background-color: #ddcd08; color:black; border: solid 2px #fff;"><center>Prime exonérée (10%)</center></th>-->
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Impôt Brut </center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>RICF</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Impôt net</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>CNPS</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Bénéficiaires CMU</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Total retenues</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>SBI</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>SBS</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Salaire Brut</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Parts patronales fiscales</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Parts patronales sociales</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Total patronales</center>
                                            </th>
                                            <th style="background-color: #ddcd08; color:black; border: solid 2px #fff;">
                                                <center>Coût salarial</center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td>
                                            <center><strong><span id="resultatnet"
                                                        style="color: green;"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultattp"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatpart"></span></strong>
                                                <center>
                                        </td>
                                        <!--<td><center><strong><span id="resultatexo"></span></strong><center></td>-->
                                        <td>
                                            <center><strong><span id="resultatimp"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatricft"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatnetimp"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatcnps"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatcmu"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="totalretenues"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="totalsbi"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="totalsbs"></span></strong>
                                                <center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatbrut" style="color: red;"></span></strong>
                                            </center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatpatfiscales"></span></strong></center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatpatsociales"></span></strong></center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatpatronal"></span></strong></center>
                                        </td>
                                        <td>
                                            <center><strong><span id="resultatcoutsal"></span></strong></center>
                                        </td>
                                    </tbody>
                                </table>
                                <div class="split-result-tables" id="brutSplitTables"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-none mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ __('Resumé de Paie') }}</h5>
                            </div>
                            <div class="col-md-6" align="right">
                                <button type="button" onclick="exportSummaryToExcel('resumeBrutNet')" id="exportExcelResumeBrutNet"
                                    class="btn btn-success">Exporter en Excel</button>
                                <button type="button" onclick="generateBrutNetSummary()" id="exportpdfbrutnet"
                                    target="_blanks" class="btn btn-info">Exporter en PDF</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="resumeBrutNet">
                        <div class="pay-summary-wrapper">
                            <div style="margin: 0,5em;">
                                <table class="table table-bordered pay-summary-table">
                                    <tr>
                                        <td><strong>Total Employé :</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeTotalEmp" style="color:yellow;"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Gain Salarial</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeGain" style="color:green;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total Impôts Nouveau Régime</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeImpNouveau" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total Parts Patronales</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumePatronales" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Perte Salariale</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumePerte" style="color:red;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total Salaires Nets</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeNet" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total Coût Salarial</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeCout" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Brut</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeBrut" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total SBI</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeSbi" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total SBS</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeSbs" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total CNPS Salariale</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeCnps" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total CMU Salariale</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeCmu" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                        <td><strong>Total Retenues Salarié</strong></td>
                                        <td style="background-color:black; text-align: right;"><strong><span
                                                    id="brutResumeRetenues" style="color:yellow;"></span> <span
                                                    style="color: white;">FCFA</span></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [ Banner ] end -->

        <!-- [ Footer ] start -->
        <footer class="bg-gray-900 text-white pt-16 pb-8">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                    <div>
                        <a href="https://dc-knowing.com/RH-Flow" class="inline-block mb-6">
                            <img src="{{ asset('img/logos/logo-light.png') }}" width="100px" alt="logo">
                        </a>
                        <p class="text-gray-400 mb-6">Simplifiez, automatisez et optimisez la gestion de vos ressources
                            humaines.</p>
                        <div class="flex space-x-4">
                            <a href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="ri-linkedin-fill"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="ri-twitter-x-fill"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="ri-instagram-fill"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-6">Produit</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Fonctionnalités</a>
                            </li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tarifs</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Témoignages</a>
                            </li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-6">Ressources</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Guides</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Centre d'aide</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-6">Paiement</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Paiement sécurisé
                                    par GT Bank</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-400 mb-4 md:mb-0">© 2025 RH-Flow. Tous droits réservés.</p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">Mentions légales</a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">Politique de
                                confidentialité</a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">CGU</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- [ Footer ] end -->
        <!-- Required Js -->
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/feather.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function loadScriptOnce(src) {
            return new Promise(function (resolve, reject) {
                var existing = document.querySelector('script[src="' + src + '"]');
                if (existing) {
                    existing.addEventListener('load', resolve, { once: true });
                    existing.addEventListener('error', reject, { once: true });
                    return;
                }

                var script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        async function ensureHtml2Pdf() {
            if (typeof html2pdf !== 'undefined') {
                return true;
            }

            try {
                await loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js');
                return typeof html2pdf !== 'undefined';
            } catch (error) {
                console.error(error);
                return false;
            }
        }

        function cleanPdfText(cell) {
            if (!cell) return '-';
            var clone = cell.cloneNode(true);
            clone.querySelectorAll('[hidden], [style*="display:none"], [style*="display: none"]').forEach(function (element) {
                element.remove();
            });
            return clone.textContent.replace(/\s+/g, ' ').trim() || '-';
        }

        function buildPdfTableFromSummary(sourceId, title) {
            var source = document.getElementById(sourceId);
            if (!source) return null;

            var sourceTable = source.querySelector('table');
            if (!sourceTable) return null;

            var wrapper = document.createElement('div');
            wrapper.style.width = '1100px';
            wrapper.style.padding = '24px';
            wrapper.style.background = '#ffffff';
            wrapper.style.color = '#111827';
            wrapper.style.fontFamily = 'Arial, sans-serif';

            var heading = document.createElement('h2');
            heading.textContent = title;
            heading.style.margin = '0 0 16px';
            heading.style.fontSize = '18px';
            heading.style.textAlign = 'center';
            heading.style.color = '#111827';
            wrapper.appendChild(heading);

            var table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.style.tableLayout = 'fixed';

            Array.from(sourceTable.querySelectorAll('tr')).forEach(function (row) {
                var cells = Array.from(row.cells);
                if (!cells.length) return;

                var tr = document.createElement('tr');
                cells.forEach(function (cell) {
                    var outputCell = document.createElement(cell.tagName.toLowerCase() === 'th' ? 'th' : 'td');
                    outputCell.textContent = cleanPdfText(cell);
                    outputCell.colSpan = cell.colSpan || 1;
                    outputCell.style.border = '1px solid #444';
                    outputCell.style.padding = '8px';
                    outputCell.style.fontSize = '11px';
                    outputCell.style.lineHeight = '1.25';
                    outputCell.style.verticalAlign = 'middle';
                    outputCell.style.wordBreak = 'break-word';

                    if (cell.tagName.toLowerCase() === 'th') {
                        outputCell.style.background = '#253e87';
                        outputCell.style.color = '#ffffff';
                        outputCell.style.textAlign = 'center';
                        outputCell.style.fontWeight = '700';
                    } else if (cell.getAttribute('style') && cell.getAttribute('style').indexOf('background-color:black') !== -1) {
                        outputCell.style.background = '#111827';
                        outputCell.style.color = '#ffeb3b';
                        outputCell.style.textAlign = 'right';
                        outputCell.style.fontWeight = '700';
                    } else {
                        outputCell.style.background = '#ffffff';
                        outputCell.style.color = '#111827';
                    }

                    tr.appendChild(outputCell);
                });
                table.appendChild(tr);
            });

            wrapper.appendChild(table);
            return wrapper;
        }

        async function exportSummaryPdf(sourceId, title, filename) {
            if (!(await ensureHtml2Pdf())) {
                alert("Le module d'export PDF n'est pas charge. Veuillez recharger la page.");
                return;
            }

            var element = document.getElementById(sourceId);
            if (!element) {
                alert("Le resume de paie est introuvable.");
                return;
            }

            // Get the table and temporarily remove the mobile block styling class
            var table = element.querySelector('table');
            var originalClass = "";
            if (table) {
                originalClass = table.className;
                table.className = "table table-bordered";
            }

            // Show logo and footer info if they exist in this block
            var logo = element.querySelector('#logo');
            var merci = element.querySelector('#merci');
            var oldLogoDisplay = logo ? logo.style.display : "";
            var oldMerciDisplay = merci ? merci.style.display : "";
            if (logo) logo.style.display = "block";
            if (merci) merci.style.display = "block";

            // Allow browser to apply the style changes before taking screenshot
            await new Promise(function(resolve) { setTimeout(resolve, 100); });

            var opt = {
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF: { unit: 'mm', format: 'A4', orientation: 'landscape' }
            };

            return html2pdf().set(opt).from(element).save().then(function () {
                // Restore original styles
                if (table) table.className = originalClass;
                if (logo) logo.style.display = oldLogoDisplay;
                if (merci) merci.style.display = oldMerciDisplay;
            }).catch(function (error) {
                console.error(error);
                alert("L'export PDF a echoue. Veuillez reessayer.");
                if (table) table.className = originalClass;
                if (logo) logo.style.display = oldLogoDisplay;
                if (merci) merci.style.display = oldMerciDisplay;
            });
        }

        async function generate() {
            var exportButton = document.getElementById('exportpdf');
            if (exportButton) exportButton.disabled = true;
            await exportSummaryPdf('resume', 'RESUME - SIMULATION DE PAIE', 'SIMULATEUR LIVRE PAIE DC-KNOWING.pdf');
            if (exportButton) exportButton.disabled = false;
            return;

            if (!(await ensureHtml2Pdf())) {
                alert("Le module d'export PDF n'est pas charge. Veuillez recharger la page.");
                if (exportButton) exportButton.disabled = false;
                return;
            }
            if (typeof html2pdf === 'undefined') {
                alert("Le module d'export PDF n'est pas chargÃ©. Veuillez recharger la page.");
                return;
            }

            var element = document.getElementById('resume');
            var logo = document.getElementById('logo');
            var info2 = document.getElementById('merci');
            var oldFooter = document.getElementById('infopied');
            if (oldFooter) oldFooter.remove();
            if (!element) {
                alert("Le rÃ©sumÃ© de paie est introuvable.");
                return;
            }
            info2.style.display = "block";
            logo.style.display = "block";
            //var div = document.createElement('div');
            var div2 = document.createElement('div');
            //div.innerHTML = `<br/>`;
            div2.innerHTML = `<div id="infopied" style="display: block; margin: 20px, 20px, 20px, 50px; padding: 1em;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div id="info" class="ftr-col" style="display: block; margin: 20px, 20px, 20px, 50px; padding: 1em;">
                                                <p>A travers ce simulateur, les entreprises sont sensibilisées à :</p>
                                                <ul>
                                                    <li><b>Payer effectivement les nouveaux salaires nets</b> qui présentent un gain. Ils pourraient connaître une augmentation supplémentaire en raison de la prime d’ancienneté.</li>
                                                    <li><b>Entreprendre une politique d’ajustement des salaires</b> qui subissent une <b>perte</b> en raison de la réforme. Cet ajustement pourrait consister à garantir au moins l’ancien net.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1"><p> <b>Nos services: </b> <br/>RH <br/> Fiscalité <br/> Formation <br/> Leadership <br/> Comptabilité <br/> Management <br/> Entrepreneuriat <br/> Droit des affaires <br/> Recherche de financement </p></div>
                                </div>
                                <div class="row">
                                    <center><p>Société à responsabilité limitée au capital de 1.000.000 F, située à Cocody II Plateaux Angré les Oscars,<br/>
                                    Registre de Commerce N° CI-ABJ-2018-B-31734, Compte Contribuable : 1864699 A, Tel : 27 22 42 14 43 - 07 67 13 19 93,
                                    email :infos@dcknowing.com</p></center>
                                </div>
                            </div>`;
            //document.getElementById('resume').appendChild(div);
            document.getElementById('resume').appendChild(div2);
            var opt = {
                filename: 'SIMULATEUR LIVRE PAIE DC-KNOWING',
                image: { type: 'jpeg', quality: 8 },
                html2canvas: { scale: 4, dpi: 72, letterRendering: true },
                jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape', margin: '1em' }
            };

            var tableau = element.querySelector('table');
            if (tableau) {
                tableau.style.width = '100%';
            }

            html2pdf().set(opt).from(element).save().then(closeScript).catch(function (error) {
                console.error(error);
                alert("L'export PDF a Ã©chouÃ©. Veuillez rÃ©essayer.");
                closeScript();
            });

        }
        async function generateBrutNetSummary() {
            var exportButton = document.getElementById('exportpdfbrutnet');
            if (exportButton) exportButton.disabled = true;
            await exportSummaryPdf('resumeBrutNet', 'RESUME BRUT AU NET RHFLOW', 'RESUME BRUT AU NET RHFLOW.pdf');
            if (exportButton) exportButton.disabled = false;
            return;

            if (!(await ensureHtml2Pdf())) {
                alert("Le module d'export PDF n'est pas charge. Veuillez recharger la page.");
                return;
            }

            if (typeof html2pdf === 'undefined') {
                alert("Le module d'export PDF n'est pas chargÃ©. Veuillez recharger la page.");
                return;
            }
            var element = document.getElementById('resumeBrutNet');
            if (!element) {
                alert("Le rÃ©sumÃ© de paie est introuvable.");
                return;
            }
            var opt = {
                filename: 'RESUME BRUT AU NET RHFLOW',
                image: { type: 'jpeg', quality: 8 },
                html2canvas: { scale: 4, dpi: 72, letterRendering: true },
                jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape', margin: '1em' }
            };

            var tableau = element.querySelector('table');
            if (tableau) {
                tableau.style.width = '100%';
            }

            html2pdf().set(opt).from(element).save().catch(function (error) {
                console.error(error);
                alert("L'export PDF a Ã©chouÃ©. Veuillez rÃ©essayer.");
            });
        }
        function closeScript() {
            /*setTimeout(function () {
                window.open(window.location, '_self').close();
            }, 100);*/

            var logo = document.getElementById('logo');
            var info = document.getElementById('info');
            var info2 = document.getElementById('merci');
            var info3 = document.getElementById('infopied');
            if (logo) logo.style.display = "none";
            if (info) info.style.display = "none";
            if (info2) info2.style.display = "none";
            if (info3) info3.style.display = "none";
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
    <script>
        function cleanCellText(cell) {
            if (!cell) return '';
            return cell.textContent.replace(/\s+/g, ' ').trim();
        }

        function buildSplitTables(tableId, targetId, options) {
            var table = document.getElementById(tableId);
            var target = document.getElementById(targetId);
            if (!table || !target) return;

            var headerRow = table.tHead ? table.tHead.rows[options.headerRowIndex || 0] : null;
            var labels = headerRow ? Array.from(headerRow.cells).map(cleanCellText) : [];
            var hiddenColumns = options.hiddenColumns || [];
            var rows = table.tBodies.length ? Array.from(table.tBodies[0].rows) : Array.from(table.rows).slice(1);
            var chunkSize = options.chunkSize || 12;
            var dataRows = rows.filter(function (row) {
                return Array.from(row.cells).some(function (cell) { return cleanCellText(cell) !== ''; });
            });
            var visibleIndexes = labels.map(function (_, index) { return index; }).filter(function (index) {
                return hiddenColumns.indexOf(index) === -1;
            });

            target.innerHTML = '';

            for (var start = 0; start < visibleIndexes.length; start += chunkSize) {
                var indexes = visibleIndexes.slice(start, start + chunkSize);
                var block = document.createElement('div');
                block.className = 'split-result-block';

                var title = document.createElement('p');
                title.className = 'split-result-title';
                title.textContent = (options.title || 'Resultat') + ' - Tableau ' + ((start / chunkSize) + 1);

                var splitTable = document.createElement('table');
                splitTable.className = 'table table-bordered split-result-table';

                var thead = document.createElement('thead');
                var headTr = document.createElement('tr');
                indexes.forEach(function (index) {
                    var th = document.createElement('th');
                    th.textContent = labels[index] || '-';
                    headTr.appendChild(th);
                });
                thead.appendChild(headTr);

                var tbody = document.createElement('tbody');
                dataRows.forEach(function (row) {
                    var cells = Array.from(row.cells);
                    var tr = document.createElement('tr');
                    indexes.forEach(function (index) {
                        var td = document.createElement('td');
                        td.textContent = cleanCellText(cells[index]) || '-';
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });

                splitTable.appendChild(thead);
                splitTable.appendChild(tbody);
                block.appendChild(title);
                block.appendChild(splitTable);
                target.appendChild(block);
            }
        }

        function refreshPayResultCards() {
            buildSplitTables('tableau', 'paySplitTables', {
                title: 'Resultat de Paie',
                headerRowIndex: 1,
                hiddenColumns: [7, 8, 9, 10],
                chunkSize: 12
            });
        }

        function refreshBrutResultCards() {
            buildSplitTables('tableauBrut', 'brutSplitTables', {
                title: 'Resultat Salaire Brut',
                headerRowIndex: 0,
                hiddenColumns: [],
                chunkSize: 8
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            var payTable = document.getElementById('tableau');
            var brutTable = document.getElementById('tableauBrut');
            var observerConfig = { childList: true, subtree: true, characterData: true };

            if (payTable) {
                new MutationObserver(refreshPayResultCards).observe(payTable, observerConfig);
                refreshPayResultCards();
            }

            if (brutTable) {
                new MutationObserver(refreshBrutResultCards).observe(brutTable, observerConfig);
                refreshBrutResultCards();
            }
        });

        function exportToExcel() {
            const table = document.getElementById('tableau');
            const rows = table.getElementsByTagName('tr');
            const data = [];
            const isOldRegimeColumn = function (index) {
                return index >= 7 && index <= 10;
            };

            /* Ajouter les titres (première ligne)
            const titleRow = rows[0];
            const titleData = [];
            const titleInputs = titleRow.querySelectorAll('th');
    
            for (let i = 0; i < titleInputs.length; i++) {
                titleData.push(titleInputs[i].innerText.trim());
                //titleData.classList.add('h6');
            }
    
            data.push(titleData);*/

            // Ajouter les titres (deuxieme ligne)
            const titleRow2 = rows[1];
            const titleData2 = [];
            const titleInputs2 = titleRow2.querySelectorAll('th');

            for (let i = 0; i < titleInputs2.length; i++) {
                if (titleInputs2[i].classList.contains('old-regime-col')) {
                    continue;
                }
                titleData2.push(titleInputs2[i].textContent.trim());
            }

            data.push(titleData2);


            // Parcourir les lignes du tableau (sauf les titres)
            for (let i = 2; i < rows.length - 1; i++) {
                const row = rows[i];
                const rowData = [];
                const cells = row.cells;

                for (let j = 0; j < cells.length; j++) {
                    if (isOldRegimeColumn(j)) {
                        continue;
                    }
                    const input = cells[j].querySelector('input[type="text"]');
                    if (input) {
                        rowData.push(parseFloat(input.value));
                    }
                }

                data.push(rowData);
            }

            // Ajouter la dernière ligne
            const lastRow = rows[rows.length - 1];
            const lastRowData = [];
            const lastRowCells = lastRow.cells;

            for (let i = 0; i < lastRowCells.length; i++) {
                if (isOldRegimeColumn(i)) {
                    continue;
                }
                lastRowData.push(lastRowCells[i].textContent.trim());
            }

            data.push(lastRowData);

            if (data.length <= 1 || data.every(function (row) {
                return row.every(function (cell) { return cell === '' || cell === null || typeof cell === 'undefined'; });
            })) {
                alert("Aucune donnee a exporter. Veuillez lancer une simulation avant le telechargement.");
                return;
            }

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Feuille1');

            // Créez un lien de téléchargement et déclenchez un clic pour télécharger le fichier
            XLSX.writeFile(wb, 'SIMULATEUR LIVRE PAIE DC-KONWING.xlsx');
        }

        function exportSummaryToExcel(sourceId) {
            const source = document.getElementById(sourceId);
            if (!source) return;
            const table = source.querySelector('table');
            if (!table) return;

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, 'Resume');
            XLSX.writeFile(wb, 'RESUME_PAIE.xlsx');
        }
    </script>
    <script>
        function afficherDiv() {
            var x = document.getElementById('NetBrut');
            var y = document.getElementById('brutaunet');
            var bouton = document.getElementById('boutonChanger');
            var z = document.getElementById('bouton2');

            if (x.style.display === "none") {
                x.style.display = "block";
                y.style.display = "none";
                bouton.innerHTML = "Brut au Net";
            } else {
                x.style.display = "none";
                y.style.display = "block";
                bouton.innerHTML = "Net au Brut";
            }
        }
    </script>

    <script>
        function toggleParagraphe() {
            // Sélectionner le paragraphe par son ID
            var paragraphe = document.getElementById('monParagraphe');

            // Changer le style pour basculer l'affichage du paragraphe
            paragraphe.style.display = (paragraphe.style.display === 'none' || paragraphe.style.display === '') ? 'block' : 'none';
        }
        function toggleParagraphe2() {
            // Sélectionner le paragraphe par son ID
            var paragraphe2 = document.getElementById('monParagraphe2');

            // Changer le style pour basculer l'affichage du paragraphe
            paragraphe2.style.display = (paragraphe2.style.display === 'none' || paragraphe2.style.display === '') ? 'block' : 'none';
        }
        function toggleParagraphe3() {
            // Sélectionner le paragraphe par son ID
            var paragraphe3 = document.getElementById('monParagraphe3');

            // Changer le style pour basculer l'affichage du paragraphe
            paragraphe3.style.display = (paragraphe3.style.display === 'none' || paragraphe3.style.display === '') ? 'block' : 'none';
        }
        function toggleParagraphe4() {
            // Sélectionner le paragraphe par son ID
            var paragraphe4 = document.getElementById('monParagraphe4');

            // Changer le style pour basculer l'affichage du paragraphe
            paragraphe4.style.display = (paragraphe4.style.display === 'none' || paragraphe4.style.display === '') ? 'block' : 'none';
        }
    </script>
    <script>
        var recepnet = 0;
        var cptemp = 0;
        var itab = 0;
        let tt_cnps;
        //var supemp = -1;
        document.getElementById("exportpdf").disabled = true;
        function status(npbrparts) {
            var x = document.getElementById("situation_mat[" + npbrparts + "]").value;
            var y = parseInt(document.getElementById("emp_enfts[" + npbrparts + "]").value) || 0;     // Total enfants à charge
            var z = parseInt(document.getElementById("emp_personnes[" + npbrparts + "]").value) || 0; // Dont enfants infirmes (inclus dans y)

            let nombreDeParts = 1; // Célibataire sans enfant = 1 part

            if (x == '2' && y == '0') {
                // Marié sans enfant → 2 parts
                nombreDeParts = 2;
            } else if (y > 0) {
                if (x == '1' || x == '3') {
                    // Célibataire ou divorcé : 1er enfant → 2 parts, puis +0.5 par enfant
                    nombreDeParts = 2 + Math.max(0, y - 1) * 0.5;
                } else if (x == '2' || x == '4') {
                    // Marié ou veuf : 1er enfant → 2.5 parts, puis +0.5 par enfant
                    nombreDeParts = 2.5 + Math.max(0, y - 1) * 0.5;
                }
                // Enfants infirmes : +1 au lieu de +0.5 → bonus supplémentaire de +0.5 par infirme
                // (les z infirmes sont déjà comptés dans y à raison de +0.5 chacun)
                nombreDeParts += z * 0.5;
            }

            document.getElementById("mystatus[" + npbrparts + "]").value = Math.min(nombreDeParts, 5).toString();
            calculbrut(npbrparts);
        }

        function statusbrut() {
            var x = document.getElementById("situation_mat2").value;
            var y = parseInt(document.getElementById("emp_enfts2").value) || 0;     // Total enfants à charge
            var z = parseInt(document.getElementById("emp_personne2").value) || 0;  // Dont enfants infirmes (inclus dans y)

            let nombreDeParts = 1; // Célibataire sans enfant = 1 part

            if (x == '2' && y == '0') {
                nombreDeParts = 2;
            } else if (y > 0) {
                if (x == '1' || x == '3') {
                    nombreDeParts = 2 + Math.max(0, y - 1) * 0.5;
                } else if (x == '2' || x == '4') {
                    nombreDeParts = 2.5 + Math.max(0, y - 1) * 0.5;
                }
                // Enfants infirmes : bonus +0.5 supplémentaire par infirme
                nombreDeParts += z * 0.5;
            }

            document.getElementById("part_igr").value = Math.min(nombreDeParts, 5).toString();
            //calculbrut(npbrparts);
        }

        function Ajout() {

            // Afficher les div en fonction du nombre
            cptemp++;

            var div = document.createElement('div');
            var div2 = document.createElement('div2');
            var div3 = document.createElement('div3');
            //var div4 = document.createElement('div4');
            div2.classList.add('row');
            div3.classList.add('row');
            //div4.classList.add('row');
            div.innerHTML = `<div class="row">
                            <div class="col-md-1">
                                <b>Employé  ` + cptemp + `:</b>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="local[`+ cptemp + `]" value="local" name="local[` + cptemp + `]" class="form-check-input" checked>
                                    <label id="label_local[`+ cptemp + `]" class="form-label">Local</label>
                                </div>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="expat[`+ cptemp + `]" value="expat" name="local[` + cptemp + `]" class="form-check-input">
                                    <label id="label_expat[`+ cptemp + `]" class="form-label">Expatrié</label>
                                </div>
                            </div>
                        </div>`;
            div2.innerHTML = `<div class="form-group col-md-3">
                            <label id="label_emp_base[`+ cptemp + `]" class="col-form-label">Salaire brut total (Y compris toutes les primes)</label>
                            <input type="number" id="emp_mont_base[`+ cptemp + `]" class="form-control" name="emp_mont_base[` + cptemp + `]" min="75000" placeholder="Entrez le salaire brut total"  oninput="calculbrut(` + cptemp + `)" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_situation_mat" class="col-form-label">Situation matrimoniale</label>
                            <select type="text" name="situation_mat[`+ cptemp + `]" class="form-control" id="situation_mat[` + cptemp + `]" onchange="status(` + cptemp + `)">
                                <option value="1">Célibataire</option>
                                <option value="2">Marié(e)</option>
                                <option value="3">Divorcé(e)</option>
                                <option value="4">Veuf(ve)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_mont[`+ cptemp + `]" class="col-form-label">Salaire Brut Imposable (Fiscal)</label>
                            <input type="number" id="emp_mont[`+ cptemp + `]" class="form-control" name="emp_mont[` + cptemp + `]" min="75000" placeholder="Entrez le salaire brut imposable" oninput="calculbrut(` + cptemp + `)">
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_brut_cnps[`+ cptemp + `]" class="col-form-label">Salaire brut Social (CNPS)</label>
                            <input type="number" id="emp_mont_cnps[`+ cptemp + `]" class="form-control" name="emp_mont_cnps[` + cptemp + `]" min="75000" max="3375000" placeholder="Entrez le salaire brut social" oninput="calculbrut(` + cptemp + `)">
                        </div>`;
            div3.innerHTML = `<div class="form-group col-md-3">
                            <label id="label_emp_enfts" class="col-form-label">Enfants à charge</label>
                            <select type="text" name="emp_enfts[`+ cptemp + `]" class="form-control" id="emp_enfts[` + cptemp + `]" onchange ="status(` + cptemp + `)">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_personnes[`+ cptemp + `]" class="col-form-label">Personnes infirmes à charge</label>
                            <select type="text" name="emp_personnes[`+ cptemp + `]" class="form-control" id="emp_personnes[` + cptemp + `]" onchange="status(` + cptemp + `)">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_parts[`+ cptemp + `]" class="col-form-label">Nombre de parts</label>
                            <input type="text" name="emp_parts" class= "form-control" id="mystatus[`+ cptemp + `]" value="1" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_cmu_pers[`+ cptemp + `]" class="col-form-label">Bénéficiaires CMU</label>
                            <select type="text" name="cmu_pers[`+ cptemp + `]" class="form-control" id="cmu_pers[` + cptemp + `]" onchange="calculbrut(` + cptemp + `)">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <h5 align="right">
                            <span id="netold[`+ cptemp + `]" style="color:#fff"> </span>
                        </h5>
                        <hr>
                        `;
            document.getElementById('form').appendChild(div);
            document.getElementById('form').appendChild(div2);
            document.getElementById('form').appendChild(div3);
            if (cptemp > 1) {
                calculbrut(cptemp - 1);
                document.getElementById("retrait").disabled = false;
                document.getElementById("retrait").hidden = false;
            }
            document.getElementById("addSig").disabled = false;

        }

        function Ajout2() {

            cptemp++;
            // Afficher les div en fonction du nombre

            var div = document.createElement('div');
            var div2 = document.createElement('div2');
            var div3 = document.createElement('div3');
            //var div4 = document.createElement('div4');
            div2.classList.add('row');
            div3.classList.add('row');
            //div4.classList.add('row');
            div.innerHTML = `<div class="row">
                            <div class="col-md-1">
                                <b>Employé  ` + cptemp + `:</b>
                            </div>
                            <div class="col-md-8">
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="local[`+ cptemp + `]" value="local" name="local[` + cptemp + `]" class="form-check-input" checked>
                                    <label id="label_local[`+ cptemp + `]" class="form-label">Local</label>
                                </div>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="expat[`+ cptemp + `]" value="expat" name="local[` + cptemp + `]" class="form-check-input">
                                    <label id="label_expat[`+ cptemp + `]" class="form-label">Expatrié</label>
                                </div>
                            </div>
                        </div>`;
            div2.innerHTML = `<div class="form-group col-md-4">
                            <label id="label_emp_base[`+ cptemp + `]" class="col-form-label">Salaire brut total (Y compris toutes les primes)</label>
                            <input type="number" id="emp_mont_base[`+ cptemp + `]" class="form-control" name="emp_mont_base[` + cptemp + `]" min="75000" placeholder="Entrez le salaire brut total"  oninput="calculbrut(` + cptemp + `)" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label id="label_situation_mat" class="col-form-label">Situation matrimoniale</label>
                            <select type="text" name="situation_mat[`+ cptemp + `]" class="form-control" id="situation_mat[` + cptemp + `]" onchange="status(` + cptemp + `)">
                                <option value="1">Célibataire</option>
                                <option value="2">Marié(e)</option>
                                <option value="3">Divorcé(e)</option>
                                <option value="4">Veuf(ve)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_mont[`+ cptemp + `]" class="col-form-label">Salaire Brut Imposable (Fiscal)</label>
                            <input type="number" id="emp_mont[`+ cptemp + `]" class="form-control" name="emp_mont[` + cptemp + `]" min="75000" placeholder="Entrez le salaire brut imposable" oninput="calculbrut(` + cptemp + `)">
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_brut_cnps[`+ cptemp + `]" class="col-form-label">Salaire brut Social (CNPS)</label>
                            <input type="number" id="emp_mont_cnps[`+ cptemp + `]" class="form-control" name="emp_mont_cnps[` + cptemp + `]" min="75000" max="3375000" placeholder="Entrez le salaire brut social" oninput="calculbrut(` + cptemp + `)">
                        </div>`;
            div3.innerHTML = `<div class="form-group col-md-3">
                            <label id="label_emp_enfts" class="col-form-label">Enfants à charge</label>
                            <select type="text" name="emp_enfts[`+ cptemp + `]" class="form-control" id="emp_enfts[` + cptemp + `]" onchange ="status(` + cptemp + `)">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_personnes[`+ cptemp + `]" class="col-form-label">Personnes infirmes à charge</label>
                            <select type="text" name="emp_personnes[`+ cptemp + `]" class="form-control" id="emp_personnes[` + cptemp + `]" onchange="status(` + cptemp + `)">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label id="label_emp_parts[`+ cptemp + `]" class="col-form-label">Nombre de parts</label>
                            <input type="text" name="emp_parts" class= "form-control" id="mystatus[`+ cptemp + `]" value="1" onchange="calculbrut(` + cptemp + `)" readonly>
                            </div>
                        <div class="form-group col-md-3">
                            <label id="label_cmu_pers[`+ cptemp + `]" class="col-form-label">Bénéficiaires CMU</label>
                            <select type="text" name="cmu_pers[`+ cptemp + `]" class="form-control" id="cmu_pers[` + cptemp + `]" onchange="calculbrut(` + cptemp + `)">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <h5 align="right">
                            <span id="netold[`+ cptemp + `]" style="color:#fff"> </span>
                        </h5>
                        <hr>
                        `;
            document.getElementById('form').appendChild(div);
            document.getElementById('form').appendChild(div2);
            document.getElementById('form').appendChild(div3);
            if (cptemp > 1) {
                calculbrut(cptemp - 1);
                document.getElementById("retrait").disabled = false;
                document.getElementById("retrait").hidden = false;
            }
            document.getElementById("simulerimport").disabled = true;
        }

        function Resultat() {

            var cpte = cptemp;
            calculbrut(cptemp);
            // Récupérer le tableau et le corps du tableau
            var tableau = document.getElementById('tableau');
            var corpsTableau = tableau.getElementsByTagName('tbody')[0];
            var totalColonnes = Array.from({ length: tableau.rows[0].cells.length }).fill(0);
            var act = document.getElementById("atravail").value;
            // Assurez-vous que tous les champs requis sont remplis
            var champsRemplis = true;

            for (var j = 1; j <= cpte; j++) {
                // Récupérer les valeurs des champs de saisie
                var mtsalary = document.getElementById("emp_mont_base[" + j + "]").value;
                var basesalary = document.getElementById("emp_mont[" + j + "]").value;
                var brut_cnps = document.getElementById("emp_mont_cnps[" + j + "]").value;

                // Vérifier si les champs sont remplis
                if (mtsalary === '' || basesalary === '' || brut_cnps === '') {
                    champsRemplis = false;
                    var confirmation = confirm('Veuillez remplir tous les champs pour l\'employé ' + j + '. "OK" pour continuer "Annuler" pour Supprimer');
                    if (confirmation) {
                        break;  // Sortir de la boucle si un champ est vide
                    } else {
                        Retrait();
                        break;  // Sortir de la boucle si un champ est vide
                    }
                }

                /* Vérifier si la valeur est un nombre*/
                if (mtsalary < 75000) {
                    alert('Le montant du Salaire brut total ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + j + '.');
                    break;  // Sortir de la boucle si un champ est vide
                }
                if (basesalary < 75000) {
                    alert('Le montant du Salaire brut fiscal ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + j + '.');
                    break;  // Sortir de la boucle si un champ est vide
                }
                if (brut_cnps < 75000) {
                    alert('Le montant du Salaire brut Social (CNPS) ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + j + '.');
                    break;  // Sortir de la boucle si un champ est vide
                } else if (brut_cnps > 3375000) {
                    alert('Le montant du Salaire brut Social (CNPS) ne doit pas excéder 3 375 000 FCFA pour l\'employé ' + j + '.');
                    break;  // Sortir de la boucle si un champ est vide
                }
            }

            // Si tous les champs sont remplis, exécuter la boucle
            if (champsRemplis) {
                for (var i = 1; i <= cpte; i++) {
                    var mtsalary = document.getElementById("emp_mont_base[" + i + "]").value;
                    var basesalary = document.getElementById("emp_mont[" + i + "]").value;
                    var brut_cnps = document.getElementById("emp_mont_cnps[" + i + "]").value;

                    if ((mtsalary >= 75000) && (basesalary >= 75000) && (brut_cnps >= 75000) && (brut_cnps < 3375001)) {

                        // Créer une nouvelle ligne et des cellules
                        var nouvelleLigne = corpsTableau.insertRow(-1);
                        var cellule1 = nouvelleLigne.insertCell(0);
                        var cellule2 = nouvelleLigne.insertCell(1);
                        var cellule3 = nouvelleLigne.insertCell(2);
                        var cellule4 = nouvelleLigne.insertCell(3);
                        var cellule5 = nouvelleLigne.insertCell(4);
                        var cellule6 = nouvelleLigne.insertCell(5);
                        var cellule7 = nouvelleLigne.insertCell(6);
                        var cellule8 = nouvelleLigne.insertCell(7);
                        var cellule9 = nouvelleLigne.insertCell(8);
                        var cellule10 = nouvelleLigne.insertCell(9);
                        var cellule11 = nouvelleLigne.insertCell(10);
                        var cellule12 = nouvelleLigne.insertCell(11);
                        var cellule13 = nouvelleLigne.insertCell(12);
                        var cellule14 = nouvelleLigne.insertCell(13);
                        var cellule15 = nouvelleLigne.insertCell(14);
                        var cellule16 = nouvelleLigne.insertCell(15);
                        var cellule17 = nouvelleLigne.insertCell(16);
                        var cellule18 = nouvelleLigne.insertCell(17);
                        var cellule19 = nouvelleLigne.insertCell(18);
                        var cellule20 = nouvelleLigne.insertCell(19);
                        var cellule21 = nouvelleLigne.insertCell(20);
                        var cellule22 = nouvelleLigne.insertCell(21);
                        var cellule23 = nouvelleLigne.insertCell(22);
                        var cellule24 = nouvelleLigne.insertCell(23);
                        var cellule25 = nouvelleLigne.insertCell(24);
                        var cellule26 = nouvelleLigne.insertCell(25);
                        var cellule27 = nouvelleLigne.insertCell(26);
                        var cellule28 = nouvelleLigne.insertCell(27);

                        //calculons les charges des employés
                        var cnps = 0;
                        var local = document.getElementById("local[" + i + "]").checked;
                        var expat = document.getElementById("expat[" + i + "]").checked;
                        var cmu = document.getElementById("cmu_pers[" + i + "]").value;
                        var brut = document.getElementById("emp_mont[" + i + "]").value;
                        var cn = (document.getElementById("emp_mont[" + i + "]").value * 80) / 100;
                        var r = 0;
                        var resultcn = 0;
                        var resultcnps;
                        var brut_total = 0;
                        var igr = 0;
                        var q = 0;
                        var n = document.getElementById("mystatus[" + i + "]").value;
                        var resultits = 0;
                        var resultigr = 0;

                        var resultcmu = 0;
                        brut_total = parseFloat(mtsalary);

                        var its = ((brut * 1.2) / 100);
                        resultits = Math.round(its);

                        if (cn >= 0 && cn <= 50000) {
                            var totaux = (cn * 0) / 100;
                            resultcn = Math.round(totaux);
                        } else if (cn > 50000 && cn <= 130000) {
                            var montcn = cn - 50000;
                            var total = (((50000 * 0) / 100) + ((montcn * 1.5) / 100));
                            r = (((brut * 80) / 100) - (its + total)) * 85 / 100;
                            resultcn = Math.round(total);
                        } else if (cn > 130000 && cn <= 200000) {
                            var montcn1 = cn - 130000;
                            var total1 = (((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((montcn1 * 5) / 100));
                            r = (((brut * 80) / 100) - (its + total1)) * 85 / 100;
                            resultcn = Math.round(total1);
                        } else if (cn > 200000) {
                            var montcn2 = cn - 200000;
                            var total2 = ((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((70000 * 5) / 100) + ((montcn2 * 10) / 100);
                            r = (((brut * 80) / 100) - (its + total2)) * 85 / 100;
                            resultcn = Math.round(total2);
                        }
                        q = r / n;
                        if (q < 25000) {
                            igr = 0;
                            resultigr = Math.round(igr);
                        } else if (q > 25000 && q < 45583) {
                            igr = (r * (10 / 110)) - (2273 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 45584 && q < 81583) {
                            igr = (r * (15 / 115)) - (4076 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 81584 && q < 126883) {
                            igr = (r * (20 / 120)) - (7031 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 126884 && q < 220383) {
                            igr = (r * (25 / 125)) - (11250 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 220384 && q < 389083) {
                            igr = (r * (35 / 135)) - (24306 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 389084 && q < 842166) {
                            igr = (r * (45 / 145)) - (44181 * n);
                            resultigr = Math.round(igr);
                        } else if (q > 842167) {
                            igr = (r * (60 / 160)) - (98633 * n);
                            resultigr = Math.round(igr);
                        }
                        // CMU : 500 FCFA/personne salarié, 500 FCFA/personne employeur (Art. CGI)
                        // Pas de barème progressif : le taux est fixe quel que soit le nombre de bénéficiaires
                        coticmu = cmu * 500;  // Part salarié
                        coticmuemp = cmu * 500;  // Part employeur
                        var tauxact = 0;
                        cnps = (brut_cnps * 6.3) / 100;
                        cnpsemp = ((brut_cnps * 7.7) / 100);
                        if (act == '0,03') {
                            tauxact = 75000 * 0.03;
                        } else if (act == '0,02') {
                            tauxact = 75000 * 0.02;
                        } else if (act == '0,04') {
                            tauxact = 75000 * 0.04;
                        } else if (act == '0,05') {
                            tauxact = 75000 * 0.05;
                        }
                        tt_cnpsemp = cnpsemp + tauxact + ((75000 * 5.75) / 100);
                        //alert(tauxact);
                        tax1 = (brut * 1.2) / 100;
                        tax2 = (brut * 11.5) / 100;
                        tax3 = (brut * 1.6) / 100;
                        //tax4 = (brut*0.6)/100;
                        tt_cnps = (brut_cnps * 6.3) / 100;
                        resultcmu = Math.round(coticmu);
                        resultcnps = Math.round(cnps);
                        resulttax1 = Math.round(tax1);
                        resulttax2 = Math.round(tax2);
                        resulttax3 = Math.round(tax3);
                        if (local) {
                            parpatronal = tt_cnpsemp + coticmuemp + tax1 + tax3;
                        } else {
                            parpatronal = tt_cnpsemp + coticmuemp + tax1 + tax2 + tax3;
                        }
                        //resulttax4 = Math.round(tax4).toLocaleString();
                        if (brut >= 0 && brut <= 75000) {
                            var tot = (brut * 0) / 100;
                            resultimpricf = Math.round(tot);
                        } else if (brut > 75000 && brut <= 240000) {
                            var mt1 = brut - 75000;
                            var tot1 = (((75000 * 0) / 100) + ((mt1 * 16) / 100));
                            //r = (((brut*80)/100)-(its+total))*85/100;
                            resultimpricf = Math.round(tot1);
                        } else if (brut > 240000 && brut <= 800000) {
                            var mt2 = brut - 240000;
                            var tot2 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((mt2 * 21) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot2);
                        } else if (brut > 800000 && brut <= 2400000) {
                            var mt3 = brut - 800000;
                            var tot3 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((mt3 * 24) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot3);
                        } else if (brut > 2400000 && brut <= 8000000) {
                            var mt4 = brut - 2400000;
                            var tot4 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((mt4 * 28) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot4);
                        } else if (brut > 8000000) {
                            var mt5 = brut - 8000000;
                            var tot5 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((5600000 * 28) / 100) + ((mt5 * 32) / 100));
                            //r = (((brut*80)/100)-(its+total2))*85/100;
                            resultimpricf = Math.round(tot5);
                        }

                        // RICF : barème fixe en FCFA selon l'article 119 bis CGI (NE PAS utiliser des taux %)
                        var nbre = parseFloat(document.getElementById("mystatus[" + i + "]").value) || 1;
                        var ricfTable = { 1: 0, 1.5: 5500, 2: 11000, 2.5: 16500, 3: 22000, 3.5: 27500, 4: 33000, 4.5: 38500, 5: 44000 };
                        resultricf = 0;
                        for (var rk in ricfTable) {
                            if (Math.abs(nbre - parseFloat(rk)) < 0.01) { resultricf = ricfTable[rk]; break; }
                        }
                        if (nbre >= 5) resultricf = 44000;
                        //var prime = brut_total - brut;
                        var impots = (resultits) + (resultcn) + (resultigr) + (resultcnps) + (resultcmu);

                        var retenue1 = (resultits) + (resultcn) + (resultigr);
                        var retenue2 = (resultimpricf) - (resultricf);
                        if (retenue2 > 0) {
                            retenue3 = ((resultits) + (resultcn) + (resultigr)) - ((resultimpricf) - (resultricf));
                            impots2 = (resultimpricf) - (resultricf) + (resultcnps) + (resultcmu);
                        } else {
                            retenue3 = ((resultits) + (resultcn) + (resultigr));
                            impots2 = (resultcnps) + (resultcmu);
                        }
                        //var
                        var salarynet1 = (brut_total - impots);
                        recepnet = salarynet1;
                        var salarynet2 = (brut_total - impots2);
                        var variant = salarynet2 - salarynet1;
                        var coutsal = parseInt(parpatronal) + parseInt(brut_total);
                        //celluletire.colSpan = 24;
                        //celluletire.innerHTML = '<span><b>PERTE</b></span>';
                        cellule1.innerHTML = 'Emp ' + i + '<input type="text" value="' + i + '" hidden>';
                        cellule2.innerHTML = (local ? 'Local' : 'Expatrié') + '<input type="text" value="0" hidden>';
                        cellule3.innerHTML = n + '<input type="text" value="' + n + '" hidden>';
                        cellule4.innerHTML = '<span>' + brut_total.toLocaleString() + ' FCFA </span> <input type="text" value="' + brut_total + '" hidden>';
                        cellule5.innerHTML = '<span>' + parseFloat(brut).toLocaleString() + ' FCFA </span> <input type="text" value="' + parseFloat(brut) + '" hidden>';
                        cellule6.innerHTML = '<span>' + parseFloat(brut_cnps).toLocaleString() + ' FCFA </span> <input type="text" value="' + parseFloat(brut_cnps) + '" hidden>';
                        cellule7.innerHTML = '<span>' + cmu + '</span> <input type="text" value="0" hidden>';
                        cellule8.innerHTML = '<span>' + resultits.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultits + '" hidden>';
                        cellule9.innerHTML = '<span>' + resultcn.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultcn + '" hidden>';
                        cellule10.innerHTML = '<span>' + resultigr.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultigr + '" hidden>';
                        cellule11.innerHTML = '<span>' + retenue1.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue1 + '" hidden>';
                        cellule12.innerHTML = '<span>' + resultimpricf.toLocaleString() + ' FCFA</span> <input type="text" value="' + resultimpricf + '" hidden>';
                        cellule13.innerHTML = '<span>' + resultricf.toLocaleString() + ' FCFA</span> <input type="text" value="' + resultricf + '" hidden>';
                        if (retenue2 > 0) {
                            cellule14.innerHTML = '<span>' + retenue2.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue2 + '" hidden>';
                        } else {
                            cellule14.innerHTML = '<span>' + 0 + ' FCFA</span> <input type="text" value="' + 0 + '" hidden>';
                        }
                        cellule15.innerHTML = '<span>' + retenue3.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue3 + '" hidden>';
                        cellule16.innerHTML = '<span>' + Math.round(resultcnps).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(tt_cnps) + '" hidden>';
                        cellule17.innerHTML = '<span>' + Math.round(resultcmu).toLocaleString() + ' FCFA </span> <input type="text" value="' + resultcmu + '" hidden>';
                        cellule18.innerHTML = '<span>' + salarynet1.toLocaleString() + ' FCFA </span> <input type="text" value="' + salarynet1 + '" hidden>';
                        cellule19.innerHTML = '<span>' + salarynet2.toLocaleString() + ' FCFA </span> <input type="text" value="' + salarynet2 + '" hidden>';
                        cellule20.innerHTML = '<span>' + variant.toLocaleString() + ' FCFA</span> <input type="text" value="' + variant + '" hidden>';
                        if (variant > 0) {
                            cellule21.innerHTML = 'Gains de : ' + variant.toLocaleString() + ' FCFA <input type="text" value="' + variant + '" hidden>';
                        } else {
                            cellule21.innerHTML = 'Perte de : ' + variant.toLocaleString() + ' FCFA <input type="text" value="' + variant + '" hidden>';
                        }

                        cellule22.innerHTML = '<span>' + Math.round(resulttax1).toLocaleString() + ' FCFA </span> <input type="text" value="' + resulttax1 + '" hidden>';
                        if (local) {
                            cellule23.innerHTML = '- <input type="text" value="0" hidden>';
                        } else {
                            cellule23.innerHTML = '<span>' + Math.round(resulttax2).toLocaleString() + ' FCFA </span> <input type="text" value="' + Math.round(resulttax2) + '" hidden>';
                        }
                        cellule24.innerHTML = '<span>' + Math.round(resulttax3).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(resulttax3) + '" hidden>';
                        cellule25.innerHTML = '<span>' + Math.round(tt_cnpsemp).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(tt_cnpsemp) + '" hidden>';
                        cellule26.innerHTML = '<span>' + coticmuemp.toLocaleString() + ' FCFA</span> <input type="text" value="' + coticmuemp + '" hidden>';
                        cellule27.innerHTML = '<span>' + Math.round(parpatronal).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(parpatronal) + '" hidden>';
                        cellule28.innerHTML = '<span>' + Math.round(coutsal).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(coutsal) + '" hidden>';
                        /*var
                        var tt_varientnet =
                        /var autreprime = parseInt(prim_nonimp)+parseInt(prim_fisc)+parseInt(prim_social);
                        // Remplir les cellules avec des valeurs*/
                    }
                }
            }
            if ((mtsalary >= 75000) && (basesalary >= 75000) && (brut_cnps >= 75000) && (brut_cnps < 3375001)) {
                // Déclarer totalColonnes en dehors de la boucle
                var totalColonnes = Array.from({ length: tableau.rows[1].cells.length }).fill(0);
                var tt_gains = 0;
                var tt_perte = 0;
                for (var k = 1; k < tableau.rows.length; k++) {
                    for (var j = 0; j < tableau.rows[k].cells.length; j++) {
                        // Utiliser parseFloat pour convertir la valeur en nombre
                        var element = tableau.rows[k].cells[j].querySelector('input[type="text"]');

                        if (element && element.value.trim() !== '' && !isNaN(element.value)) {
                            // Ajouter la valeur à totalColonnes
                            totalColonnes[j] += parseFloat(element.value);
                        }

                        if (j == 19) {
                            var cellule = tableau.rows[k].cells[j].querySelector('input[type="text"]');
                            // Assurez-vous que cellule n'est pas null avant de comparer
                            if (cellule) {
                                var valeurCellule = parseFloat(cellule.value);

                                if (valeurCellule > 0) {
                                    tt_gains += valeurCellule;
                                } else if (valeurCellule < 0) {
                                    tt_perte += valeurCellule;
                                }
                            }
                        }
                    }
                }

                // Afficher le total dans la dernière ligne
                var derniereLigne = tableau.insertRow(-1);

                var celluleTotal = derniereLigne.insertCell(0);
                var celluleTotal1 = derniereLigne.insertCell(1);
                celluleTotal.innerHTML = '<b> TOTAL :</b> <strong>' + cptemp + '</strong>';
                celluleTotal1.innerHTML = '<strong><center> - </center></strong>';
                for (var q = 2; q < totalColonnes.length; q++) {
                    var celluleTotal = derniereLigne.insertCell(q);
                    celluleTotal.innerHTML = '<strong>' + totalColonnes[q].toLocaleString() + '</strong> <b>FCFA</b>';
                }


                var totalCelluleBrut = totalColonnes[3];
                var totalCelluleSbi = totalColonnes[4];
                var totalCelluleSbs = totalColonnes[5];
                var totalCellule7 = totalColonnes[10];
                var totalCellule10 = totalColonnes[13];
                var totalCelluleCnps = totalColonnes[15];
                var totalCelluleCmu = totalColonnes[16];
                var totalCellule15 = totalColonnes[18];
                var totalCellule16 = totalColonnes[19];
                var totalCellule23 = totalColonnes[26];
                var totalCellule24 = totalColonnes[27];
                document.getElementById("totalemp").innerHTML = cptemp;
                document.getElementById("totalimpan").innerHTML = totalCellule7.toLocaleString() || 0;
                document.getElementById("totalimpnv").innerHTML = totalCellule10.toLocaleString() || 0;
                document.getElementById("totalgainsal").innerHTML = tt_gains.toLocaleString() || 0;
                document.getElementById("totalpertsal").innerHTML = tt_perte.toLocaleString() || 0;
                document.getElementById("totalnet").innerHTML = totalCellule15.toLocaleString() || 0;
                document.getElementById("totalvarmasse").innerHTML = totalCellule16.toLocaleString() || 0;
                document.getElementById("totalpatron").innerHTML = totalCellule23.toLocaleString() || 0;
                document.getElementById("totalcout").innerHTML = Math.round(totalCellule24).toLocaleString() || 0;
                document.getElementById("totalbrut").innerHTML = totalCelluleBrut.toLocaleString() || 0;
                document.getElementById("totalsbiresume").innerHTML = totalCelluleSbi.toLocaleString() || 0;
                document.getElementById("totalsbsresume").innerHTML = totalCelluleSbs.toLocaleString() || 0;
                document.getElementById("totalcnpssal").innerHTML = totalCelluleCnps.toLocaleString() || 0;
                document.getElementById("totalcmusal").innerHTML = totalCelluleCmu.toLocaleString() || 0;
                document.getElementById("totalretenuessal").innerHTML = Math.round(totalCellule10 + totalCelluleCnps + totalCelluleCmu).toLocaleString() || 0;

                document.getElementById("ajout").disabled = true;
                document.getElementById("retrait").disabled = true;
                document.getElementById("retrait").hidden = true;
                document.getElementById("update").disabled = false;
                document.getElementById("addSig").disabled = true;
                document.getElementById("exportpdf").disabled = false;
                document.getElementById("exporterExcel").disabled = false;
            }
        }

        function ResultatNet() {

            var cpte = cptemp;
            var itab = cptemp;

            // for (var i = 1; i <= cpte; i++) {

            // Récupérer la valeur du champ de saisie
            const mtsalary = document.getElementById("emp_mont_net").value;

            // Vérifier si la valeur est un nombre
            if (mtsalary == '') {
                alert('Veuillez entrer un montant valide pour l\'employé ' + i + '.');
            } else {

                //calculons les charges des employés
                var cmu = document.getElementById("cmu_pers_net").value;
                var brut = document.getElementById("emp_mont_net").value;
                var n = document.getElementById("mystatus2").value;
                var tp = document.getElementById("emp_tpt").value;
                var resultcnps = 0;
                var resultcmu = 0;
                brut_total = 0;

                if (cmu < 7) {
                    resultcmu = (cmu * 500);
                } else {
                    resultcmu = 3000 + ((cmu - 6) * 1000);
                }

                if (tp > 30000) {
                    treport = tp - 30000;
                    brut = parseFloat(brut) + parseFloat(treport);
                } else {
                    treport = tp;
                }

                cnps = (brut * 6.3) / 100;
                resultcnps = cnps;

                if (brut >= 0 && brut <= 75000) {
                    var tot = (brut * 0) / 100;
                    resultimpricf = Math.round(tot);
                } else if (brut > 75000 && brut <= 240000) {
                    var mt1 = brut - 75000;
                    var tot1 = (((75000 * 0) / 100) + ((mt1 * 16) / 100));
                    //r = (((brut*80)/100)-(its+total))*85/100;
                    resultimpricf = Math.round(tot1);
                } else if (brut > 240000 && brut <= 800000) {
                    var mt2 = brut - 240000;
                    var tot2 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((mt2 * 21) / 100));
                    //r = (((brut*80)/100)-(its+total1))*85/100;
                    resultimpricf = Math.round(tot2);
                } else if (brut > 800000 && brut <= 2400000) {
                    var mt3 = brut - 800000;
                    var tot3 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((mt3 * 24) / 100));
                    //r = (((brut*80)/100)-(its+total1))*85/100;
                    resultimpricf = Math.round(tot3);
                } else if (brut > 2400000 && brut <= 8000000) {
                    var mt4 = brut - 2400000;
                    var tot4 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((mt4 * 28) / 100));
                    //r = (((brut*80)/100)-(its+total1))*85/100;
                    resultimpricf = Math.round(tot4);
                } else if (brut > 8000000) {
                    var mt5 = brut - 8000000;
                    var tot5 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((5600000 * 28) / 100) + ((mt5 * 32) / 100));
                    //r = (((brut*80)/100)-(its+total2))*85/100;
                    resultimpricf = Math.round(tot5);
                }

                // RICF : barème fixe en FCFA selon l'article 119 bis CGI
                var nbre = parseFloat(document.getElementById("mystatus2").value) || 1;
                var ricfTable2 = { 1: 0, 1.5: 5500, 2: 11000, 2.5: 16500, 3: 22000, 3.5: 27500, 4: 33000, 4.5: 38500, 5: 44000 };
                resultricf = 0;
                for (var rk2 in ricfTable2) {
                    if (Math.abs(nbre - parseFloat(rk2)) < 0.01) { resultricf = ricfTable2[rk2]; break; }
                }
                if (nbre >= 5) resultricf = 44000;

                var impots2 = (resultimpricf) - (resultricf);
                brut_total = parseFloat(brut) + parseFloat(resultcnps) + parseFloat(resultcmu) + parseFloat(impots2) + parseFloat(tp);
                document.getElementById("resultatnet").innerHTML = brut.toLocaleString() + ' FCFA';
                document.getElementById("resultattp").innerHTML = tp.toLocaleString() + ' FCFA';
                document.getElementById("resultatcmu").innerHTML = resultcmu.toLocaleString() + ' FCFA';
                document.getElementById("resultatpart").innerHTML = nbre.toLocaleString() + ' <=> ' + resultricf + ' FCFA';
                document.getElementById("resultatbrut").innerHTML = brut_total.toLocaleString() + ' FCFA';
            }

            //document.getElementById("ajout").disabled = true;
            document.getElementById("submetnet").disabled = true;
            //document.getElementById("exporterExcel").disabled = false ;
        }

        function updatetab() {
            var tableau = document.getElementById('tableau');
            // Vérifiez si le tableau existe
            if (tableau) {
                // Parcourez les lignes du tableau
                for (var i = 2; i < tableau.rows.length; i++) {
                    // Parcourez les cellules de chaque ligne
                    for (var j = 0; j < tableau.rows[i].cells.length; j++) {
                        // Videz le contenu de chaque cellule
                        tableau.rows[i].cells[j].innerHTML = '';
                    }
                }
            } else {
                console.error('Tableau non trouvé. Veuillez vérifier l\'ID du tableau.');
            }
            // Vérifiez si le tableau existe
            if (tableau) {
                // Parcourez les lignes du tableau en partant de la fin
                for (var i = tableau.rows.length - 1; i >= 0; i--) {
                    // Vérifiez si toutes les cellules de la ligne sont vides
                    var ligneVide = true;
                    for (var j = 0; j < tableau.rows[i].cells.length; j++) {
                        if (tableau.rows[i].cells[j].innerHTML.trim() !== '') {
                            ligneVide = false;
                            break;
                        }
                    }

                    // Si la ligne est vide, supprimez-la
                    if (ligneVide) {
                        tableau.deleteRow(i);
                    }
                }
            }
            Resultat();
            document.getElementById("retrait").disabled = true;
            document.getElementById("retrait").hidden = true;
            document.getElementById("ajout").hidden = true;
            document.getElementById("ajout2").hidden = false;
            document.getElementById("addSig").disabled = true;
        }

        function resetNet() {
            // Recharge la page en ignorant le cache
            document.getElementById("cmu_pers_net").value = '';
            document.getElementById("emp_mont_net").value = '';
            document.getElementById("mystatus2").value = '';
            document.getElementById("emp_tpt").value = '';
            document.getElementById("submetnet").disabled = false;
        }

        function reset() {
            // Recharge la page en ignorant le cache
            window.location.reload(true);
        }

        function afficherChampsTexte(checkbox) {
            // Récupérer la case à cocher et le conteneur des champs texte
            var caseACocher = document.getElementById('nonimposble[' + checkbox + ']');
            var caseACocher1 = document.getElementById('sociales[' + checkbox + ']');
            var caseACocher2 = document.getElementById('fiscales[' + checkbox + ']');
            var champsTexteContainer = document.getElementById('div_prim_social[' + checkbox + ']');
            var champsTexteContainer1 = document.getElementById('div_prim_fiscal[' + checkbox + ']');
            var champsTexteContainer2 = document.getElementById('div_prim_nonimp[' + checkbox + ']');
            var prim_social = document.getElementById('prim_social[' + checkbox + ']').value;
            var prim_fisc = document.getElementById('prim_fisc[' + checkbox + ']').value;
            //var prim_nonimp = document.getElementById('prim_nonimp[' + checkbox + ']').value;

            // Modifier la visibilité des champs texte en fonction de l'état de la case à cocher
            if (caseACocher.checked) {
                champsTexteContainer.style.display = 'none';
                champsTexteContainer1.style.display = 'none';
                champsTexteContainer2.style.display = 'block';
            } else if (caseACocher1.checked) {
                if (prim_fisc > 0) {
                    champsTexteContainer1.style.display = 'block';
                    champsTexteContainer.style.display = 'block';
                    champsTexteContainer2.style.display = 'none';
                } else {
                    champsTexteContainer1.style.display = 'none';
                    champsTexteContainer.style.display = 'block';
                    champsTexteContainer2.style.display = 'none';
                }
            } else if (caseACocher2.checked) {
                if (prim_social > 0) {
                    champsTexteContainer1.style.display = 'block';
                    champsTexteContainer.style.display = 'block';
                    champsTexteContainer2.style.display = 'none';
                } else {
                    champsTexteContainer1.style.display = 'block';
                    champsTexteContainer.style.display = 'none';
                    champsTexteContainer2.style.display = 'none';
                }
            }
        }

        function MyNbre() {
            var cpte = 1;
            // Vider la zone d'affichage précédente
            document.getElementById('form').innerHTML = '';
            var div = document.createElement('div');
            div.classList.add('row');
            div.innerHTML = `<div class="row">
                                <div class="col-md-12">
                                    <marquee behavior="scroll" direction="left" scrollamount="10"><h2 style="color:white;">Cliquer sur le bouton "ajouter" ou sur le bouton "Simuler par importation" pour continuer . Bonne utilisation !</h2></marquee>
                                </div>
                            </div>`;
            document.getElementById('form').appendChild(div);
            // Supprimer le message après quelques secondes (juste un exemple, ajustez selon vos besoins)
            setTimeout(function () {
                document.getElementById('form').removeChild(div);
            }, 19000); // Supprime le message après 5 secondes

            document.getElementById("nbr").disabled = true;
            document.getElementById("addSig").disabled = false;
            document.getElementById("ajout").disabled = false;

        }

        function calculbrut(netanc) {

            // Récupérer la valeur du champ de saisie
            const mtsalary = document.getElementById("emp_mont_base[" + netanc + "]").value;
            //var basesalary = document.getElementById("emp_mont[" + netanc + "]").value;
            var n = document.getElementById("mystatus[" + netanc + "]").value;
            var brut_cnps = document.getElementById("emp_mont_cnps[" + netanc + "]").value;
            //calculons les charges des employés
            var cnps = 0;
            var cmu = document.getElementById("cmu_pers[" + netanc + "]").value;
            var brut = document.getElementById("emp_mont[" + netanc + "]").value;
            var cn = (document.getElementById("emp_mont[" + netanc + "]").value * 80) / 100;
            var r = 0;
            var resultcn = 0;
            var resultcnps;
            var brut_total = 0;
            var igr = 0;
            var q = 0;
            var resultits = 0;
            var resultigr = 0;
            var resultcmu = 0;
            brut_total = parseFloat(mtsalary);
            if ((mtsalary >= 75000) && (brut >= 75000) && (brut_cnps >= 75000) && (brut_cnps < 3375001)) {
                var its = ((brut * 1.2) / 100);
                resultits = Math.round(its);

                if (cn >= 0 && cn <= 50000) {
                    var totaux = (cn * 0) / 100;
                    resultcn = Math.round(totaux);
                } else if (cn > 50000 && cn <= 130000) {
                    var montcn = cn - 50000;
                    var total = (((50000 * 0) / 100) + ((montcn * 1.5) / 100));
                    r = (((brut * 80) / 100) - (its + total)) * 85 / 100;
                    resultcn = Math.round(total);
                } else if (cn > 130000 && cn <= 200000) {
                    var montcn1 = cn - 130000;
                    var total1 = (((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((montcn1 * 5) / 100));
                    r = (((brut * 80) / 100) - (its + total1)) * 85 / 100;
                    resultcn = Math.round(total1);
                } else if (cn > 200000) {
                    var montcn2 = cn - 200000;
                    var total2 = ((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((70000 * 5) / 100) + ((montcn2 * 10) / 100);
                    r = (((brut * 80) / 100) - (its + total2)) * 85 / 100;
                    resultcn = Math.round(total2);
                }
                q = r / n;
                if (q < 25000) {
                    igr = 0;
                    resultigr = Math.round(igr);
                } else if (q > 25000 && q < 45583) {
                    igr = (r * (10 / 110)) - (2273 * n);
                    resultigr = Math.round(igr);
                } else if (q > 45584 && q < 81583) {
                    igr = (r * (15 / 115)) - (4076 * n);
                    resultigr = Math.round(igr);
                } else if (q > 81584 && q < 126883) {
                    igr = (r * (20 / 120)) - (7031 * n);
                    resultigr = Math.round(igr);
                } else if (q > 126884 && q < 220383) {
                    igr = (r * (25 / 125)) - (11250 * n);
                    resultigr = Math.round(igr);
                } else if (q > 220384 && q < 389083) {
                    igr = (r * (35 / 135)) - (24306 * n);
                    resultigr = Math.round(igr);
                } else if (q > 389084 && q < 842166) {
                    igr = (r * (45 / 145)) - (44181 * n);
                    resultigr = Math.round(igr);
                } else if (q > 842167) {
                    igr = (r * (60 / 160)) - (98633 * n);
                    resultigr = Math.round(igr);
                }
                // CMU : 500 FCFA par personne salarié (taux fixe, sans progressivité)
                coticmu = cmu * 500;
                cnps = (brut_cnps * 6.3) / 100;
                resultcmu = Math.round(coticmu);
                resultcnps = Math.round(cnps);
                var impots = (resultits) + (resultcn) + (resultigr) + (resultcnps) + (resultcmu);
                var salarynet1 = (brut_total - impots);
                document.getElementById("netold[" + netanc + "]").innerHTML = '<span style="color:#ddcd08">Salaire Net Ancien : </span>' + salarynet1.toLocaleString() + ' FCFA';
            } else {
                document.getElementById("netold[" + netanc + "]").innerHTML = '';
            }

        }

        function Retrait() {
            // Utiliser la fonction confirm pour demander une confirmation à l'utilisateur
            var confirmation = confirm("Êtes-vous sûr de vouloir supprimer le dernier formulaire ?");

            // Si l'utilisateur clique sur "OK" dans la boîte de confirmation, procéder au retrait
            if (confirmation) {
                if (cptemp > 1) {
                    // Supprimer les trois derniers éléments ajoutés (div, div2, div3)
                    for (var i = 0; i < 3; i++) {
                        var lastChild = document.getElementById('form').lastChild;
                        lastChild.parentNode.removeChild(lastChild);
                    }

                    cptemp--;

                    // Désactiver le bouton de suppression si on est sur le premier formulaire
                    if (cptemp === 1) {
                        document.getElementById("retrait").disabled = true;
                        document.getElementById("retrait").hidden = true;
                    }

                    // Calculer à nouveau le formulaire précédent (s'il existe)
                    if (cptemp > 1) {
                        calculbrut(cptemp - 1);
                    }
                }
            }
            // Sinon, ne rien faire si l'utilisateur clique sur "Annuler" dans la boîte de confirmation
        }

        // Fonction pour importer un fichier CSV ou TXT
        function importerFichier(fichier) {
            var fileInput = document.createElement("input");
            fileInput.type = "file";

            // Écouter l'événement de changement du fichier
            fileInput.addEventListener("change", function () {
                var file = fileInput.files[0];
                var reader = new FileReader();

                // Lire le contenu du fichier
                reader.onload = function (e) {
                    var contenu = e.target.result;

                    // Diviser le contenu en lignes
                    var lignes = contenu.split(/\r\n|\n/);

                    // Parcourir chaque ligne et remplir le formulaire
                    for (var i = 1; i < lignes.length; i++) {
                        var champs = lignes[i].split(",");

                        document.getElementById("emp_mont_base[" + (i + 1) + "]").value = champs[0];
                        document.getElementById("situation_mat[" + (i + 1) + "]").value = champs[1];
                        document.getElementById("emp_mont[" + (i + 1) + "]").value = champs[2];
                        document.getElementById("emp_mont_cnps[" + (i + 1) + "]").value = champs[3];

                        // Ajouter un nouvel employé si nécessaire
                        if (i + 1 > cptemp) {
                            Ajout();
                        }
                    }
                };

                // Lire en tant que texte
                reader.readAsText(file);
            });

            // Cliquez sur le sélecteur de fichiers
            fileInput.click();
        }

    </script>
    <script>
        function telechargerModele() {
            // Créer un lien temporaire
            var lien = document.createElement('a');
            lien.href = "{{ asset(Storage::url('uploads/sample')) . '/ex_imp_simulateur.xlsx' }}";
            lien.download = 'modele_excel_simulateur.xlsx';

            // Ajouter le lien à la page
            document.body.appendChild(lien);

            // Cliquer sur le lien
            lien.click();
            //alert("Téléchargement du modèle en cours...");
            // Retirer le lien de la page
            document.body.removeChild(lien);
        }
    </script>
    <script>
        function affichedivimport() {
            var divcache = document.getElementById("form_input");
            var divaffiche = document.getElementById("etape");
            var reset = document.getElementById("reset2");
            var btnimport = document.getElementById("importetape");
            var btnsimuler = document.getElementById("simulerimport");
            var dawonload = document.getElementById("dawonload");
            divcache.style.display = "none";
            divaffiche.style.display = "block";
            reset.hidden = false;
            reset.disabled = false;
            btnimport.disabled = false;
            dawonload.disabled = false;
            btnsimuler.disabled = true;
        }
        function importerFichierXLSX() {
            var fileInput = document.createElement("input");
            fileInput.type = "file";

            fileInput.addEventListener("change", function () {
                var file = fileInput.files[0];
                var reader = new FileReader();

                reader.onload = function (e) {
                    var contenu = e.target.result;
                    var workbook = XLSX.read(contenu, { type: "binary" });

                    var firstSheet = workbook.SheetNames[0];
                    var worksheet = workbook.Sheets[firstSheet];

                    var donnees = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    remplirTableau(donnees);
                };

                reader.readAsBinaryString(file);
            });

            fileInput.click();

            document.getElementById("exporterExcel").disabled = false;
            document.getElementById("exportpdf").disabled = false;
        }

        function remplirTableau(donnees) {
            var tableau = document.getElementById("tableau");
            var champsRemplis = true;
            var erreurs = [];

            for (var i = 1; i < donnees.length; i++) {
                var rowData1 = donnees[i];
                var nbemp1 = parseFloat(rowData1[0]);
                var local1 = rowData1[1];
                var mtsalary1 = parseFloat(rowData1[2]);
                var basesalary1 = parseFloat(rowData1[3]);
                var brut_cnps1 = parseFloat(rowData1[4]);

                var nbparts1 = parseFloat(rowData1[5]);
                var cmu1 = parseFloat(rowData1[6]);
                var act1 = parseFloat(rowData1[7]);

                // Vérifier si toutes les cellules sont remplies
                if (rowData1.some(cellule => cellule === '' || cellule === null)) {
                    erreurs.push('Veuillez remplir toutes les cellules du tableau pour continuer la simulation par importation.');
                    champsRemplis = false;
                    break;  // Sortir de la boucle si un champ est vide
                }

                // Vérifications supplémentaires
                if (mtsalary1 < 75000) {
                    champsRemplis = false;
                    erreurs.push('Le montant du Salaire brut total ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + (i + 1) + '.');
                }

                if (basesalary1 < 75000) {
                    champsRemplis = false;
                    erreurs.push('Le montant du Salaire brut fiscal ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + (i + 1) + '.');
                }

                if (brut_cnps1 < 75000) {
                    champsRemplis = false;
                    erreurs.push('Le montant du Salaire brut Social (CNPS) ne doit pas être inférieur à 75 000 FCFA pour l\'employé ' + (i + 1) + '.');
                }
                if (brut_cnps1 > 3375000) {
                    champsRemplis = false;
                    erreurs.push('Le montant du Salaire brut Social (CNPS) ne doit pas excéder 3 375 000 FCFA pour l\'employé ' + (i + 1) + '.');
                }
            }
            //alert(champsRemplis);
            // Afficher les erreurs accumulées
            if (!champsRemplis) {
                alert(erreurs.join('\n'));
            }
            for (var j = 1; j < donnees.length; j++) {
                var rowData = donnees[j];
                var nbemp = parseFloat(rowData[0]);
                var local = rowData[1];
                var mtsalary = parseFloat(rowData[2]);
                var basesalary = parseFloat(rowData[3]);
                var brut_cnps = parseFloat(rowData[4]);
                var nbparts = parseFloat(rowData[5]);
                var cmu = parseFloat(rowData[6]);
                var act = parseFloat(rowData[7]);
                var cnps = 0;
                var cn = (basesalary * 80) / 100;
                var r = 0;
                var resultcn = 0;
                var resultcnps;
                var brut_total = 0;
                var igr = 0;
                var q = 0;
                var resultits = 0;
                var resultigr = 0;
                var resultcmu = 0;
                var brut = basesalary;
                brut_total = parseFloat(mtsalary);

                if (champsRemplis) {
                    if ((mtsalary >= 75000) && (basesalary >= 75000) && (brut_cnps >= 75000) && (brut_cnps < 3375001)) {
                        var its = ((brut * 1.2) / 100);
                        resultits = Math.round(its);

                        if (cn >= 0 && cn <= 50000) {
                            var totaux = (cn * 0) / 100;
                            resultcn = Math.round(totaux);
                        } else if (cn > 50000 && cn <= 130000) {
                            var montcn = cn - 50000;
                            var total = (((50000 * 0) / 100) + ((montcn * 1.5) / 100));
                            r = (((brut * 80) / 100) - (its + total)) * 85 / 100;
                            resultcn = Math.round(total);
                        } else if (cn > 130000 && cn <= 200000) {
                            var montcn1 = cn - 130000;
                            var total1 = (((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((montcn1 * 5) / 100));
                            r = (((brut * 80) / 100) - (its + total1)) * 85 / 100;
                            resultcn = Math.round(total1);
                        } else if (cn > 200000) {
                            var montcn2 = cn - 200000;
                            var total2 = ((50000 * 0) / 100) + ((80000 * 1.5) / 100) + ((70000 * 5) / 100) + ((montcn2 * 10) / 100);
                            r = (((brut * 80) / 100) - (its + total2)) * 85 / 100;
                            resultcn = Math.round(total2);
                        }
                        q = r / nbparts;
                        if (q < 25000) {
                            igr = 0;
                            resultigr = Math.round(igr);
                        } else if (q > 25000 && q < 45583) {
                            igr = (r * (10 / 110)) - (2273 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 45584 && q < 81583) {
                            igr = (r * (15 / 115)) - (4076 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 81584 && q < 126883) {
                            igr = (r * (20 / 120)) - (7031 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 126884 && q < 220383) {
                            igr = (r * (25 / 125)) - (11250 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 220384 && q < 389083) {
                            igr = (r * (35 / 135)) - (24306 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 389084 && q < 842166) {
                            igr = (r * (45 / 145)) - (44181 * nbparts);
                            resultigr = Math.round(igr);
                        } else if (q > 842167) {
                            igr = (r * (60 / 160)) - (98633 * nbparts);
                            resultigr = Math.round(igr);
                        }
                        if (cmu < 7) {
                            coticmu = (cmu * 500);
                            coticmuemp = (cmu * 500);
                        } else {
                            coticmu = 3000 + ((cmu - 6) * 1000);
                            coticmuemp = 3000;
                        }
                        var tauxact = 0;
                        cnps = (brut_cnps * 6.3) / 100;
                        cnpsemp = ((brut_cnps * 7.7) / 100);
                        if (act == '0,03') {
                            tauxact = 75000 * 0.03;
                        } else if (act == '0,02') {
                            tauxact = 75000 * 0.02;
                        } else if (act == '0,04') {
                            tauxact = 75000 * 0.04;
                        } else if (act == '0,05') {
                            tauxact = 75000 * 0.05;
                        }
                        tt_cnpsemp = cnpsemp + tauxact + ((75000 * 5.75) / 100);
                        //alert(tauxact);
                        tax1 = (brut * 1.2) / 100;
                        tax2 = (brut * 11.5) / 100;
                        tax3 = (brut * 1.6) / 100;
                        //tax4 = (brut*0.6)/100;
                        tt_cnps = (brut_cnps * 6.3) / 100;
                        resultcmu = Math.round(coticmu);
                        resultcnps = Math.round(cnps);
                        resulttax1 = Math.round(tax1);
                        resulttax2 = Math.round(tax2);
                        resulttax3 = Math.round(tax3);
                        if (local == 'local') {
                            parpatronal = tt_cnpsemp + coticmuemp + tax1 + tax3;
                        } else {
                            parpatronal = tt_cnpsemp + coticmuemp + tax1 + tax2 + tax3;
                        }
                        //resulttax4 = Math.round(tax4).toLocaleString();
                        if (brut >= 0 && brut <= 75000) {
                            var tot = (brut * 0) / 100;
                            resultimpricf = Math.round(tot);
                        } else if (brut > 75000 && brut <= 240000) {
                            var mt1 = brut - 75000;
                            var tot1 = (((75000 * 0) / 100) + ((mt1 * 16) / 100));
                            //r = (((brut*80)/100)-(its+total))*85/100;
                            resultimpricf = Math.round(tot1);
                        } else if (brut > 240000 && brut <= 800000) {
                            var mt2 = brut - 240000;
                            var tot2 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((mt2 * 21) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot2);
                        } else if (brut > 800000 && brut <= 2400000) {
                            var mt3 = brut - 800000;
                            var tot3 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((mt3 * 24) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot3);
                        } else if (brut > 2400000 && brut <= 8000000) {
                            var mt4 = brut - 2400000;
                            var tot4 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((mt4 * 28) / 100));
                            //r = (((brut*80)/100)-(its+total1))*85/100;
                            resultimpricf = Math.round(tot4);
                        } else if (brut > 8000000) {
                            var mt5 = brut - 8000000;
                            var tot5 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((5600000 * 28) / 100) + ((mt5 * 32) / 100));
                            //r = (((brut*80)/100)-(its+total2))*85/100;
                            resultimpricf = Math.round(tot5);
                        }

                        var tauxReduction = 0;
                        if (nbparts == 1.5) tauxReduction = 0.10;
                        else if (nbparts == 2) tauxReduction = 0.15;
                        else if (nbparts == 2.5) tauxReduction = 0.20;
                        else if (nbparts == 3) tauxReduction = 0.25;
                        else if (nbparts == 3.5) tauxReduction = 0.30;
                        else if (nbparts == 4) tauxReduction = 0.35;
                        else if (nbparts == 4.5) tauxReduction = 0.40;
                        else if (nbparts == 5) tauxReduction = 0.45;
                        resultricf = Math.round(resultimpricf * tauxReduction);
                        //var prime = brut_total - brut;
                        var impots = (resultits) + (resultcn) + (resultigr) + (resultcnps) + (resultcmu);

                        var retenue1 = (resultits) + (resultcn) + (resultigr);
                        var retenue2 = (resultimpricf) - (resultricf);
                        if (retenue2 > 0) {
                            retenue3 = ((resultits) + (resultcn) + (resultigr)) - ((resultimpricf) - (resultricf));
                            impots2 = (resultimpricf) - (resultricf) + (resultcnps) + (resultcmu);
                        } else {
                            retenue3 = ((resultits) + (resultcn) + (resultigr));
                            impots2 = (resultcnps) + (resultcmu);
                        }
                        //var
                        var salarynet1 = (brut_total - impots);
                        var salarynet2 = (brut_total - impots2);
                        var variant = salarynet2 - salarynet1;
                        var coutsal = parseInt(parpatronal) + parseInt(brut_total);
                        // Créer une nouvelle ligne et des cellules
                        var nouvelleLigne = corpsTableau.insertRow(-1);
                        var cellule1 = nouvelleLigne.insertCell(0);
                        var cellule2 = nouvelleLigne.insertCell(1);
                        var cellule3 = nouvelleLigne.insertCell(2);
                        var cellule4 = nouvelleLigne.insertCell(3);
                        var cellule5 = nouvelleLigne.insertCell(4);
                        var cellule6 = nouvelleLigne.insertCell(5);
                        var cellule7 = nouvelleLigne.insertCell(6);
                        var cellule8 = nouvelleLigne.insertCell(7);
                        var cellule9 = nouvelleLigne.insertCell(8);
                        var cellule10 = nouvelleLigne.insertCell(9);
                        var cellule11 = nouvelleLigne.insertCell(10);
                        var cellule12 = nouvelleLigne.insertCell(11);
                        var cellule13 = nouvelleLigne.insertCell(12);
                        var cellule14 = nouvelleLigne.insertCell(13);
                        var cellule15 = nouvelleLigne.insertCell(14);
                        var cellule16 = nouvelleLigne.insertCell(15);
                        var cellule17 = nouvelleLigne.insertCell(16);
                        var cellule18 = nouvelleLigne.insertCell(17);
                        var cellule19 = nouvelleLigne.insertCell(18);
                        var cellule20 = nouvelleLigne.insertCell(19);
                        var cellule21 = nouvelleLigne.insertCell(20);
                        var cellule22 = nouvelleLigne.insertCell(21);
                        var cellule23 = nouvelleLigne.insertCell(22);
                        var cellule24 = nouvelleLigne.insertCell(23);
                        var cellule25 = nouvelleLigne.insertCell(24);
                        var cellule26 = nouvelleLigne.insertCell(25);
                        var cellule27 = nouvelleLigne.insertCell(26);
                        var cellule28 = nouvelleLigne.insertCell(27);

                        cellule1.innerHTML = 'Emp ' + nbemp + '<input type="text" value="' + nbemp + '" hidden>';
                        cellule2.innerHTML = (local == 'local' ? 'Local' : 'Expatrié') + '<input type="text" value="0" hidden>';
                        cellule3.innerHTML = nbparts + '<input type="text" value="' + nbparts + '" hidden>';
                        cellule4.innerHTML = '<span>' + brut_total.toLocaleString() + ' FCFA </span> <input type="text" value="' + brut_total + '" hidden>';
                        cellule5.innerHTML = '<span>' + parseFloat(brut).toLocaleString() + ' FCFA </span> <input type="text" value="' + parseFloat(brut) + '" hidden>';
                        cellule6.innerHTML = '<span>' + parseFloat(brut_cnps).toLocaleString() + ' FCFA </span> <input type="text" value="' + parseFloat(brut_cnps) + '" hidden>';
                        cellule7.innerHTML = '<span>' + cmu + '</span> <input type="text" value="0" hidden>';
                        cellule8.innerHTML = '<span>' + resultits.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultits + '" hidden>';
                        cellule9.innerHTML = '<span>' + resultcn.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultcn + '" hidden>';
                        cellule10.innerHTML = '<span>' + resultigr.toLocaleString() + ' FCFA </span> <input type="text" value="' + resultigr + '" hidden>';
                        cellule11.innerHTML = '<span>' + retenue1.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue1 + '" hidden>';
                        cellule12.innerHTML = '<span>' + resultimpricf.toLocaleString() + ' FCFA</span> <input type="text" value="' + resultimpricf + '" hidden>';
                        cellule13.innerHTML = '<span>' + resultricf.toLocaleString() + ' FCFA</span> <input type="text" value="' + resultricf + '" hidden>';
                        if (retenue2 > 0) {
                            cellule14.innerHTML = '<span>' + retenue2.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue2 + '" hidden>';
                        } else {
                            cellule14.innerHTML = '<span>' + 0 + ' FCFA</span> <input type="text" value="' + 0 + '" hidden>';
                        }
                        cellule15.innerHTML = '<span>' + retenue3.toLocaleString() + ' FCFA</span> <input type="text" value="' + retenue3 + '" hidden>';
                        cellule16.innerHTML = '<span>' + Math.round(resultcnps).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(tt_cnps) + '" hidden>';
                        cellule17.innerHTML = '<span>' + Math.round(resultcmu).toLocaleString() + ' FCFA </span> <input type="text" value="' + resultcmu + '" hidden>';
                        cellule18.innerHTML = '<span>' + salarynet1.toLocaleString() + ' FCFA </span> <input type="text" value="' + salarynet1 + '" hidden>';
                        cellule19.innerHTML = '<span>' + salarynet2.toLocaleString() + ' FCFA </span> <input type="text" value="' + salarynet2 + '" hidden>';
                        cellule20.innerHTML = '<span>' + variant.toLocaleString() + ' FCFA</span> <input type="text" value="' + variant + '" hidden>';
                        if (variant > 0) {
                            cellule21.innerHTML = 'Gains de : ' + variant.toLocaleString() + ' FCFA <input type="text" value="' + variant + '" hidden>';
                        } else {
                            cellule21.innerHTML = 'Perte de : ' + variant.toLocaleString() + ' FCFA <input type="text" value="' + variant + '" hidden>';
                        }

                        cellule22.innerHTML = '<span>' + Math.round(resulttax1).toLocaleString() + ' FCFA </span> <input type="text" value="' + resulttax1 + '" hidden>';
                        if (local == 'local') {
                            cellule23.innerHTML = '- <input type="text" value="0" hidden>';
                        } else {
                            cellule23.innerHTML = '<span>' + Math.round(resulttax2).toLocaleString() + ' FCFA </span> <input type="text" value="' + Math.round(resulttax2) + '" hidden>';
                        }
                        cellule24.innerHTML = '<span>' + Math.round(resulttax3).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(resulttax3) + '" hidden>';
                        cellule25.innerHTML = '<span>' + Math.round(tt_cnpsemp).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(tt_cnpsemp) + '" hidden>';
                        cellule26.innerHTML = '<span>' + coticmuemp.toLocaleString() + ' FCFA</span> <input type="text" value="' + coticmuemp + '" hidden>';
                        cellule27.innerHTML = '<span>' + Math.round(parpatronal).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(parpatronal) + '" hidden>';
                        cellule28.innerHTML = '<span>' + Math.round(coutsal).toLocaleString() + ' FCFA</span> <input type="text" value="' + Math.round(coutsal) + '" hidden>';
                    }
                }

                //alert("NbEmp: " + nbemp + "\nType: " + local + "\nMtsalary: " + mtsalary + "\nBasesalary: " + basesalary +" "+ cn +"\nBrut_cnps: " + brut_cnps + "\nNetSalary: " + salarynet2 + "\nCMU: " + cmu + "\nNBParts: " + nbparts);
            }
            if (champsRemplis) {
                if ((mtsalary >= 75000) && (basesalary >= 75000) && (brut_cnps >= 75000) && (brut_cnps < 3375001)) {
                    // Déclarer totalColonnes en dehors de la boucle
                    var totalColonnes = Array.from({ length: tableau.rows[1].cells.length }).fill(0);
                    var tt_gains = 0;
                    var tt_perte = 0;
                    for (var k = 1; k < tableau.rows.length; k++) {
                        for (var j = 0; j < tableau.rows[k].cells.length; j++) {
                            // Utiliser parseFloat pour convertir la valeur en nombre
                            var element = tableau.rows[k].cells[j].querySelector('input[type="text"]');

                            if (element && element.value.trim() !== '' && !isNaN(element.value)) {
                                // Ajouter la valeur à totalColonnes
                                totalColonnes[j] += parseFloat(element.value);
                            }

                            if (j == 19) {
                                var cellule = tableau.rows[k].cells[j].querySelector('input[type="text"]');
                                // Assurez-vous que cellule n'est pas null avant de comparer
                                if (cellule) {
                                    var valeurCellule = parseFloat(cellule.value);

                                    if (valeurCellule > 0) {
                                        tt_gains += valeurCellule;
                                    } else if (valeurCellule < 0) {
                                        tt_perte += valeurCellule;
                                    }
                                }
                            }
                        }
                    }

                    // Afficher le total dans la dernière ligne
                    var derniereLigne = tableau.insertRow(-1);

                    var celluleTotal = derniereLigne.insertCell(0);
                    var celluleTotal1 = derniereLigne.insertCell(1);
                    celluleTotal.innerHTML = '<b> TOTAL :</b> <strong>' + nbemp + '</strong>';
                    celluleTotal1.innerHTML = '<strong><center> - </center></strong>';
                    for (var q = 2; q < totalColonnes.length; q++) {
                        var celluleTotal = derniereLigne.insertCell(q);
                        celluleTotal.innerHTML = '<strong>' + totalColonnes[q].toLocaleString() + '</strong> <b>FCFA</b>';
                    }


                    var totalCelluleBrut = totalColonnes[3];
                    var totalCelluleSbi = totalColonnes[4];
                    var totalCelluleSbs = totalColonnes[5];
                    var totalCellule7 = totalColonnes[10];
                    var totalCellule10 = totalColonnes[13];
                    var totalCelluleCnps = totalColonnes[15];
                    var totalCelluleCmu = totalColonnes[16];
                    var totalCellule15 = totalColonnes[18];
                    var totalCellule16 = totalColonnes[19];
                    var totalCellule23 = totalColonnes[26];
                    var totalCellule24 = totalColonnes[27];
                    document.getElementById("totalemp").innerHTML = nbemp;
                    document.getElementById("totalimpan").innerHTML = totalCellule7.toLocaleString() || 0;
                    document.getElementById("totalimpnv").innerHTML = totalCellule10.toLocaleString() || 0;
                    document.getElementById("totalgainsal").innerHTML = tt_gains.toLocaleString() || 0;
                    document.getElementById("totalpertsal").innerHTML = tt_perte.toLocaleString() || 0;
                    document.getElementById("totalnet").innerHTML = totalCellule15.toLocaleString() || 0;
                    document.getElementById("totalvarmasse").innerHTML = totalCellule16.toLocaleString() || 0;
                    document.getElementById("totalpatron").innerHTML = totalCellule23.toLocaleString() || 0;
                    document.getElementById("totalcout").innerHTML = Math.round(totalCellule24).toLocaleString() || 0;
                    document.getElementById("totalbrut").innerHTML = totalCelluleBrut.toLocaleString() || 0;
                    document.getElementById("totalsbiresume").innerHTML = totalCelluleSbi.toLocaleString() || 0;
                    document.getElementById("totalsbsresume").innerHTML = totalCelluleSbs.toLocaleString() || 0;
                    document.getElementById("totalcnpssal").innerHTML = totalCelluleCnps.toLocaleString() || 0;
                    document.getElementById("totalcmusal").innerHTML = totalCelluleCmu.toLocaleString() || 0;
                    document.getElementById("totalretenuessal").innerHTML = Math.round(totalCellule10 + totalCelluleCnps + totalCelluleCmu).toLocaleString() || 0;

                    document.getElementById("exportpdf").disabled = false;
                    document.getElementById("exporterExcel").disabled = false;
                    document.getElementById("simulerimport").disabled = true;
                    document.getElementById("importetape").disabled = true;
                    document.getElementById("dawonload").disabled = true;
                }
            }

        }
        document.getElementsByTagName("h1")[0].style.fontSize = "30px";

        function Resultat_brut(brut_total) {
            var tp_brut = parseInt(document.getElementById("tp_brut").value);
            var input = parseInt(document.getElementById("tp_brut_libre").value);
            var brut = 0;
            var brut_cnps = 0;
            if (tp_brut > 30000) {
                brut = brut_total - 30000 + (tp_brut - 30000);
                brut_cnps = brut_total - 30000 + (tp_brut - 30000);
            } else if (tp_brut === 30000) {
                brut = brut_total - 30000;
                brut_cnps = brut_total - 30000;
            } else if (tp_brut === 24000) {
                brut = brut_total - 24000;
                brut_cnps = brut_total - 24000;
            } else if (tp_brut === 22000) {
                brut = brut_total - 22000;
                brut_cnps = brut_total - 22000;
            } else {
                brut = brut_total - tp_brut;
                brut_cnps = brut_total - tp_brut;
            }


            var totoverstimes = 0; // Initialiser la variable pour les heures supplémentaires
            //calculons les charges des employés
            if (brut_cnps > 3375000) {
                brut_cnps = 3375000;
            }
            var cnps = 0;
            var cmu = document.getElementById("cmu_brut").value;
            var resultcnps = 0;
            var resultcmu = 0;

            coticmu = cmu * 500;
            coticmuemp = cmu * 500;

            cnps = (brut_cnps * 6.3) / 100;

            tt_cnps = (brut_cnps * 6.3) / 100;
            resultcmu = Math.round(coticmu);
            resultcnps = Math.round(cnps);
            var resultpatcn = Math.round((brut * 1.2) / 100);
            var resultpatce = 0;
            var resultpatfdfp = Math.round((brut * 1.6) / 100);
            var cnpsemp = (brut_cnps * 7.7) / 100;
            var tauxact = 0;
            var act = document.getElementById("atravail") ? document.getElementById("atravail").value : '0,03';
            if (act == '0,03') {
                tauxact = 75000 * 0.03;
            } else if (act == '0,02') {
                tauxact = 75000 * 0.02;
            } else if (act == '0,04') {
                tauxact = 75000 * 0.04;
            } else if (act == '0,05') {
                tauxact = 75000 * 0.05;
            }
            var resultpatcnps = Math.round(cnpsemp + tauxact + ((75000 * 5.75) / 100));
            var resultpatcmu = Math.round(coticmuemp);
            var totalpatronales = Math.round(resultpatcn + resultpatce + resultpatfdfp + resultpatcnps + resultpatcmu);
            var coutsalarial = Math.round(brut_total + totalpatronales);

            // Calculer le montant total de l'impôt sur le revenu imposable
            var resultimpricf = 0;
            if (brut >= 0 && brut <= 75000) {
                var tot = (brut * 0) / 100;
                resultimpricf = Math.round(tot);
            } else if (brut > 75000 && brut <= 240000) {
                var mt1 = brut - 75000;
                var tot1 = (((75000 * 0) / 100) + ((mt1 * 16) / 100));
                //r = (((brut*80)/100)-(its+total))*85/100;
                resultimpricf = Math.round(tot1);
            } else if (brut > 240000 && brut <= 800000) {
                var mt2 = brut - 240000;
                var tot2 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((mt2 * 21) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot2);
            } else if (brut > 800000 && brut <= 2400000) {
                var mt3 = brut - 800000;
                var tot3 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((mt3 * 24) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot3);
            } else if (brut > 2400000 && brut <= 8000000) {
                var mt4 = brut - 2400000;
                var tot4 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((mt4 * 28) / 100));
                //r = (((brut*80)/100)-(its+total1))*85/100;
                resultimpricf = Math.round(tot4);
            } else if (brut > 8000000) {
                var mt5 = brut - 8000000;
                var tot5 = (((75000 * 0) / 100) + ((165000 * 16) / 100) + ((560000 * 21) / 100) + ((1600000 * 24) / 100) + ((5600000 * 28) / 100) + ((mt5 * 32) / 100));
                //r = (((brut*80)/100)-(its+total2))*85/100;
                resultimpricf = Math.round(tot5);
            }
            //nombre de part en FCFA
            var nbre = parseFloat(document.getElementById("part_igr").value) || 1;
            var ricfTable = { 1: 0, 1.5: 5500, 2: 11000, 2.5: 16500, 3: 22000, 3.5: 27500, 4: 33000, 4.5: 38500, 5: 44000 };
            resultricf = 0;
            for (var rk in ricfTable) {
                if (Math.abs(nbre - parseFloat(rk)) < 0.01) { resultricf = ricfTable[rk]; break; }
            }
            if (nbre >= 5) resultricf = 44000;
            var retenue2 = (resultimpricf) - (resultricf);
            if (retenue2 > 0) {
                impots2 = (resultimpricf) - (resultricf) + (resultcnps) + (resultcmu);
            } else {
                retenue2 = 0;
                impots2 = (resultcnps) + (resultcmu);
            }
            var salaireNet = brut_total - impots2;

            var resultatimp = document.getElementById("resultatimp");
            var resultatricft = document.getElementById("resultatricft");
            var resultatpart = document.getElementById("resultatpart");
            //var resultatexo = document.getElementById("resultatexo");
            var resultatnetimp = document.getElementById("resultatnetimp");
            var resultatcnps = document.getElementById("resultatcnps");
            var totalsbi = document.getElementById("totalsbi");
            var totalsbs = document.getElementById("totalsbs");
            var resultatpatfiscalesEl = document.getElementById("resultatpatfiscales");
            var resultatpatsocialesEl = document.getElementById("resultatpatsociales");
            var resultatpatronalEl = document.getElementById("resultatpatronal");
            var resultatcoutsalEl = document.getElementById("resultatcoutsal");
            var partPatronalesFiscales = resultpatcn + resultpatce + resultpatfdfp;
            var partPatronalesSociales = resultpatcnps + resultpatcmu;

            resultatimp.innerHTML = resultimpricf.toLocaleString() + ' FCFA';
            resultatricft.innerHTML = resultricf.toLocaleString() + ' FCFA';
            //resultatexo.innerHTML = 0;
            resultatnetimp.innerHTML = retenue2.toLocaleString() + ' FCFA';
            resultatcnps.innerHTML = resultcnps.toLocaleString() + ' FCFA';
            totalretenues.innerHTML = impots2.toLocaleString() + ' FCFA';
            totalsbi.innerHTML = brut.toLocaleString() + ' FCFA';
            totalsbs.innerHTML = brut_cnps.toLocaleString() + ' FCFA';
            resultatpatfiscalesEl.innerHTML = partPatronalesFiscales.toLocaleString() + ' FCFA';
            resultatpatsocialesEl.innerHTML = partPatronalesSociales.toLocaleString() + ' FCFA';
            resultatpatronalEl.innerHTML = totalpatronales.toLocaleString() + ' FCFA';
            resultatcoutsalEl.innerHTML = coutsalarial.toLocaleString() + ' FCFA';

            var brutResumeValues = {
                brutResumeTotalEmp: 1,
                brutResumeImpAncien: 0,
                brutResumeDiffNet: 0,
                brutResumeGain: 0,
                brutResumeImpNouveau: retenue2,
                brutResumePatronales: totalpatronales,
                brutResumePerte: 0,
                brutResumeNet: salaireNet,
                brutResumeCout: coutsalarial,
                brutResumeBrut: brut_total,
                brutResumeSbi: brut,
                brutResumeSbs: brut_cnps,
                brutResumeCnps: resultcnps,
                brutResumeCmu: resultcmu,
                brutResumeRetenues: impots2
            };
            Object.keys(brutResumeValues).forEach(function (id) {
                var element = document.getElementById(id);
                if (element) {
                    element.innerHTML = Math.round(brutResumeValues[id]).toLocaleString();
                }
            });

            return salaireNet;
        }

        function trouverBrutTotal() {
            var salaireNetDesire = parseInt(document.getElementById("net_pay").value);
            if (salaireNetDesire < 75000) {
                alert("Veuillez entrer un montant supérieur ou égal à 75 000.");
                document.getElementById("net_pay").focus(); // Focus sur le champ net_pay
            } else {
                var brutTotal = salaireNetDesire * 2;
                var tp_brut = parseInt(document.getElementById("tp_brut").value);
                var input = parseInt(document.getElementById("tp_brut_libre").value);
                var tp = 0;
                var nbre = document.getElementById("part_igr").value;
                var cmu = document.getElementById("cmu_brut").value;
                var iterationsMax = 100000; // Limite le nombre d'itérations pour éviter une boucle infinie
                var iterations = 0;

                while (true) {
                    var salaireNet = Resultat_brut(brutTotal);
                    if (salaireNet === salaireNetDesire) {
                        break; // Sort de la boucle si le salaire net correspond au salaire net désiré
                    }

                    // Si le salaire net calculé est supérieur au salaire net désiré, on réduit le brut total de 100 ou de 1
                    if (salaireNet > salaireNetDesire) {
                        brutTotal -= 1000;
                    } else {
                        // Si le salaire net calculé est inférieur au salaire net désiré, on ajoute 1 au brut total
                        brutTotal += 1;
                    }

                    iterations++;
                    if (iterations >= iterationsMax) {
                        alert("Nombre maximal d'itérations atteint.");
                        break;
                    }
                }
                //alert (Resultat_brut(brutTotal));
                var resultatnet = document.getElementById("resultatnet");
                var resultattp = document.getElementById("resultattp");
                var resultatcmu = document.getElementById("resultatcmu");
                var totalretenues = document.getElementById("totalretenues");
                var resultatbrut = document.getElementById("resultatbrut");

                resultatnet.innerHTML = salaireNetDesire.toLocaleString() + ' FCFA';
                if (tp_brut > 30000) {
                    tp = (tp_brut - 30000);
                    resultattp.innerHTML = tp_brut.toLocaleString() + ' FCFA<br/> (Mt Imposable :' + tp.toLocaleString() + ' FCFA)';
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else if (tp_brut === 30000) {
                    tp = 30000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else if (tp_brut === 24000) {
                    tp = 24000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else if (tp_brut === 22000) {
                    tp = 22000;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                } else {
                    tp = tp_brut;
                    resultattp.innerHTML = tp.toLocaleString();
                    resultatbrut.innerHTML = brutTotal.toLocaleString();
                }
                resultatpart.innerHTML = nbre.toLocaleString();
                resultatcmu.innerHTML = cmu;

            }
        }
        function afficherMontant() {
            var select = document.getElementById("tp_brut");
            var input = document.getElementById("tp_brut_libre");

            if (select.value === "Montant Libre") {
                input.hidden = false; // Affiche l'input
            } else {
                input.hidden = true; // Cache l'input
            }
        }
    </script>
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('show');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function (event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');

            if (!mobileMenu.contains(event.target) && !menuButton) {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('show');
            }
        });

        // Start [ Menu hide/show on scroll ]
        let ost = 0;
        document.addEventListener("scroll", function () {
            let cOst = document.documentElement.scrollTop;
            if (cOst == 0) {
                document.querySelector(".navbar")?.classList.add("top-nav-collapse");
            } else if (cOst > ost) {
                document.querySelector(".navbar")?.classList.add("top-nav-collapse");
                document.querySelector(".navbar")?.classList.remove("default");
            } else {
                document.querySelector(".navbar")?.classList.add("default");
                document.querySelector(".navbar")?.classList.remove("top-nav-collapse");
            }
            ost = cOst;
        });
        // End [ Menu hide/show on scroll ]

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: "#navbar-example",
        });
        feather.replace();
    </script>
</body>

</html>