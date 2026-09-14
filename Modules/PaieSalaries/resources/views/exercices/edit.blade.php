@extends('layouts.app')

@section('title', 'Modifier l\'exercice')

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'exercice : {{ $exercice->nom }}</h5>
                    <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}" class="btn btn-sm btn-label-info">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.paiesalaries.exercices.update', $exercice->id) }}" method="POST" id="formExercice">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <label for="nom" class="form-label">Nom de l'exercice <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Exercice</span>
                                    <select class="form-select @error('nom') is-invalid @enderror" 
                                            id="nom" name="nom" required>
                                        <option value="">Sélectionner une année...</option>
                                        @for($year = 2020; $year <= 2030; $year++)
                                            <option value="{{ $year }}" {{ old('nom', $exercice->nom) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control flatpickr-date @error('date_debut') is-invalid @enderror" 
                                       id="date_debut" name="date_debut" 
                                       value="{{ old('date_debut', $exercice->date_debut->format('Y-m-d')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="date_fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror" 
                                       id="date_fin" name="date_fin" 
                                       value="{{ old('date_fin', $exercice->date_fin->format('Y-m-d')) }}" readonly>
                                <small class="text-muted">Calculée automatiquement</small>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                    <option value="brouillon" {{ old('statut', $exercice->statut) === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="en_cours" {{ old('statut', $exercice->statut) === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="cloture" {{ old('statut', $exercice->statut) === 'cloture' ? 'selected' : '' }}>Clôturé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $exercice->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('libs/select2/select2.js') }}"></script>
<script src="{{ asset('libs/flatpickr/flatpickr.js') }}"></script>
<script>
    $(function() {
        // Initialisation de flatpickr pour les dates
        $('.flatpickr-date').flatpickr({
            dateFormat: 'd/m/Y',
            locale: 'fr'
        });

        // Calcul automatique de la date de fin
        function calculerDateFin() {
            const dateDebut = $('#date_debut').val();
            if (dateDebut) {
                const debut = new Date(dateDebut);
                const anneeSelectionnee = $('#nom').val();
                
                // Calculer la date de fin : 31 décembre de l'année sélectionnée
                const dateFin = new Date(anneeSelectionnee, 11, 31); // mois 11 = décembre, jour 31
                
                // Formater la date en YYYY-MM-DD
                const dateFinFormatee = dateFin.toISOString().split('T')[0];
                $('#date_fin').val(dateFinFormatee);
            }
        }

        // Synchroniser la date de début avec l'année sélectionnée
        function synchroniserDateDebut() {
            const anneeSelectionnee = $('#nom').val();
            if (anneeSelectionnee) {
                // Par défaut, commencer le 1er janvier de l'année sélectionnée
                const dateDebut = new Date(anneeSelectionnee, 0, 1); // mois 0 = janvier, jour 1
                const dateDebutFormatee = dateDebut.toISOString().split('T')[0];
                $('#date_debut').val(dateDebutFormatee);
                
                // Calculer la date de fin
                calculerDateFin();
            } else {
                // Vider les dates si aucune année n'est sélectionnée
                $('#date_debut').val('');
                $('#date_fin').val('');
            }
        }

        // Écouter les changements
        $('#nom').on('change', function() {
            synchroniserDateDebut();
        });

        $('#date_debut').on('change', function() {
            calculerDateFin();
        });

        // Validation du formulaire
        $('#formExercice').on('submit', function(e) {
            const dateDebut = new Date($('#date_debut').val());
            const dateFin = new Date($('#date_fin').val());
            
            if (dateDebut >= dateFin) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'La date de fin doit être postérieure à la date de début',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
@endpush