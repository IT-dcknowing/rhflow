@extends('layouts.app')

@section('title', 'Créer un Utilisateur - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Créer un Utilisateur</h4>
                        <p class="text-muted mb-0">Ajoutez un nouvel utilisateur à votre entreprise</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.settings.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                        <button class="btn btn-info" onclick="previewForm()">
                            <i class="fas fa-eye me-1"></i>Aperçu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de Création -->
        <div class="row">
            <div class="col-xl-10 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Informations de l'Utilisateur</h5>
                        <span class="badge bg-label-primary">Nouveau</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company.settings.users.store') }}" id="createUserForm">
                            @csrf

                            <!-- Informations Personnelles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"> Informations Personnelles</h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom Complet *</label>
                                    <input type="text" class="form-control" name="name" required
                                        oninput="generateProposal()" id="name" placeholder="Ex: Jean Dupont"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username') }}" readonly required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required
                                        placeholder="jean.dupont@exemple.com" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">L'utilisateur recevra ses identifiants à cette adresse</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Mot de Passe *</label>
                                    <input type="password" class="form-control" name="password" required minlength="8"
                                        placeholder="••••••••">
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Confirmer le Mot de Passe *</label>
                                    <input type="password" class="form-control" name="password_confirmation" required
                                        placeholder="••••••••">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" name="phone" placeholder="+225 XX XX XX XX"
                                        value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type d'Utilisateur *</label>
                                    <select class="form-select" name="type" required onchange="updateTypeDescription()">
                                        <option value="">Choisir un type...</option>
                                        <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>
                                            Entreprise - Accès complet à tous les modules
                                        </option>
                                        <option value="hr" {{ old('type') == 'hr' ? 'selected' : '' }}>
                                            RH - Gestion des employés et congés
                                        </option>
                                        <option value="payroll" {{ old('type') == 'payroll' ? 'selected' : '' }}>
                                            Paie - Gestion des salaires et paies
                                        </option>
                                        <option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>
                                            Employé - Accès à son profil personnel
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div id="typeDescription" class="text-muted small mt-1">
                                        Sélectionnez le type d'utilisateur pour définir ses permissions.
                                    </div>
                                </div>
                            </div>

                            <!-- Affectation Organisationnelle -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Affectation Organisationnelle</h6>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Succursale</label>
                                    <select class="form-select" name="branch_id">
                                        <option value="">Aucune branche</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Département</label>
                                    <select class="form-select" name="department_id">
                                        <option value="">Aucun département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Poste</label>
                                    <select class="form-select" name="designation_id">
                                        <option value="">Aucun poste</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('designation_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Paramètres Supplémentaires -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Paramètres Supplémentaires</h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="isActive" checked>
                                        <label class="form-check-label" for="isActive">
                                            <strong>Compte Actif</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">L'utilisateur pourra se connecter immédiatement</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="send_welcome_email" value="1"
                                            id="sendWelcomeEmail">
                                        <label class="form-check-label" for="sendWelcomeEmail">
                                            <strong>Envoyer Email de Bienvenue</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">L'utilisateur recevra ses identifiants par email</small>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('company.settings.users.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Annuler
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-info" onclick="previewUser()">
                                                <i class="fas fa-eye me-1"></i>Aperçu
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Créer l'Utilisateur
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aperçu de l'Utilisateur -->
        <div class="row mt-4" id="previewSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Aperçu de l'Utilisateur</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hidePreview()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-lg me-3" style="width: 60px; height: 60px;">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="fas fa-user fa-28px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" id="previewName">Nom de l'utilisateur</h6>
                                        <small class="text-muted" id="previewEmail">email@exemple.com</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="badge bg-label-primary me-2" id="previewType">Type</span>
                                    <span class="badge bg-label-success" id="previewStatus">Statut</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Téléphone:</small>
                                        <div id="previewPhone">-</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Branche:</small>
                                        <div id="previewBranch">-</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Département:</small>
                                        <div id="previewDepartment">-</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Poste:</small>
                                        <div id="previewDesignation">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> L'utilisateur recevra un email avec ses identifiants de connexion
                            une fois créé.
                        </div>
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

        /* Form improvements */
        .form-label {
            font-weight: 500;
            color: #566a7f;
            margin-bottom: 0.5rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 110, 255, 0.25);
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

        .bg-label-info {
            background-color: rgba(3, 195, 236, 0.1) !important;
            color: #03c3ec !important;
        }

        .bg-label-secondary {
            background-color: rgba(133, 146, 163, 0.1) !important;
            color: #8592a3 !important;
        }

        /* Preview section */
        #previewSection {
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        function generateProposal() {
            var nameField = document.getElementById('name');
            var usernameField = document.getElementById('username');
            var username = nameField.value.replace(/\s+/g, '').toUpperCase(); // Supprime tous les espaces et convertit en majuscules

            if (username.length >= 4) {
                var firstFourLetters = username.substring(0, 4); // Prend les quatre premières lettres
                var randomDigits = Math.floor(10 + Math.random() * 90); // Génère deux chiffres aléatoires
                var proposal = firstFourLetters + randomDigits; // Combine les lettres et les chiffres

                usernameField.value = proposal; // Met à jour la valeur du champ avec la proposition générée
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Mettre à jour l'aperçu en temps réel
            const form = document.getElementById('createUserForm');
            const inputs = form.querySelectorAll('input, select');

            inputs.forEach(input => {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            // Validation du formulaire
            form.addEventListener('submit', function (e) {
                const password = form.querySelector('input[name="password"]').value;
                const confirmPassword = form.querySelector('input[name="password_confirmation"]').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    showNotification('Les mots de passe ne correspondent pas', 'error');
                    return;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    showNotification('Le mot de passe doit contenir au moins 8 caractères', 'error');
                    return;
                }
            });
        });

        function updateTypeDescription() {
            const typeSelect = document.querySelector('select[name="type"]');
            const descriptionDiv = document.getElementById('typeDescription');

            const descriptions = {
                'company': 'Accès complet à tous les modules et paramètres de l\'entreprise',
                'hr': 'Gestion des employés, congés, formations et évaluations',
                'payroll': 'Gestion des salaires, paies, déclarations sociales et fiscales',
                'employee': 'Accès à son profil personnel, demandes de congés et bulletins de paie'
            };

            const selectedType = typeSelect.value;
            descriptionDiv.textContent = descriptions[selectedType] || 'Sélectionnez le type d\'utilisateur pour définir ses permissions.';
        }

        function updatePreview() {
            const name = document.querySelector('input[name="name"]').value || 'Nom de l\'utilisateur';
            const email = document.querySelector('input[name="email"]').value || 'email@exemple.com';
            const phone = document.querySelector('input[name="phone"]').value || '-';
            const type = document.querySelector('select[name="type"]').value;
            const isActive = document.querySelector('input[name="is_active"]').checked;

            // Mettre à jour l'aperçu
            document.getElementById('previewName').textContent = name;
            document.getElementById('previewEmail').textContent = email;
            document.getElementById('previewPhone').textContent = phone;
            document.getElementById('previewStatus').textContent = isActive ? 'Actif' : 'Inactif';

            // Mettre à jour le type
            const typeLabels = {
                'company': 'Entreprise',
                'hr': 'RH',
                'payroll': 'Paie',
                'employee': 'Employé'
            };

            const typeBadge = document.getElementById('previewType');
            if (type && typeLabels[type]) {
                typeBadge.textContent = typeLabels[type];
                typeBadge.className = `badge bg-label-${type === 'company' ? 'primary' : type === 'hr' ? 'success' : type === 'payroll' ? 'info' : 'secondary'}`;
            } else {
                typeBadge.textContent = 'Type';
                typeBadge.className = 'badge bg-label-secondary';
            }

            // Mettre à jour les affectations
            const branchSelect = document.querySelector('select[name="branch_id"]');
            const departmentSelect = document.querySelector('select[name="department_id"]');
            const designationSelect = document.querySelector('select[name="designation_id"]');

            document.getElementById('previewBranch').textContent = branchSelect.options[branchSelect.selectedIndex]?.text || '-';
            document.getElementById('previewDepartment').textContent = departmentSelect.options[departmentSelect.selectedIndex]?.text || '-';
            document.getElementById('previewDesignation').textContent = designationSelect.options[designationSelect.selectedIndex]?.text || '-';
        }

        function previewUser() {
            updatePreview();
            document.getElementById('previewSection').style.display = 'block';
            document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
        }

        function previewForm() {
            previewUser();
        }

        function hidePreview() {
            document.getElementById('previewSection').style.display = 'none';
        }

        function showNotification(message, type = 'info') {
            // Créer une notification toast
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            // Ajouter au conteneur de toasts
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            toastContainer.appendChild(toast);

            // Afficher le toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }
    </script>
@endpush