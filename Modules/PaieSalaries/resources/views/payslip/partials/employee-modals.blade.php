{{-- Fenêtres par salarié (jours travaillés, éléments, aperçu du bulletin) : partagées par « Calcul salaire » et « Paie du mois ». Nécessite $periode. --}}
        <!-- Modification du nombre de jour travaillés -->
        <div class="modal fade" id="showDaysWorkModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card-header modal-header">
                        <h5 class="modal-title mb-3">Modifier le nombre de jours travaillés</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateDaysWork" method="POST">
                        @csrf
                        <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                        <div id="modalBodyContentUpdate">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour ajouter des éléments -->
        <div class="modal fade" id="addElementsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card-header modal-header">
                        <h5 class="modal-title mb-3">Ajouter des éléments de paie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('company.paiesalaries.allowance.store') }}" id="addElementsForm" method="POST">
                        @csrf
                        <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                        <div class="modal-body" id="modalBodyContent">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour afficher des éléments -->
        <div class="modal fade" id="showElementsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card-header modal-header">
                        <h5 class="modal-title mb-3">Historique des éléments de paie </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBodyContentShow">
                        <!-- Le contenu sera chargé dynamiquement via AJAX -->
                        <div class="text-center my-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour modifier des éléments -->
        <div class="modal fade" id="editElementsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card-header modal-header">
                        <h5 class="modal-title mb-3">Modifier des éléments de paie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editElementsForm" method="POST">
                        @csrf
                        <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                        <div class="modal-body" id="modalBodyContentEdit">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour afficher le bulletin -->
        <div class="modal fade" id="showBulletinModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card-header modal-header">
                        <h5 class="modal-title mb-3">Afficher le bulletin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBodyContentShowBulletin">
                        <!-- Le contenu sera chargé dynamiquement via AJAX -->
                        <div class="text-center my-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Gérer l'ouverture du modal pour le nombre de jour travaillés 
            $('.btn-update-days').on('click', function () {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#showDaysWorkModal .modal-title').text('Modifier le nombre de jours travaillés pour ' + employeeName);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.show", [":id", ":periode_id"]) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContentUpdate').html(response);
                        $('#showDaysWorkModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        $('#modalBodyContentUpdate').html('<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>');
                    }
                });
            });

            // Gérer l'ouverture du modal d'ajout d'éléments
            $('.btn-add-elements').on('click', function () {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#addElementsModal .modal-title').text('Ajouter des éléments pour ' + employeeName);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.create", [":id", ":periode_id"]) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContent').html(response);
                    },
                    error: function () {
                        $('#modalBodyContent').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal d'affichage d'éléments
            $('.btn-show-elements').on('click', function () {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#showElementsModal .modal-title').text('Historique des éléments de paie - ' + employeeName);

                // Charger le formulaire via AJAX 
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.show", [":id", ":periode_id"]) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContentShow').html(response);
                    },
                    error: function () {
                        $('#modalBodyContentShow').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal de modification des éléments
            $('.btn-edit-elements').on('click', function () {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#editElementsModal .modal-title').text('Modifier des éléments pour ' + employeeName);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.edit", [":id", ":periode_id"]) }}'
                        .replace(':id', encodeURIComponent(employeeId))
                        .replace(':periode_id', encodeURIComponent(periodeId)),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContentEdit').html(response);
                    },
                    error: function () {
                        $('#modalBodyContentEdit').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal d'aperçu du bulletins
            $('.btn-aperçu-bulletin').on('click', function () {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var exerciceId = $(this).data('exercice-id');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#showBulletinModal .modal-title').text('Aperçu du bulletin - ' + employeeName);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.preview-bulletin", [":id", ":exercice_id", ":periode_id"]) }}'
                        .replace(':id', encodeURIComponent(employeeId))
                        .replace(':exercice_id', encodeURIComponent(exerciceId))
                        .replace(':periode_id', encodeURIComponent(periodeId)),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContentShowBulletin').html(response);
                    },
                    error: function () {
                        $('#modalBodyContentShowBulletin').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });
        });
    </script>
@endpush
