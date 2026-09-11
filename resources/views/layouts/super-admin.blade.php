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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <!-- Themify Icons Fonts -->
    <link rel="stylesheet" href="{{ asset('themify-icons/themify-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rh-custom-colors.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('styles')
</head>
<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside class="layout-menu menu-vertical menu bg-menu-theme" style="position: sticky; top: 0; height: 100vh;">
                <!-- Menu Super Admin -->
                <div style="padding-top: 15px;">
                    <div class="app-brand">
                        <a href="{{ route('super-admin.dashboard') }}" class="app-brand-link">
                            <!-- ========   change your logo hear   ============ -->
                            <span class="app-brand-logo"><img src="{{ asset('img/logos/logo.png') }}"
                                alt="{{ config('app.name', 'RHFLOW') }}" class="logo logo-lg" style="height: 50px;"></span>
                        </a>
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
                            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                        </a>
                    </div>
                </div>
                <div class="menu-inner-shadow"></div>
                @php
                    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                @endphp
                <ul class="menu-inner py-1">
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Tableau de bord">Tableau de bord</span>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.dashboard' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.dashboard') }}" class="menu-link">
                            <i class="menu-icon ti ti-home"></i>
                            <div>Tableau de bord</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Pack">Pack</span>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.packs.index' || Request::route()->getName() == 'super-admin.packs.create' || Request::route()->getName() == 'super-admin.packs.edit' || Request::route()->getName() == 'super-admin.packs.show' || Request::route()->getName() == 'super-admin.packs.update' || Request::route()->getName() == 'super-admin.packs.delete' || Request::route()->getName() == 'super-admin.packs.modules' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.packs.index') }}" class="menu-link">
                            <i class="menu-icon ti ti-package"></i>
                            <div>Gestion Packs</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Entreprises">Entreprises</span>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.sectors.index' || Request::route()->getName() == 'super-admin.sectors.create' || Request::route()->getName() == 'super-admin.sectors.edit' || Request::route()->getName() == 'super-admin.sectors.show' || Request::route()->getName() == 'super-admin.sectors.update' || Request::route()->getName() == 'super-admin.sectors.delete' || Request::route()->getName() == 'super-admin.sectors.toggle' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.sectors.index') }}" class="menu-link">
                            <i class="menu-icon ti ti-view-list"></i>
                            <div>Secteurs d'activité</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.enterprises.index' || Request::route()->getName() == 'super-admin.enterprises.create' || Request::route()->getName() == 'super-admin.enterprises.edit' || Request::route()->getName() == 'super-admin.enterprises.show' || Request::route()->getName() == 'super-admin.enterprises.update' || Request::route()->getName() == 'super-admin.enterprises.subscription' || Request::route()->getName() == 'super-admin.enterprises.users' || Request::route()->getName() == 'super-admin.users.create' || Request::route()->getName() == 'super-admin.users.edit' || Request::route()->getName() == 'super-admin.users.show' || Request::route()->getName() == 'super-admin.users.update' || Request::route()->getName() == 'super-admin.users.delete' || Request::route()->getName() == 'super-admin.users.toggle' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.enterprises.index') }}" class="menu-link">
                            <i class="menu-icon ti ti-bag"></i>
                            <div>Entreprises</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.enterprises.lastlogin' || Request::route()->getName() == 'super-admin.enterprises.showactivity' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.enterprises.lastlogin') }}" class="menu-link">
                            <i class="menu-icon ti ti-save-alt"></i>
                            <div data-i18n="Dernière Connexion">{{ __('Dernière Connexion') }}</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Rapport">Rapport</span>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.reports.index' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.reports.index') }}" class="menu-link">
                            <i class="menu-icon ti ti-notepad"></i>
                            <div data-i18n="Rapport">{{ __('Rapport') }}</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Gérer les commandes">Gérer les commandes</span>
                    </li>
                    <li class="menu-item {{ in_array(Request::route()->getName(), ['super-admin.commandes.index', 'super-admin.commandes.show']) ? 'active' : '' }}">
                        <a href="{{ route('super-admin.commandes.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                            <div data-i18n="Commandes">{{ __('Commandes') }}</div>
                            @if($pendingOrdersCount > 0)
                                <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.7rem; margin-left: auto;">{{ $pendingOrdersCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.commandes.pending' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.commandes.pending') }}" class="menu-link">
                            <i class="menu-icon ti ti-reload"></i>
                            <div data-i18n="Commandes en attente">{{ __('Commandes en attente') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.commandes.coupons.index' || Request::route()->getName() == 'super-admin.commandes.coupons.edit' || Request::route()->getName() == 'super-admin.commandes.coupons.show' ? 'active' : '' }}">
                        <a href="{{ route('super-admin.commandes.coupons.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-gift"></i>
                            <div data-i18n="Coupon">{{ __('Coupon') }}</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text" data-i18n="Paramètres">Paramètres</span>
                    </li>
                    <li class="menu-item {{ Request::route()->getName() == 'super-admin.settings.index' ? ' active' : '' }}">
                        <a href="{{ route('super-admin.settings.index') }}" class="menu-link">
                            <i class="menu-icon ti ti-settings"></i>
                            <div>Paramètres</div>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Layout Content -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <div class="search-input-wrapper" style="position: relative;">
                                    <input
                                        type="text"
                                        class="form-control form-control-sm border-0 shadow-none ps-5"
                                        placeholder="Rechercher..."
                                        id="globalSearch"
                                        style="width: 300px; background: rgba(255,255,255,0.1); color: darkblue; placeholder-color: rgba(255,255,255,0.7);"
                                        autocomplete="off"
                                    >
                                    <i class="ti ti-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7);"></i>

                                    <!-- Résultats de recherche -->
                                    <div class="search-results position-absolute bg-white shadow-lg border rounded mt-1" id="searchResults" style="display: none; width: 100%; max-height: 400px; overflow-y: auto; z-index: 1000;">
                                        <div class="p-3 text-muted" id="searchLoading" style="display: none;">
                                            <div class="d-flex align-items-center">
                                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                Recherche en cours...
                                            </div>
                                        </div>
                                        <div class="p-3 text-muted" id="searchNoResults" style="display: none;">
                                            Aucun résultat trouvé
                                        </div>
                                        <div id="searchResultsList"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Search -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Notifications -->
                            <li class="nav-item dropdown">
                                <button class="btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow bg-success text-white me-2"
                                        data-bs-toggle="dropdown" id="notificationDropdown">
                                    <i class="ti ti-bell"></i>
                                    <span class="badge bg-danger rounded-pill badge-notifications" id="notificationBadge" style="display: none;">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" id="notificationDropdownMenu">
                                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span class="me-4">Notifications</span>
                                        <a href="{{ route('notifications.index') }}" class="btn btn-sm bg-primary text-white">Voir tout</a>
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
                                            <i class="ti ti-eye me-2"></i>Voir les non lues
                                        </a>
                                    </div>
                                </div>
                            </li>

                            <!-- Profil utilisateur -->
                            <li class="nav-item dropdown">
                                <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @if(auth()->user()->avatar_url && file_exists(public_path(auth()->user()->avatar_url)))
                                            <img src="{{ asset(auth()->user()->avatar_url) }}"
                                                alt="Avatar" class="w-px-40 h-auto rounded-circle" />
                                        @else
                                            <div class="avatar-initial bg-primary rounded-circle w-px-40 h-px-40 d-flex align-items-center justify-content-center">
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
                                    <a href="{{ route('super-admin.profile.index') }}" class="dropdown-item">
                                        <i class="ti ti-user me-2"></i>
                                        <span>Mon Profil</span>
                                    </a>
                                    <a href="{{ route('super-admin.settings.index') }}" class="dropdown-item">
                                        <i class="ti ti-settings me-2"></i>
                                        <span>Paramètres</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="ti ti-logout me-2"></i>
                                            <span>Déconnexion</span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Content -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Fonction pour changer de thème
        function setTheme(themeName) {
            document.documentElement.setAttribute('data-theme', themeName);
            localStorage.setItem('selectedTheme', themeName);

            // Ajouter la classe correspondante
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
                                <i class="ti ti-bell-off text-muted" style="font-size: 2rem;"></i>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    @stack('scripts-external')
    @stack('scripts')
</body>
</html>
