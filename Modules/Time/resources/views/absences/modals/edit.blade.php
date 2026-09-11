
<form action="{{ route('company.times.absences.update', $absence->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <input type="hidden" id="edit_month_date" name="month_date" value="{{ $selectedMonth ?? date('Y-m') }}">
        <input type="hidden" id="edit_workhours" name="workhours" value="{{ $workhours ?? 8 }}">
        <input type="hidden" id="edit_workdays" name="workdays" value="{{ $workdays ?? 1 }}">

        <div class="alert alert-success" role="alert">
            <h6 style="color:crimson;">{{ __("Modifier les informations de l'absence.") }}</h6>
        </div>
        
        @if(auth()->user()->type != 'employee')
            <div class="form-group col-md-12 mb-3">
                <label for="edit_employee_id" class="form-label">{{ __('Employé') }} <span class="text-danger">*</span></label>
                <select name="employee_id" id="edit_employee_id" class="select2 form-select" required>
                    @foreach($employees as $key => $employee)
                        <option value="{{ $key }}" {{ (isset($absence->employee_id) && $absence->employee_id == $key) ? 'selected' : '' }}>{{ $employee }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <input type="hidden" id="edit_employee_id" name="employee_id" value="{{ $absence->employee_id ?? '' }}">
        @endif
        
        <div class="form-group col-md-6 mb-3">
            <label for="edit_start_date" class="form-label">{{ __('Date de départ') }} <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="edit_start_date" name="date" value="{{ $absence->date ?? '' }}" required>
        </div>
        
        <div class="form-group col-md-6 mb-3">
            <label for="edit_end_date" class="form-label">{{ __('Date de reprise') }} <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="edit_end_date" name="arrival_date" value="{{ $absence->arrival_date ?? '' }}" required>
        </div>
        
        <div class="form-group col-md-4 mb-3">
            <label for="edit_hours" class="form-label">{{ __('Absences en heures') }} <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="edit_hours" name="hours" value="{{ $absence->hours ?? '' }}" required oninput="maj_edit_hours()">
        </div>
        
        <div class="form-group col-md-4 mb-3">
            <label for="edit_days" class="form-label">{{ __('Absences en Jours') }} <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="edit_days" name="days" value="{{ $absence->retenue ?? '' }}" step="0.01" required>
        </div>
        
        <div class="form-group col-md-4 mb-3">
            <label for="edit_motif_justify" class="form-label">{{ __('Justifiée') }} <span class="text-danger">*</span></label>
            <select class="form-select" name="motif_justify" id="edit_motif_justify" onchange="edit_justyfi()" required>
                <option value="" {{ !isset($absence->motif_justify) ? 'selected' : '' }}>{{ __('Sélectionner') }}</option>
                <option value="Non" {{ (isset($absence->motif_justify) && $absence->motif_justify == 'Non') ? 'selected' : '' }}>{{ __('Non') }}</option>
                <option value="Oui" {{ (isset($absence->motif_justify) && $absence->motif_justify == 'Oui') ? 'selected' : '' }}>{{ __('Oui') }}</option>
            </select>
        </div>
        
        <div id="edit_permission" class="form-group col-md-12 mb-3" style="display: none;">
            <p><strong> ARTICLE 25.12</strong></p>
            <p style="text-align: justify;">{{ __("Le travailleur comptant au moins") }} <span style="color: #ff0000;"><strong>{{ __('six (6) mois') }}</strong></span> {{ __("de présence dans l'entreprise et touché par les événements familiaux dûment justifiés, énumérés ci-après, dans la limite de") }} <span style="color: #ff0000;"><strong>{{ __('dix (10) jours') }}</strong></span> {{ __("ouvrables par an, non déductibles du congé réglementaire et n'entraînant aucune retenue de salaire, bénéficie d'une permission exceptionnelle pour les cas suivants se rapportant à la famille légale :") }}</p>
            
            <label for="edit_type_permis" class="form-label">{{ __('PERMISSIONS EXCEPTIONNELLES') }}</label>
            <select class="form-select" name="type_permis" id="edit_type_permis" onchange="edit_justyfi2()">
                <option value="">{{ __('Sélectionner un motif') }}</option>
                <option value="Mariage du travailleur : 04 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == 'Mariage du travailleur : 04 jours ouvrables') ? 'selected' : '' }}>{{ __("Mariage du travailleur : 04 jours ouvrables") }}</option>
                <option value="Mariage d'un de ses enfants : 02 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Mariage d'un de ses enfants : 02 jours ouvrables") ? 'selected' : '' }}>{{ __("Mariage d'un de ses enfants : 02 jours ouvrables") }}</option>
                <option value="Mariage d'un frère, d'une sœur : 02 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Mariage d'un frère, d'une sœur : 02 jours ouvrables") ? 'selected' : '' }}>{{ __("Mariage d'un frère, d'une sœur : 02 jours ouvrables") }}</option>
                <option value="Décès du conjoint : 05 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == 'Décès du conjoint : 05 jours ouvrables') ? 'selected' : '' }}>{{ __("Décès du conjoint : 05 jours ouvrables") }}</option>
                <option value="Décès d'un enfant, du père, de la mère du travailleur : 05 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Décès d'un enfant, du père, de la mère du travailleur : 05 jours ouvrables") ? 'selected' : '' }}>{{ __("Décès d'un enfant, du père, de la mère du travailleur : 05 jours ouvrables") }}</option>
                <option value="Décès d'un frère ou d'une sœur : 02 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Décès d'un frère ou d'une sœur : 02 jours ouvrables") ? 'selected' : '' }}>{{ __("Décès d'un frère ou d'une sœur : 02 jours ouvrables") }}</option>
                <option value="Décès d'un beau-père ou d'une belle-mère : 02 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Décès d'un beau-père ou d'une belle-mère : 02 jours ouvrables") ? 'selected' : '' }}>{{ __("Décès d'un beau-père ou d'une belle-mère : 02 jours ouvrables") }}</option>
                <option value="Naissance d'un enfant : 02 jours ouvrables" {{ (isset($absence->type_permis) && $absence->type_permis == "Naissance d'un enfant : 02 jours ouvrables") ? 'selected' : '' }}>{{ __("Naissance d'un enfant : 02 jours ouvrables") }}</option>
                <option value="Baptême d'un enfant : 01 jour ouvrable" {{ (isset($absence->type_permis) && $absence->type_permis == "Baptême d'un enfant : 01 jour ouvrable") ? 'selected' : '' }}>{{ __("Baptême d'un enfant : 01 jour ouvrable") }}</option>
                <option value="Première communion : 01 jour ouvrable" {{ (isset($absence->type_permis) && $absence->type_permis == 'Première communion : 01 jour ouvrable') ? 'selected' : '' }}>{{ __("Première communion : 01 jour ouvrable") }}</option>
                <option value="Déménagement : 01 jour ouvrable" {{ (isset($absence->type_permis) && $absence->type_permis == 'Déménagement : 01 jour ouvrable') ? 'selected' : '' }}>{{ __("Déménagement : 01 jour ouvrable") }}</option>
                <option value="Autres" {{ (isset($absence->type_permis) && $absence->type_permis == 'Autres') ? 'selected' : '' }}>{{ __("Autres") }}</option>
            </select>
        </div>
        
        <div class="form-group col-md-12 mb-3">
            <label for="edit_remark" class="form-label">{{ __("Motif de l'absence") }}</label>
            <textarea class="form-control" id="edit_remark" name="remark" rows="2" placeholder="{{ __('Détails du motif de l\'absence...') }}">{{ $absence->remark ?? '' }}</textarea>
        </div>
        
        <div class="form-group col-md-12 mb-3">
            <label for="edit_document" class="form-label">{{ __('Document justificatif (si applicable)') }}</label>
            <input type="file" class="form-control" id="edit_document" name="document">
            <small class="text-muted">{{ __('Formats acceptés : jpg, jpeg, png, pdf (Max: 2MB)') }}</small>
            <div id="current_document" class="mt-2"></div>
        </div>
        
        <div class="form-group col-md-12">
            <p class="text-justify">{{ __("Toute permission de cette nature doit faire l'objet d'une autorisation préalable de l'employeur, soit par écrit, soit en présence d'un représentant du personnel.") }}</p>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
        <button type="submit" class="btn btn-primary save-absence-btn"><i class="ti ti-save me-1"></i> {{ __('Mettre à jour') }}</button>
    </div>
