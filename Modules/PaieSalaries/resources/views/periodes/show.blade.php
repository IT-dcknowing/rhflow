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

    // Variables du mois par salarié : absences, heures supplémentaires, congés.
    // Trois requêtes pour toute la page, groupées ensuite en mémoire : une par
    // salarié aurait multiplié les allers-retours sans rien apporter.
    $absencesParSalarie = \Modules\Time\Models\TimeSheet::where('periode_id', $periode->id)
        ->orderBy('date')
        ->get()
        ->groupBy('employee_id');

    $heuresSupParSalarie = \Modules\Time\Models\Overtime::where('periode_id', $periode->id)
        ->orderBy('start_date')
        ->get()
        ->groupBy('employee_id');

    $congesParSalarie = \Modules\Leaves\Models\Leave::with('leaveType')
        ->where('periode_id', $periode->id)
        ->orderBy('start_date')
        ->get()
        ->groupBy('employee_id');

    // Total d'heures d'une ligne d'heures supplémentaires, toutes majorations
    // confondues. Le détail par taux reste consultable sur l'écran dédié.
    $heuresTotal = function ($ligne) {
        return (int) $ligne->quar_heure + (int) $ligne->heure_audd
            + (int) $ligne->heure_nuit_ferie + (int) $ligne->heure_dim_ferie
            + (int) $ligne->heure_nuit_dim_ferie;
    };

    // Éléments de paie regroupés par salarié, pour le détail du tiroir.
    // Purement de la lecture : les collections sont déjà chargées par le
    // contrôleur, on ne fait que les trier et les mettre en forme.
    $elementsParSalarie = [];

    // $allowanceId n'est renseigné que pour les primes : ce sont les seules dont le
    // montant se saisit dans le tiroir. Retenues et échéances de prêt s'affichent,
    // mais se modifient sur leur écran propre.
    // Traduction des paramètres de rubrique en étiquettes de la maquette.
    // « exo 100% » veut dire entièrement exonéré, donc Impôt NON ; « exo 0% »,
    // aucune exonération, donc Impôt OUI. La valeur d'origine reste en infobulle.
    $etiquetteFiscale = function ($trait) {
        if (!$trait) {
            return null;
        }
        return ['oui' => strpos($trait, 'exo 100%') !== 0 && stripos($trait, 'Remboursement') === false,
                'titre' => $trait];
    };

    $etiquetteSociale = function ($trait) {
        if (!$trait) {
            return null;
        }
        return ['oui' => stripos($trait, 'Non Soumis') === false, 'titre' => $trait];
    };

    $poserElement = function ($employeeId, $sens, $libelle, $montant, $fiscal = null, $social = null, $note = null, $allowanceId = null) use (&$elementsParSalarie) {
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
            'allowance_id' => $allowanceId,
        ];
    };

    foreach ($allowances as $element) {
        // On expose le montant plein : c'est lui qu'on saisit, le prorata en découle.
        $poserElement($element->employee_id, '+', $element->title,
            $element->montant ?: $element->amount,
            $element->trait_fisc, $element->trait_cnps,
            $element->jours_work && $element->jours_work != 30 ? $element->jours_work . ' j' : null,
            $element->id);
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

        $estAnomalie = ($ligne['jours'] != 30) || ($ligne['net'] <= 0 && $ligne['base'] > 0);
        $ligne['verifier'] = $estAnomalie;
        $ligne['primes'] = max(0, $ligne['brut'] - $ligne['base']);

        if ($estAnomalie) {
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
                {{-- Les trois actions étaient cachées derrière un menu « Plus » : elles
                     sont peu nombreuses et toutes utiles, autant les poser à l'écran. --}}
                <div class="pm-head-actions">
                    <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.periodes.edit', $periode->id) }}">
                        <i class="fas fa-edit me-2"></i>Modifier la période
                    </a>
                    <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.calcule') }}?periode_id={{ $periode->id }}">
                        <i class="fas fa-calculator me-2"></i>Aperçu détaillé des salaires
                    </a>
                    <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.exercices.show', $periode->exercice_id) }}">
                        <i class="fas fa-history me-2"></i>Périodes de l'exercice
                    </a>
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
                                @if($nbAnomalies > 0)
                                    <option value="anomaly">Anomalies ({{ $nbAnomalies }})</option>
                                @endif
                                <option value="ok">Conformes ({{ $nbConformes }})</option>
                                <option value="pending">En attente ({{ $nbEnAttente }})</option>
                            </select>

                            <button type="button" class="pm1-expert" id="pm1Expert" aria-pressed="false">
                                <i class="fas fa-sliders-h"></i>Mode expert : <b id="pm1ExpertEtat">Désactivé</b>
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
                                                    <div class="pm1-who-name">
                                                        <b>
                                                            <i class="fas fa-user"></i>{{ $emp->name }}
                                                            @if($ligne['pret'] > 0)<span class="pm-tag">Prêt</span>@endif
                                                            @if($ligne['rembourse'] > 0)<span class="pm-tag">Frais</span>@endif
                                                        </b>
                                                    </div>
                                                    <div class="pm1-who-mat">
                                                        <small class="text-muted">Matricule : <span class="pm-mono">{{ \Auth::user()->employeeIdFormat($emp->employee_id) ?: $emp->employee_id }}</span></small>
                                                    </div>
                                                    <div class="pm1-who-anc">
                                                        <small class="text-muted">Ancienneté : <span>{{ $ligne['anciennete'] ?: '0 an' }}</span></small>
                                                    </div>
                                                    <span class="pm1-ouvrir"><i class="fas fa-chevron-right"></i>Voir le détail</span>
                                                </div>
                                                @if($ligne['statut'] === 'anomaly')
                                                    <span class="pm1-pill anomaly" title="Jours travaillés différents de 30 ou situation à vérifier">
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
                                                    {{-- Ajouter, afficher et modifier les éléments ont quitté ce menu :
                                                         ils vivent dans le tiroir du salarié, ouvert au clic sur la ligne.
                                                         Ne reste ici que l'accès direct au bulletin. --}}
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item btn-aperçu-bulletin" href="#" data-bs-toggle="modal" data-bs-target="#showBulletinModal"
                                                            data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}"
                                                            data-exercice-id="{{ $periode->exercice_id }}" data-periode-id="{{ $periode->id }}">
                                                            <i class="fas fa-file-invoice me-2"></i>Aperçu du bulletin
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Détail du salarié, replié sous sa ligne. Rendu ici plutôt
                                             qu'en bas de page : ces éléments valent pour ce salarié. --}}
                                        <tr class="pm1-detail" data-detail-pour="{{ $emp->id }}" hidden>
                                            <td colspan="10">
                                                <div class="pm1-detail-corps">
                                            @php $idEmp = $ligne['emp']->id; @endphp
                                            <div id="pm1El-{{ $idEmp }}">
                                                <div class="pm1-el-line socle">
                                                    <div class="pm1-el-main">
                                                        <b>Salaire de base contractuel</b>
                                                        <span class="pm1-el-meta">Élément obligatoire soumis à cotisation</span>
                                                    </div>
                                                    @if($verrouille)
                                                        <span class="pm1-el-amt">{{ $fmt($ligne['salaire']) }} <small>FCFA</small></span>
                                                    @else
                                                        <span class="pm1-el-saisie">
                                                            <input type="number" class="pm1-champ pm1-champ-base" min="0" step="500"
                                                                value="{{ (int) $ligne['salaire'] }}"
                                                                aria-label="Salaire de base de {{ $emp->name }}">
                                                            <small>FCFA</small>
                                                        </span>
                                                    @endif
                                                </div>

                                                @forelse($elementsParSalarie[$idEmp] ?? [] as $element)
                                                    <div class="pm1-el-line">
                                                        <div class="pm1-el-main">
                                                            <b>{{ $element['libelle'] }}</b>
                                                            <span class="pm1-el-meta">
                                                                @php
                                                                    $fisc = $etiquetteFiscale($element['fiscal']);
                                                                    $soc = $etiquetteSociale($element['social']);
                                                                @endphp
                                                                @if($fisc)
                                                                    <span class="pm1-badge {{ $fisc['oui'] ? 'impot' : 'neutre' }}" title="{{ $fisc['titre'] }}">Impôt {{ $fisc['oui'] ? 'OUI' : 'NON' }}</span>
                                                                @endif
                                                                @if($soc)
                                                                    <span class="pm1-badge {{ $soc['oui'] ? 'social' : 'neutre' }}" title="{{ $soc['titre'] }}">Soc {{ $soc['oui'] ? 'OUI' : 'NON' }}</span>
                                                                @endif
                                                                @if($element['note'])<span class="pm1-badge neutre">{{ $element['note'] }}</span>@endif
                                                            </span>
                                                        </div>
                                                        @if(!$verrouille && $element['allowance_id'])
                                                            <span class="pm1-el-saisie">
                                                                <b class="pm1-signe pos">+</b>
                                                                <input type="number" class="pm1-champ pm1-champ-element" min="0" step="500"
                                                                    data-allowance="{{ $element['allowance_id'] }}"
                                                                    value="{{ (int) $element['montant'] }}"
                                                                    aria-label="Montant de {{ $element['libelle'] }}">
                                                                <small>FCFA</small>
                                                                <button type="button" class="pm1-btn-retirer-prime"
                                                                    data-allowance="{{ $element['allowance_id'] }}"
                                                                    data-employee-id="{{ $idEmp }}"
                                                                    data-libelle="{{ $element['libelle'] }}"
                                                                    title="Retirer {{ $element['libelle'] }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </span>
                                                        @else
                                                            <span class="pm1-el-amt {{ $element['sens'] === '+' ? 'pos' : 'neg' }}">
                                                                {{ $element['sens'] }}{{ $fmt($element['montant']) }} <small>FCFA</small>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <p class="pm1-el-vide">Aucune prime ni retenue saisie pour ce mois.</p>
                                                @endforelse
                                            </div>

                                            {{-- Variables du mois de ce salarié, recopiées dans le tiroir. --}}
                                            <div id="pm1Var-{{ $idEmp }}">
                                                @php
                                                    $sesAbsences = $absencesParSalarie[$idEmp] ?? collect();
                                                    $sesHeures = $heuresSupParSalarie[$idEmp] ?? collect();
                                                    $sesConges = $congesParSalarie[$idEmp] ?? collect();
                                                    $aucuneVariable = $sesAbsences->isEmpty() && $sesHeures->isEmpty() && $sesConges->isEmpty();
                                                @endphp

                                                @forelse($sesAbsences as $absence)
                                                    <div class="pm1-var-line">
                                                        <div class="pm1-var-main">
                                                            <b><i class="fas fa-user-clock"></i>Absence</b>
                                                            <span class="pm1-var-meta">
                                                                {{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}
                                                                @if($absence->arrival_date)
                                                                    → retour le {{ \Carbon\Carbon::parse($absence->arrival_date)->format('d/m/Y') }}
                                                                @endif
                                                                @if($absence->type_permis)
                                                                    <span class="pm1-el-tag">{{ Str::limit($absence->type_permis, 46) }}</span>
                                                                @endif
                                                                @if($absence->motif_justify)
                                                                    <span class="pm1-el-tag">Justifiée : {{ $absence->motif_justify }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <span class="pm1-var-val">
                                                            {{ (int) $absence->hours }} h
                                                            @if($absence->retenue)<small>· {{ (int) $absence->retenue }} j retenus</small>@endif
                                                        </span>
                                                    </div>
                                                @empty
                                                @endforelse

                                                @foreach($sesHeures as $heure)
                                                    <div class="pm1-var-line">
                                                        <div class="pm1-var-main">
                                                            <b><i class="fas fa-clock"></i>Heures supplémentaires</b>
                                                            <span class="pm1-var-meta">
                                                                du {{ \Carbon\Carbon::parse($heure->start_date)->format('d/m/Y H:i') }}
                                                                au {{ \Carbon\Carbon::parse($heure->end_date)->format('d/m/Y H:i') }}
                                                                <span class="pm1-el-tag {{ $heure->statut === 'approved' ? 'soc' : '' }}">{{ ucfirst($heure->statut) }}</span>
                                                                @if($heure->paid === 'paid')<span class="pm1-el-tag">Payées</span>@endif
                                                            </span>
                                                        </div>
                                                        <span class="pm1-var-val">
                                                            {{ $heuresTotal($heure) }} h
                                                            @if((float) $heure->montant > 0)<small>· {{ $fmt((float) $heure->montant) }} FCFA</small>@endif
                                                        </span>
                                                    </div>
                                                @endforeach

                                                @foreach($sesConges as $conge)
                                                    <div class="pm1-var-line">
                                                        <div class="pm1-var-main">
                                                            <b><i class="fas fa-umbrella-beach"></i>{{ $conge->leaveType->name ?? 'Congé' }}</b>
                                                            <span class="pm1-var-meta">
                                                                du {{ \Carbon\Carbon::parse($conge->start_date)->format('d/m/Y') }}
                                                                au {{ \Carbon\Carbon::parse($conge->end_date)->format('d/m/Y') }}
                                                                <span class="pm1-el-tag">{{ $conge->status }}</span>
                                                            </span>
                                                        </div>
                                                        <span class="pm1-var-val">
                                                            {{ (int) $conge->total_leave_days }} j
                                                            @if((int) $conge->amount_leave > 0)<small>· {{ $fmt($conge->amount_leave) }} FCFA</small>@endif
                                                        </span>
                                                    </div>
                                                @endforeach

                                                @if($aucuneVariable)
                                                    <p class="pm1-el-vide">Aucune absence, heure supplémentaire ni congé ce mois-ci.</p>
                                                @endif
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

                        {{-- Ce volet ne concerne plus que les périodes verrouillées :
                             paiement et documents. Les éléments du mois, les variables et
                             les échéances de prêt ont rejoint le détail de chaque ligne,
                             où ils valent pour un salarié précis. --}}
                        @if($verrouille)
                            <details class="pm1-annexes" open>
                                <summary>
                                    <i class="fas fa-sliders-h"></i>
                                    <span>Paiement et documents de la paie</span>
                                </summary>
                                <div class="pm1-annexes-body">

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
                                </div>
                            </details>
                        @endif
                    </section>


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
                        if (etatExpert) etatExpert.textContent = actif ? 'Activé' : 'Désactivé';
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

                document.querySelectorAll('.pm1-pick').forEach(function (caseACocher) {
                    caseACocher.addEventListener('change', majSelection);
                });

                // Le bouton des jours et le menu d'actions gardent leur propre rôle :
                // on arrête la propagation chez eux plutôt que de conditionner la ligne.
                document.querySelectorAll('#pmLignes .pm-days, #pmLignes .dropdown').forEach(function (element) {
                    element.addEventListener('click', function (evenement) {
                        evenement.stopPropagation();
                    });
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
                            if (identifiants && identifiants.length === 1) {
                                sessionStorage.setItem('pm1ReouvrirTiroir', String(identifiants[0]));
                            }
                            Swal.fire({ icon: 'success', title: 'Prime ajoutée', text: resultat.d.message, timer: 900, showConfirmButton: false })
                                .then(function () { window.location.reload(); });
                        })
                        .catch(function () {
                            Swal.fire({ icon: 'error', title: 'Non enregistré', text: 'Le serveur ne répond pas.', confirmButtonColor: '#253e87' });
                        });
                }

                // Ajout d'une prime depuis le tiroir, pour le salarié ouvert.
                var boutonPrimeDirecte = document.getElementById('pm1PrimeAjouter');
                if (boutonPrimeDirecte) {
                    boutonPrimeDirecte.addEventListener('click', function () {
                        var rubrique = document.getElementById('pm1PrimeRubrique');
                        var montant = document.getElementById('pm1PrimeMontantDirect');
                        var employeId = boutonPrimeDirecte.dataset.employeeId;

                        if (!employeId) { return; }

                        if (!montant.value || Number(montant.value) < 0) {
                            montant.focus();
                            Swal.fire({
                                icon: 'warning', title: 'Montant manquant',
                                text: 'Saisissez le montant de la prime.',
                                confirmButtonColor: '#253e87'
                            });
                            return;
                        }

                        envoyerPrime(
                            parseInt(rubrique.value, 10),
                            parseInt(montant.value, 10),
                            [parseInt(employeId, 10)]
                        );
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
