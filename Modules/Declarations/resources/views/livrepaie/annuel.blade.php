@extends('layouts.app')

@section('title', 'Gestion des Livres de Paie')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des Livres de Paie</h4>
                        <p class="text-muted mb-0">Consultez le livre de paie annuel des employés</p>
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
                <div class="card table-responsive" id="annuel">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4" style="margin-bottom: 10px;">
                                <div class="d-flex align-items-center justify-content-start">
                                    <h5>{{ __('Livre de paie annuel') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                        <div class="btn-box">
                                            <select id="exercice" class="form-select select2" name="exercice" tabindex="-1"
                                                aria-hidden="true">
                                                @foreach ($exercices as $exercice)
                                                    <option value="{{ $exercice->id }}" data-debut="{{ $exercice->date_debut }}"
                                                        data-fin="{{ $exercice->date_fin }}">
                                                        {{ $exercice->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="company_name" id="company_name"
                                        value="{{ $company->name ?? 'Entreprise' }}">
                                    <input type="hidden" name="cpte_tableannuel" id="cpte_tableannuel">
                                    <button class="btn btn-danger me-2" id="exportPdfAnnuel"><i
                                            class="fas fa-file-pdf me-2"></i> {{ __('Pdf') }}</button>
                                    <button class="btn btn-success" id="exportExelAnnuel"><i
                                            class="fas fa-file-pdf me-2"></i> {{ __('Excel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="table-container-annuels"></div>
                            <div id="pagination-annuels" align="center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        //fonction du livre du paie annuel
        function callbacklivrepaieAnnuel() {
            var exerciceId = $("#exercice").val();
            var selectedOption = $("#exercice").find("option:selected");
            var dateDebut = new Date(selectedOption.data('debut'));
            var dateFin = new Date(selectedOption.data('fin'));
            var exerciceName = selectedOption.text().replace(/\//g, '').replace(/\s+/g, '').trim();
            var company_name = document.getElementById('company_name').value;

            $.ajax({
                url: '{{ route('company.declarations.get_paylists_annuel') }}',
                type: 'GET',
                data: { exercice_id: exerciceId },
                success: function (data) {
                    // Vérifier si les données sont vides
                    if (data && data.length > 0) {
                        // Partie de la création des rubriques
                        var dateDuJour = new Date().toLocaleDateString();
                        var heureActuelle = new Date().toLocaleTimeString();
                        var periode = "Période : du " + dateDebut.toLocaleDateString() + " au " + dateFin.toLocaleDateString();

                        // Définir les rubriques standards
                        var standardRubriques = [
                            { id: 'nbre_jour', label: 'Nombre de jours', type: 'brut', code: '8' },
                            { id: 'basic_salary', label: 'Salaire de base', type: 'brut', code: '100' },
                            { id: 'avtg_real', label: 'Avantages en nature', type: 'brut', code: '150' },
                            { id: 'salary_brut', label: 'Total Brut', type: 'total', code: '' },
                            { id: 'total_retenue', label: 'Total Cotisations', type: 'total', code: '' },
                            { id: 'net_payble', label: 'Salaire Net', type: 'total', code: '' }
                        ];

                        // Extraire toutes les allowances uniques
                        var allowanceRubriques = {};
                        data.forEach(function (employee) {
                            if (employee.allowances && Array.isArray(employee.allowances)) {
                                employee.allowances.forEach(function (allowance) {
                                    if (allowance.amount && parseFloat(allowance.amount) > 0) {
                                        var allowanceKey = allowance.code || allowance.title;
                                        if (!allowanceRubriques[allowanceKey]) {
                                            allowanceRubriques[allowanceKey] = {
                                                id: allowanceKey,
                                                label: allowance.title,
                                                code: allowance.code,
                                                type: 'allowance',
                                                option: allowance.allowance_option_id
                                            };
                                        }
                                    }
                                });
                            }
                        });

                        // Extraire toutes les retenues ITS uniques
                        var retenueItsRubriques = {};
                        data.forEach(function (employee) {
                            if (employee.retenues && Array.isArray(employee.retenues)) {
                                employee.retenues.forEach(function (retenue) {
                                    if (retenue.amount && parseFloat(retenue.amount) > 0 && retenue.code > 400 && retenue.code < 404) {
                                        var retenueKey = retenue.code || retenue.libelle;
                                        if (!retenueItsRubriques[retenueKey]) {
                                            retenueItsRubriques[retenueKey] = {
                                                id: retenueKey,
                                                label: retenue.libelle,
                                                code: retenue.code,
                                                type: 'retenue_its',
                                                option: retenue.type_retenue_id
                                            };
                                        }
                                    }
                                });
                            }
                        });

                        // Extraire toutes les retenues CNPS uniques
                        var retenueCnpsRubriques = {};
                        data.forEach(function (employee) {
                            if (employee.retenues && Array.isArray(employee.retenues)) {
                                employee.retenues.forEach(function (retenue) {
                                    if (retenue.amount && parseFloat(retenue.amount) > 0 && retenue.code > 300 && retenue.code < 303) {
                                        var retenueKey = retenue.code || retenue.libelle;
                                        if (!retenueCnpsRubriques[retenueKey]) {
                                            retenueCnpsRubriques[retenueKey] = {
                                                id: retenueKey,
                                                label: retenue.libelle,
                                                code: retenue.code,
                                                type: 'retenue_cnps',
                                                option: retenue.type_retenue_id
                                            };
                                        }
                                    }
                                });
                            }
                        });

                        // Extraire toutes les retenues autres uniques
                        var retenueRubriques = {};
                        data.forEach(function (employee) {
                            if (employee.retenues && Array.isArray(employee.retenues)) {
                                employee.retenues.forEach(function (retenue) {
                                    if (retenue.amount && parseFloat(retenue.amount) > 0 && retenue.code > 499) {
                                        var retenueKey = retenue.code || retenue.libelle;
                                        if (!retenueRubriques[retenueKey]) {
                                            retenueRubriques[retenueKey] = {
                                                id: retenueKey,
                                                label: retenue.libelle,
                                                code: retenue.code,
                                                type: 'retenue',
                                                option: retenue.type_retenue_id
                                            };
                                        }
                                    }
                                });
                            }
                        });

                        // Combiner les rubriques en respectant l'ordre
                        var allRubriques = [];

                        // Rubriques de type brut (sauf celles correspondant au total)
                        var brutRubriques = standardRubriques.filter(r => r.type === 'brut' && r.code != '150');
                        brutRubriques.forEach(rubrique => {
                            allRubriques.push(rubrique);
                        });

                        // Ajouter les allowances triées par code
                        Object.values(allowanceRubriques)
                            .sort((a, b) => (a.code || '').localeCompare(b.code || ''))
                            .forEach(r => allRubriques.push(r));

                        // Ajouter les avantages en nature
                        allRubriques.push(standardRubriques.find(r => r.id === 'avtg_real'));

                        // Ajouter le total brut
                        allRubriques.push(standardRubriques.find(r => r.id === 'salary_brut'));

                        // Ajouter les rubriques de retenue ITS
                        Object.values(retenueItsRubriques)
                            .sort((a, b) => (a.code || '').localeCompare(b.code || ''))
                            .forEach(r => allRubriques.push(r));

                        // Ajouter les rubriques de retenue CNPS
                        Object.values(retenueCnpsRubriques)
                            .sort((a, b) => (a.code || '').localeCompare(b.code || ''))
                            .forEach(r => allRubriques.push(r));

                        // Ajouter le total des retenues
                        allRubriques.push(standardRubriques.find(r => r.id === 'total_retenue'));

                        // Ajouter les rubriques de retenue
                        Object.values(retenueRubriques)
                            .sort((a, b) => (a.code || '').localeCompare(b.code || ''))
                            .forEach(r => allRubriques.push(r));

                        // Ajouter le net à payer
                        allRubriques.push(standardRubriques.find(r => r.id === 'net_payble'));

                        // Nombre d'employés par page
                        var employeesPerPage = 8;
                        var totalPages = Math.ceil(data.length / employeesPerPage);

                        // Création du tableau HTML
                        var tableHTML = '';
                        var paginationHTML = '';

                        for (var pageIndex = 0; pageIndex < totalPages; pageIndex++) {
                            // Calculer les indices de début et de fin pour cette page
                            var startIndex = pageIndex * employeesPerPage;
                            var endIndex = Math.min(startIndex + employeesPerPage, data.length);

                            tableHTML += '<div id="page-annuel-' + (pageIndex + 1) + '" class="page-annuel" style="width:100%">';
                            tableHTML += '<table id="table-body-annuel' + (pageIndex + 1) + '" class="table-sm" width="100%">';

                            // En-tête du tableau
                            tableHTML += '<thead>';
                            tableHTML += '<tr><td colspan="' + (endIndex - startIndex + 3) + '" class="border border-dark">';
                            tableHTML += '<div class="row">';
                            tableHTML += '<div class="col-md-3"><p>Date du jour : ' + dateDuJour + '</p><p>Heure : ' + heureActuelle + '</p></div>';
                            tableHTML += '<div class="col-md-6" style="text-align:center;"><h4>Livre de paie A N N U E L</h4><p>' + periode + '</p></div>';
                            tableHTML += '<div class="col-md-3" style="text-align:right;"><p>Page : ' + (pageIndex + 1) + '/' + totalPages + '</p><h4><span style="background-color:#1e3a8a; color:white; padding:5px; border-radius:5px; font-weight:bold;">© rh-flow</span></h4></div>';
                            tableHTML += '</div>';
                            tableHTML += '</td></tr>';
                            tableHTML += '<tr><td colspan="' + (endIndex - startIndex + 3) + '"><span>Société : <b>' + company_name + '</b></span></td></tr>';

                            // En-tête des colonnes
                            tableHTML += '<tr>';
                            tableHTML += '<th width="5%" class="border border-dark"><center><strong>Code</strong></center></th>';
                            tableHTML += '<th width="25%" class="border border-dark"><center><strong>Rubriques</strong></center></th>';

                            // Noms des employés en en-tête
                            for (var i = startIndex; i < endIndex; i++) {
                                var employee = data[i];
                                tableHTML += '<th width="' + (70 / (endIndex - startIndex + 1)) + '%" class="border border-dark"><center><strong>' + employee.employee_id + '<br/>' + employee.name + '</strong></center></th>';
                            }

                            // Ajouter la colonne Total sur la dernière page
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<th width="' + (70 / (endIndex - startIndex + 1)) + '%" class="border border-dark"><center><strong>Total</strong></center></th>';
                            }
                            tableHTML += '</tr>';
                            tableHTML += '</thead>';

                            // Corps du tableau
                            tableHTML += '<tbody>';

                            // Calculer le total annuel pour chaque rubrique
                            var calculateAnnualTotal = function (rubrique, dataSet) {
                                var annualTotal = 0;

                                // Parcourir tous les employés (pas seulement ceux de la page courante)
                                for (var i = 0; i < dataSet.length; i++) {
                                    var employeeData = dataSet[i];
                                    var value = 0;

                                    if (employeeData) {
                                        if (rubrique.type === 'brut' || rubrique.type === 'total') {
                                            // Pour les rubriques standards et autres types
                                            value = employeeData[rubrique.id] !== undefined ? parseFloat(employeeData[rubrique.id].toString().replace(/\s/g, '')) : 0;
                                            if (isNaN(value)) value = 0;
                                        } else if (rubrique.type === 'allowance') {
                                            // Pour les rubriques d'allowance
                                            if (employeeData.allowances && Array.isArray(employeeData.allowances)) {
                                                var allowance = employeeData.allowances.find(a =>
                                                    (a.code && a.code === rubrique.code) ||
                                                    (a.title && a.title === rubrique.label)
                                                );
                                                if (allowance) {
                                                    value = parseFloat(allowance.amount) || 0;
                                                }
                                            }
                                        } else if (rubrique.type === 'retenue_its') {
                                            // Pour les rubriques retenue its
                                            if (employeeData.retenues && Array.isArray(employeeData.retenues)) {
                                                var retenue = employeeData.retenues.find(a =>
                                                    (a.code && a.code === rubrique.code) ||
                                                    (a.title && a.title === rubrique.label)
                                                );
                                                if (retenue) {
                                                    value = parseFloat(retenue.amount) || 0;
                                                }
                                            }

                                        } else if (rubrique.type === 'retenue_cnps') {
                                            // Pour les rubriques retenue cnps
                                            if (employeeData.retenues && Array.isArray(employeeData.retenues)) {
                                                var retenue = employeeData.retenues.find(a =>
                                                    (a.code && a.code === rubrique.code) ||
                                                    (a.title && a.title === rubrique.label)
                                                );
                                                if (retenue) {
                                                    value = parseFloat(retenue.amount) || 0;
                                                }
                                            }
                                        } else if (rubrique.type === 'retenue') {
                                            // Pour les rubriques retenue
                                            if (employeeData.retenues && Array.isArray(employeeData.retenues)) {
                                                var retenue = employeeData.retenues.find(a =>
                                                    (a.code && a.code === rubrique.code) ||
                                                    (a.title && a.title === rubrique.label)
                                                );
                                                if (retenue) {
                                                    value = parseFloat(retenue.amount) || 0;
                                                }
                                            }
                                        }
                                    }
                                    annualTotal += value;
                                }

                                return annualTotal;
                            };

                            // Lignes pour chaque rubrique
                            allRubriques.forEach(function (rubrique) {
                                var isTotal = rubrique.type === 'total';
                                var bgClass = isTotal ? 'background-color: #FEC58C;' : '';

                                // Calcul du total annuel pour cette rubrique
                                var annualTotal = calculateAnnualTotal(rubrique, data);

                                if (isTotal) {
                                    tableHTML += '<tr style="' + bgClass + '">';
                                    tableHTML += '<td colspan="2" class="border border-dark" style="text-align: center;"><strong>' + rubrique.label + '</strong></td>';
                                } else {
                                    tableHTML += '<tr>';
                                    tableHTML += '<td class="border border-dark" align="right"' + (bgClass ? ' style="' + bgClass + '"' : '') + '>' + (rubrique.code || '') + '</td>';
                                    tableHTML += '<td class="border border-dark"' + (bgClass ? ' style="' + bgClass + '"' : '') + '>' + rubrique.label + '</td>';
                                }

                                // Valeurs pour chaque employé
                                for (var i = startIndex; i < endIndex; i++) {
                                    var employee = data[i];
                                    var value = 0;

                                    if (rubrique.type === 'allowance') {
                                        // Chercher dans les allowances de l'employé
                                        if (employee.allowances && Array.isArray(employee.allowances)) {
                                            var allowance = employee.allowances.find(a =>
                                                (a.code && a.code === rubrique.code) ||
                                                (a.title && a.title === rubrique.label)
                                            );
                                            if (allowance) {
                                                value = parseFloat(allowance.amount) || 0;
                                            }
                                        }
                                    } else if (rubrique.type === 'retenue_its') {
                                        // Chercher dans les allowances de l'employé
                                        if (employee.retenues && Array.isArray(employee.retenues)) {
                                            var retenue = employee.retenues.find(a =>
                                                (a.code && a.code === rubrique.code) ||
                                                (a.libelle && a.libelle === rubrique.label)
                                            );
                                            if (retenue) {
                                                value = parseFloat(retenue.amount) || 0;
                                            }
                                        }
                                    } else if (rubrique.type === 'retenue_cnps') {
                                        // Chercher dans les allowances de l'employé
                                        if (employee.retenues && Array.isArray(employee.retenues)) {
                                            var retenue = employee.retenues.find(a =>
                                                (a.code && a.code === rubrique.code) ||
                                                (a.libelle && a.libelle === rubrique.label)
                                            );
                                            if (retenue) {
                                                value = parseFloat(retenue.amount) || 0;
                                            }
                                        }
                                    } else if (rubrique.type === 'retenue') {
                                        // Chercher dans les allowances de l'employé
                                        if (employee.retenues && Array.isArray(employee.retenues)) {
                                            var retenue = employee.retenues.find(a =>
                                                (a.code && a.code === rubrique.code) ||
                                                (a.libelle && a.libelle === rubrique.label)
                                            );
                                            if (retenue) {
                                                value = parseFloat(retenue.amount) || 0;
                                            }
                                        }
                                    } else {
                                        // Rubrique retnue
                                        value = employee[rubrique.id] !== undefined ? parseFloat(employee[rubrique.id]) : 0;
                                        if (isNaN(value)) value = 0;
                                    }

                                    var cellStyle = isTotal ? bgClass : '';
                                    tableHTML += '<td class="border border-dark" align="right"' + (cellStyle ? ' style="' + cellStyle + '"' : '') + '>';
                                    tableHTML += isTotal ? '<strong>' + value.toLocaleString('fr-FR') + '</strong>' : value.toLocaleString('fr-FR');
                                    tableHTML += '</td>';
                                }

                                // Colonne Total
                                if (pageIndex === totalPages - 1) {
                                    var displayTotal = annualTotal > 0 ? annualTotal.toLocaleString('fr-FR', { maximumFractionDigits: 0 }) : '';
                                    tableHTML += '<td class="border border-dark" align="right"' + (bgClass ? ' style="' + bgClass + '"' : '') + '>';
                                    tableHTML += isTotal ? '<strong>' + displayTotal + '</strong>' : displayTotal;
                                    tableHTML += '</td>';
                                }

                                tableHTML += '</tr>';
                            });

                            // Nombre de salariés
                            tableHTML += '<tr>';
                            tableHTML += '<td class="border border-dark" colspan="2" align="center">Nombre de salariés</td>';

                            for (var i = startIndex; i < endIndex; i++) {
                                tableHTML += '<td class="border border-dark text-center"></td>';
                            }
                            if (pageIndex === totalPages - 1) {
                                tableHTML += '<td class="border border-dark" align="right">' + (data.length) + '</td>';
                            }
                            tableHTML += '</tr>';

                            tableHTML += '</tbody>';
                            tableHTML += '</table>';
                            tableHTML += '</div>';
                        }

                        if (totalPages > 1) {
                            // Pagination
                            paginationHTML += '<div class="pagination-container mt-3">';
                            paginationHTML += '<ul class="pagination annuel">';
                            for (var i = 0; i < totalPages; i++) {
                                var activeClass = i === 0 ? 'active' : '';
                                paginationHTML += '<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + (i + 1) + '">' + (i + 1) + '</a></li>';
                            }
                            paginationHTML += '</ul>';
                            paginationHTML += '</div>';
                        }

                        // Afficher le tout
                        document.getElementById('table-container-annuels').innerHTML = tableHTML + paginationHTML;

                        // Gérer la pagination
                        $('.annuel .page-link').on('click', function (e) {
                            e.preventDefault();
                            var pageNumber = $(this).data('page');
                            $('.page-annuel').hide();
                            $('#page-annuel-' + pageNumber).show();
                            $('.annuel .page-item').removeClass('active');
                            $(this).parent().addClass('active');
                        });

                        // Afficher seulement la première page au départ
                        $('.page-annuel').hide();
                        $('#page-annuel-1').show();

                        $('#exportPdfAnnuel').on('click', function () {
                            // Obtenir tous les éléments de page
                            var pages = document.querySelectorAll('.page-annuel');

                            // Cacher la pagination et le bouton d'export pour l'impression
                            $('.pagination-container').hide();
                            $('#exportPdfAnnuel').hide();

                            // Afficher toutes les pages pour l'export PDF
                            //$('.page-annuel').show();

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
                                var processPage = function (index) {
                                    if (index >= totalPages) {
                                        // Toutes les pages ont été traitées, sauvegarder le PDF
                                        doc.save('Livre_de_paie_annuel_' + company_name + '_' + exerciceName + '.pdf');

                                        // Restaurer l'affichage après l'exportation
                                        $('.page-annuel').hide();
                                        $('#page-annuel-1').show();
                                        $('.pagination-container').show();
                                        $('#exportPdfAnnuel').show();
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
                                    doc.text("Livre de paie A N N U E L", doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

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
                                        headerCells.forEach(function (cell) {
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
                                    rows.forEach(function (row) {
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

                                        cells.forEach(function (cell, cellIndex) {
                                            var cellContent = cell.innerText.trim();

                                            // Nettoyer les valeurs numériques (supprimer les slashs et espaces)
                                            if (cellIndex >= 2) {
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
                                                } else {
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
                                        didParseCell: function (data) {
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
                                    setTimeout(function () {
                                        processPage(index + 1);
                                    }, 100);
                                };

                                // Démarrer le traitement avec la première page
                                processPage(0);
                            }
                        });

                        $('#exportExelAnnuel').on('click', function () {
                            // Get current page element
                            var pages = document.querySelectorAll('.page-annuel');
                            var company_name = document.getElementById('company_name').value;

                            // Hide pagination and export button for printing
                            $('.pagination-container').hide();
                            $('#exportExelAnnuel').hide();

                            // Create new workbook
                            var wb = XLSX.utils.book_new();

                            // Process each page
                            for (var pageIndex = 0; pageIndex < pages.length; pageIndex++) {
                                var currentPage = pages[pageIndex];
                                var pageNumber = pageIndex + 1;

                                // Get header information
                                var dateDuJour = new Date().toLocaleDateString('fr-FR');
                                var heureActuelle = new Date().toLocaleTimeString('fr-FR');
                                var periode = "Période : du " + dateDebut.toLocaleDateString() + " au " + dateFin.toLocaleDateString();
                                var companyName = document.getElementById('company_name').value;

                                // Add header data
                                var headerData = [
                                    ["Date du jour : " + dateDuJour],
                                    ["Heure : " + heureActuelle],
                                    ["Livre de paie A N N U E L"],
                                    [periode],
                                    ["Page : " + pageNumber],
                                    ["Société : " + companyName],
                                    [] // Empty row for spacing
                                ];

                                // Get table element
                                var tableElement = currentPage.querySelector('table');

                                // Extract table headers
                                var headers = [];
                                var headerCells = tableElement.querySelectorAll('thead th');
                                headerCells.forEach(function (cell) {
                                    headers.push(cell.textContent.trim());
                                });

                                // Add headers to data
                                headerData.push(headers);

                                // Extract table body data
                                var rows = tableElement.querySelectorAll('tbody tr');
                                rows.forEach(function (row) {
                                    var rowData = [];
                                    var cells = row.querySelectorAll('td');
                                    cells.forEach(function (cell) {
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
                            XLSX.writeFile(wb, 'Livre_de_paie_annuel_' + company_name + '_' + exerciceName + '.xlsx');

                            // Restore display
                            $('.pagination-container').show();
                            $('#exportExelAnnuel').show();
                        });

                    } else {
                        // Si aucune donnée n'est disponible
                        var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible pour cette année") }}</div>';
                        document.getElementById('table-container-annuels').innerHTML = tableHTML;
                    }
                },
                error: function (data) {
                    console.error("Erreur lors de la récupération des données de la paylist.");
                    document.getElementById('table-container-annuels').innerHTML =
                        '<div class="alert alert-danger text-center">Erreur lors de la récupération des données</div>';
                }
            });
        }

        // Initialiser la fonction au chargement de la page
        $(document).ready(function () {
            callbacklivrepaieAnnuel();

            // Si vous avez un champ d'année qui peut changer
            $("#exercice").on('change', function () {
                callbacklivrepaieAnnuel();
            });
        });
    </script>
@endpush