@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Erreur - RH Flow')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-bg: #0f172a;
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            overflow: hidden;
        }

        /* Abstract shapes background */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
            border-radius: 50%;
            filter: blur(80px);
            animation: float 20s infinite alternate ease-in-out;
        }

        .shape-1 { width: 500px; height: 500px; top: -100px; right: -100px; animation-delay: 0s; }
        .shape-2 { width: 600px; height: 600px; bottom: -200px; left: -200px; animation-delay: -5s; }
        .shape-3 { width: 300px; height: 300px; top: 50%; left: 10%; animation-delay: -10s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 100px) scale(1.1); }
        }

        .error-wrapper {
            max-width: 900px;
            width: 100%;
            text-align: center;
            z-index: 1;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            padding: 60px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .error-logo {
            margin-bottom: 40px;
        }

        .error-logo img {
            height: 50px;
            filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.3));
        }

        .icon-container {
            font-size: 8rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            line-height: 1;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.2));
            animation: pulse-icon 3s infinite ease-in-out;
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            color: white;
            margin: 0;
            line-height: 1;
            letter-spacing: -2px;
            opacity: 0.9;
        }

        .error-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin: 1.5rem 0 1rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 3rem;
            line-height: 1.6;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .btn-premium:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
            color: white;
            filter: brightness(1.1);
        }

        .btn-premium i {
            font-size: 0.9rem;
            transition: transform 0.3s;
        }

        .btn-premium:hover i {
            transform: translateX(-4px);
        }

        .footer-text {
            margin-top: 40px;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .glass-card {
                padding: 40px 20px;
            }
            .error-code { font-size: 3.5rem; }
            .error-title { font-size: 1.75rem; }
            .icon-container { font-size: 6rem; }
        }

        /* Debug Section Style (Modern) */
        .debug-info {
            margin-top: 3rem;
            text-align: left;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .debug-header {
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            transition: background 0.2s;
        }

        .debug-header:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .debug-content {
            padding: 20px;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            color: #fb7185;
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="error-wrapper animate__animated animate__fadeIn">
        <div class="error-logo">
            <img src="{{ asset('img/logos/logo.png') }}" alt="RH Flow">
        </div>

        <div class="glass-card">
            @yield('content')
        </div>

        <div class="footer-text">
            &copy; {{ date('Y') }} RH Flow - Système de Gestion Intelligente. Tous droits réservés.
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDebug() {
            const content = document.getElementById('debugContent');
            const icon = document.getElementById('debugIcon');
            if (content.style.display === 'block') {
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>