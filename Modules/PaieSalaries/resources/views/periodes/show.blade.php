@php
    // Compatibilité avec les versions de contrôleur antérieures au contrôle du pack.
    $hasPreviousBulletins = $hasPreviousBulletins ?? false;
    $isPackActive = $isPackActive ?? false;
    $packExpire = $hasPreviousBulletins && !$isPackActive;

    // Alertes automatiques
    $alerts = [];
    if (isset($activeLoans)) {
        foreach ($activeLoans as $loan) {
            $startDate = \Carbon\Carbon::parse($loan->start_date);
            $totalPaid = $loan->payments->sum('amount') ?? 0;
            for ($i = 0; $i < $loan->nbre_mois; $i++) {
                $paymentDate = (clone $startDate)->addMonths($i);
                $isPaid = $totalPaid >= ($i + 1) * $loan->amount_deduc;
                if (!$isPaid && $paymentDate->isPast()) {
                    $alerts[] = [
                        'type' => 'warning',
                        'message' => ($loan->employee->name ?? '?') . ' — prêt en retard (échéance ' . $paymentDate->format('d/m/Y') . ')'
                    ];
                    break;
                }
            }
        }
    }
    if (isset($conges)) {
        foreach ($conges as $conge) {
            $alerts[] = [
                'type' => 'info',
                'message' => ($conge->employee->name ?? '?') . ' — congé du ' . $conge->date_debut->format('d/m/Y') . ' au ' . $conge->date_fin->format('d/m/Y')
            ];
        }
    }

    // Étape de la paie, déduite du statut de la période et de ses bulletins
    $nbBulletins = $periode->bulletins_count;
    if (in_array($periode->statut, ['payee', 'cloture'])) {
        $etape = 'payee';
    } elseif ($periode->statut === 'annulee') {
        $etape = 'annulee';
    } elseif ($nbBulletins > 0) {
        $etape = 'payer';
    } else {
        $etape = 'preparer';
    }

    $fmt = function ($n) {
        return number_format((float) $n, 0, ',', ' ');
    };

    // « de » ou « d' » devant le nom du mois : d'avril, d'août, d'octobre
    $deMois = function ($libelle) {
        $libelle = lcfirst($libelle);
        return (preg_match('/^[aeiou]/i', $libelle) ? "d'" : 'de ') . $libelle;
    };

    $moisFr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $debutSuivant = $periode->date_fin->copy()->addDay();
    $nomSuivant = $moisFr[$debutSuivant->month - 1] . ' ' . $debutSuivant->year;

    // Éléments du mois (repris à l'ouverture, puis ajustés)
    $remboursements = $retenues->where('type_retenue_id', 5);
    $retenuesSalaire = $retenues->where('type_retenue_id', '!=', 5);
    $echeancesAppliquees = $loanPayments->where('applied', true);

    // Montants par salarié : mêmes calculs que la page « Calcul salaire »
    $lignes = [];
    $totalBrut = 0;
    $totalNet = 0;
    $aVerifier = 0;

    // Totaux de la vue hybride (modèle 1). Ce sont des sommes d'affichage :
    // aucune de ces valeurs n'entre dans le calcul du bulletin.
    $totalJours = 0;
    $totalBase = 0;
    $totalPrimes = 0;
    $totalCotis = 0;
    $totalImpot = 0;
    $nbAnomalies = 0;
    $nbConformes = 0;
    $nbEnAttente = 0;

    // Éléments de paie regroupés par salarié, pour le détail du tiroir.
    // Purement de la lecture : les collections sont déjà chargées par le
    // contrôleur, on ne fait que les trier et les mettre en forme.
    $elementsParSalarie = [];

    $poserElement = function ($employeeId, $sens, $libelle, $montant, $fiscal = null, $social = null, $note = null) use (&$elementsParSalarie) {
        if (!$employeeId) {
            return;
        }
        $elementsParSalarie[$employeeId][] = [
            'sens' => $sens,
            'libelle' => $libelle ?: 'Élément sans nom',
            'montant' => (float) $montant,
            'fiscal' => $fiscal,
            'social' => $social,
            'note' => $note,
        ];
    };

    foreach ($allowances as $element) {
        $poserElement($element->employee_id, '+', $element->title, $element->amount,
            $element->trait_fisc, $element->trait_cnps,
            $element->jours_work && $element->jours_work != 30 ? $element->jours_work . ' j' : null);
    }

    foreach ($avantages as $element) {
        $poserElement($element->employee_id, '+', $element->libelle, $element->amount_reel,
            $element->taxe_its, $element->taxe_cnps, 'Avantage en nature');
    }

    foreach ($retenues as $element) {
        $poserElement($element->employee_id, '–', $element->libelle, $element->amount, null, null, 'Retenue');
    }

    // Échéances de prêt : rendues ici aussi, pour que chaque salarié voie la sienne
    // dans son tiroir plutôt que d'aller la chercher dans le tableau de la période.
    foreach ($loanPayments as $echeance) {
        $poserElement(
            optional($echeance->loan)->employee_id,
            '–',
            'Prêt : ' . (optional($echeance->loan)->title ?: 'sans intitulé'),
            $echeance->amount,
            null,
            null,
            $echeance->applied
                ? 'Échéance appliquée'
                : 'Échéance non appliquée — à valider dans les éléments du mois'
        );
    }

    // La période est-elle encore modifiable ? Une fois les bulletins générés,
    // la grille passe en lecture seule mais garde exactement la même forme.
    $verrouille = $etape !== 'preparer';

    $ajouterLigne = function (array $ligne) use (&$lignes, &$totalBrut, &$totalNet, &$totalJours,
        &$totalBase, &$totalPrimes, &$totalCotis, &$totalImpot,
        &$nbAnomalies, &$nbConformes, &$nbEnAttente, &$aVerifier) {

        $ligne['verifier'] = $ligne['jours'] != 30;
        $ligne['primes'] = max(0, $ligne['brut'] - $ligne['base']);

        // « Anomalie » reprend la règle déjà en place (jours ≠ 30) ; « en attente »
        // signale un salarié dont rien n'a encore été calculé pour le mois.
        if ($ligne['verifier']) {
            $ligne['statut'] = 'anomaly';
            $nbAnomalies++;
        } elseif ($ligne['net'] <= 0) {
            $ligne['statut'] = 'pending';
            $nbEnAttente++;
        } else {
            $ligne['statut'] = 'ok';
            $nbConformes++;
        }

        $lignes[] = $ligne;
        $totalBrut += $ligne['brut'];
        $totalNet += $ligne['net'];
        $totalJours += $ligne['jours'];
        $totalBase += $ligne['base'];
        $totalPrimes += $ligne['primes'];
        $totalCotis += $ligne['cotis'];
        $totalImpot += $ligne['impot'];
        if ($ligne['verifier']) {
            $aVerifier++;
        }
    };

    if (!$verrouille) {

        // Période en préparation : les montants sont calculés en direct par les
        // accesseurs du modèle Employee, comme avant.
        foreach ($employees as $emp) {
            if ($emp->is_active != 1) {
                continue;
            }
            $jours = intval($emp->get_jours_work($periode->id));
            $joursRetenus = $jours > 0 ? $jours : 30;

            $ajouterLigne([
                'emp' => $emp,
                'jours' => $joursRetenus,
                'base' => $joursRetenus == 30 ? $emp->salary : round(($emp->salary / 30) * $joursRetenus),
                'brut' => $emp->get_brut_salary($periode->id),
                'net' => $emp->get_net_salary($periode->id),
                'pret' => $emp->get_loan_retenue($periode->id),
                'autre' => $emp->get_Autre_retenue($periode->id),
                'rembourse' => $emp->get_Rembourssement($periode->id),
                'dept' => $emp->department->name ?? '—',
                'anciennete' => $emp->get_Anciennete(),
                'situation' => trim($emp->get_Situation() . ' (' . $emp->get_Nombre_parts() . ' part' . ($emp->get_Nombre_parts() > 1 ? 's' : '') . ')'),
                'cotis' => $emp->get_cnps_sal($periode->id) + $emp->get_cmu_sal($periode->id),
                'impot' => $emp->get_imp_net($periode->id),
                'salaire' => $emp->salary,
            ]);
        }

    } else {

        // Bulletins générés : on lit ce qui a été figé, jamais un recalcul. Les
        // retenues sont stockées en JSON sur le bulletin, réparties par code :
        // 301 retraite CNPS et 302 CMU pour les cotisations salariales, 403 pour
        // l'impôt net, 500 pour les prêts, le reste en retenues diverses.
        foreach ($periode->bulletins()->with('employee.department')->get() as $bulletin) {
            $emp = $bulletin->employee;
            if (!$emp) {
                continue;
            }

            // Période verrouillée : le détail du tiroir vient du bulletin figé, pas
            // des éléments courants, qui ont pu bouger depuis la génération.
            $elementsParSalarie[$emp->id] = [];
            foreach (json_decode($bulletin->allowances, true) ?: [] as $gain) {
                $poserElement($emp->id, '+', $gain['title'] ?? null, $gain['amount'] ?? 0,
                    $gain['trait_fisc'] ?? null, $gain['trait_cnps'] ?? null, null);
            }

            $cotis = 0; $impot = 0; $pret = 0; $autre = 0;
            foreach (json_decode($bulletin->retenues, true) ?: [] as $poste) {
                if (empty($poste['salariale']) || $poste['salariale'] == '0') {
                    continue;
                }
                $code = (string) ($poste['code'] ?? '');
                $montant = (float) ($poste['amount'] ?? 0);

                if (in_array($code, ['301', '302'], true)) {
                    $cotis += $montant;
                } elseif ($code === '403') {
                    $impot += $montant;
                } elseif ($code === '500') {
                    $pret += $montant;
                } elseif (!in_array($code, ['401', '402'], true)) {
                    $autre += $montant;
                }

                if ($montant > 0) {
                    $poserElement($emp->id, '–', $poste['libelle'] ?? null, $montant, null, null, 'Code ' . $code);
                }
            }

            $ajouterLigne([
                'emp' => $emp,
                'bulletin' => $bulletin,
                'jours' => (int) $bulletin->nbre_jour,
                'base' => (float) $bulletin->basic_salary,
                'brut' => (float) $bulletin->salary_brut,
                'net' => (float) $bulletin->net_payble,
                'pret' => $pret,
                'autre' => $autre,
                'rembourse' => 0,
                'dept' => $emp->department->name ?? '—',
                'anciennete' => $bulletin->anciennete_emp ?: $emp->get_Anciennete(),
                'situation' => trim(($bulletin->situation_emp ?: $emp->get_Situation()) . ' (' . ($bulletin->parts_emp ?: $emp->get_Nombre_parts()) . ' part' . (($bulletin->parts_emp ?: $emp->get_Nombre_parts()) > 1 ? 's' : '') . ')'),
                'cotis' => $cotis,
                'impot' => $impot,
                // basic_salary du bulletin est déjà proratisé : on remonte au salaire
                // plein pour pouvoir afficher « salaire × jours/30 » comme ailleurs.
                'salaire' => ((int) $bulletin->nbre_jour) > 0 && ((int) $bulletin->nbre_jour) != 30
                    ? round(((float) $bulletin->basic_salary) * 30 / ((int) $bulletin->nbre_jour))
                    : (float) $bulletin->basic_salary,
            ]);
        }
    }

    // Documents de la paie (module Déclarations), période déjà sélectionnée
    $documents = [];
    if (isModuleActive('declaration')) {
        $paramsPeriode = ['exercice_id' => $periode->exercice_id, 'periode_id' => $periode->id];
        $documents = [
            ['fa-download', 'Télécharger les bulletins', 'Liste des bulletins de la période, téléchargement global en PDF', route('company.declarations.resume.index', $paramsPeriode)],
            ['fa-book', 'Livre de paie mensuel', 'Récapitulatif par salarié et par rubrique', route('company.declarations.livrepaie.mensuel')],
            ['fa-file-alt', 'État des cotisations', 'Cotisations sociales et fiscales de la période', route('company.declarations.cotisation.index', $paramsPeriode)],
            ['fa-file-code', 'Déclarations mensuelles', 'Fichiers de déclaration du mois', route('company.declarations.declaration.mensuelle')],
        ];
    }
