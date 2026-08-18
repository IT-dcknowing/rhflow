@php
	$cate = 0;
@endphp
@foreach($categories as $cat)
	<?php  $cate = $cat->id; ?>
@endforeach

<div class="modal-body">
	<input type="hidden" name="id_poste" id="id_poste" value="{{$cate}}" > 
	<input type="hidden" name="id_secteur" id="id_secteur" value="{{$secteurs->id}}" >
	<div class="row">
		<div class="form-group col-md-12">
			<label class="form-label">Sélectionner la tranche dans laquelle vous voulez insérer votre catégorie : <span class="text-danger pl-1"> *</span></label>
			<div class="row">
				@php
					$lastCategoryId = null; // Initialiser la variable
				@endphp
				<div class="form-group col-md-6">
					<select type="text" name="category_job_id_a" class="form-select col-md-5" id="category_job_id_a"> 
						<option value="">-- Sélectionner une catégorie --</option>
						@foreach ($datas as $tables => $rowspan)
							@if ($rowspan->isNotEmpty())
								@foreach ($rowspan as $rowsp)
									<option value="{{ $rowsp->id }}" data-min-hourly-salary="{{ $rowsp->salaire_minima_horaire }}" data-min-monthly-salary="{{ $rowsp->salaire_minima_mensuel }}" @if(old('category_job_id_a') == $rowsp->id) selected="selected" @endif>
										Catégorie {{ $rowsp->categorie }}
									</option>
								@endforeach
								@php
									$lastCategoryId = $rowspan->last()->id; // Obtenez l'ID de la dernière catégorie dans cette boucle
								@endphp
							@endif
						@endforeach
					</select>
				</div>
				<div class="form-group col-md-6">
					<select type="text" name="category_job_id_b" class="form-select col-md-5" id="category_job_id_b"> 
						<option value="">-- Sélectionner une catégorie --</option>
						@foreach ($datas as $tables => $rowspan)
							@if ($rowspan->isNotEmpty())
								@foreach ($rowspan as $rowsp)
									<option value="{{ $rowsp->id }}" data-min-hourly-salary="{{ $rowsp->salaire_minima_horaire }}" data-min-monthly-salary="{{ $rowsp->salaire_minima_mensuel }}" @if(old('category_job_id_b') == $rowsp->id) selected="selected" @endif>
										Catégorie {{ $rowsp->categorie }}
									</option>
								@endforeach
							@endif
						@endforeach
					</select>
					<input type="hidden" name="idposte" value="{{ isset($rowspan) && $rowspan->isNotEmpty() ? $rowspan->last()->id : '' }}">
					<span id="error_message" style="color: red; display: none;">Vous ne pouvez pas sélectionner une catégorie au-delà de celle-ci</span>
				</div>
			</div>
		</div>
		<div class="form-group col-md-12" id="category_fields" style="display: none;">
			<div class="form-group">
				<label class="col-form-label">Nom de la catégorie suggérée</label>
				<input type="text" name="category_name" class="form-control" id="category_name" style="text-align: left;" readonly value="{{ old('category_name') }}">
			</div>
			<div class="row">
				<div class="form-group col-md-6">
					<label class="col-form-label">Salaire Catégoriel Horaire</label>
					<input type="number" name="salaire_horaire" class="form-control" id="salaire_horaire" value="{{ old('salaire_horaire') }}">
					<span id="error_message2" style="color: red; display: none;"></span>
				</div>
				<div class="form-group col-md-6">
					<label class="col-form-label">Salaire Catégoriel mensuel</label>
					<input type="number" name="salaire_mensuel" class="form-control" id="salaire_mensuel" value="{{ old('salaire_mensuel') }}">
					<span id="error_message3" style="color: red; display: none;"></span>
				</div>
			</div>
		</div>
		@foreach ($categories as $poste)
			@foreach ($datas as $table => $rows)
				@if ($rows->isNotEmpty() && $rows->first()->id_secteur == $poste->id_secteur)
					<input type="hidden" name="table_key" value="{{$table}}">
					@break
				@endif
			@endforeach
		@endforeach
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#category_job_id_a').change(function() {
			// Récupérer la valeur de la catégorie sélectionnée dans category_job_id_a
			var selectedCategoryId = $(this).val();

			// Vérifier si la catégorie sélectionnée est la dernière
			if (selectedCategoryId === '{{ $lastCategoryId }}') {
				// Afficher le message d'erreur
				$('#error_message').text("Vous ne pouvez pas sélectionner une catégorie au-delà de celle-ci.").show();

				// Désactiver la sélection dans category_job_id_b
				$('#category_job_id_b').prop('disabled', true);
			} else {
				// Cacher le message d'erreur
				$('#error_message').hide();

				// Activer la sélection dans category_job_id_b
				$('#category_job_id_b').prop('disabled', false);

				// Afficher la catégorie suivante dans category_job_id_b
				$('#category_job_id_b').val(parseInt(selectedCategoryId) + 1);
			}
		});

		$('#category_job_id_a').change(function() {
			var selectedCategoryId = $(this).val();

			// Désactiver toutes les options de la liste déroulante category_job_id_b
			$('#category_job_id_b option').prop('disabled', true);

			// Réactiver l'option par défaut (sélectionnez une catégorie)
			$('#category_job_id_b option[value=""]').prop('disabled', false);

			// Réactiver l'option correspondant à la catégorie suivante
			$('#category_job_id_b option[value="' + (parseInt(selectedCategoryId) + 1) + '"]').prop('disabled', false);
		});

		$('#category_job_id_a, #category_job_id_b').change(function() {
			var categoryIdA = $('#category_job_id_a').val();
			var categoryIdB = $('#category_job_id_b').val();

			// Afficher les champs une fois que les sélections ont été faites
			$('#category_fields').show();

			// Obtenez les valeurs minimales et maximales pour le salaire horaire et mensuel à partir des catégories sélectionnées
			var minHourlySalary = parseFloat($('#category_job_id_a option:selected').data('min-hourly-salary'));
			var maxHourlySalary = parseFloat($('#category_job_id_b option:selected').data('min-hourly-salary'));
			var minMonthlySalary = parseFloat($('#category_job_id_a option:selected').data('min-monthly-salary'));
			var maxMonthlySalary = parseFloat($('#category_job_id_b option:selected').data('min-monthly-salary'));

			// Limitez les valeurs saisies dans les champs de salaire horaire et mensuel
			$('#salaire_horaire').attr('min', minHourlySalary).attr('max', maxHourlySalary);
			$('#salaire_mensuel').attr('min', minMonthlySalary).attr('max', maxMonthlySalary);

			// Revalider les champs (ne rien afficher tant qu'ils sont vides)
			verifierSalaire('#salaire_horaire', '#error_message2', minHourlySalary, maxHourlySalary, "Le salaire horaire");
			verifierSalaire('#salaire_mensuel', '#error_message3', minMonthlySalary, maxMonthlySalary, "Le salaire mensuel");
		});

		// Vérifie un champ de salaire : masque le message si le champ est vide ou si la valeur est dans les bornes
		function verifierSalaire(champ, message, min, max, libelle) {
			var valeur = $(champ).val();

			if (valeur === '' || isNaN(min) || isNaN(max)) {
				$(message).hide();
				return;
			}

			valeur = parseFloat(valeur);

			if (valeur < min || valeur > max) {
				$(message).text(libelle + " doit être compris entre " + min + " et " + max + ".").show();
			} else {
				$(message).hide();
			}
		}

		// Revalider à chaque saisie dans les champs de salaire
		$('#salaire_horaire').on('input change', function() {
			verifierSalaire('#salaire_horaire', '#error_message2',
				parseFloat($('#category_job_id_a option:selected').data('min-hourly-salary')),
				parseFloat($('#category_job_id_b option:selected').data('min-hourly-salary')),
				"Le salaire horaire");
		});

		$('#salaire_mensuel').on('input change', function() {
			verifierSalaire('#salaire_mensuel', '#error_message3',
				parseFloat($('#category_job_id_a option:selected').data('min-monthly-salary')),
				parseFloat($('#category_job_id_b option:selected').data('min-monthly-salary')),
				"Le salaire mensuel");
		});
	});

	// Variables globales pour suivre l'état actuel des lettres et des chiffres
	var currentLetter = 'H';
	var currentNumber = 0;
	
	// Fonction pour générer un code à deux caractères commençant par la lettre H et passant à la lettre suivante lorsque H9 est atteint
	function generateRandomCode() {
		// Incrémenter le numéro actuel
		currentNumber++;
	
		// Si le numéro actuel atteint 9, passer à la lettre suivante et réinitialiser le numéro à 1
		if (currentNumber > 9) {
			currentLetter = String.fromCharCode(currentLetter.charCodeAt(0) + 1);
			currentNumber = 1;
		}
	
		// Retourner le code généré
		return currentLetter + currentNumber;
	}
	
	$(document).ready(function() {
		$('#category_job_id_a').change(function() {
			var selectedCategoryId = $(this).val();
			var categoryName = $('option:selected', this).text().trim(); // Supprimer les espaces indésirables
			
			// Supprimer le mot "Catégorie" du nom de la catégorie
			categoryName = categoryName.replace("Catégorie", "").trim();
	
			// Vérifier si la catégorie existe déjà
			if (categoryNameExist(categoryName)) {
				// Passer à la lettre suivante
				currentLetter = String.fromCharCode(currentLetter.charCodeAt(0) + 1);
				currentNumber = 0; // Réinitialiser le numéro à 0 pour commencer à partir de 1 pour la nouvelle lettre
			}
	
			// Générer un code aléatoire à deux caractères
			var randomCode = generateRandomCode();
	
			// Concaténer le nom de la catégorie avec le code aléatoire
			var suggestedCategoryName = categoryName + "-" + randomCode + "";
	
			// Afficher le nom de la catégorie suggérée dans le champ de texte
			$('#category_name').val(suggestedCategoryName);
		});
	});
	
	// Fonction pour vérifier si le nom de la catégorie existe déjà
	function categoryNameExist(name) {
		// Code pour vérifier si le nom de la catégorie existe déjà dans votre système
		// Retourner true si la catégorie existe, sinon false
	}
</script>




