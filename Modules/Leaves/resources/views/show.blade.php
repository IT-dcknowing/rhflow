@extends('layouts.app')
@section('page-title')
    {{ __('Détails de la demande de congé') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}" type="text/css">
    <style>
        /* Styles pour l'impression */
        @media print {
            body * {
                visibility: hidden;
            }
            #document-to-print, #document-to-print * {
                visibility: visible;
            }
            #document-to-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .hide-on-print, .modal-footer {
                display: none !important;
            }
        }
        .company-logo {
            max-width: 150px;
            max-height: 100px;
        }
    </style>
@endpush

@section('content')
@php
    // $retourPeriodeId vient du controleur : periode du conge si elle existe encore,
    // sinon repli sur la periode ouverte la plus recente de l'entreprise.
    $retourUrl = route('company.leaves.index') . ($retourPeriodeId ? '?periode_id=' . $retourPeriodeId : '');
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-edit me-2"></i>Détails de la demande de congé
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ $retourUrl }}">Gestion des congés</a>
                            </li>
                            <li class="breadcrumb-item active">Détails de la demande #{{ $leave->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="align-items-center">
                    <a href="{{ $retourUrl }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    @if($leave->status == 'Approuvé' || $leave->status == 'Démarré')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#attestationModal">
                            <i class="fas fa-file me-2"></i> {{ __('Générer une attestation') }}
                        </button>
                    @endif
                    @if($leave->status == 'Pending')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#actionModal">
                            <i class="fas fa-check me-2"></i> {{ __('Traiter la demande')}}
                        </button>
                    @endif
                    @if($leave->isActivable())
                        @if($leave->is_active)
                            <form action="{{ route('company.leaves.deactivate', $leave->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Désactiver ce congé ? L\'allocation sortira de la paie de la période. Les bulletins déjà générés ne sont pas modifiés.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-toggle-off me-2"></i>Désactiver pour la paie
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-success js-activate-leave"
                                    data-url="{{ route('company.leaves.activateForm', $leave->id) }}">
                                <i class="fas fa-toggle-on me-2"></i>Activer pour la paie
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Détails de la demande de congé') }}</h5>
                        <div class="float-end align-items-center">
                            @if($leave->status == 'Démarré')
                            <form action="{{ route('company.leaves.end', $leave->id) }}" method="POST">
                                @csrf
                                @method('GET')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-times me-2"></i>Terminer</button>
                            </form>
                            @elseif($leave->status == 'Approuvé' || $leave->status == 'Terminé')
                            <form action="{{ route('company.leaves.start', $leave->id) }}" method="POST">
                                @csrf
                                @method('GET')
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check me-2"></i> Démarrer</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">{{ __('Employé') }}</th>
                                    <td>{{ $leave->employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type de congé') }}</th>
                                    <td>{{ $leave->leavetype->title ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Date de demande') }}</th>
                                    <td>{{ Carbon\Carbon::parse($leave->applied_on)->locale('fr')->isoFormat('DD MMMM YYYY') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Période') }}</th>
                                    <td>
                                        {{ Carbon\Carbon::parse($leave->start_date)->locale('fr')->isoFormat('DD MMMM YYYY') }} 
                                        <b>au</b> 
                                        {{ Carbon\Carbon::parse($leave->end_date)->locale('fr')->isoFormat('DD MMMM YYYY') }}
                                        ({{ $leave->total_leave_days }} jours)
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">{{ __('Statut') }}</th>
                                    <td>
                                        @if($leave->status == 'Approuvé')
                                            <span class="badge bg-success p-2 px-3 rounded">{{ __('Approuvé') }}</span>
                                        @elseif($leave->status == 'Rejeté')
                                            <span class="badge bg-danger p-2 px-3 rounded">{{ __('Rejeté') }}</span>
                                        @elseif($leave->status == 'Démarré')
                                            <span class="badge bg-primary p-2 px-3 rounded">{{ __('Démarré') }}</span>
                                        @elseif($leave->status == 'Terminé')
                                            <span class="badge bg-success p-2 px-3 rounded">{{ __('Terminé') }}</span>
                                        @else
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ __('En attente') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Motif') }}</th>
                                    <td>{{ $leave->leave_reason ?? 'Non spécifié' }}</td>
                                </tr>
                                @if(!empty($leave->remark))
                                <tr>
                                    <th>{{ __('Commentaire') }}</th>
                                    <td>{{ $leave->leave_reason }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h5 align="center"><u>Décompte de l'allocation Congé</u></h5>
                        <div class="d-flex justify-content-center">
                            <table id="mois_table" class="table-sm table-bordered" width="70%">
                                <thead>
                                    <tr>
                                        <th width="40%">Libellé</th>
                                        <th style="text-align: center;">Valeur</th>
                                        <th width="15%"></th>
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
                                    @php
                                        $dateDebut = \Carbon\Carbon::parse($leave->leave_back);
                                        $dateFin = \Carbon\Carbon::parse($leave->start_date);
                                        $nbJoursReference = (int) $dateDebut->diffInDays($dateFin);
                                    @endphp
                                    <tr>
                                        <td>Période de référence</td>
                                        <td style="text-align: right;">
                                            {{ $nbJoursReference }} 
                                        </td>
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
                            <table id="mois_table" class="table-sm table-bordered"  width="30%">
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
                                        $salaires = !empty($leave->sb_leave) ? json_decode($leave->sb_leave, true) : [];
                                        $jours = !empty($leave->days_leave) ? json_decode($leave->days_leave, true) : [];
                                        $monthsLeave = !empty($leave->month_leave) ? json_decode($leave->month_leave, true) : [];
                                    @endphp

                                    @if(!empty($monthsLeave) && is_array($monthsLeave))
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
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">Aucune donnée de mois disponible</td>
                                        </tr>
                                    @endif
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'attestation -->
    <div class="modal fade" id="attestationModal" tabindex="-1" role="dialog" aria-labelledby="attestationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attestationModalLabel">{{ __('Attestation de congé') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('leaves::attestation')
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'action -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">{{ __('Traiter la demande') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @include('leaves::action')
            </div>
        </div>
    </div>
</div>

<!-- Modale d'activation du congé pour la paie (contenu chargé en AJAX) -->
<div class="modal fade" id="activateLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-toggle-on me-2"></i>Activer le congé pour la paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div id="activateLeaveModalBody">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Charge le formulaire d'activation (choix de la période + montant) dans la modale.
    $(document).on('click', '.js-activate-leave', function () {
        var url = this.getAttribute('data-url');
        var body = document.getElementById('activateLeaveModalBody');
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('activateLeaveModal'));

        body.innerHTML = '<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
        modal.show();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) {
                if (!response.ok) { throw new Error('HTTP ' + response.status); }
                return response.text();
            })
            .then(function (html) { body.innerHTML = html; })
            .catch(function () {
                body.innerHTML = '<div class="modal-body"><div class="alert alert-danger mb-0">Impossible de charger le formulaire d\'activation.</div></div>';
            });
    });
</script>
@endpush

@push('script')
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jspdf.min.js') }}"></script>
    <script>
        // Script pour gérer l'affichage/masquage du logo dans l'attestation
        $(document).ready(function() {
            // Afficher avec logo
            $('#showlogo').on('click', function() {
                $('#logoshow').show();
                $('#logohide').hide();
            });
            
            // Afficher sans logo
            $('#hidelogo').on('click', function() {
                $('#logoshow').hide();
                $('#logohide').show();
            });
            
            // Imprimer l'attestation
            $('#printButton').on('click', function() {
                window.print();
            });
        });
    </script>
@endpush