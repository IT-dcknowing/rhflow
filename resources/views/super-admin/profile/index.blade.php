@extends('layouts.super-admin')

@section('title', 'Mon Profil - Super Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Mon Profil</h4>
                <small class="text-muted">Gérer vos informations personnelles</small>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('super-admin.profile.index') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Informations de base -->
                        <div class="col-md-8">
                            <h5 class="mb-3">Informations personnelles</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Sécurité</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <small class="text-muted">Requis pour changer le mot de passe</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Dernière modification: {{ $user->updated_at->diffForHumans() }}
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-primary bg-primary text-white">
                                    <i class="ti ti-check me-1"></i>
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </div>

                        <!-- Avatar Section -->
                        <div class="col-md-4">
                            <div class="card border bg-dark ">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        @if($user->avatar_url && file_exists(public_path($user->avatar_url)))
                                            <img src="{{ asset($user->avatar_url) }}" alt="Avatar" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2rem; color: white;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <h6 class="mb-2 text-white">{{ $user->name }}</h6>
                                    <small class="text-white">{{ $user->type_label ?? 'Super Administrateur' }}</small>

                                    <div class="mt-3">
                                        <input type="file" class="form-control d-none" id="avatar" name="avatar" accept="image/*">
                                        <label for="avatar" class="btn btn-outline-white btn-sm">
                                            <i class="ti ti-camera me-1"></i>Changer l'avatar
                                        </label>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-white">JPG, PNG ou GIF. Max 2MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Aperçu de l'avatar
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.col-md-4 img');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
