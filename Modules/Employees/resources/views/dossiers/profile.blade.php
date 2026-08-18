@extends('layouts.admin')

@section('page-title')
    {{ __('Dossier du personnel') }}
@endsection

@section('breadcrumb')
    <!--<div class="d-flex align-items-center gap-3 me-4 me-sm-0">
        <span class="bg-label-info p-1 rounded">
            <i class="ti ti-user-plus ti-xl"></i>
        </span>
        <div class="content-right">
            <p><a href="{{ route('home') }}" class="text-info">{{ __('Home') }}</a> / {{ __('Dossier du personnel') }}</p>
        </div>
    </div>-->
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Dossier du personnel') }}</li>
@endsection

{{-- @section('action-button')
    <a class="btn btn-sm btn-primary collapsed" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button"
        aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{ __('Filter') }}">
        <i class="ti ti-filter"></i>
    </a>
@endsection --}}


@php
    // $profile = asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');

@endphp
@section('content')
    <!-- User Pills -->
    <ul class="nav nav-pills mb-2">
        <li class="nav-item">
            <a class="nav-link active" href="#actif" data-bs-toggle="tab">
                <i class="ti ti-user-check ti-xs me-1"></i>
                <span class="fw-bold">{{ __('Personnel Actif') }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#inactif" data-bs-toggle="tab">
                <i class="ti ti-user-x ti-xs me-1"></i>
                <span class="fw-bold">{{ __('Personnel Inactif') }}</span>
            </a>
        </li>
    </ul>
    <!--/ User Pills -->
    <div class="col-xl-12 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="tab-content">
            <div id="actif" class="tab-pane active" role="tabpanel" aria-labelledby="actif-tab">
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @foreach($employees as $employee)
                        @if ($employee->is_active == 1)
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-end mb-3">
                                            <div class="btn-group card-option">
                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('Edit Employee')
                                                        <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                                                            class="dropdown-item" data-url="" data-size="md" data-ajax-popup="true"
                                                            data-title="{{ __('Edit employee') }}"><i class="ti ti-edit "></i><span
                                                                class="ms-2">{{ __('Edit') }}</span></a>
                                                    @endcan

                                                    @can('Delete Employee')
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['employee.destroy', $employee->id],
                                                            'id' => 'delete-form-' . $employee->id,
                                                        ]) !!}
                                                        <a href="#" class="bs-pass-para dropdown-item"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $employee->id }}"
                                                            title="{{ __('Delete') }}"><i class="ti ti-trash"></i><span
                                                                class="ms-2">{{ __('Delete') }}</span></a>
                                                        {!! Form::close() !!}
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            @php
                                                $hasPhoto = false;
                                            @endphp
                                            @foreach ($avatar as $photo)
                                                @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                    @php
                                                        $hasPhoto = true;
                                                    @endphp
                                                    <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('uploads/document')) . '/' .$photo->document_value }}" height="110" width="110" alt="User avatar">
                                                    @break
                                                @endif
                                            @endforeach
                                            @if (!$hasPhoto)
                                                <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('uploads/avatar')) }}/avatar.png" height="110" width="110" alt="User avatar">
                                            @endif
                                            <h4>{{$employee->name}}</h4>
                                            <span class="badge bg-label-secondary mt-1">{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                            <hr>
                                            @can('Show Employee Profile')
                                                <a class="btn btn-outline-primary"
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @else
                                                <a class="btn btn-outline-primary"
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @endcan
                                            @if ($employee['is_active'] == 3)
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">Sanctionné</span>
                                            @elseif ($employee['is_active'] == 4)
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">En congé</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div id="inactif" class="tab-pane" role="tabpanel" aria-labelledby="inactif-tab">
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @forelse($employees as $employee)
                        @if ($employee->is_active != 1)
                            <div class="col-xl-3 col-lg-4 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div></div>
                                            <div class="card-header-right">
                                                <i class="ti ti-lock text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            @php
                                                $hasPhoto = false;
                                            @endphp
                                            @foreach ($avatar as $photo)
                                                @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                    @php
                                                        $hasPhoto = true;
                                                    @endphp
                                                    <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('uploads/document')) . '/' .$photo->document_value }}" height="110" width="110" alt="User avatar">
                                                    @break
                                                @endif
                                            @endforeach
                                            @if (!$hasPhoto)
                                                <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('uploads/avatar')) }}/avatar.png" height="110" width="110" alt="User avatar">
                                            @endif
                                            <h4>{{$employee->name}}</h4>
                                            <span class="badge bg-label-secondary mt-1">{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                            <hr>
                                            @can('Show Employee Profile')
                                                <a class="btn btn-outline-primary"
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @else
                                                <a class="btn btn-outline-primary"
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                            @endcan
                                            @if ($employee['is_active'] == 3)
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">Sanctionné</span>
                                            @elseif ($employee['is_active'] == 4)
                                                <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">En congé</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-12">
                            <div class="text-center">
                                <h6>Aucun employé inactif trouvé</h6>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            // getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch]', function() {
            var branch_id = $(this).val();

            getDepartment(branch_id);
        });

        function getDepartment(bid) {

            $.ajax({
                url: '{{ route('monthly.getdepartment') }}',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {

                    $('.department_id').empty();
                    var emp_selct = `<select class="department_id form-control multi-select" id="choices-multiple" multiple="" required="required" name="department_id[]">
                </select>`;
                    $('.department_div').html(emp_selct);

                    $('.department_id').append('<option value=""> {{ __('Select Department') }} </option>');
                    $.each(data, function(key, value) {
                        $('.department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
              "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append('<option value="">{{ __('Select Designation') }}</option>');
                    $.each(data, function(key, value) {
                        $('#designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        }
    </script>
@endpush
