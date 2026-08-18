<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portail de Pointage - RH Flow</title>
    
    <!-- Icons & Typography -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap & Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #253e87;
            --accent: #00f2fe;
            --bg-gradient-start: #0c142c;
            --bg-gradient-end: #1e2c5a;
            --glass-bg: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-border-hover: rgba(255, 255, 255, 0.2);
            --text-bright: #ffffff;
            --text-muted: #a4b4cb;
            --success-green: #00db87;
            --danger-red: #ff4c60;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            margin: 0;
            color: var(--text-bright);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Animated blurred orbs for stunning premium aesthetics */
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            opacity: 0.4;
            animation: drift 20s infinite alternate ease-in-out;
        }
        .orb-1 {
            width: 300px;
            height: 300px;
            background: var(--primary);
            top: -50px;
            right: -50px;
        }
        .orb-2 {
            width: 250px;
            height: 250px;
            background: var(--accent);
            bottom: -50px;
            left: -50px;
            animation-duration: 15s;
        }

        @keyframes drift {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 40px) scale(1.2); }
        }

        .app-container {
            flex: 1;
            z-index: 1;
            display: flex;
            flex-direction: column;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 25px;
            margin-top: 15px;
        }

        .brand-logo {
            height: 45px;
            margin-bottom: 12px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        h1 {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
            background: linear-gradient(90deg, #ffffff 0%, #a4b4cb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 400;
            margin-top: 4px;
        }

        /* The Glassmorphism Card */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transition: border-color 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }

        .glass-card:hover {
            border-color: var(--glass-border-hover);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }

        /* Location Badge Card */
        .location-badge {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.03);
        }

        .loc-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, #2b5876 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            color: white;
            font-size: 1.1rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .loc-info {
            flex: 1;
        }

        .loc-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 2px;
        }

        .loc-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-bright);
        }

        .pulse-indicator {
            width: 8px;
            height: 8px;
            background: var(--success-green);
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(0, 219, 135, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 219, 135, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(0, 219, 135, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 219, 135, 0); }
        }

        /* Styled form group labels */
        .form-label-premium {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            display: block;
            letter-spacing: 0.5px;
        }

        /* Select2 Custom High-End Dark Theme styling override */
        .select2-container--default .select2-selection--single {
            background-color: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 16px !important;
            height: 56px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 12px !important;
            transition: all 0.3s;
        }
        .select2-container--default .select2-selection--single:hover,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary) !important;
            background-color: rgba(255,255,255,0.07) !important;
            box-shadow: 0 0 0 4px rgba(37, 62, 135, 0.2) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 54px !important;
            right: 12px !important;
        }
        .select2-dropdown {
            background-color: #162244 !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
            border-radius: 16px !important;
            box-shadow: 0 15px 30px rgba(0,0,0,0.5) !important;
            overflow: hidden;
            margin-top: 6px !important;
            backdrop-filter: blur(10px);
        }
        .select2-search__field {
            background-color: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 10px !important;
            color: white !important;
            padding: 8px 12px !important;
        }
        .select2-results__option {
            padding: 12px 16px !important;
            font-size: 0.95rem !important;
            color: #e2e8f0 !important;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary) !important;
            color: white !important;
        }
        .select2-results__option[aria-selected=true] {
            background-color: rgba(255,255,255,0.08) !important;
            color: white !important;
        }

        /* Action Buttons */
        .action-buttons {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .btn-premium {
            height: 60px;
            border-radius: 18px;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-premium i {
            margin-right: 10px;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .btn-premium:active {
            transform: scale(0.96);
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
        }

        .btn-check-in {
            background: linear-gradient(135deg, #00db87 0%, #00af6c 100%);
            box-shadow: 0 10px 25px rgba(0, 219, 135, 0.3);
        }
        .btn-check-in:hover {
            box-shadow: 0 12px 30px rgba(0, 219, 135, 0.45);
        }
        .btn-check-in:hover i {
            transform: translateY(-3px);
        }

        .btn-check-out {
            background: linear-gradient(135deg, #ff4c60 0%, #d63043 100%);
            box-shadow: 0 10px 25px rgba(255, 76, 96, 0.3);
        }
        .btn-check-out:hover {
            box-shadow: 0 12px 30px rgba(255, 76, 96, 0.45);
        }
        .btn-check-out:hover i {
            transform: translateY(3px);
        }

        /* Supervisor Footer Badge */
        .supervisor-footer {
            margin-top: auto;
            padding: 20px 0;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .badge-supervisor {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.04);
            padding: 8px 16px;
            border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.05);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
        }
        .badge-supervisor i {
            color: var(--accent);
            margin-right: 6px;
        }

        .logout-link {
            display: inline-block;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #ff4c60;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .logout-link:hover {
            opacity: 1;
        }

        /* SweetAlert custom */
        .swal2-popup-custom {
            background: #162244 !important;
            border-radius: 24px !important;
            color: #ffffff !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            backdrop-filter: blur(15px) !important;
        }
        .swal2-title-custom {
            color: #ffffff !important;
            font-family: 'Inter', sans-serif !important;
        }
        .swal2-confirm-custom {
            background-color: var(--primary) !important;
            border-radius: 12px !important;
            padding: 12px 28px !important;
            font-family: 'Inter', sans-serif !important;
        }
    </style>
</head>
<body>

    <!-- Background effects -->
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>

    <div class="app-container">
        
        <header>
            <img src="{{ asset('img/logos/logo.png') }}" alt="RH Flow Logo" class="brand-logo" onerror="this.src='https://dc-knowing.com/rhflow/img/logos/logo.png'">
            <h1>Pointage Collectif</h1>
            <div class="subtitle">Enregistrement rapide des présences</div>
        </header>

        <!-- Main glass interface -->
        <div class="glass-card">
            
            <!-- Active Location -->
            <div class="location-badge">
                <div class="loc-icon-box">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div class="loc-info">
                    <div class="loc-title">Lieu Actuel</div>
                    <div class="loc-name">{{ $location->name }}</div>
                </div>
                <div class="pulse-indicator"></div>
            </div>

            <form id="attendancePortalForm">
                <input type="hidden" id="location_id" name="location_id" value="{{ $location->id }}">
                
                <!-- Employee Select -->
                <div class="mb-3">
                    <label for="employee_id" class="form-label-premium">SÉLECTIONNER L'EMPLOYÉ</label>
                    <select class="form-control select2" id="employee_id" name="employee_id" required>
                        <option value="">Rechercher ou choisir un employé...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-name="{{ $emp->name }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Massive Touch Action Buttons -->
                <div class="action-buttons">
                    <button type="button" class="btn-premium btn-check-in btn-submit" data-type="entree">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        ENREGISTRER L'ENTRÉE
                    </button>

                    <button type="button" class="btn-premium btn-check-out btn-submit" data-type="sortie">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        ENREGISTRER LA SORTIE
                    </button>
                </div>
            </form>

        </div>

        <!-- Supervisor Info Footbar -->
        <div class="supervisor-footer">
            <div class="badge-supervisor">
                <i class="fa-solid fa-shield-halved"></i>
                Superviseur : <strong>&nbsp;{{ Auth::user()->name }}</strong>
            </div>
            <br>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">
                <i class="fa-solid fa-power-off me-1"></i> Déconnexion
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

    </div>

    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Setup AJAX header for CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize select2 with custom dropdown styling
            $('#employee_id').select2({
                width: '100%',
                dropdownParent: $('.glass-card')
            });

            // Handle Button Submits
            $('.btn-submit').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var type = $btn.data('type');
                var employeeId = $('#employee_id').val();
                var locationId = $('#location_id').val();
                var employeeName = $('#employee_id').find(':selected').data('name');

                // Form Validation check
                if (!employeeId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: 'Veuillez d\'abord sélectionner un employé dans la liste.',
                        background: '#162244',
                        color: '#ffffff',
                        confirmButtonColor: '#253e87',
                        customClass: {
                            popup: 'swal2-popup-custom',
                            title: 'swal2-title-custom',
                            confirmButton: 'swal2-confirm-custom'
                        }
                    });
                    return;
                }

                // Prepare visual loading state on the clicked button
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> EN COURS...');
                $('.btn-submit').not($btn).prop('disabled', true);

                // Execute Async Request
                $.ajax({
                    url: "{{ route('company.times.qrcode-pointage.portal.store') }}",
                    method: "POST",
                    data: {
                        employee_id: employeeId,
                        location_id: locationId,
                        type: type
                    },
                    success: function(response) {
                        // Audio feedback (optional/subtle)
                        if ('vibrate' in navigator) {
                            navigator.vibrate(75); // Subtle tactile vibration
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Validé !',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#162244',
                            color: '#ffffff',
                            customClass: {
                                popup: 'swal2-popup-custom',
                                title: 'swal2-title-custom'
                            }
                        });

                        // Reset selection so the user can pick next employee immediately
                        $('#employee_id').val(null).trigger('change');
                    },
                    error: function(xhr) {
                        var errorMessage = "Une erreur est survenue.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: errorMessage,
                            background: '#162244',
                            color: '#ffffff',
                            confirmButtonColor: '#253e87',
                            customClass: {
                                popup: 'swal2-popup-custom',
                                title: 'swal2-title-custom',
                                confirmButton: 'swal2-confirm-custom'
                            }
                        });
                    },
                    complete: function() {
                        // Restore Buttons
                        $btn.prop('disabled', false).html(originalHtml);
                        $('.btn-submit').prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>
</html>
