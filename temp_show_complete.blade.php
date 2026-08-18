@extends('layouts.app')

@section('title', 'Détails Employé - ' . $employee->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête avec informations principales -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <div class="avatar-initial bg-label-primary rounded-circle">
                                {{ substr($employee->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $employee->name }}</h4>
                            <p class="text-muted mb-0">ID: {{ $employee->employee_id }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.employees.attestation-travail', $employee->id) }}" class="btn btn-outline-success">
                            <i class="ti ti-file-text me-1"></i>Attestation de travail
                        </a>
                        <a href="{{ route('company.employees.certificat-travail', $employee->id) }}" class="btn btn-outline-info">
                            <i class="ti ti-certificate me-1"></i>Certificat de travail
                        </a>
                        <a href="{{ route('company.employees.edit', $employee->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ route('company.employees.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-phone me-2 text-primary"></i>
                                <span>{{ $employee->phone ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-mail me-2 text-primary"></i>
                                <span>{{ $employee->email ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-map-pin me-2 text-primary"></i>
                                <span>{{ $employee->address ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-cake me-2 text-primary"></i>
                                <span>{{ $employee->dob ? $employee->dob->format('d/m/Y') : 'Non renseigné' }} ({{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->age : '-' }} ans)</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-gender-male me-2 text-primary"></i>
                                <span>{{ $employee->gender }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-flag me-2 text-primary"></i>
                                <span>{{ $employee->nationality ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">
                                <i class="ti ti-user me-1"></i>Détails personnel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#contract" role="tab">
                                <i class="ti ti-file-text me-1"></i>Détails contractuels
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#family" role="tab">
                                <i class="ti ti-users me-1"></i>Famille
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#events" role="tab">
                                <i class="ti ti-calendar-event me-1"></i>Évènements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ti ti-file me-1"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#payslips" role="tab">
                                <i class="ti ti-currency-dollar me-1"></i>Bulletins de paie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bank" role="tab">
                                <i class="ti ti-building-bank me-1"></i>Détail bancaire
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Onglet Détails personnel -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informations personnelles</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Nom complet:</td>
                                            <td><strong>{{ $employee->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Date de naissance:</td>
                                            <td>{{ $employee->dob ? $employee->dob->format('d/m/Y') : 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Genre:</td>
                                            <td>{{ $employee->gender }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nationalité:</td>
                                            <td>{{ $employee->nationality ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Téléphone:</td>
                                            <td>{{ $employee->phone ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td>{{ $employee->email ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Adresse:</td>
                                            <td>{{ $employee->address ?? 'Non renseigné' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Situation familiale</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Statut matrimonial:</td>
                                            <td>
                                                @if($employee->martalstatu_id == 1) Célibataire
                                                @elseif($employee->martalstatu_id == 2) Marié(e)
                                                @elseif($employee->martalstatu_id == 3) Divorcé(e)
                                                @elseif($employee->martalstatu_id == 4) Veuf(ve)
                                                @else Non défini @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Nombre d'enfants:</td>
                                            <td>{{ $employee->enfant ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Personnes à charge:</td>
                                            <td>{{ $employee->personneinf ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Parts fiscales:</td>
                                            <td>{{ $employee->parts ?? 'Non défini' }}</td>
                                        </tr>
                                        <tr>
                                            <td>CMU:</td>
                                            <td>{{ $employee->cmu ?? 'Non défini' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Détails contractuels -->
                        <div class="tab-pane fade" id="contract" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informations professionnelles</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Date d'embauche:</td>
                                            <td>{{ $employee->start_date ? $employee->start_date->format('d/m/Y') : 'Non défini' }}</td>
                                        </tr>
                                        @if($employee->end_date)
                                        <tr>
                                            <td>Date de fin:</td>
                                            <td>{{ $employee->end_date->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Département:</td>
                                            <td>{{ $employee->department->name ?? 'Non assigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Poste:</td>
                                            <td>{{ $employee->designation->name ?? 'Non assigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Branche:</td>
                                            <td>{{ $employee->branch->name ?? 'Non assignée' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Secteur:</td>
                                            <td>
                                                @php
                                                    $sectors = [
                                                        1 => 'Commerce',
                                                        2 => 'Industrie',
                                                        3 => 'Services',
                                                        4 => 'BTP',
                                                        5 => 'Agriculture'
                                                    ];
                                                @endphp
                                                {{ $sectors[$employee->id_secteur] ?? 'Non défini' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Statut employé:</td>
                                            <td>{{ $employee->statut_emp ?? 'Non défini' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informations financières</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Salaire mensuel:</td>
                                            <td class="text-success fw-bold">{{ number_format($employee->salary, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td>Type de salaire:</td>
                                            <td>
                                                @if($employee->salary_type == 1) Fixe
                                                @elseif($employee->salary_type == 2) Variable
                                                @elseif($employee->salary_type == 3) Commission
                                                @else Non défini @endif
                                            </td>
                                        </tr>
                                        @if($employee->salary_horaire)
                                        <tr>
                                            <td>Salaire horaire:</td>
                                            <td>{{ number_format($employee->salary_horaire, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Type de paie:</td>
                                            <td>
                                                @if($employee->paytype == 1) Virement bancaire
                                                @elseif($employee->paytype == 2) Espèces
                                                @elseif($employee->paytype == 3) Chèque
                                                @else Non défini @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Famille -->
                        <div class="tab-pane fade" id="family" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">Membres de la famille</h6>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
                                    <i class="ti ti-plus me-1"></i>Ajouter un membre
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénoms</th>
                                            <th>Date de naissance</th>
                                            <th>Genre</th>
                                            <th>Type de membre</th>
                                            <th>CMU</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($familyMembers as $member)
                                        <tr>
                                            <td>{{ $member->nom }}</td>
                                            <td>{{ $member->prenoms }}</td>
                                            <td>{{ $member->date_naiss_membre ? \Carbon\Carbon::parse($member->date_naiss_membre)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $member->genre_membre }}</td>
                                            <td>{{ $member->type_membre }}</td>
                                            <td>{{ $member->cmu }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Aucun membre de famille enregistré</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Évènements -->
                        <div class="tab-pane fade" id="events" role="tabpanel">
                            <h6 class="text-primary mb-3">Suivi des évènements</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Type</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($events as $event)
                                        <tr>
                                            <td>{{ $event->title }}</td>
                                            <td>{{ $event->type->name ?? 'N/A' }}</td>
                                            <td>{{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $event->end_date ? $event->end_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $event->status == 'published' ? 'success' : 'secondary' }}">
                                                    {{ $event->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Aucun évènement enregistré</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Documents -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">Documents de l'employé</h6>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                    <i class="ti ti-upload me-1"></i>Ajouter un document
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Libellé</th>
                                            <th>Type</th>
                                            <th>Taille</th>
                                            <th>Date d'ajout</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($documents as $document)
                                        @php
                                            $docData = json_decode($document->document_value, true);
                                        @endphp
                                        <tr>
                                            <td>{{ $document->libelle }}</td>
                                            <td>{{ $docData['type'] ?? 'N/A' }}</td>
                                            <td>{{ $docData['size'] ? number_format($docData['size'] / 1024, 2) . ' Ko' : 'N/A' }}</td>
                                            <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($docData['path'])
                                                <a href="{{ asset('storage/' . $docData['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                                @endif
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucun document enregistré</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Bulletins de paie -->
                        <div class="tab-pane fade" id="payslips" role="tabpanel">
                            <h6 class="text-primary mb-3">Bulletins de paie</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mois</th>
                                            <th>Salaire brut</th>
                                            <th>Salaire net</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paySlips as $paySlip)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($paySlip->salary_month)->format('F Y') }}</td>
                                            <td>{{ number_format($paySlip->basic_salary ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($paySlip->net_salary ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <span class="badge bg-success">Payé</span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-eye"></i> Voir
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-success">
                                                    <i class="ti ti-download"></i> Télécharger
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucun bulletin de paie disponible</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Détail bancaire -->
                        <div class="tab-pane fade" id="bank" role="tabpanel">
                            <h6 class="text-primary mb-3">Informations bancaires</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-secondary mb-3">Compte bancaire</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Banque:</td>
                                            <td>{{ $employee->bank_name ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Titulaire:</td>
                                            <td>{{ $employee->account_holder_name ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Numéro de compte:</td>
                                            <td>{{ $employee->account_number ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td>BIC/SWIFT:</td>
                                            <td>{{ $employee->bank_identifier_code ?? 'Non renseigné' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-secondary mb-3">Mobile Money</h6>
                                    <table class="table table-borderless">
                                        @if($employee->orange_money)
                                        <tr>
                                            <td width="30%">Orange Money:</td>
                                            <td>{{ $employee->orange_money }}</td>
                                        </tr>
                                        @endif
                                        @if($employee->mtn_money)
                                        <tr>
                                            <td>MTN Money:</td>
                                            <td>{{ $employee->mtn_money }}</td>
                                        </tr>
                                        @endif
                                        @if($employee->moov_money)
                                        <tr>
                                            <td>Moov Money:</td>
                                            <td>{{ $employee->moov_money }}</td>
                                        </tr>
                                        @endif
                                        @if($employee->wave_money)
                                        <tr>
                                            <td>Wave Money:</td>
                                            <td>{{ $employee->wave_money }}</td>
                                        </tr>
                                        @endif
                                        @if(!$employee->orange_money && !$employee->mtn_money && !$employee->moov_money && !$employee->wave_money)
                                        <tr>
                                            <td colspan="2" class="text-muted">Aucun compte mobile money enregistré</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter membre famille -->
<div class="modal fade" id="addFamilyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un membre de famille</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addFamilyForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénoms</label>
                            <input type="text" name="prenoms" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naiss_membre" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Genre</label>
                            <select name="genre_membre" class="form-select">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de membre</label>
                            <select name="type_membre" class="form-select">
                                <option value="Enfant">Enfant</option>
                                <option value="Conjoint">Conjoint</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CMU</label>
                            <input type="text" name="cmu" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveFamilyMember()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter document -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDocumentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Libellé du document</label>
                        <input type="text" name="libelle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fichier</label>
                        <input type="file" name="document" class="form-control" required>
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveDocument()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
function saveFamilyMember() {
    const form = document.getElementById('addFamilyForm');
    const formData = new FormData(form);
    
    fetch('{{ route("company.employees.family.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'ajout du membre de famille');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout du membre de famille');
    });
}

function saveDocument() {
    const form = document.getElementById('addDocumentForm');
    const formData = new FormData(form);
    
    fetch('{{ route("company.employees.documents.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'ajout du document');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout du document');
    });
}
</script>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid #e0e0e0;
}

.text-muted {
    color: #6c757d !important;
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.5rem;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
}
</style>
@endsection
