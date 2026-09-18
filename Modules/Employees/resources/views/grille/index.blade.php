@extends('layouts.app')

@section('page-title')
    {{ __('Gestion grille salariale') }}
@endsection


@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête des Paramètres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-primary">💵 Grilles Salariales </h4>
                    <p class="text-muted mb-0">Gérez les grilles salariales des employés de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body employee-detail-body fulls-card">
                    <h5>
                        Secteur d'activité de l'entreprise : <strong>
                            {{ $secteurs->name }}
                        </strong>
                    </h5>
                </div>
            </div>

            <!-- User Pills -->
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
                @foreach ($typeposte as $poste)
                    @foreach ($data as $table => $rows)
                        @if ($rows->isNotEmpty() && $rows->first()->type_poste == $poste->id)
                            <li class="nav-item">
                                <button class="nav-link {{ $loop->parent->first ? 'active bg-primary text-white' : ''}}" id="{{ $table }}-tab" data-bs-toggle="tab" data-bs-target="#nav-{{ $table }}" type="button" role="tab" aria-controls="nav-{{ $table }}" aria-selected="{{ $loop->parent->first ? 'true' : 'false' }}">
                                    <strong>{{ $poste->title }} &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong>
                                </button>
                            </li>
                            @break
                        @endif
                    @endforeach
                @endforeach
            </ul>
            <!--/ User Pills -->

            <?php $cpte = 0;  ?>
            <div class="card">         
                <div class="tab-content" id="nav-tabContent">
                    @php $firstVisible = true; @endphp
                    @foreach ($data as $table => $rows)
                        @if ($rows->isNotEmpty())
                            <div class="tab-pane fade {{ $firstVisible ? 'show active' : '' }}" id="nav-{{ $table }}" role="tabpanel" aria-labelledby="nav-{{ $table }}-tab">
                                @php $firstVisible = false; @endphp
                                <div class="card-header" align="right">
                                    <h5>Créer une catégorie intermédiaire&nbsp;&nbsp;&nbsp;
                                        <button href="#"
                                            data-bs-toggle="modal" data-bs-target="#addGrilleModal"
                                            data-typeposte-id = "{{ $rows->first()->type_poste }}"
                                            title="{{ __('Ajouter une catégorie') }}"
                                            class="btn btn-sm btn-primary btn-add-categorie">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </h5>
                                </div>
                                <div class="card-body employee-detail-body fulls-card">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th width="1%"><center>{{ __('#') }}</center></th>
                                                <th width="30%"><center>{{ __('Nom catégorie') }}</center></th>
                                                <th width="23%"><center>{{ __('Salaire Catégoriel Horaire') }}</center></th>
                                                <th><center width="20%">{{ __('Salaire Catégoriel mensuel') }}</center></th>
                                                <th width="10%"><center>{{ __('Action') }}</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $allRows = collect();
                                                if(isset($data[$table])) $allRows = $allRows->merge($data[$table]);
                                                if(isset($dateCreate[$table])) $allRows = $allRows->merge($dateCreate[$table]);
                                                $rows = $allRows->sortBy('categorie');
                                            @endphp
                                            @foreach ($rows as $row)
                                                <tr>
                                                    <td align="center">{{ $row->id }}</td>
                                                    <td align="center">Catégorie {{ $row->categorie }}</td>
                                                    <td align="center">{{ isset($row->salaire_minima_horaire) ? number_format($row->salaire_minima_horaire, '0', '.', ' ') : '0' }}</td>
                                                    <td align="center">{{ isset($row->salaire_minima_mensuel) ? number_format($row->salaire_minima_mensuel, '0', '.', ' ') : '0' }}</td>
                                                    <td align="center">
                                                            @if ($row->company_id > 0 )
                                                            <div class="d-flex">
                                                                <div class="action-btn me-2">
                                                                    <a href="#" class="btn btn-icon btn-sm btn-label-warning edit-btn"
                                                                        data-id="{{ $row->id }}"
                                                                        data-bs-toggle="tooltip"
                                                                        title="{{ __('Edit Category') }}"
                                                                        data-bs-original-title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil"></i>
                                                                    </a>
                                                                </div>
                                                                <div class="action-btn">
                                                                    <form method="POST" action="{{ route('company.employees.grille.destroy', $row->id) }}" class="delete-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <a href="#"
                                                                            class="btn btn-icon btn-sm btn-label-danger bs-pass-para delete-btn"
                                                                            data-bs-toggle="tooltip" title=""
                                                                            data-bs-original-title="Supprimer" aria-label="Supprimer"><i
                                                                                class="ti ti-trash"></i></a>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter une catégorie intermédiaire -->
