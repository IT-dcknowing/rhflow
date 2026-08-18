 @extends('layouts.app')

@section('title', 'Gestion des Livres de Paie')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la page -->       
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📋 Gestion des Livres de Paie</h4>
                    <p class="text-muted mb-0">Consultez le livre de paie individuel de chaque employé</p>
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
 
    <!--livre de paie-->
    <div id="livre-paie" class="row">
        <div class="col-12">
            <div class="card table-responsive" id="individuel">           
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px;">
                            <div class="d-flex align-items-center justify-content-start">
                                <h5>{{ __('Livre de paie individuel') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                    <div class="btn-box">
                                        <select id="employeeName" class="form-select" name="employeeName" tabindex="-1" aria-hidden="true">
                                            @foreach ($employee as $emp)
                                                <option value="{{ $emp->user_id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                    <div class="btn-box">
                                        <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                            @foreach ($exercices as $exercice)
                                                <option value="{{ $exercice->id }}" data-debut="{{ $exercice->date_debut }}" data-fin="{{ $exercice->date_fin }}" {{ isset($activeExercice) && $activeExercice->id == $exercice->id ? 'selected' : '' }}>
                                                    {{ $exercice->nom }}
                                                </option> 
                                            @endforeach
                                        </select>
                                    </div>     
                                </div>
                                <input type="hidden" name="company_name" id="company_name" value="{{ $company->name ?? 'Entreprise' }}">
                                <input type="hidden" name="cpte_tableindividuel" id="cpte_tableindividuel">
                                <button class="btn btn-danger me-2" id="export-pdf-btn"><i class="fas fa-file-pdf me-2"></i> {{ __('Pdf') }}</button>
                                <button class="btn btn-success" id="export-excel-btn"><i class="fas fa-file-pdf me-2"></i> {{ __('Excel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-container-individuel">
                        </div>
                        <div id="pagination-individuel" align="center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
  
@push('scripts')
<script>
    //fonction du livre de paie
    function callbacklivrepaieIndiv() {
        var employeeId = $("#employeeName").val();
        var exerciceId = $("#exercice").val();
        var selectedOption  = $("#exercice").find("option:selected");
        var exerciceName = selectedOption.text().replace(/\//g, '').replace(/\s+/g, '').trim();
        var dateDebut = new Date(selectedOption.data('debut'));
        var dateFin = new Date(selectedOption.data('fin'));
        var company_name = document.getElementById('company_name').value;
        var nameEmp = '';

        // Vérifiez si year est défini
        if (!exerciceId) {
            var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible") }}</div>';
            document.getElementById('table-container-individuel').innerHTML = tableHTML;
        }

        $.ajax({
            url: '{{ route('company.declarations.get_paylists_individuel') }}',
            type: 'GET',
            data: {
                "exercice_id": exerciceId,
                "_token": "{{ csrf_token() }}"
            }, 
            success: function(data) {
                // Vérifier si les données sont vides
                if (data && data.length > 0) {
                    var dateDuJour = new Date().toLocaleDateString();
                    var heureActuelle = new Date().toLocaleTimeString();
                    var periode = "Période : du " + dateDebut.toLocaleDateString() + " au " + dateFin.toLocaleDateString();

                    // Définir les mois à afficher (de janvier à décembre)
                    var months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

                    // Filtrer les données pour l'employé sélectionné si spécifié
                    var employeeData = [];
                    var monthlyData = [];
                    
                    // Organiser les données
                    if (employeeId && employeeId !== "--") {
                        // Filtrer pour un employé spécifique
                        employeeData = data.filter(function(item) {
                            return (item.emp_user_id == employeeId);
                        });

                        if (employeeData.length === 0) {
                            console.log("Aucune donnée trouvée pour l'employé ID: " + employeeId);
                            document.getElementById('table-container-individuel').innerHTML =
                                '<div class="alert alert-warning">Aucune donnée trouvée pour cet employé</div>';
                            return;
                        }

                        // Organiser les données par mois pour l'employé
                        months.forEach(function(month, index) {
                            monthlyData[month] = null; // Initialiser tous les mois à null
                        });

                        // Remplir avec les données disponibles
                        employeeData.forEach(function(item) {
                            try {
                                var date = new Date(item.salary_month);
                                var monthIndex = date.getMonth();
                                var monthName = months[monthIndex];
                                monthlyData[monthName] = item;
                            } catch(e) {
                                console.error("Erreur de formatage de date:", e);
                            }
                        });
                    } else {
                        // Mode cumul: agréger les données de tous les employés par mois
                        months.forEach(function(month, index) {
                            monthlyData[month] = {
                                basic_salary: 0,
                                nbre_jour: 0,
                                salary_brut: 0,
                                Imp_brut: 0,
                                ricf: 0,
                                imp_net: 0,
                                cnps_sal: 0,
                                cmu_sal: 0,
                                total_retenue: 0,
                                overtime: 0,
                                loan: 0,
                                montant_retenue: 0,
                                leave: 0,
                                net_payble: 0,
                                avtg_real: 0,
                                allowance: [],
                                retenue: []
                            };
                        });
                        
                        // Parcourir toutes les données
                        data.forEach(function(item) {
                            try {
                                var date = new Date(item.salary_month);
                                var monthIndex = date.getMonth();
                                var monthName = months[monthIndex];

                                // Cumuler les valeurs numériques
                                for (var key in monthlyData[monthName]) {
                                    if (key !== 'allowance' && key !== 'retenue' && item[key] !== undefined) {
                                        var value = parseFloat(item[key].toString().replace(/\s/g, ''));
                                        if (!isNaN(value)) {
                                            monthlyData[monthName][key] += value;
                                        }
                                    }
                                }

                                // Traiter les allocations séparément
                                if (item.allowance && Array.isArray(item.allowance)) {
                                    item.allowance.forEach(function(allowance) {
                                        var amount = parseFloat(allowance.amount);
                                        if (!isNaN(amount)) {
                                            // Chercher si cette allocation existe déjà
                                            var existingAllowance = monthlyData[monthName].allowance.find(a =>
                                                (a.code && a.code === allowance.code) ||
                                                (a.title && a.title === allowance.title)
                                            );

                                            if (existingAllowance) {
                                                // Cumuler le montant
                                                existingAllowance.amount += amount;
                                            } else {
                                                // Ajouter la nouvelle allocation
                                                monthlyData[monthName].allowance.push({
                                                    code: allowance.code,
                                                    title: allowance.title,
                                                    type: 'allowance',
                                                    amount: amount,
                                                    allowance_option: allowance.allowance_option
                                                });
                                            }
                                        }
                                    });
                                }

                                // Traiter les retenues séparément
                                if (item.retenue && Array.isArray(item.retenue)) {
                                    item.retenue.forEach(function(retenue) {
                                        var amount = parseFloat(retenue.amount);
                                        if (!isNaN(amount)) {
                                            // Chercher si cette retenue existe déjà
                                            var existingRetenue = monthlyData[monthName].retenue.find(r =>
                                                (r.code && r.code === retenue.code) ||
                                                (r.libelle && r.libelle === retenue.libelle)
                                            );

                                            if (existingRetenue) {
                                                // Cumuler le montant
                                                existingRetenue.amount += amount;
                                            } else {
                                                // Ajouter la nouvelle retenue
                                                monthlyData[monthName].retenue.push({
                                                    code: retenue.code,
                                                    libelle: retenue.libelle,
                                                    type: 'retenue',
                                                    amount: amount
                                                });
                                            }
                                        }
                                    });
                                }
                            } catch(e) {
                                console.error("Erreur lors du cumul des données:", e);
                            }
                        });
                    }

                    // Nombre de mois à afficher par page
                    var monthsPerPage = 6;
                    var totalPages = Math.ceil(months.length / monthsPerPage);

                    // Création du tableau HTML
                    var tableHTML = '';
                    var paginationHTML = '';

                    // Récupérer les informations de l'employé si disponible
                    var employeeInfo = employeeId !== "--" && employeeData.length > 0
                        ? employeeData.find(emp => emp.emp_user_id == employeeId)
                        : null;

                    // Créer une page pour chaque ensemble de mois
                    for (var pageIndex = 0; pageIndex < totalPages; pageIndex++) {
                        tableHTML += '<div id="page-individuel-' + (pageIndex + 1) + '" class="page-individuel" style="width:100%;">';
                        tableHTML += '<table id="table-body-indiv-' + (pageIndex + 1) + '" class="table-sm" width="100%">';

                        // En-tête avec date, titre et numéro de page
                        tableHTML += '<thead><tr><td colspan="' + (monthsPerPage + 3) + '" class="border border-dark">';
                        tableHTML += '<div class="row">';
                        tableHTML += '<div class="col-md-3"><p>Date du jour : '+ dateDuJour +'</p><p>Heure : '+ heureActuelle +'</p></div>';
                        tableHTML += '<div class="col-md-6" style="text-align:center;"><h4>Livre de paie I N D I V I D U E L</h4><p>'+ periode +'</p></div>';
                        tableHTML += '<div class="col-md-3" style="text-align:right;"><p>Page : '+ (pageIndex + 1) +'/'+ totalPages +'</p><h4><span style="background-color:#1e3a8a; color:white; padding:5px; border-radius:5px; font-weight:bold;">© rh-flow</span></h4></div>';
                        tableHTML += '</div>';
                        tableHTML += '</td></tr>';

                        // Information sur la société
                        tableHTML += '<tr><td colspan="' + (monthsPerPage + 3) + '"><span>Société : <b>'+ company_name +'</b></span></td></tr>';

                        // Si un employé spécifique est sélectionné, afficher ses informations
                        if (employeeId && employeeId !== "--" && employeeInfo) {
                            tableHTML += '<tr><td colspan="' + (monthsPerPage + 3) + '" class="border border-dark"><span>Salarié : <b>' +
                                (employeeInfo.name || '') + '</b></span><input type="hidden" id="name_empolyee" name="name_empolyee" value="'+ employeeInfo.name +'"></td></tr>';
                                nameEmp = employeeInfo.name;
                        } else {
                            // Afficher "Cumul" quand aucun employé n'est sélectionné
                            tableHTML += '<tr><td colspan="' + (monthsPerPage + 3) + '" class="border border-dark"><span>Salarié : <b>Cumul</b></span></td></tr>';
                        }

                        // En-tête des colonnes
                        tableHTML += '<tr>';
                        tableHTML += '<th width="5%" class="border border-dark"><center><strong>Code</strong></center></th>';
                        tableHTML += '<th width="25%" class="border border-dark"><center><strong>Rubriques</strong></center></th>';

                        // Calculer les indices de début et de fin pour cette page
                        var startMonthIndex = pageIndex * monthsPerPage;
                        var endMonthIndex = Math.min((pageIndex + 1) * monthsPerPage, months.length);

                        // Ajouter les en-têtes de mois pour cette page
                        for (var i = startMonthIndex; i < endMonthIndex; i++) {
                            tableHTML += '<th width="10%" class="border border-dark"><center><strong>' + months[i] + '</strong></center></th>';
                        }

                        // Ajouter la colonne Total sur la dernière page
                        if (pageIndex === totalPages - 1) {
                            tableHTML += '<th width="10%" class="border border-dark"><center><strong>Total</strong></center></th>';
                        }
                        tableHTML += '</tr></thead>';

                        // Corps du tableau avec les données
                        tableHTML += '<tbody>';

                        // Nbre de jours imposables
                        tableHTML += '<tr><td align="right" class="border border-dark">5</td><td class="border border-dark">Nbre de jours imposables</td>';
                        var totalimpos = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalimpos += parseInt(monthlyData[months[i]].nbre_jour);
                            }
                        }
                        for (var i = startMonthIndex; i < endMonthIndex; i++) {
                            var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].nbre_jour) : 0;
                            tableHTML += '<td align="right" class="border border-dark">' + value + '</td>';
                        }
                        if (pageIndex === totalPages - 1) {
                            tableHTML += '<td align="right" class="border border-dark">'+ totalimpos.toLocaleString() +'</td></tr>';
                        }

                        // Salaire de base
                        tableHTML += '<tr><td align="right" class="border border-dark">100</td><td class="border border-dark">Salaire de base</td>';
                        var totalBasic_salary = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalBasic_salary += parseInt(monthlyData[months[i]].basic_salary.toString().replace(/\s/g, ''));
                            }
                        }
                        for (var i = startMonthIndex; i < endMonthIndex; i++) {
                            var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].basic_salary.toString().replace(/\s/g, '')) : 0;
                            tableHTML += '<td align="right" class="border border-dark">'+ value.toLocaleString() +'</td>';
                        }
                        if (pageIndex === totalPages - 1) {
                            tableHTML += '<td align="right" class="border border-dark">'+ totalBasic_salary.toLocaleString() +'</td></tr>';
                        }

                        // Afficher les allocations dynamiquement
                        var allowanceCodes = {};
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]] && monthlyData[months[i]].allowance && monthlyData[months[i]].allowance.length > 0) {
                                monthlyData[months[i]].allowance.forEach(function(allowance) {
                                    if (allowance.amount > 0) {
                                        allowanceCodes[allowance.code] = allowance.title;
                                    }
                                });
                            }
                        }

                        Object.keys(allowanceCodes).forEach(function(code) {
                            var title = allowanceCodes[code];
                            var totalAmount = 0;
                            
                            for (var i = 0; i < months.length; i++) {
                                if (monthlyData[months[i]] && monthlyData[months[i]].allowance && monthlyData[months[i]].allowance.length > 0) {
                                    monthlyData[months[i]].allowance.forEach(function(allowance) {
                                        if (allowance.code == code && allowance.amount > 0) {
                                            totalAmount += parseFloat(allowance.amount);
                                        }
                                    });
                                }
                            }

                            if (totalAmount > 0) {
                                tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + title + '</td>';
                                
                                for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                    var amount = 0;
                                    if (monthlyData[months[i]] && monthlyData[months[i]].allowance && monthlyData[months[i]].allowance.length > 0) {
                                        monthlyData[months[i]].allowance.forEach(function(allowance) {
                                            if (allowance.code == code && allowance.amount > 0) {
                                                amount = parseFloat(allowance.amount);
                                            }
                                        });
                                    }
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                }
                                
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                }
                            }
                        });

                        // Avantages en nature
                        var totalAvantages = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalAvantages += parseInt(monthlyData[months[i]].avtg_real.toString().replace(/\s/g, ''));
                            }
                        }
                        if (totalAvantages > 0) {
                            tableHTML += '<tr><td align="right" class="border border-dark">150</td><td class="border border-dark">Avantages en nature et en argent</td>';
                            for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].avtg_real.toString().replace(/\s/g, '')) : 0;
                                tableHTML += '<td align="right" class="border border-dark">'+ value.toLocaleString() +'</td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalAvantages.toLocaleString() +'</td></tr>';
                            }
                        }

                        // Total Brut
                        tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Total Brut</td>';
                        var totalSalary_brut = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalSalary_brut += parseInt(monthlyData[months[i]].salary_brut.toString().replace(/\s/g, ''));
                            }
                        }
                        for (var i = startMonthIndex; i < endMonthIndex; i++) {
                            var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].salary_brut.toString().replace(/\s/g, '')) : 0;
                            tableHTML += '<td align="right" class="border border-dark">'+ value.toLocaleString() +'</td>';
                        }
                        if (pageIndex === totalPages - 1) {
                            tableHTML += '<td align="right" class="border border-dark">'+ totalSalary_brut.toLocaleString() +'</td></tr>';
                        }

                        // Retenues ITS (codes 401-403)
                        var retenueCodes = {};
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                monthlyData[months[i]].retenue.forEach(function(retenue) {
                                    if (retenue.amount > 0 && retenue.code > 400 && retenue.code < 404) {
                                        retenueCodes[retenue.code] = retenue.libelle;
                                    }
                                });
                            }
                        }

                        Object.keys(retenueCodes).forEach(function(code) {
                            var libelle = retenueCodes[code];
                            var totalAmount = 0;
                            
                            for (var i = 0; i < months.length; i++) {
                                if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                    monthlyData[months[i]].retenue.forEach(function(retenue) {
                                        if (retenue.code == code && retenue.code > 400 && retenue.code < 404 && retenue.amount > 0) {
                                            totalAmount += parseFloat(retenue.amount);
                                        }
                                    });
                                }
                            }

                            if (totalAmount > 0) {
                                tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                
                                for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                    var amount = 0;
                                    if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                        monthlyData[months[i]].retenue.forEach(function(retenue) {
                                            if (retenue.code == code && retenue.code > 400 && retenue.code < 404 && retenue.amount > 0) {
                                                amount = parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                }
                                
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                }
                            }
                        });

                        // Retenues CNPS (codes 301-302)
                        var retenueCodesCnps = {};
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                monthlyData[months[i]].retenue.forEach(function(retenue) {
                                    if (retenue.amount > 0 && retenue.code > 300 && retenue.code < 303) {
                                        retenueCodesCnps[retenue.code] = retenue.libelle;
                                    }
                                });
                            }
                        }

                        Object.keys(retenueCodesCnps).forEach(function(code) {
                            var libelle = retenueCodesCnps[code];
                            var totalAmount = 0;
                            
                            for (var i = 0; i < months.length; i++) {
                                if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                    monthlyData[months[i]].retenue.forEach(function(retenue) {
                                        if (retenue.code == code && retenue.code > 300 && retenue.code < 303 && retenue.amount > 0) {
                                            totalAmount += parseFloat(retenue.amount);
                                        }
                                    });
                                }
                            }

                            if (totalAmount > 0) {
                                tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                
                                for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                    var amount = 0;
                                    if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                        monthlyData[months[i]].retenue.forEach(function(retenue) {
                                            if (retenue.code == code && retenue.code > 300 && retenue.code < 303 && retenue.amount > 0) {
                                                amount = parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                }
                                
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                }
                            }
                        });

                        // Total Cotisations
                        var totalCotisations = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalCotisations += parseInt(monthlyData[months[i]].total_retenue.toString().replace(/\s/g, ''));
                            }
                        }
                        if (totalCotisations > 0) {
                            tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Total Cotisations</td>';
                            for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].total_retenue.toString().replace(/\s/g, '')) : 0;
                                tableHTML += '<td align="right" class="border border-dark">'+ value.toLocaleString() +'</td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalCotisations.toLocaleString() +'</td></tr>';
                            }
                        }

                        // Autres retenues (codes > 499)
                        var autreRetenueCodes = {};
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                monthlyData[months[i]].retenue.forEach(function(retenue) {
                                    if (retenue.amount > 0) {
                                        autreRetenueCodes[retenue.code] = retenue.libelle;
                                    }
                                });
                            }
                        }

                        Object.keys(autreRetenueCodes).forEach(function(code) {
                            var libelle = autreRetenueCodes[code];
                            var totalAmount = 0;
                            
                            for (var i = 0; i < months.length; i++) {
                                if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                    monthlyData[months[i]].retenue.forEach(function(retenue) {
                                        if (retenue.code == code && retenue.code > 499 && retenue.amount > 0) {
                                            totalAmount += parseFloat(retenue.amount);
                                        }
                                    });
                                }
                            }

                            if (totalAmount > 0) {
                                tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                
                                for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                    var amount = 0;
                                    if (monthlyData[months[i]] && monthlyData[months[i]].retenue && monthlyData[months[i]].retenue.length > 0) {
                                        monthlyData[months[i]].retenue.forEach(function(retenue) {
                                            if (retenue.code == code && retenue.code > 499 && retenue.amount > 0) {
                                                amount = parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                }
                                
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                }
                            }
                        });

                        // Salaire Net
                        var totalNet = 0;
                        for (var i = 0; i < months.length; i++) {
                            if (monthlyData[months[i]]) {
                                totalNet += parseInt(monthlyData[months[i]].net_payble.toString().replace(/\s/g, ''));
                            }
                        }
                        if (totalNet > 0) {
                            tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Salaire Net</td>';
                            for (var i = startMonthIndex; i < endMonthIndex; i++) {
                                var value = monthlyData[months[i]] ? parseInt(monthlyData[months[i]].net_payble.toString().replace(/\s/g, '')) : 0;
                                tableHTML += '<td align="right" class="border border-dark">'+ value.toLocaleString() +'</td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalNet.toLocaleString() +'</td></tr>';
                            }
                        }

                        tableHTML += '</tbody></table></div>';
                    }
                    
                    // Ajouter des boutons de pagination
                    if (totalPages > 1) {
                        // Pagination
                        paginationHTML += '<div class="pagination-container mt-3">';
                        paginationHTML += '<ul class="pagination individuel">';
                        for (var i = 0; i < totalPages; i++) {
                            var activeClass = i === 0 ? 'active' : '';
                            paginationHTML += '<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + (i + 1) + '">' + (i + 1) + '</a></li>';
                        }
                        paginationHTML += '</ul>';
                        paginationHTML += '</div>';
                    }

                    // Mettre à jour le conteneur HTML
                    document.getElementById('table-container-individuel').innerHTML = tableHTML + paginationHTML;

                    // Gérer la pagination
                    $('.individuel .page-link').on('click', function(e) {
                        e.preventDefault();
                        var pageNumber = $(this).data('page');
                        $('.page-individuel').hide();
                        $('#page-individuel-' + pageNumber).show();
                        $('.individuel .page-item').removeClass('active');
                        $(this).parent().addClass('active');
                    });

                    // Afficher seulement la première page au départ
                    $('.page-individuel').hide();
                    $('#page-individuel-1').show();

                    //Exportation en pdf
                    $('#export-pdf-btn').on('click', function() {
                        // Obtenir tous les éléments de page
                        var pages = document.querySelectorAll('.page-individuel');
                        var name = nameEmp; // Assurez-vous que cette variable est définie

                        // Cacher la pagination et le bouton d'export pour l'impression
                        $('.pagination-container').hide();
                        $('#export-pdf-btn').hide();

                        // Afficher toutes les pages pour l'export PDF
                        //$('.page-individuel').show();

                        // Vérifier que jsPDF est disponible, sinon utiliser directement
                        if (typeof jspdf === 'undefined') {
                            console.error("jsPDF library not loaded");
                            this.innerHTML = '<i class="fas fa-file-pdf me-2"></i> Exporter en pdf';
                            this.disabled = false;
                            return;
                        } else {
                            generatePDF();
                        }

                        function generatePDF() {
                            // Créer un nouveau document PDF
                            var doc = new jspdf.jsPDF('p', 'mm', 'a4');
                            var totalPages = pages.length;

                            // Fonction pour nettoyer les valeurs numériques (supprimer les slashs et espaces)
                            function cleanNumericValue(value) {
                                if (!value || typeof value !== 'string') return value;

                                // Supprimer les slashs et les espaces inutiles
                                return value.replace(/\//g, '').replace(/\s+/g, '').trim();
                            }

                            // Traiter chaque page séparément
                            var processPage = function(index) {
                                if (index >= totalPages) {
                                    // Toutes les pages ont été traitées, sauvegarder le PDF
                                    doc.save('Livre_de_paie_individuel_' + name + '_' + exerciceName + '.pdf');

                                    // Restaurer l'affichage après l'exportation
                                    $('.page-individuel').hide();
                                    $('#page-individuel-1').show();
                                    $('#export-pdf-btn').show();
                                    return;
                                }

                                // Ajouter une nouvelle page sauf pour la première page
                                if (index > 0) {
                                    doc.addPage();
                                }

                                var currentPage = pages[index];
                                var pageNumber = index + 1;

                                // Récupérer les informations d'en-tête
                                var dateDuJour = new Date().toLocaleDateString('fr-FR');
                                var heureActuelle = new Date().toLocaleTimeString('fr-FR');
                                var periode = "Période : du " + dateDebut.toLocaleDateString() + " au " + dateFin.toLocaleDateString();
                                var companyName = document.getElementById('company_name').value;

                                // Ajouter l'en-tête
                                doc.setFontSize(10);
                                doc.text("Date du jour : " + dateDuJour, 10, 10);
                                doc.text("Heure : " + heureActuelle, 10, 15);

                                doc.setFontSize(16);
                                doc.text("Livre de paie I N D I V I D U E L", doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

                                doc.setFontSize(10);
                                doc.text(periode, doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
                                doc.text("Page : " + pageNumber + "/" + totalPages, doc.internal.pageSize.getWidth() - 10, 10, { align: 'right' });

                                // Logo ou texte copyright
                                doc.setFontSize(12);
                                doc.setTextColor(30, 58, 138);
                                doc.text("© RH-Flow", doc.internal.pageSize.getWidth() - 10, 20, { align: 'right' });
                                doc.setTextColor(0, 0, 0);

                                // Société et salarié
                                doc.setFontSize(10);
                                doc.text("Société : " + companyName, 10, 30);
                                doc.text("Salarié : " + name, 10, 35);

                                // Extraire le tableau et le prétraiter
                                var tableElement = currentPage.querySelector('table');

                                // Convertir le tableau HTML en tableau autoTable
                                var tableData = [];
                                var tableHeaders = [];

                                // Extraire les en-têtes
                                var headerRows = tableElement.querySelectorAll('thead tr');
                                if (headerRows.length > 0) {
                                    var headerCells = headerRows[headerRows.length - 1].querySelectorAll('th');
                                    headerCells.forEach(function(cell, cellIndex) {
                                        tableHeaders.push({
                                            content: cell.innerText.trim(),
                                            styles: {
                                                halign: 'center',
                                                valign: 'middle',
                                                fontStyle: 'bold'
                                            },
                                            colSpan: cellIndex === 0 ? 2 : 1, // Fusionner uniquement la première colonne
                                        });
                                    });
                                }

                                // Extraire les données des lignes
                                var rows = tableElement.querySelectorAll('tbody tr');
                                rows.forEach(function(row) {
                                    var rowData = [];
                                    var cells = row.querySelectorAll('td');

                                    // Vérifier si c'est une ligne de total
                                    var isTotalRow = false;
                                    if (cells.length > 0) {
                                        var firstCellText = cells[0].textContent.trim();
                                        isTotalRow = firstCellText === 'Total Brut' ||
                                                    firstCellText === 'Total Cotisations' ||
                                                    firstCellText === 'Salaire Net';
                                    }

                                    cells.forEach(function(cell, cellIndex) {
                                        var cellContent = cell.innerText.trim();

                                        // Nettoyer les valeurs numériques (supprimer les slashs et espaces)
                                        if (cellIndex >= 2) {
                                            cellContent = cleanNumericValue(cellContent);
                                        }

                                        if (isTotalRow && cellIndex >= 1) {
                                            cellContent = cleanNumericValue(cellContent);
                                        }

                                        // Définir l'alignement
                                        var alignment = 'left';
                                        if (isTotalRow) {
                                            if (cellIndex >= 2) {
                                                alignment = 'right';
                                            } else {
                                                alignment = 'center';
                                            }
                                        } else if (cellIndex >= 2 || cellIndex === 0) {
                                            alignment = 'right';
                                        }

                                        rowData.push({
                                            content: cellContent,
                                            styles: {
                                                halign: alignment,
                                                valign: 'middle',
                                                fontStyle: isTotalRow ? 'bold' : 'normal'
                                            },
                                            colSpan: isTotalRow ? (cellIndex >= 1 ? 1 : 2) : 1,
                                        });
                                    });

                                    tableData.push(rowData);

                                    if (isTotalRow) {
                                        rowData.isColored = true;
                                    }
                                });

                                // Calculer des largeurs de colonne optimales
                                var columnWidths = [];
                                var tableWidth = doc.internal.pageSize.getWidth() - 20; // Marge de 10mm de chaque côté

                                // Première colonne (Code) - petite
                                columnWidths[0] = 15;

                                // Deuxième colonne (Rubriques) - plus large
                                columnWidths[1] = 40;

                                // Répartir le reste de la largeur disponible entre les autres colonnes
                                var remainingWidth = tableWidth - columnWidths[0] - columnWidths[1];
                                var remainingCols = tableHeaders.length - 2; // Utiliser tableHeaders au lieu de headerCells
                                var colWidth = remainingWidth / remainingCols;

                                for (var i = 2; i < tableHeaders.length; i++) {
                                    columnWidths[i] = colWidth;
                                }

                                // Créer le tableau dans le PDF
                                doc.autoTable({
                                    head: [tableHeaders.map(h => h.content)],
                                    body: tableData,
                                    startY: 40, // Ajuster la position Y pour éviter le chevauchement avec l'en-tête
                                    margin: { top: 40, right: 10, bottom: 10, left: 10 },
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

                                        // Appliquer l'alignement à droite pour les colonnes numériques
                                        if (colIndex >= 2 && data.section === 'body') {
                                            data.cell.styles.halign = 'right';
                                            data.cell.styles.cellWidth = columnWidths[colIndex];
                                        }

                                        // Appliquer la couleur de fond pour les lignes de totaux
                                        if (data.section === 'body' && tableData[rowIndex] && tableData[rowIndex].isColored) {
                                            data.cell.styles.fillColor = [254, 197, 140];
                                        }
                                    },
                                    didDrawCell: function(data) {
                                        // Fusionner et centrer les cellules des lignes de totaux
                                        if (data.section === 'body' && tableData[data.row.index] && tableData[data.row.index].isColored) {
                                            if (data.column.index === 0) {
                                                // Fusionner toutes les cellules de la ligne
                                                doc.autoTableText(
                                                    data.cell.raw.content,
                                                    data.cell.x + data.cell.width / 2,
                                                    data.cell.y + data.cell.height / 2,
                                                    {
                                                        halign: 'center',
                                                        valign: 'middle'
                                                    }
                                                );
                                                data.cell.styles.fillColor = [254, 197, 140]; // Couleur de fond
                                            }
                                        }
                                    }
                                });

                                // Passer à la page suivante
                                setTimeout(function() {
                                    processPage(index + 1);
                                }, 100);
                            };

                            // Démarrer le traitement avec la première page
                            processPage(0);
                        }
                    });

                    $('#export-excel-btn').on('click', function() {
                        // Get all page elements
                        var pages = document.querySelectorAll('.page-individuel');
                        var name = nameEmp; // Make sure this variable is defined

                        // Hide pagination and export button
                        $('.pagination-container').hide();
                        $('#export-excel-btn').hide();

                        // Check if XLSX is available, otherwise use directly
                        if (typeof XLSX === 'undefined') {
                            console.error("XLSX library not loaded");
                            return;
                        } else {
                            generateExcel();
                        }

                        function generateExcel() {
                            // Create new Excel workbook
                            var wb = XLSX.utils.book_new();

                            // Process each page separately
                            for (var index = 0; index < pages.length; index++) {
                                var currentPage = pages[index];
                                var pageNumber = index + 1;

                                // Get header information
                                var dateDuJour = new Date().toLocaleDateString('fr-FR');
                                var heureActuelle = new Date().toLocaleTimeString('fr-FR');
                                var periode = "Période : du " + dateDebut.toLocaleDateString() + " au " + dateFin.toLocaleDateString();
                                var companyName = document.getElementById('company_name').value;

                                // Add header data
                                var headerData = [
                                    ["Date du jour : " + dateDuJour],
                                    ["Heure : " + heureActuelle],
                                    ["Livre de paie I N D I V I D U E L"],
                                    [periode],
                                    ["Page : " + pageNumber],
                                    ["Société : " + companyName],
                                    ["Salarié : " + name],
                                    [] // Empty row for spacing
                                ];

                                // Get table element
                                var table = currentPage.querySelector('table');

                                // Extract table headers
                                var headers = [];
                                var headerCells = table.querySelectorAll('thead th');
                                headerCells.forEach(function(cell) {
                                    headers.push(cell.textContent.trim());
                                });

                                // Add headers to data
                                headerData.push(headers);

                                // Extract table body data
                                var rows = table.querySelectorAll('tbody tr');
                                rows.forEach(function(row) {
                                    var rowData = [];
                                    var cells = row.querySelectorAll('td');
                                    cells.forEach(function(cell) {
                                        rowData.push(cell.textContent.trim());
                                    });
                                    headerData.push(rowData);
                                });

                                // Create worksheet from data
                                var ws = XLSX.utils.aoa_to_sheet(headerData);

                                // Add worksheet to workbook
                                XLSX.utils.book_append_sheet(wb, ws, "Page " + pageNumber);
                            }

                            // Save Excel file
                            XLSX.writeFile(wb, 'Livre_paie_individuel_' + name + '_' + exerciceName + '.xlsx');

                            // Restore display
                            $('.pagination-container').show();
                            $('#export-excel-btn').show();
                        }
                    });


                } else {
                    // Aucune donnée disponible
                    document.getElementById('table-container-individuel').innerHTML =
                        '<div class="alert alert-info text-center">Aucune donnée disponible pour cette année</div>';
                }
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX:", status, error);
                document.getElementById('table-container-individuel').innerHTML =
                    '<div class="alert alert-danger text-center">Erreur lors de la récupération des données</div>';
            }
        });
    }
    
    // Initialiser la fonction au chargement de la page
    $(document).ready(function() {
        callbacklivrepaieIndiv();

        $(document).on("change", "#employeeName, #exercice", function() {
            callbacklivrepaieIndiv();
        });
    });

    // Fonction pour la pagination
    function showPageIndiv(pageNum) {
        // Cacher toutes les pages
        var pages = document.getElementsByClassName('page-individuel');
        for (var i = 0; i < pages.length; i++) {
            pages[i].style.display = 'none';
        }

        // Afficher la page demandée
        var pageToShow = document.getElementById('page-individuel-' + pageNum);
        if (pageToShow) {
            pageToShow.style.display = 'block';
        }

        // Mettre à jour l'état actif des boutons de pagination
        var buttons = document.getElementsByClassName('pagination-btn');
        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].textContent == pageNum) {
                buttons[i].classList.add('active');
                buttons[i].style.backgroundColor = '#FFD700';
                buttons[i].style.fontWeight = 'bold';
            } else {
                buttons[i].classList.remove('active');
                buttons[i].style.backgroundColor = '';
                buttons[i].style.fontWeight = '';
            }
        }
    }
</script>
@endpush