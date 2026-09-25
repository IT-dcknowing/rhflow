@extends('layouts.app')

@section('title', 'Détails du contrat - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            Détails du contrat</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.contracts.index') }}">Contrats</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $contract->employee?->name ?? 'Employé non défini' }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        {{-- Actions dépliées : le menu déroulant masquait « Imprimer » derrière
                             un second clic, alors que c'est l'action la plus courante ici. --}}
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2"
                                onclick="window.print()">
                                <i class="fas fa-print"></i>
                                <span>Imprimer</span>
                            </button>
                            <button type="button" class="btn btn-outline-info d-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#addAvenantModal">
                                <i class="fas fa-file-signature"></i>
                                <span>Ajouter un avenant</span>
                            </button>
                            <a href="{{ route('company.contracts.edit', $contract->id) }}"
                                class="btn btn-primary d-flex align-items-center gap-2">
                                <i class="fas fa-edit"></i>
                                <span>Modifier</span>
                            </a>
                            <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#deleteContractModal">
                                <i class="fas fa-trash"></i>
                                <span>Supprimer</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut du contrat -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">{{ strcasecmp(trim($contract->subject), 'cdi') === 0 ? 'Contrat à durée indéterminée' : $contract->subject }}</h5>

                            </div>
                            <div>
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-secondary',
                                        'accept' => 'bg-success',
                                        'reject' => 'bg-warning',
                                        'expired' => 'bg-danger'
                                    ];
                                    $statusLabel = [
                                        'pending' => 'Brouillon',
                                        'accept' => 'Actif',
                                        'reject' => 'Expiré',
                                        'reject' => 'Résilié'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClass[$contract->status] ?? 'bg-secondary' }} fs-6">
                                    {{ $statusLabel[$contract->status] ?? 'Inconnu' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informations du contrat -->
            <div class="col-md-8 mb-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informations du contrat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Employé</label>
                                    <p>{{ $contract->employee->name ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Type de contrat</label>
                                    <p>{{ $contract->type->name ?? 'Non défini' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Date de début</label>
                                    <p>{{ $contract->start_date ? date('d/m/Y', strtotime($contract->start_date)) : 'Non définie' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Date de fin</label>
                                    <p>{{ $contract->end_date ? date('d/m/Y', strtotime($contract->end_date)) : 'Non définie' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Durée</label>
                                    {{-- « duration » contient déjà son unité (ex. « 12 mois », « 2 an(s) et
                                         3 mois », « 45 jour(s) ») : le suffixe ajouté ici donnait « 12 mois
                                         mois », et « 45 jour(s) mois » pour une durée en jours. --}}
                                    <p>{{ $contract->duration ?? 'Non définie' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Valeur</label>
                                    <p>{{ $contract->value ? number_format($contract->value, 0, ',', ' ') . ' FCFA' : 'Non définie' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if($contract->description)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <div class="border rounded p-3 bg-light">
                                            {!! $contract->description !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($contract->notes)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Notes</label>
                                        <div class="border rounded p-3 bg-light">
                                            {!! $contract->notes !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Avenants -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Avenants</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addAvenantModal">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Avenant</th>
                                    <th>Libéllé Contrat</th>
                                    <th>Document</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contract->avenants as $avenant)
                                    <tr>
                                        <td>{{ $avenant->type_avenant }}</td>
                                        <td>{{ $contract->type->name }}</td>
                                        <td>
                                            @if($avenant->file_path)
                                                <a href="{{ route('company.contracts.avenant.download', $avenant->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i>Télécharger
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">Aucun fichier</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!--<a href="{{ route('company.contracts.avenant.edit', $avenant->id) }}" class="btn btn-icon btn-sm btn-label-warning" data-bs-toggle="tooltip" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>-->
                                                <form action="{{ route('company.contracts.avenant.destroy', $avenant->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avenant ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-label-danger"
                                                        data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">Aucun avenant trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Pièces jointes -->
                <div class="card mb-4" style="overflow: hidden;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pièces jointes</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($contract->attachments as $attachment)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3 gap-2">
                                    <div class="d-flex align-items-center text-truncate" style="min-width: 0; flex: 1;">
                                        <i class="fas fa-file me-2 text-primary flex-shrink-0"></i>
                                        <span class="text-truncate" title="{{ $attachment->file_name }}" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $attachment->file_name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center flex-shrink-0 gap-1">
                                        <a href="{{ route('company.contracts.attachment.download', $attachment->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteAttachmentModal"
                                            data-id="{{ $attachment->id }}" data-name="{{ $attachment->file_name }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-3 text-muted">Aucune pièce jointe</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('company.contracts.attachment.add', $contract->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" class="form-control" name="attachment" id="attachment" required>
                                <button class="btn btn-primary" type="submit">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Signatures</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Signature de l'employé</label>
                                @if($contract->employee_signature)
                                    <div class="border rounded p-3 text-center bg-white">
                                        <img src="{{ asset('storage/' . $contract->employee_signature) }}"
                                            alt="Signature de l'employé" class="img-fluid" style="max-height: 100px;">
                                    </div>
                                @else
                                    <div class="border rounded p-3 text-center">
                                        <p class="text-muted mb-0">Non signé</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        @if($contract->employee_signature)
                            <a href="{{ route('company.contracts.signature', $contract->id) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-edit me-1"></i>Modifier la signature
                            </a>
                        @else
                            <a href="{{ route('company.contracts.signature', $contract->id) }}" class="btn btn-primary w-100">
                                <i class="fas fa-signature me-1"></i>Gérer les signatures
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout d'avenant -->
    <div class="modal fade" id="addAvenantModal" tabindex="-1" aria-labelledby="addAvenantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAvenantModalLabel">Ajouter un avenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('company.contracts.avenant.add', $contract->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="contrat_id" id="contrat_id" value="{{ $contract->id }}">
                            <input type="hidden" name="emp_id" id="emp_id" value="{{ $contract->employee_id }}">

                            <label for="amendment_type" class="form-label">Type d'avenant</label>
                            <select class="form-select" id="amendment_type" name="amendment_type">
                                <option value=" ">Sélectionnez un type d'avenant</option>
                                <option value="Reconduction">Renouvellement de contrat</option>
                                <option value="Augmentation de salaire">Augmentation de salaire</option>
                                <option value="Réduction de salaire">Réduction de salaire</option>
                                <option value="Mobilité">Mobilité (nouveau site ou lieu de travail)</option>
                                <option value="Autres">Autres</option>
                            </select>
                        </div>
                        <hr>
                        <div id="amountField" class="mb-3" style="display: none">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="branch_id" class="form-label">Succursale<span class="text-danger pl-1">
                                            *</span></label>
                                    <div class="form-icon-user">
                                        <select class="form-select @error('branch_id') is-invalid @enderror"
                                            name="branch_id" id="branch_id" required>
                                            <option value="">Sélectionner une succursale</option>
                                            @if(!empty($branches))
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ $branch->id == $contract->employee->branch_id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" value="{{$contract->employee->branch_id}}" id="brch_id">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-icon-user" id="department_id">
                                        <label for="department_id" class="form-label">Department<span
                                                class="text-danger pl-1"> *</span></label>
                                        <select class="form-select department_id" name="department_id" id="department_id"
                                            required>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ $department->id == $contract->employee->department_id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="designation_id" class="form-label">Post<span class="text-danger pl-1">
                                            *</span></label>
                                    <div class="form-icon-user designation_div">
                                        <select class="form-select" id="designation_id" name="designation_id">
                                            @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}" {{ $designation->id == $contract->employee->designation_id ? 'selected' : '' }}>
                                                    {{ $designation->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="hidden" value="{{$contract->employee->designation_id}}" id="desgn_id">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contract_types_id" class="form-label">Type de contrat<span
                                            class="text-danger pl-1"> *</span></label>
                                    <select type="text" name="contract_types_id" class="form-select" id="contract_types_id"
                                        onchange="contractend()">
                                        <option value="">-- Sélectionner un contrat --</option>
                                        @if (!empty($contractTypes))
                                            @foreach ($contractTypes as $type)
                                                <option value="{{ $type->id }}" @if($contract->employee->contrat == $type->id)
                                                selected="selected" @endif>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contrat_start" class="form-label">Date de debut du contrat<span
                                            class="text-danger pl-1"> *</span></label>
                                    <input type="date" name="contrat_start" class="form-control current_date"
                                        id="contrat_start" autocomplete="off" placeholder="Date de debut du contrat"
                                        value="{{ $contract->start_date }}">
                                </div>
                                <div id="end" class="form-group col-md-6">
                                    <label for="contrat_end" class="form-label">Date de fin du contrat</label>
                                    <input type="date" name="contrat_end" class="form-control current_date" id="contrat_end"
                                        autocomplete="off" placeholder="Date de fin du contrat"
                                        value="{{ $contract->end_date }}">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="value" class="form-label">Valeur du contrat</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01"
                                                class="form-control @error('value') is-invalid @enderror" id="value"
                                                name="value" value="{{ old('value') }}">
                                            <span class="input-group-text">{{ (!empty($company->currency) && $company->currency !== 'XOF') ? $company->currency : 'FCFA' }}</span>
                                        </div>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="duration" class="form-label">Durée</label>
                                        <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                            id="duration" name="duration" value="{{ old('duration') }}"
                                            placeholder="Ex: 12 mois">
                                        @error('duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div id="sal_cate" style="display: none;">
                            <label for="avenant_amount" class="form-label">Ancien salaire catégoriel : <strong
                                    style="color: red">{{ number_format($contract->employee->salary, 0, '.', ' ') }}
                                    FCFA</strong> | Ancien salaire net : <strong
                                    style="color: red">{{ number_format($contract->employee->get_net_salary(), 0, '.', ' ') }}
                                    FCFA</strong></label>
                            <hr>
                            <div class="row">
                                @php
                                    $jobCategories = '';
                                    $jobCategories = $company->sector->getJobCategorieAttribute();
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <label for="category_job_id" class="form-label required">Sélectionner le type de
                                        catégorie</label>
                                    <select type="text" name="category_job_id" class="form-select" id="category_job_id">
                                        <option value="">-- Sélectionner un type de catégorie --</option>
                                        @foreach($jobCategories as $jobCategorie)
                                            <option value="{{ $jobCategorie->id }}" {{ $jobCategorie->id == $contract->employee->categorie ? 'selected' : '' }}>
                                                {{ $jobCategorie->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="category_id" class="form-label">Sélectionner la catégorie<span
                                            class="text-danger pl-1"> *</span></label>
                                    <select type="text" name="category_id" class="form-select" id="category_id">
                                        <option
                                            value="{{ !empty($contract->employee->sous_categorie) ? $contract->employee->sous_categorie : '' }}"
                                            data-salaireminie="{{ $contract->employee->salary_horaire }}"
                                            data-salairemensuel="{{ $contract->employee->salary }}">Catégorie :
                                            {{$contract->employee->sous_categorie}} / S. Horaire :
                                            {{$contract->employee->salary_horaire}} / S. Mensuel :
                                            {{$contract->employee->salary}}
                                        </option>
                                        <!-- Options will be dynamically added here based on the selection in the first dropdown -->
                                    </select>
                                    <input type="hidden" name="sous_cate" id="sous_cate" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="salaire_minima_horaire" class="form-label">Salaire Catégoriel
                                        Horaire</label>
                                    <input type="number" name="salaire_minima_horaire" class="form-control"
                                        id="salaire_minima_horaire" readonly
                                        value="{{ $contract->employee->salary_horaire }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="salaire_minima_mensuel" class="form-label">Salaire Catégoriel
                                        mensuel</label>
                                    <input type="number" name="salaire_minima_mensuel" class="form-control"
                                        id="salaire_minima_mensuel" readonly value="{{ $contract->employee->salary }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="stage" style="display: none;">
                            <label for="prime_stage" class="form-label">Entrer le montant de la prime</label>
                            <input type="number" name="prime_stage" class="form-control" id="prime_stage">
                        </div>
                        <div class="mb-3">
                            <label for="avenant_description" class="form-label">Description</label>
                            <textarea class="form-control" name="avenant_description" id="avenant_description"
                                rows="3"></textarea>
                        </div>
                        <div class="input-group">
                            <input type="file" class="form-control" name="attachment" id="attachment">
                            <button class="btn btn-primary" type="submit">Ajouter</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de suppression de pièce jointe -->
    <div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAttachmentModalLabel">Supprimer la pièce jointe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette pièce jointe : <strong id="delete-attachment-name"></strong>
                        ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteAttachmentForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression de contrat -->
    <div class="modal fade" id="deleteContractModal" tabindex="-1" aria-labelledby="deleteContractModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteContractModalLabel">Supprimer le contrat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce contrat : <strong>{{ $contract->subject }}</strong> ?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Cette action est irréversible. Toutes les pièces jointes et avenants associés seront également
                        supprimés.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('company.contracts.destroy', $contract->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Configuration du modal de suppression de pièce jointe
            $('#deleteAttachmentModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');

                const modal = $(this);
                modal.find('#delete-attachment-name').text(name);

                const form = document.getElementById('deleteAttachmentForm');
                form.action = `{{ route('company.contracts.attachment.delete', ':id') }}`.replace(':id', id);
            });

            document.getElementById('amendment_type').addEventListener('change', function () {
                var selectedValue = this.value;
                var amountField = document.getElementById('amountField');
                var sal_cate = document.getElementById('sal_cate');

                if (selectedValue === 'Reconduction') {
                    amountField.style.display = 'block';
                    sal_cate.style.display = 'block';
                } else if (selectedValue === 'Augmentation de salaire' || selectedValue === 'Réduction de salaire') {
                    amountField.style.display = 'none';
                    sal_cate.style.display = 'block';
                    document.getElementById('avenant_amount').value = '';
                } else {
                    amountField.style.display = 'none';
                    sal_cate.style.display = 'none';
                    document.getElementById('avenant_amount').value = ''; // Réinitialiser la valeur du champ de saisie du montant
                }
            });

            // Calcul automatique de la durée en fonction des dates
            const startDateInput = document.getElementById('contrat_start');
            const endDateInput = document.getElementById('contrat_end');
            const durationInput = document.getElementById('duration');

            function updateDuration() {
                if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);

                    if (endDate >= startDate) {
                        // Calculer la différence en mois
                        const diffYears = endDate.getFullYear() - startDate.getFullYear();
                        const diffMonths = endDate.getMonth() - startDate.getMonth();
                        const totalMonths = diffYears * 12 + diffMonths;

                        if (totalMonths === 0) {
                            const diffDays = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
                            durationInput.value = diffDays + ' jour(s)';
                        } else if (totalMonths < 12) {
                            durationInput.value = totalMonths + ' mois';
                        } else {
                            const years = Math.floor(totalMonths / 12);
                            const months = totalMonths % 12;
                            durationInput.value = years + ' an(s)' + (months > 0 ? ' et ' + months + ' mois' : '');
                        }
                    }
                }
            }

            startDateInput.addEventListener('change', updateDuration);
            endDateInput.addEventListener('change', updateDuration);

            // ==============================================
            // GESTION DES APPELS AJAX
            // ==============================================

            // Gestion du changement de branche
            $(document).on('change', 'select[name=branch_id]', function () {
                var branch_id = $(this).val();
                var token = $('meta[name="csrf-token"]').attr('content');

                if (branch_id) {
                    $.ajax({
                        url: "{{ route('company.employees.getdepartment') }}",
                        type: "POST",
                        data: {
                            _token: token,
                            branch_id: branch_id
                        },
                        dataType: "json",
                        success: function (data) {
                            var $departmentSelect = $('select[name="department_id"]');
                            $departmentSelect.empty();
                            $departmentSelect.append('<option value="">Sélectionner un service</option>');

                            $.each(data, function (key, value) {
                                $departmentSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
                        },
                        error: function (xhr, status, error) {
                            console.error("Erreur lors du chargement des services:", xhr.responseText);
                            alert("Une erreur est survenue lors du chargement des services.");
                        }
                    });
                } else {
                    $('select[name="department_id"]').empty().append('<option value="">Sélectionner un service</option>');
                    $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
                }
            });

            // Gestion du changement de département
            $(document).on('change', 'select[name=department_id]', function () {
                var department_id = $(this).val();
                var token = $('meta[name="csrf-token"]').attr('content');

                if (department_id) {
                    $.ajax({
                        url: "{{ route('company.employees.employee.json') }}",
                        type: "POST",
                        data: {
                            _token: token,
                            department_id: department_id
                        },
                        dataType: "json",
                        success: function (data) {
                            var $designationSelect = $('select[name="designation_id"]');
                            $designationSelect.empty();
                            $designationSelect.append('<option value="">Sélectionner un poste</option>');

                            if (data && Object.keys(data).length > 0) {
                                $.each(data, function (key, value) {
                                    $designationSelect.append('<option value="' + key + '">' + value + '</option>');
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Erreur lors du chargement des postes:", xhr.responseText);
                            alert("Une erreur est survenue lors du chargement des postes.");
                        }
                    });
                } else {
                    $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
                }
            });

            // Fonction pour gérer la visibilité des champs de catégorie et salaire
            function toggleCategoryFields(show) {
                if (show) {
                    $('#cat_id, #hor_sal_id, #mens_sal_id').show();
                } else {
                    $('#cat_id, #hor_sal_id, #mens_sal_id').hide();
                    $('#category_id, #salaire_minima_horaire, #salaire_minima_mensuel').val('');
                }
            }

            // Gestion du changement de type de catégorie
            $('#category_job_id').on('change', function () {
                var selectedText = $(this).find('option:selected').text().trim();

                if (selectedText === 'Stagiaire') {
                    toggleCategoryFields(false);
                    return;
                } else {
                    toggleCategoryFields(true);
                }

                var typeId = $(this).val();
                var $categorySelect = $('#category_id');

                if (typeId) {
                    $.ajax({
                        url: "{{ route('company.employees.get-categories-by-type', ':id') }}".replace(':id', typeId),
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $categorySelect.empty().append('<option value="">Sélectionner une catégorie</option>');

                            if (data && data.length > 0) {
                                $.each(data, function (index, item) {
                                    $categorySelect.append('<option value="' + item.id + '" data-salaireminie="' + item.salaire_minima_horaire + '" data-salairemensuel="' + item.salaire_minima_mensuel + '">Catégorie : ' + item.categorie + ' / S. Horaire : ' + item.salaire_minima_horaire + ' / S. Mensuel : ' + item.salaire_minima_mensuel + '</option>');
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Erreur lors du chargement des catégories:", xhr.responseText);
                            alert("Une erreur est survenue lors du chargement des catégories.");
                        }
                    });
                } else {
                    $categorySelect.empty().append('<option value="">Sélectionner une catégorie</option>');
                }
            });

            // Gestion du changement de catégorie
            $('#category_id').on('change', function () {
                var salaireHoraire = $('#category_id option:selected').data('salaireminie');
                var salaireMensuel = $('#category_id option:selected').data('salairemensuel');

                $('#salaire_minima_horaire').val(salaireHoraire);
                $('#salaire_minima_mensuel').val(salaireMensuel);
            });

            // Vérifier la valeur initiale
            var initialText = $('#category_job_id option:selected').text().trim();
            if (initialText === 'Stagiaire') {
                toggleCategoryFields(false);
            }
        });
    </script>
@endpush