@extends('layouts.app')
@section('title', __('lang.employee'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.all_employees')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.profile_photo')</th>
                                            <th>@lang('lang.employee_name')</th>
                                            <th>@lang('lang.email')</th>
                                            <th>@lang('lang.mobile')</th>
                                            <th>@lang('lang.job_title')</th>
                                            <th>@lang('lang.wage')</th>
                                            <th>@lang('lang.annual_leave_balance')</th>
                                            <th>@lang('lang.age')</th>
                                            <th>@lang('lang.start_working_date')</th>
                                            <th>@lang('lang.current_status')</th>
                                            <th>@lang('lang.store')</th>
                                            <th>@lang('lang.pos')</th>
                                            <th class="notexport">@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($employees as $employee)
                                        <tr>
                                            <td><img src="@if(!empty($employee->getFirstMediaUrl('employee_photo'))){{$employee->getFirstMediaUrl('employee_photo')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                                alt="photo" width="50" height="50">
                                            </td>
                                            <td>
                                                {{$employee->name}}
                                            </td>
                                            <td>
                                                {{$employee->email}}
                                            </td>
                                            <td>
                                                {{$employee->mobile}}
                                            </td>
                                            <td>
                                                {{$employee->job_title}}
                                            </td>
                                            <td>
                                                {{$employee->fixed_wage_value}}
                                            </td>
                                            <td>
                                                {{App\Models\Employee::getBalanceLeave($employee->id)}}
                                            </td>
                                            <td>
                                                @if(!empty($employee->date_of_birth))
                                                {{\Carbon\Carbon::parse($employee->date_of_birth)->diff(\Carbon\Carbon::now())->format('%y')}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($employee->date_of_start_working))
                                                {{@format_date($employee->date_of_start_working)}}
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                $today_on_leave = App\Models\Leave::where('employee_id',
                                                $employee->id)->whereDate('end_date',
                                                '>=', date('Y-m-d'))->whereDate('start_date', '<=', date('Y-m-d'))->
                                                    where('status',
                                                    'approved')->first();
                                                    @endphp
                                                    @if (!empty($today_on_leave))
                                                    <label for=""
                                                        style="font-weight: bold; color: red">@lang('lang.on_leave')</label>
                                                    @else
                                                    @php
                                                    $status_today = App\Models\Attendance::where('employee_id',
                                                    $employee->id)->whereDate('date', date('Y-m-d'))->first();
                                                    @endphp
                                                    @if(!empty($status_today))
                                                    @if($status_today->status == 'late' || $status_today->status ==
                                                    'present')
                                                    <label for=""
                                                        style="font-weight: bold; color: green">@lang('lang.on_duty')</label>
                                                    @endif
                                                    @if($status_today->status == 'on_leave')
                                                    <label for=""
                                                        style="font-weight: bold; color: red">@lang('lang.on_leave')</label>
                                                    @endif
                                                    @endif
                                                    @endif
                                            </td>
                                            <td>{{implode(', ', $employee->store->pluck('name')->toArray())}}</td>
                                            <td>{{$employee->store_pos}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">@lang('lang.action')
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                        user="menu">
                                                        @can('hr_management.employee.view')
                                                        <li>
                                                            <a href="{{action('EmployeeController@show', $employee->id)}}"
                                                                class="btn"><i class="fa fa-eye"></i> @lang('lang.view')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        @endcan
                                                        @can('hr_management.employee.create_and_edit')
                                                        <li>
                                                            <a href="{{action('EmployeeController@edit', $employee->id)}}"
                                                                class="btn edit_employee"><i
                                                                    class="fa fa-pencil-square-o"></i> @lang('lang.edit')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        @endcan
                                                        @can('hr_management.employee.delete')
                                                        <li>
                                                            <a data-href="{{action('EmployeeController@destroy', $employee->id)}}"
                                                                data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                                class="btn delete_item text-red"><i class="fa fa-trash"></i> @lang('lang.delete')</a>
                                                        </li>
                                                        @endcan
                                                        @can('hr_management.suspend.create_and_edit')
                                                        <li>
                                                            <a data-href="{{action('EmployeeController@toggleActive', $employee->id)}}"
                                                                class="btn toggle-active"><i class="fa fa-ban"></i>@if($employee->is_active) @lang('lang.suspend') @else @lang('lang.reactivate')@endif</a>
                                                        </li>
                                                        @endcan
                                                        @can('hr_management.send_credentials.create_and_edit')
                                                        <li>
                                                            <a href="{{action('EmployeeController@sendLoginDetails', $employee->id)}}"
                                                                class="btn"><i class="fa fa-paper-plane"></i> @lang('lang.send_credentials')</a>
                                                        </li>
                                                        @endcan
                                                        @can('sms_module.sms.create_and_edit')
                                                        <li>
                                                            <a href="{{action('SmsController@create', ['employee_id' => $employee->id])}}"
                                                                class="btn"><i class="fa fa-comments-o"></i> @lang('lang.send_sms')</a>
                                                        </li>
                                                        @endcan
                                                        @can('email_module.email.create_and_edit')
                                                        <li>
                                                            <a href="{{action('EmailController@create', ['employee_id' => $employee->id])}}"
                                                                class="btn"><i class="fa fa-envelope "></i> @lang('lang.send_email')</a>
                                                        </li>
                                                        @endcan
                                                        @can('hr_management.leaves.create_and_edit')
                                                        <li>
                                                            <a class="btn btn-modal"
                                                                data-href="{{action('LeaveController@create', ['employee_id' => $employee->id])}}"
                                                                data-container=".view_modal">
                                                                <i class="fa fa-sign-out"></i> @lang( 'lang.leave')
                                                            </a>
                                                        </li>
                                                        @endcan
                                                        @can('hr_management.forfeit_leaves.create_and_edit')
                                                        <li>
                                                            <a class="btn btn-modal"
                                                                data-href="{{action('ForfeitLeaveController@create', ['employee_id' => $employee->id])}}"
                                                                data-container=".view_modal">
                                                                <i class="fa fa-ban"></i> @lang( 'lang.forfeit_leave')
                                                            </a>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        @endforeach


                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
  $(document).on('click', 'a.toggle-active', function(e) {
		e.preventDefault();

        $.ajax({
            method: 'get',
            url: $(this).data('href'),
            data: {  },
            success: function(result) {
                if (result.success == true) {
                    swal(
                    'Success',
                    result.msg,
                    'success'
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }else{
                    swal(
                    'Error',
                    result.msg,
                    'error'
                    );
                }
            },
        });
    });
</script>
@endsection
