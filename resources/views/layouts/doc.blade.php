@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="default">
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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
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
            border-color:rgb(3, 61, 236);
            color:rgb(3, 61, 236);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg,rgb(14, 87, 246) 0%, #03c3ec 100%);
            border-color: transparent;
        }

        .btn-outline-success:hover {
            background: linear-gradient(135deg,rgb(14, 87, 246) 0%, #03c3ec 100%);
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
    @stack('styles')
</head>
<body>
        <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container"> 
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{asset('libs/select2/select2.js')}}"></script>
    <script src="{{asset('libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
    <script src="{{asset('libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Configuration de toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        // Gestion des messages de session
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
    <script>
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
        document.addEventListener('DOMContentLoaded', function() {
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
        document.addEventListener('DOMContentLoaded', function() {
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
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();

            // Actualiser les notifications toutes les 30 secondes
            setInterval(loadNotifications, 30000);
        });

        // Système de recherche global
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            const searchResultsList = document.getElementById('searchResultsList');
            const searchLoading = document.getElementById('searchLoading');
            const searchNoResults = document.getElementById('searchNoResults');

            let searchTimeout;

            // Fonction pour effectuer la recherche
            function performSearch(query) {
                if (query.length < 2) {
                    hideSearchResults();
                    return;
                }

                showSearchLoading();

                fetch('{{ route("super-admin.search.ajax") }}?q=' + encodeURIComponent(query), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideSearchLoading();

                    if (data.success && data.results.length > 0) {
                        displaySearchResults(data.results);
                    } else {
                        showNoResults();
                    }
                })
                .catch(error => {
                    console.error('Erreur de recherche:', error);
                    hideSearchLoading();
                    showNoResults();
                });
            }

            // Afficher les résultats de recherche
            function displaySearchResults(results) {
                searchResultsList.innerHTML = '';

                results.forEach(result => {
                    const resultItem = document.createElement('a');
                    resultItem.href = result.url;
                    resultItem.className = 'd-block p-3 text-decoration-none border-bottom search-result-item';
                    resultItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-sm">
                                    <div class="avatar-initial bg-${result.color} rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="${result.icon}"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${result.title}</div>
                                <small class="text-muted">${result.subtitle}</small>
                            </div>
                            <div class="flex-shrink-0">
                                <small class="badge bg-${result.color}">${result.type}</small>
                            </div>
                        </div>
                    `;
                    searchResultsList.appendChild(resultItem);
                });

                showSearchResults();
            }

            // Afficher les résultats
            function showSearchResults() {
                searchResults.style.display = 'block';
                searchNoResults.style.display = 'none';
            }

            // Masquer les résultats
            function hideSearchResults() {
                searchResults.style.display = 'none';
            }

            // Afficher le loading
            function showSearchLoading() {
                searchResultsList.innerHTML = '';
                searchResults.style.display = 'block';
                searchLoading.style.display = 'block';
                searchNoResults.style.display = 'none';
            }

            // Masquer le loading
            function hideSearchLoading() {
                searchLoading.style.display = 'none';
            }

            // Afficher aucun résultat
            function showNoResults() {
                searchResultsList.innerHTML = '';
                searchResults.style.display = 'block';
                searchLoading.style.display = 'none';
                searchNoResults.style.display = 'block';
            }

            // Écouter les changements de l'input
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(this.value);
                }, 300);
            });

            // Masquer les résultats quand on clique ailleurs
            document.addEventListener('click', function(e) {
                if (!searchResults.contains(e.target) && e.target !== searchInput) {
                    hideSearchResults();
                }
            });

            // Afficher les résultats quand on clique sur l'input
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    performSearch(this.value);
                }
            });

            // Empêcher la propagation du clic sur les résultats
            searchResults.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Auto-dismiss notifications after 5 seconds
        const notifications = document.querySelectorAll('.alert-dismissible');
        notifications.forEach(function(notification) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(notification);
                bsAlert.close();
            }, 5000); // 5 seconds
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    @stack('scripts-external')
    @stack('scripts')
</body>
</html>