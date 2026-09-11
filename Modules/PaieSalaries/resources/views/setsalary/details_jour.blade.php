@php
    $code=101;
    $cpte = 1;
    $city = \App\Models\Utility::getValByName('company_city');
@endphp
@extends('layouts.admin')

@section('page-title')
    {{ __('Traitement des Journaliers') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Tableau de bord') }}</a></li>
    <li class="breadcrumb-item">{{ __('Traitement des Journaliers') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="col-md-12 mb-4">
                <div class="card bg-success text-white" style="padding:10px;">
                    <h5 class="mb-0" style="color:#fff;">{{ __('Période de paie : ') }} <strong> {{ $period_type }} - {{ \Carbon\Carbon::parse($start_date)->translatedFormat('D d F Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('D d F Y') }}</h5>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <!-- information -->
                    <div class="col-12 mb-4">
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="ti ti-info-circle me-2"></i>
                            <marquee scrollamount="5">
                                Assurez-vous de sélectionner les employés ou l'employé à traiter, puis cliquez sur l'un des boutons "Appliquer au groupe" ou "Appliquer à l'employé" ou "Appliquer à l'équipe" pour sauvegarder les éléments du brut.
                            </marquee>
                        </div>
                    </div>
                    <form action="{{-- route('payslip.journaliers.saveAttendance', $period->id) --}}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" id="btnGroupAssign">{{ __('Appliquer au groupe') }}</button>
                                    {{-- <button type="button" class="btn btn-secondary" id="btnTeamAssign">{{ __('Appliquer à une équipe') }}</button> --}}
                                    <button type="button" class="btn btn-info" id="btnEmpAssign">{{ __('Appliquer à un employé') }}</button>
                                    <button type="button" class="btn btn-danger" id="btnReset">{{ __('Réinitialiser') }}</button>
                                </div>
                            </div>
                            <div class="col-md-5 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-check me-1"></i>{{ __('Valider le traitement') }}
                                </button>
                            </div>
                        </div>

                        <!-- Modal pour appliquer au groupe -->
                        <div class="modal fade modal-lg" id="groupAssignModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Appliquer au groupe sélectionné') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-4">
                                            <div class="col-md-6 form-group" id="group_days_div">
                                                <label for="group_days">{{ __('Jours travaillés') }}</label>
                                                <input type="number" class="form-control" id="group_days" min="0" step="0.5" value="{{ $diff_days }}">
                                            </div>
                                            <div class="col-md-6 form-group" id="group_rate_div">
                                                <label for="group_rate">{{ __('Salaire mensuel de base') }}</label>
                                                <input type="number" class="form-control" id="group_rate" min="0">
                                            </div>
                                        </div>

                                        @if(strpos($city, 'Abidjan') !== false || strpos($city, 'abidjan') !== false ||  strpos($city, 'ABIDJAN') !== false)
                                            <input type="text" id="tp_brut" name="tp_brut" value="30000" hidden="">
                                        @elseif(strpos($city, 'Bouaké') !== false || strpos($city, 'bouaké') !== false ||  strpos($city, 'BOUAKE') !== false)
                                            <input type="text" id="tp_brut" name="tp_brut" value="24000" hidden="">
                                        @else
                                            <input type="text" id="tp_brut" name="tp_brut" value="22000" hidden="">
                                        @endif

                                        <div class="card bg-success-subtle p-3 mb-3">
                                            <h5>Calcul du brut journalier cible</h5>
                                            <div class="form-group">
                                                <label for="montant" class="mb-2">Entrez le montant du brut journalier à atteindre :</label>
                                                <div class="input-group mb-3">
                                                    <input type="number" id="montant" class="form-control" placeholder="Montant en FCFA" oninput="calculerMontants()" />
                                                </div>
                                                <small class="form-text text-muted">Ce montant sera utilisé pour déterminer les éléments du brut journalier à appliquer.</small>
                                            </div>
                                        </div>

                                        <div class="result" id="resultat"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                                        <button type="button" class="btn btn-primary" id="applyToGroup">{{ __('Appliquer') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour appliquer à une équipe -->
                        <div class="modal fade modal-lg" id="teamAssignModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Appliquer à une équipe') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-4">
                                            <div class="col-md-12 form-group">
                                                <label for="team_select">{{ __('Sélectionner une équipe') }}</label>
                                                <select class="form-select" id="team_select">
                                                    <option value="">{{ __('Choisir une équipe') }}</option>
                                                    @foreach($teams as $team)
                                                        <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->position }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12 form-group">
                                                <label for="team_days">{{ __('Jours travaillés') }}</label>
                                                <input type="number" class="form-control" id="team_days" min="0" step="0.5" value="{{ $diff_days }}">
                                            </div>
                                        </div>

                                        <div class="card bg-success-subtle p-3 mb-3">
                                            <h5>Calcul du brut journalier cible</h5>
                                            <div class="form-group">
                                                <label for="team_montant" class="mb-2">Entrez le montant du brut journalier à atteindre :</label>
                                                <div class="input-group mb-3">
                                                    <input type="number" id="team_montant" class="form-control" placeholder="Montant en FCFA" oninput="calculerMontantsTeam()" />
                                                </div>
                                                <small class="form-text text-muted">Ce montant sera utilisé pour déterminer les éléments du brut journalier à appliquer à l'équipe.</small>
                                            </div>
                                        </div>

                                        <div class="result" id="resultat_team"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                                        <button type="button" class="btn btn-primary" id="applyToTeam">{{ __('Appliquer') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour voir les éléments du brut -->
                        <div class="modal fade" id="brutAssignModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Les éléments du brut') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <!-- Table pour un affichage plus structuré -->
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Élément</th>
                                                            <th class="text-end">Montant (FCFA)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Montant journalier</td>
                                                            <td class="text-end"><span id="salaire_base" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Transport journalier</td>
                                                            <td class="text-end"><span id="tp_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Gratification journalière</td>
                                                            <td class="text-end"><span id="gratif_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Allocation de congé</td>
                                                            <td class="text-end"><span id="leave_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Prime de précarité</td>
                                                            <td class="text-end"><span id="preca_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sursalaire journalier</td>
                                                            <td class="text-end"><span id="sursa_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                        <tr class="table-info">
                                                            <td class="fw-bold">TOTAL BRUT JOURNALIER</td>
                                                            <td class="text-end"><span id="total_brut_journalier" class="fw-bold">0</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="alert alert-info mt-3">
                                                    <small>Explication des calculs :
                                                        <ul class="mt-2 mb-0">
                                                            <li>Gratification : 75% du salaire de base pour 360 jours</li>
                                                            <li>Allocation congé : 2,2 jours pour 30 jours sur (Base + Gratif + Sursalaire)</li>
                                                            <li>Prime de précarité : 3% du brut total</li>
                                                        </ul>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour créer une équipe -->
                        <div class="modal fade" id="createTeamModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Créer une nouvelle équipe') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-12 form-group">
                                                <label for="team_name">{{ __('Nom de l\'équipe') }}</label>
                                                <input type="text" class="form-control" id="team_name" placeholder="Ex: Équipe de nuit">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12 form-group">
                                                <label for="team_position">{{ __('Position/Description') }}</label>
                                                <input type="text" class="form-control" id="team_position" placeholder="Ex: Service de nuit 20h-6h">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label>{{ __('Sélectionner les employés') }}</label>
                                                <div class="alert alert-info">
                                                    <small>Sélectionnez d'abord les employés dans le tableau principal avant de les assigner à cette équipe.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                                        <button type="button" class="btn btn-primary" id="saveTeam">{{ __('Créer l\'équipe') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des équipes existantes -->
                        <div class="mb-4">
                            <hr>
                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <h5>{{ __('Équipes de journaliers') }}</h5>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-primary" id="btnCreateTeam">
                                        <i class="ti ti-users me-1"></i>{{ __('Créer une équipe') }}
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="teams-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Nom de l\'équipe') }}</th>
                                            <th>{{ __('Position/Description') }}</th>
                                            <th>{{ __('Nombre d\'employés') }}</th>
                                            <th align="center">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teams as $team)
                                            <tr>
                                                <td>{{$cpte++}}</td>
                                                <td>
                                                    <a href="#" id="select-team" class="select-team" data-team-id="{{ $team->id }}">{{ $team->name }}</a>
                                                </td>
                                                <td>{{ $team->position }}</td>
                                                <td>{{ $team->employee_count }}</td>
                                                <td align="center">
                                                    <div class="d-flex">
                                                        <div class="action-btn ms-2">
                                                            <a href="#" class="btn btn-sm bg-info align-items-center view-team-members" data-team-id="{{ $team->id }}" data-team-name="{{ $team->name }}">
                                                                <i class="ti ti-eye text-white"></i>
                                                            </a>
                                                        </div>
                                                        <div class="action-btn ms-2"></div>
                                                            <a href="#" class="btn btn-sm bg-danger delete-team" data-team-id="{{ $team->id }}">
                                                                <i class="ti ti-trash text-white"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucune équipe créée</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Modal pour voir les membres d'une équipe -->
                        <div class="modal fade" id="teamMembersModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Membres de l\'équipe') }} <span id="team-name-display"></span></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="team-members-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('ID Employé') }}</th>
                                                        <th>{{ __('Nom & Prénoms') }}</th>
                                                        <th>{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="team-members-body">
                                                    <!-- Les membres seront chargés dynamiquement ici -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Form pour ajouter des membres -->
                                        <div id="add-members-form" style="display:none;">
                                            <hr>
                                            <h6>{{ __('Ajouter des membres') }}</h6>
                                            <div class="alert alert-info">
                                                <small>{{ __('Sélectionnez les employés à ajouter à cette équipe') }}</small>
                                            </div>
                                            <div class="form-group">
                                                <select class="form-select" id="employees-to-add" multiple>
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="text-end mt-3">
                                                <button type="button" class="btn btn-outline-secondary" id="cancel-add">{{ __('Annuler') }}</button>
                                                <button type="button" class="btn btn-primary" id="confirm-add">{{ __('Confirmer') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="ajoutEmpTeam" class="btn btn-primary">{{ __('Ajouter des membres') }}</button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="journaliers-table">
                                <thead>
                                    <tr>
                                        <th class="border border-gray-200" rowspan="2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="select-all">
                                                <label class="form-check-label" for="select-all"></label>
                                            </div>
                                        </th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('ID Employé') }}</th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('Nom & Prénoms') }}</th>
                                        <th class="border border-gray-200" width="5%" rowspan="2">{{ __('Heures travaillées. (Jours)') }}</th>
                                        <th class="border border-gray-200" colspan="4">{{ __('Salaire Brut') }}</th>
                                        <th class="border border-gray-200" colspan="2">{{ __('Retenue fiscale') }}</th>
                                        <th class="border border-gray-200" colspan="2">{{ __('Retenue CNPS') }}</th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('Retenue CMU') }}</th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('Autres retenues') }}</th>
                                        <th class="bg-success text-white border border-gray-200" rowspan="2">{{ __('Salaire Net') }}</th>
                                        <th class="border border-gray-200" colspan="3">{{ __('Charges Patronales') }}</th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('Masse salariale') }}</th>
                                        <th class="border border-gray-200" rowspan="2">{{ __('Aperçu bulletins') }}</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-gray-200" width="5%">{{ __('S.Horaire catégoriel') }}</th>
                                        <th class="border border-gray-200">{{ __('Elem. Brut') }}</th>
                                        <th class="border border-gray-200">{{ __('Brut journ') }}</th>
                                        <th class="bg-info text-white border border-gray-200">{{ __('Brut total') }}</th>
                                        <th class="border border-gray-200">{{ __('SBI') }}</th>
                                        <th class="border border-gray-200">{{ __('ITS') }}</th>
                                        <th class="border border-gray-200">{{ __('SBS') }}</th>
                                        <th class="border border-gray-200">{{ __('CNPS') }}</th>
                                        <th class="border border-gray-200">{{ __('ITS PAT') }}</th>
                                        <th class="border border-gray-200">{{ __('CNPS PAT') }}</th>
                                        <th class="border border-gray-200">{{ __('CMU PAT') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $key => $employee)
                                        @php
                                            if($joursParEmploye[$employee->id]['jours_travailles'] > 0){
                                                $joursTravailles = $joursParEmploye[$employee->id]['jours_travailles'];
                                            }else{
                                                $joursTravailles = $diff_days;
                                            }
                                            $heuresTravaillees = $joursParEmploye[$employee->id]['heures_travaillees'];
                                            $tauxJournalier = $employee->salary_horaire ?? 0;
                                            $tauxMensuel = $employee->salary ?? 0;
                                            // Récupérer les pointages et heures supplémentaires
                                            $pointagesEmployee = $joursParEmploye[$employee->id]['pointages'];
                                            $timesheetsEmployee = $joursParEmploye[$employee->id]['timesheets'];
                                            $overtimesEmployee = $joursParEmploye[$employee->id]['overtimes'];
                                            $allowancesEmployee = $joursParEmploye[$employee->id]['allowances'];
                                            $loansEmployee = $joursParEmploye[$employee->id]['loans'];
                                        @endphp
                                        <tr>
                                            <td class="border border-gray-400">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input select-row" id="check-{{ $employee->id }}">
                                                    <label class="form-check-label" for="check-{{ $employee->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="border border-gray-400" align="center">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}<hr><span style="color:yellowgreen;">{{ $employee->team ? $employee->team->name : 'N/A' }}</span></td>
                                            <td class="border border-gray-400">
                                                {{ $employee->name }}
                                                <input type="hidden" name="attendance[{{ $key }}][nbre_parts]" value="{{ $employee->parts }}">
                                                <input type="hidden" name="attendance[{{ $key }}][emp_cmu]" value="{{ $employee->cmu }}">
                                            </td>
                                            <td class="border border-gray-400">
                                                <input type="hidden" name="attendance[{{ $key }}][employee_id]" value="{{ $employee->id }}">
                                                <input type="number" name="attendance[{{ $key }}][days_worked]" class="form-control days-worked"
                                                    min="0" step="0.5" value="{{ $joursTravailles }}" data-id="{{ $employee->id }}">
                                            </td>
                                            <td class="border border-gray-400">
                                                <input type="number" name="attendance[{{ $key }}][daily_rate]" class="form-control"
                                                    min="0" value="{{ $tauxJournalier }}" data-id="{{ $employee->id }}" readonly>
                                                    <input type="number" name="attendance[{{ $key }}][daily_month]" class="form-control"
                                                    min="0" value="{{ $tauxMensuel }}" data-id="{{ $employee->id }}" hidden>
                                            </td>
                                            <td class="border border-gray-400 text-center">
                                                <button type="button" class="btn btn-sm btn-info view-brut-elements" data-bs-toggle="modal" data-bs-target="#brutAssignModal" data-employee-id="{{ $employee->id }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="brut-journ" id="brut-journ-{{ $employee->id }}">0</span>
                                            </td>
                                            <td class="bg-info-subtle border border-gray-400" align="right">
                                                <span class="brut-total" id="brut-total-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="sbi" id="sbi-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="its" id="its-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="sbs" id="sbs-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="cnps" id="cnps-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="cmu" id="cmu-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="autres" id="autres-{{ $employee->id }}">0</span>
                                            </td>
                                            <td class="bg-success-subtle border border-gray-400" align="right">
                                                <span class="net" id="net-{{ $employee->id }}">{{ $joursTravailles * $tauxJournalier }}</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="patronal its" id="patronal-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="patronal cnps" id="patronal-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="patronal cmu" id="patronal-{{ $employee->id }}">0</span>
                                            </td>
                                            <td align="right" class="border border-gray-400">
                                                <span class="masse-salariale" id="masse-{{ $employee->id }}">0</span>
                                            </td>
                                            <td class="border border-gray-400 text-center">
                                                <a href="#" id="view-bulletin-{{ $employee->id }}" class="btn btn-sm btn-primary view-bulletin" data-id="{{ $employee->id }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    document.getElementById('btnCreateTeam').addEventListener('click', function() {
        // Vérifier si des employés sont sélectionnés
        if($('.select-row:checked').length > 0) {
            // Réinitialiser les champs du formulaire
            var employeeId = $('.select-row:checked').attr('id').replace('check-', '');
            var row = $('.select-row:checked').closest('tr');
            var daysWorked = parseFloat(row.find('.days-worked').val()) || 0;
            $('#team_days').val(daysWorked);
            $('#team_name').val();
            $('#team_position').val('');

            // Afficher le modal
            $('#createTeamModal').modal('show');
        } else {
            alert('Veuillez sélectionner au moins un employé pour créer une équipe.');
        }
    });

    document.getElementById('saveTeam').addEventListener('click', function() {
        const teamName = $('#team_name').val();
        const teamPosition = $('#team_position').val();

        if (!teamName) {
            alert('Veuillez saisir un nom d\'équipe');
            return;
        }

        // Récupérer les IDs des employés sélectionnés
        const selectedEmployees = [];
        $('.select-row:checked').each(function() {
            const empId = $(this).attr('id').replace('check-', '');
            selectedEmployees.push(empId);
        });

        if (selectedEmployees.length === 0) {
            alert('Veuillez sélectionner au moins un employé pour cette équipe');
            return;
        }

        // Envoyer les données au serveur
        $.ajax({
            url: '{{ route("setsalary.journaliers.createTeam") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: teamName,
                position: teamPosition,
                employees: selectedEmployees
            },
            success: function(response) {
                if (response.success) {
                    alert('Équipe créée avec succès!');
                    location.reload(); // Recharger la page pour afficher la nouvelle équipe
                } else {
                    alert('Erreur lors de la création de l\'équipe: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Erreur de serveur lors de la création de l\'équipe: ' + xhr.responseText);
            }
        });
    });

    $(document).ready(function() {
        // Afficher les membres d'une équipe
        $(document).on('click', '.view-team-members', function(e) {
            e.preventDefault();
            const teamId = $(this).data('team-id');
            const teamName = $(this).data('team-name');

            $('#team-name-display').text(teamName);

            // Charger les membres de l'équipe
            $.ajax({
                url: '{{ route("setsalary.journaliers.getTeamMembers", ":teamId") }}'.replace(':teamId', teamId),
                type: 'GET',
                data: {
                    team_id: teamId
                },
                success: function(response) {
                    const tbody = $('#team-members-body');
                    tbody.empty();

                    if (response.members && response.members.length > 0) {
                        response.members.forEach(function(member) {
                            const row = `
                                <tr>
                                    <td>#EMP00000${member.employee_id}</td>
                                    <td>${member.name}</td>
                                    <td>
                                        <div class="action-btn ms-2">
                                            <a href="#" class="btn btn-sm bg-danger align-items-center remove-from-team" data-employee-id="${member.id}" data-team-id="${teamId}">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        tbody.append('<tr><td colspan="3" class="text-center">Aucun membre dans cette équipe</td></tr>');
                    }

                    $('#teamMembersModal').modal('show');
                },
                error: function() {
                    alert('Erreur lors du chargement des membres de l\'équipe');
                }
            });
        });

        // Supprimer un membre de l'équipe
        $(document).on('click', '.remove-from-team', function(e) {
            e.preventDefault();
            const empId = $(this).data('employee-id');
            const teamId = $(this).data('team-id');
            const row = $(this).closest('tr');

            if (confirm('Êtes-vous sûr de vouloir retirer cet employé de l\'équipe?')) {
                $.ajax({
                    url: '{{ route("setsalary.journaliers.removeFromTeam") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: empId,
                        team_id: teamId
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                            alert('Employé retiré de l\'équipe avec succès!');
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression du membre');
                    }
                });
            }
        });

        // Supprimer une équipe
        $(document).on('click', '.delete-team', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cette équipe?')) {
                const teamId = $(this).data('team-id');

                $.ajax({
                    url: '{{ route("setsalary.journaliers.deleteTeam") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        team_id: teamId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Équipe supprimée avec succès!');
                            location.reload();
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression de l\'équipe');
                    }
                });
            }
        });

         // Afficher les membres de l'équipe
        $('#ajoutEmpTeam').on('click', function() {
            $('#add-members-form').toggle();
        });

        // Cancel adding members
        $('#cancel-add').on('click', function() {
            $('#add-members-form').hide();
        });

        // Confirm adding members to team
        $('#confirm-add').on('click', function() {
            const teamId = $(this).closest('.modal').data('team-id');
            const selectedEmployees = $('#employees-to-add').val();

            if (!teamId) {
                alert('ID équipe manquant');
                return;
            }

            if (!selectedEmployees || selectedEmployees.length === 0) {
                alert('Veuillez sélectionner des employés à ajouter');
                return;
            }

            $.ajax({
                url: '{{ route("setsalary.journaliers.addToTeam") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    team_id: teamId,
                    employee_ids: selectedEmployees
                },
                success: function(response) {
                    if (response.success) {
                        alert('Employés ajoutés avec succès!');

                        // Refresh members table
                        const tbody = $('#team-members-body');
                        response.members.forEach(function(member) {
                            const row = `
                                <tr>
                                    <td>#EMP00000${member.employee_id}</td>
                                    <td>${member.name}</td>
                                    <td>
                                        <div class="action-btn ms-2">
                                            <a href="#" class="btn btn-sm bg-danger align-items-center remove-from-team"
                                            data-employee-id="${member.id}" data-team-id="${teamId}">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        // Hide form and reset select
                        $('#add-members-form').hide();
                        $('#employees-to-add').val('');

                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erreur lors de l\'ajout des membres');
                }
            });
        });

        // Store team ID when opening members modal
        $(document).on('click', '.view-team-members', function() {
            const teamId = $(this).data('team-id');
            $('#teamMembersModal').data('team-id', teamId);
        });

        // Visualiser le bulletin
        $('.view-bulletin').on('click', function(e) {
            e.preventDefault();
            var employeeId = $(this).data('id');
            // Rediriger vers la page de visualisation du bulletin ou ouvrir un modal
            // window.open('/payslip/pdf/' + employeeId, '_blank');
            alert('Fonctionnalité de prévisualisation pour l\'employé #' + employeeId + ' à implémenter');
        });
    });

    $(document).ready(function() {
        // Initialisation du tableau avec DataTables
        var table = $('#journaliers-table').DataTable({
            pageLength: 50,
            language: {
                url: '{{ asset("assets/js/datatable-fr.json") }}',
                // Configuration en français au cas où le fichier JSON ne se charge pas
                processing: "Traitement en cours...",
                search: "Rechercher&nbsp;:",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                infoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
                infoFiltered: "(filtré de _MAX_ éléments au total)",
                infoPostFix: "",
                loadingRecords: "Chargement en cours...",
                zeroRecords: "Aucun élément à afficher",
                emptyTable: "Aucune donnée disponible dans le tableau",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: 'CSV',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Imprimer',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            columnDefs: [
                { orderable: false, targets: [0, 3, 4] }
            ]
        });

        // Récupérer le montant du transport selon la ville
        const transportJournalier = parseFloat($('#tp_brut').val()) / 26;

        // Sélectionner/désélectionner toutes les lignes
        $('#select-all').on('change', function() {
            $('.select-row').prop('checked', $(this).is(':checked'));
        });

        // Sélectionner les employés d'une équipe
        $(document).on('click', '.select-team', function(e) {
            e.preventDefault();
            const teamId = $(this).data('team-id');

            // Décocher toutes les cases d'abord
            $('.select-row').prop('checked', false);

            // Récupérer les employés de l'équipe via AJAX
            $.ajax({
                url: '{{ route("setsalary.journaliers.getTeamMembersLine", ":teamId") }}'.replace(':teamId', teamId),
                type: 'GET',
                success: function(response) {
                    if (response.members && response.members.length > 0) {
                    // Cocher les cases des employés de l'équipe
                    response.members.forEach(function(member) {
                        // Cocher la case
                        $('#check-' + member.id).prop('checked', true);
                        // Sélectionner visuellement la ligne
                        $('#check-' + member.id).closest('tr').addClass('table-primary');
                    });
                    // Faire défiler jusqu'au premier employé sélectionné
                    $('html, body').animate({
                        scrollTop: $('#check-' + response.members[0].id).closest('tr').offset().top - 100
                    }, 500);
                    } else {
                    alert('Aucun employé dans cette équipe');
                    }
                },
                error: function() {
                    alert('Erreur lors de la sélection des membres de l\'équipe');
                }
            });

            // Enlever la sélection visuelle quand on décoche
            $(document).on('change', '.select-row', function () {
                if (!$(this).prop('checked')) {
                    $(this).closest('tr').removeClass('table-primary');
                }
            });
        });

        // Fonction pour calculer les éléments du brut pour un employé
        function calculateBrutElements(salaireBase, salaireBaseJournalier, joursTravailles, brutJournalierTarget) {
            // Si un montant cible du brut journalier est fourni, on l'utilise pour le calcul inverse
            if (brutJournalierTarget && brutJournalierTarget > 0) {
                // Transport légal journalier (déjà défini en haut)

                // Gratification journalière (75% du salaire de base pour 360 jours)
                const gratificationJournaliere = (0.75 * salaireBaseJournalier * 26) / 360;

                // Initialisation du sursalaire
                let sursalaireInitial = brutJournalierTarget - (salaireBaseJournalier + transportJournalier + gratificationJournaliere);
                let totalActuel = 0;
                let delta = 0.1; // Précision souhaitée
                let maxIterations = 10000; // Éviter une boucle infinie
                let iterations = 0;

                while (Math.abs(Math.round(totalActuel) - brutJournalierTarget) > 0.5 && iterations < maxIterations) {
                    // Calcul des éléments variables
                    const baseConge = salaireBaseJournalier + gratificationJournaliere + sursalaireInitial;
                    const allocationConge = (baseConge * 2.2) / 30;
                    const brutSansPrime = salaireBaseJournalier + transportJournalier + gratificationJournaliere + sursalaireInitial + allocationConge;
                    const primePrecarite = brutSansPrime * 0.03;

                    totalActuel = brutSansPrime + primePrecarite;

                    // Ajuster le sursalaire
                    if (totalActuel < brutJournalierTarget) {
                        sursalaireInitial += delta;
                    } else {
                        sursalaireInitial -= delta;
                    }

                    iterations++;
                }

                const sursalaire = sursalaireInitial;
                // Calculs finaux avec le sursalaire trouvé
                const baseConge = salaireBaseJournalier + gratificationJournaliere + sursalaire;
                const allocationConge = (baseConge * 2.2) / 30;
                const brutSansPrime = salaireBaseJournalier + transportJournalier + gratificationJournaliere + sursalaire + allocationConge;
                const primePrecarite = brutSansPrime * 0.03;

                // Total brut journalier
                const brutJournalier = brutSansPrime + primePrecarite;

                return {
                    salaireBase: Math.round(salaireBaseJournalier),
                    transport: Math.round(transportJournalier),
                    gratification: Math.round(gratificationJournaliere),
                    sursalaire: Math.round(sursalaire),
                    allocation: Math.round(allocationConge),
                    precarite: Math.round(primePrecarite),
                    brutTotal: Math.round(brutJournalier),
                    brutMensuel: (Math.round(brutJournalier) * joursTravailles),
                };
            } else {
                // Calcul direct sans cible
                // Salaire de base journalier
                const gratificationJournaliere = (0.75 * salaireBaseJournalier * 26) / 360;
                const sursalaire = 0; // Par défaut, pas de sursalaire

                // Base pour le calcul de l'allocation congé
                const baseConge = salaireBaseJournalier + gratificationJournaliere + sursalaire;
                const allocationConge = (baseConge * 2.2) / 30;

                // Brut sans prime de précarité
                const brutSansPrime = salaireBaseJournalier + transportJournalier + gratificationJournaliere + sursalaire + allocationConge;
                const primePrecarite = brutSansPrime * 0.03;

                // Total brut journalier
                const brutJournalier = brutSansPrime + primePrecarite;

                return {
                    salaireBase: Math.round(salaireBaseJournalier),
                    transport: Math.round(transportJournalier),
                    gratification: Math.round(gratificationJournaliere),
                    sursalaire: Math.round(sursalaire),
                    allocation: Math.round(allocationConge),
                    precarite: Math.round(primePrecarite),
                    brutTotal: Math.round(brutJournalier),
                    brutMensuel: Math.round(brutJournalier * joursTravailles)
                };
            }
        }

        // Fonction pour calculer les retenues et charges
        function calculateRetenues(brutMensuel) {
            // Salaire brut imposable (SBI) - 92% du brut est imposable
            const sbi = brutMensuel * 0.92;

            // Récupérer le nombre de parts
            const nbre_parts = parseInt($('input[name="attendance[0][nbre_parts]"]').val()) || 1;
            const emp_cmu = parseInt($('input[name="attendance[0][emp_cmu]"]').val()) || 1;

            // Calcul du salaire journalier imposable
            const day_salary = sbi / 26;
            const nbre_jours = 26; // Nombre de jours standard par mois

            // Calcul de l'ITS selon les tranches
            let its = 0;

            if (day_salary > 0) {
                if (day_salary <= 2500) {
                    its = 0;
                } else if (day_salary <= 8000) {
                    its = ((day_salary - 2500) * 0.16);
                } else if (day_salary <= 26667) {
                    its = ((5500 * 0.16) + ((day_salary - 8000) * 0.21));
                } else if (day_salary <= 80000) {
                    its = ((5500 * 0.16) + (18667 * 0.21) + ((day_salary - 26667) * 0.24));
                } else if (day_salary <= 266667) {
                    its = ((5500 * 0.16) + (18667 * 0.21) + (53333 * 0.24) + ((day_salary - 80000) * 0.28));
                } else {
                    its = ((5500 * 0.16) + (18667 * 0.21) + (53333 * 0.24) + (186667 * 0.28) + ((day_salary - 266667) * 0.32));
                }
                its = Math.round(its * nbre_jours);
            }

            // Réduction d'impôt selon le nombre de parts
            const reductionImpot = {
                1: 0,
                1.5: 183,
                2: 367,
                2.5: 550,
                3: 733,
                3.5: 917,
                4: 1100,
                4.5: 1283,
                5: 1467
            };

            const reduction = (reductionImpot[nbre_parts] || 0) * nbre_jours;
            its = Math.max(0, its - reduction);

            // Salaire brut social (SBS) - 97% du brut est soumis aux cotisations
            const sbs = brutMensuel * 0.97;

            // CNPS employé - 6.3% du SBS plafonné à 1647433 FCFA
            const plafondCNPS = 1647433;
            const baseCNPS = Math.min(sbs, plafondCNPS);
            const cnps = baseCNPS * 0.063;

            // CMU calculation
            let coticmu, coticmuemp;
            if (emp_cmu < 7) {
                coticmu = Math.round(emp_cmu * 500);
                coticmuemp = emp_cmu * 500;
            } else {
                coticmu = 3000 + Math.round((emp_cmu - 6) * 1000);
                coticmuemp = 3000;
            }

            const cmu = Math.round(coticmu/2);

            // Charges patronales
            const itsPatronal = sbi * 0.120; // 80% de l'ITS
            const cnpsPatronal = baseCNPS * 0.175; // 17.5% du SBS plafonné
            const cmuPatronal = Math.round(coticmuemp/2)

            // Salaire net
            const net = brutMensuel - its - cnps - cmu;

            // Masse salariale (brut + charges patronales)
            const masseSalariale = brutMensuel + itsPatronal + cnpsPatronal + cmuPatronal;

            return {
                sbi: Math.round(sbi),
                its: Math.round(its),
                sbs: Math.round(sbs),
                cnps: Math.round(cnps),
                cmu: Math.round(cmu),
                net: Math.round(net),
                itsPatronal: Math.round(itsPatronal),
                cnpsPatronal: Math.round(cnpsPatronal),
                cmuPatronal: Math.round(cmuPatronal),
                masseSalariale: Math.round(masseSalariale)
            };
        }

        // Fonction pour mettre à jour tous les calculs dans une ligne
        function calculateSalary(row, brutJournalierTarget = 0) {
            const employeeId = row.find('.select-row').attr('id').replace('check-', '');
            const joursTravailles = parseFloat(row.find('.days-worked').val()) || 0;
            const tauxMensuel = parseFloat(row.find('input[name^="attendance"][name$="[daily_month]"]').val()) || 0;
            brutJournalierTarget = parseFloat($('#montant').val()) || 0;

            // Calcul du salaire horaire (salaire mensuel / 173.33)
            const tauxHoraire = tauxMensuel > 0 ? tauxMensuel / 173.33 : 0;
            // Mettre à jour le champ du taux horaire (lecture seule)
            row.find('input[name^="attendance"][name$="[daily_rate]"]').val(Math.round(tauxHoraire.toFixed(0)));

            // Calcul du salaire journalier (salaire mensuel / 26)
            const tauxJournalier = tauxMensuel > 0 ? tauxMensuel / 26 : 0;

            // Calculer les éléments du brut
            const brutElements = calculateBrutElements(tauxMensuel, tauxJournalier, joursTravailles, brutJournalierTarget);

            // Mettre à jour le montant du brut journalier affiché
            row.find('.brut-journ').text(brutElements.brutTotal.toFixed(0));

            // Calculer le brut total (brut journalier * jours travaillés)
            const brutTotal = brutElements.brutMensuel;
            row.find('.brut-total').text(brutTotal.toFixed(0));

            // Calculer les retenues et charges
            const retenues = calculateRetenues(brutTotal);

            // Mettre à jour les montants affichés
            row.find('.sbi').text(retenues.sbi.toFixed(0));
            row.find('.its').text(retenues.its.toFixed(0));
            row.find('.sbs').text(retenues.sbs.toFixed(0));
            row.find('.cnps').text(retenues.cnps.toFixed(0));
            row.find('.cmu').text(retenues.cmu.toFixed(0));
            row.find('.net').text(retenues.net.toFixed(0));
            row.find('.patronal.its').text(retenues.itsPatronal.toFixed(0));
            row.find('.patronal.cnps').text(retenues.cnpsPatronal.toFixed(0));
            row.find('.patronal.cmu').text(retenues.cmuPatronal.toFixed(0));
            row.find('.masse-salariale').text(retenues.masseSalariale.toFixed(0));

            // Stocker les données calculées pour l'utilisation dans le modal
            row.data('brutElements', brutElements);
        }

        // Recalculer lors de la modification des jours ou du taux
        $(document).on('change keyup', '.days-worked, input[name^="attendance"][name$="[daily_month]"]', function() {
             ($(this).closest('tr'));
        });

        // Afficher les éléments du brut dans le modal
        $(document).on('click', '.view-brut-elements', function() {
            const employeeId = $(this).data('employee-id');
            const row = $('#check-' + employeeId).closest('tr');
            const brutElements = row.data('brutElements');

            if (brutElements) {
                $('#salaire_base').text(brutElements.salaireBase.toFixed(0));
                $('#tp_journalier').text(brutElements.transport.toFixed(0));
                $('#gratif_journalier').text(brutElements.gratification.toFixed(0));
                $('#leave_journalier').text(brutElements.allocation.toFixed(0));
                $('#preca_journalier').text(brutElements.precarite.toFixed(0));
                $('#sursa_journalier').text(brutElements.sursalaire.toFixed(0));
                $('#total_brut_journalier').text(brutElements.brutTotal.toFixed(0));
            } else {
                // Si les calculs n'ont pas encore été effectués
                const tauxMensuel = parseFloat(row.find('input[name^="attendance"][name$="[daily_month]"]').val()) || 0;
                const tauxJournalier = tauxMensuel / 26;
                const joursTravailles = parseFloat(row.find('.days-worked').val()) || 0;
                const newBrutElements = calculateBrutElements(tauxMensuel, tauxJournalier, joursTravailles);

                $('#salaire_base').text(newBrutElements.salaireBase.toFixed(0));
                $('#tp_journalier').text(newBrutElements.transport.toFixed(0));
                $('#gratif_journalier').text(newBrutElements.gratification.toFixed(0));
                $('#leave_journalier').text(newBrutElements.allocation.toFixed(0));
                $('#preca_journalier').text(newBrutElements.precarite.toFixed(0));
                $('#sursa_journalier').text(newBrutElements.sursalaire.toFixed(0));
                $('#total_brut_journalier').text(newBrutElements.brutTotal.toFixed(0));

                // Stocker pour utilisation future
                row.data('brutElements', newBrutElements);
            }
        });

        // Ouvrir le modal pour appliquer au groupe
        $('#btnGroupAssign').on('click', function() {
            if($('.select-row:checked').length > 0) {
                // Réinitialiser les valeurs du modal
                var employeeId = $('.select-row:checked').attr('id').replace('check-', '');
                var row = $('.select-row:checked').closest('tr');
                var daysWorked = parseFloat(row.find('.days-worked').val()) || 0;

                $('#group_days').val(daysWorked);
                $('#group_days').addClass('col-md-12');
                $('#group_days_div').addClass('col-md-12');
                $('#group_rate_div').hide();

                $('#groupAssignModal').modal('show');
            } else {
                alert('Veuillez sélectionner au moins un employé.');
            }
        });

        // Ouvrir le modal pour appliquer à l'employé
        $('#btnEmpAssign').on('click', function() {
            if($('.select-row:checked').length === 1) {
                var employeeId = $('.select-row:checked').attr('id').replace('check-', '');
                var row = $('.select-row:checked').closest('tr');
                var daysWorked = parseFloat(row.find('.days-worked').val()) || 0;
                var salaryMonth = parseFloat(row.find('input[name^="attendance"][name$="[daily_month]"]').val()) || 0;

                // Récupérer les données actuelles de l'employé
                $('#group_days').val(daysWorked);
                $('#group_rate').val(salaryMonth);

                // Titre du modal
                $('#groupAssignModal .modal-title').text('Appliquer à l\'employé sélectionné');

                $('#groupAssignModal').modal('show');
            } else if($('.select-row:checked').length === 0) {
                alert('Veuillez sélectionner un employé.');
            } else {
                alert('Veuillez sélectionner un seul employé à la fois.');
            }
        });

        // Traitement pour appliquer à une équipe
        $('#btnTeamAssign').on('click', function() {
            // Vérifie s'il y a des employés sélectionnés (pas des équipes)
            if($('.select-row:checked').length > 0) {
                // Réinitialiser les valeurs du modal
                var employeeId = $('.select-row:checked').attr('id').replace('check-', '');
                var row = $('.select-row:checked').closest('tr');
                var daysWorked = parseFloat(row.find('.days-worked').val()) || 0;
                $('#team_days').val(daysWorked); // Utiliser la valeur par défaut si disponible
                $('#team_rate').val('');
                $('#team_montant').val('');
                $('#resultat_team').html('');

                $('#teamAssignModal').modal('show');
            } else {
                alert('Veuillez sélectionner au moins un employé pour cette équipe.');
            }
        });

        // Appliquer les valeurs au groupe ou à l'employé sélectionné
        $('#applyToGroup').on('click', function() {
            var days = parseFloat($('#group_days').val());
            var salaryMonth = parseFloat($('#group_rate').val());
            var brutJournalierTarget = parseFloat($('#montant').val()) || 0;

            $('.select-row:checked').each(function() {
                var row = $(this).closest('tr');

                if(!isNaN(days)) {
                    row.find('.days-worked').val(days);
                }

                if(!isNaN(salaryMonth)) {
                    row.find('input[name^="attendance"][name$="[daily_month]"]').val(salaryMonth);
                    // Le taux horaire sera calculé automatiquement
                }

                calculateSalary(row, brutJournalierTarget);
            });

            $('#groupAssignModal').modal('hide');

            // Remettre le titre par défaut
            $('#groupAssignModal .modal-title').text('Appliquer au groupe sélectionné');
        });

        // Appliquer les valeurs au groupe ou à l'employé sélectionné
        $('#applyToTeam').on('click', function() {
            var days = parseFloat($('#team_days').val());
            var salaryMonth = parseFloat($('#group_rate').val());
            var brutJournalierTarget = parseFloat($('#team_montant').val()) || 0;

            $('.select-row:checked').each(function() {
                var row = $(this).closest('tr');

                if(!isNaN(days)) {
                    row.find('.days-worked').val(days);
                }

                if(!isNaN(salaryMonth)) {
                    row.find('input[name^="attendance"][name$="[daily_month]"]').val(salaryMonth);
                    // Le taux horaire sera calculé automatiquement
                }

                calculateSalary(row, brutJournalierTarget);
            });

            $('#teamAssignModal').modal('hide');
        });

        // Réinitialiser les valeurs
        $('#btnReset').on('click', function() {
            if(confirm('Êtes-vous sûr de vouloir réinitialiser toutes les valeurs?')) {
                $('.select-row:checked').each(function() {
                    var row = $(this).closest('tr');
                    row.find('.days-worked').val(0);
                    // Ne pas réinitialiser le taux journalier car c'est une valeur de base
                    calculateSalary(row);
                });
            }
        });

        // Mise à jour de la fonction calculerMontants pour utiliser la fonction de calcul existante
        window.calculerMontants = function() {
            const montantBrutJournalier = parseFloat(document.getElementById('montant').value);
            if (isNaN(montantBrutJournalier) || montantBrutJournalier <= 0) {
                document.getElementById('resultat').innerText = "Veuillez entrer un montant valide pour le brut journalier.";
                return;
            }

            // On prend une valeur de base standard pour le calcul
            const salaireBaseJournalier = 2885; // Valeur par défaut si nécessaire

            // Utiliser la fonction de calcul existante
            const brutElements = calculateBrutElements(salaireBaseJournalier * 26, salaireBaseJournalier, 1, montantBrutJournalier);

            // Affichage des résultats
            document.getElementById('resultat').innerHTML = `
                <div class="card bg-warning-subtle p-3">
                    <h5>Répartition suggérée pour un brut journalier de ${montantBrutJournalier.toLocaleString()} FCFA</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Élément</th>
                                    <th class="text-end">Montant (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Salaire de base</td>
                                    <td class="text-end">${brutElements.salaireBase.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Transport</td>
                                    <td class="text-end">${brutElements.transport.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Gratification</td>
                                    <td class="text-end">${brutElements.gratification.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Sursalaire</td>
                                    <td class="text-end">${brutElements.sursalaire.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Allocation congé</td>
                                    <td class="text-end">${brutElements.allocation.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Prime précarité</td>
                                    <td class="text-end">${brutElements.precarite.toLocaleString()}</td>
                                </tr>
                                <tr class="table-info">
                                    <th>TOTAL</th>
                                    <th class="text-end">${brutElements.brutTotal.toLocaleString()}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        };

        // Mise à jour de la fonction calculerMontants pour utiliser la fonction de calcul existante
        window.calculerMontantsTeam = function() {
            const montant = parseFloat($('#team_montant').val());

            if (isNaN(montant) || montant <= 0) {
                document.getElementById('resultat_team').innerText = "Veuillez entrer un montant valide pour le brut journalier.";
                return;
            }

            // On prend une valeur de base standard pour le calcul
            const salaireBaseJournalier = 2885; // Valeur par défaut si nécessaire

            // Utiliser la fonction de calcul existante
            const brutElements = calculateBrutElements(salaireBaseJournalier * 26, salaireBaseJournalier, 1, montant);

            let html = `
                <div class="card bg-warning-subtle p-3">
                    <h5>Répartition suggérée pour un brut journalier de ${montant.toLocaleString()} FCFA</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Élément</th>
                                    <th class="text-end">Montant (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Salaire de base</td>
                                    <td class="text-end">${brutElements.salaireBase.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Transport</td>
                                    <td class="text-end">${brutElements.transport.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Gratification</td>
                                    <td class="text-end">${brutElements.gratification.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Sursalaire</td>
                                    <td class="text-end">${brutElements.sursalaire.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Allocation congé</td>
                                    <td class="text-end">${brutElements.allocation.toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td>Prime précarité</td>
                                    <td class="text-end">${brutElements.precarite.toLocaleString()}</td>
                                </tr>
                                <tr class="table-info">
                                    <th>TOTAL</th>
                                    <th class="text-end">${brutElements.brutTotal.toLocaleString()}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            $('#resultat_team').html(html);
        };

        // Calculer tous les salaires au chargement
        $('tbody tr').each(function() {
            calculateSalary($(this));
        });
    });

</script>
@endpush
