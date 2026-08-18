@php
	$cate = 0;
	$record = null;
	$tables = [
		'SecteurBatiment',
		'SecteurBatimentEmploye',
		'SecteurIndusAgent',
		'SecteurIndusCadre',
		'SecteurIndustrielEmploye',
		'SecteurIndustrielOuvrier',
		'SecteurIndusCauffeur',
		'SecteurAgriAutreChauffeur',
		'SecteurAgriAutreEmploye',
		'SecteurAgriAutreOuvrier',
		'SecteurAgriCrcChauffeur',
		'SecteurAgriCrcEmploye',
		'SecteurAgriCrcOuvrier',
		'SecteurAssurancesAgent',
		'SecteurAssurancesEmploye',
		'SecteurBanqueAgent',
		'SecteurBanqueEmploye',
		'SecteurBatimentAgent',
		'SecteurBatimentCadre',
		'SecteurBatimentChauffeur',
		'SecteurCommerceAgent',
		'SecteurCommerceCadre',
		'SecteurCommerceEmploye',
		'SecteurDockersEmploye', 
		'SecteurElevageChauffeur',
		'SecteurElevageEmploye',
		'SecteurElevageOuvrier',
		'SecteurForestierChauffeur',
		'SecteurForestierEmploye',
		'SecteurForestierOuvrier',
		'SecteurHotelleri',
		'SecteurHotellerieMaitrise',
		'SecteurHotellerisCadre',
		'SecteurIndustrielAgriAgent',
		'SecteurIndustrielAgriCadre',
		'SecteurIndustrielAgriChauffeur',
		'SecteurIndustrielAgriEmploye',
		'SecteurIndustrielAgriOuvrier',
		'SecteurIndustrielBoisAgent',
		'SecteurIndustrielBoisCadre',
		'SecteurIndustrielBoisChauffeur',
		'SecteurIndustrielBoisEmploye',
		'SecteurIndustrielBoisOuvrier',
		'SecteurIndustrielPolyAgent',
		'SecteurIndustrielPolyCadre',
		'SecteurIndustrielPolyEmploye',
		'SecteurIndustrielPolyOuvrier',
		'SecteurIndustrielSucreAgent',
		'SecteurIndustrielSucreCadre',
		'SecteurIndustrielSucreChauffeur',
		'SecteurIndustrielSucreEmploye',
		'SecteurIndustrielSucreOuvrier',
		'SecteurIndustrielTextAgent',
		'SecteurIndustrielTextCadre',
		'SecteurIndustrielTextChauffeur',
		'SecteurIndustrielTextEmploye',
		'SecteurIndustrielTextOuvrier',
		'SecteurIndustrielThonAgent',
		'SecteurIndustrielThonCadre',
		'SecteurIndustrielThonChauffeur',
		'SecteurIndustrielThonEmploye',
		'SecteurIndustrielThonOuvrier',
		'SecteurMaisonEmploye',
		'SecteurMaritimeCapit',
		'SecteurMaritimeChefMeca',
		'SecteurMaritimeMachine',
		'SecteurMaritimeMaitre',
		'SecteurMaritimeMatelo',
		'SecteurMaritimePoly',
		'SecteurMaritimeSecondCapit',
		'SecteurMaritimeSecondMeca',
		'SecteurNettoyageChauffeur',
		'SecteurNettoyageEmploye',
		'SecteurNettoyageOuvrier',
		'SecteurPechesBosc',
		'SecteurPechesBrevet',
		'SecteurPechesCapit',
		'SecteurPechesChefMoteur',
		'SecteurPechesCotiereBosco',
		'SecteurPechesCotiereCapi',
		'SecteurPechesCotiereCapisCapa',
		'SecteurPechesCotiereEleve',
		'SecteurPechesCotiereMatlotSimpl',
		'SecteurPechesCotiereMatlot',
		'SecteurPechesCotiereMeca',
		'SecteurPechesCotiereNoviece',
		'SecteurPechesCotiereSecondBoco',
		'SecteurPechesEleve',
		'SecteurPechesGraisseur',
		'SecteurPechesLargesBoscoElec',
		'SecteurPechesLargesCuisto',
		'SecteurPechesLargesEleve',
		'SecteurPechesLargesGraisse',
		'SecteurPechesLargesMatlot',
		'SecteurPechesLargesMatlotsSimple',
		'SecteurPechesLargesNovice',
		'SecteurPechesLargesOffPont',
		'SecteurPechesLargesSecondBosco',
		'SecteurPechesMecani',
		'SecteurPechesNovice',
		'SecteurPechesOffPon',
		'SecteurPechesSbrevet',
		'SecteurPechesSceonBosco',
		'SecteurPechesSconCapit',
		'SecteurPechesSconMeca',
		'SecteurPetroDistAgent',
		'SecteurPetroDistCadre',
		'SecteurPetroDistChauffeur',
		'SecteurPetroDistEmploye',
		'SecteurPetroProdAgent',
		'SecteurPetroProdCadre',
		'SecteurPetroProdChauffeur',
		'SecteurPetroProdEmploye',
		'SecteurPetroProdOuvrier',
		'SecteurSecuriteChauffeur',
		'SecteurSecuriteEmploye',
		'SecteurTourisme',
		'SecteurTourismsMatrise',
		'SecteurTourismsCadre',
		'SecteurTransportAgent',
		'SecteurTransportCadre',
		'SecteurTransportChauffeur',
		'SecteurTransportEmploye',
		'SecteurTransportOuvrier',
		'SecteurTrpsAerienAgent',
		'SecteurTrpsAerienCadre',
		'SecteurTrpsAerienCadresSup',
		'SecteurTrpsAerienOuvrier',
		'SecteurTrpsFondAgent',
		'SecteurTrpsFondCadre',
		'SecteurTrpsFondEmploye',
	];

	// Trouver l'enregistrement à modifier
	foreach ($tables as $table) {
		$model = app("App\\Models\\$table");
		$foundRecord = $model->find($id);
		if ($foundRecord && $foundRecord->company_id == Auth::user()->company_id) {
			$record = $foundRecord;
			$cate = $foundRecord->type_poste;
			break;
		}
	}
