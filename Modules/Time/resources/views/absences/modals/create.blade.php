<!-- Modal de création d'absence -->
<div class="modal fade" id="createAbsenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Nouvelle Absence') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createAbsenceForm" action="{{ route('company.times.absences.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="periode_id" value="{{ $periode->id ?? '' }}">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="month_date" name="month_date" value="{{ $selectedMonth ?? date('Y-m') }}">
                        <input type="hidden" id="year_date" name="year_date" value="{{ $currentYear ?? date('Y') }}">
                        <input type="hidden" id="workhours" name="workhours" value="{{ $workhours ?? 8 }}">
                        <input type="hidden" id="workdays" name="workdays" value="{{ $workdays ?? 1 }}">
                        <input type="hidden" name="month" value="{{ $currentMonth ?? date('m') }}">
                        
                        <div class="alert alert-success" role="alert">
                            <h6 style="color:crimson;">{{ __("Enregistrer les absences du mois de ") }} {{ Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('F') }} {{ __("uniquement. Saisir les informations des dates de début et de retour sous ce format 'jj/mm/aaaa'.") }}</h6>
                        </div>
                        
                        @if(auth()->user()->type != 'employee')
                            <div class="form-group col-md-12 mb-3">
                                <label for="employee_id" class="form-label">{{ __('Employé') }} <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="select2 form-select" required>
                                    <option value="">{{ __('Sélectionner un employé') }}</option>
                                    @foreach($employees as $key => $employee)
                                        <option value="{{ $key }}">{{ $employee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                        @endif
                        
                        <div class="form-group col-md-6 mb-3">
                            <label for="start_date" class="form-label">{{ __('Date de départ') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="date" required>
                        </div>
                        
                        <div class="form-group col-md-6 mb-3">
                            <label for="end_date" class="form-label">{{ __('Date de reprise') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="arrival_date" required>
                        </div>
                        
                        <div class="alert alert-info mb-3" role="alert">
                            <h6>{{ __("Vous avez la possibilité d'ajuster le nombre d'heures d'absence de l'employé s'il est inférieur à celui indiqué dans le champ.") }}</h6>
                        </div>
                        
                        <div class="form-group col-md-4 mb-3">
                            <label for="hours" class="form-label">{{ __('Absences en heures') }} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="hours" name="hours" required oninput="maj_hours()">
                        </div>
                        
                        <div class="form-group col-md-4 mb-3">
                            <label for="days" class="form-label">{{ __('Absences en Jours') }} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="days" name="days" step="0.01" required>
                        </div>
                        
                        <div class="form-group col-md-4 mb-3">
                            <label for="motif_justify" class="form-label">{{ __('Justifiée') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="motif_justify" id="motif_justify" onchange="justyfi()" required>
                                <option value="">{{ __('Sélectionner') }}</option>
                                <option value="Non">{{ __('Non') }}</option>
                                <option value="Oui">{{ __('Oui') }}</option>
                            </select>
                        </div>
                        
                        <div id="permission" class="form-group col-md-12 mb-3" style="display: none;">
                            <p><strong> ARTICLE 25.12</strong></p>
                            <p style="text-align: justify;">{{ __("Le travailleur comptant au moins") }} <span style="color: #ff0000;"><strong>{{ __('six (6) mois') }}</strong></span> {{ __("de présence dans l'entreprise et touché par les événements familiaux dûment justifiés, énumérés ci-après, dans la limite de") }} <span style="color: #ff0000;"><strong>{{ __('dix (10) jours') }}</strong></span> {{ __("ouvrables par an, non déductibles du congé réglementaire et n'entraînant aucune retenue de salaire, bénéficie d'une permission exceptionnelle pour les cas suivants se rapportant à la famille légale :") }}</p>
                            
                            <label for="type_permis" class="form-label">{{ __('PERMISSIONS EXCEPTIONNELLES') }}</label>
                            <select class="form-select" name="type_permis" id="type_permis" onchange="justyfi2()">
                                <option value="Mariage du travailleur : 04 jours ouvrables">{{ __("Mariage du travailleur : 04 jours ouvrables") }}</option>
                                <option value="Mariage d'un de ses enfants : 02 jours ouvrables">{{ __("Mariage d'un de ses enfants : 02 jours ouvrables") }}</option>
                                <option value="Mariage d'un frère, d'une sœur : 02 jours ouvrables">{{ __("Mariage d'un frère, d'une sœur : 02 jours ouvrables") }}</option>
                                <option value="Décès du conjoint : 05 jours ouvrables">{{ __("Décès du conjoint : 05 jours ouvrables") }}</option>
                                <option value="Décès d'un enfant, du père, de la mère du travailleur : 05 jours ouvrables">{{ __("Décès d'un enfant, du père, de la mère du travailleur : 05 jours ouvrables") }}</option>
                                <option value="Décès d'un frère ou d'une sœur : 02 jours ouvrables">{{ __("Décès d'un frère ou d'une sœur : 02 jours ouvrables") }}</option>
                                <option value="Décès d'un beau-père ou d'une belle-mère : 02 jours ouvrables">{{ __("Décès d'un beau-père ou d'une belle-mère : 02 jours ouvrables") }}</option>
                                <option value="Naissance d'un enfant : 02 jours ouvrables">{{ __("Naissance d'un enfant : 02 jours ouvrables") }}</option>
                                <option value="Baptême d'un enfant : 01 jour ouvrable">{{ __("Baptême d'un enfant : 01 jour ouvrable") }}</option>
                                <option value="Première communion : 01 jour ouvrable">{{ __("Première communion : 01 jour ouvrable") }}</option>
                                <option value="Déménagement : 01 jour ouvrable">{{ __("Déménagement : 01 jour ouvrable") }}</option>
                                <option value="Autres">{{ __("Autres") }}</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-12 mb-3">
                            <label for="remark" class="form-label">{{ __("Motif de l'absence") }}</label>
                            <textarea class="form-control" id="remark" name="remark" rows="2" placeholder="{{ __('Détails du motif de l\'absence...') }}"></textarea>
                        </div>
                        
                        <div class="form-group col-md-12 mb-3">
                            <label for="document" class="form-label">{{ __('Document justificatif (si applicable)') }}</label>
                            <input type="file" class="form-control" id="document" name="document">
                            <small class="text-muted">{{ __('Formats acceptés : jpg, jpeg, png, pdf (Max: 2MB)') }}</small>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <p class="text-justify">{{ __("Toute permission de cette nature doit faire l'objet d'une autorisation préalable de l'employeur, soit par écrit, soit en présence d'un représentant du personnel.") }}</p>
                            <p class="text-justify">{{ __("En cas de force majeure rendant impossible l'autorisation préalable de l'employeur, la présentation des pièces justifiant l'absence doit s'effectuer dans les plus brefs délais et, au plus tard, dans les") }} <span class="text-danger fw-bold">{{ __('quinze (15) jours') }}</span> {{ __("qui suivent l'événement.") }}</p>
                            <p class="text-justify">Si celui-ci se produit hors du lieu d’emploi et nécessite le déplacement du travailleur, l’employeur accordera un délai de route de<span style="color: #ff0000;"><strong> deux (2) jours</strong></span> lorsque le lieu où s’est produit l’événement est situé à moins de 400 kilomètres et <span style="color: #ff0000;"><strong>trois (3) jours</strong> </span>au-delà de 400 kilomètres. Ces délais de route ne seront pas rémunérés.</p>
                            <p class="text-justify">En ce qui concerne les autres membres de la famille, non cités ci-dessus, une permission de<span style="color: #ff0000;"><strong> deux (2) jours</strong></span> peut être accordée en cas de décès et d’<span style="color: #ff0000;"><strong>un (1) jour</strong> </span>en cas de mariage. Ces absences ne sont pas payées.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Enregistrer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fonction pour gérer l'affichage du champ de permission
    function justyfi() {
        var motif_justify = document.getElementById("motif_justify").value;
        var permission = document.getElementById("permission");
        var type_permis = document.getElementById("type_permis").value;
        var remark = document.getElementById("remark");

        if (motif_justify == 'Oui') {
            permission.style.display = 'block';
            remark.value = type_permis;
        } else {
            permission.style.display = 'none';
            remark.value = '';
        }
    }

    // Fonction pour mettre à jour la remarque en fonction du type de permission
    function justyfi2() {
        var type_permis = document.getElementById("type_permis").value;
        var remark = document.getElementById("remark");
        
        if (type_permis == 'Autres') {
            remark.value = '';
            remark.focus();
        } else {
            remark.value = type_permis;
        }
    }

    // Fonction pour calculer la différence entre deux dates
    function calculerDifferenceDates() {
        var dateDepartStr = document.getElementById('start_date').value;
        var dateRetourStr = document.getElementById('end_date').value;
        var workhours = parseFloat(document.getElementById('workhours').value) || 8;
        var workdays = parseFloat(document.getElementById('workdays').value) || 1;
        var taux = workhours / workdays;

        // Si la date de départ est vide, on ne fait rien
        if (!dateDepartStr) return;

        // Si la date de retour est vide, on met 1 jour par défaut
        if (!dateRetourStr) {
            document.getElementById('days').value = 1;
            document.getElementById('hours').value = taux.toFixed(2);
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
        document.getElementById('days').value = differenceJours;
        document.getElementById('hours').value = differenceHeures.toFixed(2);
    }

    // Fonction pour mettre à jour les heures en fonction des jours
    function maj_hours() {
        var days = parseFloat(document.getElementById('days').value) || 0;
        var workhours = parseFloat(document.getElementById('workhours').value) || 8;
        var workdays = parseFloat(document.getElementById('workdays').value) || 1;
        var taux = workhours / workdays;
        var hours = days * taux;
        document.getElementById('hours').value = hours.toFixed(2);
    }

    // Écouteurs d'événements
    document.addEventListener('DOMContentLoaded', function() {
        // Calculer la différence de dates quand les champs de date changent
        document.getElementById('start_date').addEventListener('change', calculerDifferenceDates);
        document.getElementById('end_date').addEventListener('change', calculerDifferenceDates);
        
        // Mettre à jour les jours quand les heures changent
        document.getElementById('hours').addEventListener('input', function() {
            var hours = parseFloat(this.value) || 0;
            var workhours = parseFloat(document.getElementById('workhours').value) || 8;
            var workdays = parseFloat(document.getElementById('workdays').value) || 1;
            var taux = workhours / workdays;
            var days = hours / taux;
            document.getElementById('days').value = days.toFixed(2);
        });
        
        // Mettre à jour les heures quand les jours changent
        document.getElementById('days').addEventListener('input', function() {
            var days = parseFloat(this.value) || 0;
            var workhours = parseFloat(document.getElementById('workhours').value) || 8;
            var workdays = parseFloat(document.getElementById('workdays').value) || 1;
            var taux = workhours / workdays;
            var hours = days * taux;
            document.getElementById('hours').value = hours.toFixed(2);
        });
    });
</script>
@endpush
