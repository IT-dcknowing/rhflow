{{-- Tiroir latéral de la vue hybride (modèle 1) : la « chirurgie » sur un salarié.

     Un seul tiroir dans le DOM, rempli en JavaScript depuis les data-* de la ligne
     cliquée. Rendre un tiroir par salarié alourdirait la page pour rien.

     Il n'édite rien lui-même : chaque bouton rouvre la modale qui servait déjà à
     cette action. Le calcul de paie reste donc exactement le même. --}}
<div class="pm1-drawer-backdrop" id="pm1Backdrop" hidden></div>

<aside class="pm1-drawer" id="pm1Drawer" hidden aria-labelledby="pm1DrawerNom" role="dialog" aria-modal="true">

    <header class="pm1-drawer-head">
        <div>
            <div class="pm1-drawer-ident">
                <span class="pm1-drawer-mat pm-mono" id="pm1DrawerMat"></span>
                <h2 id="pm1DrawerNom"></h2>
            </div>
            <p class="pm1-drawer-sub">
                Département : <b id="pm1DrawerDept"></b><span id="pm1DrawerSitBloc"> · <b id="pm1DrawerSit"></b></span><span id="pm1DrawerAncBloc"> · Ancienneté : <b id="pm1DrawerAnc"></b></span>
            </p>
        </div>
        <button type="button" class="pm1-drawer-close" id="pm1DrawerClose" aria-label="Fermer le détail">
            <i class="fas fa-times"></i>
        </button>
    </header>

    <div class="pm1-drawer-body">

        {{-- Bandeau d'anomalie, affiché seulement si la ligne en porte une --}}
        <div class="pm1-drawer-alert" id="pm1DrawerAlerte" hidden>
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Anomalie détectée</strong>
                <p>Les jours travaillés diffèrent de la base de 30 jours calendaires : le salaire de base est proratisé.</p>
            </div>
        </div>

        {{-- Section 1 : présence et temps --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-clock"></i>Section 1 — Présence et temps</h3>
            <div class="pm1-card">
                <div class="pm1-card-row">
                    <div>
                        <span class="pm1-lbl">Jours travaillés (base 30 j calendaires)</span>
                        <span class="pm1-hint" id="pm1DrawerProrata"></span>
                    </div>
                    @if($verrouille)
                        <span class="pm-days"><b class="pm-mono" id="pm1DrawerJours"></b> j</span>
                    @else
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-update-days" id="pm1DrawerJoursBtn"
                            data-employee-id="" data-employee-name="" data-periode-id="{{ $periode->id }}">
                            <b class="pm-mono" id="pm1DrawerJours"></b> j <i class="fas fa-pen ms-1"></i>
                        </button>
                    @endif
                </div>
                <div class="pm1-card-line">
                    <span>Salaire de base retenu</span>
                    <b class="pm-mono" id="pm1DrawerBase"></b>
                </div>
            </div>
        </section>

        {{-- Section 2 : éléments, primes et retenues --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-coins"></i>Section 2 — Éléments, primes et retenues</h3>
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
            @unless($verrouille)
            <div class="pm1-actions">
                <span class="pm1-actions-titre">Primes et éléments de paie</span>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-add-elements" id="pm1DrawerAdd"
                    data-bs-toggle="modal" data-bs-target="#addElementsModal"
                    data-employee-id="" data-employee-name="" data-periode-id="{{ $periode->id }}">
                    <i class="fas fa-plus me-1"></i>Ajouter
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-show-elements" id="pm1DrawerShow"
                    data-bs-toggle="modal" data-bs-target="#showElementsModal"
                    data-employee-id="" data-employee-name="" data-periode-id="{{ $periode->id }}">
                    <i class="fas fa-eye me-1"></i>Afficher
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-elements" id="pm1DrawerEdit"
                    data-bs-toggle="modal" data-bs-target="#editElementsModal"
                    data-employee-id="" data-employee-name="" data-periode-id="{{ $periode->id }}">
                    <i class="fas fa-pen me-1"></i>Modifier
                </button>
            </div>
            @endunless
        </section>

        {{-- Section 3 : les autres natures d'éléments. Chaque lien ouvre l'écran
             dédié, déjà filtré sur la période. Ces modules n'acceptent pas encore
             de filtre par salarié : le nom est rappelé ici pour ne pas le perdre
             en chemin. --}}
        <section class="pm1-sec">
            <h3><i class="fas fa-layer-group"></i>Section 3 — Traiter la paie de <span id="pm1DrawerNom2"></span></h3>

            @if($verrouille)
                <p class="pm1-el-vide">
                    Les bulletins de cette période sont déjà générés : les éléments ne
                    sont plus repris. Toute correction doit passer par une nouvelle période.
                </p>
            @else
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
            <h3><i class="fas fa-calculator"></i>Section 4 — {{ $verrouille ? 'Détail du bulletin' : 'Simulation du bulletin' }}</h3>
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
        <button type="button" class="btn btn-primary btn-aperçu-bulletin" id="pm1DrawerBulletin"
            data-bs-toggle="modal" data-bs-target="#showBulletinModal"
            data-employee-id="" data-employee-name=""
            data-exercice-id="{{ $periode->exercice_id }}" data-periode-id="{{ $periode->id }}">
            <i class="fas fa-file-invoice me-2"></i>Voir le bulletin
        </button>
        <button type="button" class="btn btn-outline-secondary" id="pm1DrawerFermer">Fermer</button>
    </footer>

</aside>
