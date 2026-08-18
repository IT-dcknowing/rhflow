<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Maintenance - RH Flow')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%,rgb(52, 71, 237) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        .maintenance-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1.5rem;
        }

        .maintenance-title {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .maintenance-message {
            color: #5a6c7d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .maintenance-time {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 2rem 0;
            color: #495057;
        }

        .btn-retry {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <!-- Logo -->
        <img src="{{ asset('img/logos/logo.png') }}" alt="RH Flow" class="logo">

        <!-- Icône de maintenance -->
        <div class="maintenance-icon">
            <i class="ti ti-tools"></i>
        </div>

        <!-- Titre -->
        <h1 class="maintenance-title">Maintenance en cours</h1>

        <!-- Message -->
        <p class="maintenance-message">
            {{ $message ?? 'Le site est actuellement en maintenance. Nous travaillons dur pour améliorer votre expérience.' }}
        </p>

        <!-- Temps estimé -->
        <div class="maintenance-time">
            <i class="ti ti-clock me-2"></i>
            <strong>Maintenance en cours...</strong><br>
            <small>Nous serons bientôt de retour. Merci pour votre patience.</small>
        </div>

        <!-- Bouton réessayer -->
        <button onclick="retryAccess()" class="btn btn-retry mb-4">
            <i class="ti ti-refresh me-2"></i>
            Réessayer
        </button>

        <!-- se deconnecter -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-info">
                <i class="ti ti-logout me-2"></i>
                Se deconnecter
            </button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function retryAccess() {
            // Recharger la page pour vérifier si la maintenance est terminée
            window.location.reload();
        }

        // Vérifier automatiquement toutes les 30 secondes si la maintenance est terminée
        setInterval(function() {
            fetch(window.location.href, {
                method: 'HEAD',
                cache: 'no-cache'
            })
            .then(response => {
                if (response.status === 200) {
                    // Si la page répond normalement, recharger
                    window.location.reload();
                }
            })
            .catch(error => {
                // Ignorer les erreurs de réseau
                console.log('Maintenance toujours en cours...');
            });
        }, 30000);
    </script>
</body>
</html>
