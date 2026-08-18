@php
	use Carbon\Carbon;
    // Définir la locale en français
    setlocale(LC_TIME, 'fr_FR.utf8');
    $date = Carbon::parse($monthpaie);
@endphp

@extends('layouts.admin')

@section('page-title')
   {{ __('Manage Employee Salary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Salary') }}</li>
@endsection


@section('content')
<div class="row">
    @include('setsalary.menu_cycle')
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success" role="alert">
            <p><strong>** Configurer les salaires de chaque employé pour le mois de traitement sélectionné. Cliquer sur le matricule de l'employé en question ou sur le bouton "Afficher".</strong></p>
            <p>** NB : <strong>Si les salaires ont été déjà configurés depuis un précédent mois qu'il n'y a pas de modification à faire, cliquer sur le bouton "Étape 8".</strong></p>
        </div>
        <div class="card">
            <div class="card-header card-body table-border-style">
                {{-- <h5></h5> --}}
                <div class="card-datatable table-responsive pt-0">
                    <table class="table" id="pc-dt-simple">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('Employee Id') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Poste') }}</th>
                                <th>{{ __('Salary') }} de base</th>
                                <th>{{ __('Net Salary') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                @if(isset($employee->statut_emp) && $employee->is_active == 4)
                                    <tr>
                                        <td align="center">
                                            @if($employee->is_active != 2)
                                                <button disabled class="btn btn-outline-primary">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </button>
                                            @else
                                                <i class="ti ti-lock" style="color: red;"></i>
                                            @endif
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td> {{ !empty(\Auth::user()->getDesignation($employee['designation_id'])) ? \Auth::user()->getDesignation($employee['designation_id'])->name : '-' }}
                                            </td>
                                        <td colspan="2" align="center">
                                            <h6>Employé indisponible</h6><span class="status_badge badge p-2 px-3 rounded" style="background-color: orange;">{{ $employee['statut_emp'] }}</span>
                                        </td>
                                        <td class="Action disabled" align="center">
                                            <span>
                                                @if($employee->is_active != 2)
                                                    <i class="ti ti-lock" style="color: orange;"></i>
                                                @else
                                                    <i class="ti ti-lock" style="color: red;"></i>
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td align="center">
                                            @if($employee->is_active != 2)
                                                <a href="{{ route('setsalary.show', ['eid' => $employee->id, 'monthpaie' => $monthpaie]) }}"
                                                    class="btn btn-outline-primary">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </a>
                                            @else
                                                <i class="ti ti-lock" style="color: red;"></i>
                                            @endif
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td> {{ !empty(\Auth::user()->getDesignation($employee['designation_id'])) ? \Auth::user()->getDesignation($employee['designation_id'])->name : '-' }}
                                            </td>
                                        <td>{{number_format($employee->salary, 0 , '.' , ' ')}} FCFA</td>
                                        <td>{{ !empty($employee->get_net_salary()) ? number_format($employee->get_net_salary(), 0 , '.' , ' ') : '' }} FCFA
                                        </td>
                                        <td class="Action" align="center">
                                            <span>
                                                @if($employee->is_active != 2)
                                                    <div class="action-btn ms-2">
                                                        <a href="{{ route('setsalary.show', ['eid' => $employee->id, 'monthpaie' => $monthpaie]) }}"
                                                            class="btn btn-sm bg-info align-items-center" data-bs-toggle="tooltip"
                                                            title="" data-bs-original-title="{{ __('View') }}">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <i class="ti ti-lock" style="color: red;"></i>
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