</form>
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Sélectionner un élément',
                dropdownParent: $this.parent()
            });
        });

        // Fonction pour gérer l'affichage du champ de permission dans le modal d'édition
        function edit_justyfi() {
            var motif_justify = document.getElementById("edit_motif_justify").value;
            var permission = document.getElementById("edit_permission");
            var type_permis = document.getElementById("edit_type_permis").value;
            var remark = document.getElementById("edit_remark");

            if (motif_justify == 'Oui') {
                permission.style.display = 'block';
                if (type_permis) {
                    remark.value = type_permis;
                }
            } else {
                permission.style.display = 'none';
                remark.value = '';
            }
        }

        // Initialisation au chargement du document
        document.addEventListener('DOMContentLoaded', function() {
            // Déclencher la fonction edit_justyfi si nécessaire
            var motifJustify = document.getElementById("edit_motif_justify");
            var remark = document.getElementById("edit_remark");
            var typePermis = document.getElementById("edit_type_permis");
            
            if (motifJustify) {
                edit_justyfi();
                if (remark) remark.focus();
            }
            
            if (typePermis && typePermis.value) {
                remark.value = typePermis.value;
            }
        });
        
        // Fonction pour mettre à jour les heures en fonction des jours dans le modal d'édition
        function maj_edit_hours() {
            var days = parseFloat(document.getElementById('edit_days').value) || 0;
            var workhours = parseFloat(document.getElementById('edit_workhours').value) || 8;
            var hours = days * workhours;
            document.getElementById('edit_hours').value = hours.toFixed(2);
            var workdays = parseFloat(document.getElementById('edit_workdays').value) || 1;
            var taux = workhours / workdays;
            var hours = days * taux;
            document.getElementById('edit_hours').value = hours.toFixed(2);
        }
        
        // Fonction pour calculer la différence entre deux dates dans le modal d'édition
        function calculerEditDifferenceDates() {
            var dateDepartStr = document.getElementById('edit_start_date').value;
            var dateRetourStr = document.getElementById('edit_end_date').value;
            var workhours = parseFloat(document.getElementById('edit_workhours').value) || 8;
            var workdays = parseFloat(document.getElementById('edit_workdays').value) || 1;
            var taux = workhours / workdays;

            // Si la date de départ est vide, on ne fait rien
            if (!dateDepartStr) return;

            // Si la date de retour est vide, on met 1 jour par défaut
            if (!dateRetourStr) {
                document.getElementById('edit_days').value = 1;
                document.getElementById('edit_hours').value = taux.toFixed(2);
                return;
            }

            // Conversion des dates
            var depart = new Date(dateDepartStr);
            var retour = new Date(dateRetourStr);

            // Calcul de la différence en jours
            var difference = retour - depart;
            var differenceJours = Math.ceil(difference / (1000 * 60 * 60 * 24)) + 1; // +1 pour inclure le jour de départ
            
            // Calcul de la différence en heures
            var differenceHeures = differenceJours * taux;

            // Mise à jour des champs
            document.getElementById('edit_days').value = differenceJours;
            document.getElementById('edit_hours').value = differenceHeures.toFixed(2);
        }
    });
</script>
@endpush
