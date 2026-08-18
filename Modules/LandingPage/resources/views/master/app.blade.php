<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RH Flow - Gestion RH')</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#1e3a8a',secondary:'#64748b'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(255,255,255,0.9) 70%, rgba(255,255,255,0) 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1);
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
        .custom-checkbox:hover input ~ .checkmark {
            border-color: #1e3a8a;
        }
        .custom-checkbox input:checked ~ .checkmark {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        .custom-checkbox input:checked ~ .checkmark:after {
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
        
        /* Styles pour les alertes modernes */
        .alert {
            position: relative;
            padding: 16px 20px;
            margin-bottom: 20px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideInDown 0.3s ease-out;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-left: 4px solid #047857;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-left: 4px solid #b91c1c;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-left: 4px solid #b45309;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-left: 4px solid #1d4ed8;
        }
        
        .alert .btn-close {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .alert .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }
        
        .alert .btn-close:before {
            content: '×';
            color: white;
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }
        
        .alert i {
            margin-right: 8px;
            font-size: 16px;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        
        .alert.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
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
        input:checked + .switch-slider {
            background-color: #1e3a8a;
        }
        input:checked + .switch-slider:before {
            transform: translateX(24px);
        }
    </style>
    <script src="https://cdn.cinetpay.com/seamless/main.js" type="text/javascript"></script>
</head>
<body class="bg-white">
    <div class="container mx-auto px-4">   
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div>
                    <a href="https://dc-knowing.com/RH-Flow" class="inline-block mb-6">
                       <img src="{{ asset('img/logos/logo-light.png') }}" width="100px" alt="logo">
                    </a>
                    <p class="text-gray-400 mb-6">Simplifiez, automatisez et optimisez la gestion de vos ressources humaines.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-instagram-fill"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Produit</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Fonctionnalités</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Témoignages</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Ressources</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Guides</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Centre d'aide</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Paiement</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Paiement sécurisé par GT Bank</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 mb-4 md:mb-0">© 2025 RH-Flow. Tous droits réservés.</p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Mentions légales</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Politique de confidentialité</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">CGU</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ Accordion
            const faqButtons = document.querySelectorAll('.faq-item button');
            faqButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    const icon = button.querySelector('i');

                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                        icon.classList.remove('ri-arrow-up-s-line');
                        icon.classList.add('ri-arrow-down-s-line');
                    } else {
                        content.style.display = 'block';
                        icon.classList.remove('ri-arrow-down-s-line');
                        icon.classList.add('ri-arrow-up-s-line');
                    }
                });
            });
            
            // Amélioration des alertes
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                // Auto-dismiss après 5 secondes pour les alertes de succès
                if (alert.classList.contains('alert-success')) {
                    setTimeout(() => {
                        alert.classList.add('fade-out');
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }, 5000);
                }
                
                // Fermeture manuelle
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        alert.classList.add('fade-out');
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    });
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
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

            menuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('translate-x-full');
            });

            const closeButton = mobileMenu.querySelector('button');
            closeButton.addEventListener('click', function() {
                mobileMenu.classList.add('translate-x-full');
            });

            // Close mobile menu when clicking on links
            const mobileLinks = mobileMenu.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
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
            CinetPay.waitResponse(function(data) {
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
            CinetPay.onError(function(data) {
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
            CinetPay.waitResponse(function(data) {
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
            CinetPay.onError(function(data) {
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
            CinetPay.waitResponse(function(data) {
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
    </script>
    @stack('scripts-external')
    @stack('scripts')
</body>
</html>
