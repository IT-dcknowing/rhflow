
    <div class="card table-responsive" style="padding: 10px;">
        <!--<table class="table" id="employee-list">
            <tbody>
                <tr>
                    @foreach($employees->take(15) as $employee)
                        <td class="list-group-item list-group-item-action {{ Request::route()->getName() == 'setsalary.employee_details' && Request::route()->parameter('id') == $employee->id ? 'active' : '' }}">
                            <a href="{{ route('setsalary.employee_details', ['id' => $employee->id, 'monthpaie' => $monthpaie]) }}" class="nav-link">
                                {{ $employee->name }}
                            </a>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>-->
        <h5 class="card-title mb-2">{{ __('Liste des Journaliers') }}</h5>
        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
            <ul class="nav-pills d-inline-flex list-unstyled mb-2">
                @foreach($employees as $employee)
                    <li class="nav-item">
                        <a class="nav-link {{ Request::route()->getName() == 'setsalary.employee_details' && Request::route()->parameter('id') == $employee->id ? 'active' : '' }}" href="{{ route('setsalary.employee_details', ['id' => $employee->id, 'monthpaie' => $monthpaie]) }}" style="padding: 10px;">
                            <i class="ti ti-user ti-xs me-1"></i> {{ $employee->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
