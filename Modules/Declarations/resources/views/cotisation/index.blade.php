@extends('layouts.app')

@section('title', __('État des cotisations'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des Bulletins de Paie</h4>
                        <p class="text-muted mb-0">Consultez et modifiez les bulletins de paie générés</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-2" style="margin-bottom: 10px;">
                                <div class="d-flex align-items-center justify-content-start">
                                    <h5>{{ __('État des cotisations') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                        <div class="btn-box">
                                            <select id="exercice" class="form-select select2" name="exercice" tabindex="-1"
                                                aria-hidden="true">
                                                @foreach ($exercices as $exercice)
                                                    <option value="{{ $exercice->id }}" data-debut="{{ $exercice->date_debut }}"
                                                        data-fin="{{ $exercice->date_fin }}" {{ isset($activeExercice) && $activeExercice->id == $exercice->id ? 'selected' : '' }}>
                                                        {{ $exercice->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                        <div class="btn-box">
                                            <select name="periodes" id="periodes" class="form-select">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                        <div class="btn-box">
                                            <select id="employeeName" class="form-select" name="employeeName" tabindex="-1"
                                                aria-hidden="true">
                                                <option value="--">Tous les employés</option>
                                                @foreach($employee as $emp)
                                                    <option value="{{ $emp->user_id }}">{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="filter_month3" class="filter_month2">
                                    <input type="hidden" name="filter_year3" class="filter_year2">
                                    <button id="EtatPdfCotisation" class="btn btn-danger me-2"><i
                                            class="fas fa-file-pdf me-2"></i> {{ __('Pdf') }}</button>
                                    <button id="EtatExcelCotisation" class="btn btn-success"><i
                                            class="fas fa-file-pdf me-2"></i> {{ __('Excel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-cotisations">
                            <table class="table table-bordered table-sm" width="100%">
                                <thead>
                                    <tr>
                                        <td colspan="12" class="border border-dark text-center"
                                            style="background-color: #FEC58C;">
                                            <strong>TABLEAU RÉCAPITULATIF DES COTISATIONS SOCIALES ET FISCALES</strong>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="12" class="border border-dark text-center">
                                            {{ __('Veuillez sélectionner un exercice et une période') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Charger les périodes quand on change d'exercice
        function callperiodepaie() {
            var exerciceId = $("#exercice").val();
            if (exerciceId) {
                $.ajax({
                    url: '{{ route("company.declarations.get_periodes") }}',
                    type: 'GET',
                    data: { exercice: exerciceId },
                    success: function (data) {
                        var periodesSelect = $("#periodes");
                        periodesSelect.empty();
                        periodesSelect.append('<option value="--">Toutes les périodes</option>');

                        $.each(data, function (key, periode) {
                            var dateDebut = new Date(periode.date_debut);
                            var dateFin = new Date(periode.date_fin);
                            var libelle = 'Période du ' + dateDebut.toLocaleDateString('fr-FR') + ' au ' + dateFin.toLocaleDateString('fr-FR');

                            periodesSelect.append('<option value="' + periode.id + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                        });

                        // Période demandée dans l'adresse (lien depuis « Paie du mois »)
                        var periodeDemandee = new URLSearchParams(window.location.search).get('periode_id');
                        if (periodeDemandee && periodesSelect.find('option[value="' + periodeDemandee + '"]').length) {
                            periodesSelect.val(periodeDemandee);
                        }

                        // Déclencher le chargement des données si une période est sélectionnée
                        if (periodesSelect.val()) {
                            callbacketatcoti();
                        }
                    },
                    error: function () {
                        console.error('Erreur lors du chargement des périodes');
                    }
                });
            }
        }

        function callbacketatcoti() {
            var excercieId = $("#exercice").val();
            var periodeId = $("#periodes").val();
            var employeeId = $("#employeeName").val();

            $.ajax({
                url: '{{ route('company.declarations.get_cotisations') }}',
                type: 'GET',
                data: {
                    "exercice_id": excercieId,
                    "periode_id": periodeId,
                    "employee_id": employeeId,
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    if (data.length > 0) {
                        generateCotisationTable(data);
                    } else {
                        showEmptyTable();
                    }
                },
                error: function (data) {
                    console.error("Erreur lors de la récupération des données de la paylist.");
                }
            });
        }

        function generateCotisationTable(data) {
            // Calculer les totaux
            var totals = calculateTotals(data);

            // Variables pour l'en-tête
            var dateDuJour = new Date().toLocaleDateString('fr-FR');
            var heureActuelle = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            var periode = $("#periodes option:selected").text() || $("#exercice option:selected").text();
            var company_name = "{{ $company->name ?? 'Entreprise' }}";
            var pageIndex = 0;
            var totalPages = 1;

            var tableHTML = `
                <table class="table table-bordered table-sm" width="100%">
                    <thead>
                        <tr>
                            <td colspan="12" class="border border-dark">
                                <div class="row">
                                    <div class="col-md-3"><p>Date du jour : ${dateDuJour}</p><p>Heure : ${heureActuelle}</p></div>
                                    <div class="col-md-6" style="text-align:center;"><h4>Livre de paie A N N U E L</h4><p>${periode}</p></div>
                                    <div class="col-md-3" style="text-align:right;"><p>Page : ${pageIndex + 1}/${totalPages}</p><h4><span style="background-color:#1e3a8a; color:white; padding:5px; border-radius:5px; font-weight:bold;">© rh-flow</span></h4></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="12"><span>Société : <b>${company_name}</b></span></td>
                        </tr>
                        <tr bgcolor="#FEC58C">
                            <td align="center" width="5%" class="border border-dark align-middle"><strong>Code</strong></td>
                            <td align="center" width="25%" class="border border-dark align-middle"><strong>Rubriques des cotisations</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Taux salarial</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Taux patronal</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Taux global</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Assiette de cotisation</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Base</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Montant salarial</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Montant patronal</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Montant global</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Eff.<br>H.</strong></td>
                            <td align="center" class="border border-dark align-middle"><strong>Eff.<br>F.</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        ${generateCotisationRows(totals)}
                    </tbody>
                </table>
            `;

            $('#table-cotisations').html(tableHTML);
        }

        function calculateTotals(data) {
            var totals = {
                brut_impo: 0,
                brut_cnps: 0,
                imp_brut: 0,
                ricf: 0,
                imp_net: 0,
                cnps_sal: 0,
                cnps_emp: 0,
                pf_emp: 0,
                acc_work: 0,
                taxe_fpc: 0,
                taxe_verser: 0,
                taxe_app: 0,
                cmu_sal: 0,
                cmu_emp: 0,
                ce_emp: 0,
                ce_exp_emp: 0,
                sexemale: 0,
                sexefemale: 0,
                cpte: 0,
                brut_impo_expat: 0,
                simg: 75000
            };

            $.each(data, function (index, element) {
                // Calculer l'exonération pour cet employé
                var resltexoemp = 0;
                if (element.allowance && Array.isArray(element.allowance)) {
                    var totalbase = 0;
                    var toto = 0;
                    var toto2 = 0;

                    $.each(element.allowance, function (i, allowance) {
                        if (allowance.allowance_option == 26 || allowance.allowance_option == 11) {
                            totalbase += parseFloat(allowance.amount) || 0;
                        }
                        if (allowance.trait_fisc && allowance.trait_fisc.startsWith('exo 10%')) {
                            toto += parseFloat(allowance.amount) || 0;
                        }
                        if (allowance.trait_fisc && allowance.trait_fisc.startsWith('exo 100%')) {
                            if (allowance.allowance_option == 11 && parseFloat(allowance.amount) > 30000) {
                                toto2 += 30000;
                            } else {
                                toto2 += parseFloat(allowance.amount) || 0;
                            }
                        }
                    });

                    var exo = ((parseFloat(element.salary_brut) - totalbase) * 10) / 100;
                    resltexoemp = toto > exo ? (exo + toto2) : (toto + toto2);
                }

                // Calculer le brut imposable avec exonération
                var brut_imposable_emp = parseFloat(element.salary_brut) - resltexoemp;

                // Cumuler les totaux
                totals.brut_impo += brut_imposable_emp;
                totals.brut_cnps += parseFloat(element.net_sociale) || 0;

                // Cumule des retenues
                if (element.retenue && Array.isArray(element.retenue)) {
                    $.each(element.retenue, function (i, retenue) {
                        if (retenue.code == 401) {
                            totals.imp_brut += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 402) {
                            totals.ricf += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 403) {
                            totals.imp_net += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 409) {
                            totals.ce_emp += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 410) {
                            totals.ce_exp_emp += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 412) {
                            totals.taxe_fpc += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 413) {
                            totals.taxe_verser += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 411) {
                            totals.taxe_app += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 301) {
                            totals.cnps_sal += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 307) {
                            totals.cnps_emp += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 305) {
                            totals.acc_work += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 306) {
                            totals.pf_emp += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 302) {
                            totals.cmu_sal += parseFloat(retenue.amount) || 0;
                        } else if (retenue.code == 307) {
                            totals.cmu_emp += parseFloat(retenue.amount) || 0;
                        }
                    });
                }

                totals.cpte++;

                if (element.sexe == 'Male') {
                    totals.sexemale++;
                }
                if (element.sexe == 'Female') {
                    totals.sexefemale++;
                }
                if (element.charge_expat == 'expat') {
                    if (element.sexe == 'Male') {
                        totals.cpte_expat_male = (totals.cpte_expat_male || 0) + 1;
                    }
                    if (element.sexe == 'Female') {
                        totals.cpte_expat_female = (totals.cpte_expat_female || 0) + 1;
                    }
                    totals.brut_impo_expat += parseFloat(element.net_imposable) || 0;
                }
            });

            return totals;
        }

        function generateCotisationRows(totals) {
            var rows = '';

            // Impôts brut avant RICF
            rows += `<tr>
                <td align="right" class="border border-dark">401</td>
                <td class="border border-dark">Impôts brut avant RICF</td>
                <td align="right" class="border border-dark"></td>
                <td align="center" rowspan="2" class="border border-dark" style="vertical-align: middle;">Calculé</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.imp_brut.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.imp_brut.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Réduction pour Charges de Famille
            rows += `<tr>
                <td align="right" class="border border-dark">402</td>
                <td class="border border-dark">Réduction pour Charges de Famille</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.ricf.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.ricf.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Total des ITS - Part salariale
            rows += `<tr>
                <td bgcolor="#FEC58C" align="center" class="border border-dark" colspan="2"><strong>Total des ITS - Part salariale </strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.imp_net.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>0</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.imp_net.toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Contribution Employeur
            rows += `<tr>
                <td align="right" class="border border-dark">409</td>
                <td class="border border-dark">Contribution Employeur</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">1,200</td>
                <td align="right" class="border border-dark">1,200</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.ce_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.ce_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Contribution Employeur (Expatrié)
            rows += `<tr>
                <td align="right" class="border border-dark">410</td>
                <td class="border border-dark">Contribution Employeur (Expatrié)</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">9,200</td>
                <td align="right" class="border border-dark">9,200</td>
                <td align="right" class="border border-dark">${totals.brut_impo_expat.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo_expat.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.ce_exp_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.ce_exp_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.cpte_expat_male || 0}</td>
                <td align="right" class="border border-dark">${totals.cpte_expat_female || 0}</td>
            </tr>`;

            // Total des ITS - Part patronale
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total des ITS - Part patronale</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>0</strong></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${(totals.ce_exp_emp + totals.ce_emp).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${(totals.ce_exp_emp + totals.ce_emp).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Taxe.F.P.C
            rows += `<tr>
                <td align="right" class="border border-dark">412</td>
                <td class="border border-dark">Taxe.F.P.C</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">0,600</td>
                <td align="right" class="border border-dark">0,600</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc / 2).toLocaleString()}</td>
                <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc / 2).toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Taxe.F.P.C à verser
            rows += `<tr>
                <td align="right" class="border border-dark">413</td>
                <td class="border border-dark">Taxe.F.P.C à verser</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">0,600</td>
                <td align="right" class="border border-dark">0,600</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc / 2).toLocaleString()}</td>
                <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc / 2).toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Total FDFP - Taxe à la FPC
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total FDFP - Taxe à la FPC </strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>0</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${Math.round(totals.taxe_fpc / 2).toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${Math.round(totals.taxe_fpc / 2).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Taxe d'Apprentissage
            rows += `<tr>
                <td align="right" class="border border-dark">411</td>
                <td class="border border-dark">Taxe d'Apprentissage</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">0,400</td>
                <td align="right" class="border border-dark">0,400</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_impo.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.taxe_app.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.taxe_app.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Total FDFP - Taxe d'Apprentissage
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total FDFP - Taxe d'Apprentissage</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>0</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.taxe_app.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.taxe_app.toLocaleString()}</strong></td>
                <td align="right" colspan="2" bgcolor="#FEC58C" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Total FDFP
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total FDFP</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>0</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.taxe_app + totals.taxe_fpc / 2).toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.taxe_app + totals.taxe_fpc / 2).toLocaleString()}</strong></td>
                <td align="right" colspan="2" bgcolor="#FEC58C" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Ligne vide
            rows += `<tr>
                <td align="right" colspan="12" class="border border-dark"><br></td>
            </tr>`;

            // Total IMPOT
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total IMPOT</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.imp_net.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.ce_exp_emp + totals.ce_emp + totals.taxe_fpc / 2 + totals.taxe_app).toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.imp_net + totals.ce_exp_emp + totals.ce_emp + totals.taxe_fpc / 2 + totals.taxe_app).toLocaleString()}</strong></td>
                <td align="right" colspan="2" bgcolor="#FEC58C" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Retenue CNPS
            rows += `<tr>
                <td align="right" class="border border-dark">301</td>
                <td class="border border-dark">Retenue CNPS</td>
                <td align="right" class="border border-dark">6,300</td>
                <td align="right" class="border border-dark">7,700</td>
                <td align="right" class="border border-dark">14,000</td>
                <td align="right" class="border border-dark">${totals.brut_cnps.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.brut_cnps.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.cnps_sal.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.cnps_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${(totals.cnps_sal + totals.cnps_emp).toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Accident de travail
            rows += `<tr>
                <td align="right" class="border border-dark">305</td>
                <td class="border border-dark">Accident de travail</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">5,000</td>
                <td align="right" class="border border-dark">5,000</td>
                <td align="right" class="border border-dark">${totals.simg.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.simg.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.acc_work.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.acc_work.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Prestation Familiale
            rows += `<tr>
                <td align="right" class="border border-dark">306</td>
                <td class="border border-dark">Prestation Familiale</td>
                <td align="right" class="border border-dark">0,000</td>
                <td align="right" class="border border-dark">5,750</td>
                <td align="right" class="border border-dark">5,750</td>
                <td align="right" class="border border-dark">${totals.simg.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.simg.toLocaleString()}</td>
                <td align="right" class="border border-dark"></td>
                <td align="right" class="border border-dark">${totals.pf_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.pf_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Total CNPS
            rows += `<tr>
                <td bgcolor="#FEC58C" align="center" class="border border-dark" colspan="2"><strong>Total CNPS</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.cnps_sal.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.cnps_emp + totals.pf_emp + totals.acc_work).toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.cnps_sal + totals.cnps_emp + totals.pf_emp + totals.acc_work).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"> </td>
            </tr>`;

            // Couverture Maladie Universelle
            rows += `<tr>
                <td align="right" class="border border-dark">302</td>
                <td class="border border-dark">Couverture Maladie Universelle</td>
                <td align="right" class="border border-dark">50,00</td>
                <td align="right" class="border border-dark">50,00</td>
                <td align="right" class="border border-dark">100,00</td>
                <td align="right" class="border border-dark">${totals.brut_cnps.toLocaleString()}</td>
                <td align="right" class="border border-dark">1 000</td>
                <td align="right" class="border border-dark">${totals.cmu_sal.toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.cmu_emp.toLocaleString()}</td>
                <td align="right" class="border border-dark">${(totals.cmu_emp + totals.cmu_sal).toLocaleString()}</td>
                <td align="right" class="border border-dark">${totals.sexemale}</td>
                <td align="right" class="border border-dark">${totals.sexefemale}</td>
            </tr>`;

            // Total CMU
            rows += `<tr>
                <td bgcolor="#FEC58C" align="center" class="border border-dark" colspan="2"><strong>Total CMU</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.cmu_sal.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.cmu_emp.toLocaleString()}</strong></td>
                <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.cmu_emp + totals.cmu_sal).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"><strong></strong></td>
            </tr>`;

            // Ligne vide
            rows += `<tr>
                <td align="right" colspan="12" class="border border-dark"><br></td>
            </tr>`;

            // Total Général
            rows += `<tr>
                <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total Général</strong></td>
                <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${Math.round(totals.imp_net + totals.cnps_sal + totals.cmu_sal).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${Math.round(totals.cnps_emp + totals.pf_emp + totals.acc_work + totals.taxe_fpc / 2 + totals.taxe_app + totals.cmu_emp + totals.ce_emp + totals.ce_exp_emp).toLocaleString()}</strong></td>
                <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${Math.round(totals.imp_net + totals.cnps_emp + totals.pf_emp + totals.acc_work + totals.taxe_fpc / 2 + totals.taxe_app + totals.cmu_emp + totals.ce_emp + totals.ce_exp_emp).toLocaleString()}</strong></td>
                <td align="right" colspan="2" bgcolor="#FEC58C" class="border border-dark"><strong></strong>${(totals.sexemale + totals.sexefemale)}</td>
            </tr>`;

            return rows;
        }

        function showEmptyTable() {
            var tableHTML = `
                <table class="table table-bordered table-sm" width="100%">
                    <thead>
                        <tr>
                            <td colspan="12" class="border border-dark text-center" style="background-color: #FEC58C;">
                                <strong>TABLEAU RÉCAPITULATIF DES COTISATIONS SOCIALES ET FISCALES</strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="12" class="border border-dark text-center">
                                {{ __('Aucune données') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;

            $('#table-cotisations').html(tableHTML);
        }

        $(document).ready(function () {
            // Exercice demandé dans l'adresse (lien depuis « Paie du mois »)
            var exerciceDemande = new URLSearchParams(window.location.search).get('exercice_id');
            if (exerciceDemande && $('#exercice option[value="' + exerciceDemande + '"]').length) {
                $('#exercice').val(exerciceDemande).trigger('change.select2');
            }

            callperiodepaie();

            // Si vous avez un champ d'année qui peut changer
            $("#exercice").on('change', function () {
                callperiodepaie();
            });

            callbacketatcoti();

            $(document).on("change", "#exercice, #periodes, #employeeName", function () {
                callbacketatcoti();
            });

            // Export PDF
            $('#EtatPdfCotisation').on('click', function () {
                var currentPage = document.getElementById('table-cotisations');
                var company_name = "{{ $company->name ?? 'Entreprise' }}";
                var exerciceName = $("#exercice option:selected").text().replace(/\//g, '').replace(/\s+/g, '').trim();

                // Cacher le bouton d'export pour l'impression
                $('#EtatPdfCotisation').hide();

                // Vérifier que jsPDF est disponible
                if (typeof jspdf === 'undefined') {
                    console.error("jsPDF library not loaded");
                    this.innerHTML = '<i class="fas fa-file-pdf me-2"></i> Exporter en pdf';
                    this.disabled = false;
                    return;
                } else {
                    generatePDF();
                }

                function generatePDF() {
                    // Créer un nouveau document PDF en mode paysage
                    var doc = new jspdf.jsPDF('l', 'mm', 'a4');

                    // En-tête du tableau
                    var dateDuJour = new Date().toLocaleDateString('fr-FR');
                    var heureActuelle = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    var holiday_name = $("#periodes option:selected").text().trim();
                    var periode = holiday_name !== "Toutes les périodes" ? holiday_name : $("#exercice option:selected").text().trim();
                    var company_name = "{{ $company->name ?? 'Entreprise' }}";

                    // Extraire le tableau
                    var tableElement = currentPage.querySelector('table');
                    var allRows = Array.from(tableElement.querySelectorAll('tr'));

                    // Header au niveau de la 3ème ligne (Code, Rubriques...)
                    var headerRowElement = allRows[2];
                    var tableHeaders = [];
                    if (headerRowElement) {
                        var headerCells = headerRowElement.querySelectorAll('td');
                        headerCells.forEach(function (cell) {
                            tableHeaders.push(cell.innerText.trim());
                        });
                    }

                    var tableData = [];
                    var bodyRows = allRows.slice(3);

                    bodyRows.forEach(function (row) {
                        var rowData = [];
                        var cells = Array.from(row.querySelectorAll('td'));
                        if (cells.length === 0) return;

                        var firstCellText = cells[0].textContent.trim();
                        var isTotalRow = firstCellText.includes('Total') || firstCellText.includes('IMPOT') || firstCellText.includes('Général');
                        var isEmptyLine = cells.length === 1 && firstCellText === "";

                        if (isEmptyLine) {
                            tableData.push([{ content: '', colSpan: 12, styles: { minCellHeight: 2 } }]);
                            return;
                        }

                        cells.forEach(function (cell, cellIndex) {
                            var cellContent = cell.innerText.trim();
                            var colSpan = parseInt(cell.getAttribute('colspan')) || 1;
                            var rowSpan = parseInt(cell.getAttribute('rowspan')) || 1;

                            // Remplacer les espaces par des espaces insécables pour les nombres afin d'éviter le wrap vertical
                            if (cellContent.match(/^-?\d+([\s\d]*)(,\d+)?$/)) {
                                cellContent = cellContent.replace(/\s/g, '\u00A0');
                            }

                            var cellStyles = {
                                valign: 'middle',
                                fontStyle: isTotalRow ? 'bold' : 'normal',
                                fontSize: isTotalRow ? 6.5 : 6,
                                fillColor: isTotalRow ? [254, 197, 140] : null
                            };

                            // Alignement intelligent
                            if (isTotalRow) {
                                if (cellIndex === 0) {
                                    cellStyles.halign = 'left';
                                } else if (cellContent === "" || cellContent.match(/^-?\d+([\u00A0\d]*)(,\d+)?$/)) {
                                    cellStyles.halign = 'right';
                                } else {
                                    cellStyles.halign = 'center';
                                }
                            } else {
                                if (cellIndex === 1) {
                                    cellStyles.halign = 'left';
                                } else if (cellContent.toLowerCase() === 'calculé') {
                                    cellStyles.halign = 'center';
                                } else if (cellIndex >= 2 || cellIndex === 0) {
                                    cellStyles.halign = 'right';
                                } else {
                                    cellStyles.halign = 'center';
                                }
                            }

                            rowData.push({
                                content: cellContent,
                                styles: cellStyles,
                                colSpan: colSpan,
                                rowSpan: rowSpan
                            });
                        });

                        tableData.push(rowData);
                    });

                    doc.autoTable({
                        head: [tableHeaders],
                        body: tableData,
                        startY: 25,
                        margin: { top: 25, right: 10, bottom: 15, left: 10 },
                        theme: 'grid',
                        styles: {
                            fontSize: 6,
                            cellPadding: 1,
                            overflow: 'linebreak',
                            font: 'helvetica',
                            minCellHeight: 4
                        },
                        headStyles: {
                            fillColor: [254, 197, 140],
                            textColor: [0, 0, 0],
                            fontStyle: 'bold',
                            halign: 'center',
                            fontSize: 6.5
                        },
                        columnStyles: {
                            0: { cellWidth: 12 }, // Code
                            1: { cellWidth: 65 }, // Rubriques (assez large pour éviter les coupures)
                            2: { cellWidth: 15 }, // Taux
                            3: { cellWidth: 15 },
                            4: { cellWidth: 15 },
                            10: { cellWidth: 12 }, // Eff
                            11: { cellWidth: 12 }
                        },
                        didDrawPage: function (data) {
                            doc.setFontSize(8);
                            doc.setTextColor(40);
                            doc.text('Date du jour : ' + dateDuJour, 10, 10);
                            doc.text('Heure : ' + heureActuelle, 10, 15);

                            doc.setFontSize(14);
                            doc.setFont(undefined, 'bold');
                            doc.text('Livre de paie A N N U E L', 148.5, 12, { align: 'center' });
                            doc.setFontSize(10);
                            doc.text(periode, 148.5, 18, { align: 'center' });

                            doc.setFontSize(8);
                            doc.setFont(undefined, 'normal');
                            doc.text('Page : ' + doc.internal.getNumberOfPages(), 287, 10, { align: 'right' });

                            doc.setFillColor(30, 58, 138);
                            doc.roundedRect(260, 13, 27, 8, 1, 1, 'F');
                            doc.setTextColor(255, 255, 255);
                            doc.setFont(undefined, 'bold');
                            doc.text('© rh-flow', 273.5, 18.5, { align: 'center' });

                            doc.setTextColor(0, 0, 0);
                            doc.setFont(undefined, 'normal');
                            doc.setFontSize(9);
                            doc.text('Société : ' + company_name, 10, 22);
                        }
                    });

                    doc.save('Etat_des_cotisations_' + company_name + '_' + exerciceName + '.pdf');
                    $('#EtatPdfCotisation').show();
                }
            });

            // Export Excel
            $('#EtatExcelCotisation').on('click', function () {
                var currentPage = document.getElementById('table-cotisations');
                var company_name = "{{ $company->name ?? 'Entreprise' }}";
                var exerciceName = $("#exercice option:selected").text().replace(/\//g, '').replace(/\s+/g, '').trim();

                // Cacher le bouton d'export pour l'impression
                $('#EtatExcelCotisation').hide();

                // Vérifier que XLSX est disponible
                if (typeof XLSX === 'undefined') {
                    console.error("XLSX library not loaded");
                    this.innerHTML = '<i class="fas fa-file-excel me-2"></i> Exporter en excel';
                    this.disabled = false;
                    return;
                } else {
                    generateExcel();
                }

                function generateExcel() {
                    // Créer un nouveau classeur Excel
                    var wb = XLSX.utils.book_new();

                    // Convertir le tableau HTML en tableau de données
                    var ws = XLSX.utils.table_to_sheet(currentPage.querySelector('table'));

                    // Ajouter le tableau au classeur
                    XLSX.utils.book_append_sheet(wb, ws, 'Etat des cotisations');

                    // Sauvegarder le fichier Excel
                    XLSX.writeFile(wb, 'Etat_des_cotisations_' + company_name + '_' + exerciceName + '.xlsx');

                    // Restaurer l'affichage
                    $('#EtatExcelCotisation').show();
                }
            });
        });
    </script>
@endpush