@endphp

@extends('layouts.app')

@section('title', 'Paie : ' . $periode->nom)

@include('paiesalaries::periodes.partials.styles')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="pm">

            {{-- ===== EN-TÊTE ===== --}}
            <div class="pm-head">
                <div>
                    <div class="pm-eyebrow">Paie {{ $periode->type_periode }} · Exercice {{ $periode->exercice->nom }}</div>
                    <div class="pm-title">
                        <h1>{{ $periode->nom }}</h1>
                        @if($etape === 'payee')
                            <span class="pm-chip ok"><i class="fas fa-check"></i>{{ $periode->statut === 'cloture' ? 'Clôturée' : 'Payée' }}</span>
                        @elseif($etape === 'annulee')
                            <span class="pm-chip bad">Annulée</span>
                        @elseif($etape === 'payer')
                            <span class="pm-chip navy">Bulletins générés</span>
                        @else
                            <span class="pm-chip warn">En préparation</span>
                        @endif
                    </div>
                    <div class="pm-meta">
                        <span class="pm-mono"><i class="fas fa-calendar-alt"></i>{{ $periode->date_debut->format('d/m/Y') }} → {{ $periode->date_fin->format('d/m/Y') }}</span>
                        @if($periode->date_paiement)
                            <span>Paiement {{ $etape === 'payee' ? 'le' : 'prévu le' }} <span class="pm-mono">{{ $periode->date_paiement->format('d/m/Y') }}</span></span>
                        @endif
                        <span><i class="fas fa-users"></i>{{ $etape === 'preparer' ? count($lignes) . ' salarié(s)' : $nbBulletins . ' bulletin(s)' }}</span>
                    </div>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Plus
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('company.paiesalaries.periodes.edit', $periode->id) }}">
                            <i class="fas fa-edit me-2"></i>Modifier la période
                        </a>
                        <a class="dropdown-item" href="{{ route('company.paiesalaries.calcule') }}?periode_id={{ $periode->id }}">
                            <i class="fas fa-calculator me-2"></i>Aperçu détaillé des salaires
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('company.paiesalaries.exercices.show', $periode->exercice_id) }}">
                            <i class="fas fa-history me-2"></i>Périodes de l'exercice
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== ÉTAPES ===== --}}
            @if($etape !== 'annulee')
                {{-- ============================================================
                     VUE HYBRIDE (modèle 1) — la grille EST l'écran de paie.

                     Plus de bascule « Préparer / Vérifier » : le contrôle se fait
                     dans la grille elle-même, anomalie par anomalie, et la
                     génération des bulletins part du bandeau d'audit. Les saisies
                     annexes (éléments du mois, variables, échéances de prêt) restent
                     accessibles sous la grille, repliées par défaut.
                     ============================================================ --}}
                <div class="pm">

                    @if($verrouille)
                        <div class="pm-banner {{ $etape === 'payee' ? 'ok' : 'navy' }}">
                            <i class="fas fa-{{ $etape === 'payee' ? 'check-circle' : 'file-invoice' }}"></i>
                            <div>
                                @if($etape === 'payee')
                                    <b>Paie {{ $deMois($periode->nom) }} {{ $periode->statut === 'cloture' ? 'clôturée' : 'payée' }}@if($periode->date_paiement) le {{ $periode->date_paiement->format('d/m/Y') }}@endif</b>
                                    <p>{{ count($lignes) }} bulletin(s) · net versé <span class="pm-mono">{{ $fmt($totalNet) }} FCFA</span>. La grille ci-dessous est figée sur les bulletins générés.</p>
                                @else
                                    <b>{{ count($lignes) }} bulletin(s) générés</b>
                                    <p>La grille est figée sur les bulletins. Validez le paiement une fois les virements effectués.</p>
                                @endif
                            </div>
                        </div>
                    @endif


                    <section class="pm-panel pm1" id="pm-salaries">
                        <div class="pm-panel-head">
                            <div>
                                <h2>{{ $verrouille ? 'Bulletins de la période' : 'Éléments par salarié' }}</h2>
                                <p>
                                    Cliquez sur une ligne pour ouvrir son détail. Cochez plusieurs lignes pour en voir le cumul.
                                    @if($verrouille)<b>Période verrouillée : les montants ne sont plus modifiables.</b>@endif
                                </p>
                            </div>
                        </div>

                        {{-- Barre de traitement de masse : n'apparaît qu'une fois des lignes cochées --}}
                        <div class="pm1-mass" id="pm1Mass" hidden>
                            <span class="pm1-mass-count"><b id="pm1MassCount">0</b> salarié(s) sélectionné(s)</span>
                            <span class="pm1-mass-sum">Net cumulé <b class="pm-mono" id="pm1MassNet">0</b> FCFA</span>
                            @unless($verrouille)
                                @if(($optionsPrimes ?? collect())->isNotEmpty())
                                    <button type="button" class="pm1-mass-act" data-action="prime">
                                        <i class="fas fa-gift"></i>Prime…
                                    </button>
                                @endif
                                <button type="button" class="pm1-mass-act" data-action="base">
                                    <i class="fas fa-money-bill"></i>Salaire de base…
                                </button>
                                <button type="button" class="pm1-mass-act" data-action="jours">
                                    <i class="fas fa-calendar-day"></i>Jours travaillés…
                                </button>
                            @endunless
                            <button type="button" class="pm1-mass-clear" id="pm1MassClear">
                                <i class="fas fa-times"></i>Tout décocher
                            </button>
                        </div>

                        {{-- Barre d'outils : recherche, filtre de statut, mode expert --}}
                        <div class="pm1-tools">
                            <div class="pm1-search">
                                <i class="fas fa-search"></i>
                                <label class="visually-hidden" for="pmRecherche">Rechercher un salarié</label>
                                <input type="search" class="form-control" id="pmRecherche"
                                    placeholder="Rechercher par nom, identifiant, département…">
                            </div>

                            <label class="visually-hidden" for="pm1Filtre">Filtrer par statut</label>
                            <select class="form-select pm1-filter" id="pm1Filtre">
                                <option value="all">Filtre : tous ({{ count($lignes) }})</option>
                                <option value="anomaly">Avec anomalies ({{ $nbAnomalies }})</option>
                                <option value="ok">Sans anomalie ({{ $nbConformes }})</option>
                                <option value="pending">En attente ({{ $nbEnAttente }})</option>
                            </select>

                            <button type="button" class="pm1-expert" id="pm1Expert" aria-pressed="false">
                                <i class="fas fa-sliders-h"></i>Mode expert : <b id="pm1ExpertEtat">OFF</b>
                            </button>

                            <button type="button" class="pm1-expert" id="pm1Reset" title="Vider la recherche, le filtre et la sélection">
                                <i class="fas fa-undo"></i>Réinitialiser
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover pm1-table">
                                <thead>
                                    <tr>
                                        <th class="pm1-check">
                                            <input type="checkbox" class="form-check-input" id="pm1SelectAll"
                                                aria-label="Tout sélectionner">
                                        </th>
                                        <th>Salarié</th>
                                        <th>Département</th>
                                        <th class="text-center">Jours</th>
                                        <th class="pm-num">Base (FCFA)</th>
                                        <th class="pm-num">Primes (FCFA)</th>
                                        <th class="pm-num pm1-exp" hidden>Cotis. (CI)</th>
                                        <th class="pm-num pm1-exp" hidden>Impôt (ITS)</th>
                                        <th class="pm-num pm1-th-net">Net à payer</th>
                                        <th class="text-end"><span class="visually-hidden">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody id="pmLignes">
                                    @forelse($lignes as $ligne)
                                        @php $emp = $ligne['emp']; @endphp
                                        <tr class="pm1-row is-{{ $ligne['statut'] }}"
                                            data-texte="{{ mb_strtolower($emp->name . ' ' . $emp->employee_id . ' ' . $ligne['dept']) }}"
                                            data-verifier="{{ $ligne['verifier'] ? 1 : 0 }}"
                                            data-statut="{{ $ligne['statut'] }}"
                                            data-net="{{ (int) $ligne['net'] }}"
                                            data-employee-id="{{ $emp->id }}"
                                            data-employee-name="{{ $emp->name }}"
                                            data-matricule="{{ $emp->employee_id }}"
                                            data-dept="{{ $ligne['dept'] }}"
                                            data-anciennete="{{ $ligne['anciennete'] }}"
                                            data-situation="{{ $ligne['situation'] }}"
                                            data-jours="{{ $ligne['jours'] }}"
                                            data-base="{{ $fmt($ligne['base']) }}"
                                            data-salaire="{{ $fmt($emp->salary) }}"
                                            data-primes="{{ $fmt($ligne['primes']) }}"
                                            data-brut="{{ $fmt($ligne['brut']) }}"
                                            data-cotis="{{ $fmt($ligne['cotis']) }}"
                                            data-impot="{{ $fmt($ligne['impot']) }}"
                                            data-retenues="{{ $fmt($ligne['pret'] + $ligne['autre']) }}"
                                            data-net-fmt="{{ $fmt($ligne['net']) }}">
                                            <td class="pm1-check">
                                                <input type="checkbox" class="form-check-input pm1-pick"
                                                    aria-label="Sélectionner {{ $emp->name }}">
                                            </td>
                                            <td>
                                                <div class="pm-who pm1-who">
                                                    <b>
                                                        <i class="fas fa-user"></i>{{ $emp->name }}
                                                        @if($ligne['pret'] > 0)<span class="pm-tag">Prêt</span>@endif
                                                        @if($ligne['rembourse'] > 0)<span class="pm-tag">Frais</span>@endif
                                                    </b>
                                                    <small>ID <span class="pm-mono">{{ $emp->employee_id }}</span>@if($ligne['anciennete']) · anc. {{ $ligne['anciennete'] }}@endif</small>
                                                </div>
                                                @if($ligne['statut'] === 'anomaly')
                                                    <span class="pm1-pill anomaly" title="Jours travaillés différents de 30">
                                                        <i class="fas fa-exclamation-triangle"></i>Anomalie
                                                    </span>
                                                @elseif($ligne['statut'] === 'pending')
                                                    <span class="pm1-pill pending" title="Aucun montant calculé pour ce mois">En attente</span>
                                                @endif
                                            </td>
                                            <td class="pm1-dept">{{ $ligne['dept'] }}</td>
                                            <td class="text-center">
                                                @if($verrouille)
                                                    <span class="pm-days {{ $ligne['verifier'] ? 'warn' : '' }}">{{ $ligne['jours'] }} j</span>
                                                @else
                                                    <button type="button" class="pm-days btn-update-days {{ $ligne['verifier'] ? 'warn' : '' }}"
                                                        data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}"
                                                        data-periode-id="{{ $periode->id }}" title="Modifier les jours travaillés">
                                                        {{ $ligne['jours'] }} j <i class="fas fa-pen" style="font-size:10px"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="pm-num">
                                                @if($verrouille)
                                                    {{ $fmt($ligne['base']) }}
                                                @else
                                                    {{-- Le champ porte le salaire contractuel plein, pas le prorata :
                                                         c'est lui qu'on modifie, le prorata s'en déduit. --}}
                                                    <input type="text" class="pm1-base" inputmode="numeric"
                                                        value="{{ $fmt($ligne['salaire']) }}"
                                                        data-initial="{{ $fmt($ligne['salaire']) }}"
                                                        aria-label="Salaire de base de {{ $emp->name }}">
                                                @endif
                                                @if($ligne['jours'] != 30)
                                                    {{-- Sur période verrouillée, $emp->salary est le salaire actuel du
                                                         contrat, pas celui du bulletin : on affiche la valeur figée. --}}
                                                    <br><small class="text-muted">base retenue {{ $fmt($ligne['base']) }} · {{ $ligne['jours'] }}/30</small>
                                                @endif
                                            </td>
                                            <td class="pm-num pm1-primes">{{ $fmt($ligne['primes']) }}</td>
                                            <td class="pm-num pm1-exp" hidden>–{{ $fmt($ligne['cotis']) }}</td>
                                            <td class="pm-num pm1-exp" hidden>–{{ $fmt($ligne['impot']) }}</td>
                                            <td class="pm-num pm-net">
                                                <span class="pm1-net-wrap">
                                                    {{ $fmt($ligne['net']) }}
                                                    @if($ligne['statut'] === 'anomaly')
                                                        <i class="fas fa-exclamation-triangle pm1-ico warn" title="Anomalie à vérifier"></i>
                                                    @elseif($ligne['statut'] === 'pending')
                                                        <i class="far fa-circle pm1-ico faint" title="En attente de saisie"></i>
                                                    @else
                                                        <i class="fas fa-check-circle pm1-ico ok" title="Conforme"></i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-secondary" data-bs-toggle="dropdown"
                                                        aria-expanded="false" aria-label="Actions pour {{ $emp->name }}">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @unless($verrouille)
                                                        <a class="dropdown-item btn-add-elements" href="#" data-bs-toggle="modal" data-bs-target="#addElementsModal"
                                                            data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}" data-periode-id="{{ $periode->id }}">
                                                            <i class="fas fa-plus me-2"></i>Ajouter des éléments
                                                        </a>
                                                        <a class="dropdown-item btn-show-elements" href="#" data-bs-toggle="modal" data-bs-target="#showElementsModal"
                                                            data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}" data-periode-id="{{ $periode->id }}">
                                                            <i class="fas fa-eye me-2"></i>Afficher les éléments
                                                        </a>
                                                        <a class="dropdown-item btn-edit-elements" href="#" data-bs-toggle="modal" data-bs-target="#editElementsModal"
                                                            data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}" data-periode-id="{{ $periode->id }}">
                                                            <i class="fas fa-pen me-2"></i>Modifier les éléments
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        @endunless
                                                        <a class="dropdown-item btn-aperçu-bulletin" href="#" data-bs-toggle="modal" data-bs-target="#showBulletinModal"
                                                            data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}"
                                                            data-exercice-id="{{ $periode->exercice_id }}" data-periode-id="{{ $periode->id }}">
                                                            <i class="fas fa-file-invoice me-2"></i>Aperçu du bulletin
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">Aucun salarié actif avec un contrat sur cette période.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="pmAucunResultat" hidden>
                                        <td colspan="10" class="text-center text-muted py-4">Aucun salarié ne correspond à la recherche.</td>
                                    </tr>
                                </tbody>
                                @if(count($lignes) > 0)
                                    <tfoot>
                                        <tr class="pm1-totaux">
                                            <td class="pm1-check">—</td>
                                            <td>Totaux (<span id="pmCompte">{{ count($lignes) }}</span> salariés)</td>
                                            <td>—</td>
                                            <td class="text-center pm-mono">{{ $fmt($totalJours) }}</td>
                                            <td class="pm-num">{{ $fmt($totalBase) }}</td>
                                            <td class="pm-num">{{ $fmt($totalPrimes) }}</td>
                                            <td class="pm-num pm1-exp" hidden>–{{ $fmt($totalCotis) }}</td>
                                            <td class="pm-num pm1-exp" hidden>–{{ $fmt($totalImpot) }}</td>
                                            <td class="pm-num pm1-total-net">{{ $fmt($totalNet) }} FCFA</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        <div class="pm-table-foot">
                            <span>Montants en FCFA · base de 30 jours</span>
                            <span>Les primes affichées sont l'écart entre le brut et le salaire de base.</span>
                        </div>
                    </section>

                    {{-- Détail des éléments de paie, un bloc masqué par salarié. Le tiroir
                         recopie celui de la ligne cliquée. Rendu ici plutôt qu'appelé en
                         AJAX : les collections sont déjà en mémoire, autant s'en servir. --}}
                    <div class="pm1-el-sources" hidden>
                        @foreach($lignes as $ligne)
                            @php $idEmp = $ligne['emp']->id; @endphp
                            <div id="pm1El-{{ $idEmp }}">
                                <div class="pm1-el-line socle">
                                    <div class="pm1-el-main">
                                        <b>Salaire de base contractuel</b>
                                        <span class="pm1-el-meta">Élément obligatoire soumis à cotisation</span>
                                    </div>
                                    <span class="pm1-el-amt">{{ $fmt($ligne['salaire']) }} <small>FCFA</small></span>
                                </div>

                                @forelse($elementsParSalarie[$idEmp] ?? [] as $element)
                                    <div class="pm1-el-line">
                                        <div class="pm1-el-main">
                                            <b>{{ $element['libelle'] }}</b>
                                            <span class="pm1-el-meta">
                                                @if($element['fiscal'])<span class="pm1-el-tag fisc">Fiscal : {{ $element['fiscal'] }}</span>@endif
                                                @if($element['social'])<span class="pm1-el-tag soc">CNPS : {{ $element['social'] }}</span>@endif
                                                @if($element['note'])<span class="pm1-el-tag">{{ $element['note'] }}</span>@endif
                                            </span>
                                        </div>
                                        <span class="pm1-el-amt {{ $element['sens'] === '+' ? 'pos' : 'neg' }}">
                                            {{ $element['sens'] }}{{ $fmt($element['montant']) }} <small>FCFA</small>
                                        </span>
                                    </div>
                                @empty
                                    <p class="pm1-el-vide">Aucune prime ni retenue saisie pour ce mois.</p>
                                @endforelse
                            </div>
                        @endforeach
                    </div>

                    {{-- Bandeau d'audit : état de la paie à gauche, action de clture à
                         droite. Une seule barre pour les trois états de la période. --}}
                    <div class="pm1-audit">
                        <div class="pm1-audit-txt">
                            <i class="fas fa-building"></i>
                            @if($etape === 'payee')
                                <span><b>Période validée et verrouillée</b> — grille figée sur les bulletins générés</span>
                            @elseif($verrouille)
                                <span><b>Bulletins générés</b> — en attente de validation du paiement</span>
                            @elseif(count($lignes) === 0)
                                <span><b>Aucun salarié sur cette période</b> — vérifiez les dates d'embauche : un salarié n'entre dans la paie que si son contrat couvre le mois</span>
                            @elseif($nbAnomalies > 0)
                                <span><b>{{ $nbAnomalies }} anomalie{{ $nbAnomalies > 1 ? 's' : '' }} détectée{{ $nbAnomalies > 1 ? 's' : '' }}</b> — cliquez sur une ligne pour ouvrir le détail</span>
                            @elseif(count($alerts) > 0)
                                <span><b>{{ count($alerts) }} point{{ count($alerts) > 1 ? 's' : '' }} d'attention</b> — détail sous la grille, vous pouvez continuer</span>
                            @else
                                <span><b>Aucune anomalie</b> — {{ count($lignes) }} salarié(s) prêts pour la génération des bulletins</span>
                            @endif
                        </div>
                        <div class="pm1-audit-actions">
                            <span class="pm1-audit-net">
                                Net {{ $etape === 'payee' ? 'versé' : 'total' }}
                                <b class="pm-mono">{{ $fmt($totalNet) }}</b> FCFA
                            </span>

                            @if($etape === 'payee')
                                <span class="pm1-verrou"><i class="fas fa-lock"></i>Période verrouillée</span>
                                <a class="btn btn-primary pm1-valider" href="{{ route('company.paiesalaries.paie-du-mois', ['nouvelle' => 1]) }}">
                                    Ouvrir la paie {{ $deMois($nomSuivant) }} <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            @elseif($verrouille)
                                <button type="submit" form="pmFormPaiement" class="btn btn-success pm1-valider">
                                    <i class="fas fa-check-circle me-2"></i>Valider le paiement
                                </button>
                            @elseif($packExpire)
                                <a href="{{ route('company.plan.pricing') }}" class="btn btn-danger pm1-valider">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Pack expiré : renouveler
                                </a>
                            @else
                                <button type="button" class="btn btn-primary pm1-valider" data-bs-toggle="modal"
                                    data-bs-target="#genererBulletinsModal" {{ count($lignes) === 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-check-circle me-2"></i>Valider et générer les {{ count($lignes) }} bulletins
                                    <span class="pm1-valider-montant">{{ number_format($totalNet / 1000000, 2, ',', ' ') }} M FCFA</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bloc replié : le modèle 1 met la grille au centre, tout le reste
                         passe au second plan sans disparaître. Son contenu dépend de
                         l'état de la période : saisies du mois avant génération,
                         paiement et documents ensuite. --}}
                    <details class="pm1-annexes">
                        <summary>
                            <i class="fas fa-sliders-h"></i>
                            @if($verrouille)
                                <span>Paiement et documents de la paie</span>
                            @else
                                <span>Éléments du mois, variables et échéances de prêt</span>
                                @if(count($alerts) > 0)
                                    <span class="pm-chip warn">{{ count($alerts) }} à regarder</span>
                                @endif
                            @endif
                        </summary>
                        <div class="pm1-annexes-body">

                            @if($verrouille)

                                @if($etape !== 'payee')
                                    <section class="pm-panel">
                                        <div class="pm-panel-head">
                                            <div>
                                                <h2>Valider le paiement</h2>
                                                <p>Marque la période et ses {{ count($lignes) }} bulletin(s) comme payés. Le bouton se trouve dans la barre du bas.</p>
                                            </div>
                                        </div>
                                        <div class="pm-panel-body">
                                            <form id="pmFormPaiement" action="{{ route('company.paiesalaries.periodes.valider-paiement', $periode->id) }}" method="POST">
                                                @csrf
                                                <label class="form-label" for="date_paiement_effectif">Date de paiement effectif</label>
                                                <input type="date" class="form-control pm-mono mb-3" id="date_paiement_effectif" name="date_paiement_effectif"
                                                    value="{{ now()->format('Y-m-d') }}" required>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="envoyerNotifications" name="envoyer_notifications" checked>
                                                    <label class="form-check-label" for="envoyerNotifications">Envoyer les notifications aux employés</label>
                                                </div>
                                            </form>
                                        </div>
                                    </section>
                                @endif

                                @include('paiesalaries::periodes.partials.documents')

                            @else


                                <div class="pm-grid-2">
                                    <section class="pm-panel">
                                        <div class="pm-panel-head">
                                            <div>
                                                <h2>{{ $previousPeriode ? 'Repris de ' . $previousPeriode->nom : 'Éléments du mois' }}</h2>
                                                <p>
                                                    {{ $previousPeriode
                                                        ? "Copiés à l'ouverture de la paie. Récapitulatif : la saisie se fait dans le détail de chaque salarié."
                                                        : 'Récapitulatif du mois. La saisie se fait dans le détail de chaque salarié.' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="pm-panel-body">
                                            <ul class="pm-list">
                                                <li>
                                                    <span class="pm-bx ok"><i class="fas fa-plus"></i></span>
                                                    <span class="pm-txt"><b>Primes et indemnités</b><small>{{ $allowances->count() }} ligne(s) · montants par salarié</small></span>
                                                    <span class="pm-amt">{{ $fmt($allowances->sum('amount')) }}</span>
                                                </li>
                                                <li>
                                                    <span class="pm-bx bad"><i class="fas fa-minus"></i></span>
                                                    <span class="pm-txt"><b>Retenues sur salaire</b><small>{{ $retenuesSalaire->count() }} ligne(s)</small></span>
                                                    <span class="pm-amt">{{ $fmt($retenuesSalaire->sum('amount')) }}</span>
                                                </li>
                                                <li>
                                                    <span class="pm-bx warn"><i class="fas fa-university"></i></span>
                                                    <span class="pm-txt">
                                                        <b>Échéances de prêt</b>
                                                        <small>{{ $echeancesAppliquees->count() }} appliquée(s) sur {{ $loanPayments->count() }}</small>
                                                    </span>
                                                    <span class="pm-amt">{{ $fmt($echeancesAppliquees->sum('amount')) }}</span>
                                                    @if($loanPayments->count() > 0)
                                                        <a class="pm-link" href="#pm-echeances">Voir</a>
                                                    @else
                                                        <a class="pm-link" href="{{ route('company.loans.index') }}?periode_id={{ $periode->id }}">Prêts</a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <span class="pm-bx"><i class="fas fa-gift"></i></span>
                                                    <span class="pm-txt"><b>Avantages en nature</b><small>{{ $avantages->count() }} ligne(s)</small></span>
                                                    <span class="pm-amt">{{ $fmt($avantages->sum('amount_reel')) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </section>

                                    <section class="pm-panel">
                                        <div class="pm-panel-head">
                                            <div>
                                                <h2>Points d'attention</h2>
                                                <p>À regarder avant de vérifier les montants.</p>
                                            </div>
                                            @if(count($alerts) > 0)
                                                <span class="pm-chip warn">{{ count($alerts) }}</span>
                                            @endif
                                        </div>
                                        <div class="pm-panel-body">
                                            <ul class="pm-alerts">
                                                @forelse($alerts as $alert)
                                                    <li class="pm-alert {{ $alert['type'] === 'info' ? 'info' : 'warning' }}">
                                                        <i class="fas {{ $alert['type'] === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle' }}"></i>
                                                        <p>{{ $alert['message'] }}</p>
                                                    </li>
                                                @empty
                                                    <li class="pm-alert ok">
                                                        <i class="fas fa-check"></i>
                                                        <p>Rien à signaler pour cette période.</p>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </section>
                                </div>

                                <section class="pm-panel">
                                    <div class="pm-panel-head">
                                        <div>
                                            <h2>Ce qui change ce mois-ci</h2>
                                            <p>Chaque case ouvre la saisie filtrée sur {{ $periode->nom }}.</p>
                                        </div>
                                    </div>
                                    <div class="pm-panel-body">
                                        <div class="pm-vars">
                                            <a class="pm-var" href="{{ route('company.times.absences.index') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-user-clock"></i>Absences</span>
                                                <span class="pm-var-val">Saisir ou vérifier</span>
                                            </a>
                                            <a class="pm-var" href="{{ route('company.times.overtime.index') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-clock"></i>Heures supplémentaires</span>
                                                <span class="pm-var-val">Saisir ou vérifier</span>
                                            </a>
                                            <a class="pm-var" href="{{ route('company.leaves.index') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-umbrella-beach"></i>Congés</span>
                                                <span class="pm-var-val">Saisir ou vérifier</span>
                                            </a>
                                            <a class="pm-var {{ $remboursements->count() > 0 ? 'has' : '' }}" href="{{ route('company.paiesalaries.remboursements') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-receipt"></i>Remboursements de frais</span>
                                                <span class="pm-var-val">
                                                    {{ $remboursements->count() > 0 ? $remboursements->count() . ' ligne(s) · ' . $fmt($remboursements->sum('amount')) : 'Aucun ce mois-ci' }}
                                                </span>
                                            </a>
                                            <a class="pm-var" href="{{ route('company.ruptures.index') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-user-slash"></i>Ruptures de contrat</span>
                                                <span class="pm-var-val">Saisir ou vérifier</span>
                                            </a>
                                            <a class="pm-var" href="{{ route('company.loans.index') }}?periode_id={{ $periode->id }}">
                                                <span class="pm-var-top"><i class="fas fa-university"></i>Prêts</span>
                                                <span class="pm-var-val">Accorder ou suivre</span>
                                            </a>
                                        </div>
                                    </div>
                                </section>

                                @if($loanPayments->count() > 0)
                                    <section class="pm-panel" id="pm-echeances">
                                        <div class="pm-panel-head">
                                            <div>
                                                <h2>Échéances de prêt ({{ $loanPayments->count() }})</h2>
                                                <p>Appliquez l'échéance du mois pour qu'elle soit retenue sur le bulletin. Un prêt n'entre dans la paie que période par période.</p>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Salarié</th>
                                                        <th>Prêt</th>
                                                        <th class="text-center">Avancement</th>
                                                        <th class="pm-num">Reste dû</th>
                                                        <th class="pm-num">Échéance</th>
                                                        <th class="text-end">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($loanPayments as $echeance)
                                                        <tr>
                                                            <td>{{ $echeance->loan->employee->name ?? '-' }}</td>
                                                            <td>
                                                                {{ $echeance->loan->title }}
                                                                @if($echeance->hors_periode)
                                                                    <br>
                                                                    <small class="text-warning">
                                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                                        Hors échéancier ({{ \Carbon\Carbon::parse($echeance->loan->start_date)->format('m/Y') }}
                                                                        → {{ \Carbon\Carbon::parse($echeance->loan->end_date)->format('m/Y') }})
                                                                    </small>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-label-info">{{ $echeance->echeances_payees }} / {{ $echeance->nbre_mois }} mois</span>
                                                            </td>
                                                            <td class="pm-num">{{ $fmt($echeance->remaining_amount) }}</td>
                                                            <td class="pm-num">{{ $fmt($echeance->amount) }}</td>
                                                            <td class="text-end">
                                                                @if($echeance->applied)
                                                                    <span class="badge bg-label-success me-1"><i class="fas fa-check me-1"></i>Appliquée</span>
                                                                    <form action="{{ route('company.paiesalaries.periodes.loanpaiement.retirer', $echeance->loan->id) }}"
                                                                        method="POST" class="d-inline"
                                                                        onsubmit="return confirm('Retirer l\'échéance de ce prêt pour {{ $periode->nom }} ? Le prêt reste actif, il ne sera simplement pas retenu ce mois-ci.');">
                                                                        @csrf
                                                                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Ne pas retenir ce prêt sur cette période">
                                                                            <i class="fas fa-ban"></i>Retirer
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form action="{{ route('company.paiesalaries.periodes.loanpaiement', $echeance->loan->id) }}"
                                                                        method="POST" class="d-inline"
                                                                        onsubmit="return confirm('Appliquer une échéance de {{ $fmt($echeance->amount) }} FCFA sur cette période ?');">
                                                                        @csrf
                                                                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                                                        <input type="hidden" name="amount" value="{{ (int) $echeance->amount }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                            <i class="fas fa-plus"></i>Appliquer
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </section>
                                @endif
                            @endif

                        </div>
                    </details>


                </div>

            @else

                <div class="pm-banner bad">
                    <i class="fas fa-ban"></i>
                    <div>
                        <b>Cette période est annulée</b>
                        <p>Modifiez la période pour la remettre en préparation, ou ouvrez la paie suivante.</p>
                    </div>
                </div>

                <div class="pm-actionbar">
                    <div class="pm-ctx">Aucune action possible sur une période annulée</div>
                    <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.periodes.edit', $periode->id) }}">Modifier la période</a>
                    <a class="btn btn-primary pm-btn-main" href="{{ route('company.paiesalaries.paie-du-mois', ['nouvelle' => 1]) }}">
                        Ouvrir la paie {{ $deMois($nomSuivant) }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

            @endif

        </div>
    </div>

    @if($etape !== 'annulee')
        @include('paiesalaries::periodes.partials.tiroir')
        @include('paiesalaries::payslip.partials.employee-modals')
        @unless($packExpire || $verrouille)
            @include('paiesalaries::periodes.modals.generer-bulletins')
        @endunless
    @endif
@endsection

@if($etape !== 'annulee')
    @push('scripts')
        <script>
            (function () {
                // ---------- Vue hybride (modèle 1) ----------
                // Recherche, filtre de statut, mode expert, sélection multiple et
                // tiroir latéral. Tout est de l'affichage : aucune de ces actions
                // ne touche aux montants, qui restent calculés côté serveur.
                var recherche = document.getElementById('pmRecherche');
                var filtreStatut = document.getElementById('pm1Filtre');
                var lignes = Array.prototype.slice.call(document.querySelectorAll('#pmLignes tr[data-texte]'));
                var compte = document.getElementById('pmCompte');
                var aucunResultat = document.getElementById('pmAucunResultat');

                function lignesVisibles() {
                    return lignes.filter(function (ligne) { return !ligne.hidden; });
                }

                function filtrer() {
                    var texte = ((recherche && recherche.value) || '').trim().toLowerCase();
                    var statut = filtreStatut ? filtreStatut.value : 'all';
                    var visibles = 0;

                    lignes.forEach(function (ligne) {
                        var ok = (!texte || ligne.dataset.texte.indexOf(texte) !== -1)
                            && (statut === 'all' || ligne.dataset.statut === statut);
                        ligne.hidden = !ok;
                        if (ok) {
                            visibles++;
                        } else {
                            // Une ligne masquée ne doit pas rester comptée dans la sélection.
                            var caseACocher = ligne.querySelector('.pm1-pick');
                            if (caseACocher) caseACocher.checked = false;
                        }
                    });

                    if (compte) compte.textContent = visibles;
                    if (aucunResultat) aucunResultat.hidden = visibles > 0 || lignes.length === 0;
                    majSelection();
                }

                if (recherche) recherche.addEventListener('input', filtrer);
                if (filtreStatut) filtreStatut.addEventListener('change', filtrer);

                // Mode expert : révèle les colonnes cotisations et impôt
                var boutonExpert = document.getElementById('pm1Expert');
                var etatExpert = document.getElementById('pm1ExpertEtat');
                if (boutonExpert) {
                    boutonExpert.addEventListener('click', function () {
                        var actif = boutonExpert.getAttribute('aria-pressed') !== 'true';
                        boutonExpert.setAttribute('aria-pressed', actif ? 'true' : 'false');
                        if (etatExpert) etatExpert.textContent = actif ? 'ON' : 'OFF';
                        document.querySelectorAll('.pm1-exp').forEach(function (cellule) {
                            cellule.hidden = !actif;
                        });
                        try { sessionStorage.setItem('pm1Expert_{{ $periode->id }}', actif ? '1' : '0'); } catch (e) {}
                    });

                    var expertMemorise = null;
                    try { expertMemorise = sessionStorage.getItem('pm1Expert_{{ $periode->id }}'); } catch (e) {}
                    if (expertMemorise === '1') boutonExpert.click();
                }

                // Réinitialiser : ne remet à zéro que l'affichage, jamais les données.
                var boutonReset = document.getElementById('pm1Reset');
                if (boutonReset) {
                    boutonReset.addEventListener('click', function () {
                        if (recherche) recherche.value = '';
                        if (filtreStatut) filtreStatut.value = 'all';
                        lignes.forEach(function (ligne) {
                            var c = ligne.querySelector('.pm1-pick');
                            if (c) c.checked = false;
                        });
                        filtrer();
                    });
                }

                // Sélection multiple et cumul du net
                var toutSelectionner = document.getElementById('pm1SelectAll');
                var barreMasse = document.getElementById('pm1Mass');
                var masseCompte = document.getElementById('pm1MassCount');
                var masseNet = document.getElementById('pm1MassNet');

                function majSelection() {
                    var visibles = lignesVisibles();
                    var cochees = visibles.filter(function (ligne) {
                        var c = ligne.querySelector('.pm1-pick');
                        return c && c.checked;
                    });

                    var total = cochees.reduce(function (somme, ligne) {
                        return somme + (parseInt(ligne.dataset.net, 10) || 0);
                    }, 0);

                    if (barreMasse) barreMasse.hidden = cochees.length === 0;
                    if (masseCompte) masseCompte.textContent = cochees.length;
                    if (masseNet) masseNet.textContent = total.toLocaleString('fr-FR');

                    if (toutSelectionner) {
                        toutSelectionner.checked = visibles.length > 0 && cochees.length === visibles.length;
                        toutSelectionner.indeterminate = cochees.length > 0 && cochees.length < visibles.length;
                    }
                }

                if (toutSelectionner) {
                    toutSelectionner.addEventListener('change', function () {
                        lignesVisibles().forEach(function (ligne) {
                            var c = ligne.querySelector('.pm1-pick');
                            if (c) c.checked = toutSelectionner.checked;
                        });
                        majSelection();
                    });
                }

                var effacerSelection = document.getElementById('pm1MassClear');
                if (effacerSelection) {
                    effacerSelection.addEventListener('click', function () {
                        lignes.forEach(function (ligne) {
                            var c = ligne.querySelector('.pm1-pick');
                            if (c) c.checked = false;
                        });
                        majSelection();
                    });
                }

                // Tiroir latéral
                var tiroir = document.getElementById('pm1Drawer');
                var voile = document.getElementById('pm1Backdrop');

                function remplir(id, valeur) {
                    var cible = document.getElementById(id);
                    if (cible) cible.textContent = valeur;
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
                    remplir('pm1DrawerBase', (d.base || '0') + ' FCFA');
                    // Détail des éléments : on recopie le bloc pré-rendu du salarié.
                    var hote = document.getElementById('pm1DrawerElements');
                    var source = document.getElementById('pm1El-' + d.employeeId);
                    if (hote) {
                        hote.innerHTML = source
                            ? source.innerHTML
                            : '<p class="pm1-el-vide">Détail indisponible pour ce salarié.</p>';
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

                    var jours = parseInt(d.jours, 10) || 0;
                    remplir('pm1DrawerProrata', jours === 30
                        ? 'Mois complet (30/30 j)'
                        : 'Prorata appliqué : ' + jours + '/30 j (' + (30 - jours) + ' j d’écart)');

                    var alerte = document.getElementById('pm1DrawerAlerte');
                    if (alerte) alerte.hidden = d.statut !== 'anomaly';

                    ['pm1DrawerJoursBtn', 'pm1DrawerAdd', 'pm1DrawerShow', 'pm1DrawerEdit', 'pm1DrawerBulletin']
                        .forEach(function (id) { cibler(id, d.employeeId, d.employeeName); });

                    if (voile) voile.hidden = false;
                    if (tiroir) {
                        tiroir.hidden = false;
                        tiroir.classList.add('is-open');
                    }
                    document.body.style.overflow = 'hidden';
                }

                function fermerTiroir() {
                    if (voile) voile.hidden = true;
                    if (tiroir) {
                        tiroir.hidden = true;
                        tiroir.classList.remove('is-open');
                    }
                    document.body.style.overflow = '';
                }

                lignes.forEach(function (ligne) {
                    ligne.addEventListener('click', function (evenement) {
                        // Case à cocher, menu d'actions et bouton « jours » gardent leur rôle.
                        if (evenement.target.closest('.pm1-check, .dropdown, .pm-days, a, button, input')) return;
                        ouvrirTiroir(ligne);
                    });
                });

                document.querySelectorAll('.pm1-pick').forEach(function (caseACocher) {
                    caseACocher.addEventListener('change', majSelection);
                });

                [document.getElementById('pm1DrawerClose'), document.getElementById('pm1DrawerFermer'), voile]
                    .forEach(function (element) {
                        if (element) element.addEventListener('click', fermerTiroir);
                    });

                document.addEventListener('keydown', function (evenement) {
                    if (evenement.key === 'Escape' && tiroir && !tiroir.hidden) fermerTiroir();
                });

                // Les actions du tiroir ouvrent une modale : le tiroir doit s'effacer.
                ['pm1DrawerJoursBtn', 'pm1DrawerAdd', 'pm1DrawerShow', 'pm1DrawerEdit', 'pm1DrawerBulletin']
                    .forEach(function (id) {
                        var bouton = document.getElementById(id);
                        if (bouton) bouton.addEventListener('click', fermerTiroir);
                    });


                // ---------- Écriture : base d'un salarié et actions de masse ----------
                // Un seul point d'entrée côté serveur ; modifier une ligne, c'est une
                // sélection de un. Le serveur rejoue prime d'ancienneté et retenues
                // légales, donc la page est rechargée pour afficher les montants à jour.
                var urlTraitement = @json(route('company.paiesalaries.periodes.traitement-masse', $periode->id));
                var jetonCsrf = @json(csrf_token());

                function nombreDepuis(texte) {
                    var propre = (texte || '').replace(/[^0-9]/g, '');
                    return propre === '' ? null : parseInt(propre, 10);
                }

                function envoyer(action, valeur, identifiants, libelle) {
                    Swal.fire({
                        title: 'Enregistrement…',
                        allowOutsideClick: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    fetch(urlTraitement, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': jetonCsrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ action: action, valeur: valeur, employee_ids: identifiants })
                    })
                        .then(function (reponse) { return reponse.json().then(function (d) { return { ok: reponse.ok, d: d }; }); })
                        .then(function (resultat) {
                            if (!resultat.ok || !resultat.d.success) {
                                Swal.fire({ icon: 'error', title: 'Non enregistré', text: resultat.d.message || 'Le traitement a échoué.', confirmButtonColor: '#253e87' });
                                return;
                            }
                            Swal.fire({
                                icon: 'success', title: libelle, text: resultat.d.message,
                                timer: 1400, showConfirmButton: false
                            }).then(function () { window.location.reload(); });
                        })
                        .catch(function () {
                            Swal.fire({ icon: 'error', title: 'Non enregistré', text: 'Le serveur n\'a pas répondu.', confirmButtonColor: '#253e87' });
                        });
                }

                function envoyerPrime(rubriqueId, montant, identifiants) {
                    Swal.fire({ title: 'Enregistrement…', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

                    fetch(urlTraitement, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': jetonCsrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            action: 'prime', valeur: montant,
                            allowance_option_id: rubriqueId, employee_ids: identifiants
                        })
                    })
                        .then(function (reponse) { return reponse.json().then(function (d) { return { ok: reponse.ok, d: d }; }); })
                        .then(function (resultat) {
                            if (!resultat.ok || !resultat.d.success) {
                                Swal.fire({ icon: 'error', title: 'Non enregistré', text: resultat.d.message || 'Le traitement a échoué.', confirmButtonColor: '#253e87' });
                                return;
                            }
                            Swal.fire({ icon: 'success', title: 'Prime appliquée', text: resultat.d.message, timer: 1400, showConfirmButton: false })
                                .then(function () { window.location.reload(); });
                        })
                        .catch(function () {
                            Swal.fire({ icon: 'error', title: 'Non enregistré', text: 'Le serveur ne répond pas.', confirmButtonColor: '#253e87' });
                        });
                }

                // Édition en place du salaire de base
                document.querySelectorAll('.pm1-base').forEach(function (champ) {
                    champ.addEventListener('click', function (evenement) { evenement.stopPropagation(); });

                    champ.addEventListener('keydown', function (evenement) {
                        if (evenement.key === 'Enter') { evenement.preventDefault(); champ.blur(); }
                        if (evenement.key === 'Escape') { champ.value = champ.dataset.initial; champ.blur(); }
                    });

                    champ.addEventListener('blur', function () {
                        var valeur = nombreDepuis(champ.value);
                        var initiale = nombreDepuis(champ.dataset.initial);

                        if (valeur === null || valeur === initiale) {
                            champ.value = champ.dataset.initial;
                            return;
                        }

                        var ligne = champ.closest('tr');
                        envoyer('base', valeur, [parseInt(ligne.dataset.employeeId, 10)], 'Salaire de base modifié');
                    });
                });

                // Actions de masse sur la sélection
                document.querySelectorAll('.pm1-mass-act').forEach(function (bouton) {
                    bouton.addEventListener('click', function () {
                        var choisies = lignesVisibles().filter(function (ligne) {
                            var c = ligne.querySelector('.pm1-pick');
                            return c && c.checked;
                        });

                        if (choisies.length === 0) { return; }

                        var action = bouton.dataset.action;

                        // Une prime : on choisit la rubrique puis le montant, appliqués
                        // à toute la sélection d'un coup.
                        if (action === 'prime') {
                            Swal.fire({
                                title: 'Appliquer une prime',
                                html: '<select id="pm1PrimeOption" class="swal2-select" style="width:100%">'
                                    + @json($optionsPrimes->map(function ($o) { return ['id' => $o->id, 'name' => $o->name]; })->values())
                                        .map(function (o) { return '<option value="' + o.id + '">' + o.name + '</option>'; }).join('')
                                    + '</select>'
                                    + '<input id="pm1PrimeMontant" type="number" min="0" step="500" class="swal2-input" placeholder="Montant en FCFA">',
                                focusConfirm: false,
                                showCancelButton: true,
                                confirmButtonText: 'Appliquer aux ' + choisies.length + ' salarié(s)',
                                cancelButtonText: 'Annuler',
                                confirmButtonColor: '#253e87',
                                cancelButtonColor: '#8592a3',
                                preConfirm: function () {
                                    var rubrique = document.getElementById('pm1PrimeOption').value;
                                    var montant = document.getElementById('pm1PrimeMontant').value;
                                    if (!rubrique) { Swal.showValidationMessage('Choisissez une rubrique.'); return false; }
                                    if (montant === '' || Number(montant) < 0) { Swal.showValidationMessage('Saisissez un montant.'); return false; }
                                    return { rubrique: parseInt(rubrique, 10), montant: parseInt(montant, 10) };
                                }
                            }).then(function (resultat) {
                                if (!resultat.isConfirmed) { return; }
                                envoyerPrime(
                                    resultat.value.rubrique,
                                    resultat.value.montant,
                                    choisies.map(function (ligne) { return parseInt(ligne.dataset.employeeId, 10); })
                                );
                            });
                            return;
                        }

                        var surBase = action === 'base';

                        Swal.fire({
                            title: surBase ? 'Salaire de base' : 'Jours travaillés',
                            input: 'number',
                            inputLabel: 'Valeur appliquée aux ' + choisies.length + ' salarié(s) sélectionné(s)',
                            inputAttributes: surBase ? { min: 0, step: 1000 } : { min: 0, max: 30, step: 1 },
                            showCancelButton: true,
                            confirmButtonText: 'Appliquer',
                            cancelButtonText: 'Annuler',
                            confirmButtonColor: '#253e87',
                            cancelButtonColor: '#8592a3',
                            inputValidator: function (valeur) {
                                if (valeur === '' || valeur === null) { return 'Saisissez une valeur.'; }
                                if (!surBase && (valeur < 0 || valeur > 30)) { return 'Les jours vont de 0 à 30.'; }
                                if (surBase && valeur < 0) { return 'Le salaire ne peut pas être négatif.'; }
                                return null;
                            }
                        }).then(function (resultat) {
                            if (!resultat.isConfirmed) { return; }
                            envoyer(
                                action,
                                parseInt(resultat.value, 10),
                                choisies.map(function (ligne) { return parseInt(ligne.dataset.employeeId, 10); }),
                                surBase ? 'Salaire de base appliqué' : 'Jours appliqués'
                            );
                        });
                    });
                });

                filtrer();
            })();
        </script>
    @endpush
@endif
