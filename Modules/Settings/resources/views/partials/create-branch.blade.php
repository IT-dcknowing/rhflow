@extends('layouts.app')

@section('title', 'Sites & Succursales - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Siéges & Succursales</h4>
                        <p class="text-muted mb-0">Gérez les différents sites de votre entreprise</p>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBranchModal">
                            <i class="fas fa-plus me-1"></i>Nouvelle Succursale
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Sites Configurés</h5>
                        <span class="badge bg-label-primary">Nouveau site</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company.settings.branches.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom de la Succursale <span
                                                class="text-danger">*</span></label>
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

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="2"
                                        placeholder="Description du site..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Adresse</label>
                                    <textarea class="form-control" name="address" rows="2"
                                        placeholder="123 Rue de la Paix, 75001 Paris"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" name="phone"
                                            placeholder="+225 01 01 222 333">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="contact@site.com">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Manager du Site <span class="text-danger">*</span></label>
                                    @if($companyUsers->count() > 0)
                                        @if($companyUsers->count() > 10)
                                            <!-- Select avec recherche pour plus de 100 utilisateurs -->
                                            <select class="form-select select2" name="manager_id"
                                                data-placeholder="Rechercher un utilisateur...">
                                                <option value="">Aucun manager</option>
                                                @foreach($companyUsers as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <!-- Select simple pour moins de 100 utilisateurs -->
                                            <select class="form-select" name="manager_id">
                                                <option value="">Aucun manager</option>
                                                @foreach($companyUsers as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Aucun utilisateur trouvé. Créez d'abord des utilisateurs dans votre entreprise.
                                        </div>
                                        <input type="hidden" name="manager_id" value="">
                                    @endif
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Créer</button>
                            </div>
                        </form>
                    </div>
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

        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            min-height: 38px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding: 6px 12px;
            line-height: 1.5;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
            right: 12px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow b {
            border-color: #6c757d transparent transparent transparent;
            border-width: 5px 4px 0 4px;
            margin-top: -2px;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

        // Initialiser Select2 si plus de 100 utilisateurs
        function initSelect2() {
            if (typeof $ !== 'undefined' && $('.select2').length > 0) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Rechercher un utilisateur...',
                    allowClear: true,
                    minimumInputLength: 2,
                    language: {
                        noResults: function () {
                            return "Aucun utilisateur trouvé";
                        },
                        searching: function () {
                            return "Recherche...";
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Générer le code automatiquement
            generateBranchCode();

            // Initialiser Select2 si nécessaire
            initSelect2();

            // Réinitialiser le formulaire quand le modal se ferme
            const modal = document.getElementById('createBranchModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function () {
                    // Réinitialiser les champs
                    document.getElementById('branchName').value = '';
                    document.getElementById('branchCode').value = '';
                    document.querySelector('textarea[name="description"]').value = '';
                    document.querySelector('textarea[name="address"]').value = '';
                    document.querySelector('input[name="phone"]').value = '';
                    document.querySelector('input[name="email"]').value = '';
                    document.querySelector('select[name="manager_id"]').value = '';

                    // Réinitialiser la checkbox
                    document.getElementById('isActive').checked = true;
                });
            }
        });

        function editBranch(id) {
            // Rediriger vers la page d'édition ou ouvrir un modal
            alert('Fonction d\'édition en cours de développement');
        }

        function deleteBranch(id, name) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer la succursale "${name}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/company/settings/branches/${id}`;

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