@endphp

<div class="modal-body">
	<input type="hidden" name="id_poste" id="id_poste" value="{{$cate}}" > 
	<input type="hidden" name="id_secteur" id="id_secteur" value="{{$secteurs->id}}" >
	<div class="row">
		<div class="form-group col-md-12">
			<label class="form-label">Nom de la catégorie <span class="text-danger pl-1"> *</span></label>
			<input type="text" name="category_name" class="form-control" id="category_name" value="{{ $record ? $record->categorie : old('category_name') }}" required>
		</div>
        <div class="form-group col-md-6">
            <label class="col-form-label">Salaire Catégoriel Horaire</label>
            <input type="number" name="salaire_horaire" class="form-control" id="salaire_horaire" value="{{ $record ? $record->salaire_minima_horaire : old('salaire_horaire') }}" required>
            <span id="error_message2" style="color: red; display: none;"></span>
        </div>
        <div class="form-group col-md-6">
            <label class="col-form-label">Salaire Catégoriel mensuel</label>
            <input type="number" name="salaire_mensuel" class="form-control" id="salaire_mensuel" value="{{ $record ? $record->salaire_minima_mensuel : old('salaire_mensuel') }}" required>
            <span id="error_message3" style="color: red; display: none;"></span>
        </div>
		<input type="hidden" name="table_key" value="{{ $record ? class_basename($record) : '' }}">
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#salaire_horaire, #salaire_mensuel').on('input', function() {
			var salaireHoraire = parseFloat($('#salaire_horaire').val()) || 0;
			var salaireMensuel = parseFloat($('#salaire_mensuel').val()) || 0;
			
			// Validation basique
			if (salaireHoraire < 0) {
				$('#error_message2').text("Le salaire horaire ne peut pas être négatif").show();
			} else {
				$('#error_message2').hide();
			}
			
			if (salaireMensuel < 0) {
				$('#error_message3').text("Le salaire mensuel ne peut pas être négatif").show();
			} else {
				$('#error_message3').hide();
			}
		});
	});
</script>
