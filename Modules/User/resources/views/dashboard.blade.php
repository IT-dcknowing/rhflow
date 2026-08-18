@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Module User</h1>
            <p class="text-gray-600 mb-4">Gestion des utilisateurs via le système modulaire Laravel.</p>

            <div class="space-y-3">
                <a href="#"
                   class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                    Liste des Utilisateurs
                </a>
                <a href="#"
                   class="block w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                    Créer un Utilisateur
                </a>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Module: {!! config('user.name') !!}
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        console.log('Module User');
    </script>
@endpush