<div class="modal fade" id="addGrilleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Ajouter une catégorie intermédiaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('company.employees.grille.store') }}" id="addGrilleForm" method="POST">
                @csrf
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
                    <button type="submit" class="btn btn-primary retenue-btn">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier une catégorie intermédiaire -->
<div class="modal fade" id="editGrilleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Modifier une catégorie intermédiaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGrilleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editModalBodyContent">
                    <!-- Le contenu sera chargé dynamiquement via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary retenue-btn">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
	<script>
		// Navigation fluide entre les tabs
		$(document).ready(function() {
			const tabs = document.querySelectorAll('.nav-link');
			const tabPanes = document.querySelectorAll('.tab-pane');
			
			// Fonction pour activer un tab spécifique
			function activateTab(targetTab) {
				// Désactiver tous les tabs et panes
				tabs.forEach(tab => {
					tab.classList.remove('active', 'bg-primary', 'text-white');
				});
				tabPanes.forEach(pane => {
					pane.classList.remove('show', 'active');
				});
				
				// Activer le tab cliqué et son contenu
				targetTab.classList.add('active', 'bg-primary', 'text-white');
				const targetPane = document.querySelector(targetTab.getAttribute('data-bs-target'));
				if (targetPane) {
					targetPane.classList.add('show', 'active');
				}
			}
			
			// Ajouter les écouteurs d'événements
			tabs.forEach(tab => {
				tab.addEventListener('click', function(e) {
					e.preventDefault();
					activateTab(this);
				});
			});
			
			// Optionnel : s'assurer que le premier tab est bien stylé si ce n'est pas fait par PHP
            if (tabs.length > 0 && !document.querySelector('.nav-link.active')) {
                activateTab(tabs[0]);
            }

            // Gérer l'ouverture du modal d'ajout de catégorie
            $('.btn-add-categorie').on('click', function() {  
                var id_poste =  $(this).data('typeposte-id');
               
                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.employees.grille.create", ":id") }}'.replace(':id', id_poste),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContent').html(response);
                    },
                    error: function() {
                        $('#modalBodyContent').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal d'édition de catégorie
            $('.edit-btn').on('click', function(e) {
                e.preventDefault();
                var categoryId = $(this).data('id');
                
                // Charger le formulaire d'édition via AJAX
                $.ajax({
                    url: '{{ route("company.employees.grille.edit", ":id") }}'.replace(':id', categoryId),
                    type: 'GET',
                    success: function(response) {
                        $('#editModalBodyContent').html(response);
                        $('#editGrilleForm').attr('action', '{{ route("company.employees.grille.update", ":id") }}'.replace(':id', categoryId));
                        $('#editGrilleModal').modal('show');
                    },
                    error: function() {
                        $('#editModalBodyContent').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                        $('#editGrilleModal').modal('show');
                    }
                });
            });
		});

        // SweetAlert pour la suppression
		document.addEventListener('DOMContentLoaded', function() {
			const deleteButtons = document.querySelectorAll('.delete-btn');
			
			deleteButtons.forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const form = this.closest('.delete-form');
					const itemName = 'cette catégorie';
					
					Swal.fire({
						title: 'Êtes-vous sûr?',
						text: `Voulez-vous vraiment supprimer ${itemName}? Cette action est irréversible.`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#d33',
						cancelButtonColor: '#3085d6',
						confirmButtonText: 'Oui, supprimer!',
						cancelButtonText: 'Annuler',
						showLoaderOnConfirm: true,
						preConfirm: function() {
							return form.submit();
						}
					}).then((result) => {
						if (result.isConfirmed) {
							// Le formulaire sera soumis via preConfirm
							Swal.fire(
								'Supprimé!',
								'La catégorie a été supprimée avec succès.',
								'success'
							);
						}
					});
				});
			});
		});
	</script>
@endpush
