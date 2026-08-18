@extends('landingpage::master.app')

@section('title', 'Politique de Confidentialité - RH Flow')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="space-y-6 flex justify-center items-center">
            <a href="{{route('landingpage')}}">
                <img src="{{ asset('img/logos/logo-dark.png') }}" width="100px" alt="logo">
            </a>
        </div>
        <br>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">POLITIQUE DE CONFIDENTIALITÉ – RH FLOW</h1>
        <p class="text-sm text-gray-600 mb-8">Dernière mise à jour : 01/01/2026</p>
        
        <p class="text-gray-700 mb-6">
            RH Flow est une application de gestion des ressources humaines destinée aux entreprises et cabinets de conseil.
        </p>

        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">1. Données collectées</h2>
                <p class="text-gray-700 mb-3">
                    Dans le cadre de l'utilisation de l'application RH Flow, nous pouvons collecter les données suivantes :
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                    <li>Identité : nom, prénoms</li>
                    <li>Coordonnées : téléphone, email</li>
                    <li>Informations professionnelles : poste, salaire, présence</li>
                    <li>Documents RH : contrats, fiches de paie, dossiers salariés</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">2. Utilisation des données</h2>
                <p class="text-gray-700 mb-3">
                    Les données collectées sont utilisées exclusivement pour :
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                    <li>La gestion administrative des ressources humaines</li>
                    <li>Le traitement de la paie</li>
                    <li>Le suivi de la présence et des performances</li>
                    <li>Le respect des obligations légales et sociales</li>
                    <li>L'amélioration des services proposés</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">3. Partage des données</h2>
                <p class="text-gray-700 mb-3">
                    Les données ne sont ni vendues ni cédées à des tiers.
                    Elles sont accessibles uniquement :
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                    <li>À l'entreprise utilisatrice</li>
                    <li>Aux administrateurs autorisés</li>
                    <li>Au cabinet gestionnaire (le cas échéant)</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">4. Sécurité des données</h2>
                <p class="text-gray-700 mb-3">
                    Nous mettons en œuvre des mesures techniques et organisationnelles pour assurer la sécurité et la confidentialité des données :
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                    <li>Accès sécurisé par authentification</li>
                    <li>Stockage sécurisé des données</li>
                    <li>Restriction des accès selon les profils utilisateurs</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">5. Conservation des données</h2>
                <p class="text-gray-700">
                    Les données sont conservées pendant la durée nécessaire à l'exécution des services et conformément aux obligations légales en vigueur.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">6. Droits des utilisateurs</h2>
                <p class="text-gray-700 mb-3">
                    Conformément à la réglementation applicable, les utilisateurs disposent des droits suivants :
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                    <li>Droit d'accès</li>
                    <li>Droit de rectification</li>
                    <li>Droit de suppression</li>
                    <li>Droit d'opposition</li>
                </ul>
                <p class="text-gray-700 mt-3">
                    Toute demande peut être adressée à l'adresse ci-dessous.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">7. Contact</h2>
                <p class="text-gray-700 mb-3">
                    Pour toute question relative à la protection des données personnelles, vous pouvez nous contacter :
                </p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700"><strong>Email :</strong> rhflow@dc-knowing.com</p>
                    <p class="text-gray-700"><strong>Adresse :</strong> Côte d'Ivoire, Abidjan, Cocody, Riviera Bonoumin</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection