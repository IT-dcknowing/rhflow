@extends('layouts.app')

@section('title', 'Gestion des Bulletins de Paie')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Gestion des Bulletins de Paie</h4>
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

        <!-- Cartes de statistiques -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Total bulletins</span>
                                <h3 class="card-title mb-0">{{ number_format($totalBulletins, 0, ',', ' ') }}</h3>
                                <div class="d-flex align-items-center mt-2">
                                    <small class="text-success me-1">
                                        <i class="fas fa-arrow-up"></i> 12%
                                    </small>
                                    <small class="text-muted">vs mois dernier</small>
                                </div>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-file-invoice stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Bulletins validés</span>
                                <h3 class="card-title mb-0">{{ number_format($bulletinsValides, 0, ',', ' ') }}</h3>
                                <div class="progress mx-auto" style="width: 80%; height: 6px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $totalBulletins > 0 ? round(($bulletinsValides / $totalBulletins * 100), 1) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">En cours</span>
                                <h3 class="card-title mb-0">{{ number_format($bulletinsEnCours, 0, ',', ' ') }}</h3>
                                <div class="progress mx-auto" style="width: 80%; height: 6px;">
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $totalBulletins > 0 ? round(($bulletinsEnCours / $totalBulletins * 100), 1) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Masse salariale</span>
                                <h3 class="card-title mb-0">{{ number_format($masseSalariale, 0, ',', ' ') }} FCFA</h3>
                                <div class="d-flex align-items-center mt-2">
                                    <small class="text-info me-1">
                                        <i class="fas fa-arrow-up"></i> 8.2%
                                    </small>
                                    <small class="text-muted">vs mois dernier</small>
                                </div>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-coins stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des bulletins -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-5">
                    <h5 class="mb-0">Liste des Bulletins de Paie</h5>
                </div>
                <div class="col-md-7">
                    <div class="d-flex justify-content-end">
                        <div class="btn-box me-2">
                            <select id="exercice" class="form-select select2" name="exercice" tabindex="-1"
                                aria-hidden="true">
                                @foreach($exercices as $exercice)
                                    <option value="{{ $exercice->id }}">
                                        {{ $exercice->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="btn-box me-2">
                            <select name="periodes" id="periodes" class="form-select">

                            </select>
                        </div>
                        <button class="btn btn-sm btn-outline-success" id="bullAll">
                            <i class="fas fa-eye me-1"></i>Voir tous les bulletins
                        </button>
                        <button class="btn btn-sm btn-primary ms-2" id="btnDownloadGlobal">
                            <i class="fas fa-download me-1"></i>Télécharger Global (PDF)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="bulletinsTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Employés</th>
                                <th>Période</th>
                                <th>Salaire de base</th>
                                <th>Salaire brut</th>
                                <th>Total retenues</th>
                                <th>Net à payer</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paySlips as $paySlip)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input checkbox-bulletin" type="checkbox"
                                                value="{{ $paySlip->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($paySlip->employee->name ?? 'Inconnu', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $paySlip->employee->name ?? 'Employé inconnu' }}</h6>
                                                <small class="text-muted">{{ $paySlip->emploi ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('M Y') }}</small>
                                    </td>
                                    <td align="right">
                                        <span class="fw-semibold">{{ number_format($paySlip->basic_salary, 0, ',', ' ') }}
                                            FCFA</span>
                                    </td>
                                    <td align="right">
                                        <span class="fw-semibold">{{ number_format($paySlip->salary_brut, 0, ',', ' ') }}
                                            FCFA</span>
                                    </td>
                                    <td align="right">
                                        <span
                                            class="fw-semibold text-danger">{{ number_format($paySlip->total_retenue, 0, ',', ' ') }}
                                            FCFA</span>
                                    </td>
                                    <td align="right">
                                        <span
                                            class="fw-semibold text-success">{{ number_format($paySlip->net_payble, 0, ',', ' ') }}
                                            FCFA</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $paySlip->status == 1 ? 'success' : ($paySlip->status == 0 ? 'warning' : 'secondary') }}">
                                            {{ $paySlip->status == 1 ? 'Validé' : ($paySlip->status == 0 ? 'Généré' : 'Brouillon') }}
                                        </span>
                                    </td>
                                    <td align="center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('company.declarations.resume.show', $paySlip->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Voir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('company.declarations.resume.edit', $paySlip->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        onclick="deleteBulletin({{ $paySlip->id }})">
                                                        <i class="fas fa-trash me-2"></i>Supprimer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-8">
                                        <div class="text-center">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun bulletin trouvé</h5>
                                            <p class="text-muted">Commencez par générer des bulletins de paie</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($paySlips->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $paySlips->firstItem() }} à {{ $paySlips->lastItem() }} sur {{ $paySlips->total() }}
                            bulletins
                        </div>
                        <div>
                            {{ $paySlips->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions groupées -->
            <div class="card-footer bg-light" id="bulkActions" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">
                            <span id="selectedCount">0</span> bulletin(s) sélectionné(s)
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="bulkValidate()">
                            <i class="fas fa-check me-1"></i>Valider
                        </button>
                        <button class="btn btn-outline-info" onclick="bulkExport()">
                            <i class="fas fa-download me-1"></i>Exporter
                        </button>
                        <button class="btn btn-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de tous les bulletins -->
    <div class="modal fade" id="bulletinsAllModal" tabindex="-1" aria-labelledby="bulletinsAllModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulletinsAllModalLabel">Tous les bulletins de paie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bulletinsAllContent">
                    <!-- Le contenu sera chargé ici -->
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/bulletin-queue.js') }}?v={{ filemtime(public_path('js/bulletin-queue.js')) }}"></script>
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
                            callbacklivrepaie();
                        }
                    },
                    error: function () {
                        console.error('Erreur lors du chargement des périodes');
                    }
                });
            } else {
                var tableHTML = '<div class="alert alert-info text-center">{{ __("Aucune donnée disponible") }}</div>';
                document.getElementById('table-container').innerHTML = tableHTML;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Gestion de la sélection
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.checkbox-bulletin');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            // Écouteur d'événement pour le bouton "Voir tous les bulletins"
            document.getElementById('bullAll').addEventListener('click', function () {
                loadBulletinsAll();
            });

            // Écouteur d'événement pour le bouton "Télécharger Global"
            document.getElementById('btnDownloadGlobal').addEventListener('click', function () {
                var periodeId = $("#periodes").val();

                if (!periodeId) {
                    alert('Veuillez sélectionner une période');
                    return;
                }

                var filename = 'bulletins_global_' + $('#periodes option:selected').text().replace(/\s+/g, '_');

                if (typeof generateBulletinsQueueType1 === 'function') {
                    generateBulletinsQueueType1(periodeId, filename);
                } else {
                    console.error('La fonction generateBulletinsQueueType1 n\'est pas chargée.');
                    alert('Erreur: Le service de téléchargement n\'est pas disponible.');
                }
            });

            // Sélection/Désélection tout
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Mise à jour des actions groupées
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.checkbox-bulletin:checked');
                const hasChecked = checkedBoxes.length > 0;

                bulkActions.style.display = hasChecked ? 'block' : 'none';
                selectedCount.textContent = checkedBoxes.length;

                // Mettre à jour l'état du select all
                if (checkedBoxes.length === checkboxes.length) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else if (checkedBoxes.length > 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }

            // Recherche
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#bulletinsTable tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });

        // Fonction pour charger tous les bulletins dans le modal
        function loadBulletinsAll() {
            var periodeId = $("#periodes").val();

            if (!periodeId) {
                alert('Veuillez sélectionner une période');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('bulletinsAllModal'));
            const contentDiv = document.getElementById('bulletinsAllContent');

            // Afficher un indicateur de chargement
            contentDiv.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>';

            // Ouvrir le modal
            modal.show();

            // Charger le contenu via AJAX avec l'ID de la période
            fetch('{{ route("company.declarations.resume.bulletins-all", ":periodeId") }}'.replace(':periodeId', periodeId))
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;

                    // Réinitialiser les scripts pour les bulletins
                    initializeBulletinsScripts();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des bulletins:', error);
                    contentDiv.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des bulletins. Veuillez réessayer.</div>';
                });
        }

        // Fonction pour initialiser les scripts des bulletins après chargement
        function initializeBulletinsScripts() {
            // Réinitialiser les écouteurs d'événements pour les onglets
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function (e) {
                    console.log('Onglet activé:', e.target);
                });
            });

            // Réinitialiser les fonctions de téléchargement si elles existent
            if (typeof downloadBulletin === 'function') {
                console.log('Fonctions de téléchargement initialisées');
            }

            // Réinitialiser les fonctions d'affichage/masquage de logo
            if (typeof afficheLogSign === 'function') {
                console.log('Fonctions de logo initialisées');
            }
        }

        // Fonctions utilitaires
        function downloadBulletin(id) {
            window.open(`/declarations/resume/${id}/download`, '_blank');
        }

        function deleteBulletin(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce bulletin ?')) {
                $.ajax({
                    url: '{{ route("company.declarations.resume.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || 'Bulletin supprimé avec succès');
                            // Recharger les données du tableau
                            callbacklivrepaie();
                        } else {
                            toastr.error(response.message || 'Erreur lors de la suppression');
                        }
                    },
                    error: function (xhr) {
                        console.error('Erreur:', xhr);
                        toastr.error('Erreur lors de la suppression');
                    }
                });
            }
        }

        function bulkValidate() {
            const selected = document.querySelectorAll('.checkbox-bulletin:checked');
            if (confirm(`Valider ${selected.length} bulletin(s) ?`)) {
                // Implémenter la validation groupée
                console.log('Validation groupée:', selected.length);
            }
        }

        function bulkExport() {
            const selected = document.querySelectorAll('.checkbox-bulletin:checked');
            if (confirm(`Exporter ${selected.length} bulletin(s) ?`)) {
                // Implémenter l'export groupé
                console.log('Export groupé:', selected.length);
            }
        }

        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.checkbox-bulletin:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Veuillez sélectionner au moins un bulletin');
                return;
            }

            if (confirm(`Supprimer ${ids.length} bulletin(s) ? Cette action est irréversible.`)) {
                $.ajax({
                    url: '{{ route("company.declarations.resume.bulk-destroy") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: ids
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || 'Bulletins supprimés avec succès');
                            // Réinitialiser le select all et masquer les actions groupées
                            document.getElementById('selectAll').checked = false;
                            document.getElementById('bulkActions').style.display = 'none';
                            // Recharger les données du tableau
                            callbacklivrepaie();
                        } else {
                            toastr.error(response.message || 'Erreur lors de la suppression');
                        }
                    },
                    error: function (xhr) {
                        console.error('Erreur:', xhr);
                        toastr.error('Erreur lors de la suppression groupée');
                    }
                });
            }
        }

        // Charger les bulletins en fonction de la période sélectionnée
        function callbacklivrepaie() {
            var periodeId = $("#periodes").val();
            var exerciceId = $("#exercice").val();

            if (!periodeId || !exerciceId) {
                return;
            }

            // Afficher un indicateur de chargement
            var tableBody = $('#bulletinsTable tbody');
            tableBody.html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></td></tr>');

            $.ajax({
                url: '{{ route("company.declarations.get_bulletins") }}',
                type: 'GET',
                data: {
                    periode: periodeId,
                    exercice: exerciceId
                },
                success: function (data) {
                    updateTable(data);
                    updateStatistics(data.stats);
                },
                error: function (xhr) {
                    console.error('Erreur lors du chargement des bulletins:', xhr);
                    tableBody.html('<tr><td colspan="9" class="text-center py-4"><div class="alert alert-danger">Erreur lors du chargement des données. Veuillez réessayer.</div></td></tr>');
                }
            });
        }

        // Mettre à jour le tableau des bulletins
        function updateTable(data) {
            var tableBody = $('#bulletinsTable tbody');

            if (data.bulletins && data.bulletins.length > 0) {
                var html = '';
                $.each(data.bulletins, function (index, bulletin) {
                    html += '<tr>';
                    html += '<td><div class="form-check"><input class="form-check-input checkbox-bulletin" type="checkbox" value="' + bulletin.id + '"></div></td>';
                    html += '<td>';
                    html += '<div class="d-flex align-items-center">';
                    html += '<div class="avatar avatar-sm me-2">';
                    html += '<span class="avatar-initial rounded-circle bg-label-primary">' + (bulletin.employee_name ? bulletin.employee_name.charAt(0).toUpperCase() : 'E') + '</span>';
                    html += '</div>';
                    html += '<div>';
                    html += '<h6 class="mb-0">' + (bulletin.employee_name || 'Employé inconnu') + '</h6>';
                    html += '<small class="text-muted">' + (bulletin.emploi || 'N/A') + '</small>';
                    html += '</div>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td><small>' + (bulletin.salary_month_formatted || '') + '</small></td>';
                    html += '<td align="right"><span class="fw-semibold">' + formatNumber(bulletin.basic_salary) + ' FCFA</span></td>';
                    html += '<td align="right"><span class="fw-semibold">' + formatNumber(bulletin.salary_brut) + ' FCFA</span></td>';
                    html += '<td align="right"><span class="fw-semibold text-danger">' + formatNumber(bulletin.total_retenue) + ' FCFA</span></td>';
                    html += '<td align="right"><span class="fw-semibold text-success">' + formatNumber(bulletin.net_payble) + ' FCFA</span></td>';
                    html += '<td>';
                    var statusClass = bulletin.status == 1 ? 'success' : (bulletin.status == 0 ? 'warning' : 'secondary');
                    var statusText = bulletin.status == 1 ? 'Validé' : (bulletin.status == 0 ? 'Généré' : 'Brouillon');
                    html += '<span class="badge bg-' + statusClass + '">' + statusText + '</span>';
                    html += '</td>';
                    html += '<td align="center">';
                    html += '<div class="dropdown">';
                    html += '<button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="dropdown">';
                    html += '<i class="fas fa-ellipsis-v"></i>';
                    html += '</button>';
                    html += '<ul class="dropdown-menu">';
                    html += '<li><a class="dropdown-item" href="resume/' + bulletin.id + '"><i class="fas fa-eye me-2"></i>Voir</a></li>';
                    html += '<li><a class="dropdown-item" href="resume/' + bulletin.id + '/edit"><i class="fas fa-edit me-2"></i>Modifier</a></li>';
                    html += '<li><hr class="dropdown-divider"></li>';
                    html += '<li><a class="dropdown-item text-danger" href="#" onclick="deleteBulletin(' + bulletin.id + ')"><i class="fas fa-trash me-2"></i>Supprimer</a></li>';
                    html += '</ul>';
                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                });
                tableBody.html(html);
            } else {
                tableBody.html('<tr><td colspan="9" class="text-center py-8"><div class="text-center"><i class="fas fa-file-invoice fa-3x text-muted mb-3"></i><h5 class="text-muted">Aucun bulletin trouvé</h5><p class="text-muted">Aucun bulletin de paie disponible pour cette période</p></div></td></tr>');
            }

            // Réinitialiser les écouteurs d'événements
            reinitializeEventListeners();
        }

        // Mettre à jour les statistiques
        function updateStatistics(stats) {
            if (stats) {
                $('.stat-card h3').eq(0).text(formatNumber(stats.total || 0));
                $('.stat-card h3').eq(1).text(formatNumber(stats.valides || 0));
                $('.stat-card h3').eq(2).text(formatNumber(stats.encours || 0));
                $('.stat-card h3').eq(3).text(formatNumber(stats.masse_salariale || 0) + ' FCFA');

                // Mettre à jour les barres de progression
                var total = stats.total || 0;
                if (total > 0) {
                    $('.progress-bar').eq(0).css('width', round((stats.valides / total * 100), 1) + '%');
                    $('.progress-bar').eq(1).css('width', round((stats.encours / total * 100), 1) + '%');
                }
            }
        }

        // Réinitialiser les écouteurs d'événements après mise à jour du tableau
        function reinitializeEventListeners() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.checkbox-bulletin');

            // Mettre à jour les écouteurs pour les nouvelles checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.removeEventListener('change', updateBulkActions);
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Réinitialiser l'état du select all
            selectAll.checked = false;
            selectAll.indeterminate = false;
            updateBulkActions();
        }

        // Fonction utilitaire pour formater les nombres
        function formatNumber(num) {
            if (num === null || num === undefined) return '0';
            return new Intl.NumberFormat('fr-FR').format(num);
        }

        // Initialiser la fonction au chargement de la page
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

            // Charger les données quand on change de période
            $("#periodes").on('change', function () {
                callbacklivrepaie();
            });

            // Charger les bulletins pour la période actuelle au chargement
            if ($("#periodes").val()) {
                callbacklivrepaie();
            }
        });

        // Gérer l'affichage du logo
        function afficheLogSign() {
            var logos = document.querySelectorAll(".logoBull1");
            var signatures = document.querySelectorAll(".signatureBull1");

            logos.forEach(function (logo) {
                logo.classList.remove('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.remove('d-none');
            });

            console.log('Logo et signature affichés');
        }

        function cacheLogSign() {
            var logos = document.querySelectorAll(".logoBull1");
            var signatures = document.querySelectorAll(".signatureBull1");

            logos.forEach(function (logo) {
                logo.classList.add('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.add('d-none');
            });

            console.log('Logo et signature cachés');
        }

        function afficheLogSign2() {
            var logos = document.querySelectorAll(".logoBull2");
            var signatures = document.querySelectorAll(".signatureBull2");

            logos.forEach(function (logo) {
                logo.classList.remove('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.remove('d-none');
            });

            console.log('Logo et signature affichés');
        }

        function cacheLogSign2() {
            var logos = document.querySelectorAll(".logoBull2");
            var signatures = document.querySelectorAll(".signatureBull2");

            logos.forEach(function (logo) {
                logo.classList.add('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.add('d-none');
            });

            console.log('Logo et signature cachés');
        }

        function afficheLogSign3() {
            var logos = document.querySelectorAll(".logoBull3");
            var signatures = document.querySelectorAll(".signatureBull3");

            logos.forEach(function (logo) {
                logo.classList.remove('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.remove('d-none');
            });

            console.log('Logo et signature affichés');
        }

        function cacheLogSign3() {
            var logos = document.querySelectorAll(".logoBull3");
            var signatures = document.querySelectorAll(".signatureBull3");

            logos.forEach(function (logo) {
                logo.classList.add('d-none');
            });

            signatures.forEach(function (signature) {
                signature.classList.add('d-none');
            });

            console.log('Logo et signature cachés');
        }

        /**
         * Déterminer automatiquement si utiliser client-side ou queue
         * Basé sur le nombre de bulletins
         */
        function downloadBulletinsAuto(bulletinType, periodeId, filename) {
            const bulletinCount = document.querySelectorAll('.pagebulletin' + bulletinType).length;

            console.log(`Bulletins type ${bulletinType}: ${bulletinCount}`);

            // Seuil : utiliser queue si > 50 bulletins
            if (bulletinCount > 50) {
                console.log('Utilisation de la Queue pour gros volume');
                generateBulletinsQueueType(bulletinType, periodeId, filename);
            } else {
                console.log('Utilisation du client-side pour petit volume');
                downloadAllBulletinsType(bulletinType, filename);
            }
        }

        /**
         * Wrapper pour les fonctions de queue
         */
        function generateBulletinsQueueType(bulletinType, periodeId, filename) {
            switch (bulletinType) {
                case 1:
                    generateBulletinsQueueType1(periodeId, filename);
                    break;
                case 2:
                    generateBulletinsQueueType2(periodeId, filename);
                    break;
                case 3:
                    generateBulletinsQueueType3(periodeId, filename);
                    break;
            }
        }

        /**
         * Wrapper pour les fonctions client-side
         */
        function downloadAllBulletinsType(bulletinType, filename) {
            switch (bulletinType) {
                case 1:
                    downloadAllBulletinsType1('mainDownloadButton', filename);
                    break;
                case 2:
                    downloadAllBulletinsType2('mainDownloadButton2', filename);
                    break;
                case 3:
                    downloadAllBulletinsType3('mainDownloadButton3', filename);
                    break;
            }
        }

        // Fonction pour télécharger tous les bulletins de type 1
        async function downloadAllBulletinsType1(btnId, filename) {
            const button = document.getElementById(btnId);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="fa fa-spinner fa-spin me-2"></span> Génération...';

            var bulletins = $('.pagebulletin1');
            var totalPages = bulletins.length;

            try {
                // Charger les bibliothèques si elles ne sont pas déjà disponibles
                if (typeof jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }

                const { jsPDF } = window.jspdf;
                let processedCount = 0;

                // Traiter chaque bulletin un par un
                for (let i = 0; i < totalPages; i++) {
                    const bulletinId = 'bull-' + (i + 1);
                    const bulletinElement = document.getElementById(bulletinId);

                    if (!bulletinElement) {
                        console.error('Bulletin non trouvé:', bulletinId);
                        continue;
                    }

                    // Préparer le bulletin pour le rendu
                    const originalDisplay = bulletinElement.style.display;
                    const originalVisibility = bulletinElement.style.visibility;
                    bulletinElement.style.display = 'block';
                    bulletinElement.style.visibility = 'visible';

                    try {
                        // Créer un nouveau document pour CHAQUE bulletin
                        const doc = new jsPDF('p', 'mm', 'a4');

                        // Générer le PDF pour ce bulletin
                        await doc.html(bulletinElement, {
                            callback: function (doc) {
                                // Supprimer les pages vides à la fin
                                const pageCount = doc.internal.getNumberOfPages();
                                for (let i = pageCount; i > 1; i--) {
                                    doc.deletePage(i);
                                }
                            },
                            startY: 15,
                            margin: [15, 5, 5, 5],
                            width: 190,
                            windowWidth: bulletinElement.scrollWidth || bulletinElement.clientWidth,
                            x: 0,
                            y: 0,
                            html2canvas: {
                                scale: 0.19,
                                useCORS: true,
                                allowTaint: true,
                                logging: false
                            }
                        });

                        // Stocker le PDF dans un array
                        if (processedCount === 0) {
                            // Premier bulletin : on garde ce doc comme document principal
                            window.mainDoc = doc;
                        } else {
                            // Bulletins suivants : on ajoute leurs pages au document principal
                            const pages = doc.internal.pages;
                            // Ignorer la première page vide (index 0)
                            for (let p = 1; p < pages.length; p++) {
                                window.mainDoc.addPage();
                                const pageContent = pages[p];
                                window.mainDoc.internal.pages[window.mainDoc.internal.pages.length - 1] = pageContent;
                            }
                        }

                        processedCount++;

                    } catch (err) {
                        console.error(`Erreur lors du traitement du bulletin ${i + 1}:`, err);
                    } finally {
                        // Restaurer l'état original du bulletin
                        bulletinElement.style.display = originalDisplay;
                        bulletinElement.style.visibility = originalVisibility;
                    }
                }

                // Vérifier si des bulletins ont été traités
                if (processedCount === 0) {
                    throw new Error('Aucun bulletin n\'a pu être généré');
                }

                // Sauvegarder le PDF final
                window.mainDoc.save(`${filename}.pdf`);
                delete window.mainDoc; // Nettoyer

            } catch (error) {
                console.error('Erreur lors de la génération du PDF de type 1 :', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la génération du PDF de type 1'
                });
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Fonction pour télécharger tous les bulletins de type 2
        async function downloadAllBulletinsType2(btnId, filename) {
            const button = document.getElementById(btnId);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="fa fa-spinner fa-spin me-2"></span> Génération...';

            var bulletins = $('.pagebulletin2');
            var totalPages = bulletins.length;

            try {
                // Charger les bibliothèques si elles ne sont pas déjà disponibles
                if (typeof jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }

                const { jsPDF } = window.jspdf;
                let processedCount = 0;

                // Traiter chaque bulletin un par un
                for (let i = 0; i < totalPages; i++) {
                    const bulletinId = 'bull2-' + (i + 1);
                    const bulletinElement = document.getElementById(bulletinId);

                    if (!bulletinElement) {
                        console.error('Bulletin non trouvé:', bulletinId);
                        continue;
                    }

                    // Préparer le bulletin pour le rendu
                    const originalDisplay = bulletinElement.style.display;
                    const originalVisibility = bulletinElement.style.visibility;
                    bulletinElement.style.display = 'block';
                    bulletinElement.style.visibility = 'visible';

                    try {
                        // Créer un nouveau document pour CHAQUE bulletin
                        const doc = new jsPDF('p', 'mm', 'a4');

                        // Générer le PDF pour ce bulletin
                        await doc.html(bulletinElement, {
                            callback: function (doc) {
                                // Supprimer les pages vides à la fin
                                const pageCount = doc.internal.getNumberOfPages();
                                for (let i = pageCount; i > 1; i--) {
                                    doc.deletePage(i);
                                }
                            },
                            startY: 15,
                            margin: [15, 5, 5, 5],
                            width: 190,
                            windowWidth: bulletinElement.scrollWidth || bulletinElement.clientWidth,
                            x: 0,
                            y: 0,
                            html2canvas: {
                                scale: 0.19,
                                useCORS: true,
                                allowTaint: true,
                                logging: false
                            }
                        });

                        // Stocker le PDF dans un array
                        if (processedCount === 0) {
                            // Premier bulletin : on garde ce doc comme document principal
                            window.mainDoc = doc;
                        } else {
                            // Bulletins suivants : on ajoute leurs pages au document principal
                            const pages = doc.internal.pages;
                            // Ignorer la première page vide (index 0)
                            for (let p = 1; p < pages.length; p++) {
                                window.mainDoc.addPage();
                                const pageContent = pages[p];
                                window.mainDoc.internal.pages[window.mainDoc.internal.pages.length - 1] = pageContent;
                            }
                        }

                        processedCount++;

                    } catch (err) {
                        console.error(`Erreur lors du traitement du bulletin ${i + 1}:`, err);
                    } finally {
                        // Restaurer l'état original du bulletin
                        bulletinElement.style.display = originalDisplay;
                        bulletinElement.style.visibility = originalVisibility;
                    }
                }

                // Vérifier si des bulletins ont été traités
                if (processedCount === 0) {
                    throw new Error('Aucun bulletin n\'a pu être généré');
                }

                // Sauvegarder le PDF final
                window.mainDoc.save(`${filename}.pdf`);
                delete window.mainDoc; // Nettoyer

            } catch (error) {
                console.error('Erreur lors de la génération du PDF de type 2 :', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la génération du PDF de type 2'
                });
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Fonction pour télécharger tous les bulletins de type 3
        async function downloadAllBulletinsType3(btnId, filename) {
            const button = document.getElementById(btnId);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="fa fa-spinner fa-spin me-2"></span> Génération...';

            var bulletins = $('.pagebulletin3');
            var totalPages = bulletins.length;

            try {
                // Charger les bibliothèques si elles ne sont pas déjà disponibles
                if (typeof jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }

                const { jsPDF } = window.jspdf;
                let processedCount = 0;

                // Traiter chaque bulletin un par un
                for (let i = 0; i < totalPages; i++) {
                    const bulletinId = 'bull3-' + (i + 1);
                    const bulletinElement = document.getElementById(bulletinId);

                    if (!bulletinElement) {
                        console.error('Bulletin non trouvé:', bulletinId);
                        continue;
                    }

                    // Préparer le bulletin pour le rendu
                    const originalDisplay = bulletinElement.style.display;
                    const originalVisibility = bulletinElement.style.visibility;
                    bulletinElement.style.display = 'block';
                    bulletinElement.style.visibility = 'visible';

                    try {
                        // Créer un nouveau document pour CHAQUE bulletin
                        const doc = new jsPDF('p', 'mm', 'a4');

                        // Générer le PDF pour ce bulletin
                        await doc.html(bulletinElement, {
                            callback: function (doc) {
                                // Supprimer les pages vides à la fin
                                const pageCount = doc.internal.getNumberOfPages();
                                for (let i = pageCount; i > 1; i--) {
                                    doc.deletePage(i);
                                }
                            },
                            startY: 15,
                            margin: [15, 5, 5, 5],
                            width: 190,
                            windowWidth: bulletinElement.scrollWidth || bulletinElement.clientWidth,
                            x: 0,
                            y: 0,
                            html2canvas: {
                                scale: 0.19,
                                useCORS: true,
                                allowTaint: true,
                                logging: false
                            }
                        });

                        // Stocker le PDF dans un array
                        if (processedCount === 0) {
                            // Premier bulletin : on garde ce doc comme document principal
                            window.mainDoc = doc;
                        } else {
                            // Bulletins suivants : on ajoute leurs pages au document principal
                            const pages = doc.internal.pages;
                            // Ignorer la première page vide (index 0)
                            for (let p = 1; p < pages.length; p++) {
                                window.mainDoc.addPage();
                                const pageContent = pages[p];
                                window.mainDoc.internal.pages[window.mainDoc.internal.pages.length - 1] = pageContent;
                            }
                        }

                        processedCount++;

                    } catch (err) {
                        console.error(`Erreur lors du traitement du bulletin ${i + 1}:`, err);
                    } finally {
                        // Restaurer l'état original du bulletin
                        bulletinElement.style.display = originalDisplay;
                        bulletinElement.style.visibility = originalVisibility;
                    }
                }

                // Vérifier si des bulletins ont été traités
                if (processedCount === 0) {
                    throw new Error('Aucun bulletin n\'a pu être généré');
                }

                // Sauvegarder le PDF final
                window.mainDoc.save(`${filename}.pdf`);
                delete window.mainDoc; // Nettoyer

            } catch (error) {
                console.error('Erreur lors de la génération du PDF de type 3 :', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la génération du PDF de type 3'
                });
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Fonction utilitaire pour charger un script
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        .stat-card {
            border-left: 4px solid;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .border-left-primary {
            border-left-color: #696cff !important;
        }

        .border-left-success {
            border-left-color: #71dd37 !important;
        }

        .border-left-warning {
            border-left-color: #ffab00 !important;
        }

        .border-left-info {
            border-left-color: #03c3ec !important;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
@endpush