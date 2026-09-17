@extends('layouts.app')

@section('title', 'Sites & Succursales - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Sièges & Succursales</h4>
                        <p class="text-muted mb-0">Gérez les différents sites de votre entreprise</p>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBranchModal">
                            <i class="fas fa-plus me-1"></i>Nouvelle Succursale
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <x-kpi-grid>
            <x-kpi icon="fas fa-building" color="primary" label="Total" sublabel="Sites"
                :value="$stats['total_branches']" />

            <x-kpi icon="fas fa-check-circle" color="success" label="Actifs" sublabel="Sites"
                :value="$stats['active_branches']" />

            <x-kpi icon="fas fa-pause-circle" color="warning" label="Inactifs" sublabel="Sites"
                :value="$stats['inactive_branches']" />

            <x-kpi icon="fas fa-users" color="info" label="Services" sublabel="Au total"
                :value="$company->departments()->count()" />
        </x-kpi-grid>

        <!-- Liste des Succursales -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Succursales</h5>
                        <span class="badge bg-label-primary">{{ $stats['total_branches'] }} sites</span>
                    </div>
                    <div class="card-body">
                        @if($branches->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border-top dataTable no-footer" id="branches-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Site</th>
                                            <th>Type</th>
                                            <th>Coordonnées</th>
                                            <th>Manager</th>
                                            <th>Services</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($branches as $branch)
                                            <tr>
                                                <td><span class="badge bg-label-secondary">{{ $branch->code }}</span></td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ $branch->name }}</h6>
                                                        @if($branch->address)
                                                            <small class="text-muted">{{ $branch->address }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $branch->type === 'siege' ? 'bg-label-primary' : 'bg-label-secondary' }}">
                                                        {{ $branch->type === 'siege' ? 'Siège' : 'Succursale' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($branch->phone)
                                                        <div><small class="text-muted"><i
                                                                    class="fas fa-phone me-1"></i>{{ $branch->phone }}</small>
                                                        </div>
                                                    @endif
                                                    @if($branch->email)
                                                        <div><small class="text-muted"><i
                                                                    class="fas fa-envelope me-1"></i>{{ $branch->email }}</small>
                                                        </div>
                                                    @endif
                                                    @if(!$branch->phone && !$branch->email)
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($branch->manager)
                                                        <small><i
                                                                class="fas fa-user-tie me-1 text-muted"></i>{{ $branch->manager->name }}</small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-label-info">{{ $branch->departments->count() }}</span>
                                                </td>
                                                {{-- data-order : DataTables trie sur la valeur brute, pas sur le libelle --}}
                                                <td data-order="{{ $branch->is_active ? 1 : 0 }}">
                                                    <span
                                                        class="badge {{ $branch->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ $branch->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="editBranch({{ $branch->id }})" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if(!$branch->is_active)
                                                            <button class="btn btn-sm btn-outline-success"
                                                                onclick="activateBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}', false)"
                                                                title="Activer">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-warning"
                                                                onclick="activateBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}', true)"
                                                                title="Désactiver">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}')"
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
                                <div class="mb-3 d-flex justify-content-center align-items-center">
                                    <div class="avatar avatar-xl" style="width: 80px; height: 80px;">
                                        <div
                                            class="avatar-initial bg-label-secondary text-center justify-content-center align-items-center rounded">
                                            <i class="fas fa-building fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-muted">Aucune succursale configurée</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier site</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBranchModal">
                                    <i class="fas fa-plus me-1"></i>Créer une Succursale
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Création -->
    <div class="modal fade" id="createBranchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nouvelle Succursale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.branches.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la Succursale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="Ex: Siège Social, Succursale Nord..." id="branchName">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" required placeholder="SUCC-XXXX"
                                    id="branchCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de site <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" required id="branchType">
                                    <option value="succursale">Succursale</option>
                                    <option value="siege">Siège</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="address" rows="2"
                                placeholder="123 Rue de la Paix, 75001 Paris"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" name="phone" placeholder="+225 01 01 222 333">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="contact@site.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Manager du Site <span class="text-danger">*</span></label>
                            @if($companyUsers->count() > 0)
                                {{-- Une seule liste, quel que soit le nombre d'utilisateurs. Le markup était
                                     auparavant dupliqué selon un seuil (« > 10 »), avec deux rendus différents
                                     pour le même champ. Toutes les options restent dans le DOM, mais la liste
                                     déroulante n'en propose que LIMITE_MANAGERS à la fois (voir initSelect2) :
                                     les autres s'atteignent par la barre de recherche. --}}
                                <select class="form-select select2" name="manager_id" required
                                    data-placeholder="Sélectionner un manager">
                                    <option value="">Sélectionner un manager</option>
                                    @foreach($companyUsers as $user)
                                        {{-- Parenthèses seulement si l'email existe, sinon le libellé se terminait par « () ». --}}
                                        <option value="{{ $user->id }}">{{ $user->name }}@if($user->email) ({{ $user->email }})@endif</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Aucun utilisateur trouvé. Créez d'abord des utilisateurs dans votre entreprise.
                                </div>
                                <input type="hidden" name="manager_id" value="">
                            @endif
                        </div>

                        {{-- Création d'un manager sans quitter la modale. Les champs n'ont volontairement
                             ni « name » ni « required » : ils ne doivent ni partir avec le formulaire du
                             site, ni bloquer sa validation. --}}
                        <div class="mb-3">
                            <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2"
                                type="button" data-bs-toggle="collapse" data-bs-target="#nouveauManager">
                                <i class="fas fa-user-plus"></i>
                                <span>Créer un manager</span>
                            </button>

                            <div class="collapse mt-3" id="nouveauManager">
                                <div class="border rounded p-3">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="managerNom">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="managerEmail">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Téléphone</label>
                                            <input type="text" class="form-control" id="managerTelephone">
                                        </div>
                                    </div>

                                    {{-- Le mot de passe n'est plus saisi ici : il est généré à la création
                                         et affiché une seule fois ci-dessous, à transmettre au manager. --}}
                                    <div id="managerMotDePasseGenere" class="alert alert-success mt-3 mb-0 d-none">
                                    </div>

                                    <div id="managerErreur" class="text-danger small mt-2 d-none"></div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button"
                                            class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                                            id="enregistrerManager">
                                            <i class="fas fa-save"></i>
                                            <span>Enregistrer le manager</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                checked>
                            <label class="form-check-label" for="isActive">
                                Site actif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'Édition -->
    <div class="modal fade" id="editBranchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Modifier la Succursale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editBranchForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la Succursale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="Ex: Siège Social, Succursale Nord..." id="editBranchName">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" required placeholder="SUCC-XXXX"
                                    id="editBranchCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de site <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" required id="editBranchType">
                                    <option value="succursale">Succursale</option>
                                    <option value="siege">Siège</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="address" rows="2"
                                placeholder="123 Rue de la Paix, 75001 Paris" id="editBranchAddress"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" name="phone" placeholder="+225 01 01 222 333"
                                    id="editBranchPhone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="contact@site.com"
                                    id="editBranchEmail">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Manager du Site <span class="text-danger">*</span></label>
                            @if($companyUsers->count() > 0)
                                {{-- Même liste unique que dans la modale de création : seules
                                     LIMITE_MANAGERS entrées sont proposées à la fois, les autres
                                     s'atteignent par la barre de recherche (voir initSelect2). --}}
                                <select class="form-select select2" name="manager_id" id="editBranchManager" required
                                    data-placeholder="Sélectionner un manager">
                                    <option value="">Sélectionner un manager</option>
                                    @foreach($companyUsers as $user)
                                        {{-- Parenthèses seulement si l'email existe, sinon le libellé se terminait par « () ». --}}
                                        <option value="{{ $user->id }}">{{ $user->name }}@if($user->email) ({{ $user->email }})@endif</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Aucun utilisateur trouvé. Créez d'abord des utilisateurs dans votre entreprise.
                                </div>
                                <input type="hidden" name="manager_id" value="">
                            @endif
                        </div>

                        <input type="hidden" name="is_active" value="0" id="editBranchIsActiveHidden">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editBranchIsActive"
                                value="1">
                            <label class="form-check-label" for="editBranchIsActive">
                                Site actif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
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

        /* Select2 Custom Styles */
        .select2-container {
            width: 100% !important;
        }

        /* Thème « default » : c'est le seul dont la feuille est chargée par le layout.
           Ces règles ciblaient « bootstrap-5 », dont la CSS est absente du projet :
           la sélection n'était alors stylée par rien et le libellé sortait du cadre. */
        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            height: 38px;
        }

        /* Le layout pose déjà padding: 5px 10px sur la sélection : on ne rajoute pas
           le retrait horizontal par défaut de Select2, qui décalerait le texte. */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            padding-left: 0;
            padding-right: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6c757d transparent transparent transparent;
            border-width: 5px 4px 0 4px;
            margin-top: -2px;
        }

        .select2-container--default .select2-dropdown {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Filet de sécurité : le nombre d'entrées est déjà borné côté JS (LIMITE_MANAGERS),
           cette hauteur empêche tout déroulé inattendu de la liste. */
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 190px;
        }

        /* Au-dessus du fond de la modale, sinon la liste s'ouvre derrière. */
        .select2-container--open {
            z-index: 1060;
        }

        .select2-results__option {
            padding: 8px 12px;
        }

        .select2-results__option--selected {
            background-color: #696cff;
        }

        .select2-results__option--highlighted {
            background-color: rgba(105, 110, 255, 0.1);
            color: #253e87;
        }

        /* Code input styling */
        #branchCode {
            background-color: #f8f9fa;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Tableau des sites en DataTable (colonne Actions non triable / non filtrable)
        $(function () {
            'use strict';

            @if($branches->count() > 0)
                $('#branches-table').DataTable({
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

        // Générer automatiquement le code de la branche
        function generateBranchCode() {
            const nameInput = document.getElementById('branchName');
            const codeInput = document.getElementById('branchCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'SUCC-';

                    if (name.length > 0) {
                        // Prendre les 4 premières lettres du nom et les mettre en majuscules
                        const namePart = name.substring(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '');

                        // Ajouter un nombre aléatoire pour éviter les doublons
                        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

                        code += namePart + randomNum;
                    }

                    codeInput.value = code;
                });
            }
        }

        // Générer automatiquement le code de la branche pour l'édition
        function generateEditBranchCode() {
            const nameInput = document.getElementById('editBranchName');
            const codeInput = document.getElementById('editBranchCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'SUCC-';

                    if (name.length > 0) {
                        // Prendre les 4 premières lettres du nom et les mettre en majuscules
                        const namePart = name.substring(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '');

                        // Ajouter un nombre aléatoire pour éviter les doublons
                        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

                        code += namePart + randomNum;
                    }

                    codeInput.value = code;
                });
            }
        }

        // Nombre d'entrées proposées à la fois dans la liste des managers.
        // Les autres ne sont pas déroulées : on les atteint par la barre de recherche.
        const LIMITE_MANAGERS = 4;

        // Select2 sur la liste des managers.
        //
        // Corrections par rapport à la version d'origine :
        // - pas de theme 'bootstrap-5' : sa feuille de style n'est pas chargée par le projet,
        //   les classes émises n'étaient donc stylées par rien et le libellé sortait du cadre ;
        // - dropdownParent sur la modale : sans lui la liste s'ouvre derrière le fond ;
        // - destroy préalable : le layout initialise déjà .select2 au chargement de la page
        //   (resources/views/layouts/app.blade.php), sans dropdownParent. On repart de zéro ;
        // - source locale bornée : une simple hauteur de liste ne suffisait pas, tous les
        //   managers restaient présents et la liste défilait.
        function initSelect2() {
            if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
                return;
            }

            $('.select2').each(function () {
                const $select = $(this);
                const $modale = $select.closest('.modal');

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    placeholder: $select.data('placeholder') || 'Sélectionner un manager',
                    allowClear: true,
                    dropdownParent: $modale.length ? $modale : $(document.body),
                    // Source locale : les <option> du select, filtrées sur la saisie puis
                    // tronquées. Elles sont relues à chaque appel et non mises en cache,
                    // pour qu'un manager créé depuis cette modale soit aussitôt proposé.
                    ajax: {
                        delay: 0,
                        transport: function (parametres, reussite) {
                            const saisie = (parametres.data && parametres.data.term ? parametres.data.term : '')
                                .trim()
                                .toLowerCase();

                            const resultats = $select.find('option')
                                .toArray()
                                .filter(option => option.value !== '')
                                .map(option => ({ id: option.value, text: option.textContent.trim() }))
                                .filter(option => saisie === '' || option.text.toLowerCase().includes(saisie))
                                .slice(0, LIMITE_MANAGERS);

                            reussite({ results: resultats });

                            // Select2 attend un objet annulable en retour du transport.
                            return { abort: function () { } };
                        }
                    },
                    language: {
                        noResults: function () {
                            return "Aucun utilisateur trouvé";
                        },
                        searching: function () {
                            return "Recherche...";
                        }
                    }
                });
            });
        }

        // Création d'un manager sans quitter la modale du site.
        // Seul l'essentiel est demandé : l'identifiant de connexion est généré côté serveur,
        // comme pour les comptes créés depuis Paramètres › Utilisateurs.
        function creerManager() {
            const bouton = document.getElementById('enregistrerManager');
            const zoneErreur = document.getElementById('managerErreur');
            const nom = document.getElementById('managerNom').value.trim();
            const email = document.getElementById('managerEmail').value.trim();
            const telephone = document.getElementById('managerTelephone').value.trim();

            function afficherErreur(message) {
                zoneErreur.textContent = message;
                zoneErreur.classList.remove('d-none');
            }

            zoneErreur.classList.add('d-none');

            if (!nom || !email) {
                afficherErreur('Le nom et l\'email sont obligatoires.');
                return;
            }

            const libelleInitial = bouton.innerHTML;
            bouton.disabled = true;
            bouton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Enregistrement...</span>';

            fetch('{{ route('company.settings.branches.managers.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: nom, email: email, phone: telephone })
            })
                .then(reponse => reponse.json().then(donnees => ({ ok: reponse.ok, donnees })))
                .then(({ ok, donnees }) => {
                    if (!ok || !donnees.success) {
                        // Laravel renvoie les erreurs de validation dans « errors » (422).
                        const premiere = donnees.errors
                            ? Object.values(donnees.errors)[0][0]
                            : (donnees.message || 'Création impossible.');
                        afficherErreur(premiere);
                        return;
                    }

                    const listes = document.querySelectorAll('select[name="manager_id"]');

                    // Aucune liste sur la page : elle n'est rendue que s'il existe déjà un
                    // utilisateur. On recharge pour que le formulaire s'affiche correctement.
                    if (listes.length === 0) {
                        window.location.reload();
                        return;
                    }

                    listes.forEach(liste => {
                        const option = new Option(donnees.user.libelle, donnees.user.id, false, false);
                        liste.add(option);
                    });

                    // Sélectionner le nouveau manager dans la modale de création.
                    const listeCreation = document.querySelector('#createBranchModal select[name="manager_id"]');
                    if (listeCreation) {
                        listeCreation.value = donnees.user.id;
                    }

                    if (typeof $ !== 'undefined') {
                        $('select[name="manager_id"]').trigger('change');
                    }

                    document.getElementById('managerNom').value = '';
                    document.getElementById('managerEmail').value = '';
                    document.getElementById('managerTelephone').value = '';

                    // Le mot de passe n'est jamais réaffichable ensuite : il n'est stocké
                    // qu'en haché. On le laisse visible et le panneau ouvert pour qu'il
                    // puisse être relevé et transmis au manager.
                    const zoneMotDePasse = document.getElementById('managerMotDePasseGenere');
                    zoneMotDePasse.innerHTML = 'Manager créé. Identifiant : <strong>'
                        + donnees.user.identifiant + '</strong> — mot de passe provisoire : <strong>'
                        + donnees.motDePasse + '</strong><br>'
                        + '<small>Notez-le maintenant : il ne pourra plus être affiché.</small>';
                    zoneMotDePasse.classList.remove('d-none');
                })
                .catch(() => afficherErreur('Création impossible, réessayez.'))
                .finally(() => {
                    bouton.disabled = false;
                    bouton.innerHTML = libelleInitial;
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const bouton = document.getElementById('enregistrerManager');
            if (bouton) {
                bouton.addEventListener('click', creerManager);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Générer le code automatiquement
            generateBranchCode();
            generateEditBranchCode();

            // Initialiser Select2 si nécessaire
            initSelect2();

            // Réinitialiser le formulaire quand le modal se ferme
            const createModal = document.getElementById('createBranchModal');
            if (createModal) {
                createModal.addEventListener('hidden.bs.modal', function () {
                    // Réinitialiser les champs
                    document.getElementById('branchName').value = '';
                    document.getElementById('branchCode').value = '';
                    // Aucune des deux modales n'a de champ « description » : la ligne qui le
                    // remettait à zéro levait une TypeError et interrompait toute la suite de
                    // la réinitialisation (adresse, téléphone, email, manager, case à cocher).
                    document.getElementById('branchType').value = 'succursale';
                    document.querySelector('textarea[name="address"]').value = '';
                    document.querySelector('input[name="phone"]').value = '';
                    document.querySelector('input[name="email"]').value = '';
                    // Select2 ne suit pas une écriture directe de .value : sans « change »,
                    // l'ancien manager resterait affiché à la réouverture de la modale.
                    const listeManager = document.querySelector('#createBranchModal select[name="manager_id"]');
                    if (listeManager) {
                        listeManager.value = '';
                        if (typeof $ !== 'undefined') {
                            $(listeManager).trigger('change');
                        }
                    }

                    // Réinitialiser la checkbox
                    document.getElementById('isActive').checked = true;

                    // Masquer le mot de passe du manager créé : il ne doit pas réapparaître
                    // à la prochaine ouverture de la modale.
                    const zoneMotDePasse = document.getElementById('managerMotDePasseGenere');
                    if (zoneMotDePasse) {
                        zoneMotDePasse.classList.add('d-none');
                        zoneMotDePasse.innerHTML = '';
                    }
                });
            }

            // Réinitialiser le formulaire d'édition quand le modal se ferme
            const editModal = document.getElementById('editBranchModal');
            if (editModal) {
                editModal.addEventListener('hidden.bs.modal', function () {
                    // Réinitialiser les champs
                    document.getElementById('editBranchName').value = '';
                    document.getElementById('editBranchCode').value = '';
                    // Même remarque que pour la modale de création : pas de champ « description ».
                    document.getElementById('editBranchType').value = 'succursale';
                    document.getElementById('editBranchAddress').value = '';
                    document.getElementById('editBranchPhone').value = '';
                    document.getElementById('editBranchEmail').value = '';
                    const listeManagerEdition = document.getElementById('editBranchManager');
                    if (listeManagerEdition) {
                        listeManagerEdition.value = '';
                        if (typeof $ !== 'undefined') {
                            $(listeManagerEdition).trigger('change');
                        }
                    }
                    document.getElementById('editBranchIsActive').checked = true;

                    // Remettre l'action par défaut
                    document.getElementById('editBranchForm').action = '';
                });
            }
        });

        function editBranch(id) {
            // Récupérer les données de la branche via AJAX
            fetch(`{{ url('/company/settings/branches') }}/${id}/edit`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const branch = data.branch;

                        // Remplir le formulaire d'édition
                        document.getElementById('editBranchName').value = branch.name;
                        document.getElementById('editBranchCode').value = branch.code;
                        document.getElementById('editBranchAddress').value = branch.address || '';
                        document.getElementById('editBranchPhone').value = branch.phone || '';
                        document.getElementById('editBranchEmail').value = branch.email || '';

                        // Type de site (siège ou succursale)
                        const typeSelect = document.getElementById('editBranchType');
                        if (typeSelect) {
                            typeSelect.value = branch.type || 'succursale';
                        }

                        // Manager
                        const managerSelect = document.getElementById('editBranchManager');
                        if (managerSelect) {
                            managerSelect.value = branch.manager_id || '';
                        }

                        // Status actif
                        document.getElementById('editBranchIsActive').checked = branch.is_active;

                        // Mettre à jour l'action du formulaire
                        document.getElementById('editBranchForm').action = `{{ url('/company/settings/branches') }}/${id}`;

                        // Réinitialiser Select2 si nécessaire
                        if (typeof $ !== 'undefined' && $('.select2').length > 0) {
                            $('.select2').trigger('change');
                        }

                        // Ouvrir le modal
                        const modal = new bootstrap.Modal(document.getElementById('editBranchModal'));
                        modal.show();
                    } else {
                        alert('Erreur lors du chargement des données: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des données');
                });
        }

        function activateBranch(id, name, isActive) {
            const action = isActive ? 'désactiver' : 'activer';
            const confirmMessage = `Êtes-vous sûr de vouloir ${action} la succursale "${name}" ?`;

            if (confirm(confirmMessage)) {
                // Créer un formulaire pour la route toggle avec méthode PUT
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/branches') }}/${id}/toggle`;

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

        function deleteBranch(id, name) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer la succursale "${name}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression avec la route nommée
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/branches') }}/${id}`;

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
    </script>
@endpush