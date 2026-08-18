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
                    <p class="text-muted mb-0">Consultez le livre de paie mensuel des employés</p>
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
    <div class="row">
        <div class="col-12">
            <div class="card table-responsive" id="munsuel">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px;">
                            <div class="d-flex align-items-center justify-content-start">
                                <h5>{{ __('Livre de paie mensuel') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                    <div class="btn-box">
                                        <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                            @foreach ($exercices as $exercice)
                                                <option value="{{ $exercice->id }}" {{ isset($activeExercice) && $activeExercice->id == $exercice->id ? 'selected' : '' }}>
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
                                <button id="ExportToMensuel" class="btn btn-danger me-2"><i class="fas fa-file-pdf me-2"></i> {{ __('Pdf') }}</button>
                                <button id="ExcelToMensuel" class="btn btn-success"><i class="fas fa-file-pdf me-2"></i> {{ __('Excel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-container" style="padding:10px;"></div>
                        <div id="pagination" align="center"></div>
                    </div>
                    <input type="hidden" name="company_name" id="company_name" value="{{ $company->name }}">
                    <input type="hidden" name="cpte_table" class="form-control" id="cpte_table">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
  
@push('scripts')
<script>
    // Charger les périodes quand on change d'exercice
    function callperiodepaie(selectedPeriodeId = null) {
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
                        var isSelected = (selectedPeriodeId && selectedPeriodeId == periode.id) ? 'selected' : '';
                        periodesSelect.append('<option value="' + periode.id + '" ' + isSelected + ' data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                    });
                    
                    // Déclencher le chargement des données si une période est sélectionnée
                    if (periodesSelect.val()) {
                        callbacklivrepaie();
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des périodes');
                }
            });
        }else{
            var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible") }}</div>';
            document.getElementById('table-container').innerHTML = tableHTML;
        }
    }
    
    //Livre de paie Mensuel
    function callbacklivrepaie() {
        var periodeId = $("#periodes").val();
        var periodeName = $("#periodes").find("option:selected").text().replace(/\//g, '').replace(/\s+/g, '').trim();
        var company_name = document.getElementById('company_name').value;
        
        if (!periodeId) {
            document.getElementById('table-container').innerHTML = 
                '<div class="alert alert-info text-center">Veuillez sélectionner un exercice et ensuite une période</div>';
            return;
        }
        
        $.ajax({
            url: '{{ route('company.declarations.get_paylists') }}',
            type: 'GET',
            data: { periode_id: periodeId },
            success: function(data) {
                // Vérifier si les données sont vides
                if (data.length > 0) {
                    // Nombre total de colonnes dans le tableau
                    var totalColumns = data.length;
                    var dateDuJour = new Date().toLocaleDateString();
                    var heureActuelle = new Date().toLocaleTimeString();
                    
                    // Récupérer les informations de la période depuis le select
                    var periodeSelect = $("#periodes option:selected");
                    var periodeText = periodeSelect.text();
                    var premierJourFormatte = new Date(periodeSelect.data('pdebut')).toLocaleDateString('fr-FR');
                    var dernierJourFormatte = new Date(periodeSelect.data('pfin')).toLocaleDateString('fr-FR');
                    var periode = "Période : " + periodeText + " du " + premierJourFormatte + " au " + dernierJourFormatte;
                    
                    // Nombre de colonnes à afficher par page (y compris la première colonne)
                    var columnsPerPage = 8;

                    // Calcul du nombre total de pages
                    var totalPages = Math.ceil(totalColumns / (columnsPerPage - 1));
                    document.getElementById('cpte_table').value = totalPages;
                    // Création du tableau HTML
                    var tableHTML = '';
                    var paginationHTML = '';

                    var cpte_prime = 0; var primeTitles = {}; var codePrimes = {}; var cpte_employee = 0;// Objet pour stocker les titres des primes rencontrées
                    $.each(data, function(index, element) {
                        var allowances = element.allowance;
                        cpte_employee++
                        $.each(allowances, function(i, allowance) {
                            if (allowance.amount > 0 && (allowance.allowance_option == 30 || allowance.allowance_option > 31)) {
                                var primeTitle = allowance.title;
                                var codePrime = allowance.code;
                                cpte_prime = 1; // Vérifier si le titre de la prime n'est pas déjà stocké dans l'objet
                                if (!primeTitles.hasOwnProperty(primeTitle)) {
                                    primeTitles[primeTitle] = true; // Stocker le titre de la prime dans l'objet
                                }
                                if (!codePrimes.hasOwnProperty(codePrime)) {
                                    codePrimes[codePrime] = true; // Stocker le titre de la prime dans l'objet
                                }
                            }
                        });
                    });

                    // Ajout des colonnes restantes
                    for (var pageIndex = 0; pageIndex < totalPages; pageIndex++) {
                        tableHTML += '<div id="page-mensuel-' + (pageIndex + 1) + '" class="page-mensuel" style="width: 100%;">';
                        tableHTML += '<table id="table-body-' + (pageIndex + 1) + '" class="table-sm">';
                        tableHTML += '<thead><tr><td colspan ="'+ (3+ parseInt(cpte_employee)) +'" class="border border-dark">';
                        tableHTML += '<div class="row"><div class="col-md-3"><p>Date du jour : '+ dateDuJour +'</p><br> <p>Heure : '+ heureActuelle +'</p></div>';
                        tableHTML += '<div class="col-md-6" style="text-align:center;"><h4>Livre de paie M E N S U E L</h4><br><p>'+ periode +'</p></div>',
                        tableHTML += '<div class="col-md-3" style="text-align:right;"><p>Page : '+ (pageIndex + 1) +'</p><br><h4> <span style="background-color:#1e3a8a; color:white; padding:5px; border-radius:5px; font-weight:bold;">© rh-flow</span></h4></div></div>';
                        tableHTML += '</td></tr>';
                        tableHTML += '<tr><td colspan ="'+ (3+ parseInt(cpte_employee)) +'"><span>Société :  <b>'+ company_name +'</b></span></td></tr>';
                        tableHTML += '<tr>';
                            // Ajout de la première colonne de l'entête sur toutes les pages
                            tableHTML += '<th width="5%" class="border border-dark"><center><strong>Code</strong></center></th>';
                            tableHTML += '<th width="25%" class="border border-dark"><center><strong>Rubriques</strong></center></th>';
                            // Ajout des colonnes restantes de l'entête pour cette page
                            var startIndex = pageIndex * (columnsPerPage - 1);
                            var endIndex = Math.min((pageIndex + 1) * (columnsPerPage - 1), totalColumns);
                            for (var i = startIndex; i < endIndex; i++) {
                                // Utilisation des données de chaque élément pour créer les colonnes de l'en-tête
                                var element = data[i];
                                tableHTML += '<th width="10%" class="border border-dark"><center><strong>' + element.employee_id + '<br/>' + element.name + '</strong></center></th>';
                            }
                            // Ajout de la colonne "Total" uniquement sur le dernier tableau
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<th width="10%" class="border border-dark"><center><strong>Total</strong></center></th>';
                            }
                        tableHTML += '</tr></thead>';
                        tableHTML += '<tbody>';
                            tableHTML += '<tr><td align="right" width="5%" class="border border-dark">5</td><td width="25%" class="border border-dark">Nbre de jours imposables</td>';
                            var totalimpos = 0;
                            $.each(data, function(index, element) {
                                totalimpos = totalimpos + 30;
                            });
                            // Ajout des lignes de données pour cette page
                            for (var i = startIndex; i < endIndex; i++) {
                                tableHTML += '<td align="right" class="border border-dark">30</td>';
                            }
                            // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalimpos.toLocaleString() +'</td></tr>';
                            }

                            tableHTML += '<tr><td align="right" class="border border-dark">8</td><td class="border border-dark">Nbre de jours travaillés</td>';
                            var totalJours = 0;
                            $.each(data, function(index, element) {
                                totalJours = totalJours + parseInt(element.nbre_jour);
                            });
                            // Ajout des lignes de données pour cette page
                            for (var i = startIndex; i < endIndex; i++) {
                                var element = data[i];
                                tableHTML += '<td align="right" class="border border-dark">'+ element.nbre_jour +'</td>';
                            }
                            // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalJours.toLocaleString() +'</td></tr>';
                            }

                            tableHTML += '<tr><td align="right" class="border border-dark">100</td><td class="border border-dark">Salaire de base</td>';
                            var totalBasic_salary = 0;
                            $.each(data, function(index, element) {
                                totalBasic_salary = totalBasic_salary + parseInt(element.basic_salary.replace(/\s/g, ''));
                            });
                            // Ajout des lignes de données pour cette page
                            for (var i = startIndex; i < endIndex; i++) {
                                var element = data[i];
                                tableHTML += '<td align="right" class="border border-dark">'+ element.basic_salary +'</td>';
                            }
                            // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalBasic_salary.toLocaleString() +'</td></tr>';
                            }

                            // Afficher les allocations dynamiquement
                            var allowanceCodes = {};
                            $.each(data, function(index, element) {
                                if (element.allowance && element.allowance.length > 0) {
                                    $.each(element.allowance, function(i, allowance) {
                                        if (allowance.amount > 0) {
                                            allowanceCodes[allowance.code] = allowance.title;
                                        }
                                    });
                                }
                            });

                            $.each(allowanceCodes, function(code, title) {
                                var totalAmount = 0;
                                $.each(data, function(index, element) {
                                    if (element.allowance && element.allowance.length > 0) {
                                        $.each(element.allowance, function(i, allowance) {
                                            if (allowance.code == code && allowance.amount > 0) {
                                                totalAmount += parseFloat(allowance.amount);
                                            }
                                        });
                                    }
                                });

                                if (totalAmount > 0) {
                                    tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + title + '</td>';
                                    
                                    // Ajout des lignes de données pour cette page
                                    for (var i = startIndex; i < endIndex; i++) {
                                        var element = data[i];
                                        var amount = 0;
                                        if (element.allowance && element.allowance.length > 0) {
                                            $.each(element.allowance, function(j, allowance) {
                                                if (allowance.code == code && allowance.amount > 0) {
                                                    amount = parseFloat(allowance.amount);
                                                    return false;
                                                }
                                            });
                                        }
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                    }
                                    
                                    // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                                    if (pageIndex === totalPages - 1) {
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                    }
                                }
                            });

                            // Avantages en nature
                            var totalAvantages = 0;
                            $.each(data, function(index, element) {
                                totalAvantages += parseInt(element.avtg_real.replace(/\s/g, ''));
                            });
                            if (totalAvantages > 0) {
                                tableHTML += '<tr><td align="right" class="border border-dark">150</td><td class="border border-dark">Avantages en nature et en argent</td>';
                                for (var i = startIndex; i < endIndex; i++) {
                                    var element = data[i];
                                    tableHTML += '<td align="right" class="border border-dark">'+ element.avtg_real +'</td>';
                                }
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">'+ parseInt(totalAvantages).toLocaleString() +'</td></tr>';
                                }
                            }

                            // Total Brut
                            tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Total Brut</td>';
                            var totalSalary_brut = 0;
                            $.each(data, function(index, element) {
                                totalSalary_brut += parseInt(element.salary_brut.replace(/\s/g, ''));
                            });
                            for (var i = startIndex; i < endIndex; i++) {
                                var element = data[i];
                                tableHTML += '<td align="right" class="border border-dark">'+ element.salary_brut +'</td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">'+ totalSalary_brut.toLocaleString() +'</td></tr>';
                            }
                            <!--------- Retenues ITS --------->
                            // Afficher les retenues dynamiquement
                            var retenueCodes = {};
                            $.each(data, function(index, element) {
                                if (element.retenue && element.retenue.length > 0) {
                                    $.each(element.retenue, function(i, retenue) {
                                        if (retenue.amount > 0 && retenue.code > 400 && retenue.code < 404) {
                                            retenueCodes[retenue.code] = retenue.libelle;
                                        }
                                    });
                                }
                            });

                            $.each(retenueCodes, function(code, libelle) {
                                var totalAmount = 0;
                                $.each(data, function(index, element) {
                                    if (element.retenue && element.retenue.length > 0) {
                                        $.each(element.retenue, function(i, retenue) {
                                            if (retenue.code == code && retenue.code > 400 && retenue.code < 404 && retenue.amount > 0) {
                                                totalAmount += parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                });

                                if (totalAmount > 0) {
                                    tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                    
                                    // Ajout des lignes de données pour cette page
                                    for (var i = startIndex; i < endIndex; i++) {
                                        var element = data[i];
                                        var amount = 0;
                                        if (element.retenue && element.retenue.length > 0) {
                                            $.each(element.retenue, function(j, retenue) {
                                                if (retenue.code == code && retenue.code > 400 && retenue.code < 404 && retenue.amount > 0) {
                                                    amount = parseFloat(retenue.amount);
                                                    return false;
                                                }
                                            });
                                        }
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                    }
                                    
                                    // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                                    if (pageIndex === totalPages - 1) {
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                    }
                                }
                            });

                            var retenueCodesCnps = {};
                            $.each(data, function(index, element) {
                                if (element.retenue && element.retenue.length > 0) {
                                    $.each(element.retenue, function(i, retenue) {
                                        if (retenue.amount > 0 && retenue.code > 300 && retenue.code < 303) {
                                            retenueCodesCnps[retenue.code] = retenue.libelle;
                                        }
                                    });
                                }
                            });

                            $.each(retenueCodesCnps, function(code, libelle) {
                                var totalAmount = 0;
                                $.each(data, function(index, element) {
                                    if (element.retenue && element.retenue.length > 0) {
                                        $.each(element.retenue, function(i, retenue) {
                                            if (retenue.code == code && retenue.code > 300 && retenue.code < 303 && retenue.amount > 0) {
                                                totalAmount += parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                });

                                if (totalAmount > 0) {
                                    tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                    
                                    // Ajout des lignes de données pour cette page
                                    for (var i = startIndex; i < endIndex; i++) {
                                        var element = data[i];
                                        var amount = 0;
                                        if (element.retenue && element.retenue.length > 0) {
                                            $.each(element.retenue, function(j, retenue) {
                                                if (retenue.code == code && retenue.code > 300 && retenue.code < 303 && retenue.amount > 0) {
                                                    amount = parseFloat(retenue.amount);
                                                    return false;
                                                }
                                            });
                                        }
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                    }
                                    
                                    // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                                    if (pageIndex === totalPages - 1) {
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                    }
                                }
                            });

                            // Total Cotisations
                            var totalCotisations = 0;
                            $.each(data, function(index, element) {
                                totalCotisations += parseInt(element.total_retenue.replace(/\s/g, ''));
                            });
                            if (totalCotisations > 0) {
                                tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Total Cotisations</td>';
                                for (var i = startIndex; i < endIndex; i++) {
                                    var element = data[i];
                                    tableHTML += '<td align="right" class="border border-dark">'+ element.total_retenue +'</td>';
                                }
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">'+ parseInt(totalCotisations).toLocaleString() +'</td></tr>';
                                }
                            }

                            // Afficher les autres retenues dynamiquement
                            var autreRetenueCodes = {};
                            $.each(data, function(index, element) {
                                if (element.retenue && element.retenue.length > 0) {
                                    $.each(element.retenue, function(i, retenue) {
                                        if (retenue.amount > 0) {
                                            autreRetenueCodes[retenue.code] = retenue.libelle;
                                        }
                                    });
                                }
                            });

                            $.each(autreRetenueCodes, function(code, libelle) {
                                var totalAmount = 0;
                                $.each(data, function(index, element) {
                                    if (element.retenue && element.retenue.length > 0) {
                                        $.each(element.retenue, function(i, retenue) {
                                            if (retenue.code == code && retenue.code > 499 && retenue.amount > 0) {
                                                totalAmount += parseFloat(retenue.amount);
                                            }
                                        });
                                    }
                                });

                                if (totalAmount > 0) {
                                    tableHTML += '<tr><td align="right" width="5%" class="border border-dark">' + code + '</td><td width="25%" class="border border-dark">' + libelle + '</td>';
                                    
                                    // Ajout des lignes de données pour cette page
                                    for (var i = startIndex; i < endIndex; i++) {
                                        var element = data[i];
                                        var amount = 0;
                                        if (element.retenue && element.retenue.length > 0) {
                                            $.each(element.retenue, function(j, retenue) {
                                                if (retenue.code == code && retenue.code > 499 && retenue.amount > 0) {
                                                    amount = parseFloat(retenue.amount);
                                                    return false;
                                                }
                                            });
                                        }
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(amount).toLocaleString() + '</td>';
                                    }
                                    
                                    // Ajout des données de la colonne "Total" uniquement sur le dernier tableau
                                    if (pageIndex === totalPages - 1) {
                                        tableHTML += '<td align="right" class="border border-dark">' + Math.round(totalAmount).toLocaleString() + '</td></tr>';
                                    }
                                }
                            });

                            // Salaire Net
                            var totalNet = 0;
                            $.each(data, function(index, element) {
                                totalNet += parseInt(element.net_payble.replace(/\s/g, ''));
                            });
                            if (totalNet > 0) {
                                tableHTML += '<tr bgcolor="#FEC58C"><td align="center" class="border border-dark" colspan="2">Salaire Net</td>';
                                for (var i = startIndex; i < endIndex; i++) {
                                    var element = data[i];
                                    tableHTML += '<td align="right" class="border border-dark">'+ element.net_payble +'</td>';
                                }
                                if (pageIndex === totalPages - 1) {
                                    tableHTML += '<td align="right" class="border border-dark">'+ parseInt(totalNet).toLocaleString() +'</td></tr>';
                                }
                            }

                            // Nombre de salariés
                            var cpte = 0;
                            tableHTML += '<tr><td align="center" colspan="2" class="border border-dark">Nombre de salariés</td>';
                            for (var i = startIndex; i < endIndex; i++) {
                                tableHTML += '<td align="right" class="border border-dark"> </td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td align="right" class="border border-dark">' + data.length + '</td></tr>';
                            }
                        
                        tableHTML += '</tbody></table>';
                        tableHTML += '</div>';

                        //paginationHTML += '<button onclick="showPage(' + (pageIndex + 1) + ')">' + (pageIndex + 1) + '</button>';
                    }

                    // Ajouter des boutons de pagination
                    if (totalPages > 1) {
                        // Pagination
                        paginationHTML += '<div class="pagination-container mt-3">';
                        paginationHTML += '<ul class="pagination mensuel">';
                        for (var i = 0; i < totalPages; i++) {
                            var activeClass = i === 0 ? 'active' : '';
                            paginationHTML += '<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + (i + 1) + '">' + (i + 1) + '</a></li>';
                        }
                        paginationHTML += '</ul>';
                        paginationHTML += '</div>';
                    }
                        // Ajout du tableau et de la pagination au document
                    document.getElementById('table-container').innerHTML = tableHTML;
                    document.getElementById('pagination').innerHTML = paginationHTML;

                    // Gérer la pagination
                    $('.mensuel .page-link').on('click', function(e) {
                        e.preventDefault();
                        var pageNumber = $(this).data('page');
                        $('.page-mensuel').hide();
                        $('#page-mensuel-' + pageNumber).show();
                        $('.mensuel .page-item').removeClass('active');
                        $(this).parent().addClass('active');
                    });

                    // Afficher seulement la première page au départ
                    $('.page-mensuel').hide();
                    $('#page-mensuel-1').show();

                    $('#ExportToMensuel').on('click', function() {
                        // Obtenir tous les éléments de page
                        var pages = document.querySelectorAll('.page-mensuel');

                        // Afficher un message de chargement
                        this.innerHTML = 'Génération en cours...';
                        this.disabled = true;

                        // Cacher la pagination et le bouton d'export pour l'impression
                        $('.pagination-container').hide();
                        $('#ExportToMensuel').hide();

                        // Afficher toutes les pages pour l'export PDF
                        //$('.page-mensuel').show();

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
                                    doc.save('Livre_de_paie_mensuel_'+ company_name +'_'+ periodeName +'.pdf');

                                    // Restaurer l'affichage après l'exportation
                                    $('.page-mensuel').hide();
                                    $('#page-mensuel-1').show();
                                    $('.pagination-container').show();
                                    $('#ExportToMensuel').show();
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
                                var periode = "Période : du " + premierJourFormatte + " au " + dernierJourFormatte;
                                var companyName = document.getElementById('company_name').value;

                                // Ajouter l'en-tête
                                doc.setFontSize(10);
                                doc.text("Date du jour : " + dateDuJour, 10, 10);
                                doc.text("Heure : " + heureActuelle, 10, 15);

                                doc.setFontSize(16);
                                doc.text("Livre de paie M E N S U E L", doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

                                doc.setFontSize(10);
                                doc.text(periode, doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
                                doc.text("Page : " + pageNumber + "/" + totalPages, doc.internal.pageSize.getWidth() - 10, 10, { align: 'right' });

                                // Logo ou texte copyright
                                doc.setFontSize(12);
                                doc.setTextColor(30, 58, 138);
                                doc.text("© RH-Flow", doc.internal.pageSize.getWidth() - 10, 20, { align: 'right' });
                                doc.setTextColor(0, 0, 0);

                                // Société
                                doc.setFontSize(10);
                                doc.text("Société : " + companyName, 10, 30);

                                // Extraire le tableau et le prétraiter
                                var tableElement = currentPage.querySelector('table');

                                // Convertir le tableau HTML en tableau autoTable
                                var tableData = [];
                                var tableHeaders = [];

                                // Extraire les en-têtes
                                var headerRows = tableElement.querySelectorAll('thead tr');
                                if (headerRows.length > 0) {
                                    var headerCells = headerRows[headerRows.length - 1].querySelectorAll('th');
                                    headerCells.forEach(function(cell) {
                                        tableHeaders.push({
                                            content: cell.innerText.trim(),
                                            styles: {
                                                halign: 'center',
                                                valign: 'middle',
                                                fontStyle: 'bold'
                                            }
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
                                                    firstCellText === 'Salaire Net' ||
                                                    firstCellText === 'Nombre de salariés';
                                    }

                                    cells.forEach(function(cell, cellIndex) {
                                        var cellContent = cell.innerText.trim();

                                        // Nettoyer les valeurs numériques (supprimer les slashs et espaces)
                                        if(cellIndex >= 2){
                                            cellContent = cleanNumericValue(cellContent);
                                        }

                                        if (isTotalRow && cellIndex >= 1) {
                                            cellContent = cleanNumericValue(cellContent);
                                        }

                                        // Determine alignment
                                        var alignment = 'left';
                                        if (isTotalRow) {
                                            if (cellIndex >= 2) {
                                                alignment = 'right';
                                            }else{
                                                alignment = 'center';
                                            }
                                        } else if (cellIndex >= 2 || cellIndex == 0) {
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
                                    startY: 35,
                                    margin: { top: 35, right: 10, bottom: 10, left: 10 },
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

                                        // Appliquer la couleur de fond pour les lignes de totaux mais pas la dernière ligne
                                        if (data.section === 'body' && tableData[rowIndex] && tableData[rowIndex].isColored && rowIndex < tableData.length - 1) {
                                            data.cell.styles.fillColor = [254, 197, 140];
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
                        this.innerHTML = '<i class="fas fa-file-pdf me-2"></i> Exporter en pdf';
                        this.disabled = false;
                    });

                    $('#ExcelToMensuel').on('click', function() {
                        var workbook = XLSX.utils.book_new();
                        var totalPages = parseInt(document.getElementById('cpte_table').value);
                        var periodeId = $("#periodes").val();
                        var periodeText = $("#periodes option:selected").text();
                        var company_name = document.getElementById('company_name').value;

                        // Create worksheet for each page
                        for (var pageIndex = 0; pageIndex < totalPages; pageIndex++) {
                            var currentPage = document.querySelectorAll('.page-mensuel')[pageIndex];
                            var tableElement = currentPage.querySelector('table');

                            // Extract header data
                            var headers = [];
                            var headerCells = tableElement.querySelectorAll('thead th');
                            headerCells.forEach(function(cell) {
                                headers.push(cell.textContent.trim());
                            });

                            // Extract table data
                            var tableData = [headers];
                            var rows = tableElement.querySelectorAll('tbody tr');
                            rows.forEach(function(row) {
                                var rowData = [];
                                var cells = row.querySelectorAll('td');
                                cells.forEach(function(cell) {
                                    rowData.push(cell.textContent.trim());
                                });
                                tableData.push(rowData);
                            });

                            // Create worksheet from data
                            var ws = XLSX.utils.aoa_to_sheet(tableData);

                            // Add worksheet to workbook
                            var wsName = "Page " + (pageIndex + 1);
                            XLSX.utils.book_append_sheet(workbook, ws, wsName);
                        }

                        // Generate Excel file
                        var fileName = 'LIVRE_PAIE_MENSUEL_' + company_name + '_' + periodeText.replace(/[^a-z0-9]/gi, '_') + '.xlsx';
                        XLSX.writeFile(workbook, fileName);
                    });

                }else{
                    // Si aucune donnée n'est disponible
                    var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible pour ce mois") }}</div>';
                    document.getElementById('table-container').innerHTML = tableHTML;
                }
            },
            error: function(data) {
                console.error("Erreur lors de la récupération des données de la paylist.");
            }
        });
    }

    // Initialiser la fonction au chargement de la page
    $(document).ready(function() {
        @if(isset($activeExercice))
            callperiodepaie({{ $activePeriode->id ?? 'null' }});
        @else
            callperiodepaie();
        @endif

        // Si vous avez un champ d'année qui peut changer
        $("#exercice").on('change', function() {
            callperiodepaie();
        });

        // Charger les données quand on change de période
        $("#periodes").on('change', function() {
            callbacklivrepaie();
        });
    });
</script>
@endpush