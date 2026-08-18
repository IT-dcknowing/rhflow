
    <div class="card table-responsive" style="padding: 10px;">
        <h5 class="card-title mb-2">{{ __('Liste des Mensuels') }}</h5>
        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
            <ul class="nav-pills d-inline-flex list-unstyled mb-2">
                @foreach($employees as $employee)
                    <li class="me-3">
                        <a class="nav-link {{ Request::route()->getName() == 'setsalary.employee_details_mois' && Request::route()->parameter('id') == $employee->id ? 'active' : '' }}" href="{{ route('setsalary.employee_details_mois', ['id' => $employee->id, 'monthpaie' => $monthpaie]) }}" style="padding: 10px;">
                            <i class="ti ti-user ti-xs me-1"></i> {{ $employee->name }}
                        </a>
                    </li>
                @endforeach
                <br>
            </ul>
        </div>
    </div>
