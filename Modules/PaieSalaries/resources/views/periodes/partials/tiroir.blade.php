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
                        <span class="pm1-lbl">Temps de travail effectif <small class="text-muted">(base 30j calendaires)</small></span>
                        <span class="pm1-hint prorata" id="pm1DrawerProrata"></span>
                    </div>
                    @if($verrouille)
                        <span class="pm-days"><b class="pm-mono" id="pm1DrawerJours"></b> j</span>
                    @else
                        <span class="pm1-el-saisie">
                            <input type="number" class="pm1-champ" id="pm1DrawerJoursChamp" min="0" max="30" step="1"
                                aria-label="Temps de travail effectif (Jours)">
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
                                min="0" step="1" placeholder="Montant FCFA">
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
                    <button type="button" class="pm1-tr js-open-nested" data-theme="retenue">
                        <i class="fas fa-minus-circle"></i><span>Retenues sur salaire</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="pret">
                        <i class="fas fa-university"></i><span>Prêts et échéances</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="avantage">
                        <i class="fas fa-gift"></i><span>Avantages en nature</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="heures_sup">
                        <i class="fas fa-clock"></i><span>Heures supplémentaires</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="conge">
                        <i class="fas fa-umbrella-beach"></i><span>Congés</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="absence">
                        <i class="fas fa-user-clock"></i><span>Absences</span>
                    </button>
                    <button type="button" class="pm1-tr js-open-nested" data-theme="remboursement">
                        <i class="fas fa-receipt"></i><span>Remboursement de frais</span>
                    </button>
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
                <div class="pm1-card-line" id="pm1DrawerRembourseLigne" hidden>
                    <span>Remboursement de frais (gain)</span>
                    <b class="pm-mono pos" id="pm1DrawerRembourse"></b>
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
        <button type="button" class="btn pm1-fermer" id="pm1DrawerFermer">Terminer</button>
    </footer>

    {{-- Barre latérale coulissante secondaire (Nested Drawer) pour traiter la thématique --}}
    <div class="pm1-nested-drawer" id="pm1NestedDrawer" aria-hidden="true">
        <div class="pm1-nested-head">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" id="pm1NestedBack" title="Retour au détail du salarié">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div>
                    <span class="pm1-nested-badge" id="pm1NestedBadge">THÉMATIQUE</span>
                    <h3 class="pm1-nested-title" id="pm1NestedTitle">Formulaire</h3>
                </div>
            </div>
            <button type="button" class="pm1-drawer-close" id="pm1NestedClose" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="pm1-nested-body">
            <p class="pm1-nested-subtitle">
                Salarié : <strong id="pm1NestedEmpNom"></strong> <span class="badge bg-label-primary font-monospace ms-1" id="pm1NestedEmpMat"></span>
            </p>

            <form id="pm1NestedForm">
                <input type="hidden" id="pm1NestedTheme" name="theme" value="">
                <input type="hidden" id="pm1NestedEmpId" name="employee_id" value="">
                <input type="hidden" id="pm1NestedPeriodeId" name="periode_id" value="{{ $periode->id }}">

                {{-- Contenu dynamique selon la thématique cliquée --}}
                <div id="pm1NestedFormContent"></div>

                <div class="pm1-nested-footer mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2" id="pm1NestedSubmit">
                        <i class="fas fa-save me-1"></i>Enregistrer &amp; recalculer
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="pm1NestedCancel">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

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

        /* Sous-panneau latéral coulissant (Nested Drawer) pour traiter la thématique */
        .pm1-nested-drawer {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: #ffffff;
            z-index: 1055;
            transform: translateX(100%);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.12);
        }
        .pm1-nested-drawer.is-open {
            transform: translateX(0);
        }
        .pm1-nested-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #E8E8E6;
            background: #FAFAFA;
        }
        .pm1-nested-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            background: rgba(37, 62, 135, .1);
            color: #253e87;
            margin-bottom: 4px;
        }
        .pm1-nested-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1F1F1E;
        }
        .pm1-nested-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }
        .pm1-nested-subtitle {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #E8E8E6;
            font-size: 13px;
            color: #6B6B6B;
        }
        .pm1-nested-footer {
            padding-top: 16px;
            border-top: 1px solid #E8E8E6;
        }
        button.pm1-tr {
            cursor: pointer;
            text-align: left;
            width: 100%;
        }
    </style>
@endpush

{{-- Le tiroir embarque sa propre logique, dans son propre <script>. Une erreur
     de syntaxe ailleurs dans la page empêcherait tout un bloc de s'exécuter ;
     isolé ici, l'ouverture au clic reste opérante quoi qu'il arrive au reste. --}}
