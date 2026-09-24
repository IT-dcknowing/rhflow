{{-- Tiroir latéral de la vue hybride (modèle 1) : la « chirurgie » sur un salarié.

     Un seul tiroir dans le DOM, rempli en JavaScript depuis les data-* de la ligne
     cliquée. Rendre un tiroir par salarié alourdirait la page pour rien.

     Il n'édite rien lui-même : chaque bouton rouvre la modale qui servait déjà à
     cette action. Le calcul de paie reste donc exactement le même. --}}
<div class="offcanvas offcanvas-end pm1-drawer" tabindex="-1" id="pm1Drawer" aria-labelledby="pm1DrawerNom">

    <header class="pm1-drawer-head">
        <div>
            <div class="pm1-drawer-ident">
                <span class="pm1-drawer-mat pm-mono" id="pm1DrawerMat"></span>
                <h2 id="pm1DrawerNom"></h2>
            </div>
            <p class="pm1-drawer-sub">
                Département : <b id="pm1DrawerDept"></b><span id="pm1DrawerSitBloc"> • <b id="pm1DrawerSit"></b></span><span id="pm1DrawerAncBloc"> • Ancienneté : <b id="pm1DrawerAnc"></b></span>
            </p>
        </div>
        <button type="button" class="pm1-drawer-close" id="pm1DrawerClose" aria-label="Fermer le détail">
            <i class="fas fa-times"></i>
        </button>
    </header>

    <div class="pm1-drawer-body">


        {{-- Section 1 : présence et temps --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-clock"></i>Section 1 — Présence &amp; Temps (Pointeuse)</h3>
            <div class="pm1-card">
                <div class="pm1-card-row">
                    <div>
                        <span class="pm1-lbl">Jours travaillés (base 30j calendaires)</span>
                        <span class="pm1-hint prorata" id="pm1DrawerProrata"></span>
                    </div>
                    @if($verrouille)
                        <span class="pm-days"><b class="pm-mono" id="pm1DrawerJours"></b> j</span>
                    @else
                        <span class="pm1-el-saisie">
                            <input type="number" class="pm1-champ" id="pm1DrawerJoursChamp" min="0" max="30" step="1"
                                aria-label="Jours travaillés">
                            <small>jours</small>
                        </span>
                    @endif
                </div>
                <div class="pm1-card-line">
                    <span>Salaire de base proratisé :</span>
                    <b class="pm-mono" id="pm1DrawerBase"></b>
                </div>
            </div>
        </section>

        {{-- Section 2 : éléments, primes et retenues --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-coins"></i>Section 2 — Éléments, Primes &amp; Retenues</h3>
            {{-- Le détail ligne à ligne est recopié ici à l'ouverture du tiroir. --}}
            <div class="pm1-elements" id="pm1DrawerElements"></div>

            <div class="pm1-card pm1-el-totaux">
                <div class="pm1-card-line">
                    <span>Total primes et indemnités</span>
                    <b class="pm-mono pos" id="pm1DrawerPrimes"></b>
                </div>
                <div class="pm1-card-line">
                    <span>Total prêts et retenues</span>
                    <b class="pm-mono neg" id="pm1DrawerRetenues"></b>
                </div>
            </div>
            {{-- Ajout d'une prime sur place. Passer par la modale existante aurait
                 empilé une fenêtre sur le tiroir ; ici le formulaire est dans le
                 tiroir et poste sur le même point d'entrée que le traitement en
                 masse, avec un seul salarié. --}}
            @unless($verrouille)
                @if(($optionsPrimes ?? collect())->isNotEmpty())
                    <div class="pm1-prime-rapide">
                        <span class="pm1-actions-titre">Ajouter une prime</span>
                        <div class="pm1-prime-champs">
                            <label class="visually-hidden" for="pm1PrimeRubrique">Rubrique</label>
                            <select id="pm1PrimeRubrique" class="form-select form-select-sm">
                                @foreach($optionsPrimes as $option)
                                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                                @endforeach
                            </select>
                            <label class="visually-hidden" for="pm1PrimeMontantDirect">Montant</label>
                            <input type="number" id="pm1PrimeMontantDirect" class="form-control form-control-sm"
                                min="0" step="500" placeholder="Montant FCFA">
                            <button type="button" class="btn btn-primary btn-sm" id="pm1PrimeAjouter">
                                <i class="fas fa-plus me-1"></i>Ajouter
                            </button>
                        </div>
                        <span class="pm1-hint">Le traitement fiscal et social vient de la rubrique choisie.</span>
                    </div>
                @endif
            @endunless


        </section>

        {{-- Section 3 : les autres natures d'éléments. Chaque lien ouvre l'écran
             dédié, déjà filtré sur la période. Ces modules n'acceptent pas encore
             de filtre par salarié : le nom est rappelé ici pour ne pas le perdre
             en chemin. --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-layer-group"></i>Section 3 — Variables du Mois de <span id="pm1DrawerNom2"></span></h3>

            {{-- Valeurs du mois pour ce salarié, recopiées à l'ouverture du tiroir. --}}
            <div class="pm1-variables" id="pm1DrawerVariables"></div>

            @if($verrouille)
                <p class="pm1-el-vide">
                    Les bulletins de cette période sont déjà générés : les éléments ne
                    sont plus repris. Toute correction doit passer par une nouvelle période.
                </p>
            @else
                <span class="pm1-actions-titre pm1-traitement-titre">Saisir ou corriger</span>
                <div class="pm1-traitement">
                    <a class="pm1-tr" href="{{ route('company.paiesalaries.retenues.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-minus-circle"></i><span>Retenues sur salaire</span>
                    </a>
                    <a class="pm1-tr" href="{{ route('company.loans.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-university"></i><span>Prêts et échéances</span>
                    </a>
                    <a class="pm1-tr" href="{{ route('company.avantages.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-gift"></i><span>Avantages en nature</span>
                    </a>
                    <a class="pm1-tr" href="{{ route('company.times.overtime.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-clock"></i><span>Heures supplémentaires</span>
                    </a>
                    <a class="pm1-tr" href="{{ route('company.leaves.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-umbrella-beach"></i><span>Congés</span>
                    </a>
                    <a class="pm1-tr" href="{{ route('company.times.absences.index') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-user-clock"></i><span>Absences</span>
                    </a>
                </div>
            @endif
        </section>

        {{-- Section 4 : simulation du bulletin --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-calculator"></i>Section 4 — {{ $verrouille ? 'Détail du Bulletin' : 'Simulation du Bulletin' }}</h3>
            <div class="pm1-card pm1-simul">
                <div class="pm1-card-line">
                    <span>Salaire brut</span>
                    <b class="pm-mono" id="pm1DrawerBrut"></b>
                </div>
                <div class="pm1-card-line">
                    <span>Cotisations sociales (CNPS + CMU)</span>
                    <b class="pm-mono neg" id="pm1DrawerCotis"></b>
                </div>
                <div class="pm1-card-line">
                    <span>Impôt sur salaires (ITS)</span>
                    <b class="pm-mono neg" id="pm1DrawerImpot"></b>
                </div>
                <div class="pm1-card-line">
                    <span>Prêts et retenues</span>
                    <b class="pm-mono neg" id="pm1DrawerRetenues2"></b>
                </div>
                <div class="pm1-card-total">
                    <span>Net à payer</span>
                    <b class="pm-mono" id="pm1DrawerNet"></b>
                </div>
            </div>
        </section>

    </div>

    <footer class="pm1-drawer-foot">
        @unless($verrouille)
            <button type="button" class="btn btn-primary pm1-enregistrer" id="pm1DrawerEnregistrer">
                Enregistrer &amp; recalculer
            </button>
        @endunless
        <button type="button" class="btn btn-outline-secondary btn-aperçu-bulletin" id="pm1DrawerBulletin"
            data-bs-toggle="modal" data-bs-target="#showBulletinModal"
            data-employee-id="" data-employee-name=""
            data-exercice-id="{{ $periode->exercice_id }}" data-periode-id="{{ $periode->id }}">
            <i class="fas fa-file-invoice me-2"></i>Bulletin
        </button>
        <button type="button" class="btn pm1-fermer" id="pm1DrawerFermer">Fermer</button>
    </footer>

</div>


{{-- Le tiroir porte son propre style, comme il porte son propre script. Les
     valeurs viennent de Model1Drawer.tsx. Les sélecteurs sont préfixés par
     #pm1Drawer pour primer sur les règles globales du thème (h2, h3, p…),
     qui écrasaient sinon les tailles de police du panneau. --}}
@push('styles')
    <style>
        #pm1Drawer { width: 50%; max-width: none; border-left: 1px solid #E8E8E6; background: #fff; }
        @media (max-width: 992px) { #pm1Drawer { width: 100%; } }

        #pm1Drawer .pm1-drawer-head { display: flex; align-items: flex-start; justify-content: space-between;
            gap: 12px; padding: 24px; border-bottom: 1px solid #E8E8E6; }
        #pm1Drawer .pm1-drawer-ident { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        #pm1Drawer .pm1-drawer-mat { padding: 4px 10px; border-radius: 6px; background: rgba(37, 62, 135, .1);
            font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: #253e87; }
        #pm1Drawer h2 { margin: 0; font-size: 18px; font-weight: 700; color: #1F1F1E; line-height: 1.3; }
        #pm1Drawer .pm1-drawer-sub { margin: 4px 0 0; font-size: 12px; color: #6B6B6B; line-height: 1.5; }
        #pm1Drawer .pm1-drawer-sub b { font-weight: 700; color: #1F1F1E; }
        #pm1Drawer .pm1-drawer-close { padding: 6px 9px; border: 0; border-radius: 8px; background: none; color: #6B6B6B; }
        #pm1Drawer .pm1-drawer-close:hover { background: #F0F0EE; color: #1F1F1E; }

        #pm1Drawer .pm1-drawer-body { flex: 1; overflow-y: auto; padding: 24px;
            display: flex; flex-direction: column; gap: 24px; }

        #pm1Drawer .pm1-sec { display: block; }
        #pm1Drawer .pm1-sec h3 { display: flex; align-items: center; gap: 8px; margin: 0 0 10px;
            font-size: 11.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            color: #6B6B6B; line-height: 1.4; }
        #pm1Drawer .pm1-sec h3 i { font-size: 13px; color: #253e87; }

        #pm1Drawer .pm1-card { padding: 16px; border: 1px solid #E8E8E6; border-radius: 12px; background: #FAFAFA; }
        #pm1Drawer .pm1-card-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        #pm1Drawer .pm1-lbl { display: block; font-size: 12px; font-weight: 700; color: #6B6B6B; }
        #pm1Drawer .pm1-hint { display: block; margin-top: 2px; font-size: 11px; color: #6B6B6B; }
        #pm1Drawer .pm1-hint.prorata { color: #253e87; font-weight: 700; }
        #pm1Drawer .pm1-card-line { display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding-top: 10px; margin-top: 10px; border-top: 1px solid #E8E8E6;
            font-size: 12px; font-weight: 700; color: #6B6B6B; }
        #pm1Drawer .pm1-card > .pm1-card-line:first-child { padding-top: 0; margin-top: 0; border-top: 0; }
        #pm1Drawer .pm1-card-line b { font-family: var(--font-mono); font-size: 13px; color: #253e87; }
        #pm1Drawer .pm1-card-total { display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding-top: 12px; margin-top: 12px; border-top: 1px solid #E8E8E6;
            font-size: 13px; font-weight: 700; color: #253e87; }
        #pm1Drawer .pm1-card-total b { font-family: var(--font-mono); font-size: 17px; color: #253e87; }

        #pm1Drawer .pm1-elements, #pm1Drawer .pm1-variables { display: flex; flex-direction: column; gap: 8px; }
        #pm1Drawer .pm1-el-line, #pm1Drawer .pm1-var-line { display: flex; align-items: center;
            justify-content: space-between; gap: 12px; padding: 12px;
            border: 1px solid #E8E8E6; border-radius: 12px; background: #fff; }
        #pm1Drawer .pm1-el-line.socle { background: #FAFAFA; }
        #pm1Drawer .pm1-el-main, #pm1Drawer .pm1-var-main { min-width: 0; }
        #pm1Drawer .pm1-el-main b, #pm1Drawer .pm1-var-main b { display: block; font-size: 12px;
            font-weight: 700; color: #1F1F1E; }
        #pm1Drawer .pm1-el-meta, #pm1Drawer .pm1-var-meta { display: flex; align-items: center; gap: 6px;
            flex-wrap: wrap; margin-top: 4px; font-size: 10.5px; color: #6B6B6B; }
        #pm1Drawer .pm1-badge { padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        #pm1Drawer .pm1-badge.impot { background: #fef3c7; color: #92400e; }
        #pm1Drawer .pm1-badge.social { background: #dbeafe; color: #1e40af; }
        #pm1Drawer .pm1-badge.neutre { background: #f3f4f6; color: #4b5563; }
        #pm1Drawer .pm1-el-amt, #pm1Drawer .pm1-var-val { flex-shrink: 0; font-family: var(--font-mono);
            font-size: 13px; font-weight: 700; color: #1F1F1E; white-space: nowrap; }
        #pm1Drawer .pm1-el-amt.pos { color: #047857; }
        #pm1Drawer .pm1-el-amt.neg { color: #b91c1c; }
        #pm1Drawer .pm1-el-vide { margin: 0; padding: 14px; border: 1px dashed #E8E8E6; border-radius: 10px;
            font-size: 12px; color: #6B6B6B; text-align: center; }

        #pm1Drawer .pm1-el-saisie { display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0; }
        #pm1Drawer .pm1-el-saisie small { font-size: 10.5px; color: #6B6B6B; }
        #pm1Drawer .pm1-champ { padding: 6px 10px; border: 1px solid #E8E8E6; border-radius: 8px; background: #fff;
            font-family: var(--font-mono); font-size: 13px; font-weight: 700; text-align: right; color: #1F1F1E; }
        #pm1Drawer .pm1-champ:focus { outline: 0; border-color: #253e87; box-shadow: 0 0 0 3px rgba(37, 62, 135, .12); }
        #pm1Drawer .pm1-champ-base { width: 144px; }
        #pm1Drawer .pm1-champ-element { width: 112px; background: #FAFAFA; font-size: 12px; }
        #pm1Drawer #pm1DrawerJoursChamp { width: 80px; text-align: center; }
        #pm1Drawer .pm1-signe { font-size: 13px; font-weight: 700; }
        #pm1Drawer .pm1-signe.pos { color: #047857; }

        #pm1Drawer .pm1-prime-rapide { margin-top: 12px; padding: 12px; border: 1px solid rgba(37, 62, 135, .25);
            border-radius: 12px; background: rgba(37, 62, 135, .06); }
        #pm1Drawer .pm1-prime-champs { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) auto;
            gap: 8px; margin: 8px 0 6px; }
        @media (max-width: 560px) { #pm1Drawer .pm1-prime-champs { grid-template-columns: minmax(0, 1fr); } }
        #pm1Drawer .pm1-actions-titre { display: block; font-size: 11px; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; color: #6B6B6B; }
        #pm1Drawer .pm1-actions-elements { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin-top: 12px; }
        #pm1Drawer .pm1-traitement { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        @media (max-width: 640px) { #pm1Drawer .pm1-traitement { grid-template-columns: minmax(0, 1fr); } }
        #pm1Drawer .pm1-tr { display: flex; align-items: center; gap: 9px; padding: 11px 13px;
            border: 1px solid #E8E8E6; border-radius: 10px; background: #FAFAFA;
            font-size: 12px; font-weight: 700; color: #1F1F1E; text-decoration: none; }
        #pm1Drawer .pm1-tr:hover { border-color: rgba(37, 62, 135, .35); background: rgba(37, 62, 135, .06); color: #253e87; }
        #pm1Drawer .pm1-tr i { width: 15px; text-align: center; color: #253e87; }

        #pm1Drawer .pm1-drawer-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px;
            border: 1px solid rgba(37, 62, 135, .3); border-radius: 12px; background: rgba(37, 62, 135, .08); }
        #pm1Drawer .pm1-drawer-alert i { margin-top: 2px; color: #253e87; }
        #pm1Drawer .pm1-drawer-alert strong { display: block; font-size: 11px; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase; color: #253e87; }
        #pm1Drawer .pm1-drawer-alert p { margin: 2px 0 0; font-size: 12px; color: #1F1F1E; }

        #pm1Drawer .pm1-drawer-foot { display: flex; gap: 12px; padding: 24px; border-top: 1px solid #E8E8E6; background: #fff; }
        #pm1Drawer .pm1-enregistrer { flex: 1; padding: 12px; border-radius: 8px; background: #253e87;
            border-color: #253e87; color: #fff; font-weight: 700; font-size: 13px;
            letter-spacing: .06em; text-transform: uppercase; }
        #pm1Drawer .pm1-enregistrer:hover { background: #1c306d; border-color: #1c306d; color: #fff; }
        #pm1Drawer .pm1-fermer { flex: 0 0 auto; padding: 12px 24px; border-radius: 8px; background: #e5e5e5;
            color: #1F1F1E; font-weight: 700; font-size: 13px; text-transform: uppercase; }
        #pm1Drawer .pm1-fermer:hover { background: #d4d4d4; color: #1F1F1E; }
    </style>
@endpush

{{-- Le tiroir embarque sa propre logique, dans son propre <script>. Une erreur
     de syntaxe ailleurs dans la page empêcherait tout un bloc de s'exécuter ;
     isolé ici, l'ouverture au clic reste opérante quoi qu'il arrive au reste. --}}
@push('scripts')
    <script>
        (function () {
            // Tiroir latéral
            var tiroir = document.getElementById('pm1Drawer');

            function remplir(id, valeur) {
                var cible = document.getElementById(id);
                if (cible) cible.textContent = valeur;
            }

            function formatMilliers(n) {
                return (Math.round(n) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            }

            function recalculerProrataTiroir() {
                var champJours = document.getElementById('pm1DrawerJoursChamp');
                var hote = document.getElementById('pm1DrawerElements');
                var champBase = hote ? hote.querySelector('.pm1-champ-base') : null;

                var j = champJours ? parseInt(champJours.value, 10) : 30;
                if (isNaN(j) || j < 0) j = 0;

                var salaireContractuel = champBase ? (parseFloat(champBase.value) || 0) : 0;
                // 29 jours n'est pas un mois complet : proratisation (29/30)
                var estMoisComplet = (j === 30);
                var salaireProratise = estMoisComplet ? salaireContractuel : Math.round((salaireContractuel / 30) * j);

                remplir('pm1DrawerProrata', estMoisComplet
                    ? 'Mois complet (30/30 j)'
                    : 'Prorata appliqué : ' + j + '/30 j (' + (30 - j) + ' j d’écart)');

                remplir('pm1DrawerBase', formatMilliers(salaireProratise) + ' FCFA');
            }

            // Les gestionnaires des modales lisent $(this).data(...), qui met en
            // cache la valeur initiale de l'attribut. Écrire par setAttribute ne
            // suffirait donc pas : il faut aussi rafraîchir le cache jQuery.
            function cibler(id, employeId, nom) {
                var bouton = document.getElementById(id);
                if (!bouton) return;
                bouton.setAttribute('data-employee-id', employeId);
                bouton.setAttribute('data-employee-name', nom);
                if (window.jQuery) {
                    window.jQuery(bouton).data('employee-id', employeId).data('employee-name', nom);
                }
            }

            function ouvrirTiroir(ligne) {
                var d = ligne.dataset;

                remplir('pm1DrawerMat', d.matricule || '—');
                remplir('pm1DrawerNom', d.employeeName || '');
                remplir('pm1DrawerNom2', d.employeeName || 'ce salarié');
                remplir('pm1DrawerDept', d.dept || '—');
                remplir('pm1DrawerAnc', d.anciennete || '');
                remplir('pm1DrawerSit', d.situation || '');
                remplir('pm1DrawerJours', d.jours || '0');
                var champJours = document.getElementById('pm1DrawerJoursChamp');
                if (champJours) {
                    champJours.value = d.jours || '0';
                    champJours.oninput = recalculerProrataTiroir;
                    champJours.onchange = recalculerProrataTiroir;
                }
                remplir('pm1DrawerBase', (d.base || '0') + ' FCFA');
                // Détail des éléments : on recopie le bloc pré-rendu du salarié.
                var hote = document.getElementById('pm1DrawerElements');
                var source = document.getElementById('pm1El-' + d.employeeId);
                if (hote) {
                    hote.innerHTML = source
                        ? source.innerHTML
                        : '<p class="pm1-el-vide">Détail indisponible pour ce salarié.</p>';
                    var champBase = hote.querySelector('.pm1-champ-base');
                    if (champBase) {
                        champBase.oninput = recalculerProrataTiroir;
                        champBase.onchange = recalculerProrataTiroir;
                    }
                }

                // Variables du mois : absences, heures supplémentaires, congés.
                var hoteVar = document.getElementById('pm1DrawerVariables');
                var sourceVar = document.getElementById('pm1Var-' + d.employeeId);
                if (hoteVar) {
                    hoteVar.innerHTML = sourceVar ? sourceVar.innerHTML : '';
                }
                remplir('pm1DrawerPrimes', '+' + (d.primes || '0') + ' FCFA');
                remplir('pm1DrawerRetenues', '–' + (d.retenues || '0') + ' FCFA');
                remplir('pm1DrawerRetenues2', '–' + (d.retenues || '0') + ' FCFA');
                remplir('pm1DrawerBrut', (d.brut || '0') + ' FCFA');
                remplir('pm1DrawerCotis', '–' + (d.cotis || '0') + ' FCFA');
                remplir('pm1DrawerImpot', '–' + (d.impot || '0') + ' FCFA');
                remplir('pm1DrawerNet', (d.netFmt || '0') + ' FCFA');

                var blocAnc = document.getElementById('pm1DrawerAncBloc');
                if (blocAnc) blocAnc.hidden = !d.anciennete;
                var blocSit = document.getElementById('pm1DrawerSitBloc');
                if (blocSit) blocSit.hidden = !d.situation;

                recalculerProrataTiroir();


                ['pm1DrawerJoursBtn', 'pm1DrawerAdd', 'pm1DrawerShow', 'pm1DrawerEdit', 'pm1DrawerBulletin']
                    .forEach(function (id) { cibler(id, d.employeeId, d.employeeName); });

                var btnEnr = document.getElementById('pm1DrawerEnregistrer');
                if (btnEnr) { btnEnr.dataset.employeeId = d.employeeId; }

                // Le formulaire de prime rapide vise le salarié ouvert.
                var ajoutPrime = document.getElementById('pm1PrimeAjouter');
                if (ajoutPrime) {
                    ajoutPrime.dataset.employeeId = d.employeeId;
                    var champMontant = document.getElementById('pm1PrimeMontantDirect');
                    if (champMontant) { champMontant.value = ''; }
                }

                afficher();
            }

            // Bootstrap est chargé partout dans l'application et ses offcanvas y
            // fonctionnent : on s'appuie dessus plutôt que de gérer soi-même voile,
            // blocage du défilement et touche Échap. Repli manuel si jamais absent.
            function instance() {
                if (window.bootstrap && window.bootstrap.Offcanvas && tiroir) {
                    return window.bootstrap.Offcanvas.getOrCreateInstance(tiroir);
                }
                return null;
            }

            function afficher() {
                var bs = instance();
                if (bs) {
                    bs.show();
                    return;
                }
                if (tiroir) {
                    tiroir.classList.add('show');
                    tiroir.style.visibility = 'visible';
                }
            }

            function fermerTiroir() {
                var bs = instance();
                if (bs) {
                    bs.hide();
                    return;
                }
                if (tiroir) {
                    tiroir.classList.remove('show');
                    tiroir.style.visibility = '';
                }
            }

            // Délégation sur le corps du tableau plutôt qu'un écouteur par ligne :
            // un seul point d'attache, qui survit à un re-rendu des lignes et ne
            // dépend pas de l'ordre d'exécution des scripts de la page.
            var corpsTableau = document.getElementById('pmLignes');
            if (corpsTableau) {
                corpsTableau.addEventListener('click', function (evenement) {
                    // Une seule exception : la case à cocher, sans quoi la sélection
                    // deviendrait impossible. Tout le reste de la ligne ouvre le détail.
                    if (evenement.target.closest('.pm1-check')) {
                        return;
                    }

                    var ligne = evenement.target.closest('tr.pm1-row');
                    if (!ligne) {
                        return;
                    }

                    // La ligne de détail reste dans le tableau mais ne se déplie plus :
                    // elle sert de source au tiroir, qui en recopie le contenu. Afficher
                    // les deux en même temps brouillait l'écran.
                    document.querySelectorAll('tr.pm1-row').forEach(function (autre) {
                        autre.classList.remove('is-ouvert');
                    });
                    ligne.classList.add('is-ouvert');

                    try {
                        ouvrirTiroir(ligne);
                    } catch (erreur) {
                        console.error('Ouverture du détail salarié :', erreur);
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Détail indisponible',
                                text: "Le détail de ce salarié n'a pas pu s'ouvrir : " + erreur.message,
                                confirmButtonColor: '#253e87'
                            });
                        }
                    }
                });
            }

            // Fermeture : croix et bouton Fermer. Le voile et la touche Échap sont
            // pris en charge par l'offcanvas.
            [
                document.getElementById('pm1DrawerClose'),
                document.getElementById('pm1DrawerFermer')
            ].forEach(function (element) {
                if (element) { element.addEventListener('click', fermerTiroir); }
            });

            // Les actions qui ouvrent une modale effacent le tiroir : sans cela la
            // fenêtre Bootstrap se retrouverait empilée derrière lui.
            ['pm1DrawerJoursBtn', 'pm1DrawerAdd', 'pm1DrawerShow', 'pm1DrawerEdit', 'pm1DrawerBulletin']
                .forEach(function (id) {
                    var bouton = document.getElementById(id);
                    if (bouton) { bouton.addEventListener('click', fermerTiroir); }
                });

            // Enregistrer & recalculer : jours, salaire de base et montants des primes
            // partent ensemble, le serveur rejoue ensuite ancienneté et retenues légales.
            var boutonEnregistrer = document.getElementById('pm1DrawerEnregistrer');
            if (boutonEnregistrer) {
                boutonEnregistrer.addEventListener('click', function () {
                    var salarieId = boutonEnregistrer.dataset.employeeId;
                    if (!salarieId) { return; }

                    var hote = document.getElementById('pm1DrawerElements');
                    var champJours = document.getElementById('pm1DrawerJoursChamp');
                    var champBase = hote ? hote.querySelector('.pm1-champ-base') : null;

                    var elements = [];
                    if (hote) {
                        hote.querySelectorAll('.pm1-champ-element').forEach(function (champ) {
                            elements.push({
                                id: parseInt(champ.dataset.allowance, 10),
                                montant: parseInt(champ.value || '0', 10)
                            });
                        });
                    }

                    var charge = {
                        jours: parseInt((champJours && champJours.value) || '30', 10),
                        base: parseInt((champBase && champBase.value) || '0', 10),
                        elements: elements
                    };

                    Swal.fire({
                        title: 'Enregistrement…',
                        allowOutsideClick: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    var base = @json(route('company.paiesalaries.periodes.enregistrer-salarie', [$periode->id, 0]));

                    fetch(base.slice(0, -1) + salarieId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(charge)
                    })
                        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                        .then(function (res) {
                            if (!res.ok || !res.d.success) {
                                Swal.fire({
                                    icon: 'error', title: 'Non enregistré',
                                    text: res.d.message || "L'enregistrement a échoué.",
                                    confirmButtonColor: '#253e87'
                                });
                                return;
                            }
                            Swal.fire({
                                icon: 'success', title: 'Recalculé', text: res.d.message,
                                timer: 1400, showConfirmButton: false
                            }).then(function () { window.location.reload(); });
                        })
                        .catch(function () {
                            Swal.fire({
                                icon: 'error', title: 'Non enregistré',
                                text: 'Le serveur ne répond pas.',
                                confirmButtonColor: '#253e87'
                            });
                        });
                });
            }

            // Rendu accessible au reste de la page : le formulaire de prime rapide
            // a besoin de fermer le tiroir après enregistrement.
            window.pm1FermerTiroir = fermerTiroir;
        })();
    </script>
@endpush
