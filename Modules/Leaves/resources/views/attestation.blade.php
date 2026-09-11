<?php

$logo ='';
$company_logo = '';

// Déterminer le titre de civilité en fonction du genre
$title = ($leave->employee->gender == 'Male') ? 'Monsieur' : (($leave->employee->gender == 'Female') ? 'Madame' : 'Monsieur/Madame');

?>
 
<!-- Container principal avec classe pour l'impression -->
<div class="modal-xl" role="document">
    <div class="modal-body">
         <!-- Boutons de sélection d'affichage - cachés à l'impression -->
         <div class="col-12 text-end mb-3 hide-on-print">
            <input type="button" value="{{ __('Avec logo') }}" class="btn btn-success btn-sm" id="showlogo">
            <input type="button" value="{{ __('Sans logo') }}" class="btn btn-warning btn-sm" id="hidelogo">
        </div>
        <div class="document-container" id="document-to-print" style="width: 100%; padding: 10px;">
            <!-- Contenu du document -->
            <div class="print-content">
                <!-- Zone avec logo - visible par défaut -->
                <div class="row" id="logoshow">
                    <div class="col-lg-12 text-md-end mb-2 hide-on-print" style="display: none;">
                        <button class="btn btn-sm btn-primary" id="downloadButtonLogo" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="{{ __('Download') }}" >
                            <span class="fa fa-download"></span>
                        </button>
                    </div>
                    <div class="row print-visible">
                        <div class="col-md-6" style="vertical-align: middle;" align="left">
                            <img src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo . '?' . time() : 'logo-dark.png' . '?' . time()) }}"
                                width="80px;" class="company-logo">
                        </div>
                        <div class="col-md-6 text-end">
                            <p>Abidjan le {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Zone sans logo - masquée par défaut -->
                <div id="logohide" style="display: none;">
                    <div class="col-lg-12 text-md-end mb-2 hide-on-print" style="display: none;">
                        <button class="btn btn-sm btn-primary" id="downloadButton" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="{{ __('Download') }}" onclick="downloadWithoutLogo()"><span class="fa fa-download"></span>
                        </button>
                    </div>
                    <div class="row print-visible">
                        <div class="col-12 text-end">
                            <p>Abidjan le {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="email" id="email" value="{{$leave->employee->email}}">
                    <input type="hidden" id="start_date" name="start_date" value="{{$leave->start_date}}">
                    <input type="hidden" id="back_date" name="back_date" value="{{$leave->end_date}}">
                    <input type="hidden" id="employee_id" name="employee_id" value="{{$leave->employee->id}}">
                </div>
                <!-- Destinataire aligné à droite avec titre conditionnel -->
                <div class="row">
                    <div class="col-12 text-end mb-5">
                        <p>À {{ $title }}<br>
                        <strong>{{ $leave->employee->name }}</strong></p>
                    </div>
                </div>

                <!-- Objet avec soulignement -->
                <div class="row">
                    <div class="col-12 mb-4">
                        <p><span style="text-decoration: underline;"><strong>Objet :</strong></span> Notification de départ en congés</p>
                    </div>
                </div>

                <!-- Corps du texte avec titre conditionnel -->
                <div class="row">
                    <div class="col-12">
                        <p>{{ $title }},</p>
                        <p>
                            Nous venons par la présente vous informer que vos congés pour l'année {{ date('Y') }} débuteront le
                            <strong>{{ \Carbon\Carbon::parse($leave->start_date ?? '2025-03-28')->locale('fr')->isoFormat('DD/MM/YYYY') }}</strong>
                            et prendront fin le <strong>{{ \Carbon\Carbon::parse($leave->end_date ?? '2025-05-07')->locale('fr')->isoFormat('DD/MM/YYYY') }}</strong>.
                            <!--La reprise du travail est donc fixée au lundi
                            <strong>{{ \Carbon\Carbon::parse($leave->end_date ?? '2025-05-07')->addDay()->locale('fr')->isoFormat('DD MMMM YYYY') }}</strong>. -->
                        </p>

                        <p class="mt-4">Nous vous prions d'agréer, {{ strtolower($title) }}, l'expression de nos salutations distinguées.</p>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <h5 align="center"><u>Décompte de l'allocation Congé</u></h5>
                    <div class="d-flex justify-content-center">
                        <table id="mois_table" class="table-sm table-bordered" width="70%">
                            <thead>
                                <tr>
                                    <th width="40%">Libellé</th>
                                    <th style="text-align: center;">Valeur</th>
                                    <th width="25%"></th>
                                </tr>
                            </thead>
                            <tbody id="mois_body">
                                <tr>
                                    <td>Part IGR</td>
                                    <td style="text-align: right;">{{$leave->employee->parts}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Date de retour du dernier congé</td>
                                    <td style="text-align: right;">{{ \Carbon\Carbon::parse($leave->leave_back)->locale('fr')->isoFormat('DD/MM/YYYY') }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Date de départ en congé</td>
                                    <td style="text-align: right;">{{ \Carbon\Carbon::parse($leave->start_date)->locale('fr')->isoFormat('DD/MM/YYYY') }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Période de référence</td>
                                    <td style="text-align: right;">{{ $leave->remark }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Nombre de jours de congé</td>
                                    <td style="text-align: right;">{{$leave->total_leave_days}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Date de reprise</td>
                                    <td style="text-align: right;">{{\Carbon\Carbon::parse($leave->end_date)->locale('fr')->isoFormat('DD/MM/YYYY')}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>SMM</td>
                                    <td style="text-align: right;"><span id="smm"></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Allocation congé brute</td>
                                    <td style="text-align: right;">{{number_format($leave->amount_leave ,0,'.',' ')}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">
                                        <p>Total :</p>
                                        <input type="hidden" name="total_salary_brut" id="total_salary_brut">
                                        <input type="hidden" name="cpte_salary_brut" id="cpte_salary_brut">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="mois_table" class="table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Mois</th>
                                    <th style="text-align: center;">Salaire Brut (SB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                    $total_jours = 0;
                                    $LeaveMonth = '';
                                    $salaires = !empty($leave->sb_leave) ? (array) json_decode($leave->sb_leave, true) : [];
                                    $jours = !empty($leave->days_leave) ? (array) json_decode($leave->days_leave, true) : [];
                                    $monthsLeave = !empty($leave->month_leave) ? (array) json_decode($leave->month_leave, true) : [];
                                @endphp

                                @foreach ($monthsLeave as $moisNum => $moisDate)
                                    @php
                                        // On ignore les mois marqués "N/A"
                                        if ($moisDate === 'N/A') continue;

                                        $montant = isset($salaires[$moisNum]) ? (int) $salaires[$moisNum] : 0;
                                        $dayswork = isset($jours[$moisNum]) ? (int) $jours[$moisNum] : 0;

                                        $total += $montant;
                                        $total_jours += $dayswork;
                                    @endphp

                                    <tr>
                                        <td style="text-align: right;">
                                            {{ \Carbon\Carbon::parse($moisDate)->translatedFormat('M-y'); }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($montant, 0, ',', ' ') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" style="text-align: right;">
                                        <p><strong><span id="total_jours_payés">{{ number_format($total, 0, '.', ' ')}}</span></strong></p>
                                        <input type="hidden" name="nb_mois_payés" value="{{ $total }}" id="nb_mois_payés">
                                        <input type="hidden" name="nb_jours_payés" value="{{ $total_jours }}" id="nb_jours_payés">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Signature avec espace pour le cachet -->
                <div class="row">
                    <div class="col-12 text-end">
                        <p style="text-decoration: underline;" class="mt-5">La Direction</p>
                        <!-- Espace pour le cachet -->
                        <div class="stamp-space">&nbsp;</div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action en pied de page - cachés à l'impression -->
            <div class="modal-footer hide-on-print">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                <button class="btn btn-sm btn-primary" onclick="printDocument()">
                    <i class="ti ti-printer"></i> {{ __('Imprimer') }}
                </button>
                <a href="#" class="btn btn-sm btn-info" id="downloadButtonIcon" onclick="downloadCurrentVersion()">
                    <i class="ti ti-file-download"></i> {{ __('Télécharger') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JavaScript -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Par défaut, afficher la version avec logo
        $('#logoshow').show();
        $('#logohide').hide();

        // Gérer l'affichage des div en fonction du clic sur les boutons
        $('#showlogo').click(function() {
            $('#logoshow').show();
            $('#logohide').hide();
            $(this).addClass('active');
            $('#hidelogo').removeClass('active');
        });

        $('#hidelogo').click(function() {
            $('#logoshow').hide();
            $('#logohide').show();
            $(this).addClass('active');
            $('#showlogo').removeClass('active');
        });

        // Initialiser les classes 'active'
        $('#showlogo').addClass('active');

        // Fonctions pour les boutons d'action
        window.saveAsPDF = function() {
            // Implémentation de la fonction saveAsPDF
            downloadCurrentVersion();
        };

        window.sendEmail = function() {
            // Implémentation de la fonction sendEmail
            var email = $('#email').val();
            alert('Envoi de l\'email à ' + email);
            // Votre code d'envoi d'email ici
        };

        var jours = $('#nb_jours_payés').val();
        var netpayes = $('#nb_mois_payés').val();
        document.getElementById('smm').innerHTML = Math.round((parseInt(netpayes)/parseInt(jours))*30).toLocaleString();;
    });

    // Fonction améliorée pour l'impression
    function printDocument() {
        window.print();
    }

    // Fonctions pour gérer les téléchargements
    function downloadWithLogo() {
        // Afficher un indicateur de chargement
        showLoading();

        // Préparation pour télécharger la version avec logo
        const originalContent = document.querySelector('#document-to-print').innerHTML;
        prepareForDownload(true);

        // Création d'une copie du contenu pour le PDF
        const element = document.querySelector('#document-to-print');

        // Configuration des options pour html2pdf
        const opt = {
            margin:       [15, 15, 15, 15],
            filename:     'attestation_conge_avec_logo.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Générer le PDF
        html2pdf().set(opt).from(element).save().then(function() {
            // Restaurer le contenu original
            document.querySelector('#document-to-print').innerHTML = originalContent;
            // Réinitialiser l'affichage
            resetAfterDownload();
            // Masquer l'indicateur de chargement
            hideLoading();
        }).catch(function(error) {
            console.error('Erreur lors de la génération du PDF:', error);
            alert('Une erreur est survenue lors de la génération du PDF. Veuillez réessayer.');
            // Restaurer le contenu original en cas d'erreur
            document.querySelector('#document-to-print').innerHTML = originalContent;
            resetAfterDownload();
            hideLoading();
        });
    }

    $('#downloadButtonLogo').on('click', async function () {
		// Récupérer l'élément avec id="print2"
		var print2Element = document.getElementById('document-to-print');

		try {
			// Charger les bibliothèques si elles ne sont pas déjà disponibles
			if (typeof jspdf === 'undefined') {
				await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
				await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js');
			}

			// Créer un nouveau document PDF
			const { jsPDF } = window.jspdf;
			var doc = new jsPDF('p', 'mm', 'a4');

			// Ajouter l'élément print2 au PDF si disponible
			if (print2Element) {
				await doc.html(print2Element, {
					startY: 15,
					margin: [35, 5, 5, 5],
					width: 160,
					windowWidth: print2Element.clientWidth,
					autoPaging: 'text',
					x: 0,
					y: 0,
					html2canvas: {
						scale: 0.19 // Ajuster l'échelle pour augmenter la police
					},
					callback: function (doc) {
						console.log('PDF généré avec succès');
					},
					async: true,
				});
			}

			// Sauvegarder le PDF
			doc.save(`attestation_conge_{{ $leave->employee->name }}.pdf`);

		} catch (error) {
			console.error('Erreur lors de la génération du PDF :', error);
			alert('Une erreur est survenue lors de la génération du PDF');
		}
	});

    function downloadWithoutLogo() {
        // Afficher un indicateur de chargement
        showLoading();

        // Préparation pour télécharger la version sans logo
        const originalContent = document.querySelector('#document-to-print').innerHTML;
        prepareForDownload(false);

        // Création d'une copie du contenu pour le PDF
        const element = document.querySelector('#document-to-print');

        // Configuration des options pour html2pdf
        const opt = {
            margin:       [15, 15, 15, 15],
            filename:     'attestation_conge_sans_logo.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Générer le PDF
        html2pdf().set(opt).from(element).save().then(function() {
            // Restaurer le contenu original
            document.querySelector('#document-to-print').innerHTML = originalContent;
            // Réinitialiser l'affichage
            resetAfterDownload();
            // Masquer l'indicateur de chargement
            hideLoading();
        }).catch(function(error) {
            console.error('Erreur lors de la génération du PDF:', error);
            alert('Une erreur est survenue lors de la génération du PDF. Veuillez réessayer.');
            // Restaurer le contenu original en cas d'erreur
            document.querySelector('#document-to-print').innerHTML = originalContent;
            resetAfterDownload();
            hideLoading();
        });
    }

    function downloadCurrentVersion() {
        // Déterminer quelle version est actuellement affichée et télécharger en conséquence
        if ($('#logoshow').is(':visible')) {
            downloadWithLogo();
        } else {
            downloadWithoutLogo();
        }
    }

    // Fonction pour préparer le document avant téléchargement
    function prepareForDownload(withLogo) {
        // Cacher tous les éléments non nécessaires pour le téléchargement
        const elementsToHide = document.querySelectorAll('.hide-on-print');
        elementsToHide.forEach(el => {
            el.style.display = 'none';
        });

        if (!withLogo) {
            // Si sans logo, s'assurer que le conteneur du logo est caché et le conteneur sans logo est visible
            document.querySelector('#logoshow').style.display = 'none';
            document.querySelector('#logohide').style.display = 'block';

            // S'assurer que seule la partie date est visible dans le conteneur sans logo
            const hideButtons = document.querySelectorAll('#logohide .hide-on-print');
            hideButtons.forEach(el => {
                el.style.display = 'none';
            });
        } else {
            // Si avec logo, s'assurer que le conteneur du logo est visible et le conteneur sans logo est caché
            document.querySelector('#logoshow').style.display = 'block';
            document.querySelector('#logohide').style.display = 'none';

            // S'assurer que seule la partie logo et date est visible dans le conteneur avec logo
            const hideButtons = document.querySelectorAll('#logoshow .hide-on-print');
            hideButtons.forEach(el => {
                el.style.display = 'none';
            });
        }
    }

    // Fonction pour réinitialiser l'affichage après téléchargement
    function resetAfterDownload() {
        // Réafficher les éléments cachés
        const elementsToShow = document.querySelectorAll('.hide-on-print');
        elementsToShow.forEach(el => {
            el.style.display = '';
        });

        // Réinitialiser l'affichage selon l'état actuel des boutons
        if ($('#showlogo').hasClass('active')) {
            $('#logoshow').show();
            $('#logohide').hide();
        } else if ($('#hidelogo').hasClass('active')) {
            $('#logoshow').hide();
            $('#logohide').show();
        } else {
            // Par défaut, afficher le logo
            $('#logoshow').show();
            $('#logohide').hide();
        }
    }

    // Fonctions pour afficher/masquer un indicateur de chargement
    function showLoading() {
        // Créer un indicateur de chargement s'il n'existe pas déjà
        if (!document.getElementById('loading-indicator')) {
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'loading-indicator';
            loadingDiv.style.position = 'fixed';
            loadingDiv.style.top = '0';
            loadingDiv.style.left = '0';
            loadingDiv.style.width = '100%';
            loadingDiv.style.height = '100%';
            loadingDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            loadingDiv.style.display = 'flex';
            loadingDiv.style.justifyContent = 'center';
            loadingDiv.style.alignItems = 'center';
            loadingDiv.style.zIndex = '9999';

            const loadingText = document.createElement('div');
            loadingText.textContent = 'Génération du PDF en cours...';
            loadingText.style.padding = '20px';
            loadingText.style.backgroundColor = '#fff';
            loadingText.style.borderRadius = '5px';
            loadingText.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.2)';

            loadingDiv.appendChild(loadingText);
            document.body.appendChild(loadingDiv);
        } else {
            document.getElementById('loading-indicator').style.display = 'flex';
        }
    }

    function hideLoading() {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    }
</script>
