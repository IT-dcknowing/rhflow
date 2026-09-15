<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RH Flow - Gestion RH')</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: 'rgb(53, 149, 246)', secondary: '#64748b', accent: '#f59e0b', dark: '#1e293b' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        :root {
            --rh-primary: #001760;
            --rh-primary-rgb: 0, 23, 96;
            --rh-primary-light: rgba(0, 23, 96, 0.12);
            --rh-primary-ultra-light: rgba(0, 23, 96, 0.06);
            --rh-glow: rgba(0, 23, 96, 0.25);
            --rh-border: rgba(0, 23, 96, 0.08);
            --rh-accent: #0033A0;
            --rh-success: #10B981;
            --rh-warning: #F59E0B;
            --white: #FFFFFF;
            --off-white: #F8FAFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--rh-primary);
            overflow-x: hidden;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
            background: var(--white);
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(0, 23, 96, 0.03) 0%, transparent 50%),
                radial-gradient(ellipse 60% 80% at 80% 70%, rgba(0, 51, 160, 0.02) 0%, transparent 50%),
                radial-gradient(ellipse 100% 100% at 50% 0%, rgba(248, 250, 255, 0.8) 0%, transparent 50%);
            pointer-events: none;
        }

        #particles-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .hero-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1400px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr 1.15fr;
                gap: 4rem;
            }
        }

        /* ========== LEFT SIDE ========== */
        .hero-left {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .badge-container { display: flex; align-items: center; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--rh-primary-ultra-light);
            border: 1px solid var(--rh-border);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--rh-primary);
            animation: badgeFadeIn 0.6s ease-out;
        }

        .badge-dot {
            width: 6px; height: 6px;
            background: var(--rh-success);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes badgeFadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.3); }
        }

        .title-container { animation: titleSlideIn 0.8s ease-out 0.2s both; }

        @keyframes titleSlideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .title {
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--rh-primary);
        }

        .title-accent { position: relative; display: inline-block; }

        .title-accent::after {
            content: '';
            position: absolute;
            bottom: 0.1em;
            left: -0.05em;
            right: -0.05em;
            height: 0.3em;
            background: linear-gradient(90deg, rgba(0, 51, 160, 0.15), rgba(0, 23, 96, 0.08));
            border-radius: 4px;
            z-index: -1;
            animation: highlightExpand 0.6s ease-out 0.8s both;
        }

        @keyframes highlightExpand {
            from { transform: scaleX(0); transform-origin: left; }
            to { transform: scaleX(1); transform-origin: left; }
        }

        .subtitle {
            font-size: 1.125rem;
            line-height: 1.7;
            color: var(--gray-600);
            max-width: 480px;
            animation: subtitleFadeIn 0.8s ease-out 0.4s both;
        }

        @keyframes subtitleFadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cta-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            animation: ctaFadeIn 0.8s ease-out 0.6s both;
        }

        @keyframes ctaFadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--rh-primary) 0%, var(--rh-accent) 100%);
            color: var(--white);
            box-shadow: 0 4px 14px rgba(0, 23, 96, 0.25), 0 0 0 0 rgba(0, 23, 96, 0);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 23, 96, 0.35), 0 0 0 4px rgba(0, 23, 96, 0.1);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            background: var(--white);
            color: var(--rh-primary);
            border: 2px solid var(--rh-border);
            box-shadow: 0 2px 8px rgba(0, 23, 96, 0.06);
        }

        .btn-secondary:hover {
            border-color: var(--rh-primary);
            background: var(--rh-primary-ultra-light);
            transform: translateY(-2px);
        }

        .btn-icon { width: 20px; height: 20px; transition: transform 0.3s ease; }
        .btn-primary:hover .btn-icon { transform: translateX(4px); }

        .trust-indicators {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            animation: trustFadeIn 0.8s ease-out 0.8s both;
        }

        @keyframes trustFadeIn { from { opacity: 0; } to { opacity: 1; } }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .trust-icon { width: 16px; height: 16px; color: var(--rh-success); }

        /* ========== RIGHT SIDE - CARD CAROUSEL ========== */
        .hero-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            perspective: 1500px;
            animation: rightFadeIn 1s ease-out 0.3s both;
        }

        @keyframes rightFadeIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .card-glow {
            position: absolute;
            inset: -30px;
            background: radial-gradient(ellipse at center, rgba(0, 23, 96, 0.10) 0%, transparent 70%);
            border-radius: 40px;
            filter: blur(25px);
            animation: glowPulse 4s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.03); }
        }

        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.25);
            border-color: #3b82f6;
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .stats-counter {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .custom-checkbox {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
        }

        .custom-checkbox:hover input~.checkmark {
            border-color: #1e3a8a;
        }

        .custom-checkbox input:checked~.checkmark {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .custom-checkbox input:checked~.checkmark:after {
            display: block;
        }

        .custom-checkbox .checkmark:after {
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }

        .custom-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .4s;
            border-radius: 24px;
        }

        .switch-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.switch-slider {
            background-color: #1e3a8a;
        }

        input:checked+.switch-slider:before {
            transform: translateX(24px);
        }

        /* ========== NEW HERO STYLES ========== */
        .card-wrapper {
            position: relative;
            width: 100%;
            max-width: 620px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-glow {
            position: absolute;
            inset: -30px;
            background: radial-gradient(ellipse at center, rgba(0, 23, 96, 0.10) 0%, transparent 70%);
            border-radius: 40px;
            filter: blur(25px);
            animation: glowPulse 4s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.03); }
        }

        .card-3d {
            position: relative;
            width: 100%;
            background: var(--white);
            border-radius: 20px;
            box-shadow:
                0 4px 6px rgba(0, 23, 96, 0.02),
                0 12px 24px rgba(0, 23, 96, 0.05),
                0 32px 64px rgba(0, 23, 96, 0.08);
            border: 1px solid var(--rh-border);
            overflow: hidden;
            transform-style: preserve-3d;
            will-change: transform, box-shadow;
            animation: cardFloat 5s ease-in-out infinite;
        }

        @keyframes cardFloat {
            0%, 100% { transform: perspective(1000px) rotateY(-3deg) translateY(0px); }
            50%       { transform: perspective(1000px) rotateY(-2deg) translateY(-10px); }
        }

        .card-3d::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: radial-gradient(
                circle at var(--sheen-x, 50%) var(--sheen-y, 50%),
                rgba(255, 255, 255, 0.18) 0%,
                transparent 60%
            );
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 90;
        }

        .card-3d.is-hovering::after { opacity: 1; }

        .card-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            z-index: 95;
            pointer-events: none;
            animation: lightSweep 5s ease-in-out infinite;
        }

        @keyframes lightSweep {
            0%, 75% { left: -100%; }
            100% { left: 200%; }
        }

        .card-reflection {
            position: absolute;
            bottom: -60%;
            left: 5%;
            right: 5%;
            height: 60%;
            background: linear-gradient(to bottom, rgba(0, 23, 96, 0.03), transparent);
            transform: scaleY(-1) perspective(500px) rotateX(30deg);
            filter: blur(8px);
            opacity: 0.35;
            pointer-events: none;
            border-radius: 20px;
        }

        .screen-container {
            position: relative;
            width: 100%;
            min-height: 420px;
            height: clamp(380px, 40vw, 520px);
            overflow: hidden;
        }

        .screen {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: scale(0.96);
            transition: opacity 0.5s ease, transform 0.5s ease;
            padding: 1.75rem;
            background: var(--white);
            display: flex;
            flex-direction: column;
        }

        .screen.active {
            opacity: 1;
            transform: scale(1);
            z-index: 10;
        }

        .screen-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .screen-title { font-size: 1.05rem; font-weight: 700; color: var(--rh-primary); }
        .screen-badge {
            font-size: 0.75rem; font-weight: 500;
            padding: 0.25rem 0.75rem; border-radius: 9999px;
            background: var(--rh-primary-ultra-light); color: var(--rh-primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .stat-card {
            background: var(--off-white);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid var(--rh-border);
        }

        .stat-label { font-size: 0.75rem; color: var(--gray-500); margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--rh-primary); }
        .stat-change {
            font-size: 0.75rem; color: var(--rh-success);
            display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;
        }

        .chart-area {
            flex: 1;
            min-height: 0;
            background: var(--off-white);
            border-radius: 12px;
            padding: 1rem 1rem 0.5rem 1rem;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: visible;
        }

        .chart-svg-wrap {
            flex: 1;
            min-height: 0;
            position: relative;
        }

        .chart-svg-wrap svg {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .chart-grid line { stroke: rgba(0, 23, 96, 0.06); stroke-width: 1; }

        .chart-fill { opacity: 0; animation: chartFillIn 0.6s ease-out 1.6s forwards; }
        @keyframes chartFillIn { to { opacity: 1; } }

        .chart-line {
            fill: none;
            stroke: var(--rh-primary);
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 600;
            stroke-dashoffset: 600;
            animation: drawLine 1.5s ease-out 0.3s forwards;
        }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }

        .chart-point {
            fill: var(--white);
            stroke: var(--rh-primary);
            stroke-width: 2.5;
            transform: scale(0);
            cursor: pointer;
            transition: transform 0.15s ease, stroke-width 0.15s ease;
        }

        .chart-point.visible { animation: pointSpring 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .chart-point:hover { transform: scale(1.4) !important; stroke-width: 3; }
        @keyframes pointSpring {
            0% { transform: scale(0); }
            70% { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        .chart-labels {
            display: flex;
            justify-content: space-between;
            padding: 0.375rem 0.25rem 0;
            font-size: 0.6875rem;
            color: var(--gray-400);
            font-weight: 500;
        }

        .chart-tooltip {
            position: absolute;
            background: var(--white);
            border: 1px solid rgba(0, 23, 96, 0.12);
            border-radius: 8px;
            padding: 6px 12px;
            box-shadow: 0 4px 20px rgba(0, 23, 96, 0.15);
            font-size: 12px;
            color: var(--rh-primary);
            font-weight: 600;
            pointer-events: none;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            white-space: nowrap;
            z-index: 60;
        }

        .chart-tooltip.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .employee-list { display: flex; flex-direction: column; gap: 0.75rem; }

        .employee-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem; background: var(--off-white);
            border-radius: 10px; border: 1px solid var(--rh-border);
            animation: itemSlideIn 0.4s ease-out both;
        }

        .employee-item:nth-child(1) { animation-delay: 0.1s; }
        .employee-item:nth-child(2) { animation-delay: 0.2s; }
        .employee-item:nth-child(3) { animation-delay: 0.3s; }
        .employee-item:nth-child(4) { animation-delay: 0.4s; }

        @keyframes itemSlideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .employee-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--rh-primary-light), var(--rh-primary-ultra-light));
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 0.875rem; color: var(--rh-primary);
            flex-shrink: 0;
        }

        .employee-info { flex: 1; min-width: 0; }
        .employee-name { font-size: 0.875rem; font-weight: 600; color: var(--rh-primary); }
        .employee-role { font-size: 0.75rem; color: var(--gray-500); }
        .employee-status { width: 8px; height: 8px; border-radius: 50%; background: var(--rh-success); flex-shrink: 0; }

        .payroll-summary {
            background: linear-gradient(135deg, var(--rh-primary) 0%, var(--rh-accent) 100%);
            border-radius: 16px; padding: 1.25rem; color: var(--white); margin-bottom: 1rem;
        }

        .payroll-label { font-size: 0.875rem; opacity: 0.8; margin-bottom: 0.25rem; }
        .payroll-amount { font-size: 2rem; font-weight: 700; }

        .payroll-details {
            display: flex; gap: 1.5rem; margin-top: 0.75rem;
            padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2);
        }

        .payroll-detail { font-size: 0.75rem; }
        .payroll-detail-label { opacity: 0.7; }
        .payroll-detail-value { font-weight: 600; }

        .payroll-actions { display: flex; gap: 0.75rem; }

        .payroll-btn {
            flex: 1; padding: 0.75rem; border-radius: 10px;
            font-size: 0.875rem; font-weight: 600; cursor: pointer;
            border: none; transition: all 0.2s ease;
            position: relative; overflow: hidden; font-family: inherit;
        }

        .payroll-btn-primary { background: var(--rh-primary); color: var(--white); }
        .payroll-btn-secondary { background: var(--off-white); color: var(--rh-primary); border: 1px solid var(--rh-border); }
        .payroll-btn:hover { transform: translateY(-1px); }
        .payroll-btn.clicked { animation: btnClick 0.3s ease; }

        @keyframes btnClick {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }

        .bulletin-preview {
            background: var(--off-white);
            border-radius: 12px; border: 1px solid var(--rh-border); overflow: hidden;
        }

        .bulletin-header {
            background: var(--rh-primary); color: var(--white); padding: 1rem;
            display: flex; justify-content: space-between; align-items: center;
        }

        .bulletin-logo { font-weight: 700; font-size: 0.875rem; }
        .bulletin-period { font-size: 0.75rem; opacity: 0.8; }

        .bulletin-content { padding: 1rem; }

        .bulletin-row {
            display: flex; justify-content: space-between;
            padding: 0.5rem 0; font-size: 0.8rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .bulletin-row:last-child { border-bottom: none; font-weight: 700; color: var(--rh-primary); }
        .bulletin-row-label { color: var(--gray-600); }

        .progress-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
        }

        .progress-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(0, 23, 96, 0.20);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .progress-dot.active {
            width: 28px;
            border-radius: 3px;
            background: var(--rh-primary);
            box-shadow: 0 0 10px rgba(0, 23, 96, 0.35);
        }

        .floating-badge {
            position: absolute;
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(0, 23, 96, 0.09);
            border-radius: 14px;
            padding: 9px 13px;
            box-shadow:
                0 4px 24px rgba(0, 23, 96, 0.10),
                0 1px 4px rgba(0, 23, 96, 0.05),
                inset 0 1px 0 rgba(255,255,255,0.9);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            white-space: nowrap;
            pointer-events: none;
            z-index: 20;
            will-change: transform;
        }

        .fb-inner {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fb-icon { font-size: 14px; line-height: 1; flex-shrink: 0; }
        .fb-main { font-size: 11.5px; font-weight: 700; color: var(--rh-primary); line-height: 1.25; }
        .fb-sub  { font-size: 10px;   font-weight: 500; color: var(--gray-500);   line-height: 1.25; }

        .fb-its { top: -16px; right: 55px; animation: fbFadeIn 0.6s ease-out 1.1s both; }
        .fb-its .fb-inner { animation: fbFloat1 2.8s ease-in-out infinite; }

        .fb-masse { bottom: 23%; left: -16px; animation: fbFadeIn 0.6s ease-out 1.4s both; }
        .fb-masse .fb-inner { animation: fbFloat2 3.5s ease-in-out 0.4s infinite; }

        .fb-time { top: 37%; right: -16px; animation: fbFadeIn 0.6s ease-out 1.7s both; }
        .fb-time .fb-inner { animation: fbFloat3 4.2s ease-in-out 0.9s infinite; }

        .fb-syscohada { top: 20%; left: -16px; animation: fbFadeIn 0.6s ease-out 2.0s both; }
        .fb-syscohada .fb-inner { animation: fbFloat4 3.1s ease-in-out 1.3s infinite; }

        @keyframes fbFadeIn {
            from { opacity: 0; transform: scale(0.82) translateY(8px); }
            to   { opacity: 1; transform: scale(1)    translateY(0);   }
        }
        @keyframes fbFloat1 { 0%,100%{transform:translateY(0px)}   50%{transform:translateY(-7px)} }
        @keyframes fbFloat2 { 0%,100%{transform:translateY(-3px)}  50%{transform:translateY(6px)}  }
        @keyframes fbFloat3 { 0%,100%{transform:translateY(-5px)}  50%{transform:translateY(4px)}  }
        @keyframes fbFloat4 { 0%,100%{transform:translateY(0px)}   50%{transform:translateY(-9px)} }

        .fb-its .fb-icon { color: #10B981; }

        @media (max-width: 1280px) { .fb-masse, .fb-syscohada { display: none; } }
        @media (max-width: 1100px) { .fb-time  { display: none; } }

        .virtual-cursor {
            position: absolute; width: 20px; height: 20px;
            z-index: 100; pointer-events: none; opacity: 0;
            transition: opacity 0.3s ease;
        }

        .virtual-cursor.visible { opacity: 1; }
        .virtual-cursor svg { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }

        .sparkles-container {
            position: absolute; inset: 0;
            pointer-events: none; z-index: 50; overflow: hidden;
        }

        .sparkle {
            position: absolute;
            width: 8px; height: 8px;
            background: var(--rh-primary);
            border-radius: 50%; opacity: 0;
        }

        .sparkle.animate { animation: sparkle 0.8s ease-out forwards; }

        @keyframes sparkle {
            0% { opacity: 1; transform: scale(0) translate(0, 0); }
            100% { opacity: 0; transform: scale(1) translate(var(--tx), var(--ty)); }
        }

        @media (max-width: 1023px) {
            .hero { padding: 1.5rem; }
            .card-wrapper { max-width: 520px; }
            .screen-container { min-height: 360px; height: 400px; }
        }

        @media (max-width: 640px) {
            .screen-container { min-height: 340px; height: 380px; }
            .screen { padding: 1.25rem; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
    <script src="https://cdn.cinetpay.com/seamless/main.js" type="text/javascript"></script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TDCW7Z7RDV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-TDCW7Z7RDV');
    </script>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('landingpage.index') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('img/logos/logo-dark.png') }}" alt="RH-Flow" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">RH-Flow</span>
                    </a>
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="#fonctionnalites"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">Fonctionnalités</a>
                        <a href="#avantages"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">Avantages</a>
                        <a href="#temoignages"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">Témoignages</a>
                        <a href="#tarifs"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">Tarifs</a>
                        <a href="#contact"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">Contact</a>
                        <a href="{{ route('simulateur') }}"
                            class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors font-bold">Simulateur</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ \App\Http\Controllers\Auth\LoginController::dashboardUrl() }}"
                            class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-700/90 transition-colors font-medium">Mon
                            espace</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium hidden md:block">Se
                            connecter</a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-700/90 transition-colors font-medium">Essai
                            gratuit</a>
                    @endauth
                    <button class="md:hidden flex items-center justify-center w-10 h-10 text-gray-700"
                        id="mobile-menu-button">
                        <i class="ri-menu-line ri-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="hidden md:hidden bg-white border-t border-gray-100" id="mobile-menu">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="#fonctionnalites"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Fonctionnalités</a>
                <a href="#avantages"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Avantages</a>
                <a href="#temoignages"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Témoignages</a>
                <a href="#tarifs"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Tarifs</a>
                <a href="#contact"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Contact</a>
                <a href="{{ route('simulateur') }}"
                    class="block text-blue-700 hover:text-primary transition-colors font-bold py-2">Simulateur</a>
                <a href="{{ route('login') }}"
                    class="block text-gray-600 hover:text-primary transition-colors font-medium py-2">Se connecter</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <canvas id="particles-canvas"></canvas>
        <div class="hero-container">
            <!-- Left Side -->
            <div class="hero-left">
                <div class="badge-container">
                    <div class="badge">
                        <span class="badge-dot"></span>
                        <span>Nouveau: Application mobile employé disponible</span>
                    </div>
                </div>

                <div class="title-container">
                    <h1 class="title">
                        Gérez vos RH en toute <span class="title-accent">simplicité</span>
                    </h1>
                </div>

                <p class="subtitle">
                    Automatisez la paie, les congés et les déclarations ITS, CNPS et CMU.
                    Gain de temps garanti pour votre entreprise.
                </p>

                <div class="cta-container">
                    <a href="#" class="btn btn-primary">
                        Commencer gratuitement
                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        Demander une démo
                    </a>
                </div>

                <div class="trust-indicators">
                    <div class="trust-item">
                        <svg class="trust-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>14 jours d'essai gratuit</span>
                    </div>
                    <div class="trust-item">
                        <svg class="trust-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>Aucune carte requise</span>
                    </div>
                    <div class="trust-item">
                        <svg class="trust-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>Support 24/7</span>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hero-right">
                <div class="card-wrapper">
                    <div class="card-glow"></div>

                    <!-- Floating Badges -->
                    <div class="floating-badge fb-its">
                        <div class="fb-inner">
                            <span class="fb-icon">✓</span>
                            <div>
                                <div class="fb-main">Déclaration ITS</div>
                                <div class="fb-sub">envoyée</div>
                            </div>
                        </div>
                    </div>

                    <div class="floating-badge fb-masse">
                        <div class="fb-inner">
                            <span class="fb-icon">📊</span>
                            <div>
                                <div class="fb-main">+2.1%</div>
                                <div class="fb-sub">Masse sal.</div>
                            </div>
                        </div>
                    </div>

                    <div class="floating-badge fb-time">
                        <div class="fb-inner">
                            <span class="fb-icon">⚡</span>
                            <div>
                                <div class="fb-main">3 min</div>
                                <div class="fb-sub">Paie générée</div>
                            </div>
                        </div>
                    </div>

                    <div class="floating-badge fb-syscohada">
                        <div class="fb-inner">
                            <span class="fb-icon">🔒</span>
                            <div>
                                <div class="fb-main">100% conforme</div>
                                <div class="fb-sub">SYSCOHADA</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-3d" id="card3d">
                        <div class="screen-container">
                            <!-- Screen 1: Dashboard -->
                            <div class="screen active" data-screen="0">
                                <div class="screen-header">
                                    <span class="screen-title">Tableau de bord</span>
                                    <span class="screen-badge">Mai 2026</span>
                                </div>
                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="stat-label">Employés actifs</div>
                                        <div class="stat-value">127</div>
                                        <div class="stat-change">
                                            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            +3 ce mois
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-label">Masse salariale</div>
                                        <div class="stat-value">48.2M</div>
                                        <div class="stat-change">
                                            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            +2.1%
                                        </div>
                                    </div>
                                </div>
                                <!-- Chart -->
                                <div class="chart-area" id="chartArea">
                                    <div class="chart-svg-wrap" id="chartSvgWrap"></div>
                                    <div class="chart-labels">
                                        <span>Déc</span><span>Jan</span><span>Fév</span><span>Mar</span><span>Avr</span><span>Mai</span>
                                    </div>
                                    <div class="chart-tooltip" id="chartTooltip">
                                        <div class="chart-tooltip-month" id="tooltipMonth"></div>
                                        <div id="tooltipValue"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen 2: Employees -->
                            <div class="screen" data-screen="1">
                                <div class="screen-header">
                                    <span class="screen-title">Employés</span>
                                    <span class="screen-badge">127 actifs</span>
                                </div>
                                <div class="employee-list">
                                    <div class="employee-item">
                                        <div class="employee-avatar">AK</div>
                                        <div class="employee-info">
                                            <div class="employee-name">Aminata Kéné</div>
                                            <div class="employee-role">Directrice RH</div>
                                        </div>
                                        <div class="employee-status"></div>
                                    </div>
                                    <div class="employee-item">
                                        <div class="employee-avatar">KT</div>
                                        <div class="employee-info">
                                            <div class="employee-name">Kouadio Traoré</div>
                                            <div class="employee-role">Comptable Senior</div>
                                        </div>
                                        <div class="employee-status"></div>
                                    </div>
                                    <div class="employee-item">
                                        <div class="employee-avatar">FD</div>
                                        <div class="employee-info">
                                            <div class="employee-name">Fatou Diallo</div>
                                            <div class="employee-role">Chef de projet</div>
                                        </div>
                                        <div class="employee-status"></div>
                                    </div>
                                    <div class="employee-item">
                                        <div class="employee-avatar">YS</div>
                                        <div class="employee-info">
                                            <div class="employee-name">Yao Sanogo</div>
                                            <div class="employee-role">Développeur</div>
                                        </div>
                                        <div class="employee-status"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen 3: Payroll -->
                            <div class="screen" data-screen="2">
                                <div class="screen-header">
                                    <span class="screen-title">Paie - Mai 2026</span>
                                    <span class="screen-badge">En cours</span>
                                </div>
                                <div class="payroll-summary">
                                    <div class="payroll-label">Total à verser</div>
                                    <div class="payroll-amount">48 234 500 F</div>
                                    <div class="payroll-details">
                                        <div class="payroll-detail">
                                            <div class="payroll-detail-label">Salaires nets</div>
                                            <div class="payroll-detail-value">38.2M F</div>
                                        </div>
                                        <div class="payroll-detail">
                                            <div class="payroll-detail-label">Cotisations</div>
                                            <div class="payroll-detail-value">10.0M F</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payroll-actions">
                                    <button class="payroll-btn payroll-btn-primary" id="validateBtn">Valider la paie</button>
                                    <button class="payroll-btn payroll-btn-secondary" id="exportBtn">Exporter</button>
                                </div>
                            </div>

                            <!-- Screen 4: Bulletin -->
                            <div class="screen" data-screen="3">
                                <div class="screen-header">
                                    <span class="screen-title">Bulletin de paie</span>
                                    <span class="screen-badge">Généré</span>
                                </div>
                                <div class="bulletin-preview">
                                    <div class="bulletin-header">
                                        <span class="bulletin-logo">RH-Flow</span>
                                        <span class="bulletin-period">Mai 2026</span>
                                    </div>
                                    <div class="bulletin-content">
                                        <div class="bulletin-row">
                                            <span class="bulletin-row-label">Salaire de base</span>
                                            <span>450 000 F</span>
                                        </div>
                                        <div class="bulletin-row">
                                            <span class="bulletin-row-label">Primes</span>
                                            <span>75 000 F</span>
                                        </div>
                                        <div class="bulletin-row">
                                            <span class="bulletin-row-label">CNPS (-6.3%)</span>
                                            <span>-33 075 F</span>
                                        </div>
                                        <div class="bulletin-row">
                                            <span class="bulletin-row-label">ITS</span>
                                            <span>-28 500 F</span>
                                        </div>
                                        <div class="bulletin-row">
                                            <span>Net à payer</span>
                                            <span>463 425 F</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="sparkles-container" id="sparklesContainer"></div>
                            </div>

                            <!-- Virtual Cursor -->
                            <div class="virtual-cursor" id="virtualCursor">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path d="M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87c.48 0 .72-.58.38-.92L6.35 2.85a.5.5 0 0 0-.85.36Z" fill="#001760" stroke="#fff" stroke-width="1.5"/>
                                </svg>
                                <div class="cursor-click-ring" id="clickRing"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-reflection"></div>

                    <!-- Progress Dots -->
                    <div class="progress-dots" id="progressDots">
                        <div class="progress-dot active" data-index="0"></div>
                        <div class="progress-dot" data-index="1"></div>
                        <div class="progress-dot" data-index="2"></div>
                        <div class="progress-dot" data-index="3"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Features Section -->
    <section id="fonctionnalites" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-primary/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-star-line text-primary mr-2"></i>
                    <span class="text-primary font-semibold">Fonctionnalités</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tout ce dont vous avez besoin</h2>
                <p class="text-xl text-gray-600">Une solution complète pour gérer efficacement vos ressources humaines
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-700 to-blue-700 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-calendar-check-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Gestion des congés</h3>
                    <p class="text-gray-600 mb-6">Suivi automatique des demandes, validation en un clic et calendrier
                        partagé pour une vision claire des absences.</p>

                </div>

                <!-- Feature 2 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-file-text-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Paie et déclarations</h3>
                    <p class="text-gray-600 mb-6">Génération automatique des bulletins, déclarations CNPS/ITS/CMU au
                        format XML et export simplifié.</p>

                </div>

                <!-- Feature 3 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-team-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Gestion du personnel</h3>
                    <p class="text-gray-600 mb-6">Fiches collaborateurs complètes, suivi des performances, entretiens et
                        objectifs personnalisés.</p>

                </div>

                <!-- Feature 4 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-smartphone-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Application mobile</h3>
                    <p class="text-gray-600 mb-6">Accès complet sur mobile pour vos équipes. Demandes de congés,
                        consultation et notifications en temps réel.</p>

                </div>

                <!-- Feature 5 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-shield-check-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Sécurité avancée</h3>
                    <p class="text-gray-600 mb-6">Données hébergées en France, chiffrement de bout en bout et conformité
                        RGPD totale.</p>

                </div>

                <!-- Feature 6 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-customer-service-2-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Support dédié</h3>
                    <p class="text-gray-600 mb-6">Accompagnement personnalisé, formation des équipes et support
                        technique 7j/7 par téléphone et email.</p>

                </div>

                <!-- Feature 7 - Dashboard Analytics -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-bar-chart-box-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Tableau de bord analytique</h3>
                    <p class="text-gray-600 mb-6">Suivez vos indicateurs RH en temps réel avec des graphiques
                        interactifs et des rapports personnalisés.</p>

                </div>

                <!-- Feature 8 - Document Management -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ri-file-cloud-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Gestion documentaire</h3>
                    <p class="text-gray-600 mb-6">Centralisez contrats, bulletins de paie et documents RH avec accès
                        sécurisé et archivage automatique.</p>

                </div>
            </div>
        </div>
    </section>



    <!-- Why Choose Us Section -->
    <section id="avantages" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-accent/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-award-line text-accent mr-2"></i>
                    <span class="text-accent font-semibold">Avantages</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Pourquoi les entreprises nous choisissent
                </h2>
                <p class="text-xl text-gray-600">Découvrez ce qui rend RH-Flow unique sur le marché ivoirien</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Advantage 1 -->
                <div class="text-center group">
                    <div
                        class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-700 to-blue-700 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="ri-layout-masonry-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Interface intuitive</h3>
                    <p class="text-gray-600">Design moderne et ergonomique, formation des équipes en moins d'une heure.
                    </p>
                </div>

                <!-- Advantage 2 -->
                <div class="text-center group">
                    <div
                        class="w-20 h-20 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="ri-shield-check-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">100% sécurisé</h3>
                    <p class="text-gray-600">Protection optimale de vos données avec un hébergement exclusivement en
                        France.</p>
                </div>

                <!-- Advantage 3 -->
                <div class="text-center group">
                    <div
                        class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="ri-headphone-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Support premium</h3>
                    <p class="text-gray-600">Accompagnement personnalisé et support technique 7j/7 inclus dans tous les
                        plans.</p>
                </div>

                <!-- Advantage 4 -->
                <div class="text-center group">
                    <div
                        class="w-20 h-20 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="ri-plug-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Intégration simple</h3>
                    <p class="text-gray-600">Connectez vos outils existants et importez vos données en quelques clics.
                    </p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-xl p-8 md:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center bg-green-100 rounded-full px-4 py-2 mb-6">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-green-700 font-semibold">Résultats prouvés</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                            Une transformation digitale réussie
                        </h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center">
                                <i class="ri-check-double-line text-green-500 text-xl mr-3"></i>
                                <span class="text-gray-700">75% de temps économisé sur les tâches administratives</span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-check-double-line text-green-500 text-xl mr-3"></i>
                                <span class="text-gray-700">100% de conformité avec les réglementations
                                    ivoiriennes</span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-check-double-line text-green-500 text-xl mr-3"></i>
                                <span class="text-gray-700">Satisfaction client </span>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#tarifs"
                                class="bg-primary text-white px-8 py-4 rounded-button hover:bg-primary/90 transition-colors font-bold text-center shadow-lg">
                                Démarrer maintenant
                            </a>
                            <a href="#contact"
                                class="bg-transparent border-2 border-primary text-primary px-8 py-4 rounded-button hover:bg-primary hover:text-white transition-colors font-bold text-center">
                                Contacter un expert
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <img src="{{ asset('images/banner-rh/banner-mobil-2.png') }}" alt="Application RH-Flow"
                            class="rounded-2xl shadow-2xl w-full">
                        <div
                            class="absolute -top-4 -right-4 bg-accent text-white rounded-xl shadow-lg p-3 hidden lg:block">
                            <div class="flex items-center space-x-2">
                                <i class="ri-star-fill text-yellow-300"></i>
                                <span class="font-bold">4.9/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="temoignages" class="py-16 md:py-24 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-primary/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-heart-3-line text-primary mr-2"></i>
                    <span class="text-primary font-semibold">Témoignages</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ce que nos clients disent de nous</h2>
                <p class="text-xl text-gray-600">Découvrez les expériences réelles des entreprises qui nous font
                    confiance</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Testimonial -->
                <div
                    class="lg:col-span-2 bg-gradient-to-br from-blue-700 to-blue-700/90 text-white p-10 rounded-3xl shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>

                    <div class="relative z-10">
                        <div class="flex items-center mb-6">
                            <div class="text-amber-300 flex">
                                <i class="ri-star-fill text-2xl"></i>
                                <i class="ri-star-fill text-2xl"></i>
                                <i class="ri-star-fill text-2xl"></i>
                                <i class="ri-star-fill text-2xl"></i>
                                <i class="ri-star-fill text-2xl"></i>
                            </div>
                            <span class="ml-4 text-white/80 font-medium">5.0/5.0</span>
                        </div>

                        <blockquote class="text-2xl font-medium mb-8 leading-relaxed">
                            "RH-Flow a transformé complètement notre façon de gérer les RH. L'automatisation des
                            processus nous a fait économiser 15h par semaine et nos collaborateurs sont beaucoup plus
                            satisfaits."
                        </blockquote>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-xl">FB</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xl text-white">Franck Bakary</h4>
                                    <p class="text-white/80">Responsable RH, B-Home</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Side Testimonials -->
                <div class="space-y-6">
                    <div
                        class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center mb-4">
                            <div class="text-amber-400 flex">
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-4 leading-relaxed">"L'application mobile est géniale ! Je peux tout
                            gérer depuis mon téléphone."</p>
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">BK</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Béatrice Kouakou</h4>
                                <p class="text-gray-500 text-sm">Comptable, La Fabrique</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center mb-4">
                            <div class="text-amber-400 flex">
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                                <i class="ri-star-fill text-lg"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-4 leading-relaxed">"Le support client est exceptionnel, toujours
                            disponible pour nous aider."</p>
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">DK</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Djissa Kouamé</h4>
                                <p class="text-gray-500 text-sm">Responsable RH, Leader</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>

    <!-- Pricing Section -->
    <section id="tarifs" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-accent/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-price-tag-3-line text-accent mr-2"></i>
                    <span class="text-accent font-semibold">Tarifs</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Des plans adaptés à votre croissance</h2>
                <p class="text-xl text-gray-600 mb-4">Choisissez la formule idéale pour votre entreprise</p>

                <!-- Message de réduction pour paiement annuel -->
                <div id="yearly-discount-message"
                    class="hidden bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="ri-gift-line text-green-600 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-800 font-semibold">Économisez jusqu'à 10% !</p>
                            <p class="text-green-600 text-sm">Profitez d'une réduction exclusive avec les abonnements
                                annuels</p>
                        </div>
                    </div>
                </div>

                <!-- Boutons de filtrage Mois/Annuel -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
                    <button onclick="showMonthlyPlans()" id="monthly-btn"
                        class="bg-blue-700 text-white px-8 py-3 rounded-lg hover:bg-blue-800 transition-colors font-medium">
                        <i class="ri-calendar-line mr-2"></i>
                        Mois
                    </button>
                    <button onclick="showYearlyPlans()" id="yearly-btn"
                        class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        <i class="ri-calendar-2-line mr-2"></i>
                        Annuel
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" id="plans-container">
                @foreach ($plans as $pack)
                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col relative @if($pack->popular == 1) ring-2 ring-primary ring-offset-4 @endif"
                        data-price="{{ $pack->price }}" data-price-yearly="{{ $pack->price_yearly }}"
                        data-id="{{ $pack->id }}">
                        @if($pack->popular == 1)
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                <div class="bg-primary text-white px-4 py-2 rounded-full text-sm font-bold">
                                    Plus populaire
                                </div>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{$pack->name}}</h3>
                        </div>

                        <div class="mb-6">
                            @if($pack->id == 100 || $pack->id == 102)
                                <div class="text-3xl font-bold text-gray-900">Sur mesure</div>
                                <p class="text-gray-600 mt-2">Contactez-nous pour un devis personnalisé</p>
                            @else
                                <div class="price-display">
                                    <div class="text-4xl font-bold text-gray-900 mb-2">
                                        <span class="monthly-price">{{number_format($pack->price, 0, ',', ' ')}} FCFA</span>
                                        <span class="yearly-price"
                                            style="display:none;">{{number_format($pack->price_yearly, 0, ',', ' ')}}
                                            FCFA</span>
                                        <span class="price-period monthly-period text-lg text-gray-500 font-normal">/mois</span>
                                        <span class="price-period yearly-period text-lg text-gray-500 font-normal"
                                            style="display:none;">/an</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow">
                            @php
                                // Les limites ne sont pas affichées ailleurs sur cette carte : on garde « X salariés max », sans les « + »
                                $features = collect($pack->features ?? [])->map(fn ($f) => trim(preg_replace('/\s*\+\s*/u', ' ', (string) $f)))->filter();
                            @endphp
                            @foreach ($features as $feature)
                                <li class="flex items-start">
                                    <i class="ri-check-double-line text-green-500 mr-3 text-xl flex-shrink-0"></i>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-auto">
                            @if($pack->id == 100 || $pack->id == 102)
                                <a href="#contact"
                                    class="w-full bg-gray-100 text-gray-900 px-6 py-4 rounded-button hover:bg-gray-200 transition-colors font-bold text-center block">
                                    Nous contacter
                                </a>
                            @elseif($pack->price == 0.00)
                                <a href="{{ route('register', ['plan_id' => $pack->id]) }}"
                                    class="w-full bg-blue-700 text-white px-6 py-4 rounded-button hover:bg-green-600 transition-colors font-bold text-center block shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    Commencer
                                </a>
                            @else
                                <a href="{{ route('register', ['plan_id' => $pack->id]) }}"
                                    class="w-full bg-blue-700 text-white px-6 py-4 rounded-button hover:bg-blue-700/90 transition-colors font-bold text-center block shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    Essayer gratuitement
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="demo"
        class="py-16 md:py-24 bg-gradient-to-br from-blue-700 via-blue-700 to-blue-700 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    <i class="ri-rocket-2-line mr-2"></i>
                    <span class="font-medium">Démarrage rapide</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-bold mb-6 leading-tight">
                    Prêt à transformer votre gestion RH ?
                </h2>
                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
                    Rejoignez les 500+ entreprises qui font déjà confiance à RH-Flow pour optimiser leurs processus RH
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('register') }}"
                        class="bg-white text-primary px-8 py-4 rounded-button hover:bg-gray-100 transition-all duration-300 font-bold text-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Commencer l'essai gratuit
                    </a>
                    <a href="#contact"
                        class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-button hover:bg-white hover:text-primary transition-all duration-300 font-bold text-center">
                        Planifier une démo
                    </a>
                </div>
                <div class="flex items-center justify-center space-x-8 text-white/80">
                    <div class="flex items-center">
                        <i class="ri-check-line text-green-400 mr-2"></i>
                        <span class="text-sm">14 jours d'essai</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-check-line text-green-400 mr-2"></i>
                        <span class="text-sm">Aucune carte requise</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-check-line text-green-400 mr-2"></i>
                        <span class="text-sm">Support 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-primary/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-question-line text-primary mr-2"></i>
                    <span class="text-primary font-semibold">FAQ</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Questions fréquentes</h2>
                <p class="text-xl text-gray-600">Tout ce que vous devez savoir sur RH-Flow</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <div class="faq-item bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <button
                            class="faq-button w-full px-8 py-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 text-lg">RH-Flow est-il adapté aux entreprises
                                ivoiriennes ?</span>
                            <i
                                class="ri-arrow-down-s-line text-2xl text-gray-500 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-8 pb-6">
                            <p class="text-gray-600 leading-relaxed">Oui, RH-Flow est spécifiquement conçu pour le
                                contexte ivoirien. Nous intégrons les réglementations locales (CNPS, ITS, CMU) et
                                adaptons nos fonctionnalités aux besoins spécifiques des entreprises en Côte d'Ivoire.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <button
                            class="faq-button w-full px-8 py-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 text-lg">Combien de temps faut-il pour
                                l'implémentation ?</span>
                            <i
                                class="ri-arrow-down-s-line text-2xl text-gray-500 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-8 pb-6">
                            <p class="text-gray-600 leading-relaxed">L'implémentation est très rapide ! En moyenne, nos
                                clients sont opérationnels en moins de 48h. Nous vous accompagnons dans l'import de vos
                                données et la formation de vos équipes.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <button
                            class="faq-button w-full px-8 py-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 text-lg">Mes données sont-elles sécurisées ?</span>
                            <i
                                class="ri-arrow-down-s-line text-2xl text-gray-500 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-8 pb-6">
                            <p class="text-gray-600 leading-relaxed">Absolument. Vos données sont hébergées en France,
                                chiffrées de bout en bout et sauvegardées quotidiennement. Nous sommes conformes au RGPD
                                et aux réglementations ivoiriennes sur la protection des données.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <button
                            class="faq-button w-full px-8 py-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 text-lg">Puis-je importer mes données existantes
                                ?</span>
                            <i
                                class="ri-arrow-down-s-line text-2xl text-gray-500 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-8 pb-6">
                            <p class="text-gray-600 leading-relaxed">Oui, RH-Flow permet d'importer facilement vos
                                données depuis Excel, CSV ou d'autres logiciels RH. Notre équipe vous assiste
                                gratuitement dans le processus de migration.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <button
                            class="faq-button w-full px-8 py-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="font-semibold text-gray-900 text-lg">Quel type de support est offert ?</span>
                            <i
                                class="ri-arrow-down-s-line text-2xl text-gray-500 transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-8 pb-6">
                            <p class="text-gray-600 leading-relaxed">Nous offrons un support complet : formation
                                initiale, assistance technique 7j/7, documentation détaillée et webinaires mensuels. Le
                                support est inclus dans tous nos plans sans frais supplémentaires.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center bg-primary/10 rounded-full px-4 py-2 mb-6">
                    <i class="ri-mail-line text-primary mr-2"></i>
                    <span class="text-primary font-semibold">Contact</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Contactez-nous</h2>
                <p class="text-xl text-gray-600">Notre équipe est à votre écoute pour répondre à toutes vos questions
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                <!-- Contact Form -->
                <div
                    class="bg-gradient-to-br from-primary to-primary/90 text-white rounded-3xl p-10 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold mb-8">Envoyez-nous un message</h3>
                        <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-white/80 font-medium mb-2">Nom complet</label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                    placeholder="Votre nom">
                            </div>

                            <div>
                                <label for="email" class="block text-white/80 font-medium mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                    placeholder="votre@email.com">
                            </div>

                            <div>
                                <label for="company" class="block text-white/80 font-medium mb-2">Entreprise</label>
                                <input type="text" id="company" name="company"
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                    placeholder="Nom de votre entreprise">
                            </div>

                            <div>
                                <label for="message" class="block text-white/80 font-medium mb-2">Message</label>
                                <textarea id="message" name="message" rows="4" required
                                    class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                    placeholder="Décrivez votre besoin..."></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-white text-primary px-6 py-4 rounded-lg hover:bg-gray-100 transition-colors font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Informations de contact</h3>

                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="ri-phone-line text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Téléphone</h4>
                                    <p class="text-gray-600">+225 07 67 13 19 93</p>
                                    <p class="text-gray-500 text-sm">Lun-Ven 9h-18h</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div
                                    class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="ri-mail-line text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Email</h4>
                                    <p class="text-gray-600">infos@dcknowing.com</p>
                                    <p class="text-gray-500 text-sm">Réponse sous 24h</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div
                                    class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="ri-map-pin-line text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Adresse</h4>
                                    <p class="text-gray-600">Abidjan, Cocody</p>
                                    <p class="text-gray-500 text-sm">Riviera Bonoumin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-accent/10 to-accent/5 rounded-2xl p-8 border border-accent/20">
                        <h4 class="font-bold text-gray-900 mb-4 text-xl">Besoin d'une démo ?</h4>
                        <p class="text-gray-600 mb-6">Planifiez une démonstration personnalisée avec l'un de nos experts
                            RH et découvrez comment RH-Flow peut transformer votre gestion.</p>
                        <a href="#demo"
                            class="inline-flex items-center bg-accent text-white px-6 py-3 rounded-lg hover:bg-accent/90 transition-colors font-bold">
                            <span>Demander une démo</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="{{ asset('img/logos/logo-light.png') }}" width="120px" alt="RH-Flow"
                            class="h-10 w-auto">
                        <span class="text-2xl font-bold text-white">RH-Flow</span>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        La solution RH complète pour les entreprises ivoiriennes. Automatisez, optimisez et transformez
                        votre gestion des ressources humaines avec notre plateforme moderne et intuitive.
                    </p>

                    <div class="bg-primary/10 rounded-xl p-4 border border-primary/20">
                        <div class="flex items-center">
                            <i class="ri-phone-line text-primary mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold text-white">Support client</p>
                                <p class="text-gray-300">+225 07 67 13 19 93</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div>
                    <h3 class="text-lg font-bold mb-6 text-white">Produit</h3>
                    <ul class="space-y-3">
                        <li><a href="#fonctionnalites"
                                class="text-gray-300 hover:text-primary transition-colors">Fonctionnalités</a></li>
                        <li><a href="#tarifs" class="text-gray-300 hover:text-primary transition-colors">Tarifs</a></li>
                        <li><a href="#temoignages"
                                class="text-gray-300 hover:text-primary transition-colors">Témoignages</a></li>
                        <li><a href="#faq" class="text-gray-300 hover:text-primary transition-colors">FAQ</a></li>
                        <li><a href="#demo" class="text-gray-300 hover:text-primary transition-colors">Démo</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="text-lg font-bold mb-6 text-white">Ressources</h3>
                    <ul class="space-y-3">
                        <li><a href="{{route('privacy') }}"
                                class="text-gray-300 hover:text-primary transition-colors">Confidentialité</a></li>
                        <li><a href="#avantages"
                                class="text-gray-300 hover:text-primary transition-colors">Avantages</a></li>
                        <li><a href="{{route('simulateur') }}"
                                class="text-gray-300 hover:text-primary transition-colors">Simulateur</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-primary transition-colors">Contact</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-primary transition-colors">Blog</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col lg:flex-row justify-between items-center">
                    <div class="mb-4 lg:mb-0">
                        <p class="text-gray-400">© 2025 RH-Flow. Tous droits réservés.</p>
                        <p class="text-gray-500 text-sm mt-1">Solution RH pour les entreprises ivoiriennes</p>
                    </div>
                    <div class="flex flex-wrap items-center space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Mentions légales</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Politique de
                            confidentialité</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">CGU</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Paiement sécurisé</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function showMonthlyPlans() {
            const plans = document.querySelectorAll('#plans-container > div');
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const discountMessage = document.getElementById('yearly-discount-message');

            // Mettre à jour les styles des boutons
            monthlyBtn.className = 'bg-blue-700 text-white px-8 py-3 rounded-lg hover:bg-blue-800 transition-colors font-medium';
            yearlyBtn.className = 'bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium';

            // Masquer le message de réduction
            discountMessage.classList.add('hidden');

            // Afficher/masquer les plans selon le prix mensuel
            plans.forEach(plan => {
                const planPrice = parseFloat(plan.dataset.price || '0');
                const planId = parseInt(plan.dataset.id || '0');

                // Afficher tous les plans sauf Pro Master (150000) et Pro Day (600000) pour le mois
                if (planPrice === 0 || planPrice === 5000 || planPrice === 10000 || planPrice === 20000 || planPrice === 35000 || planPrice === 50000) {
                    plan.style.display = 'flex';
                    plan.style.animation = 'fadeIn 0.3s ease-in-out';

                    // Afficher uniquement les prix mensuels
                    const monthlyPrices = plan.querySelectorAll('.monthly-price');
                    const yearlyPrices = plan.querySelectorAll('.yearly-price');
                    const monthlyPeriods = plan.querySelectorAll('.monthly-period');
                    const yearlyPeriods = plan.querySelectorAll('.yearly-period');

                    monthlyPrices.forEach(el => el.style.display = 'inline');
                    yearlyPrices.forEach(el => el.style.display = 'none');
                    monthlyPeriods.forEach(el => el.style.display = 'inline');
                    yearlyPeriods.forEach(el => el.style.display = 'none');
                } else {
                    plan.style.display = 'none';
                }
            });
        }

        function showYearlyPlans() {
            const plans = document.querySelectorAll('#plans-container > div');
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const discountMessage = document.getElementById('yearly-discount-message');

            // Mettre à jour les styles des boutons
            yearlyBtn.className = 'bg-blue-700 text-white px-8 py-3 rounded-lg hover:bg-blue-800 transition-colors font-medium';
            monthlyBtn.className = 'bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium';

            // Afficher le message de réduction
            discountMessage.classList.remove('hidden');
            discountMessage.style.animation = 'fadeIn 0.3s ease-in-out';

            // Afficher/masquer les plans selon le prix annuel
            plans.forEach(plan => {
                const planPrice = parseFloat(plan.dataset.price || '0');
                const planId = parseInt(plan.dataset.id || '0');
                const planPriceYearly = parseFloat(plan.dataset.priceYearly || '0');

                // Afficher tous les plans y compris Pro Master et Pro Day pour l'année
                if (planPriceYearly > 0 || planPrice === 0) {
                    plan.style.display = 'flex';
                    plan.style.animation = 'fadeIn 0.3s ease-in-out';

                    // Afficher uniquement les prix annuels
                    const monthlyPrices = plan.querySelectorAll('.monthly-price');
                    const yearlyPrices = plan.querySelectorAll('.yearly-price');
                    const monthlyPeriods = plan.querySelectorAll('.monthly-period');
                    const yearlyPeriods = plan.querySelectorAll('.yearly-period');

                    monthlyPrices.forEach(el => el.style.display = 'none');
                    yearlyPrices.forEach(el => el.style.display = 'inline');
                    monthlyPeriods.forEach(el => el.style.display = 'none');
                    yearlyPeriods.forEach(el => el.style.display = 'inline');
                } else {
                    plan.style.display = 'none';
                }
            });
        }

        // Initialiser l'affichage mensuel au chargement
        document.addEventListener('DOMContentLoaded', function () {
            showMonthlyPlans();
        });

        // Ajouter les styles d'animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const menuButton = document.querySelector('.md\\:hidden');
            const mobileMenu = document.createElement('div');
            mobileMenu.className = 'fixed inset-0 bg-white z-50 transform translate-x-full transition-transform duration-300 ease-in-out';
            mobileMenu.innerHTML = `
                <div class="flex justify-between items-center p-4 border-b">
                    <span class="text-primary font-['Pacifico'] text-2xl">RH-Flow</span>
                    <button class="w-10 h-10 flex items-center justify-center text-gray-700">
                        <i class="ri-close-line ri-xl"></i>
                    </button>
                </div>
                <nav class="p-4">
                    <ul class="space-y-4">
                        <li><a href="#fonctionnalites" class="block py-2 text-gray-700 hover:text-primary font-medium">Fonctionnalités</a></li>
                        <li><a href="#avantages" class="block py-2 text-gray-700 hover:text-primary font-medium">Avantages</a></li>
                        <li><a href="#tarifs" class="block py-2 text-gray-700 hover:text-primary font-medium">Tarifs</a></li>
                        <li><a href="#contact" class="block py-2 text-gray-700 hover:text-primary font-medium">Contact</a></li>
                        <li class="pt-4 border-t"><a href="{{ route('login') }}" class="block py-2 text-gray-700 hover:text-primary font-medium">Se connecter</a></li>
                        <li><a href="#contact" class="block py-2 bg-primary text-white px-6 py-2 rounded-button text-center">Demander une démo</a></li>
                    </ul>
                </nav>
            `;
            document.body.appendChild(mobileMenu);

            menuButton.addEventListener('click', function () {
                mobileMenu.classList.remove('translate-x-full');
            });

            const closeButton = mobileMenu.querySelector('button');
            closeButton.addEventListener('click', function () {
                mobileMenu.classList.add('translate-x-full');
            });

            // Close mobile menu when clicking on links
            const mobileLinks = mobileMenu.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function () {
                    mobileMenu.classList.add('translate-x-full');
                });
            });
        });
        function checkout() {
            CinetPay.setConfig({
                apikey: '291038086662625fc7026f9.57751076',//   YOUR APIKEY
                site_id: '5871268',//YOUR_SITE_ID
                notify_url: 'https://dc-knowing.com/RH-Flow/',
                mode: 'PRODUCTION'
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(), // YOUR TRANSACTION ID
                amount: 5000,
                currency: 'XOF',
                channels: 'MOBILE_MONEY',
                description: 'Paiement du pack BASIC',
            });
            CinetPay.waitResponse(function (data) {
                if (data.status == "REFUSED") {
                    if (alert("Votre paiement a échoué")) {
                        window.location.reload();
                    }
                } else if (data.status == "ACCEPTED") {
                    if (alert("Votre paiement a été effectué avec succès")) {
                        window.location.href = "https://dc-knowing.com/RH-Flow/register/2";
                    }
                }
            });
            CinetPay.onError(function (data) {
                console.log(data);
            });
        }
        function checkout1() {
            CinetPay.setConfig({
                apikey: '291038086662625fc7026f9.57751076',//   YOUR APIKEY
                site_id: '5871268',//YOUR_SITE_ID
                notify_url: 'https://dc-knowing.com/RH-Flow/',
                mode: 'PRODUCTION'
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(), // YOUR TRANSACTION ID
                amount: 10000,
                currency: 'XOF',
                channels: 'MOBILE_MONEY',
                description: 'Paiement du pack PRO',
            });
            CinetPay.waitResponse(function (data) {
                if (data.status == "REFUSED") {
                    if (alert("Votre paiement a échoué")) {
                        window.location.reload();
                    }
                } else if (data.status == "ACCEPTED") {
                    if (alert("Votre paiement a été effectué avec succès")) {
                        window.location.href = "https://dc-knowing.com/RH-Flow/register/3";
                    }
                }
            });
            CinetPay.onError(function (data) {
                console.log(data);
            });
        }
        function checkout2() {
            CinetPay.setConfig({
                apikey: '291038086662625fc7026f9.57751076',//   YOUR APIKEY
                site_id: '5871268',//YOUR_SITE_ID
                notify_url: 'https://dc-knowing.com/RH-Flow/',
                mode: 'PRODUCTION'
            });
            CinetPay.getCheckout({
                transaction_id: Math.floor(Math.random() * 100000000).toString(), // YOUR TRANSACTION ID
                amount: 50000,
                currency: 'XOF',
                channels: 'MOBILE_MONEY',
                description: 'Paiement du pack PRO MAX',

            });
            CinetPay.waitResponse(function (data) {
                if (data.status == "REFUSED") {
                    if (alert("Votre paiement a échoué")) {
                        window.location.reload();
                    }
                } else if (data.status == "ACCEPTED") {
                    if (alert("Votre paiement a été effectué avec succès")) {
                        window.location.href = "https://dc-knowing.com/RH-Flow/register/8";
                    }
                }
            });
            CinetPay.onError(function(data) {
                console.log(data);
            });
        }

        // ========== NEW HERO SCRIPTS ==========

        // PARTICLES SYSTEM
        (function() {
            var canvas = document.getElementById('particles-canvas');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var particles = [];

            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }

            function createParticles() {
                particles = [];
                for (var i = 0; i < 6; i++) {
                    particles.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        size: Math.random() * 3 + 1,
                        speedX: (Math.random() - 0.5) * 0.3,
                        speedY: (Math.random() - 0.5) * 0.3,
                        opacity: Math.random() * 0.05 + 0.03
                    });
                }
            }

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(function(p) {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(0, 23, 96, ' + p.opacity + ')';
                    ctx.fill();
                    p.x += p.speedX;
                    p.y += p.speedY;
                    if (p.x < 0) p.x = canvas.width;
                    if (p.x > canvas.width) p.x = 0;
                    if (p.y < 0) p.y = canvas.height;
                    if (p.y > canvas.height) p.y = 0;
                });
                requestAnimationFrame(draw);
            }

            window.addEventListener('resize', function() { resize(); createParticles(); });
            resize();
            createParticles();
            draw();
        })();

        // SVG LINE CHART
        (function() {
            var chartData = [
                { month: 'Dec 2025', label: 'Dec', value: 38.2 },
                { month: 'Jan 2026', label: 'Jan', value: 40.1 },
                { month: 'Fev 2026', label: 'Fev', value: 41.8 },
                { month: 'Mar 2026', label: 'Mar', value: 43.5 },
                { month: 'Avr 2026', label: 'Avr', value: 45.9 },
                { month: 'Mai 2026', label: 'Mai', value: 48.2 }
            ];

            var wrap = document.getElementById('chartSvgWrap');
            if (!wrap) return;
            var tooltip = document.getElementById('chartTooltip');
            var tooltipMonth = document.getElementById('tooltipMonth');
            var tooltipValue = document.getElementById('tooltipValue');

            function buildChart() {
                var rect = wrap.getBoundingClientRect();
                var w = rect.width || 400;
                var h = rect.height || 120;

                var padL = 8, padR = 8, padT = 12, padB = 8;
                var plotW = w - padL - padR;
                var plotH = h - padT - padB;

                var minV = 36, maxV = 50;
                var rangeV = maxV - minV;

                var pts = chartData.map(function(d, i) {
                    var x = padL + (i / (chartData.length - 1)) * plotW;
                    var y = padT + plotH - ((d.value - minV) / rangeV) * plotH;
                    return { x: x, y: y, data: d };
                });

                function catmullRomToBezier(points) {
                    var d = 'M' + points[0].x + ',' + points[0].y;
                    for (var i = 0; i < points.length - 1; i++) {
                        var p0 = points[Math.max(i - 1, 0)];
                        var p1 = points[i];
                        var p2 = points[i + 1];
                        var p3 = points[Math.min(i + 2, points.length - 1)];
                        var tension = 0.3;
                        var cp1x = p1.x + (p2.x - p0.x) * tension;
                        var cp1y = p1.y + (p2.y - p0.y) * tension;
                        var cp2x = p2.x - (p3.x - p1.x) * tension;
                        var cp2y = p2.y - (p3.y - p1.y) * tension;
                        d += ' C' + cp1x + ',' + cp1y + ' ' + cp2x + ',' + cp2y + ' ' + p2.x + ',' + p2.y;
                    }
                    return d;
                }

                var linePath = catmullRomToBezier(pts);
                var fillPath = linePath + ' L' + pts[pts.length-1].x + ',' + (padT + plotH) + ' L' + pts[0].x + ',' + (padT + plotH) + ' Z';

                var svgNS = 'http://www.w3.org/2000/svg';
                var svg = document.createElementNS(svgNS, 'svg');
                svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
                svg.setAttribute('preserveAspectRatio', 'none');
                svg.style.width = '100%';
                svg.style.height = '100%';
                svg.style.overflow = 'visible';

                var defs = document.createElementNS(svgNS, 'defs');
                var grad = document.createElementNS(svgNS, 'linearGradient');
                grad.setAttribute('id', 'chartGrad');
                grad.setAttribute('x1', '0'); grad.setAttribute('y1', '0');
                grad.setAttribute('x2', '0'); grad.setAttribute('y2', '1');
                var stop1 = document.createElementNS(svgNS, 'stop');
                stop1.setAttribute('offset', '0%');
                stop1.setAttribute('stop-color', '#001760');
                stop1.setAttribute('stop-opacity', '0.18');
                var stop2 = document.createElementNS(svgNS, 'stop');
                stop2.setAttribute('offset', '100%');
                stop2.setAttribute('stop-color', '#001760');
                stop2.setAttribute('stop-opacity', '0');
                grad.appendChild(stop1);
                grad.appendChild(stop2);
                defs.appendChild(grad);
                svg.appendChild(defs);

                var gridG = document.createElementNS(svgNS, 'g');
                gridG.setAttribute('class', 'chart-grid');
                for (var g = 0; g <= 4; g++) {
                    var gy = padT + (g / 4) * plotH;
                    var line = document.createElementNS(svgNS, 'line');
                    line.setAttribute('x1', padL); line.setAttribute('y1', gy);
                    line.setAttribute('x2', padL + plotW); line.setAttribute('y2', gy);
                    gridG.appendChild(line);
                }
                svg.appendChild(gridG);

                var fillEl = document.createElementNS(svgNS, 'path');
                fillEl.setAttribute('d', fillPath);
                fillEl.setAttribute('fill', 'url(#chartGrad)');
                fillEl.setAttribute('class', 'chart-fill');
                svg.appendChild(fillEl);

                var lineEl = document.createElementNS(svgNS, 'path');
                lineEl.setAttribute('d', linePath);
                lineEl.setAttribute('class', 'chart-line');
                svg.appendChild(lineEl);

                pts.forEach(function(pt, idx) {
                    if (idx === pts.length - 1) {
                        var pulseRing = document.createElementNS(svgNS, 'circle');
                        pulseRing.setAttribute('cx', pt.x);
                        pulseRing.setAttribute('cy', pt.y);
                        pulseRing.setAttribute('r', '5');
                        svg.appendChild(pulseRing);
                    }

                    var circle = document.createElementNS(svgNS, 'circle');
                    circle.setAttribute('cx', pt.x);
                    circle.setAttribute('cy', pt.y);
                    circle.setAttribute('r', '5');
                    circle.setAttribute('class', 'chart-point');
                    circle.setAttribute('data-idx', idx);
                    svg.appendChild(circle);

                    setTimeout(function() { circle.classList.add('visible'); }, 1600 + idx * 120);

                    circle.addEventListener('mouseenter', function(e) {
                        var chartRect = wrap.getBoundingClientRect();
                        var cx = parseFloat(circle.getAttribute('cx'));
                        var cy = parseFloat(circle.getAttribute('cy'));
                        var scaleX = chartRect.width / w;
                        var scaleY = chartRect.height / h;
                        var px = cx * scaleX;
                        var py = cy * scaleY;

                        tooltipMonth.textContent = pt.data.month;
                        tooltipValue.textContent = pt.data.value.toFixed(1) + 'M FCFA';
                        tooltip.classList.add('visible');
                        tooltip.style.left = px + 'px';
                        tooltip.style.top = (py - 48) + 'px';
                        tooltip.style.transform = 'translateX(-50%)';
                    });

                    circle.addEventListener('mouseleave', function() {
                        tooltip.classList.remove('visible');
                    });
                });

                wrap.innerHTML = '';
                wrap.appendChild(svg);

                requestAnimationFrame(function() {
                    var len = lineEl.getTotalLength();
                    lineEl.style.strokeDasharray = len;
                    lineEl.style.strokeDashoffset = len;
                    lineEl.style.animation = 'none';
                    void lineEl.offsetWidth;
                    lineEl.style.animation = 'drawLine 1.5s ease-out 0.3s forwards';
                });
            }

            buildChart();
            var resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(buildChart, 200);
            });
        })();

        // 3D TILT + SHEEN
        (function() {
            var card = document.getElementById('card3d');
            if (!card) return;
            var isHovering = false;
            var currentX = 0, currentY = 0;
            var targetX = 0, targetY = 0;
            var isMobile = 'ontouchstart' in window;

            if (isMobile) return;

            function animate() {
                currentX += (targetX - currentX) * 0.08;
                currentY += (targetY - currentY) * 0.08;

                if (isHovering) {
                    var shadowX = currentY * 2;
                    var shadowY = currentX * -2;
                    card.style.transform = 'perspective(1000px) rotateX(' + currentX + 'deg) rotateY(' + currentY + 'deg) scale(1.02)';
                    card.style.boxShadow = shadowX + 'px ' + (shadowY + 20) + 'px 60px rgba(0,23,96,0.12), ' + (shadowX * 0.5) + 'px ' + (shadowY * 0.5 + 8) + 'px 20px rgba(0,23,96,0.08)';
                } else {
                    if (Math.abs(currentX) > 0.01 || Math.abs(currentY) > 0.01) {
                        card.style.transform = 'perspective(1000px) rotateX(' + currentX + 'deg) rotateY(' + currentY + 'deg)';
                    } else {
                        card.style.transform = '';
                        card.style.boxShadow = '';
                    }
                }
                requestAnimationFrame(animate);
            }

            animate();

            card.addEventListener('mouseenter', function() {
                isHovering = true;
                card.style.animationPlayState = 'paused';
                card.classList.add('is-hovering');
            });

            card.addEventListener('mouseleave', function() {
                isHovering = false;
                targetX = 0;
                targetY = 0;
                card.classList.remove('is-hovering');
                setTimeout(function() {
                    if (!isHovering) card.style.animationPlayState = 'running';
                }, 600);
            });

            card.addEventListener('mousemove', function(e) {
                if (!isHovering) return;
                var rect = card.getBoundingClientRect();
                var centerX = rect.left + rect.width / 2;
                var centerY = rect.top + rect.height / 2;
                var relX = (e.clientX - centerX) / (rect.width / 2);
                var relY = (e.clientY - centerY) / (rect.height / 2);
                relX = Math.max(-1, Math.min(1, relX));
                relY = Math.max(-1, Math.min(1, relY));
                targetX = relY * -8;
                targetY = relX * 8;

                var px = ((e.clientX - rect.left) / rect.width) * 100;
                var py = ((e.clientY - rect.top) / rect.height) * 100;
                card.style.setProperty('--sheen-x', px + '%');
                card.style.setProperty('--sheen-y', py + '%');
            });
        })();

        // SCREEN CAROUSEL
        (function() {
            var screens = document.querySelectorAll('.screen');
            var dots = document.querySelectorAll('.progress-dot');
            var card = document.getElementById('card3d');
            if (!screens.length) return;
            var currentScreen = 0;
            var interval;
            var isPaused = false;

            function showScreen(index) {
                screens.forEach(function(s, i) { s.classList.toggle('active', i === index); });
                dots.forEach(function(d, i) { d.classList.toggle('active', i === index); });
                currentScreen = index;
            }

            function nextScreen() {
                if (isPaused) return;
                showScreen((currentScreen + 1) % screens.length);
            }

            function startAutoplay() { interval = setInterval(nextScreen, 5000); }
            function stopAutoplay() { clearInterval(interval); }

            dots.forEach(function(dot, i) {
                dot.addEventListener('click', function() {
                    showScreen(i);
                    stopAutoplay();
                    startAutoplay();
                });
            });

            card.addEventListener('mouseenter', function() { isPaused = true; });
            card.addEventListener('mouseleave', function() { isPaused = false; });

            startAutoplay();
        })();
    </script>
</body>

</html>
