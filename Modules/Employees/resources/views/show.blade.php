@extends('layouts.app')

@section('title', 'Détails Employé - ' . $employee->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
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
                            <p class="text-muted mb-0"><span class="fw-semibold">Matricule :</span> {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ route('company.employees.attestation-travail', $employee->id) }}" class="btn btn-outline-success">
                            <i class="fas fa-file-text me-1"></i>Attestation de travail
                        </a>
                        <a href="{{ route('company.employees.certificat-travail', $employee->id) }}" class="btn btn-outline-info">
                            <i class="fas fa-certificate me-1"></i>Certificat de travail
                        </a>
                        <a href="{{ route('company.employees.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                        <a href="{{ route('company.employees.edit', $employee->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(!$employee->start_date)
                        <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                            <span><i class="fas fa-file-contract me-2"></i>Aucun contrat associé : la date d'embauche n'est pas renseignée.</span>
                            <a href="{{ route('company.contracts.employee', $employee->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-plus me-1"></i>Associer un contrat
                            </a>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <span>{{ $employee->phone ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <span>{{ $employee->email ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-map me-2 text-primary"></i>
                                <span>{{ $employee->address ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-cake me-2 text-primary"></i>
                                <span>{{ $employee->dob ? $employee->dob->format('d/m/Y') : 'Non renseigné' }} ({{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->age : '-' }} ans)</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-{{$employee->gender ==  'Male' ? 'male':'female'}} me-2 text-primary"></i>
                                <span>{{$employee->gender ==  'Male' ? 'Masculin':'Feminin'}}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-flag me-2 text-primary"></i>
                                <span>{{ $employee->country->name ?? 'Non renseigné' }}</span>
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
                <div class="card-header border-bottom-0 pb-0">
                    {{-- onglets-defilants : défilement horizontal sans barre visible (cf. rhflow-design.css).
                         Le « pb-1 » d'origine ne réservait que 4 px, la barre native recouvrait les onglets. --}}
                    <div class="onglets-defilants" style="-webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none; overflow-x: auto; overflow-y: hidden;">
                        <ul class="nav nav-tabs card-header-tabs flex-nowrap" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link text-nowrap active" data-bs-toggle="tab" href="#personal" role="tab">
                                    <i class="fas fa-user me-1"></i>Détails personnel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#contract" role="tab">
                                    <i class="fas fa-file-text me-1"></i>Détails contractuels
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#family" role="tab">
                                    <i class="fas fa-users me-1"></i>Famille
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#events" role="tab">
                                    <i class="fas fa-calendar me-1"></i>Évènements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#documents" role="tab">
                                    <i class="fas fa-file me-1"></i>Documents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#payslips" role="tab">
                                    <i class="fas fa-file-text me-1"></i>Bulletins de paie
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#bank" role="tab">
                                    <i class="fas fa-building me-1"></i>Détail bancaire
                                </a>
                            </li>
                        </ul>
                    </div>
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
                                            <td>{{ $employee->country->name ?? 'Non renseigné' }}</td>
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
                                            <td>{{ $employee->company->sector->name ?? 'Non défini' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Statut employé:</td>
                                            @if($employee->is_active == 1)
                                            <td><span class="badge bg-{{ $employee->statut_emp ? 'warning' : 'success' }}">{{ $employee->statut_emp ?? 'Actif' }}</span></td>
                                            @else
                                            <td><span class="badge bg-danger">Incatif</span></td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Informations financières</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%">Salaire de base:</td>
                                            <td class="text-success fw-bold">{{ number_format($employee->salary, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td width="30%">Salaire Net:</td>
                                            @if($periode)
                                                <td class="text-success fw-bold">{{ number_format(($employee->get_net_salary($periode->id)), 0, ',', ' ') }} FCFA</td>
                                            @else
                                                <td class="text-success fw-bold">{{ number_format(($employee->get_net_salary()), 0, ',', ' ') }} FCFA</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Type de salaire:</td>
                                            <td>
                                                @if($employee->salary_type == 1) Mensuelle
                                                @elseif($employee->salary_type == 2) Journalier
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
                                                {{ $employee->paytypeEmp->name ?? 'Non renseigné' }}
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
                                    <i class="fas fa-plus me-1"></i>Ajouter un membre
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
                                            <td>
                                                {{$member->genre_membre ==  'M' ? 'Garçon':'Fille'}}
                                                <input type="hidden" id="edit-family-genre{{ $member->id }}" value="{{ $member->genre_membre }}">
                                                <input type="hidden" id="edit-family-num-cmu{{ $member->id }}" value="{{ $member->num_cmu }}">
                                                <input type="hidden" id="edit-family-doc{{ $member->id }}" value="{{ $member->document }}">
                                            </td>
                                            <td>{{ $member->type_membre }}</td>
                                            <td>{{ $member->cmu }}</td>
                                            <td>
                                                <button class="btn btn-icon btn-sm btn-label-warning" data-family-id="{{ $member->id }}" onclick="editFamilyMember({{ $member->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-label-danger" data-family-id="{{ $member->id }}" onclick="deleteFamilyMember({{ $member->id }})">
                                                    <i class="fas fa-trash"></i>
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
                                        @foreach($events as $event)
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
                                                <a href="{{ route('employees.events.show', ['employee' => $employee->id, 'event' => $event->id]) }}" class="btn btn-icon btn-sm btn-label-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($ruptures as $rupture)
                                        <tr>
                                            <td>{{ $rupture->ruptureType->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($rupture->solde, 0,'.',' ') ?? '0' }} FCFA</td>
                                            <td>{{ $rupture->notice_date ? \Carbon\Carbon::parse($rupture->notice_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $rupture->termination_date ? \Carbon\Carbon::parse($rupture->termination_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $rupture->status == 'completed' ? 'danger' : 'warning' }}">
                                                    {{ $rupture->status == 'completed' ? 'Fin de contrat' : ($rupture->status == 'rejected' ? 'Réjetée':'Approuvée') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('company.ruptures.show', $rupture->id) }}" class="btn btn-icon btn-sm btn-label-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if($ruptures->count() == 0 && $events->count() == 0)
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Aucun évènement enregistré</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Documents -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">Documents de l'employé</h6>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                    <i class="fas fa-upload me-1"></i>Ajouter un document
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
                                            $docData = json_decode($document->document_value, true) ?? [];
                                        @endphp
                                        <tr>
                                            <td>{{ $document->libelle }}</td>
                                            <td>{{ $docData['type'] ?? 'N/A' }}</td>
                                            <td>{{ ($docData['size'] ?? null) ? number_format($docData['size'] / 1024, 2) . ' Ko' : 'N/A' }}</td>
                                            <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if(!empty($docData['path']))
                                                <a href="{{ asset('storage/' . $docData['path']) }}" target="_blank" class="btn btn-icon btn-sm btn-label-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                                <button class="btn btn-icon btn-sm btn-label-danger" onclick="deleteDocument({{ $document->id }})">
                                                    <i class="fas fa-trash"></i>
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
                                            <th>Salaire imposable</th>
                                            <th>Retenues</th>
                                            <th>Salaire net</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paySlips as $paySlip)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}</td>
                                            <td>{{ number_format($paySlip->salary_brut ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($paySlip->net_imposable ?? 0, 0, ',', ' ') }} FCFA</td>                                            
                                            <td class="text-danger fw-bold">
                                                {{ number_format($paySlip->total_retenue ?? 0, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-success fw-bold">{{ number_format($paySlip->net_payble ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <a href="#" class="btn btn-icon btn-sm btn-label-info" id="showBulletin" data-payslip-id="{{ $paySlip->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Aucun bulletin de paie disponible</td>
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
                <form id="addFamilyForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénoms</label>
                            <input type="text" name="prenoms" id="prenoms" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naiss_membre" id="date_naiss_membre" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Genre</label>
                            <select name="genre_membre" id="genre_membre" class="form-select">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de membre</label>
                            <select name="type_membre" id="type_membre" class="form-select">
                                <option value="Enfant">Enfant</option>
                                <option value="Conjoint">Conjoint</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CMU</label>
                            <select name="cmu" id="cmu" class="form-select" onchange="toggleCmuDocument(this.value)">
                                <option value="Non">Non</option>
                                <option value="Oui">Oui</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="cmu_document_field">
                            <label class="form-label">numero CMU</label>
                            <input type="text" name="num_cmu" id="num_cmu" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Document CMU</label>
                            <input type="file" name="cmu_document" id="cmu_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>  
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveFamilyMember()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier membre famille -->
<div class="modal fade" id="editFamilyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier un membre de famille</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editFamilyForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editFamilyId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" id="edit_nom" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénoms</label>
                            <input type="text" id="edit_prenoms" name="prenoms" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" id="edit_date_naiss_membre" name="date_naiss_membre" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Genre</label>
                            <select id="edit_genre_membre" name="genre_membre" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="Masculin">Masculin</option>
                                <option value="Féminin">Féminin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de membre</label>
                            <select id="edit_type_membre" name="type_membre" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="Enfant">Enfant</option>
                                <option value="Conjoint">Conjoint</option>
                                <option value="Parent">Parent</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CMU</label>
                            <select id="edit_cmu" name="cmu" class="form-select" required onchange="toggleEditCmuDocument(this.value)">
                                <option value="">Sélectionner</option>
                                <option value="Oui">Oui</option>
                                <option value="Non">Non</option>  
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3" id="edit_num_cmu_field" style="display: none;">
                            <label class="form-label">Numéro CMU</label>
                            <input type="text" id="edit_num_cmu" name="num_cmu" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Document CMU</label>
                            <input type="file" id="edit_cmu_document" name="cmu_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="updateFamilyMember()">Mettre à jour</button>
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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveDocument()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Afficher le bulletin -->
<div class="modal fade" id="showBulletinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulletin de paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bulletinContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2">Chargement du bulletin...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCmuDocument(value) {
        const cmuDocField = document.getElementById('cmu_document_field');
        if (value === 'Oui') {
            cmuDocField.style.display = 'block';
        } else {
            cmuDocField.style.display = 'none';
        }
    }

    function toggleEditCmuDocument(value) {
        const cmuNumField = document.getElementById('edit_num_cmu_field');
        if (value === 'Oui') {
            cmuNumField.style.display = 'block';
        } else {
            cmuNumField.style.display = 'none';
        }
    }

    function saveFamilyMember() {
        const form = document.getElementById('addFamilyForm');
        
        // Créer manuellement les données du formulaire pour éviter les problèmes avec FormData
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        // Ajouter manuellement chaque champ
        formData.append('nom', document.getElementById('nom').value);
        formData.append('prenoms', document.getElementById('prenoms').value);
        formData.append('date_naiss_membre', document.getElementById('date_naiss_membre').value);
        formData.append('genre_membre', document.getElementById('genre_membre').value);
        formData.append('type_membre', document.getElementById('type_membre').value);
        formData.append('cmu', document.getElementById('cmu').value);
        formData.append('num_cmu', document.getElementById('num_cmu').value);
        formData.append('employee_id', '{{ $employee->id }}');
        formData.append('company_id', '{{ auth()->user()->company_id }}');
        
        // Ajouter le fichier s'il est sélectionné
        const fileInput = document.getElementById('cmu_document');
        if (fileInput.files.length > 0) {
            formData.append('cmu_document', fileInput.files[0]);
        }
        
        // Debug: afficher les données envoyées
        console.log('Données envoyées (save):');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('{{ route("company.employees.family.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur de validation');
                    });
                }
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Afficher la notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: data.message || 'Membre de famille ajouté avec succès',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                console.log('Response data:', data);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.message || 'Erreur lors de l\'ajout du membre de famille'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                text: error.message || 'Erreur lors de l\'ajout du membre de famille'
            });
        });
    }

    function editFamilyMember(id) {
        // Récupérer les données du membre de famille depuis le tableau
        const row = document.querySelector(`[data-family-id="${id}"]`).closest('tr');
        const cells = row.getElementsByTagName('td');
        
        // Remplir le formulaire de modification
        document.getElementById('editFamilyId').value = id;
        document.getElementById('edit_nom').value = cells[0].textContent.trim();
        document.getElementById('edit_prenoms').value = cells[1].textContent.trim();
        
        // Gérer la date de naissance
        const dateText = cells[2].textContent.trim();
        if (dateText !== 'N/A') {
            const [day, month, year] = dateText.split('/');
            document.getElementById('edit_date_naiss_membre').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        }
        
        document.getElementById('edit_genre_membre').value = document.getElementById('edit-family-genre'+ id).value;
        document.getElementById('edit_num_cmu').value = document.getElementById('edit-family-num-cmu'+ id).value;
        // Ne pas définir la valeur du champ file - impossible en JavaScript pour des raisons de sécurité
        // document.getElementById('edit_cmu_document').value = document.getElementById('edit-family-doc'+ id).value;
        document.getElementById('edit_type_membre').value = cells[4].textContent.trim();
        document.getElementById('edit_cmu').value = cells[5].textContent.trim();
        
        // Afficher/masquer les champs CMU selon la valeur
        toggleEditCmuDocument(cells[5].textContent.trim());
        
        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('editFamilyModal'));
        modal.show();
    }

    function updateFamilyMember() {
        const id = document.getElementById('editFamilyId').value;
        const form = document.getElementById('editFamilyForm');
        
        // Créer manuellement les données du formulaire pour éviter les problèmes avec FormData
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('id', id);
        
        // Ajouter manuellement chaque champ
        formData.append('nom', document.getElementById('edit_nom').value);
        formData.append('prenoms', document.getElementById('edit_prenoms').value);
        formData.append('date_naiss_membre', document.getElementById('edit_date_naiss_membre').value);
        formData.append('genre_membre', document.getElementById('edit_genre_membre').value);
        formData.append('type_membre', document.getElementById('edit_type_membre').value);
        formData.append('cmu', document.getElementById('edit_cmu').value);
        formData.append('num_cmu', document.getElementById('edit_num_cmu').value);
        
        // Ajouter le fichier s'il est sélectionné
        const fileInput = document.getElementById('edit_cmu_document');
        if (fileInput.files.length > 0) {
            formData.append('cmu_document', fileInput.files[0]);
        }
        
        // Debug: afficher les données envoyées
        console.log('Données envoyées:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch(`{{ route('company.employees.family.update', ':id') }}`.replace(':id', id), {
            method: 'POST', // Utiliser POST avec _method: PUT pour FormData
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur de validation');
                    });
                }
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Afficher la notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: data.message || 'Membre de famille mis à jour avec succès',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                console.log('Response data:', data);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.message || 'Erreur lors de la mise à jour du membre de famille'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                text: error.message || 'Erreur lors de la mise à jour du membre de famille'
            });
        });
    }

    function deleteFamilyMember(id) {
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action supprimera définitivement ce membre de famille!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('company.employees.family.destroy', ':id') }}`.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Supprimé!',
                            'Le membre de famille a été supprimé avec succès.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Erreur!',
                            data.message || 'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Erreur!',
                        'Une erreur est survenue lors de la suppression.',
                        'error'
                    );
                });
            }
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
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur de validation');
                    });
                }
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Afficher la notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: data.message || 'Document ajouté avec succès',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                console.log('Response data:', data);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.message || 'Erreur lors de l\'ajout du document'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                text: error.message || 'Erreur lors de l\'ajout du document'
            });
        });
    }

    function deleteDocument(id) {
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action supprimera définitivement ce document!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('company.employees.documents.destroy', ':id') }}`.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Supprimé!',
                            data.message || 'Le document a été supprimé avec succès.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Erreur!',
                            data.message || 'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Erreur!',
                        'Erreur lors de la suppression du document.',
                        'error'
                    );
                });
            }
        });
    }

    // Variable globale pour stocker l'URL du bulletin courant
    let currentBulletinUrl = '';

    // Fonction pour afficher le bulletin dans le modal
    function showBulletin(paySlipId) {
        currentBulletinUrl = `{{ route('company.employees.bulletins.show', ':id') }}`.replace(':id', paySlipId);
        
        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('showBulletinModal'));
        modal.show();
        
        // Charger le contenu du bulletin
        loadBulletinContent(currentBulletinUrl);
    }

    // Fonction pour charger le contenu du bulletin
    function loadBulletinContent(url) {
        const bulletinContent = document.getElementById('bulletinContent');
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement du bulletin');
                }
                return response.text();
            })
            .then(html => {
                // Afficher le contenu HTML dans le modal
                bulletinContent.innerHTML = html;
                
                // Ajuster les styles pour l'affichage dans le modal
                const content = bulletinContent.querySelector('body') || bulletinContent;
                content.style.overflowY = 'auto';
            })
            .catch(error => {
                console.error('Error:', error);
                bulletinContent.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Erreur lors du chargement du bulletin</p>
                        <small>${error.message}</small>
                    </div>
                `;
            });
    }

    // Attacher l'événement click aux boutons avec la classe showModale
    $(document).ready(function() {
        console.log('DOM ready, attaching showModale events');
        
        $('#showBulletin').on('click', function(e) {
            e.preventDefault();
            var paySlipId = $(this).data('payslip-id'); 
            console.log('PaySlip ID clicked:', paySlipId);
            console.log('Bootstrap available:', typeof bootstrap);
            showBulletin(paySlipId);
        });
    });

    // Gérer l'affichage du logo
    function afficheLogSign() {
        var logo = document.getElementById("logoShow");
        var signature = document.getElementById("signatureShow");
        
        logo.style.display = 'block';
        signature.classList.remove('d-none');
        console.log('Logo et signature affichés');
    }

    function cacheLogSign() {
        var logo = document.getElementById("logoShow");
        var signature = document.getElementById("signatureShow");
        
        logo.style.display = 'none';
        signature.classList.add('d-none');
        console.log('Logo et signature cachés');
    }

    function afficheLogSign2() {
        var logo = document.getElementById("logoShow2");
        var signature = document.getElementById("signatureShow2");
        
        logo.style.display = 'block';
        signature.classList.remove('d-none');
        console.log('Logo et signature affichés');
    }

    function cacheLogSign2() {
        var logo = document.getElementById("logoShow2");
        var signature = document.getElementById("signatureShow2");
        
        logo.style.display = 'none';
        signature.classList.add('d-none');
        console.log('Logo et signature cachés');
    }

    function afficheLogSign3() {
        var logo = document.getElementById("logoShow3");
        var signature = document.getElementById("signatureShow3");
        
        logo.style.display = 'block';
        signature.classList.remove('d-none');
        console.log('Logo et signature affichés');
    }

    function cacheLogSign3() {
        var logo = document.getElementById("logoShow3");
        var signature = document.getElementById("signatureShow3");
        
        logo.style.display = 'none';
        signature.classList.add('d-none');
        console.log('Logo et signature cachés');
    }

    // Fonction générique pour télécharger un bulletin en PDF
    async function downloadBulletin(elementId, btnId, filename) {
        var element = document.getElementById(elementId);
        const button = document.getElementById(btnId);
        
        if (!element) {
            console.error(`Element with id ${elementId} not found`); 
            return;
        }
        console.log(element);
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="fa fa-spinner fa-spin me-2"></span> Génération...';

		try {
			// Charger les bibliothèques si elles ne sont pas déjà disponibles
			if (typeof jspdf === 'undefined') {
				await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
				await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
			}

			// Créer un nouveau document PDF
			const { jsPDF } = window.jspdf;
			var doc = new jsPDF('p', 'mm', 'a4');

			// Utiliser doc.html() pour générer du texte vectoriel
			if (element && element.innerHTML.trim() !== '') {
				await doc.html(element, {
					startY: 15,
					margin: [15, 5, 5, 5],
					width: 190,
					windowWidth: element.scrollWidth || element.clientWidth,
                    autoPaging: 'text',
					x: 0,
					y: 0,
					html2canvas: {
						scale: 0.19,
						useCORS: true,
						allowTaint: true,
						logging: false
					},
					callback: function(doc) {
						// Supprimer les pages vides à la fin
						const pageCount = doc.internal.getNumberOfPages();
						for (let i = pageCount; i > 1; i--) {
							doc.deletePage(i);
						}
					}
				});
			} else {
				throw new Error('Contenu du bulletin non trouvé ou vide');
			}

			// Sauvegarder le PDF
			doc.save(`${filename}.pdf`);

		} catch (error) {
			console.error('Erreur lors de la génération du PDF :', error);
			Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la génération du PDF'
            });
		} finally {
			button.disabled = false;
			button.innerHTML = originalText;
		}
	}

	// Fonction utilitaire pour charger un script
	function loadScript(src) {
		return new Promise((resolve, reject) => {
			const script = document.createElement('script');
			script.src = src;
			script.onload = resolve;
			script.onerror = reject;
			document.head.appendChild(script);
		});
	}
</script>
@endpush    
  
