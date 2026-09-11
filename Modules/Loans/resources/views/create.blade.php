@extends('layouts.app')

@section('title', 'Ajouter un Prêt')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">📅 Ajout d'un Prêt</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Prêts</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a type="button" class="btn btn-primary" href="{{ route('company.loans.index') }}?periode_id={{ $periode->id }}">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Détails du Prêt</h5>
                    <div class="card-body">
                        <form id="loanForm" method="POST" action="{{ route('company.loans.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                            <div class="col-md-6">
                                <label class="form-label" for="employee_id">Employé</label>
                                <select id="employee_id" name="employee_id" class="select2 form-select" data-allow-clear="true" required>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary="{{ $employee->get_net_salary($periode->id) }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="branch_id">Succursale</label>
                                <select id="branch_id" name="branch_id" class="select2 form-select" data-allow-clear="true">
                                    <option value="">Sélectionner une succursale</option>
                                    @foreach($branches as $branche)
                                        <option value="{{ $branche->id }}">{{ $branche->name }}</option>
                                    @endforeach
                                </select>
                                @error('branche_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="loan_option">Type de Prêt</label>
                                <select id="loan_option" name="loan_option" class="select2 form-select" data-allow-clear="true" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($loanOptions as $option)
                                        <option value="{{ $option->id }}" 
                                                data-max-amount="{{ $option->max_amount }}"
                                                data-interest-rate="{{ $option->interest_rate / 100 }}">
                                            {{ $option->name }}
                                            @if($option->interest_rate > 0)
                                                ({{ number_format($option->interest_rate, 2) }}% d'intérêt)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('loan_option')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="title">Titre du Prêt</label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="Titre du prêt" required />
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label" for="type">Type de Déduction</label>
                                <select id="type" name="type" class="form-select" required>
                                    <option value="fixe">Montant Fixe</option>
                                    <option value="pourcentage">Pourcentage du salaire</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12" id="base_salary_container" style="display: none;">
                                <label class="form-label" for="salary">Salaire Net</label>
                                <div class="input-group">
                                    <input type="number" id="salary" name="salary" class="form-control" readonly />
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="amount">Montant Total du prêt</label>
                                <div class="input-group">
                                    <input type="number" id="amount" name="amount" class="form-control" placeholder="0" min="0" required />
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="amount_deduc">Deductibilité Mensuelle</label>
                                <div class="input-group">
                                    <input type="number" id="amount_deduc" name="amount_deduc" class="form-control" placeholder="0" min="0" required />
                                    <span class="input-group-text" id="amount_deduc_currency">FCFA</span>
                                </div>
                                @error('amount_deduc')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="nbre_mois">Nombre de Mois</label>
                                <input type="number" id="nbre_mois" name="nbre_mois" class="form-control" placeholder="0" min="1" required />
                                @error('nbre_mois')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12" id="deductible_container" style="display: none;">
                                <strong>
                                    <label class="form-label" for="pourcentage_deductible">Montant du Pourcentage de Deductibilité : </label>
                                    <span id="pourcentage_deductible"class="text-danger"></span> FCFA
                                </strong>
                                <input type="number" id="pourcentage_montant" name="pourcentage_montant" hidden />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="start_date">Date de Début de prélèvement</label>
                                <input type="text" id="start_date" name="start_date" class="form-control flatpickr-date" placeholder="DD-MM-YYYY" required />
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="end_date">Date de Fin</label>
                                <input type="text" id="end_date" name="end_date" class="form-control flatpickr-date" placeholder="DD-MM-YYYY" required />
                                @error('end_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="reason">Motif du Prêt</label>
                                <textarea id="reason" name="reason" class="form-control" rows="3"></textarea>
                                @error('reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="statut">Statut</label>
                                <select id="statut" name="statut" class="form-select" required>
                                    <option value="pending">En Attente de validation</option>
                                    <option value="running">Octroyé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="is_active">Actif</label>
                                <select id="is_active" name="is_active" class="form-select" required>
                                    <option value="1">Oui</option>
                                    <option value="0">Non</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back();">Retour</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Initialisation de Select2
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Sélectionner un élément',
                dropdownParent: $this.parent()
            });
        });

        // Initialisation de Flatpickr
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            locale: 'fr'
        });

        // Remplissage automatique du titre
        $('#loan_option').on('change', function() {
            var loanOption = $(this).find('option:selected').text();
            $('#title').val(loanOption);
        });

        // Récupération du salaire de base de l'employé
        $('#employee_id').on('change', function() {
            var employeeId = $(this).val();
            if (employeeId) {
                var salary = $('#employee_id option:selected').data('salary');
                $('#salary').val(salary);
                calculateMonthlyPayment();
            }
        });

        // Gestion du changement de type de déduction
        $('#type').on('change', function() {
            if ($(this).val() === 'pourcentage') {
                $('#base_salary_container').show();
                $('#amount_deduc').attr('max', '100');
                $('#amount_deduc').attr('placeholder', '0%');
                $('#amount_deduc_currency').text('%');
                $('#deductible_container').show();
            } else {
                $('#base_salary_container').hide();
                $('#amount_deduc').removeAttr('max');
                $('#amount_deduc').attr('placeholder', '0');
                $('#amount_deduc_currency').text('FCFA');
                $('#deductible_container').hide();
            }
            calculateMonthlyPayment();
        });

        // Fonction de calcul du paiement mensuel
        function calculateMonthlyPayment() {
            var amount = parseFloat($('#amount').val()) || 0;
            var amountDeduc = parseFloat($('#amount_deduc').val()) || 0;
            var type = $('#type').val();
            var baseSalary = parseFloat($('#salary').val()) || 0;

            if (type === 'pourcentage' && baseSalary > 0) {
                // Calcul du montant de la mensualité en fonction du pourcentage
                var monthlyAmount = (baseSalary * amountDeduc) / 100;
                // Calcul du nombre de mois nécessaires
                var months = Math.ceil(amount / monthlyAmount);
                $('#pourcentage_deductible').html(Math.round(monthlyAmount));
                $('#pourcentage_montant').val(Math.round(monthlyAmount));
                $('#nbre_mois').val(months);
            } else if (type === 'fixe' && amount > 0 && amountDeduc > 0) {
                // Calcul standard pour un montant fixe
                var months = Math.ceil(amount / amountDeduc);
                $('#nbre_mois').val(months);
            }
        }

        // Mise à jour du calcul lors de la modification des champs
        $('#amount, #amount_deduc, #type').on('change keyup', function() {
            calculateMonthlyPayment();
        });

        // Calcul automatique de la date de fin
        $('#start_date, #nbre_mois').on('change', function() {
            var startDate = $('#start_date').val();
            var months = parseInt($('#nbre_mois').val()) || 0;
            
            if (startDate && months > 0) {
                var endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + months);
                
                var year = endDate.getFullYear();
                var month = String(endDate.getMonth() + 1).padStart(2, '0');
                var day = String(endDate.getDate()).padStart(2, '0');
                
                $('#end_date').val(`${year}-${month}-${day}`);
            }
        });

        // Validation du formulaire
        const loanForm = document.getElementById('loanForm');
        
        FormValidation.formValidation(loanForm, {
            fields: {
                employee_id: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner un employé'
                        }
                    }
                },
                loan_option: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner un type de prêt'
                        }
                    }
                },
                title: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez entrer un titre'
                        }
                    }
                },
                type: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner un type'
                        }
                    }
                },
                amount: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez entrer un montant'
                        },
                        numeric: {
                            message: 'La valeur doit être numérique'
                        },
                        greaterThan: {
                            min: 0,
                            message: 'La valeur doit être positive'
                        }
                    }
                },
                nbre_mois: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez entrer le nombre de mois'
                        },
                        integer: {
                            message: 'La valeur doit être un entier'
                        },
                        greaterThan: {
                            min: 1,
                            message: 'La valeur doit être au moins 1'
                        }
                    }
                },
                amount_deduc: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez entrer la mensualité'
                        },
                        numeric: {
                            message: 'La valeur doit être numérique'
                        },
                        greaterThan: {
                            min: 0,
                            message: 'La valeur doit être positive'
                        }
                    }
                },
                start_date: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner une date de début'
                        }
                    }
                },
                end_date: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner une date de fin'
                        }
                    }
                },
                statut: {
                    validators: {
                        notEmpty: {
                            message: 'Veuillez sélectionner un statut'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.col-md-6'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        });
    });
</script>
@endpush