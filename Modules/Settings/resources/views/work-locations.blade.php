@extends('layouts.app')

@section('title', 'Emplacements de Travail - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📍 Emplacements de Travail</h4>
                    <p class="text-muted mb-0">Gérez les sites de travail et les pointeuses</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.config') }}" class="btn btn-outline-info">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWorkLocationModal">
                        <i class="fas fa-plus me-1"></i>Nouvel Emplacement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-info rounded">
                            <i class="fas fa-map-marker-alt fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-info">{{ $workLocations->count() }}</h3>
                    <p class="text-muted mb-2">Total Emplacements</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-success rounded">
                            <i class="fas fa-clock fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-success">{{ $workLocations->where('has_time_clock', true)->count() }}</h3>
                    <p class="text-muted mb-2">Avec Pointeuse</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-warning rounded">
                            <i class="fas fa-building fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-warning">{{ $workLocations->where('type', 'office')->count() }}</h3>
                    <p class="text-muted mb-2">Bureaux</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-secondary rounded">
                            <i class="fas fa-users fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-secondary">{{ $workLocations->where('type', 'remote')->count() }}</h3>
                    <p class="text-muted mb-2">Télétravail</p>
                </div>
            </div>
        </div>
    </div> 

    <!-- Liste des Emplacements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 Emplacements Configurés</h5>
                    <span class="badge bg-label-primary">{{ $workLocations->count() }} emplacements</span>
                </div>
                <div class="card-body">
                    @if($workLocations->count() > 0)
                        <div class="row">
                            @foreach($workLocations as $location)
                            <div class="col-xl-6 col-lg-6 mb-4">
                                <div class="card border h-100 {{ $location->is_active ? 'border-info' : 'border-secondary' }}" data-location-id="{{ $location->id }}" data-location='@json($location->toArray())'>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                    <div class="avatar-initial {{ $location->is_active ? 'bg-label-info' : 'bg-label-secondary' }} rounded">
                                                        <i class="fas fa-{{ $location->type == 'office' ? 'building' : ($location->type == 'remote' ? 'home' : 'map-marker-alt') }} fa-20px"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $location->name }}</h6>
                                                    <small class="text-muted">{{ ucfirst($location->type) }}</small>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="editWorkLocation({{ $location->id }})">
                                                        <i class="fas fa-edit me-1"></i>Modifier
                                                    </a></li>

                                                    <li><a class="dropdown-item {{ $location->is_active ? 'text-warning' : 'text-success' }}" href="#" onclick="toggleWorkLocationStatus({{ $location->id }}, {{ $location->is_active ? 'true' : 'false' }})">
                                                        <i class="fas fa-{{ $location->is_active ? 'toggle-off' : 'toggle-on' }} me-1"></i>{{ $location->is_active ? 'Désactiver' : 'Activer' }}
                                                    </a></li>

                                                    <li><hr class="dropdown-divider"></li>

                                                    <li><a class="dropdown-item" href="#" onclick="generateQRCode({{ $location->id }})">
                                                        <i class="fas fa-qrcode me-1"></i>Générer QR Code
                                                    </a></li>

                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteWorkLocation({{ $location->id }}, '{{ $location->name }}')">
                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            @if($location->address)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                                    <small class="text-muted">{{ $location->full_address }}</small>
                                                </div>
                                            @endif

                                            @if($location->has_time_clock)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-clock me-2 text-success"></i>
                                                    <small class="text-success">Pointeuse configurée</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge {{ $location->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                    {{ $location->is_active ? '✅ Actif' : '❌ Inactif' }}
                                                </span>
                                                @if($location->branch)
                                                    <span class="badge bg-label-info ms-1">{{ $location->branch->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                @if($location->has_time_clock)
                                                    <i class="fas fa-clock text-success"></i>
                                                @else
                                                    <i class="fas fa-clock text-muted"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-map fa-32px"></i>
                                    </div>
                                </div>
                            </div>
                            <h5 class="text-muted">Aucun emplacement configuré</h5>
                            <p class="text-muted mb-4">Commencez par créer votre premier emplacement de travail</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWorkLocationModal">
                                <i class="fas fa-plus me-1"></i>Créer un Emplacement
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Création -->
<div class="modal fade" id="createWorkLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ Nouvel Emplacement de Travail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('company.settings.work-locations.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom de l'Emplacement *</label>
                            <input type="text" class="form-control" name="name" required placeholder="Ex: Bureau Principal, Site A...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type *</label>
                            <select class="form-select" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="office">Bureau</option>
                                <option value="remote">Télétravail</option>
                                <option value="client_site">Site Client</option>
                                <option value="warehouse">Entrepôt</option>
                                <option value="factory">Usine</option>
                                <option value="store">Magasin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Description de l'emplacement..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Adresse (Localisation) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="address_autocomplete" name="address_display" placeholder="Tapez une adresse pour rechercher..." autocomplete="off" required>
                                <input type="hidden" id="address" name="address">
                                <input type="hidden" id="city" name="city">
                                <input type="hidden" id="state" name="state">
                                <input type="hidden" id="country" name="country">
                                <input type="hidden" id="postal_code" name="postal_code">
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                                <button class="btn btn-outline-secondary" type="button" id="clear_address">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="autocomplete-results" class="autocomplete-results" style="display: none;"></div>
                            <small class="text-muted">Commencez à taper pour voir les suggestions d'adresses</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Code Postal</label>
                            <input type="text" class="form-control" id="postal_code_display" placeholder="75001" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" class="form-control" id="city_display" placeholder="Paris" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Région</label>
                            <input type="text" class="form-control" id="state_display" placeholder="Île-de-France" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pays</label>
                            <input type="text" class="form-control" id="country_display" placeholder="France" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Succursale</label>
                            <select class="form-select" name="branch_id">
                                <option value="">Sélectionner une succursale</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager</label>
                            <select class="form-select" name="manager_id">
                                <option value="">Sélectionner un manager</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="has_time_clock" value="0" id="hasTimeClockHidden">
                                <input class="form-check-input" type="checkbox" name="has_time_clock" id="hasTimeClock" value="1">
                                <label class="form-check-label" for="hasTimeClock">
                                    Pointeuse sur site
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="allow_remote_clock" value="0" id="allowRemoteClockHidden">
                                <input class="form-check-input" type="checkbox" name="allow_remote_clock" id="allowRemoteClock" value="1">
                                <label class="form-check-label" for="allowRemoteClock">
                                    Autoriser le pointage à distance
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="timeClockSettings" style="display: none;">
                        <h6 class="mb-3">⚙️ Configuration de la Pointeuse</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in Début</label>
                                <input type="time" class="form-control" name="check_in_start" value="08:00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in Fin</label>
                                <input type="time" class="form-control" name="check_in_end" value="09:30">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Début</label>
                                <input type="time" class="form-control" name="check_out_start" value="17:00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Fin</label>
                                <input type="time" class="form-control" name="check_out_end" value="18:30">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Distance Max (mètres)</label>
                            <input type="number" class="form-control" name="max_distance_meters" value="100" min="10" max="1000">
                            <small class="text-muted">Distance maximale autorisée pour le pointage géolocalisé</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'Edition -->
<div class="modal fade" id="editWorkLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ Modifier l'Emplacement de Travail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="editWorkLocationForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom de l'Emplacement *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required placeholder="Ex: Bureau Principal, Site A...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type *</label>
                            <select class="form-select" name="type" id="edit_type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="office">Bureau</option>
                                <option value="remote">Télétravail</option>
                                <option value="client_site">Site Client</option>
                                <option value="warehouse">Entrepôt</option>
                                <option value="factory">Usine</option>
                                <option value="store">Magasin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="2" placeholder="Description de l'emplacement..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Adresse (Localisation) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_address_autocomplete" name="address_display" placeholder="Tapez une adresse pour rechercher..." autocomplete="off" required>
                                <input type="hidden" id="edit_address" name="address">
                                <input type="hidden" id="edit_city" name="city">
                                <input type="hidden" id="edit_state" name="state">
                                <input type="hidden" id="edit_country" name="country">
                                <input type="hidden" id="edit_postal_code" name="postal_code">
                                <input type="hidden" id="edit_latitude" name="latitude">
                                <input type="hidden" id="edit_longitude" name="longitude">
                                <button class="btn btn-outline-secondary" type="button" id="edit_clear_address">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="edit_autocomplete_results" class="autocomplete-results" style="display: none;"></div>
                            <small class="text-muted">Commencez à taper pour voir les suggestions d'adresses</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Code Postal</label>
                            <input type="text" class="form-control" id="edit_postal_code_display" placeholder="75001" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" class="form-control" id="edit_city_display" placeholder="Paris" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Région</label>
                            <input type="text" class="form-control" id="edit_state_display" placeholder="Île-de-France" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pays</label>
                            <input type="text" class="form-control" id="edit_country_display" placeholder="France" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Succursale</label>
                            <select class="form-select" name="branch_id" id="edit_branch_id">
                                <option value="">Sélectionner une succursale</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager</label>
                            <select class="form-select" name="manager_id" id="edit_manager_id">
                                <option value="">Sélectionner un manager</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="has_time_clock" value="0" id="editHasTimeClockHidden">
                                <input class="form-check-input" type="checkbox" name="has_time_clock" id="editHasTimeClock" value="1">
                                <label class="form-check-label" for="editHasTimeClock">
                                    Pointeuse sur site
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="allow_remote_clock" value="0" id="editAllowRemoteClockHidden">
                                <input class="form-check-input" type="checkbox" name="allow_remote_clock" id="editAllowRemoteClock" value="1">
                                <label class="form-check-label" for="editAllowRemoteClock">
                                    Autoriser le pointage à distance
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                                <label class="form-check-label" for="editIsActive">
                                    Emplacement actif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="editTimeClockSettings" style="display: none;">
                        <h6 class="mb-3">⚙️ Configuration de la Pointeuse</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in Début</label>
                                <input type="time" class="form-control" name="check_in_start" id="edit_check_in_start" value="08:00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-in Fin</label>
                                <input type="time" class="form-control" name="check_in_end" id="edit_check_in_end" value="09:30">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Début</label>
                                <input type="time" class="form-control" name="check_out_start" id="edit_check_out_start" value="17:00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Fin</label>
                                <input type="time" class="form-control" name="check_out_end" id="edit_check_out_end" value="18:30">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Distance Max (mètres)</label>
                            <input type="number" class="form-control" name="max_distance_meters" id="edit_max_distance_meters" value="100" min="10" max="1000">
                            <small class="text-muted">Distance maximale autorisée pour le pointage géolocalisé</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    .bg-label-secondary {
        background-color: rgba(133, 146, 163, 0.1) !important;
        color: #8592a3 !important;
    }

    /* Empty state styling */
    .empty-state .avatar-initial {
        background-color: rgba(133, 146, 163, 0.1) !important;
        color: #8592a3 !important;
    }

    /* Time clock settings panel */
    #timeClockSettings {
        background-color: rgba(3, 195, 236, 0.05);
        border: 1px solid rgba(3, 195, 236, 0.2);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    /* Autocomplete styles */
    .autocomplete-results {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }

    .autocomplete-item {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .autocomplete-item:hover {
        background-color: rgba(105, 110, 255, 0.1);
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item .item-title {
        font-weight: 500;
        color: #566a7f;
        margin-bottom: 2px;
    }

    .autocomplete-item .item-subtitle {
        font-size: 0.875rem;
        color: #8592a3;
    }

    .autocomplete-item .item-badge {
        float: right;
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 4px;
        background-color: rgba(3, 195, 236, 0.1);
        color: #03c3ec;
    }

    .autocomplete-item.existing .item-badge {
        background-color: rgba(40, 200, 72, 0.1);
        color: #28c848;
    }

    /* Edit modal time clock settings panel */
    #editTimeClockSettings {
        background-color: rgba(3, 195, 236, 0.05);
        border: 1px solid rgba(3, 195, 236, 0.2);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    /* Edit autocomplete styles */
    #edit_autocomplete_results.autocomplete-results {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1050;
        max-height: 300px;
        overflow-y: auto;
    }

    #edit_autocomplete_results .autocomplete-item {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #edit_autocomplete_results .autocomplete-item:hover {
        background-color: rgba(105, 110, 255, 0.1);
    }

    #edit_autocomplete_results .autocomplete-item:last-child {
        border-bottom: none;
    }

    #edit_autocomplete_results .autocomplete-item .item-title {
        font-weight: 500;
        color: #566a7f;
        margin-bottom: 2px;
    }

    #edit_autocomplete_results .autocomplete-item .item-subtitle {
        font-size: 0.875rem;
        color: #8592a3;
    }

    #edit_autocomplete_results .autocomplete-item .item-badge {
        float: right;
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 4px;
        background-color: rgba(3, 195, 236, 0.1);
        color: #03c3ec;
    }

    #edit_autocomplete_results .autocomplete-item.existing .item-badge {
        background-color: rgba(40, 200, 72, 0.1);
        color: #28c848;
    }
