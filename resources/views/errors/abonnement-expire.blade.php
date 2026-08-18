<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement échu - RH Flow</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <style>
        :root {
            --rh-primary: #001760;
            --rh-danger: #dc3545;
            --gray-600: #4b5563;
            --gray-100: #f3f4f6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background: #f8faff;
            color: #111827;
        }

        .carte {
            max-width: 560px;
            width: 100%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 23, 96, .10);
            padding: 40px;
            text-align: center;
        }

        .pastille {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: rgba(220, 53, 69, .10);
            color: var(--rh-danger);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 34px;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 12px;
            color: var(--rh-primary);
        }

        p {
            color: var(--gray-600);
            line-height: 1.6;
            margin: 0 0 16px;
        }

        .echeance {
            display: inline-block;
            background: var(--gray-100);
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .echeance strong {
            color: var(--rh-danger);
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primaire {
            background: var(--rh-primary);
            color: #fff;
        }

        .btn-secondaire {
            background: #fff;
            color: var(--gray-600);
            border-color: #d1d5db;
        }

        .aide {
            margin-top: 24px;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="carte">
        <div class="pastille">⏳</div>

        <h1>Votre abonnement est arrivé à échéance</h1>

        @if($estProprietaire)
            <p>
                L'accès à RH&nbsp;Flow est suspendu jusqu'au renouvellement de votre abonnement.
                Vos données sont conservées et redeviendront accessibles dès la régularisation.
            </p>
        @else
            <p>
                L'accès à RH&nbsp;Flow est suspendu : l'abonnement de votre entreprise
                est arrivé à échéance et n'a pas été renouvelé.
                Rapprochez-vous de l'administrateur de votre entreprise pour le renouveler.
            </p>
        @endif

        @if($echeance)
            <div class="echeance">
                @if($plan)
                    Offre <strong>{{ $plan }}</strong> &nbsp;•&nbsp;
                @endif
                Échue le <strong>{{ $echeance->format('d/m/Y') }}</strong>
                ({{ $echeance->diffForHumans() }})
            </div>
        @endif

        <div class="actions">
            @if($estProprietaire)
                <a href="{{ route('company.packs.index') }}" class="btn btn-primaire">Renouveler mon abonnement</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondaire">Se déconnecter</button>
            </form>
        </div>

        <p class="aide">
            Besoin d'aide ? Contactez le support RH Flow.
        </p>
    </div>
</body>

</html>
