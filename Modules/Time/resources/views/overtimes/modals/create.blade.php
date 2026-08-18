<div class="modal fade" id="overtimeCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Nouvelle Heure Supplémentaire') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="overtimeCreateForm" method="POST">
                @csrf
                <input type="hidden" class="form-control" id="periode_id" name="periode_id" value="{{$periode->id}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-success" role="alert">
                            <h6 style="color:crimson;">Enregistrer les heures supplémentaires de la période sélectionnée uniquement. Saisisser les informations des dates de début et de retour sous ce format 'jj/mm/aaaa'.</h6>
                        </div>
                        <div class="form-group  col-md-5">
                            <h6 style="color: #ff0000; text-align: left;">Définition des Heures Supplémentaires :</h6>
                            <p style="text-align: justify;">Les heures supplémentaires sont décomptées à la semaine. Elles donnent lieu à des majorations de salaire.</p>
                            <p style="text-align: justify;">Le plafond applicable aux heures supplémentaires est : </p>
                                <ul>
                                    <li>de 15h/ supplémentaires max. par semaine par salarié</li>
                                    <li>et de 3 heures max. par jour au-delà de la durée journalière de travail prévue pour le salarié</li>
                                    <li>et de 75 heures max par an par salarié</li>
                                </ul>
                            <h6 style="color: #ff0000; text-align: left;">Majoration des Heures Supplémentaires :</h6>
                            <p  style="text-align: justify;">A défaut de dispositions conventionnelles ou d’accords collectifs:</p>
                            <ul>
                                <li>Heures Supplémentaires réalisées hors Dimanche (ou jour de repos hebdo), hors Jour Férié et hors Nuit</li>
                            </ul>
                        </div>
                        <div class="form-group  col-md-7">
                            <h6><br></h6>
                            <p  style="text-align: justify;">La majoration minimale du Salaire Réel est en fonction du rang de l’heure supplémentaire :</p>
                            <ul>
                                <li>15% pour les heures réalisées de la 41ème à la 46ème heure sur la semaine</li>
                                <li>50% de majoration pour les suivantes</li>
                            </ul>
                            <p style="text-align: justify;">Heures Supplémentaires réalisées le Dimanche (ou jour de repos hebdo) ou pendant un Jour Férié (heures de Jour)</p>
                            <p style="text-align: justify;">Ces Heures Supplémentaires donnent lieu à une majoration du Salaire Réel comme suit:</p>
                            <ul>
                                <li>75% de majoration pour toutes les heures réalisées</li>
                            </ul>
                            <p style="text-align: justify;">Heures Supplémentaires réalisées la Nuit</p>
                            <p style="text-align: justify;">Ces Heures Supplémentaires donnent lieu à une majoration comme suit:</p>
                            <ul>
                                <li>Pour les heures de Nuit réalisées le Dimanche ou un Jour Férié : 100% de majoration pour toutes les heures réalisées</li>
                                <li>Pour les heures réalisées la Nuit mais hors Dimanche et hors Jour Férié : 75% de majoration pour toutes les heures réalisées</li>
                            </ul>
                        </div>
                    </div> 
                    <div class="row">
                        @if(auth()->user()->type != 'employee')
                        <div class="col-md-6 mb-3">
                            <label for="emp_id" class="form-label">{{ __('Employé') }} <span class="text-danger">*</span></label>
                            <select class="select2 form-select" id="emp_id" name="emp_id" required>
                                <option value="">{{ __('Sélectionner un employé') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }} {{$employee->get_brut_salary_base_sup($periode->id)}}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" value="" hidden>
                        </div>
                        @else
                            <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                        @endif
                        <div class="col-md-6 mb-3">
                            <label for="taux_hour" class="form-label">Salaire de base heure supplémentaire <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="brut" name="brut" value="">
                            <input type="number" class="form-control" id="taux_hour" name="taux_hour" value="" hidden>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">{{ __('Date et heure de début') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">{{ __('Date et heure de fin') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                                                
                        <div class="col-12">
                            <h6 class="mt-3 mb-3">{{ __('Détails des heures supplémentaires') }} - Taux horaire : <span id="taux_quar_heure" class="text-success">0.00</span> FCFA</h6>
                            
                            <div class="table-responsive mb-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th colspan="2">{{ __('Type d\'heure') }}</th>
                                            <th>{{ __('Nombre d\'heures') }}</th>
                                            <th>{{ __('Montant') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2">De la 41è à la 46è heure</td>
                                            <td>
                                                <input type="number" step="0.25" min="0" class="form-control form-control-sm" id="quar_heure" name="quar_heure" value="0">
                                            </td>
                                            <td id="montant_quar_heure">0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Au délà de la 46è heure</td>
                                            <td>
                                                <input type="number" step="0.25" min="0" class="form-control form-control-sm" id="heure_audd" name="heure_audd" value="0" oninput="calculateOvertime()">
                                            </td>
                                            <td id="montant_heure_audd">0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Nuit (autres jours que jours fériés et dimanche)</td>
                                            <td>
                                                <input type="number" step="0.25" min="0" class="form-control form-control-sm" id="heure_nuit_ferie" name="heure_nuit_ferie" value="0" oninput="calculateOvertime()">
                                            </td>
                                            <td id="montant_nuit_ferie">0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Journée dimanche et jours fériés</td>
                                            <td>
                                                <input type="number" step="0.25" min="0" class="form-control form-control-sm" id="heure_dim_ferie" name="heure_dim_ferie" value="0" oninput="calculateOvertime()">
                                            </td>
                                            <td id="montant_dim_ferie">0.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Nuit dimanche et jours fériés</td>
                                            <td>
                                                <input type="number" step="0.25" min="0" class="form-control form-control-sm" id="heure_nuit_dim_ferie" name="heure_nuit_dim_ferie" value="0" oninput="calculateOvertime()">
                                            </td>
                                            <td id="montant_nuit_dim_ferie">0.00</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="2"><strong>{{ __('Total majoration') }}</strong></td>
                                            <td id="total_heures">0.00</td>
                                            <td><strong id="montant_total">0.00</strong> FCFA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <input type="hidden" id="montant" name="montant" value="0">
                            <input type="hidden" name="month" value="{{ date('Y-m') }}">
                            
                            <div class="mb-3">
                                <label for="remark" class="form-label">{{ __('Remarques') }}</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Enregistrer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Remplacer l'écouteur d'événement actuel par celui-ci
        $('#emp_id').on('select2:select', function(e) {
            var selectedData = $(this).select2('data')[0]; // Récupère les données de l'option sélectionnée
            var selectedValue = selectedData.id; // La valeur complète de l'option
            var selectedEmployeeId = selectedValue.split(' ')[0];
            var selectedBrutSalary = selectedValue.split(' ')[1]; // Récupère le salaire brut
            
            // Mise à jour des champs
            $('#employee_id').val(selectedEmployeeId);
            $('#brut').val(Math.round(selectedBrutSalary));
            
            // Mise à jour de l'interface utilisateur
            var tauxHoraire = Math.round(parseInt(selectedBrutSalary) / 173.33) || 0;
            $('#taux_quar_heure').text(tauxHoraire);
            $('#taux_hour').val(tauxHoraire);
            
            // Recalculer les heures supplémentaires si nécessaire
            calculateOvertime();
        });
        
        // Mise à jour de la période en fonction des dates sélectionnées
        function updatePeriod() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            
            if (startDate && endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                
                // Vérifier si les dates sont valides
                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                    return;
                }
                
                // Formater la période (ex: "01/01/2023 - 31/01/2023")
                var formattedStart = start.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                var formattedEnd = end.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                
                $('#period').val(formattedStart + ' - ' + formattedEnd);
                
                // Calculer la durée en heures
                var diffInMs = end - start;
                var diffInHours = diffInMs / (1000 * 60 * 60);
                
                // Mettre à jour le champ d'heures supplémentaires si vide
                if (parseFloat($('#heure_audd').val()) === 0 && diffInHours > 0) {
                    $('#heure_audd').val(diffInHours.toFixed(2));
                }
                
                // Déclencher le calcul
                calculateOvertime();
            }
        }

        $('#quar_heure, #heure_audd, #heure_nuit_ferie, #heure_dim_ferie, #heure_nuit_dim_ferie').on('input', function() {
            calculateOvertime();
        });

        // Initialisation du calcul au chargement de la page
        calculateOvertime();
    });
    // Calcul automatique des heures et du montant
    function calculateOvertime() {
        var tauxHoraire = (parseInt($('#brut').val())/173.33) || 0;
        var quarHeure = parseFloat($('#quar_heure').val()) || 0;
        var heureAudd = parseFloat($('#heure_audd').val()) || 0;
        var heureNuitFerie = parseFloat($('#heure_nuit_ferie').val()) || 0;
        var heureDimFerie = parseFloat($('#heure_dim_ferie').val()) || 0;
        var heureNuitDimFerie = parseFloat($('#heure_nuit_dim_ferie').val()) || 0;
        var montantQuarHeure = 0;
        var montantHeureAudd = 0;
        var montantNuitFerie = 0;
        var montantDimFerie = 0;
        var montantNuitDimFerie = 0;
        
        // Calcul des taux
        var tauxQuarHeure = ((tauxHoraire * 15) / 100) * quarHeure;
        var tauxHeureAudd = ((tauxHoraire * 50) / 100) * heureAudd;
        var tauxNuitFerie = ((tauxHoraire * 75) / 100) * heureNuitFerie;
        var tauxDimFerie = ((tauxHoraire * 75) / 100) * heureDimFerie;
        var tauxNuitDimFerie = ((tauxHoraire * 100) / 100) * heureNuitDimFerie;
        
        // Calcul des montants
        if(tauxQuarHeure > 0){
            montantQuarHeure = tauxHoraire + tauxQuarHeure;
        }
        if(tauxHeureAudd > 0){
            montantHeureAudd = tauxHoraire + tauxHeureAudd;
        }
        if(tauxNuitFerie > 0){
            montantNuitFerie = tauxHoraire + tauxNuitFerie;
        }
        if(tauxDimFerie > 0){
            montantDimFerie = tauxHoraire + tauxDimFerie;
        }
        if(tauxNuitDimFerie > 0){
            montantNuitDimFerie = tauxHoraire + tauxNuitDimFerie;
        }
        
        // Calcul du total des heures et du montant total
        var totalHeures = quarHeure + heureAudd + heureNuitFerie + heureDimFerie + heureNuitDimFerie;
        var montantTotal = montantQuarHeure + montantHeureAudd + montantNuitFerie + montantDimFerie + montantNuitDimFerie;
        
        $('#montant_quar_heure').text(Math.round(montantQuarHeure).toLocaleString());
        $('#montant_heure_audd').text(Math.round(montantHeureAudd).toLocaleString());
        $('#montant_nuit_ferie').text(Math.round(montantNuitFerie).toLocaleString());
        $('#montant_dim_ferie').text(Math.round(montantDimFerie).toLocaleString());
        $('#montant_nuit_dim_ferie').text(Math.round(montantNuitDimFerie).toLocaleString());
        
        $('#total_heures').text(totalHeures);
        $('#montant_total').text(Math.round(montantTotal).toLocaleString());
        
        // Mise à jour des champs cachés
        $('#montant').val(montantTotal);
    }
</script>
@endpush
