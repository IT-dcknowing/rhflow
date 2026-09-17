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
    if ($etape === 'preparer') {
        foreach ($employees as $emp) {
            if ($emp->is_active != 1) {
                continue;
            }
            $jours = intval($emp->get_jours_work($periode->id));
            $joursRetenus = $jours > 0 ? $jours : 30;
            $ligne = [
                'emp' => $emp,
                'jours' => $joursRetenus,
                'base' => $joursRetenus == 30 ? $emp->salary : round(($emp->salary / 30) * $joursRetenus),
                'brut' => $emp->get_brut_salary($periode->id),
                'net' => $emp->get_net_salary($periode->id),
                'pret' => $emp->get_loan_retenue($periode->id),
                'autre' => $emp->get_Autre_retenue($periode->id),
                'rembourse' => $emp->get_Rembourssement($periode->id),
            ];
            $ligne['verifier'] = $joursRetenus != 30;
            $lignes[] = $ligne;
            $totalBrut += $ligne['brut'];
            $totalNet += $ligne['net'];
            if ($ligne['verifier']) {
                $aVerifier++;
            }
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
            @if($etape === 'preparer')
                @include('paiesalaries::periodes.partials.etapes', ['courante' => 0, 'faites' => 0, 'cliquables' => true])
            @elseif($etape === 'payer')
                @include('paiesalaries::periodes.partials.etapes', ['courante' => 3, 'faites' => 3, 'cliquables' => false])
            @elseif($etape === 'payee')
                @include('paiesalaries::periodes.partials.etapes', ['courante' => 3, 'faites' => 4, 'cliquables' => false])
            @endif

            @if($etape === 'preparer')

                {{-- ============================================================
                     1. PRÉPARER
                     ============================================================ --}}
                <div class="pm" data-panel="preparer">
                    <div class="pm-grid-2">
                        <section class="pm-panel">
                            <div class="pm-panel-head">
                                <div>
                                    <h2>{{ $previousPeriode ? 'Repris de ' . $previousPeriode->nom : 'Éléments du mois' }}</h2>
                                    <p>
                                        {{ $previousPeriode
                                            ? "Copiés à l'ouverture de la paie. Modifiez seulement ce qui change ce mois-ci."
                                            : 'Ajoutez les primes, retenues et avantages de ce mois.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="pm-panel-body">
                                <ul class="pm-list">
                                    <li>
                                        <span class="pm-bx ok"><i class="fas fa-plus"></i></span>
                                        <span class="pm-txt"><b>Primes et indemnités</b><small>{{ $allowances->count() }} ligne(s) · montants par salarié</small></span>
                                        <span class="pm-amt">{{ $fmt($allowances->sum('amount')) }}</span>
                                        {{-- Les montants se saisissent salarié par salarié, dans le tableau
                                             « Éléments par salarié » plus bas sur cette même étape. --}}
                                        <a class="pm-link" href="#pm-salaries">Modifier</a>
                                    </li>
                                    <li>
                                        <span class="pm-bx bad"><i class="fas fa-minus"></i></span>
                                        <span class="pm-txt"><b>Retenues sur salaire</b><small>{{ $retenuesSalaire->count() }} ligne(s)</small></span>
                                        <span class="pm-amt">{{ $fmt($retenuesSalaire->sum('amount')) }}</span>
                                        <a class="pm-link" href="{{ route('company.paiesalaries.retenues.index') }}?periode_id={{ $periode->id }}">Modifier</a>
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
                                        <a class="pm-link" href="{{ route('company.avantages.index') }}?periode_id={{ $periode->id }}">Modifier</a>
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

                    {{-- Saisie des éléments salarié par salarié : jours travaillés, ajout et
                         modification des primes, retenues et avantages. Tout se fait ici, à
                         l'étape « Préparer » ; « Vérifier » ne sert plus qu'au contrôle. --}}
                    <section class="pm-panel" id="pm-salaries">
                        <div class="pm-panel-head">
                            <div>
                                <h2>Éléments par salarié</h2>
                                <p>Ajoutez ou corrigez les éléments de chaque salarié, et les jours travaillés.</p>
                            </div>
                            <div class="pm-tools">
                                <label class="visually-hidden" for="pmRecherche">Rechercher un salarié</label>
                                <input type="search" class="form-control" id="pmRecherche" placeholder="Nom ou identifiant">
                                @if($aVerifier > 0)
                                    <button type="button" class="btn btn-outline-secondary pm-filter" id="pmFiltre" aria-pressed="false">
                                        <i class="fas fa-exclamation-triangle"></i>Jours ≠ 30 ({{ $aVerifier }})
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Salarié</th>
                                        <th>Jours</th>
                                        <th class="pm-num">Salaire de base</th>
                                        <th class="pm-num">Brut</th>
                                        <th class="pm-num">Prêts et retenues</th>
                                        <th class="pm-num">Net à payer</th>
                                        <th class="text-end"><span class="visually-hidden">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody id="pmLignes">
                                    @forelse($lignes as $ligne)
                                        @php $emp = $ligne['emp']; @endphp
                                        <tr data-texte="{{ mb_strtolower($emp->name . ' ' . $emp->employee_id) }}" data-verifier="{{ $ligne['verifier'] ? 1 : 0 }}">
                                            <td>
                                                <div class="pm-who">
                                                    <b>
                                                        {{ $emp->name }}
                                                        @if($ligne['pret'] > 0)<span class="pm-tag">Prêt</span>@endif
                                                        @if($ligne['rembourse'] > 0)<span class="pm-tag">Frais</span>@endif
                                                    </b>
                                                    <small>ID <span class="pm-mono">{{ $emp->employee_id }}</span></small>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="pm-days btn-update-days {{ $ligne['verifier'] ? 'warn' : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#showDaysWorkModal"
                                                    data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}"
                                                    data-periode-id="{{ $periode->id }}" title="Modifier les jours travaillés">
                                                    {{ $ligne['jours'] }} j <i class="fas fa-pen" style="font-size:10px"></i>
                                                </button>
                                            </td>
                                            <td class="pm-num">
                                                {{ $fmt($ligne['base']) }}
                                                @if($ligne['jours'] != 30)
                                                    <br><small class="text-muted">{{ $fmt($emp->salary) }} × {{ $ligne['jours'] }}/30</small>
                                                @endif
                                            </td>
                                            <td class="pm-num">{{ $fmt($ligne['brut']) }}</td>
                                            <td class="pm-num">{{ $fmt($ligne['pret'] + $ligne['autre']) }}</td>
                                            <td class="pm-num pm-net">{{ $fmt($ligne['net']) }}</td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"
                                                        aria-expanded="false" aria-label="Actions pour {{ $emp->name }}">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
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
                                            <td colspan="7" class="text-center text-muted py-4">Aucun salarié actif avec un contrat sur cette période.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="pmAucunResultat" hidden>
                                        <td colspan="7" class="text-center text-muted py-4">Aucun salarié ne correspond à la recherche.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pm-table-foot">
                            <span><span id="pmCompte">{{ count($lignes) }}</span> salarié(s) affiché(s) sur {{ count($lignes) }}</span>
                            <span>Montants en FCFA · base de 30 jours</span>
                        </div>
                    </section>
                </div>

                <div class="pm-actionbar" data-bar="preparer">
                    <div class="pm-ctx">
                        @if(count($alerts) > 0)
                            <b>{{ count($alerts) }} point(s) d'attention</b> · vous pouvez continuer, ils restent signalés
                        @else
                            <b>Éléments prêts</b> · vérifiez les montants avant de générer les bulletins
                        @endif
                    </div>
                    <button type="button" class="btn btn-primary pm-btn-main" data-aller="verifier">
                        Vérifier les montants <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                {{-- ============================================================
                     2. VÉRIFIER
                     ============================================================ --}}
                <div class="pm" data-panel="verifier" hidden>
                    <div class="pm-figures">
                        <div class="pm-fig"><span>Salariés</span><b>{{ count($lignes) }}</b></div>
                        <div class="pm-fig"><span>Salaire brut</span><b>{{ $fmt($totalBrut) }}<small>FCFA</small></b></div>
                        <div class="pm-fig net"><span>Net à payer</span><b>{{ $fmt($totalNet) }}<small>FCFA</small></b></div>
                    </div>

                    {{-- Étape de contrôle : lecture seule. Toute modification (jours travaillés,
                         primes, retenues, éléments de paie) se fait à l'étape « Préparer ». Seul
                         l'aperçu du bulletin reste accessible ici. --}}
                    <section class="pm-panel">
                        <div class="pm-panel-head">
                            <div>
                                <h2>Contrôle des montants</h2>
                                <p>Calculés en direct avec les éléments du mois. Rien n'est enregistré avant la génération
                                    des bulletins. Pour corriger un montant, revenez à l'étape « Préparer ».</p>
                            </div>
                            @if($aVerifier > 0)
                                <span class="pm-chip warn">{{ $aVerifier }} salarié(s) à jours ≠ 30</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Salarié</th>
                                        <th>Jours</th>
                                        <th class="pm-num">Brut</th>
                                        <th class="pm-num">Prêts et retenues</th>
                                        <th class="pm-num">Net à payer</th>
                                        <th class="text-end">Bulletin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lignes as $ligne)
                                        @php $emp = $ligne['emp']; @endphp
                                        <tr>
                                            <td>
                                                <div class="pm-who">
                                                    <b>
                                                        {{ $emp->name }}
                                                        @if($ligne['pret'] > 0)<span class="pm-tag">Prêt</span>@endif
                                                        @if($ligne['rembourse'] > 0)<span class="pm-tag">Frais</span>@endif
                                                    </b>
                                                    <small>ID <span class="pm-mono">{{ $emp->employee_id }}</span></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="pm-days {{ $ligne['verifier'] ? 'warn' : '' }}">{{ $ligne['jours'] }} j</span>
                                            </td>
                                            <td class="pm-num">{{ $fmt($ligne['brut']) }}</td>
                                            <td class="pm-num">{{ $fmt($ligne['pret'] + $ligne['autre']) }}</td>
                                            <td class="pm-num pm-net">{{ $fmt($ligne['net']) }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-aperçu-bulletin"
                                                    data-bs-toggle="modal" data-bs-target="#showBulletinModal"
                                                    data-employee-id="{{ $emp->id }}" data-employee-name="{{ $emp->name }}"
                                                    data-exercice-id="{{ $periode->exercice_id }}" data-periode-id="{{ $periode->id }}"
                                                    title="Aperçu du bulletin de {{ $emp->name }}">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Aucun salarié actif avec un contrat sur cette période.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="pm-table-foot">
                            <span>{{ count($lignes) }} salarié(s)</span>
                            <span>Montants en FCFA · base de 30 jours</span>
                        </div>
                    </section>
                </div>

                <div class="pm-actionbar" data-bar="verifier" hidden>
                    <div class="pm-ctx">
                        <b>{{ count($lignes) }} salarié(s)</b> · net à payer <b class="pm-mono">{{ $fmt($totalNet) }} FCFA</b>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-aller="preparer">Retour aux éléments</button>
                    @if($packExpire)
                        <a href="{{ route('company.plan.pricing') }}" class="btn btn-danger pm-btn-main">
                            <i class="fas fa-exclamation-triangle"></i>Pack expiré : renouveler
                        </a>
                    @else
                        <button type="button" class="btn btn-primary pm-btn-main" data-bs-toggle="modal" data-bs-target="#genererBulletinsModal"
                            {{ count($lignes) === 0 ? 'disabled' : '' }}>
                            Générer les {{ count($lignes) }} bulletins <i class="fas fa-arrow-right"></i>
                        </button>
                    @endif
                </div>

            @elseif($etape === 'payer')

                {{-- ============================================================
                     4. PAYER
                     ============================================================ --}}
                <div class="pm-banner navy">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <b>{{ $nbBulletins }} bulletin(s) générés</b>
                        <p>Net à payer : <span class="pm-mono">{{ $fmt($periode->bulletins_sum_net) }} FCFA</span>. Validez le paiement une fois les virements effectués.</p>
                    </div>
                </div>

                <div class="pm-grid-2">
                    <section class="pm-panel">
                        <div class="pm-panel-head">
                            <div>
                                <h2>Valider le paiement</h2>
                                <p>Marque la période et ses {{ $nbBulletins }} bulletin(s) comme payés.</p>
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
                    @include('paiesalaries::periodes.partials.documents')
                </div>

                <div class="pm-actionbar">
                    <div class="pm-ctx">Bulletins générés · <b>en attente de paiement</b></div>
                    <a class="btn btn-outline-secondary" href="{{ route('company.paiesalaries.paie-du-mois', ['nouvelle' => 1]) }}">
                        Ouvrir la paie {{ $deMois($nomSuivant) }}
                    </a>
                    <button type="submit" form="pmFormPaiement" class="btn btn-success pm-btn-main">
                        <i class="fas fa-check"></i>Valider le paiement
                    </button>
                </div>

            @elseif($etape === 'payee')

                <div class="pm-banner ok">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <b>Paie {{ $deMois($periode->nom) }} {{ $periode->statut === 'cloture' ? 'clôturée' : 'payée' }}@if($periode->date_paiement) le {{ $periode->date_paiement->format('d/m/Y') }}@endif</b>
                        <p>{{ $nbBulletins }} bulletin(s) · net versé <span class="pm-mono">{{ $fmt($periode->bulletins_sum_net) }} FCFA</span>. Les documents restent disponibles ci-dessous.</p>
                    </div>
                </div>

                @include('paiesalaries::periodes.partials.documents')

                <div class="pm-actionbar">
                    <div class="pm-ctx">Paie <b>{{ $deMois($periode->nom) }}</b> terminée</div>
                    <a class="btn btn-primary pm-btn-main" href="{{ route('company.paiesalaries.paie-du-mois', ['nouvelle' => 1]) }}">
                        Ouvrir la paie {{ $deMois($nomSuivant) }} <i class="fas fa-arrow-right"></i>
                    </a>
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

    @if($etape === 'preparer')
        @include('paiesalaries::payslip.partials.employee-modals')
        @unless($packExpire)
            @include('paiesalaries::periodes.modals.generer-bulletins')
        @endunless
    @endif
@endsection

@if($etape === 'preparer')
    @push('scripts')
        <script>
            (function () {
                var cleEtape = 'paieEtape_{{ $periode->id }}';

                // Passage Préparer <-> Vérifier sans recharger la page
                function allerA(etape) {
                    document.querySelectorAll('[data-panel]').forEach(function (panneau) {
                        panneau.hidden = panneau.dataset.panel !== etape;
                    });
                    document.querySelectorAll('[data-bar]').forEach(function (barre) {
                        barre.hidden = barre.dataset.bar !== etape;
                    });
                    var index = etape === 'verifier' ? 1 : 0;
                    document.querySelectorAll('.pm-step').forEach(function (item, i) {
                        item.classList.toggle('current', i === index);
                        item.classList.toggle('done', i < index);
                        var sous = item.querySelector('.pm-step-sub');
                        if (sous) sous.textContent = i < index ? sous.dataset.done : sous.dataset.todo;
                        var bouton = item.querySelector('button');
                        if (i === index) bouton.setAttribute('aria-current', 'step'); else bouton.removeAttribute('aria-current');
                    });
                    try { sessionStorage.setItem(cleEtape, etape); } catch (e) {}
                }

                document.querySelectorAll('[data-aller]').forEach(function (bouton) {
                    bouton.addEventListener('click', function () {
                        allerA(bouton.dataset.aller);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });
                document.querySelectorAll('.pm-step button[data-step]').forEach(function (bouton) {
                    bouton.addEventListener('click', function () {
                        allerA(bouton.dataset.step === '1' ? 'verifier' : 'preparer');
                    });
                });

                var etapeMemorisee = null;
                try { etapeMemorisee = sessionStorage.getItem(cleEtape); } catch (e) {}
                allerA(window.location.hash === '#verifier' ? 'verifier' : (etapeMemorisee || 'preparer'));

                // Recherche et filtre du tableau des salariés
                var recherche = document.getElementById('pmRecherche');
                var filtre = document.getElementById('pmFiltre');
                var lignes = document.querySelectorAll('#pmLignes tr[data-texte]');

                function filtrer() {
                    var texte = (recherche.value || '').trim().toLowerCase();
                    var seulementAVerifier = filtre && filtre.getAttribute('aria-pressed') === 'true';
                    var visibles = 0;
                    lignes.forEach(function (ligne) {
                        var ok = (!texte || ligne.dataset.texte.indexOf(texte) !== -1)
                            && (!seulementAVerifier || ligne.dataset.verifier === '1');
                        ligne.hidden = !ok;
                        if (ok) visibles++;
                    });
                    document.getElementById('pmCompte').textContent = visibles;
                    document.getElementById('pmAucunResultat').hidden = visibles > 0 || lignes.length === 0;
                }

                if (recherche) recherche.addEventListener('input', filtrer);
                if (filtre) {
                    filtre.addEventListener('click', function () {
                        filtre.setAttribute('aria-pressed', filtre.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
                        filtrer();
                    });
                }
            })();
        </script>
    @endpush
@endif
