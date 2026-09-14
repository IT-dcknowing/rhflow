@extends('layouts.app')

@section('title', 'Configuration Entreprise - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Configuration de l'Entreprise</h4>
                        <p class="text-muted mb-0">Gérez les informations et paramètres de votre entreprise</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.settings.config') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de Base -->
        <div class="row mb-4">
            <div class="col-xl-12 col-lg-12 mb-4">
                <form method="POST" action="{{ route('company.settings.settings.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8 col-lg-8 mb-4">
                            <!-- Informations de Base -->
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"> Informations de Base</h5>
                                    <span class="badge bg-label-primary">Entreprise</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de l'Entreprise <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ $company->name }}"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ $company->email }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Préfixe des employés</label>
                                            @if($company->employee_prefix)
                                                <input type="text" class="form-control" name="employee_prefix"
                                                    value="{{ $company->employee_prefix }}">
                                            @else
                                                <input type="text" class="form-control" name="employee_prefix"
                                                    value="#{{ substr($company->name, 0, 4) }}-EMP">
                                            @endif
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <input type="tel" class="form-control" name="phone"
                                                value="{{ $company->phone }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Site Web</label>
                                            <input type="url" class="form-control" name="website"
                                                value="{{ $company->website }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Adresse</label>
                                        <textarea class="form-control" name="address"
                                            rows="3">{{ $company->address }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Ville</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ $company->city }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Code Postal</label>
                                            <input type="text" class="form-control" name="postal_code"
                                                value="{{ $company->postal_code }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pays</label>
                                            <input type="text" class="form-control" name="country"
                                                value="{{ $company->country }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Taille de l'entreprise <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('size') is-invalid @enderror" name="size"
                                                required>
                                                <option value="">Choisir la taille...</option>
                                                <option value="startup" {{ old('size', $company->size) == 'startup' ? 'selected' : '' }}>Startup</option>
                                                <option value="small" {{ old('size', $company->size) == 'small' ? 'selected' : '' }}>Petite entreprise (1-50 employés)</option>
                                                <option value="medium" {{ old('size', $company->size) == 'medium' ? 'selected' : '' }}>Moyenne entreprise (51-200 employés)</option>
                                                <option value="large" {{ old('size', $company->size) == 'large' ? 'selected' : '' }}>Grande entreprise (201-1000 employés)</option>
                                                <option value="enterprise" {{ old('size', $company->size) == 'enterprise' ? 'selected' : '' }}>Grande entreprise (1000+ employés)</option>
                                            </select>
                                            @error('size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Taux d'accident de travail (CNPS) <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('accident_taux') is-invalid @enderror"
                                                name="accident_taux" required>
                                                <option value="">Choisir le taux...</option>
                                                <option value="0,02" {{ old('accident_taux', $company->accident_taux) == '0,02' ? 'selected' : '' }}>2%</option>
                                                <option value="0,03" {{ old('accident_taux', $company->accident_taux) == '0,03' ? 'selected' : '' }} selected>3%
                                                </option>
                                                <option value="0,04" {{ old('accident_taux', $company->accident_taux) == '0,04' ? 'selected' : '' }}>4%</option>
                                                <option value="0,05" {{ old('accident_taux', $company->accident_taux) == '0,05' ? 'selected' : '' }}>5%</option>
                                            </select>
                                            @error('accident_taux')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Secteur d'activité <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('industry') is-invalid @enderror"
                                                name="industry" required>
                                                <option value="">Choisir un secteur...</option>
                                                @foreach($sectors as $sector)
                                                    <option value="{{ $sector->slug }}" {{ old('industry', $company->industry) == $sector->slug ? 'selected' : '' }}>
                                                        {{ $sector->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('industry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description"
                                            rows="3">{{ old('description', $company->description) }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Heures de travail par semaine <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="working_hours_per_week"
                                                value="{{ old('working_hours_per_week', $company->working_hours_per_week) }}"
                                                min="1" max="168">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jours travaillés par semaine <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="working_days_per_week"
                                                value="{{ old('working_days_per_week', $company->working_days_per_week) }}"
                                                min="1" max="7">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations administratives -->
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"> Informations Administratives</h5>
                                    <span class="badge bg-label-primary">Entreprise</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">N° Compte Contribuable <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="tax_id"
                                                value="{{ old('tax_id', $company->tax_id) }}" maxlength="8"
                                                placeholder="9727498F">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">N° RCCM <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="registration_number"
                                                value="{{ old('registration_number', $company->registration_number) }}"
                                                placeholder="CI-ABJ-03-2023-B13-00452">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 mb-4">
                            <!-- Paramètres géographiques -->
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"> Paramètres géographiques</h5>
                                    <span class="badge bg-label-primary">Entreprise</span>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Fuseau horaire</label>
                                            <select class="form-select" name="timezone">
                                                <option value="">Choisir un fuseau horaire...</option>
                                                <option value="Africa/Abidjan" {{ old('timezone', $company->timezone) == 'Africa/Abidjan' ? 'selected' : '' }}>
                                                    Africa/Abidjan (GMT+0)</option>
                                                <option value="Europe/Paris" {{ old('timezone', $company->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris
                                                    (GMT+1)</option>
                                                <option value="Europe/London" {{ old('timezone', $company->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London
                                                    (GMT+0)</option>
                                                <option value="America/New_York" {{ old('timezone', $company->timezone) == 'America/New_York' ? 'selected' : '' }}>
                                                    America/New_York (GMT-5)</option>
                                                <option value="America/Montreal" {{ old('timezone', $company->timezone) == 'America/Montreal' ? 'selected' : '' }}>
                                                    America/Montreal (GMT-5)</option>
                                                <option value="Asia/Tokyo" {{ old('timezone', $company->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo
                                                    (GMT+9)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Région / État</label>
                                            <input type="text" class="form-control" name="state"
                                                value="{{ old('state', $company->state) }}">
                                        </div>
                                    </div>

                                    <!-- Coordonnées géographiques (cachées) -->
                                    <input type="hidden" name="latitude" value="{{ old('latitude', $company->latitude) }}">
                                    <input type="hidden" name="longitude"
                                        value="{{ old('longitude', $company->longitude) }}">
                                    <input type="hidden" name="location_data"
                                        value="{{ old('location_data', $company->location_data) }}">
                                </div>
                            </div>

                            <!-- Aperçu du Thème -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"> Couleurs du bulletin</h5>
                                    <span class="badge bg-label-info">Personnalisation</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Couleur Principale</label>
                                        <input type="color" class="form-control form-control-color" name="primary_color"
                                            value="{{ $company->getThemePrimaryColor() }}" title="Choisir une couleur">
                                        <small class="text-muted">Couleur principale de l'interface</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Couleur Secondaire</label>
                                        <input type="color" class="form-control form-control-color" name="secondary_color"
                                            value="{{ $company->getThemeSecondaryColor() }}" title="Choisir une couleur">
                                        <small class="text-muted">Couleur d'accent et des boutons</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Couleur de Fond Header</label>
                                        <input type="color" class="form-control form-control-color" name="header_bg_color"
                                            value="{{ $company->getThemeHeaderBgColor() }}" title="Choisir une couleur">
                                        <small class="text-muted">Couleur de fond de l'en-tête</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Aperçu du Thème du bulletin</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge me-2"
                                            style="background-color: {{ $company->getThemePrimaryColor() }}; color: white;">
                                            Primaire
                                        </div>
                                        <small class="text-muted">{{ $company->getThemePrimaryColor() }}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge me-2"
                                            style="background-color: {{ $company->getThemeSecondaryColor() }}; color: white;">
                                            Secondaire
                                        </div>
                                        <small class="text-muted">{{ $company->getThemeSecondaryColor() }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="badge me-2"
                                            style="background-color: {{ $company->getThemeHeaderBgColor() }}; color: {{ $company->getThemeHeaderBgColor() == '#ffffff' ? 'black' : 'white' }};">
                                            Header
                                        </div>
                                        <small class="text-muted">{{ $company->getThemeHeaderBgColor() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Enregistrer les Modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload de Documents -->
        <div class="row">
            <!-- Logo -->
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Logo</h5>
                        <span class="badge bg-label-success">Image</span>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3 d-flex justify-content-center align-items-center">
                            @if($company->logo)
                                <div>
                                    <img src="{{ url($company->logo_url) }}" alt="Logo" class="img-fluid mb-2"
                                        style="max-height: 100px;">
                                    <small class="text-muted d-block">{{ basename($company->logo) }}</small>
                                </div>
                            @else
                                <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                    <div class="avatar-initial bg-label-secondary rounded"
                                        style="align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-32px"></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.logo.update') }}"
                            enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="logo" accept="image/*" required
                                    style="display: none;" id="logoInput">
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('logoInput').click()">
                                    <i class="fas fa-upload me-1"></i>Changer le Logo
                                </button>
                                <div id="logoFileName" class="text-muted small mt-1"></div>
                            </div>
                            {{-- Grise tant qu'aucun nouveau fichier n'est choisi : la piece est deja enregistree --}}
                            <button type="submit" class="btn btn-primary" id="logoSubmit"
                                @if($company->logo) disabled title="Pièce déjà enregistrée : choisissez un nouveau fichier pour la remplacer" @endif>
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </form>

                        <small class="text-muted d-block mt-2">Formats: JPEG, PNG, GIF, SVG (max 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Signature Électronique -->
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Signature</h5>
                        <span class="badge bg-label-info">Document</span>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3 d-flex justify-content-center align-items-center">
                            @if($company->electronic_signature)
                                <div>
                                    <img src="{{ url($company->electronic_signature_url) }}" alt="Signature"
                                        class="img-fluid mb-2" style="max-height: 100px;">
                                    <small class="text-muted d-block">{{ basename($company->electronic_signature) }}</small>
                                </div>
                            @else
                                <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-signature fa-32px"></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.signature.update') }}"
                            enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="signature" accept="image/*" required
                                    style="display: none;" id="signatureInput">
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('signatureInput').click()">
                                    <i class="fas fa-upload me-1"></i>Changer la Signature
                                </button>
                                <div id="signatureFileName" class="text-muted small mt-1"></div>
                            </div>
                            {{-- Grise tant qu'aucun nouveau fichier n'est choisi : la piece est deja enregistree --}}
                            <button type="submit" class="btn btn-primary" id="signatureSubmit"
                                @if($company->electronic_signature) disabled title="Pièce déjà enregistrée : choisissez un nouveau fichier pour la remplacer" @endif>
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </form>

                        <small class="text-muted d-block mt-2">Formats: JPEG, PNG, GIF (max 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Cachet Électronique -->
            <div class="col-xl-4 col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cachet</h5>
                        <span class="badge bg-label-warning">Document</span>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3 d-flex justify-content-center align-items-center">
                            @if($company->electronic_stamp)
                                <div>
                                    <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" class="img-fluid mb-2"
                                        style="max-height: 100px;">
                                    <small class="text-muted d-block">{{ basename($company->electronic_stamp) }}</small>
                                </div>
                            @else
                                <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-stamp fa-32px"></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.stamp.update') }}"
                            enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="stamp" accept="image/*" required
                                    style="display: none;" id="stampInput">
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('stampInput').click()">
                                    <i class="fas fa-upload me-1"></i>Changer le Cachet
                                </button>
                                <div id="stampFileName" class="text-muted small mt-1"></div>
                            </div>
                            {{-- Grise tant qu'aucun nouveau fichier n'est choisi : la piece est deja enregistree --}}
                            <button type="submit" class="btn btn-primary" id="stampSubmit"
                                @if($company->electronic_stamp) disabled title="Pièce déjà enregistrée : choisissez un nouveau fichier pour la remplacer" @endif>
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </form>

                        <small class="text-muted d-block mt-2">Formats: JPEG, PNG, GIF (max 2MB)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom styles for color pickers */
        .form-control-color {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            cursor: pointer;
        }

        .form-control-color::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .form-control-color::-webkit-color-swatch {
            border-radius: 6px;
            border: none;
        }

        /* Card hover effects */
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(105, 110, 255, 0.05) 0%, rgba(3, 195, 236, 0.05) 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h5 {
            color: #566a7f;
            font-weight: 600;
        }

        /* Button improvements */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #696cff 0%, #03c3ec 100%);
            border-color: transparent;
        }

        /* Avatar improvements */
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
        }

        .bg-label-primary {
            background-color: rgba(105, 110, 255, 0.1) !important;
            color: #253e87 !important;
        }

        .bg-label-success {
            background-color: rgba(40, 200, 72, 0.1) !important;
            color: #28c848 !important;
        }

        .bg-label-warning {
            background-color: rgba(255, 205, 7, 0.1) !important;
            color: #ffcd07 !important;
        }

        .bg-label-info {
            background-color: rgba(3, 195, 236, 0.1) !important;
            color: #03c3ec !important;
        }

        .bg-label-danger {
            background-color: rgba(255, 77, 77, 0.1) !important;
            color: #ff4d4f !important;
        }

        .bg-label-secondary {
            background-color: rgba(133, 146, 163, 0.1) !important;
            color: #8592a3 !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            h3 {
                font-size: 1.5rem;
            }

            h5 {
                font-size: 1.125rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Script pour gérer les uploads de fichiers
        document.addEventListener('DOMContentLoaded', function () {
            // Reactive le bouton Enregistrer des qu'un nouveau fichier est choisi,
            // et le regrise si la selection est annulee alors qu'une piece existe deja.
            function bindUpload(type) {
                const input = document.getElementById(`${type}Input`);
                const submit = document.getElementById(`${type}Submit`);
                if (!input) return;

                const dejaEnregistre = submit ? submit.hasAttribute('disabled') : false;

                input.addEventListener('change', function () {
                    showFileName(`${type}FileName`, this.files[0]);
                    if (!submit) return;
                    const aUnFichier = this.files.length > 0;
                    submit.disabled = dejaEnregistre && !aUnFichier;
                    if (aUnFichier) {
                        submit.removeAttribute('title');
                    }
                });
            }

            ['logo', 'signature', 'stamp'].forEach(bindUpload);

            // Validation et feedback visuel pour tous les formulaires
            ['logo', 'signature', 'stamp'].forEach(type => {
                const form = document.querySelector(`form[action*="${type}"]`);
                const input = document.getElementById(`${type}Input`);

                if (form && input) {
                    form.addEventListener('submit', function (e) {
                        if (input && input.files.length > 0) {
                            const file = input.files[0];

                            // Validation de la taille (2MB)
                            if (file.size > 2 * 1024 * 1024) {
                                e.preventDefault();
                                alert(`Le fichier ${file.name} ne doit pas dépasser 2MB`);
                                return;
                            }

                            // Validation du type
                            if (!file.type.startsWith('image/')) {
                                e.preventDefault();
                                alert(`Le fichier ${file.name} doit être une image`);
                                return;
                            }
                        }
                    });
                }
            });

            // Aperçu des couleurs en temps réel
            const colorInputs = document.querySelectorAll('input[type="color"]');
            colorInputs.forEach(input => {
                input.addEventListener('change', function () {
                    updateThemePreview();
                });
            });
        });

        // Fonction pour afficher le nom du fichier sélectionné
        function showFileName(elementId, file) {
            const element = document.getElementById(elementId);
            if (element && file) {
                element.innerHTML = `<i class="fas fa-file me-1"></i>${file.name} (${formatFileSize(file.size)})`;
                element.className = 'text-success small mt-1';
            }
        }

        // Fonction pour formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // --- Guide IA Proactif pour la Configuration ---
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                showAiTip("Ici, vous configurez l'identité de votre entreprise. Ces informations apparaîtront sur tous vos documents officiels.");
            }, 1500);

            // Conseil sur les identifiants fiscaux
            const taxInput = document.querySelector('input[name="tax_id"]');
            if (taxInput) {
                taxInput.addEventListener('focus', function () {
                    showAiTip("Le <b>Compte Contribuable</b> est essentiel pour vos déclarations fiscales mensuelles.");
                });
            }

            // Conseil sur les couleurs du bulletin
            const colorInput = document.querySelector('input[name="primary_color"]');
            if (colorInput) {
                colorInput.addEventListener('focus', function () {
                    showAiTip("Personnalisez vos bulletins de paie avec les couleurs de votre charte graphique !");
                });
            }

            // Conseil sur le logo / cachet
            const stampInput = document.getElementById('stampInput');
            if (stampInput) {
                stampInput.closest('.card').addEventListener('mouseenter', function () {
                    showAiTip("Un cachet électronique permet de générer des bulletins de paie et attestations déjà signés.");
                });
            }
        });
    </script>
@endpush