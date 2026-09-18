@extends('layouts.admin')

@section('page-title')
    {{ __('Traitement des avantages') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Tableau de bord') }}</a></li>
    <li class="breadcrumb-item">{{ __('Avantages natures ou argents') }}</li>
@endsection


@section('content')
    <div class="row">
		@include('setsalary.menu_cycle')
    </div>
    <div class="row">
        <div class="col-md-12" id="paie-calculate">
            <div class="card">
                <div class="card-header">
                    <div class="row">
						<div class="col-11">
							<h5>Avantage en nature ou en argent</h5>
						</div>
						@can('Create Overtime')
							<div class="col-1 text-end">
                                <a href="#" data-size="lg" data-url="{{ route('avantages.create') }}" data-ajax-popup="true"
                                    data-bs-toggle="tooltip" title="{{ __('Créer un avantage') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
							</div>
						@endcan
					</div>
                </div>
                <div class="card-body">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="table" id="pc-dt-simple">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('Employee Id') }}</th>
                                    <th>{{ __('Nom') }}</th>
                                    <th>{{ __('Type d\'avantage') }}</th>
                                    <th>{{ __('Montant réel') }}</th>
                                    <th>{{ __('Montant selon le barème') }}</th>
                                    <th>{{ __('Traitement') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $emp)
                                    @foreach ($avantages as $avantage)
                                        @if($emp->id == $avantage->employee_Id)
                                            @foreach($traitements as $traitement)
                                                @if($avantage->traitement_id == $traitement->id)
                                                    <tr>
                                                        <td align="center"><strong  class="btn btn-outline-primary">{{ \Auth::user()->employeeIdFormat($emp->employee_id) }}</strong></td>
                                                        <td align="center">
                                                            {{$emp->name}}
                                                        </td>
                                                        <td>
                                                            @if($avantage->type_avantage == 1)
                                                                Avantage en Nature
                                                            @elseif($avantage->type_avantage == 2)
                                                                Avantage en Argent
                                                            @elseif($avantage->type_avantage == 3)
                                                                Assurance-vie complémentaire
                                                            @else
                                                                Assurance santé
                                                            @endif
                                                        </td>
                                                        <td align="right">{{ number_format($avantage->montant_reel,'0','.',' ') }} FCFA</td>
                                                        <td align="right">
                                                            @if($avantage->type_avantage == 1)
                                                                {{ number_format(($avantage->amount),'0','.',' ') }} FCFA
                                                            @else
                                                                {{ number_format(($avantage->montant_reel),'0','.',' ') }} FCFA
                                                            @endif
                                                        </td>
                                                        <td>{{ $traitement->libelle }}</td>
                                                        <td class="Action" align="center">
                                                            @if($avantage->statut == 1)
                                                                <a class="btn btn-warning" href="{{ URL::to('payslip/updateState', $avantage->id) }}">
                                                                    Suspendre
                                                                </a>
                                                                @else
                                                                <a class="btn btn-info" href="{{ URL::to('payslip/updateContinue', $avantage->id) }}">
                                                                    Reprendre
                                                                </a>
                                                            @endif
                                                            <hr>
                                                            <div class="d-flex" style="justify-content: center;">
                                                                <div class="action-btn me-2">
                                                                    <a href="#" class="btn btn-icon btn-sm btn-label-warning"
                                                                        data-size="lg"
                                                                        data-url="{{ URL::to('avantages/' . $avantage->id . '/edit') }}"
                                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                        title="" data-title="{{ __('Edit') }}"
                                                                        data-bs-original-title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil"></i>
                                                                    </a>
                                                                </div>
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['avantages.deleted', $avantage->id],
                                                                    'id' => 'delete-form-' . $avantage->id,
                                                                ]) !!}
                                                                    <div class="action-btn">
                                                                        <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                            data-bs-toggle="tooltip" title=""
                                                                            data-bs-original-title="Supprimer" aria-label="Supprimer"><i
                                                                                class="ti ti-trash"></i></a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    function status() {
        var x = document.getElementById("situation_mat").value;
        var y = document.getElementById("emp_enfts").value;
        var z = document.getElementById("emp_personnes").value;
        //var i = 0.5;
        //const STATUT_MATRIMONIAL = x;
        let nombreDeParts = 0x1;

        if (x == '2' && y == '0') {
            nombreDeParts = 2;
        } else {
            if (y > 0) {
                if (x == '1' || x == '3') {
                    nombreDeParts = 0x2 + Math.max(0x0, y - 0x1) * 0.5;
                } else if (x == '2'|| x == '4') {
                    nombreDeParts = 2.5 + Math.max(0x0, y - 0x1) * 0.5;
                }
            }
        }
        nombreDeParts = nombreDeParts + parseFloat(z);
        document.getElementById("mystatus").value = Math.min(nombreDeParts, 0x5);
        }
</script>