</style>
@endpush


@push('scripts')
<script>
    // Gestion de l'affichage des paramètres de pointeuse
    document.addEventListener('DOMContentLoaded', function() {
        const hasTimeClockCheckbox = document.getElementById('hasTimeClock');
        const timeClockSettings = document.getElementById('timeClockSettings');

        if (hasTimeClockCheckbox) {
            hasTimeClockCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    timeClockSettings.style.display = 'block';
                } else {
                    timeClockSettings.style.display = 'none';
                }
            });
        }
    });

    function generateQRCode(locationId) {
        // Créer un formulaire dynamique pour soumettre la requête de génération
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('company.settings.attendance-system.qr-code-generate') }}";
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = "{{ csrf_token() }}";
        form.appendChild(csrfInput);
        
        const locationInput = document.createElement('input');
        locationInput.type = 'hidden';
        locationInput.name = 'location_id';
        locationInput.value = locationId;
        form.appendChild(locationInput);

        const sizeInput = document.createElement('input');
        sizeInput.type = 'hidden';
        sizeInput.name = 'qr_size';
        sizeInput.value = '500';
        form.appendChild(sizeInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    function editWorkLocation(id) {
        // Récupérer les données de l'emplacement depuis l'attribut data
        const locationCard = document.querySelector(`[data-location-id="${id}"]`);
        if (!locationCard) {
            alert('Emplacement non trouvé');
            return;
        }

        const locationData = JSON.parse(locationCard.getAttribute('data-location'));

        // Remplir le formulaire d'édition
        document.getElementById('edit_name').value = locationData.name || '';
        document.getElementById('edit_type').value = locationData.type || '';
        document.getElementById('edit_description').value = locationData.description || '';
        document.getElementById('edit_branch_id').value = locationData.branch_id || '';
        document.getElementById('edit_manager_id').value = locationData.manager_id || '';

        // Remplir l'adresse
        document.getElementById('edit_address_autocomplete').value = locationData.address || '';
        document.getElementById('edit_address').value = locationData.address || '';
        document.getElementById('edit_city').value = locationData.city || '';
        document.getElementById('edit_state').value = locationData.state || '';
        document.getElementById('edit_country').value = locationData.country || '';
        document.getElementById('edit_postal_code').value = locationData.postal_code || '';
        document.getElementById('edit_latitude').value = locationData.latitude || '';
        document.getElementById('edit_longitude').value = locationData.longitude || '';

        // Mettre à jour les champs d'affichage
        document.getElementById('edit_city_display').value = locationData.city || '';
        document.getElementById('edit_state_display').value = locationData.state || '';
        document.getElementById('edit_country_display').value = locationData.country || '';
        document.getElementById('edit_postal_code_display').value = locationData.postal_code || '';

        // Configurer les checkboxes
        document.getElementById('editHasTimeClock').checked = locationData.has_time_clock || false;
        document.getElementById('editAllowRemoteClock').checked = locationData.allow_remote_clock || false;
        document.getElementById('editIsActive').checked = locationData.is_active || false;

        // Configurer les paramètres de pointeuse si activée
        if (locationData.has_time_clock) {
            document.getElementById('editTimeClockSettings').style.display = 'block';
            document.getElementById('edit_check_in_start').value = locationData.check_in_start || '08:00';
            document.getElementById('edit_check_in_end').value = locationData.check_in_end || '09:30';
            document.getElementById('edit_check_out_start').value = locationData.check_out_start || '17:00';
            document.getElementById('edit_check_out_end').value = locationData.check_out_end || '18:30';
            document.getElementById('edit_max_distance_meters').value = locationData.max_distance_meters || '100';
        } else {
            document.getElementById('editTimeClockSettings').style.display = 'none';
        }

        // Mettre à jour l'action du formulaire
        document.getElementById('editWorkLocationForm').action = `{{ url('/company/settings/work-locations') }}/${id}`;

        // Changer la méthode du formulaire pour PUT
        const editForm = document.getElementById('editWorkLocationForm');
        let methodInput = editForm.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            editForm.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        // Ouvrir le modal
        const modal = new bootstrap.Modal(document.getElementById('editWorkLocationModal'));
        modal.show();
    }

    function deleteWorkLocation(id, name) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'emplacement "${name}" ? Cette action est irréversible.`)) {
            // Créer un formulaire de suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/company/settings/work-locations/${id}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Fonction pour basculer le statut actif/inactif d'un emplacement
    function toggleWorkLocationStatus(id, currentStatus) {
        const newStatus = !currentStatus;
        const statusText = newStatus ? 'activer' : 'désactiver';

        if (confirm(`Êtes-vous sûr de vouloir ${statusText} cet emplacement ?`)) {
            // Créer un formulaire pour la route toggle avec méthode PUT
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/company/settings/work-locations') }}/${id}/toggle`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Gestion de l'autocomplète des adresses
    document.addEventListener('DOMContentLoaded', function() {
        const autocompleteInput = document.getElementById('address_autocomplete');
        const resultsContainer = document.getElementById('autocomplete-results');
        const clearButton = document.getElementById('clear_address');

        let debounceTimer;
        let currentQuery = '';

        // Éléments à mettre à jour
        const addressField = document.getElementById('address');
        const cityField = document.getElementById('city');
        const stateField = document.getElementById('state');
        const countryField = document.getElementById('country');
        const postalCodeField = document.getElementById('postal_code');
        const latitudeField = document.getElementById('latitude');
        const longitudeField = document.getElementById('longitude');

        const cityDisplay = document.getElementById('city_display');
        const stateDisplay = document.getElementById('state_display');
        const countryDisplay = document.getElementById('country_display');
        const postalCodeDisplay = document.getElementById('postal_code_display');

        // Fonction pour rechercher des suggestions
        function searchLocations(query) {
            if (query.length < 2) {
                hideResults();
                return;
            }

            fetch(`{{url('/company/settings/api/location-suggestions?q=${encodeURIComponent(query)}&limit=8')}}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
                hideResults();
            });
        }

        // Afficher les résultats
        function displayResults(results) {
            if (results.length === 0) {
                hideResults();
                return;
            }

            resultsContainer.innerHTML = '';

            results.forEach(result => {
                const item = document.createElement('div');
                item.className = `autocomplete-item ${result.type === 'existing' ? 'existing' : ''}`;
                item.dataset.result = JSON.stringify(result);

                let badgeText = '';
                if (result.type === 'existing') {
                    badgeText = 'Existant';
                } else if (result.source === 'nominatim') {
                    badgeText = 'Nouveau';
                } else {
                    badgeText = 'Pays';
                }

                item.innerHTML = `
                    <div class="item-title">${result.text}</div>
                    ${result.city || result.state ? `<div class="item-subtitle">${[result.city, result.state].filter(Boolean).join(', ')}</div>` : ''}
                    <span class="item-badge">${badgeText}</span>
                `;

                item.addEventListener('click', () => selectResult(result));
                resultsContainer.appendChild(item);
            });

            resultsContainer.style.display = 'block';
        }

        // Masquer les résultats
        function hideResults() {
            resultsContainer.style.display = 'none';
        }

        // Sélectionner un résultat
        function selectResult(result) {
            // Mettre à jour le champ de recherche
            autocompleteInput.value = result.text;

            // Mettre à jour les champs cachés
            addressField.value = result.text || '';
            cityField.value = result.city || '';
            stateField.value = result.state || '';
            countryField.value = result.country || '';
            postalCodeField.value = result.postal_code || '';

            // Gérer les coordonnées GPS
            if (result.latitude && result.longitude && !isNaN(result.latitude) && !isNaN(result.longitude)) {
                latitudeField.value = result.latitude;
                longitudeField.value = result.longitude;
            } else {
                // Vider les champs de coordonnées si pas de valeurs valides
                latitudeField.value = '';
                longitudeField.value = '';
            }

            // Mettre à jour les champs d'affichage
            cityDisplay.value = result.city || '';
            stateDisplay.value = result.state || '';
            countryDisplay.value = result.country || '';
            postalCodeDisplay.value = result.postal_code || '';

            // Ajouter les attributs name aux champs qui ont des valeurs
            if (result.address) addressField.setAttribute('name', 'address');
            if (result.city) cityField.setAttribute('name', 'city');
            if (result.state) stateField.setAttribute('name', 'state');
            if (result.country) countryField.setAttribute('name', 'country');
            if (result.postal_code) postalCodeField.setAttribute('name', 'postal_code');
            if (result.latitude && result.longitude && !isNaN(result.latitude) && !isNaN(result.longitude)) {
                latitudeField.setAttribute('name', 'latitude');
                longitudeField.setAttribute('name', 'longitude');
            }

            // Masquer les résultats
            hideResults();

            // Activer la validation du formulaire
            autocompleteInput.setCustomValidity('');
        }

        // Effacer la sélection
        function clearSelection() {
            autocompleteInput.value = '';
            addressField.value = '';
            cityField.value = '';
            stateField.value = '';
            countryField.value = '';
            postalCodeField.value = '';
            latitudeField.value = '';
            longitudeField.value = '';

            cityDisplay.value = '';
            stateDisplay.value = '';
            countryDisplay.value = '';
            postalCodeDisplay.value = '';

            // Supprimer les attributs name des champs vides
            const emptyFields = ['address', 'city', 'state', 'country', 'postal_code', 'latitude', 'longitude'];
            emptyFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.removeAttribute('name');
                }
            });

            autocompleteInput.focus();
            hideResults();
            autocompleteInput.setCustomValidity('');
        }

        // Événements
        autocompleteInput.addEventListener('input', function() {
            currentQuery = this.value;

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchLocations(currentQuery);
            }, 300);
        });

        autocompleteInput.addEventListener('blur', function() {
            // Attendre un peu avant de masquer les résultats pour permettre le clic
            setTimeout(() => {
                hideResults();
            }, 200);
        });

        clearButton.addEventListener('click', clearSelection);

        // Masquer les résultats quand on clique en dehors
        document.addEventListener('click', function(e) {
            if (!autocompleteInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                hideResults();
            }
        });

        // Validation du formulaire
        const form = document.querySelector('form[action*="work-locations"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Restaurer les attributs name des champs qui ont des valeurs
                if (addressField.value.trim()) addressField.setAttribute('name', 'address');
                if (cityField.value.trim()) cityField.setAttribute('name', 'city');
                if (stateField.value.trim()) stateField.setAttribute('name', 'state');
                if (countryField.value.trim()) countryField.setAttribute('name', 'country');
                if (postalCodeField.value.trim()) postalCodeField.setAttribute('name', 'postal_code');
                if (latitudeField.value.trim() && longitudeField.value.trim()) {
                    latitudeField.setAttribute('name', 'latitude');
                    longitudeField.setAttribute('name', 'longitude');
                }

                // Validation finale
                if (!addressField.value || !autocompleteInput.value) {
                    e.preventDefault();
                    autocompleteInput.setCustomValidity('Veuillez sélectionner une adresse valide dans la liste des suggestions.');
                    autocompleteInput.reportValidity();
                    autocompleteInput.focus();
                    return false;
                }
                autocompleteInput.setCustomValidity('');
            });
        }
    });
</script>
@endpush