@push('scripts')
    <script>
        (function () {
            // Options dynamiques pour les formulaires des thématiques
            window.pm1TypesRetenues = @json($typesRetenues ?? []);
            window.pm1OptionsPrets = @json($optionsPrets ?? []);
            window.pm1TypesConges = @json($typesConges ?? []);
            window.pm1Periode = { id: {{ (int) $periode->id }}, debut: {!! json_encode($periode->date_debut) !!}, fin: {!! json_encode($periode->date_fin) !!} };

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

                // Refermer le sous-tiroir thématique s'il était ouvert
                fermerNestedDrawer();

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

                // Variables du mois : absences, heures supplémentaires, congés, retenues, etc.
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

                var ligneRemb = document.getElementById('pm1DrawerRembourseLigne');
                var valRemb = parseFloat(d.rembourseVal || 0) || 0;
                if (ligneRemb) {
                    ligneRemb.hidden = (valRemb <= 0);
                    remplir('pm1DrawerRembourse', '+' + (d.rembourse || '0') + ' FCFA');
                }

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

            // Bootstrap offcanvas
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
                fermerNestedDrawer();
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

            // =========================================================
            // GESTION DU SOUS-PANNEAU LATÉRAL COULISSANT (NESTED DRAWER)
            // =========================================================
            var nestedDrawer = document.getElementById('pm1NestedDrawer');
            var nestedForm = document.getElementById('pm1NestedForm');
            var nestedFormContent = document.getElementById('pm1NestedFormContent');
            var nestedTitle = document.getElementById('pm1NestedTitle');
            var nestedBadge = document.getElementById('pm1NestedBadge');
            var nestedThemeInput = document.getElementById('pm1NestedTheme');
            var nestedEmpIdInput = document.getElementById('pm1NestedEmpId');
            var nestedEmpNom = document.getElementById('pm1NestedEmpNom');
            var nestedEmpMat = document.getElementById('pm1NestedEmpMat');

            // Un <select> obligatoire sans option est une impasse : l'utilisateur ne
            // peut ni choisir, ni comprendre pourquoi. Plutôt que de le renvoyer vers
            // l'écran de configuration — ce qui lui ferait perdre le tiroir, le salarié
            // et sa saisie en cours — le référentiel se crée ici même, puis la liste se
            // recharge et le formulaire reprend son cours.
            function blocReferentielVide(libelle, cle) {
                var champJours = cle === 'conge'
                    ? '<div class="pm1-ref-jours">'
                        + '<label for="pm1RefJours">Jours accordés</label>'
                        + '<input type="number" id="pm1RefJours" class="form-control form-control-sm" min="0" max="365" value="0">'
                      + '</div>'
                    : '';

                return '<div class="pm1-ref-vide" data-referentiel="' + cle + '">'
                    + '<i class="fas fa-info-circle"></i>'
                    + '<div class="pm1-ref-corps">'
                        + '<strong>Aucun ' + libelle + ' n’est encore enregistré</strong>'
                        + '<p>Créez-en un ici pour continuer, sans quitter le tiroir.</p>'
                        + '<div class="pm1-ref-form">'
                            + '<div class="pm1-ref-nom">'
                                + '<label for="pm1RefNom">Nom du ' + libelle + '</label>'
                                + '<input type="text" id="pm1RefNom" class="form-control form-control-sm" maxlength="255" placeholder="Ex : ' + (cle === 'conge' ? 'Congé annuel' : 'Avance sur salaire') + '">'
                            + '</div>'
                            + champJours
                            + '<button type="button" class="btn btn-primary btn-sm" id="pm1RefCreer">'
                                + '<i class="fas fa-plus me-1"></i>Créer'
                            + '</button>'
                        + '</div>'
                        + '<span class="pm1-ref-erreur" id="pm1RefErreur" hidden></span>'
                    + '</div></div>';
            }

            // Création du référentiel depuis le tiroir, puis rechargement de la liste.
            function brancherCreationReferentiel(theme) {
                var bloc = nestedFormContent.querySelector('.pm1-ref-vide');
                if (!bloc) return;

                var cle = bloc.dataset.referentiel;
                var champNom = document.getElementById('pm1RefNom');
                var champJours = document.getElementById('pm1RefJours');
                var bouton = document.getElementById('pm1RefCreer');
                var erreur = document.getElementById('pm1RefErreur');

                function afficherErreur(message) {
                    if (!erreur) return;
                    erreur.textContent = message;
                    erreur.hidden = false;
                }

                function creer() {
                    var nom = (champNom && champNom.value || '').trim();
                    if (!nom) {
                        afficherErreur('Indiquez un nom.');
                        if (champNom) champNom.focus();
                        return;
                    }

                    if (erreur) erreur.hidden = true;
                    bouton.disabled = true;
                    bouton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Création…';

                    fetch(@json(route('company.paiesalaries.referentiels.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            referentiel: cle,
                            nom: nom,
                            jours: champJours ? (parseInt(champJours.value, 10) || 0) : null
                        })
                    })
                    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        if (!res.ok || !res.d.success) {
                            throw new Error(res.d.message || 'Création impossible.');
                        }

                        // La liste est alimentée côté navigateur : pas besoin de
                        // recharger la page pour que le <select> se remplisse.
                        if (cle === 'pret') {
                            window.pm1OptionsPrets = (window.pm1OptionsPrets || []).concat([res.d.option]);
                        } else if (cle === 'conge') {
                            window.pm1TypesConges = (window.pm1TypesConges || []).concat([res.d.option]);
                        }

                        if (window.toastr) {
                            toastr.success(res.d.message);
                        }

                        // Le panneau se redessine avec le vrai formulaire.
                        ouvrirNestedDrawer(theme);
                    })
                    .catch(function (e) {
                        afficherErreur(e.message || 'Création impossible.');
                        bouton.disabled = false;
                        bouton.innerHTML = '<i class="fas fa-plus me-1"></i>Créer';
                    });
                }

                if (bouton) bouton.addEventListener('click', creer);
                if (champNom) {
                    champNom.addEventListener('keydown', function (e) {
                        // Entrée valide la création sans soumettre le formulaire parent.
                        if (e.key === 'Enter') { e.preventDefault(); creer(); }
                    });
                    champNom.focus();
                }
            }

            function getRetenueFormHtml() {
                var opts = (window.pm1TypesRetenues || []).map(function (t) {
                    return '<option value="' + t.id + '">' + t.libelle + '</option>';
                }).join('');
                return `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type / Rubrique de retenue</label>
                        <select class="form-select" name="type_retenue_id">
                            <option value="">Sélectionner une rubrique (optionnel)</option>
                            ` + opts + `
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Libellé de la retenue <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="libelle" placeholder="Ex: Avance exceptionnelle, Retenue cantine..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant à retenir (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control font-monospace" name="amount" min="1" step="1" placeholder="Ex: 25000" required>
                    </div>
                `;
            }

            function getPretFormHtml() {
                var natures = window.pm1OptionsPrets || [];
                if (!natures.length) {
                    return blocReferentielVide('type de prêt', 'pret');
                }
                var opts = natures.map(function (o) {
                    return '<option value="' + o.id + '">' + o.name + '</option>';
                }).join('');
                return `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nature du prêt <span class="text-danger">*</span></label>
                        <select class="form-select" name="loan_option" required>
                            ` + opts + `
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Intitulé du prêt <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Ex: Prêt rentrée scolaire, Caution..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Montant total (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control font-monospace" id="nested_loan_amt" name="amount" min="1" step="1" placeholder="Ex: 300000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Durée (en mois) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="nested_loan_months" name="nbre_mois" min="1" max="120" value="6" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Échéance mensuelle (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control font-monospace" id="nested_loan_deduc" name="amount_deduc" min="1" step="1" placeholder="Ex: 50000" required>
                        <small class="text-muted">Calculée automatiquement selon le total et la durée.</small>
                    </div>
                `;
            }

            function getAvantageFormHtml() {
                return `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type d'avantage <span class="text-danger">*</span></label>
                        <select class="form-select" name="type_avantage" required>
                            <option value="avantage_en_nature">Avantage en nature (Logement, Véhicule, etc.)</option>
                            <option value="avantage_en_argent">Avantage en argent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description / Libellé <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="libelle" placeholder="Ex: Logement de fonction, Véhicule..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant réel estimé (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control font-monospace" name="amount" min="1" step="1" placeholder="Ex: 75000" required>
                    </div>
                `;
            }

            function getHeuresSupFormHtml(baseSalary) {
                var taux = Math.round(baseSalary / 173.33) || 1500;
                var debut = (window.pm1Periode && window.pm1Periode.debut) || '';
                var fin = (window.pm1Periode && window.pm1Periode.fin) || '';
                return `
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <i class="fas fa-info-circle me-1"></i> Taux horaire de base : <b id="nested_hs_taux_txt">` + formatMilliers(taux) + `</b> FCFA/h
                    </div>
                    <input type="hidden" name="taux_hour" id="nested_hs_taux" value="` + taux + `">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Début de période <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="start_date" value="` + debut + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Fin de période <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="end_date" value="` + fin + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Période limitée au mois : du ` + debut + ` au ` + fin + `</small>
                    <div class="table-responsive border rounded mb-3">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="table-light">
                                <tr><th>Majoration légale</th><th style="width:110px">Heures</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><small class="fw-bold">41è à 46è heure (+15%)</small></td>
                                    <td><input type="number" step="0.5" min="0" value="0" name="quar_heure" class="form-control form-control-sm nested-hs-champ"></td>
                                </tr>
                                <tr>
                                    <td><small class="fw-bold">Au-delà de 46h (+50%)</small></td>
                                    <td><input type="number" step="0.5" min="0" value="0" name="heure_audd" class="form-control form-control-sm nested-hs-champ"></td>
                                </tr>
                                <tr>
                                    <td><small class="fw-bold">Nuit semaine (+75%)</small></td>
                                    <td><input type="number" step="0.5" min="0" value="0" name="heure_nuit_ferie" class="form-control form-control-sm nested-hs-champ"></td>
                                </tr>
                                <tr>
                                    <td><small class="fw-bold">Dimanche / Férié jour (+75%)</small></td>
                                    <td><input type="number" step="0.5" min="0" value="0" name="heure_dim_ferie" class="form-control form-control-sm nested-hs-champ"></td>
                                </tr>
                                <tr>
                                    <td><small class="fw-bold">Dimanche / Férié nuit (+100%)</small></td>
                                    <td><input type="number" step="0.5" min="0" value="0" name="heure_nuit_dim_ferie" class="form-control form-control-sm nested-hs-champ"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light rounded border mb-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total à payer :</span>
                        <b class="fs-5 text-primary font-monospace" id="nested_hs_total">0 FCFA</b>
                    </div>
                    <input type="hidden" name="montant" id="nested_hs_montant" value="0">
                `;
            }

            function getCongeFormHtml() {
                var types = window.pm1TypesConges || [];
                if (!types.length) {
                    return blocReferentielVide('type de congé', 'conge');
                }
                var opts = types.map(function (c) {
                    return '<option value="' + c.id + '">' + c.title + '</option>';
                }).join('');
                var debut = (window.pm1Periode && window.pm1Periode.debut) || '';
                var fin = (window.pm1Periode && window.pm1Periode.fin) || '';
                return `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de congé <span class="text-danger">*</span></label>
                        <select class="form-select" name="leave_type_id" required>
                            ` + opts + `
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="nested_conge_start" value="` + debut + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="nested_conge_end" value="` + fin + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-2">Les dates doivent être comprises dans le mois de paie (` + debut + ` au ` + fin + `)</small>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de jours <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="days" id="nested_conge_days" min="1" max="31" value="1" required>
                        <small class="text-muted">Calculé automatiquement selon les dates (modifiable si besoin).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Allocation de congé (FCFA)</label>
                        <input type="number" class="form-control font-monospace" name="amount_leave" min="0" step="1" value="0" placeholder="0 si maintien de salaire">
                        <small class="text-muted">Laisser 0 si déjà inclus dans le salaire régulier.</small>
                    </div>
                `;
            }

            function getRemboursementFormHtml() {
                return `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Objet du remboursement <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="libelle" placeholder="Ex : Frais de mission, carburant, téléphone…" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control font-monospace" name="amount" min="1" step="1" placeholder="Ex : 25000" required>
                        <small class="text-muted">Versé en plus du salaire, sans cotisation ni impôt.</small>
                    </div>
                `;
            }

            function getAbsenceFormHtml() {
                var debut = (window.pm1Periode && window.pm1Periode.debut) || '';
                var fin = (window.pm1Periode && window.pm1Periode.fin) || '';
                return `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de départ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="nested_abs_start" value="` + debut + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de reprise <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="nested_abs_end" value="` + fin + `" min="` + debut + `" max="` + fin + `" required>
                        </div>
                    </div>
                    <small class="text-muted d-block mb-2">Les dates d'absence doivent être comprises dans le mois de paie (` + debut + ` au ` + fin + `)</small>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Heures d'absence <span class="text-danger">*</span></label>
                            <input type="number" class="form-control font-monospace" name="hours" min="0" step="0.5" value="8" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jours retenus <span class="text-danger">*</span></label>
                            <input type="number" class="form-control font-monospace" name="days" min="0" step="0.5" value="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Absence justifiée ? <span class="text-danger">*</span></label>
                        <select class="form-select" name="motif_justify" id="nested_abs_just">
                            <option value="Non">Non (injustifiée)</option>
                            <option value="Oui">Oui (permission autorisée)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="nested_abs_perm_bloc" style="display:none;">
                        <label class="form-label fw-bold">Permission exceptionnelle</label>
                        <select class="form-select" name="type_permis">
                            <option value="Mariage du travailleur : 04 jours ouvrables">Mariage du travailleur : 04 jours ouvrables</option>
                            <option value="Mariage d'un de ses enfants : 02 jours ouvrables">Mariage d'un de ses enfants : 02 jours ouvrables</option>
                            <option value="Mariage d'un frère, d'une sœur : 02 jours ouvrables">Mariage d'un frère, d'une sœur : 02 jours ouvrables</option>
                            <option value="Décès du conjoint : 05 jours ouvrables">Décès du conjoint : 05 jours ouvrables</option>
                            <option value="Décès d'un enfant, du père, de la mère : 05 jours ouvrables">Décès d'un enfant, du père, de la mère : 05 jours ouvrables</option>
                            <option value="Naissance d'un enfant : 02 jours ouvrables">Naissance d'un enfant : 02 jours ouvrables</option>
                            <option value="Autres">Autres</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motif / Commentaire</label>
                        <textarea class="form-control" name="remark" rows="2" placeholder="Détails du motif..."></textarea>
                    </div>
                `;
            }

            function ouvrirNestedDrawer(theme) {
                var empId = document.getElementById('pm1DrawerEnregistrer') ? document.getElementById('pm1DrawerEnregistrer').dataset.employeeId : null;
                var empNom = document.getElementById('pm1DrawerNom') ? document.getElementById('pm1DrawerNom').textContent : '';
                var empMat = document.getElementById('pm1DrawerMat') ? document.getElementById('pm1DrawerMat').textContent : '';
                var hote = document.getElementById('pm1DrawerElements');
                var champBase = hote ? hote.querySelector('.pm1-champ-base') : null;
                var baseSalary = champBase ? (parseFloat(champBase.value) || 0) : 0;

                if (!empId) return;

                nestedThemeInput.value = theme;
                nestedEmpIdInput.value = empId;
                nestedEmpNom.textContent = empNom;
                nestedEmpMat.textContent = empMat;

                var config = {
                    'retenue': {
                        title: 'Ajouter une retenue sur salaire',
                        badge: 'Retenue',
                        html: getRetenueFormHtml()
                    },
                    'pret': {
                        title: 'Ajouter un prêt ou une échéance',
                        badge: 'Prêt & Échéance',
                        html: getPretFormHtml()
                    },
                    'avantage': {
                        title: 'Ajouter un avantage en nature / argent',
                        badge: 'Avantage',
                        html: getAvantageFormHtml()
                    },
                    'heures_sup': {
                        title: 'Saisir des heures supplémentaires',
                        badge: 'Heures sup',
                        html: getHeuresSupFormHtml(baseSalary)
                    },
                    'conge': {
                        title: 'Poser un congé',
                        badge: 'Congé',
                        html: getCongeFormHtml()
                    },
                    'absence': {
                        title: 'Enregistrer une absence',
                        badge: 'Absence',
                        html: getAbsenceFormHtml()
                    },
                    'remboursement': {
                        title: 'Ajouter un remboursement de frais',
                        badge: 'Remboursement',
                        html: getRemboursementFormHtml()
                    }
                };

                var cur = config[theme];
                if (!cur) return;

                nestedTitle.textContent = cur.title;
                nestedBadge.textContent = cur.badge;
                nestedFormContent.innerHTML = cur.html;

                // Referentiel vide : le panneau n'affiche qu'une explication, il n'y a
                // rien a enregistrer. Le bouton de soumission serait trompeur.
                // Referentiel vide : le panneau n'affiche qu'un formulaire de creation,
                // il n'y a rien a enregistrer. On masque par la classe et non par
                // l'attribut hidden : Bootstrap impose display:inline-block sur .btn,
                // qui l'emporte sur la regle [hidden] du navigateur.
                var referentielVide = !!nestedFormContent.querySelector('.pm1-ref-vide');
                var boutonEnvoyer = document.getElementById('pm1NestedSubmit');
                var boutonAnnuler = document.getElementById('pm1NestedCancel');
                if (boutonEnvoyer) boutonEnvoyer.classList.toggle('d-none', referentielVide);
                if (boutonAnnuler) boutonAnnuler.classList.toggle('mt-2', !referentielVide);
                if (referentielVide) {
                    brancherCreationReferentiel(theme);
                }

                // Calculs automatiques
                if (theme === 'pret') {
                    var tot = document.getElementById('nested_loan_amt');
                    var mois = document.getElementById('nested_loan_months');
                    var ded = document.getElementById('nested_loan_deduc');
                    function calcDeduc() {
                        var t = parseFloat(tot.value) || 0;
                        var m = parseInt(mois.value, 10) || 1;
                        if (t > 0 && m > 0) {
                            ded.value = Math.round(t / m);
                        }
                    }
                    if (tot && mois && ded) {
                        tot.addEventListener('input', calcDeduc);
                        mois.addEventListener('input', calcDeduc);
                    }
                } else if (theme === 'heures_sup') {
                    var champsHs = nestedFormContent.querySelectorAll('.nested-hs-champ');
                    var txtTot = document.getElementById('nested_hs_total');
                    var inpTot = document.getElementById('nested_hs_montant');
                    var inpTaux = document.getElementById('nested_hs_taux');
                    var tauxH = parseFloat(inpTaux ? inpTaux.value : 0) || 1500;

                    function calcHs() {
                        var h15 = parseFloat(nestedFormContent.querySelector('[name="quar_heure"]')?.value) || 0;
                        var h50 = parseFloat(nestedFormContent.querySelector('[name="heure_audd"]')?.value) || 0;
                        var h75a = parseFloat(nestedFormContent.querySelector('[name="heure_nuit_ferie"]')?.value) || 0;
                        var h75b = parseFloat(nestedFormContent.querySelector('[name="heure_dim_ferie"]')?.value) || 0;
                        var h100 = parseFloat(nestedFormContent.querySelector('[name="heure_nuit_dim_ferie"]')?.value) || 0;

                        var tot = Math.round(
                            (h15 * tauxH * 1.15) +
                            (h50 * tauxH * 1.50) +
                            (h75a * tauxH * 1.75) +
                            (h75b * tauxH * 1.75) +
                            (h100 * tauxH * 2.00)
                        );
                        if (txtTot) txtTot.textContent = formatMilliers(tot) + ' FCFA';
                        if (inpTot) inpTot.value = tot;
                    }
                    champsHs.forEach(function (inp) {
                        inp.addEventListener('input', calcHs);
                    });
                } else if (theme === 'conge') {
                    var cDebut = document.getElementById('nested_conge_start');
                    var cFin = document.getElementById('nested_conge_end');
                    var cDays = document.getElementById('nested_conge_days');

                    function calcJoursConge() {
                        if (!cDebut || !cFin || !cDays) return;
                        var d1 = new Date(cDebut.value);
                        var d2 = new Date(cFin.value);
                        if (!isNaN(d1.getTime()) && !isNaN(d2.getTime())) {
                            if (d2 < d1) {
                                cFin.value = cDebut.value;
                                d2 = d1;
                            }
                            var diffMs = d2.getTime() - d1.getTime();
                            var diffJours = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                            cDays.value = Math.max(1, diffJours);
                        }
                    }

                    if (cDebut && cFin) {
                        cDebut.addEventListener('change', calcJoursConge);
                        cFin.addEventListener('change', calcJoursConge);
                        calcJoursConge();
                    }
                } else if (theme === 'absence') {
                    var aDebut = document.getElementById('nested_abs_start');
                    var aFin = document.getElementById('nested_abs_end');
                    var aDays = nestedFormContent.querySelector('[name="days"]');
                    var aHours = nestedFormContent.querySelector('[name="hours"]');

                    function calcJoursAbsence() {
                        if (!aDebut || !aFin || !aDays) return;
                        var d1 = new Date(aDebut.value);
                        var d2 = new Date(aFin.value);
                        if (!isNaN(d1.getTime()) && !isNaN(d2.getTime())) {
                            if (d2 < d1) {
                                aFin.value = aDebut.value;
                                d2 = d1;
                            }
                            var diffMs = d2.getTime() - d1.getTime();
                            var diffJours = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1;
                            var j = Math.max(1, diffJours);
                            aDays.value = j;
                            if (aHours) aHours.value = j * 8;
                        }
                    }

                    if (aDebut && aFin) {
                        aDebut.addEventListener('change', calcJoursAbsence);
                        aFin.addEventListener('change', calcJoursAbsence);
                        calcJoursAbsence();
                    }

                    var justSel = document.getElementById('nested_abs_just');
                    var permBloc = document.getElementById('nested_abs_perm_bloc');
                    if (justSel && permBloc) {
                        justSel.addEventListener('change', function () {
                            permBloc.style.display = justSel.value === 'Oui' ? 'block' : 'none';
                        });
                    }
                }

                if (nestedDrawer) {
                    nestedDrawer.classList.add('is-open');
                }
            }

            function fermerNestedDrawer() {
                if (nestedDrawer) {
                    nestedDrawer.classList.remove('is-open');
                }
            }

            // Écouteur pour ouvrir le sous-tiroir
            document.querySelectorAll('.js-open-nested').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var theme = btn.dataset.theme;
                    ouvrirNestedDrawer(theme);
                });
            });

            // Écouteurs de fermeture du sous-tiroir
            [document.getElementById('pm1NestedBack'), document.getElementById('pm1NestedClose'), document.getElementById('pm1NestedCancel')].forEach(function (el) {
                if (el) el.addEventListener('click', fermerNestedDrawer);
            });

            // Soumission du formulaire de la thématique
            if (nestedForm) {
                nestedForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var empId = document.getElementById('pm1NestedEmpId').value;
                    if (!empId) return;

                    var formData = new FormData(nestedForm);
                    var payload = {};
                    formData.forEach(function (v, k) {
                        payload[k] = v;
                    });

                    Swal.fire({
                        title: 'Enregistrement…',
                        allowOutsideClick: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    var urlAjout = "{{ route('company.paiesalaries.periodes.ajouter-variable', [$periode->id, '__EMPID__']) }}".replace('__EMPID__', empId);

                    fetch(urlAjout, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        if (!res.ok || !res.d.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: res.d.message || "L'enregistrement a échoué.",
                                confirmButtonColor: '#253e87'
                            });
                            return;
                        }

                        fermerNestedDrawer();
                        sessionStorage.setItem('pm1ReouvrirTiroir', String(empId));

                        Swal.fire({
                            icon: 'success',
                            title: 'Enregistré',
                            text: res.d.message,
                            timer: 900,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    })
                    .catch(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Le serveur ne répond pas.',
                            confirmButtonColor: '#253e87'
                        });
                    });
                });
            }

            // Gestionnaire de retrait pour TOUTES les variables (absence, overtime, conge, retenue, pret, avantage)
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.pm1-btn-retirer-variable');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();

                var type = btn.dataset.type;
                var id = btn.dataset.id;
                var empId = btn.dataset.employeeId || (document.getElementById('pm1DrawerEnregistrer') ? document.getElementById('pm1DrawerEnregistrer').dataset.employeeId : null);
                var libelle = btn.dataset.libelle || 'cet élément';

                if (!type || !id) return;

                // Un pret dont l'echeance n'est pas encore posee sur la periode ne peut
                // pas en etre « retire » : il n'y a rien a retirer. La seule action qui
                // ait un sens est de le sortir de la paie. Le dire avant, pas apres.
                var applique = btn.dataset.applique !== '0';
                var titreConfirmation = 'Retirer cet élément ?';
                var texteConfirmation = 'Voulez-vous retirer "' + libelle + '" pour ce salarié ?';

                if (type === 'pret' && !applique) {
                    titreConfirmation = 'Annuler ce prêt ?';
                    texteConfirmation = 'Aucune échéance de "' + libelle + '" n’est posée sur cette période. '
                        + 'Le retirer revient à annuler le prêt : il ne sera plus prélevé sur aucune période. '
                        + 'Les échéances déjà prélevées sont conservées.';
                }

                Swal.fire({
                    title: titreConfirmation,
                    text: texteConfirmation,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, retirer',
                    cancelButtonText: 'Annuler'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Retrait en cours…',
                        allowOutsideClick: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    var urlRetirer = "{{ route('company.paiesalaries.periodes.retirer-variable', [$periode->id, '__EMPID__']) }}".replace('__EMPID__', empId);

                    fetch(urlRetirer, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: type, id: id })
                    })
                    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        if (!res.ok || !res.d.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: res.d.message || "Le retrait a échoué.",
                                confirmButtonColor: '#253e87'
                            });
                            return;
                        }

                        if (empId) {
                            sessionStorage.setItem('pm1ReouvrirTiroir', String(empId));
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Élément retiré',
                            text: res.d.message,
                            timer: 800,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    })
                    .catch(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Le serveur ne répond pas.',
                            confirmButtonColor: '#253e87'
                        });
                    });
                });
            });

            // Délégation sur le corps du tableau plutôt qu'un écouteur par ligne :
            // un seul point d'attache, qui survit à un re-rendu des lignes et ne
            // dépend pas de l'ordre d'exécution des scripts de la page.
            var corpsTableau = document.getElementById('pmLignes');
            if (corpsTableau) {
                corpsTableau.addEventListener('click', function (evenement) {
                    if (evenement.target.closest('.pm1-check')) {
                        return;
                    }

                    var ligne = evenement.target.closest('tr.pm1-row');
                    if (!ligne) {
                        return;
                    }

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

            ['pm1DrawerJoursBtn', 'pm1DrawerAdd', 'pm1DrawerShow', 'pm1DrawerEdit', 'pm1DrawerBulletin']
                .forEach(function (id) {
                    var bouton = document.getElementById(id);
                    if (bouton) { bouton.addEventListener('click', fermerTiroir); }
                });

            // Enregistrer & recalculer : jours, salaire de base et montants des primes
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

                    var base = "{{ route('company.paiesalaries.periodes.enregistrer-salarie', [$periode->id, 0]) }}";

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
                            if (salarieId) {
                                sessionStorage.setItem('pm1ReouvrirTiroir', String(salarieId));
                            }
                            Swal.fire({
                                icon: 'success', title: 'Recalculé', text: res.d.message,
                                timer: 900, showConfirmButton: false
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

            // Suppression / retrait d'une prime depuis le tiroir
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.pm1-btn-retirer-prime');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();

                var allowanceId = btn.dataset.allowance;
                var empId = btn.dataset.employeeId || (document.getElementById('pm1DrawerEnregistrer') ? document.getElementById('pm1DrawerEnregistrer').dataset.employeeId : null);
                var libelle = btn.dataset.libelle || 'cette prime';

                if (!allowanceId) return;

                Swal.fire({
                    title: 'Retirer la prime ?',
                    text: 'Voulez-vous retirer "' + libelle + '" pour ce salarié ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, retirer',
                    cancelButtonText: 'Annuler'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Suppression…',
                        allowOutsideClick: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    var urlSuppr = @json(route('company.paiesalaries.allowance.destroy', ':id')).replace(':id', allowanceId);

                    fetch(urlSuppr, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                    .then(function (res) {
                        if (!res.ok || !res.d.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: res.d.message || "La suppression a échoué.",
                                confirmButtonColor: '#253e87'
                            });
                            return;
                        }

                        if (empId) {
                            sessionStorage.setItem('pm1ReouvrirTiroir', String(empId));
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Prime retirée',
                            text: res.d.message,
                            timer: 800,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    })
                    .catch(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Le serveur ne répond pas.',
                            confirmButtonColor: '#253e87'
                        });
                    });
                });
            });

            // Réouverture automatique du tiroir pour rester sur l'interface du salarié après ajout ou retrait de prime
            var reouvrirId = sessionStorage.getItem('pm1ReouvrirTiroir');
            if (reouvrirId) {
                sessionStorage.removeItem('pm1ReouvrirTiroir');
                setTimeout(function () {
                    var ligneCible = document.querySelector('tr.pm1-row[data-employee-id="' + reouvrirId + '"]');
                    if (ligneCible) {
                        document.querySelectorAll('tr.pm1-row').forEach(function (autre) {
                            autre.classList.remove('is-ouvert');
                        });
                        ligneCible.classList.add('is-ouvert');
                        try {
                            ouvrirTiroir(ligneCible);
                        } catch (err) {
                            console.error('Erreur réouverture tiroir :', err);
                        }
                    }
                }, 120);
            }

            // Rendu accessible au reste de la page
            window.pm1FermerTiroir = fermerTiroir;
            window.pm1OuvrirTiroirParId = function (empId) {
                var ligneCible = document.querySelector('tr.pm1-row[data-employee-id="' + empId + '"]');
                if (ligneCible) {
                    document.querySelectorAll('tr.pm1-row').forEach(function (autre) {
                        autre.classList.remove('is-ouvert');
                    });
                    ligneCible.classList.add('is-ouvert');
                    try {
                        ouvrirTiroir(ligneCible);
                    } catch (err) {
                        console.error('Erreur ouverture tiroir :', err);
                    }
                }
            };

            // Traitement direct depuis le tiroir des anomalies
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.js-traiter-anomalie-depuis-tiroir');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();

                var empId = btn.dataset.employeeId;
                if (!empId) return;

                var elAno = document.getElementById('pm1AnomaliesDrawer');
                if (elAno && window.bootstrap && window.bootstrap.Offcanvas) {
                    var bsAno = window.bootstrap.Offcanvas.getInstance(elAno);
                    if (bsAno) bsAno.hide();
                }

                setTimeout(function () {
                    window.pm1OuvrirTiroirParId(empId);
                }, 200);
            });
        })();
    </script>
@endpush
