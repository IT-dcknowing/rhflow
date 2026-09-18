@extends('layouts.app')

@section('title', 'Documents Société - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Documents Société</h4>
                        <p class="text-muted mb-0">Gérez tous les documents officiels de votre entreprise</p>
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
                        <button class="btn btn-primary" onclick="uploadAllDocuments()">
                            <i class="fas fa-upload me-1"></i>Import multiple
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Légaux de l'Entreprise -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Documents Légaux</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addDocumentModal">
                                <i class="fas fa-plus me-1"></i>Ajouter un Document
                            </button>
                            <span class="badge bg-label-info">{{ $companyDocuments->count() }} documents</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($companyDocuments->count() > 0)
                            {{-- Les attributs data-document-id et data-document sont repris tels quels sur
                                 la ligne : editDocument() les lit pour remplir le formulaire de modification. --}}
                            <div class="table-responsive">
                                <table class="table table-hover border-top dataTable no-footer" id="documents-legaux-table">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Type</th>
                                            <th>Expiration</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($companyDocuments as $document)
                                            <tr data-document-id="{{ $document->id }}"
                                                data-document='@json($document->toArray())'>
                                                <td>
                                                    <h6 class="mb-0">{{ $document->document_name }}</h6>
                                                    @if($document->description)
                                                        <small class="text-muted">{{ Str::limit($document->description, 80) }}</small>
                                                    @endif
                                                </td>
                                                <td><small class="text-muted">{{ $document->document_type_name }}</small></td>
                                                {{-- data-order : DataTables trie sur la date réelle, pas sur le libellé --}}
                                                <td data-order="{{ $document->expiry_date?->timestamp ?? 0 }}">
                                                    @if($document->expiry_date)
                                                        <small
                                                            class="{{ $document->isExpired() ? 'text-danger' : ($document->isExpiringSoon() ? 'text-warning' : 'text-success') }}">
                                                            {{ $document->expiry_date->format('d/m/Y') }}
                                                        </small>
                                                        @if($document->isExpired())
                                                            <span class="badge bg-label-danger ms-1">Expiré</span>
                                                        @elseif($document->isExpiringSoon())
                                                            <span class="badge bg-label-warning ms-1">Bientôt</span>
                                                        @endif
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td data-order="{{ $document->is_verified ? 1 : 0 }}">
                                                    <span
                                                        class="badge {{ $document->is_verified ? 'bg-label-success' : 'bg-label-warning' }}">
                                                        {{ $document->status_badge }}
                                                    </span>
                                                    @if($document->verified_by)
                                                        <small class="text-muted d-block">Par : {{ $document->verified_by }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        <a href="{{ url('/company/settings/company-documents/legal/' . $document->id . '/download') }}"
                                                            class="btn btn-icon btn-sm btn-label-primary" title="Télécharger">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-icon btn-sm btn-label-warning"
                                                            onclick="editDocument({{ $document->id }})" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if(!$document->is_verified)
                                                            <button type="button" class="btn btn-icon btn-sm btn-label-success"
                                                                onclick="toggleDocumentVerification({{ $document->id }})"
                                                                title="Vérifier">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-icon btn-sm btn-label-secondary"
                                                                onclick="toggleDocumentVerification({{ $document->id }})"
                                                                title="Dé-vérifier">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-icon btn-sm btn-label-danger"
                                                            onclick="deleteDocument({{ $document->id }}, '{{ addslashes($document->document_name) }}')"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-file-alt fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-muted">Aucun document légal</h5>
                                <p class="text-muted mb-4">Ajoutez vos documents officiels pour une meilleure conformité</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                    <i class="fas fa-plus me-1"></i>Ajouter un Document
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>















        <!-- Documents Principaux -->
        <div class="row mb-4">
            <!-- Logo de l'Entreprise -->
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Logo</h5>
                        <div class="d-flex gap-1">
                            <span class="badge bg-label-success">Principal</span>
                            @if($company->logo)
                                <span class="badge bg-label-info">Configuré</span>
                            @else
                                <span class="badge bg-label-warning">Requis</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($company->logo)
                                <img src="{{ url($company->logo_url) }}" alt="Logo" class="img-fluid mb-3"
                                    style="max-height: 120px; border-radius: 8px;">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-success">Logo configuré</div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-image fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-warning">Logo manquant</div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.logo.update') }}"
                            enctype="multipart/form-data" class="d-inline-block w-100">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="logo" accept="image/*" required
                                    style="display: none;" id="logoInput" onchange="previewLogo(this)">
                                <button type="button" class="btn btn-outline-primary w-100 mb-2"
                                    onclick="const inp = document.getElementById('logoInput'); inp.value = ''; inp.click();">
                                    <i
                                        class="fas fa-upload me-1"></i>{{ $company->logo ? 'Changer le Logo' : 'Uploader le Logo' }}
                                </button>
                                <button type="submit" class="btn btn-primary w-100" id="saveLogoBtn" {{ $company->logo ? 'disabled' : '' }}>
                                    <i class="fas fa-save me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>

                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <small class="text-muted">Aperçu:</small>
                            <br>
                            <img id="logoPreviewImg" src="" alt="Aperçu" class="img-fluid mt-1"
                                style="max-height: 60px; border-radius: 4px;">
                        </div>

                        <small class="text-muted d-block mt-2">
                            Formats: JPEG, PNG, GIF, SVG<br>
                            Taille max: 2MB<br>
                            Recommandé: 200x60px
                        </small>
                    </div>
                </div>
            </div>

            <!-- Signature Électronique -->
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Signature</h5>
                        <div class="d-flex gap-1">
                            <span class="badge bg-label-info">Documents</span>
                            @if($company->electronic_signature)
                                <span class="badge bg-label-success">Configurée</span>
                            @else
                                <span class="badge bg-label-secondary">Optionnel</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($company->electronic_signature)
                                <img src="{{url($company->electronic_signature_url) }}" alt="Signature" class="img-fluid mb-3"
                                    style="max-height: 120px; border-radius: 8px;">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-success">Signature configurée</div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-signature fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-secondary">Signature non configurée</div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.signature.update') }}"
                            enctype="multipart/form-data" class="d-inline-block w-100">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="signature" accept="image/*" required
                                    style="display: none;" id="signatureInput" onchange="previewSignature(this)">
                                <button type="button" class="btn btn-outline-info w-100 mb-2"
                                    onclick="const inp = document.getElementById('signatureInput'); inp.value = ''; inp.click();">
                                    <i
                                        class="fas fa-upload me-1"></i>{{ $company->electronic_signature ? 'Changer la Signature' : 'Uploader la Signature' }}
                                </button>
                                <button type="submit" class="btn btn-info w-100" id="saveSignatureBtn" {{ $company->electronic_signature ? 'disabled' : '' }}>
                                    <i class="fas fa-save me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>

                        <div id="signaturePreview" class="mt-2" style="display: none;">
                            <small class="text-muted">Aperçu:</small>
                            <br>
                            <img id="signaturePreviewImg" src="" alt="Aperçu" class="img-fluid mt-1"
                                style="max-height: 60px; border-radius: 4px;">
                        </div>

                        <small class="text-muted d-block mt-2">
                            Formats: JPEG, PNG, GIF<br>
                            Taille max: 2MB<br>
                            Recommandé: 300x100px
                        </small>
                    </div>
                </div>
            </div>

            <!-- Cachet Électronique -->
            <div class="col-xl-4 col-lg-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Cachet</h5>
                        <div class="d-flex gap-1">
                            <span class="badge bg-label-warning">Documents</span>
                            @if($company->electronic_stamp)
                                <span class="badge bg-label-success">Configuré</span>
                            @else
                                <span class="badge bg-label-secondary">Optionnel</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($company->electronic_stamp)
                                <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" class="img-fluid mb-3"
                                    style="max-height: 120px; border-radius: 8px;">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-success">Cachet configuré</div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-stamp fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge bg-label-secondary">Cachet non configuré</div>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('company.settings.stamp.update') }}"
                            enctype="multipart/form-data" class="d-inline-block w-100">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control" name="stamp" accept="image/*" required
                                    style="display: none;" id="stampInput" onchange="previewStamp(this)">
                                <button type="button" class="btn btn-outline-warning w-100 mb-2"
                                    onclick="const inp = document.getElementById('stampInput'); inp.value = ''; inp.click();">
                                    <i
                                        class="fas fa-upload me-1"></i>{{ $company->electronic_stamp ? 'Changer le Cachet' : 'Uploader le Cachet' }}
                                </button>
                                <button type="submit" class="btn btn-warning w-100" id="saveStampBtn" {{ $company->electronic_stamp ? 'disabled' : '' }}>
                                    <i class="fas fa-save me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>

                        <div id="stampPreview" class="mt-2" style="display: none;">
                            <small class="text-muted">Aperçu:</small>
                            <br>
                            <img id="stampPreviewImg" src="" alt="Aperçu" class="img-fluid mt-1"
                                style="max-height: 60px; border-radius: 4px;">
                        </div>

                        <small class="text-muted d-block mt-2">
                            Formats: JPEG, PNG, GIF<br>
                            Taille max: 2MB<br>
                            Recommandé: 150x150px
                        </small>
                    </div>
                </div>
            </div>
        </div>




        <!-- Statistiques des Documents -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Aperçu des Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="avatar avatar-lg mb-2 mx-auto" style="width: 60px; height: 60px;">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <span class="fa-24px">{{ $documentStats['total_documents'] }}</span>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Total Documents</h6>
                                    <small class="text-muted">Tous types confondus</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="avatar avatar-lg mb-2 mx-auto" style="width: 60px; height: 60px;">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <span class="fa-24px">{{ $documentStats['verified_documents'] }}</span>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Vérifiés</h6>
                                    <small class="text-muted">Documents approuvés</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="avatar avatar-lg mb-2 mx-auto" style="width: 60px; height: 60px;">
                                        <div class="avatar-initial bg-label-warning rounded">
                                            <span class="fa-24px">{{ $documentStats['expiring_soon_documents'] }}</span>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Bientôt Expirés</h6>
                                    <small class="text-muted">30 jours</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="avatar avatar-lg mb-2 mx-auto" style="width: 60px; height: 60px;">
                                        <div class="avatar-initial bg-label-danger rounded">
                                            <span class="fa-24px">{{ $documentStats['expired_documents'] }}</span>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Expirés</h6>
                                    <small class="text-muted">À renouveler</small>
                                </div>
                            </div>
                        </div>

                        @if(count($documentStats['missing_required']) > 0)
                            <div class="alert alert-warning">
                                <h6 class="mb-2">⚠️ Documents Requis Manquants</h6>
                                <p class="mb-0">Les documents suivants sont requis mais manquants :</p>
                                <ul class="mb-0 mt-2">
                                    @foreach($documentStats['missing_required'] as $missingType)
                                        <li>{{ \Modules\Settings\Models\CompanyDocument::DOCUMENT_TYPES[$missingType] ?? ucfirst($missingType) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des Modifications -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Historique des Modifications</h5>
                        <span class="badge bg-label-secondary">Audit</span>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @if($company->logo)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm me-3" style="width: 35px; height: 35px;">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="fas fa-image fa-18px"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">Logo mis à jour</h6>
                                                    <p class="text-muted mb-0">Logo d'entreprise configuré avec succès</p>
                                                </div>
                                                <small class="text-muted">Il y a
                                                    {{ $company->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($company->electronic_signature)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm me-3" style="width: 35px; height: 35px;">
                                            <div class="avatar-initial bg-label-info rounded">
                                                <i class="fas fa-signature fa-18px"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">Signature électronique ajoutée</h6>
                                                    <p class="text-muted mb-0">Signature numérique configurée pour les documents
                                                        officiels</p>
                                                </div>
                                                <small class="text-muted">Il y a
                                                    {{ $company->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($company->electronic_stamp)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm me-3" style="width: 35px; height: 35px;">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="fas fa-stamp fa-18px"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">Cachet électronique ajouté</h6>
                                                    <p class="text-muted mb-0">Cachet numérique configuré pour validation</p>
                                                </div>
                                                <small class="text-muted">Il y a
                                                    {{ $company->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!$company->logo && !$company->electronic_signature && !$company->electronic_stamp)
                                <div class="text-center py-4">
                                    <div class="avatar avatar-xl mb-3 mx-auto" style="width: 80px; height: 80px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-file-alt fa-32px"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Aucun document configuré</h6>
                                    <p class="text-muted mb-0">Commencez par uploader vos documents officiels</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal d'Ajout de Document -->
        <div class="modal fade" id="addDocumentModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Ajouter un Document Légal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('company.settings.company-documents.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type de Document *</label>
                                    <select class="form-select" name="document_type" required>
                                        <option value="">Sélectionner un type</option>
                                        @foreach(\Modules\Settings\Models\CompanyDocument::DOCUMENT_TYPES as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom du Document *</label>
                                    <input type="text" class="form-control" name="document_name" required
                                        placeholder="Ex: Statuts de l'entreprise 2024">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2"
                                    placeholder="Description du document..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'Émission</label>
                                    <input type="date" class="form-control" name="issue_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'Expiration</label>
                                    <input type="date" class="form-control" name="expiry_date">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fichier Document *</label>
                                <input type="file" class="form-control" name="file" required
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Formats acceptés: PDF, JPG, PNG, DOC, DOCX (max 10MB)</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_required" id="isRequired"
                                            value="1">
                                        <label class="form-check-label" for="isRequired">
                                            Document requis
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_verified" id="isVerified"
                                            value="1">
                                        <label class="form-check-label" for="isVerified">
                                            Vérifié par l'administration
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="verificationNotes" style="display: none;">
                                <label class="form-label">Notes de Vérification</label>
                                <textarea class="form-control" name="verification_notes" rows="2"
                                    placeholder="Notes sur la vérification du document..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter le Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Édition de Document -->
        <div class="modal fade" id="editDocumentModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="" id="editDocumentForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type de Document *</label>
                                    <select class="form-select" name="document_type" id="edit_document_type" required>
                                        <option value="">Sélectionner un type</option>
                                        @foreach(\Modules\Settings\Models\CompanyDocument::DOCUMENT_TYPES as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom du Document *</label>
                                    <input type="text" class="form-control" name="document_name" id="edit_document_name"
                                        required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'Émission</label>
                                    <input type="date" class="form-control" name="issue_date" id="edit_issue_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date d'Expiration</label>
                                    <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nouveau Fichier (optionnel)</label>
                                <input type="file" class="form-control" name="file"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Laissez vide pour garder le fichier actuel</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_required"
                                            id="edit_is_required" value="1">
                                        <label class="form-check-label" for="edit_is_required">
                                            Document requis
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_verified"
                                            id="edit_is_verified" value="1">
                                        <label class="form-check-label" for="edit_is_verified">
                                            Vérifié par l'administration
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="edit_verification_notes_container" style="display: none;">
                                <label class="form-label">Notes de Vérification</label>
                                <textarea class="form-control" name="verification_notes" id="edit_verification_notes"
                                    rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                        </div>
                    </form>
                </div>
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

        /* Timeline styling */
        .timeline-item {
            position: relative;
            padding-left: 50px;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 35px;
            bottom: -20px;
            width: 2px;
            background: linear-gradient(to bottom, rgba(105, 110, 255, 0.3), rgba(105, 110, 255, 0.1));
        }

        .timeline-item .avatar-initial {
            position: absolute;
            left: 0;
            top: 0;
        }

        /* Progress bar customization */
        .progress {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
            background: linear-gradient(135deg, #28c848 0%, #03c3ec 100%);
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
    </style>
@endpush

@push('scripts')
    <script>
        // Tableau des documents légaux, même configuration que les autres listes du projet
        // (cf. Paramètres › Sites) : colonne Actions non triable, libellés en français.
        $(function () {
            'use strict';

            @if($companyDocuments->count() > 0)
                $('#documents-legaux-table').DataTable({
                    processing: true,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { targets: -1, orderable: false, searchable: false }
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });
            @endif
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Validation des formulaires
            const forms = document.querySelectorAll('form[enctype="multipart/form-data"]');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    const fileInput = form.querySelector('input[type="file"]');
                    if (fileInput && fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        if (file.size > 2 * 1024 * 1024) { // 2MB
                            e.preventDefault();
                            alert('Le fichier ne doit pas dépasser 2MB');
                            return;
                        }

                        // Vérifier le type de fichier
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                        if (!allowedTypes.includes(file.type)) {
                            e.preventDefault();
                            alert('Format de fichier non autorisé. Utilisez JPEG, PNG, GIF ou SVG.');
                            return;
                        }
                    }
                });
            });
        });

        function previewLogo(input) {
            const saveBtn = document.getElementById('saveLogoBtn');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('logoPreviewImg').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
                if (saveBtn) saveBtn.disabled = false;
            } else {
                const preview = document.getElementById('logoPreview');
                if (preview) preview.style.display = 'none';
                @if($company->logo)
                    if (saveBtn) saveBtn.disabled = true;
                @else
                    if (saveBtn) saveBtn.disabled = false;
                @endif
                                        }
        }

        function previewSignature(input) {
            const saveBtn = document.getElementById('saveSignatureBtn');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('signaturePreviewImg').src = e.target.result;
                    document.getElementById('signaturePreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
                if (saveBtn) saveBtn.disabled = false;
            } else {
                const preview = document.getElementById('signaturePreview');
                if (preview) preview.style.display = 'none';
                @if($company->electronic_signature)
                    if (saveBtn) saveBtn.disabled = true;
                @else
                    if (saveBtn) saveBtn.disabled = false;
                @endif
                                        }
        }

        function previewStamp(input) {
            const saveBtn = document.getElementById('saveStampBtn');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('stampPreviewImg').src = e.target.result;
                    document.getElementById('stampPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
                if (saveBtn) saveBtn.disabled = false;
            } else {
                const preview = document.getElementById('stampPreview');
                if (preview) preview.style.display = 'none';
                @if($company->electronic_stamp)
                    if (saveBtn) saveBtn.disabled = true;
                @else
                    if (saveBtn) saveBtn.disabled = false;
                @endif
                                        }
        }

        function uploadAllDocuments() {
            // Créer un modal d'upload multiple
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">📤 Import multiple de documents</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="{{ route('company.settings.documents.update') }}" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row g-3">
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Logo</label>
                                                                    <input type="file" class="form-control" name="logo" accept="image/*">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Signature</label>
                                                                    <input type="file" class="form-control" name="signature" accept="image/*">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Cachet</label>
                                                                    <input type="file" class="form-control" name="stamp" accept="image/*">
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <button type="submit" class="btn btn-primary">Uploader Tout</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
            document.body.appendChild(modal);

            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();

            modal.addEventListener('hidden.bs.modal', function () {
                document.body.removeChild(modal);
            });
        }

        function downloadAllDocuments() {
            const documents = [];
            @if($company->logo)
                documents.push({ url: '{{ $company->logo_url }}', name: 'logo' });
            @endif
            @if($company->electronic_signature)
                documents.push({ url: '{{ $company->electronic_signature_url }}', name: 'signature' });
            @endif
            @if($company->electronic_stamp)
                documents.push({ url: '{{ $company->electronic_stamp_url }}', name: 'cachet' });
            @endif

                                        if (documents.length === 0) {
                alert('Aucun document à télécharger');
                return;
            }

            documents.forEach(doc => {
                const link = document.createElement('a');
                link.href = doc.url;
                link.download = `${doc.name}_${new Date().getTime()}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }

        function previewAllDocuments() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">👁️ Aperçu de Tous les Documents</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        @if($company->logo)
                                                            <div class="mb-4">
                                                                <h6>Logo</h6>
                                                                <img src="{{ $company->logo_url }}" alt="Logo" class="img-fluid" style="max-height: 150px;">
                                                            </div>
                                                        @endif
                                                        @if($company->electronic_signature)
                                                            <div class="mb-4">
                                                                <h6>Signature</h6>
                                                                <img src="{{ $company->electronic_signature_url }}" alt="Signature" class="img-fluid" style="max-height: 150px;">
                                                            </div>
                                                        @endif
                                                        @if($company->electronic_stamp)
                                                            <div class="mb-4">
                                                                <h6>Cachet</h6>
                                                                <img src="{{ $company->electronic_stamp_url }}" alt="Cachet" class="img-fluid" style="max-height: 150px;">
                                                            </div>
                                                        @endif
                                                        @if(!$company->logo && !$company->electronic_signature && !$company->electronic_stamp)
                                                            <p class="text-muted">Aucun document à afficher</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        `;
            document.body.appendChild(modal);

            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();

            modal.addEventListener('hidden.bs.modal', function () {
                document.body.removeChild(modal);
            });
        }

        function resetAllDocuments() {
            if (confirm('Êtes-vous sûr de vouloir supprimer tous les documents ? Cette action est irréversible.')) {
                // Implémenter la suppression de tous les documents
                alert('Suppression de tous les documents en cours de développement');
            }
        }

        // Gestion des documents de société
        function editDocument(id) {
            // Récupérer les données du document depuis l'attribut data
            const documentCard = document.querySelector(`[data-document-id="${id}"]`);
            if (!documentCard) {
                alert('Document non trouvé');
                return;
            }

            const documentData = JSON.parse(documentCard.getAttribute('data-document'));

            // Remplir le formulaire d'édition
            document.getElementById('edit_document_type').value = documentData.document_type || '';
            document.getElementById('edit_document_name').value = documentData.document_name || '';
            document.getElementById('edit_description').value = documentData.description || '';
            document.getElementById('edit_issue_date').value = documentData.issue_date || '';
            document.getElementById('edit_expiry_date').value = documentData.expiry_date || '';
            document.getElementById('edit_is_required').checked = documentData.is_required || false;
            document.getElementById('edit_is_verified').checked = documentData.is_verified || false;
            document.getElementById('edit_verification_notes').value = documentData.verification_notes || '';

            // Mettre à jour l'action du formulaire
            document.getElementById('editDocumentForm').action = `/company/settings/company-documents/legal/${id}`;

            // Changer la méthode du formulaire pour PUT
            const editForm = document.getElementById('editDocumentForm');
            let methodInput = editForm.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                editForm.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // Gérer l'affichage des notes de vérification
            const editVerificationNotesContainer = document.getElementById('edit_verification_notes_container');
            if (documentData.is_verified) {
                editVerificationNotesContainer.style.display = 'block';
            } else {
                editVerificationNotesContainer.style.display = 'none';
            }

            // Ouvrir le modal
            const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
            modal.show();
        }

        function deleteDocument(id, name) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le document "${name}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/company/settings/company-documents/legal/${id}`;

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

        function toggleDocumentVerification(id) {
            fetch(`/company/settings/company-documents/legal/${id}/toggle-verification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour refléter les changements
                        location.reload();
                    } else {
                        alert('Erreur lors de la mise à jour du statut de vérification');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour du statut de vérification');
                });
        }

                                    /* Déclarations globales des fonctions pour éviter les erreurs
                                    window.editDocument = function(id) {
                                        console.log('editDocument called with id:', id);
                                        // Récupérer les données du document depuis l'attribut data
                                        const documentCard = document.querySelector(`[data-document-id="${id}"]`);
                                        if (!documentCard) {
                                            alert('Document non trouvé');
                                            return;
                                        }

                                        const documentData = JSON.parse(documentCard.getAttribute('data-document'));
                                        console.log('Document data:', documentData);

                                        // Remplir le formulaire d'édition
                                        document.getElementById('edit_document_type').value = documentData.document_type || '';
                                        document.getElementById('edit_document_name').value = documentData.document_name || '';
                                        document.getElementById('edit_description').value = documentData.description || '';
                                        document.getElementById('edit_issue_date').value = documentData.issue_date || '';
                                        document.getElementById('edit_expiry_date').value = documentData.expiry_date || '';
                                        document.getElementById('edit_is_required').checked = documentData.is_required || false;
                                        document.getElementById('edit_is_verified').checked = documentData.is_verified || false;
                                        document.getElementById('edit_verification_notes').value = documentData.verification_notes || '';

                                        // Mettre à jour l'action du formulaire
                                        document.getElementById('editDocumentForm').action = `/company/settings/company-documents/legal/${id}`;

                                        // Changer la méthode du formulaire pour PUT
                                        const editForm = document.getElementById('editDocumentForm');
                                        let methodInput = editForm.querySelector('input[name="_method"]');
                                        if (!methodInput) {
                                            methodInput = document.createElement('input');
                                            methodInput.type = 'hidden';
                                            methodInput.name = '_method';
                                            editForm.appendChild(methodInput);
                                        }
                                        methodInput.value = 'PUT';

                                        // Gérer l'affichage des notes de vérification
                                        const editVerificationNotesContainer = document.getElementById('edit_verification_notes_container');
                                        if (documentData.is_verified) {
                                            editVerificationNotesContainer.style.display = 'block';
                                        } else {
                                            editVerificationNotesContainer.style.display = 'none';
                                        }

                                        // Ouvrir le modal
                                        const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
                                        modal.show();
                                    };

                                    window.deleteDocument = function(id, name) {
                                        console.log('deleteDocument called with id:', id, 'name:', name);
                                        if (confirm(`Êtes-vous sûr de vouloir supprimer le document "${name}" ? Cette action est irréversible.`)) {
                                            // Créer un formulaire de suppression
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = `/company/settings/company-documents/legal/${id}`;

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
                                    };

        window.toggleDocumentVerification = function (id) {
            console.log('toggleDocumentVerification called with id:', id);
            fetch(`/company/settings/company-documents/legal/${id}/toggle-verification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour refléter les changements
                        location.reload();
                    } else {
                        alert('Erreur lors de la mise à jour du statut de vérification');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour du statut de vérification');
                });
        };*/

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Company Documents JavaScript loaded successfully');

            // Test des fonctions
            console.log('editDocument function:', typeof editDocument);
            console.log('toggleDocumentVerification function:', typeof toggleDocumentVerification);
            console.log('deleteDocument function:', typeof deleteDocument);

            const isVerifiedCheckbox = document.getElementById('isVerified');
            const verificationNotes = document.getElementById('verificationNotes');

            if (isVerifiedCheckbox && verificationNotes) {
                isVerifiedCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        verificationNotes.style.display = 'block';
                    } else {
                        verificationNotes.style.display = 'none';
                    }
                });
            }

            // Gestion des checkboxes du modal d'édition
            const editIsVerifiedCheckbox = document.getElementById('edit_is_verified');
            const editVerificationNotesContainer = document.getElementById('edit_verification_notes_container');

            if (editIsVerifiedCheckbox && editVerificationNotesContainer) {
                editIsVerifiedCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        editVerificationNotesContainer.style.display = 'block';
                    } else {
                        editVerificationNotesContainer.style.display = 'none';
                    }
                });
            }
        });


        // Gestion des checkboxes du modal d'ajout
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Company Documents JavaScript loaded successfully');

            // Test des fonctions
            console.log('editDocument function:', typeof editDocument);
            console.log('toggleDocumentVerification function:', typeof toggleDocumentVerification);
            console.log('deleteDocument function:', typeof deleteDocument);

            const isVerifiedCheckbox = document.getElementById('isVerified');
            const verificationNotes = document.getElementById('verificationNotes');

            if (isVerifiedCheckbox && verificationNotes) {
                isVerifiedCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        verificationNotes.style.display = 'block';
                    } else {
                        verificationNotes.style.display = 'none';
                    }
                });
            }

            // Gestion des checkboxes du modal d'édition
            const editIsVerifiedCheckbox = document.getElementById('edit_is_verified');
            const editVerificationNotesContainer = document.getElementById('edit_verification_notes_container');

            if (editIsVerifiedCheckbox && editVerificationNotesContainer) {
                editIsVerifiedCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        editVerificationNotesContainer.style.display = 'block';
                    } else {
                        editVerificationNotesContainer.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endpush