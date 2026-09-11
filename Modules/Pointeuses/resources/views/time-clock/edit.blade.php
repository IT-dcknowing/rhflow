@extends('layouts.admin')

@section('page-title')
    {{ __('Modifier le QR Code de pointage') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Modifier le lieu') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="card col-md-8 offset-md-2 p-4">
            <form id="locationForm" action="{{ route('locations.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="form-group col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('Nom du lieu') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" required>
                        <div class="invalid-feedback" id="name-error">
                            @error('name') {{ $message }} @enderror
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}<span class="text-danger pl-1"> *</span>
                        <div class="form-icon-user">
                            {{ Form::select('branch_id', $branches, old('branche_id', $location->branche_id), ['class' => 'form-select branch_id', 'required' => 'required', 'placeholder' => __('Select Branch'), 'id' => 'branch_id']) }}
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <div class="form-icon-user" id="department_id">
                            {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}<span class="text-danger pl-1"> *</span>
                            <select class="form-select department_id" name="department_id" id="department_id" placeholder="__('Department')" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="nbre_emp" class="form-label">{{ __('Nombre d\'employés') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nbre_emp') is-invalid @enderror" id="nbre_emp" name="nbre_emp" value="{{ old('nbre_emp', $location->size) }}" required>
                        <div class="invalid-feedback" id="nbre_emp-error">@error('nbre_emp') {{ $message }} @enderror</div>
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label">{{ __('Adresse') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address', $location->address) }}</textarea>
                        <div class="invalid-feedback" id="address-error">@error('address') {{ $message }} @enderror</div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Lieu actif') }}</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="saveLocationBtn">
                    <i class="fas fa-save me-2"></i> {{ __('Enregistrer le lieu') }}
                </button>
                <button type="button" class="btn btn-secondary" id="refresh-locations">
                    <i class="fas fa-sync-alt me-2"></i> {{ __('Rafraîchir') }}
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Assurez-vous que jQuery est chargé avant ce script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var b_id = $('#branch_id').val();
        getDepartment(b_id);

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
                    $('#saveLocationBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i> {{ __("Enregistrer le lieu") }}');

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
                var emp_selct = `<select class="form-select department_id" name="department_id" id="department_id"
                                        placeholder="Sélectionner un département" required>
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
