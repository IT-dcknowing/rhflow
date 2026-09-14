@extends('layouts.app')

@section('title', 'Ajouter une période de paie')

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/sweetalert2/sweetalert2.css') }}">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Nouvelle période de paie</h5>
                        <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}"
                            class="btn btn-label-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.paiesalaries.periodes.store', $exercice->id) }}" method="POST"
                            id="formPeriode">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="type_periode" class="form-label">Type de période <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('type_periode') is-invalid @enderror"
                                        id="type_periode" name="type_periode" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="mensuelle" {{ old('type_periode') == 'mensuelle' ? 'selected' : '' }}>
                                            Mensuelle</option>
                                        <option value="quinzaine" {{ old('type_periode') == 'quinzaine' ? 'selected' : '' }}>
                                            Quinzaine</option>
                                        <option value="hebdomadaire" {{ old('type_periode') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                    </select>
                                    @error('type_periode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="nom" class="form-label">Nom de la période <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom"
                                        name="nom" value="{{ old('nom') }}" readonly>
                                    <small class="text-muted">Généré automatiquement</small>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3" id="container_selection">
                                    <!-- Contenu dynamique : mois ou semaines -->
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label for="date_debut" class="form-label">Date de début <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control flatpickr-date @error('date_debut') is-invalid @enderror"
                                        id="date_debut" name="date_debut" value="{{ old('date_debut') }}"
                                        data-min-date="{{ $exercice->date_debut->format('Y-m-d') }}"
                                        data-max-date="{{ $exercice->date_fin->format('Y-m-d') }}" required>
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="date_fin" class="form-label">Date de fin <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control flatpickr-date @error('date_fin') is-invalid @enderror"
                                        id="date_fin" name="date_fin" value="{{ old('date_fin') }}"
                                        data-min-date="{{ $exercice->date_debut->format('Y-m-d') }}"
                                        data-max-date="{{ $exercice->date_fin->format('Y-m-d') }}" required>
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="date_paiement" class="form-label">Date de paiement </label>
                                    <input type="text"
                                        class="form-control flatpickr-date @error('date_paiement') is-invalid @enderror"
                                        id="date_paiement" name="date_paiement" value="{{ old('date_paiement') }}"
                                        data-min-date="{{ $exercice->date_debut->format('Y-m-d') }}"
                                        data-max-date="{{ $exercice->date_fin->format('Y-m-d') }}">
                                    @error('date_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                        name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('libs/select2/select2.js') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(function () {
            // Données de l'exercice
            const exerciceAnnee = '{{ $exercice->nom }}';
            const exerciceDateDebut = new Date('{{ $exercice->date_debut->format('Y-m-d') }}');
            const exerciceDateFin = new Date('{{ $exercice->date_fin->format('Y-m-d') }}');

            // Fonction pour formater les dates localement sans décalage de fuseau horaire
            function formaterDateLocale(date) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            }

            // Générer les options de mois
            function genererOptionsMois() {
                const mois = [
                    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                ];

                let options = '<option value="">Sélectionner un mois...</option>';
                for (let i = 0; i < mois.length; i++) {
                    options += `<option value="${i + 1}">${mois[i]}</option>`;
                }
                return options;
            }

            // Générer les options de quinzaines
            function genererOptionsQuinzaines() {
                const mois = [
                    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                ];

                let options = '<option value="">Sélectionner une quinzaine...</option>';
                for (let i = 0; i < mois.length; i++) {
                    options += `<option value="${i + 1}-1">1ère quinzaine ${mois[i]} ${exerciceAnnee}</option>`;
                    options += `<option value="${i + 1}-2">2ème quinzaine ${mois[i]} ${exerciceAnnee}</option>`;
                }
                return options;
            }

            // Générer les options de semaines
            function genererOptionsSemaines() {
                let options = '<option value="">Sélectionner une semaine...</option>';
                let semaineNum = 1;
                let currentDate = new Date(exerciceDateDebut);

                while (currentDate <= exerciceDateFin) {
                    const debutSemaine = new Date(currentDate);
                    const finSemaine = new Date(currentDate);
                    finSemaine.setDate(finSemaine.getDate() + 6);

                    if (finSemaine > exerciceDateFin) {
                        finSemaine.setTime(exerciceDateFin.getTime());
                    }

                    const debutStr = debutSemaine.toLocaleDateString('fr-FR');
                    const finStr = finSemaine.toLocaleDateString('fr-FR');

                    options += `<option value="${semaineNum}">Semaine ${semaineNum} (${debutStr} - ${finStr})</option>`;

                    semaineNum++;
                    currentDate.setDate(currentDate.getDate() + 7);
                }

                return options;
            }

            // Mettre à jour le conteneur de sélection
            function mettreAJourSelection(typePeriode) {
                const container = $('#container_selection');

                if (typePeriode === 'mensuelle') {
                    container.html(`
                                        <label for="mois" class="form-label">Mois <span class="text-danger">*</span></label>
                                        <select class="form-select" id="mois" name="mois" required>
                                            ${genererOptionsMois()}
                                        </select>
                                    `);
                } else if (typePeriode === 'quinzaine') {
                    container.html(`
                                        <label for="quinzaine" class="form-label">Quinzaine <span class="text-danger">*</span></label>
                                        <select class="form-select" id="quinzaine" name="quinzaine" required>
                                            ${genererOptionsQuinzaines()}
                                        </select>
                                    `);
                } else if (typePeriode === 'hebdomadaire') {
                    container.html(`
                                        <label for="semaine" class="form-label">Semaine <span class="text-danger">*</span></label>
                                        <select class="form-select" id="semaine" name="semaine" required>
                                            ${genererOptionsSemaines()}
                                        </select>
                                    `);
                } else {
                    container.html('');
                }
            }

            // Calculer les dates selon la sélection
            function calculerDates() {
                const typePeriode = $('#type_periode').val();
                let nomPeriode = '';
                let dateDebut = null;
                let dateFin = null;
                const anneeNumerique = exerciceDateFin.getFullYear();

                if (typePeriode === 'mensuelle') {
                    const mois = $('#mois').val();
                    if (mois) {
                        const nomsMois = [
                            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                        ];
                        nomPeriode = `${nomsMois[mois - 1]} ${anneeNumerique}`;

                        dateDebut = new Date(anneeNumerique, mois - 1, 1);
                        dateFin = new Date(anneeNumerique, mois, 0); // Dernier jour du mois
                    }
                } else if (typePeriode === 'hebdomadaire') {
                    const semaine = $('#semaine').val();
                    if (semaine) {
                        nomPeriode = `Semaine ${semaine} ${anneeNumerique}`;

                        // Calculer les dates de la semaine
                        let semaineNum = 1;
                        let currentDate = new Date(exerciceDateDebut);

                        while (currentDate <= exerciceDateFin && semaineNum < semaine) {
                            currentDate.setDate(currentDate.getDate() + 7);
                            semaineNum++;
                        }

                        if (semaineNum == semaine) {
                            dateDebut = new Date(currentDate);
                            dateFin = new Date(currentDate);
                            dateFin.setDate(dateFin.getDate() + 6);
                        }
                    }
                } else if (typePeriode === 'quinzaine') {
                    const quinzaine = $('#quinzaine').val();
                    if (quinzaine) {
                        const [mois, periode] = quinzaine.split('-');
                        const nomsMois = [
                            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                        ];

                        nomPeriode = `${periode === '1' ? '1ère' : '2ème'} quinzaine ${nomsMois[mois - 1]} ${anneeNumerique}`;

                        if (periode === '1') {
                            // 1ère quinzaine : 1er au 15 du mois
                            dateDebut = new Date(anneeNumerique, mois - 1, 1);
                            dateFin = new Date(anneeNumerique, mois - 1, 15);
                        } else {
                            // 2ème quinzaine : 16 au dernier jour du mois
                            dateDebut = new Date(anneeNumerique, mois - 1, 16);
                            dateFin = new Date(anneeNumerique, mois, 0); // Dernier jour du mois
                        }
                    }
                } else if (typePeriode === 'autre') {
                    nomPeriode = 'Autre période';
                    // L'utilisateur peut définir manuellement
                }

                // Mettre à jour les champs
                if (nomPeriode) {
                    $('#nom').val(nomPeriode);
                }

                if (dateDebut && dateFin) {
                    // Limiter les dates aux frontières de l'exercice pour éviter les erreurs hors limite
                    if (dateDebut < exerciceDateDebut) {
                        dateDebut = new Date(exerciceDateDebut);
                    }
                    if (dateFin > exerciceDateFin) {
                        dateFin = new Date(exerciceDateFin);
                    }

                    const formattedDebut = formaterDateLocale(dateDebut);
                    const formattedFin = formaterDateLocale(dateFin);
                    $('#date_debut').val(formattedDebut);
                    $('#date_fin').val(formattedFin);
                    $('#date_paiement').val(formattedFin);
                    // Mettre à jour les limites des datepickers
                    if (dateDebutPicker[0] && dateDebutPicker[0]._flatpickr) {
                        dateDebutPicker[0]._flatpickr.setDate(dateDebut);
                    }
                    if (dateFinPicker[0] && dateFinPicker[0]._flatpickr) {
                        dateFinPicker[0]._flatpickr.setDate(dateFin);
                        dateFinPicker[0]._flatpickr.set('minDate', dateDebut);
                    }
                    if (datePaiement[0] && datePaiement[0]._flatpickr) {
                        datePaiement[0]._flatpickr.setDate(dateFin);
                        datePaiement[0]._flatpickr.set('minDate', dateFin);
                    }
                }
            }

                // Écouter les changements
                $('#type_periode').on('change', function () {
                    const typePeriode = $(this).val();
                    mettreAJourSelection(typePeriode);

                    // Réinitialiser les dates si le type change
                    $('#nom').val('');
                    $('#date_debut').val('');
                    $('#date_fin').val('');
                });

                // Écouter les changements de sélection
                $(document).on('change', '#mois, #semaine, #quinzaine', function () {
                    calculerDates();
                });

                // Initialisation de flatpickr pour les dates
                const dateDebutPicker = $('input#date_debut');
                const dateFinPicker = $('input#date_fin');
                const datePaiement = $('input#date_paiement');

                // Locale française pour Flatpickr définie inline
                const flatpickrFrenchLocale = {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'],
                        longhand: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
                    },
                    months: {
                        shorthand: ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'],
                        longhand: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre']
                    },
                    ordinal: function (nth) {
                        if (nth === 1) return 'er';
                        return '';
                    },
                    rangeSeparator: ' au ',
                    weekAbbreviation: 'Sem',
                    scrollTitle: 'Défiler pour augmenter',
                    toggleTitle: 'Cliquer pour basculer',
                    time24hr: true
                };

                dateDebutPicker.flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    locale: flatpickrFrenchLocale,
                    minDate: dateDebutPicker.data('min-date'),
                    maxDate: dateDebutPicker.data('max-date'),
                    onChange: function (selectedDates, dateStr) {
                        if (dateFinPicker[0] && dateFinPicker[0]._flatpickr) {
                            dateFinPicker[0]._flatpickr.set('minDate', dateStr);
                        }
                        if (datePaiement[0] && datePaiement[0]._flatpickr) {
                            datePaiement[0]._flatpickr.set('minDate', dateStr);
                        }
                    }
                });

                dateFinPicker.flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    locale: flatpickrFrenchLocale,
                    minDate: dateFinPicker.data('min-date'),
                    maxDate: dateFinPicker.data('max-date')
                });

                datePaiement.flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    locale: flatpickrFrenchLocale,
                    minDate: datePaiement.data('min-date'),
                    maxDate: datePaiement.data('max-date')
                });

                // Validation du formulaire
                $('#formPeriode').on('submit', function (e) {
                    const debut = new Date(dateDebutPicker.val());
                    const fin = new Date(dateFinPicker.val());
                    const paiement = new Date(datePaiement.val());

                    if (debut >= fin) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'La date de fin doit être postérieure à la date de début',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    if (paiement < fin) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'La date de paiement doit être postérieure ou égale à la date de fin',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Un seul envoi : un double clic créait la même période deux fois
                    if (!e.isDefaultPrevented()) {
                        $(this).find('button[type="submit"]').prop('disabled', true);
                    }
                });
            });
        </script>
@endpush