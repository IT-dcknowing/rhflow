@extends('layouts.app')

@section('title', 'Modification du contrat - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Modification du contrat</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.contracts.index') }}">Gestion des contrats</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('company.contracts.show', $contract->id) }}">{{ $contract->subject }}</a>
                                </li>
                                <li class="breadcrumb-item active">Modifier</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('company.contracts.show', $contract->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informations du contrat</h5>
                        <span
                            class="badge bg-{{ $contract->status == 'accept' ? 'success' : ($contract->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ $contract->status == 'accept' ? 'Actif' : ($contract->status == 'pending' ? 'Brouillon' : 'Terminé') }}
                        </span>
                    </div>
                    <form id="form_contrat" action="{{ route('company.contracts.update', $contract->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Informations générales -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="type_id" class="form-label">Type de contrat <span
                                                class="text-danger">*</span></label>
                                        <select class="select2 form-select @error('type_id') is-invalid @enderror"
                                            id="type_id" name="type_id" required>
                                            <option value="">Sélectionner un type</option>
                                            @foreach($contractTypes as $type)
                                                @php
                                                    // Un contrat à durée déterminée impose une date de fin.
                                                    $nomType = mb_strtolower($type->name ?? '');
                                                    $typeEstCdd = !str_contains($nomType, 'indétermin')
                                                        && !str_contains($nomType, 'indetermin')
                                                        && !str_contains($nomType, 'cdi')
                                                        && (str_contains($nomType, 'détermin')
                                                            || str_contains($nomType, 'determin')
                                                            || str_contains($nomType, 'cdd'));
                                                @endphp
                                                <option value="{{ $type->id }}" data-cdd="{{ $typeEstCdd ? '1' : '0' }}"
                                                    {{ old('type_id', $contract->type_id) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="subject" class="form-label">Intitulé du contrat <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control bg-light @error('subject') is-invalid @enderror"
                                            id="subject" name="subject" value="{{ old('subject', $contract->subject) }}"
                                            required readonly tabindex="-1">
                                        <small class="text-muted">Rempli automatiquement d'après le type de contrat</small>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="employee_id" class="form-label">Employé <span
                                                class="text-danger">*</span></label>
                                        <select class="select2 form-select @error('employee_id') is-invalid @enderror"
                                            id="employee_id" name="employee_id" required>
                                            <option value="">Sélectionner un employé</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="value" class="form-label">Valeur du contrat</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01"
                                                class="form-control @error('value') is-invalid @enderror" id="value"
                                                name="value" value="{{ old('value', $contract->value) }}">
                                            <span class="input-group-text">{{ $company->currency ?? 'XOF' }}</span>
                                        </div>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dates et durée -->
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="start_date" class="form-label">Date de début <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date"
                                            value="{{ old('start_date', $contract->start_date) }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="end_date" class="form-label">Date de fin
                                            <span class="text-danger d-none" id="end_date_requis">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date"
                                            value="{{ old('end_date', $contract->end_date) }}">
                                        <small class="text-muted" id="end_date_aide">Laisser vide pour un contrat à durée indéterminée</small>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="duration" class="form-label">Durée</label>
                                        <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                            id="duration" name="duration" value="{{ old('duration', $contract->duration) }}"
                                            placeholder="Ex: 12 mois">
                                        @error('duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Statut -->
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status">
                                            <option value="pending" {{ old('status', $contract->status) == 'pending' ? 'selected' : '' }}>Brouillon</option>
                                            <option value="accept" {{ old('status', $contract->status) == 'accept' ? 'selected' : '' }}>Actif</option>
                                            <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Terminé</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description et notes -->
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                            id="description" name="description"
                                            rows="3">{{ old('description', $contract->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="contract_description" class="form-label">Contenu du contrat</label>
                                        <textarea class="form-control @error('contract_description') is-invalid @enderror"
                                            id="contract_description" name="contract_description"
                                            rows="5">{{ old('contract_description', $contract->contract_description) }}</textarea>
                                        @error('contract_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="notes" class="form-label">Notes internes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                            name="notes" rows="2">{{ old('notes', $contract->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Pièces jointes -->
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="attachments" class="form-label">Ajouter des pièces jointes</label>
                                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                            id="attachments" name="attachments[]" multiple>
                                        <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers (PDF, Word,
                                            Excel, Images)</small>
                                        @error('attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('company.contracts.show', $contract->id) }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Sélectionner un élément',
                    dropdownParent: $this.parent()
                });
            });

            // Remplacer l'écouteur d'événement actuel par celui-ci
            $('#type_id').on('select2:select', function (e) {
                var selectedData = $(this).select2('data')[0]; // Récupère les données de l'option sélectionnée
                var selectedValue = selectedData.id; // La valeur complète de l'option

                // Met à jour le champ subject selon la valeur sélectionnée
                if (selectedValue == 3) {
                    $('#subject').val('Contrat à durée indéterminée');
                } else if (selectedValue == 4) {
                    $('#subject').val('Contrat à durée déterminée');
                } else if (selectedValue == 9) {
                    $('#subject').val('Contrat d’expatrié à durée déterminée');
                } else {
                    $('#subject').val(selectedData.text); // Utilise text au lieu de textContent
                }

                majObligationDateFin();
            });

            // Un contrat à durée déterminée ne peut pas être enregistré sans date de fin.
            var champTypeContrat = document.getElementById('type_id');
            var champDateFin = document.getElementById('end_date');
            var marqueurRequis = document.getElementById('end_date_requis');
            var aideDateFin = document.getElementById('end_date_aide');

            function typeSelectionneEstCdd() {
                var option = champTypeContrat.options[champTypeContrat.selectedIndex];
                return !!option && option.getAttribute('data-cdd') === '1';
            }

            function majObligationDateFin() {
                var cdd = typeSelectionneEstCdd();

                champDateFin.required = cdd;
                marqueurRequis.classList.toggle('d-none', !cdd);
                aideDateFin.textContent = cdd
                    ? 'Obligatoire pour un contrat à durée déterminée'
                    : 'Laisser vide pour un contrat à durée indéterminée';

                if (!cdd) {
                    champDateFin.classList.remove('is-invalid');
                }
            }

            champTypeContrat.addEventListener('change', majObligationDateFin);
            majObligationDateFin();

            document.getElementById('form_contrat').addEventListener('submit', function (e) {
                if (typeSelectionneEstCdd() && !champDateFin.value) {
                    e.preventDefault();
                    e.stopPropagation();

                    champDateFin.classList.add('is-invalid');
                    champDateFin.focus();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Date de fin manquante',
                            text: 'Un contrat à durée déterminée doit comporter une date de fin.',
                            confirmButtonColor: '#253e87'
                        });
                    } else {
                        alert('Un contrat à durée déterminée doit comporter une date de fin.');
                    }
                }
            });

            // Initialisation de l'éditeur de texte riche pour le contenu du contrat
            if (typeof Quill !== 'undefined') {
                var quill = new Quill('#contract_description_editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                            ['clean']
                        ]
                    }
                });

                // Synchroniser le contenu de l'éditeur avec le champ caché
                var form = document.querySelector('form');
                form.onsubmit = function () {
                    var contractDescription = document.querySelector('#contract_description');
                    contractDescription.value = quill.root.innerHTML;
                    return true;
                };
            }

            // Calcul automatique de la durée en fonction des dates
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
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
        });
    </script>
@endpush