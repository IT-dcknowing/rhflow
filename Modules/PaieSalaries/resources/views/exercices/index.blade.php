@extends('layouts.app')

@section('title', 'Gestion des exercices de paie')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des exercices de paie</h4>
                        <p class="text-muted mb-0">Gérez les exercices de paie des employés</p>
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
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ route('company.paiesalaries.exercices.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Nouvel exercice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des exercices</h5>
                <div>
                    {{ $exercices->count() }} exercices
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="exercicesTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th>Périodes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exercices as $exercice)
                                <tr>
                                    <td>{{ $exercice->code }}</td>
                                    <td>
                                        <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}">
                                            Exercice {{ $exercice->nom }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $exercice->date_debut->format('d/m/Y') }} -
                                        {{ $exercice->date_fin->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($exercice->statut === 'en_cours')
                                            <span class="badge bg-label-success">En cours</span>
                                        @elseif($exercice->statut === 'cloture')
                                            <span class="badge bg-label-secondary">Clôturé</span>
                                        @else
                                            <span class="badge bg-label-warning">Brouillon</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $exercice->periodes->count() }} périodes
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}"
                                                class="btn btn-icon btn-sm btn-label-info me-1" data-bs-toggle="tooltip"
                                                title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('company.paiesalaries.exercices.edit', $exercice->id) }}"
                                                class="btn btn-icon btn-sm btn-label-warning me-1" data-bs-toggle="tooltip"
                                                title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('company.paiesalaries.exercices.destroy', $exercice->id) }}"
                                                method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-label-danger"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">Aucun exercice trouvé</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $exercices->links() }}
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
            $('#exercicesTable').DataTable({
                responsive: true,
                // Tri sur « Code ». L'index est passé de 1 à 0 avec le retrait de la colonne N°.
                order: [[0, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                },
            });

            // Confirmation de suppression
            $('.delete-form').on('submit', function (e) {
                e.preventDefault();
                const form = this;

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
        });
    </script>
@endpush