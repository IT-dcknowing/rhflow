@php
    $cpte = 0;
    // Récupérer les noms des éléments déjà sélectionnés par l'entreprise
    $selectedNames = $elements->pluck('name')->toArray();
    foreach ($elementsDefault as $element) {
        if (!in_array($element->name, $selectedNames)) {
            $cpte++;
        }
    }
@endphp
@extends('layouts.app')

@section('title', 'Configuration des éléments du brut')

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/sweetalert2/sweetalert2.css') }}">
    <style>
        .element-card {
            border-left: 4px solid #253e87;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .element-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Configuration des éléments du brut</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Exercices</li>
                            </ol>
                        </nav>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(\Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ \Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex justify-content-end">
                        @if($periode)
                            <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button"
                                class="align-items-center btn btn-info me-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la période
                            </a>
                        @endif
                        @if($elements->isEmpty() && $elementsDefault->isNotEmpty())
                            <form id="saveDefaultsForm" method="POST"
                                action="{{ route('company.paiesalaries.allowance.saveDefaults') }}" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="elements" id="selectedElementsInput" value="">
                                <button type="button" id="selectAllDefaults" class="btn btn-sm btn-outline-primary ">
                                    <i class="fas fa-check-square me-2"></i>Tout sélectionner
                                </button>
                                <button type="button" id="deselectAllDefaults" class="btn btn-sm btn-outline-warning ">
                                    <i class="fas fa-square me-2"></i>Tout désélectionner
                                </button>
                                <button type="submit" id="saveSelectedDefaults" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer les éléments sélectionnés
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#addElementModal">
                                <i class="fas fa-plus me-2"></i>Nouvel élément
                            </button>
                            @if($cpte > 0)
                                <form id="saveDefaultsForm" method="POST"
                                    action="{{ route('company.paiesalaries.allowance.saveDefaults') }}" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="elements" id="selectedElementsInput" value="">
                                    <button type="button" id="selectAllDefaults" class="btn btn-sm btn-outline-primary ">
                                        <i class="fas fa-check-square me-2"></i>Tout sélectionner
                                    </button>
                                    <button type="button" id="deselectAllDefaults" class="btn btn-sm btn-outline-warning ">
                                        <i class="fas fa-square me-2"></i>Tout désélectionner
                                    </button>
                                    <button type="submit" id="saveSelectedDefaults" class="btn btn-sm btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les éléments sélectionnés
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Information</strong>
            <p>Vous pouvez ajouter des éléments de base ou des éléments personnalisés.</p>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="elementsTable">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Code Compta</th>
                                        <th>Libellé</th>
                                        <th>Type</th>
                                        <th>Social</th>
                                        <th>Fiscal</th>
                                        <th>Actions</th>
                                        <th>Sélectionner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($elements as $element)
                                        <tr>
                                            <td>{{ $element->id }}</td>
                                            <td>{{ $element->code_compta }}</td>
                                            <td>{{ $element->name }}</td>
                                            <td>
                                                @if($element->type == 'default')
                                                    <span class="badge bg-label-info">Default</span>
                                                @else
                                                    <span class="badge bg-label-warning">Personnalisée</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-label-secondary">{{ $element->param_social }}</span></td>
                                            <td><span class="badge bg-label-danger">{{ $element->param_fiscal }}</span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-secondary dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item edit-element" href="#"
                                                            data-allowance-id="{{ $element->id }}"
                                                            data-allowance-name="{{ $element->name }}" data-bs-toggle="modal"
                                                            data-bs-target="#editElementsModal">
                                                            <i class="fas fa-edit me-2"></i>Modifier
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <form
                                                            action="{{ route('company.paiesalaries.allowance.destroyOption', $element->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item text-danger delete-element">
                                                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input default-element-checkbox" type="checkbox"
                                                        value="{{ $element->id }}" id="defaultElement{{ $element->id }}" checked
                                                        disabled>
                                                    <label class="form-check-label" for="defaultElement{{ $element->id }}">
                                                        Sélectionner
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @php
                                        // Récupérer les noms des éléments déjà sélectionnés par l'entreprise
                                        $selectedNames = $elements->pluck('name')->toArray();
                                    @endphp
                                    @foreach($elementsDefault as $element)
                                        @if(!in_array($element->name, $selectedNames))
                                            <tr>
                                                <td>{{ $element->id }}</td>
                                                <td>{{ $element->code_compta }}</td>
                                                <td>{{ $element->name }}</td>
                                                <td>
                                                    @if($element->type == 'default')
                                                        <span class="badge bg-label-info">Default</span>
                                                    @else
                                                        <span class="badge bg-label-warning">Personnalisée</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-label-secondary">{{ $element->param_social }}</span></td>
                                                <td><span class="badge bg-label-danger">{{ $element->param_fiscal }}</span></td>
                                                <td>
                                                    <small class="text-muted">Élément par défaut</small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input default-element-checkbox" type="checkbox"
                                                            value="{{ $element->id }}" id="defaultElement{{ $element->id }}">
                                                        <label class="form-check-label" for="defaultElement{{ $element->id }}">
                                                            Sélectionner
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout/Modification -->
    @include('paiesalaries::allowance.modals.element-form')
    <!-- Modal pour modifier des éléments -->
    <div class="modal fade" id="editElementsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Modifier un élément de paie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBodyContentEdit">
                    <!-- Le contenu sera chargé dynamiquement via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(function () {
            // Initialisation de DataTable
            $('#elementsTable').DataTable({
                responsive: true,
                order: [[1, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                },
            });

            // Gestion de la suppression
            $('.delete-element').on('click', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Cette action est irréversible !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Boutons Tout sélectionner / Tout désélectionner
            $('#selectAllDefaults').on('click', function () {
                $('.default-element-checkbox').prop('checked', true);
            });

            $('#deselectAllDefaults').on('click', function () {
                $('.default-element-checkbox').prop('checked', false);
            });

            // Gestion de la sélection et enregistrement des éléments par défaut
            $('#saveDefaultsForm').on('submit', function (e) {
                e.preventDefault();

                const selectedElements = [];
                $('.default-element-checkbox:checked').each(function () {
                    selectedElements.push($(this).val());
                });

                if (selectedElements.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Aucune sélection',
                        text: 'Veuillez sélectionner au moins un élément à enregistrer.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Mettre à jour le champ hidden avec les éléments sélectionnés
                $('#selectedElementsInput').val(JSON.stringify(selectedElements));

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `Vous allez enregistrer ${selectedElements.length} élément(s) par défaut pour votre entreprise.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, enregistrer',
                    cancelButtonText: 'Annuler',
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Afficher un indicateur de chargement
                        Swal.fire({
                            title: 'Enregistrement en cours...',
                            html: 'Veuillez patienter pendant que nous enregistrons vos éléments.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Soumettre le formulaire
                        this.submit();
                    }
                });
            });

            // Gestion de l'édition
            $('.edit-element').on('click', function () {
                const elementId = $(this).data('allowance-id');
                const allowanceName = $(this).data('allowance-name');

                // Mettre à jour le titre du modal
                $('#editElementsModal .modal-title').text('Modifier des éléments pour ' + allowanceName);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.editOption.edit", ":id") }}'.replace(':id', elementId),
                    type: 'GET',
                    success: function (response) {
                        $('#modalBodyContentEdit').html(response);
                    },
                    error: function () {
                    }
                });
            });
        });

        function exofisc() {
            var trait_fisc = document.getElementById("trait_fisc").value;
            var span = document.getElementById("art_1");
            var div = document.getElementById("art_2");
            var input = document.getElementById("amount_imp_fisc");

            if (trait_fisc == 'exo 100%') {
                div.style.display = 'block';
                input.hidden = false;
            } else {
                span.innerHTML = '';
                div.style.display = 'none';
                input.hidden = true;
            }
        }

        function montimpo() {
            var trait_cnps = document.getElementById("trait_cnps").value;
            var input = document.getElementById("amount_imp");
            if (trait_cnps == 'Soumis au-delà du mode de calcul') {
                input.hidden = false;
            } else {
                input.hidden = true;
            }
        }
    </script>
@endpush