@extends('layouts.app')

@section('title', __('État des cotisations'))

@section('content')
    <div class="container-fluid">
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
                                            <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                                @foreach ($exercices as $exercice)
                                                    <option value="{{ $exercice->id }}" data-debut="{{ $exercice->date_debut }}" data-fin="{{ $exercice->date_fin }}">
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
                                            <select id="employeeName" class="form-select" name="employeeName" tabindex="-1" aria-hidden="true">
                                                <option value="--">-- Tous les employés --</option>   
                                                @foreach ($employee as $emp)
                                                    <option value="{{ $emp->user_id }}">{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="filter_month3" class="filter_month2">
                                    <input type="hidden" name="filter_year3" class="filter_year2">
                                    <button id="EtatPdfCotisation" class="btn btn-danger me-2"><i class="fas fa-file-pdf me-2"></i> {{ __('Pdf') }}</button>
                                    <button id="EtatExcelCotisation" class="btn btn-success"><i class="fas fa-file-pdf me-2"></i> {{ __('Excel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-cotisations">
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
                success: function(data) {
                    var periodesSelect = $("#periodes");
                    periodesSelect.empty();
                    
                    $.each(data, function(key, periode) {
                        var dateDebut = new Date(periode.date_debut);
                        var dateFin = new Date(periode.date_fin);
                        var libelle = 'Période du ' + dateDebut.toLocaleDateString('fr-FR') + ' au ' + dateFin.toLocaleDateString('fr-FR');
                        
                        periodesSelect.append('<option value="' + periode.id + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                    });
                    
                    // Déclencher le chargement des données si une période est sélectionnée
                    if (periodesSelect.val()) {
                        callbacketatcoti();
                    }
                },
                error: function() {
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
            success: function(data) {
                if (data.length > 0) {
                    generateCotisationTable(data);
                } else {
                    showEmptyTable();
                }
            },
            error: function(data) {
                console.error("Erreur lors de la récupération des données de la paylist.");
            }
        });
    }

    function generateCotisationTable(data) {
        // Calculer les totaux
        var totals = calculateTotals(data);
        
        var tableHTML = `
            <table class="table table-bordered table-sm" width="100%">
                <thead>
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

        $.each(data, function(index, element) {
            // Calculer l'exonération pour cet employé
            var resltexoemp = 0;
            if (element.allowance && Array.isArray(element.allowance)) {
                var totalbase = 0;
                var toto = 0;
                var toto2 = 0;
                
                $.each(element.allowance, function(i, allowance) {
                    if (allowance.allowance_option == 26 || allowance.allowance_option == 11) {
                        totalbase += parseFloat(allowance.amount) || 0;
                    }
                    if (allowance.trait_fisc && allowance.trait_fisc.startsWith('10% - Art 116')) {
                        toto += parseFloat(allowance.amount) || 0;
                    }
                    if (allowance.trait_fisc && allowance.trait_fisc.startsWith('100% - Art 116')) {
                        if(allowance.allowance_option == 11 && parseFloat(allowance.amount) > 30000){
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
            totals.imp_brut += parseFloat(element.Imp_brut) || 0;
            
            // Gérer RICF
            if((parseFloat(element.Imp_brut) - parseFloat(element.ricf)) < 0){
                totals.ricf += parseFloat(element.Imp_brut) || 0;
            } else {
                totals.ricf += parseFloat(element.ricf) || 0;
            }
            
            // Gérer imp_net
            if((parseFloat(element.Imp_brut) - parseFloat(element.ricf)) < 0){
                totals.imp_net += 0;
            } else {
                totals.imp_net += parseFloat(element.imp_net) || 0;
            }
            
            totals.cnps_sal += parseFloat(element.cnps_sal) || 0;
            totals.cnps_emp += parseFloat(element.cnps_emp) || 0;
            totals.pf_emp += parseFloat(element.pf_emp) || 0;
            totals.acc_work += parseFloat(element.acc_trav) || 0;
            totals.taxe_fpc += parseFloat(element.taxe_fpc) || 0;
            totals.taxe_app += parseFloat(element.taxe_appr) || 0;
            totals.cmu_sal += parseFloat(element.cmu_sal) || 0;
            totals.cmu_emp += parseFloat(element.cmu_emp) || 0;
            totals.ce_emp += parseFloat(element.ce_emp) || 0;
            totals.ce_exp_emp += parseFloat(element.ce_exp_emp) || 0;
            totals.cpte++;
            
            if(element.sexe == 'Male'){
                totals.sexemale++;
            }
            if(element.sexe == 'Female'){
                totals.sexefemale++;
            }
            if(element.charge_expat == 'expat'){
                if(element.sexe == 'Male'){
                    totals.cpte_expat_male = (totals.cpte_expat_male || 0) + 1;
                }
                if(element.sexe == 'Female'){
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

        // Total CNPS
        rows += `<tr>
            <td bgcolor="#FEC58C" align="center" class="border border-dark" colspan="2"><strong>Total CNPS</strong></td>
            <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${totals.cnps_sal.toLocaleString()}</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.cnps_emp + totals.pf_emp + totals.acc_work).toLocaleString()}</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.cnps_sal + totals.cnps_emp + totals.pf_emp + totals.acc_work).toLocaleString()}</strong></td>
            <td align="right" bgcolor="#FEC58C" colspan="2" class="border border-dark"> </td>
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
            <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc/2).toLocaleString()}</td>
            <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc/2).toLocaleString()}</td>
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
            <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc/2).toLocaleString()}</td>
            <td align="right" class="border border-dark">${Math.round(totals.taxe_fpc/2).toLocaleString()}</td>
            <td align="right" class="border border-dark">${totals.sexemale}</td>
            <td align="right" class="border border-dark">${totals.sexefemale}</td>
        </tr>`;

        // Total FDFP - Taxe à la FPC
        rows += `<tr>
            <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total FDFP - Taxe à la FPC </strong></td>
            <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>0</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${Math.round(totals.taxe_fpc/2).toLocaleString()}</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${Math.round(totals.taxe_fpc/2).toLocaleString()}</strong></td>
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
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.taxe_app + totals.taxe_fpc/2).toLocaleString()}</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.taxe_app + totals.taxe_fpc/2).toLocaleString()}</strong></td>
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
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.ce_exp_emp + totals.ce_emp + totals.taxe_fpc/2 + totals.taxe_app).toLocaleString()}</strong></td>
            <td bgcolor="#FEC58C" align="right" class="border border-dark"><strong>${(totals.imp_net + totals.ce_exp_emp + totals.ce_emp + totals.taxe_fpc/2 + totals.taxe_app).toLocaleString()}</strong></td>
            <td align="right" colspan="2" bgcolor="#FEC58C" class="border border-dark"><strong></strong></td>
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

        // Ligne vide
        rows += `<tr>
            <td align="right" colspan="12" class="border border-dark"><br></td>
        </tr>`;

        // Total Général
        rows += `<tr>
            <td align="center" bgcolor="#FEC58C" class="border border-dark" colspan="2"><strong>Total Général</strong></td>
            <td align="right" bgcolor="#FEC58C" colspan="5" class="border border-dark"></td>
            <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${(totals.imp_net + totals.cnps_sal + totals.cmu_sal).toLocaleString()}</strong></td>
            <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${(totals.cnps_emp + totals.pf_emp + totals.acc_work + totals.taxe_fpc/2 + totals.taxe_app + totals.cmu_emp + totals.ce_emp + totals.ce_exp_emp).toLocaleString()}</strong></td>
            <td align="right" bgcolor="#FEC58C" class="border border-dark"><strong>${(totals.imp_net + totals.cnps_emp + totals.pf_emp + totals.acc_work + totals.taxe_fpc/2 + totals.taxe_app + totals.cmu_emp + totals.ce_emp + totals.ce_exp_emp).toLocaleString()}</strong></td>
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

    $(document).ready(function() {
        callperiodepaie();

        // Si vous avez un champ d'année qui peut changer
        $("#exercice").on('change', function() {
            callperiodepaie();
        });

        callbacketatcoti();

        $(document).on("change", "#exercice, #periodes, #employeeName", function() {
            callbacketatcoti();
        });

        // Export PDF
        $('#EtatPdfCotisation').on('click', function() {
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

                // Fonction pour nettoyer les valeurs numériques
                function cleanNumericValue(value) {
                    if (!value || typeof value !== 'string') return value;
                    return value.replace(/\//g, '').replace(/\s+/g, '').trim();
                }

                // Extraire le tableau et le prétraiter
                var tableElement = currentPage.querySelector('table');

                // Convertir le tableau HTML en tableau autoTable
                var tableData = [];
                var tableHeaders = [];

                // Extraire les en-têtes
                var headerRow = tableElement.querySelector('tr');
                if (headerRow) {
                    var headerCells = headerRow.querySelectorAll('td');
                    headerCells.forEach(function(cell, cellIndex) {
                        tableHeaders.push({
                            content: cell.innerText.trim(),
                            styles: {
                                halign: 'center',
                                valign: 'middle',
                                fontStyle: 'bold'
                            },
                            colSpan: cellIndex === 0 ? 2 : 1,
                        });
                    });
                }

                // Extraire les données des lignes
                var rows = tableElement.querySelectorAll('tr');
                rows.forEach(function(row, rowIndex) {
                    // Ignorer la première ligne (en-têtes)
                    if (rowIndex === 0) return;

                    var rowData = [];
                    var cells = row.querySelectorAll('td');

                    // Vérifier si c'est une ligne de total
                    var isTotalRow = false;
                    if (cells.length > 0) {
                        var firstCellText = cells[0].textContent.trim();
                        isTotalRow = firstCellText.includes('Total') || firstCellText.includes('IMPOT') || firstCellText.includes('Général');
                    }

                    cells.forEach(function(cell, cellIndex) {
                        var cellContent = cell.innerText.trim();

                        // Nettoyer les valeurs numériques
                        if (cellIndex >= 2) {
                            cellContent = cleanNumericValue(cellContent);
                        }

                        // Définir l'alignement
                        var alignment = 'left';
                        if (isTotalRow) {
                            if (cellIndex >= 2) {
                                if (cellIndex === 3 && cellContent.toLowerCase() === 'calculé') {
                                    alignment = 'center';
                                } else {
                                    alignment = 'right';
                                }
                            } else {
                                alignment = 'center';
                            }
                        } else if (cellIndex >= 2 || cellIndex === 0) {
                            alignment = 'right';
                        }

                        // Fusion des colonnes
                        var colSpan = 1;
                        var rowSpan = 1;
                        if (isTotalRow) {
                            if (cellIndex === 0) {
                                colSpan = 2;
                            } else if (cellIndex === 1) {
                                colSpan = 5;
                            } else if (cellIndex === 5) {
                                colSpan = 2;
                            }
                        }

                        // Fusion verticale pour la cellule contenant "Calculé"
                        if (cellContent.toLowerCase() === 'calculé') {
                            rowSpan = 2;
                            alignment = 'center';
                        }

                        rowData.push({
                            content: cellContent,
                            styles: {
                                halign: cellContent.toLowerCase() === 'calculé' ? 'center' : alignment,
                                valign: 'middle',
                                fontStyle: isTotalRow ? 'bold' : 'normal',
                                fillColor: rowIndex === 0 ? [254, 197, 140] : null,
                            },
                            colSpan: colSpan,
                            rowSpan: rowSpan,
                        });
                    });

                    tableData.push(rowData);

                    if (isTotalRow) {
                        rowData.isColored = true;
                    }
                });

                // Calculer des largeurs de colonne optimales
                var columnWidths = [];
                var tableWidth = doc.internal.pageSize.getWidth() - 20;

                columnWidths[0] = 15;
                columnWidths[1] = 50;

                var remainingWidth = tableWidth - columnWidths[0] - columnWidths[1];
                var remainingCols = tableHeaders.length - 2;
                var colWidth = remainingWidth / remainingCols;

                for (var i = 2; i < tableHeaders.length; i++) {
                    columnWidths[i] = colWidth;
                }

                // Créer le tableau dans le PDF
                doc.autoTable({
                    head: [tableHeaders.map(h => h.content)],
                    body: tableData,
                    startY: 15,
                    margin: { top: 15, right: 10, bottom: 10, left: 10 },
                    styles: {
                        fontSize: 7,
                        cellPadding: 2,
                        overflow: 'linebreak',
                        lineWidth: 0.1
                    },
                    headStyles: {
                        fillColor: [240, 240, 240],
                        textColor: [0, 0, 0],
                        fontStyle: 'bold'
                    },
                    columnStyles: {
                        0: { halign: 'right', cellWidth: columnWidths[0] },
                        1: { halign: 'left', cellWidth: columnWidths[1] }
                    },
                    didParseCell: function(data) {
                        var rowIndex = data.row.index;
                        var colIndex = data.column.index;

                        if (colIndex >= 2 && data.section === 'body') {
                            data.cell.styles.halign = 'right';
                            data.cell.styles.cellWidth = columnWidths[colIndex];
                        }
                    }
                });

                // Sauvegarder le PDF
                doc.save('Etat_des_cotisations_' + company_name + '_' + exerciceName + '.pdf');

                // Restaurer l'affichage
                $('#EtatPdfCotisation').show();
            }
        });

        // Export Excel
        $('#EtatExcelCotisation').on('click', function() {
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
