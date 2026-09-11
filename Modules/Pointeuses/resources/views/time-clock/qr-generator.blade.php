@extends('layouts.admin')

@section('page-title')
    {{ __('Générer un QR Code de pointage') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Générer') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Formulaire de génération de QR code -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Générer un QR Code de pointage') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('qr-code.generate') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="location_id">{{ __('Sélectionner un lieu') }}</label>
                            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">{{ __('-- Sélectionnez un lieu --') }}</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="qr_size">{{ __('Taille du QR Code') }}</label>
                            <select name="qr_size" id="qr_size" class="form-select">
                                <option value="300">300 x 300 px</option>
                                <option value="500" selected>500 x 500 px</option>
                                <option value="800">800 x 800 px</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="api_url">{{ __('URL de l\'API mobile') }}</label>
                            <input type="url" class="form-control @error('api_url') is-invalid @enderror"
                                id="api_url" name="api_url" value="{{ config('app.url') }}api/pointage/scanner"
                                readonly>
                            <small class="form-text text-muted">
                                {{ __('Cette URL sera encodée dans le QR code et ne peut pas être modifiée.') }}
                            </small>
                            @error('api_url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ __('Le QR Code généré permettra aux employés de pointer facilement sur le lieu sélectionné.') }}
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ __('Générer QR Code') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des lieux avec QR codes -->
            <div class="card mt-4">
                <div class="card-body">
                    @if(count($activeLocations) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Lieu') }}</th>
                                        <th>{{ __('Adresse') }}</th>
                                        <th>{{ __('Statut') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeLocations as $location)
                                        <tr>
                                            <td>
                                                <strong>{{ \App\Models\Utility::getValByName('company_name') }}</strong><br>
                                                <small class="text-muted">{{ \App\Models\Utility::getValByName('company_city') }}, {{ \App\Models\Utility::getValByName('company_country') }}</small>
                                            </td>
                                            <td>{{  \App\Models\Utility::getValByName('company_address') }}</td>
                                            <td class="text-center">
                                                @if($location->is_active)
                                                    <span class="alert alert-success">{{ __('Actif') }}</span>
                                                @else
                                                    <span class="alert alert-danger">{{ __('Inactif') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('qr-code.show-for-location', $location->id) }}"
                                                        class="btn btn-info me-2" title="{{ __('Voir QR Code') }}">
                                                        <i class="fas fa-qrcode"></i>
                                                    </a>
                                                    <form action="{{route('qr-code.generate-for-location')}}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="location_id" value="{{ $location->id }}">
                                                        <input type="hidden" name="qr_size" value="500">
                                                        <button type="submit" class="btn btn-primary me-2" title="{{ __('Régénérer') }}">
                                                            <i class="fas fa-sync"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{route('locations.edit', $location->id) }}"
                                                        class="btn btn-warning" title="{{ __('Modifier le lieu') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination si nécessaire -->
                        @if($activeLocations->hasPages())
                            <div class="mt-3">
                                {{ $activeLocations->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ __('Aucun lieu actif trouvé. Veuillez d\'abord créer des lieux.') }}
                        </div>
                        <div class="text-center mt-3">
                            <a class="btn btn-primary mb-2" href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                <i class="fas fa-plus-circle me-2"></i> {{ __('Créer un nouveau lieu') }}
                            </a>
                        </div>
                    @endif
                    @if (count($activeLocations) > 0)
                        <div class="text-center mt-3">
                            <a class="btn btn-primary mb-2" href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                <i class="fas fa-plus-circle me-2"></i> {{ __('Créer un nouveau lieu') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter un nouveau lieu -->
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addLocationModalLabel" style="color: white;">
                    <i class="fas fa-map-marker-alt me-2"></i> {{ __('Ajouter un nouveau lieu') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('locations.store') }}" method="POST" id="locationForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('Nom du lieu') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}<span class="text-danger pl-1"> *</span>
                            <div class="form-icon-user">
                                {{ Form::select('branch_id', $branche, null, ['class' => 'form-select branch_id', 'required' => 'required', 'placeholder' => __('Select Branch'), 'id' => 'branch_id', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <div class="form-icon-user" id="department_id">
                                {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<span class="text-danger pl-1"> *</span>
                                <select class="form-select department_id" name="department_id" id="department_id" placeholder="__('Department')" required>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nbre_emp" class="form-label">{{ __('Nombre d\'employés') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nbre_emp" name="nbre_emp" required>
                            <div class="invalid-feedback" id="nbre_emp-error"></div>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">{{ __('Adresse') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            <div class="invalid-feedback" id="address-error"></div>
                        </div>

                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">{{ __('Lieu actif') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> {{ __('Annuler') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveLocationBtn">
                        <i class="fas fa-save me-1"></i> {{ __('Enregistrer le lieu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assurez-vous que jQuery est chargé avant ce script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var b_id = $('#branch_id').val();
        //getDepartment(b_id);

        // Rafraîchir la liste des lieux
        $('#refresh-locations').on('click', function() {
            location.reload();
        });

        // Soumission du formulaire de lieu en AJAX
        $('#locationForm').on('submit', function(e) {
            e.preventDefault();

            // Réinitialiser les messages d'erreur
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Désactiver le bouton de soumission pour éviter les doubles soumissions
            $('#saveLocationBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Enregistrement...") }}');

            // Envoyer les données via AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    // Afficher un message de succès
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Succès!") }}',
                        text: response.message || '{{ __("Le lieu a été créé avec succès.") }}',
                        confirmButtonText: '{{ __("OK") }}'
                    }).then((result) => {
                        // Recharger la page pour afficher le nouveau lieu
                        location.reload();
                    });
                },
                error: function(xhr) {
                    // Réactiver le bouton de soumission
                    $('#saveLocationBtn').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> {{ __("Enregistrer le lieu") }}');

                    // Gérer les erreurs de validation
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;

                        // Afficher les erreurs sur les champs correspondants
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        });

                        // Faire défiler jusqu'à la première erreur
                        $('html, body').animate({
                            scrollTop: $('.is-invalid:first').offset().top - 100
                        }, 500);
                    } else {
                        // Afficher une erreur générale
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Erreur!") }}',
                            text: '{{ __("Une erreur est survenue. Veuillez réessayer.") }}',
                            confirmButtonText: '{{ __("OK") }}'
                        });
                    }
                }
            });
        });
    });

    $(document).on('change', 'select[name=branch_id]', function() {
        var branch_id = $(this).val();
        getDepartment(branch_id);
    });

    function getDepartment(bid) {

        $.ajax({
            url: '{{ route('monthly.getdepartment') }}',
            type: 'POST',
            data: {
                "branch_id": bid,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {

                $('.department_id').empty();
                var emp_selct = `<select class="form-select department_id" name="department_id" id="choices-multiple"
                                        placeholder="Select Department" required>
                                        </select>`;
                $('.department_div').html(emp_selct);

                $('.department_id').append('<option value=""> {{ __('Department') }} </option>');
                $.each(data, function(key, value) {
                    $('.department_id').append('<option value="' + key + '">' + value +
                        '</option>');
                });
                new Choices('#choices-multiple', {
                    removeItemButton: true,
                });
            }
        });
    }
</script>
@endsection
