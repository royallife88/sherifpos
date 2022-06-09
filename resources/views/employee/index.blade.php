@extends('layouts.app')
@section('title', __('lang.employee'))

@section('content')
    <div class="container-fluid">
        @can('hr_management.employee.create_and_edit')
            <a style="color: white" href="{{ action('EmployeeController@create') }}" class="btn btn-info"><i
                    class="dripicons-plus"></i>
                @lang('lang.add_new_employee')</a>
        @endcan
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form action="">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                        {!! Form::text('start_date', request()->start_date, ['class' => 'form-control filter']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                        {!! Form::text('start_time', request()->start_time, ['class' => 'form-control time_picker filter']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                        {!! Form::text('end_date', request()->end_date, ['class' => 'form-control filter']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                        {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker filter']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('payment_status', __('lang.payment_status'), []) !!}
                                        {!! Form::select('payment_status', ['pending' => __('lang.pending'), 'paid' => __('lang.paid')], request()->payment_status, ['class' => 'form-control filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <br>
                                    <button class="btn btn-primary mt-2 ml-2" type="submit">@lang('lang.filter')</button>
                                    <a href="{{ action('EmployeeController@index') }}"
                                        class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table" id="employee_table">
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
                                                <th>@lang('lang.commission')</th>
                                                <th>@lang('lang.total_paid')</th>
                                                <th>@lang('lang.pending')</th>
                                                <th class="notexport">@lang('lang.action')</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {{-- @foreach ($employees as $employee)
                                                <tr>
                                                    <td><img src="@if (!empty($employee->getFirstMediaUrl('employee_photo'))) {{ $employee->getFirstMediaUrl('employee_photo') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                                            alt="photo" width="50" height="50">
                                                    </td>
                                                    <td>
                                                        {{ $employee->name }}
                                                    </td>
                                                    <td>
                                                        {{ $employee->email }}
                                                    </td>
                                                    <td>
                                                        {{ $employee->mobile }}
                                                    </td>
                                                    <td>
                                                        {{ $employee->job_title }}
                                                    </td>
                                                    <td>
                                                        {{ $employee->fixed_wage_value }}
                                                    </td>
                                                    <td>
                                                        {{ App\Models\Employee::getBalanceLeave($employee->id) }}
                                                    </td>
                                                    <td>
                                                        @if (!empty($employee->date_of_birth))
                                                            {{ \Carbon\Carbon::parse($employee->date_of_birth)->diff(\Carbon\Carbon::now())->format('%y') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($employee->date_of_start_working))
                                                            {{ @format_date($employee->date_of_start_working) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $today_on_leave = App\Models\Leave::where('employee_id', $employee->id)
                                                                ->whereDate('end_date', '>=', date('Y-m-d'))
                                                                ->whereDate('start_date', '<=', date('Y-m-d'))
                                                                ->where('status', 'approved')
                                                                ->first();
                                                        @endphp
                                                        @if (!empty($today_on_leave))
                                                            <label for=""
                                                                style="font-weight: bold; color: red">@lang('lang.on_leave')</label>
                                                        @else
                                                            @php
                                                                $status_today = App\Models\Attendance::where('employee_id', $employee->id)
                                                                    ->whereDate('date', date('Y-m-d'))
                                                                    ->first();
                                                            @endphp
                                                            @if (!empty($status_today))
                                                                @if ($status_today->status == 'late' || $status_today->status == 'present')
                                                                    <label for=""
                                                                        style="font-weight: bold; color: green">@lang('lang.on_duty')</label>
                                                                @endif
                                                                @if ($status_today->status == 'on_leave')
                                                                    <label for=""
                                                                        style="font-weight: bold; color: red">@lang('lang.on_leave')</label>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ implode(', ', $employee->store->pluck('name')->toArray()) }}
                                                    </td>
                                                    <td>{{ $employee->store_pos }}</td>
                                                    @php
                                                        $logged_employee = App\Models\Employee::where('user_id', Auth::id())->first();
                                                    @endphp
                                                    @if (auth()->user()->can('hr_management.employee_commission.view'))
                                                        <td>{{ @num_format($employee->total_commission) }}</td>
                                                        <td>{{ @num_format($employee->total_commission_paid) }}</td>
                                                        <td>{{ @num_format($employee->total_commission - $employee->total_commission_paid) }}
                                                        </td>
                                                    @elseif($employee->id == $logged_employee->id)
                                                        <td>{{ @num_format($employee->total_commission) }}</td>
                                                        <td>{{ @num_format($employee->total_commission_paid) }}</td>
                                                        <td>{{ @num_format($employee->total_commission - $employee->total_commission_paid) }}
                                                        </td>
                                                    @else
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    @endif

                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-default btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">@lang('lang.action')
                                                                <span class="caret"></span>
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                                user="menu">
                                                                @can('hr_management.employee.view')
                                                                    <li>
                                                                        <a href="{{ action('EmployeeController@show', $employee->id) }}"
                                                                            class="btn"><i
                                                                                class="fa fa-eye"></i>
                                                                            @lang('lang.view')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('hr_management.employee.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('EmployeeController@edit', $employee->id) }}"
                                                                            class="btn edit_employee"><i
                                                                                class="fa fa-pencil-square-o"></i>
                                                                            @lang('lang.edit')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('hr_management.employee.delete')
                                                                    <li>
                                                                        <a data-href="{{ action('EmployeeController@destroy', $employee->id) }}"
                                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                            class="btn delete_item text-red"><i
                                                                                class="fa fa-trash"></i>
                                                                            @lang('lang.delete')</a>
                                                                    </li>
                                                                @endcan
                                                                @can('hr_management.suspend.create_and_edit')
                                                                    <li>
                                                                        <a data-href="{{ action('EmployeeController@toggleActive', $employee->id) }}"
                                                                            class="btn toggle-active"><i
                                                                                class="fa fa-ban"></i>
                                                                            @if ($employee->is_active)
                                                                                @lang('lang.suspend')
                                                                            @else
                                                                                @lang('lang.reactivate')
                                                                            @endif
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                                @can('hr_management.send_credentials.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('EmployeeController@sendLoginDetails', $employee->id) }}"
                                                                            class="btn"><i
                                                                                class="fa fa-paper-plane"></i>
                                                                            @lang('lang.send_credentials')</a>
                                                                    </li>
                                                                @endcan
                                                                @can('sms_module.sms.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('SmsController@create', ['employee_id' => $employee->id]) }}"
                                                                            class="btn"><i
                                                                                class="fa fa-comments-o"></i>
                                                                            @lang('lang.send_sms')</a>
                                                                    </li>
                                                                @endcan
                                                                @can('email_module.email.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('EmailController@create', ['employee_id' => $employee->id]) }}"
                                                                            class="btn"><i
                                                                                class="fa fa-envelope "></i>
                                                                            @lang('lang.send_email')</a>
                                                                    </li>
                                                                @endcan
                                                                @can('hr_management.leaves.create_and_edit')
                                                                    <li>
                                                                        <a class="btn btn-modal"
                                                                            data-href="{{ action('LeaveController@create', ['employee_id' => $employee->id]) }}"
                                                                            data-container=".view_modal">
                                                                            <i class="fa fa-sign-out"></i> @lang( 'lang.leave')
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                                @can('hr_management.forfeit_leaves.create_and_edit')
                                                                    <li>
                                                                        <a class="btn btn-modal"
                                                                            data-href="{{ action('ForfeitLeaveController@create', ['employee_id' => $employee->id]) }}"
                                                                            data-container=".view_modal">
                                                                            <i class="fa fa-ban"></i> @lang(
                                                                            'lang.forfeit_leave')
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @endforeach --}}


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
                data: {},
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
                    } else {
                        swal(
                            'Error',
                            result.msg,
                            'error'
                        );
                    }
                },
            });
        });

        $(document).ready(function() {
            employee_table = $("#employee_table").DataTable({
                lengthChange: true,
                paging: true,
                info: false,
                bAutoWidth: false,
                // order: [],
                language: {
                    url: dt_lang_url,
                },
                lengthMenu: [
                    [10, 25, 50, 75, 100, 200, 500, -1],
                    [10, 25, 50, 75, 100, 200, 500, "All"],
                ],
                dom: "lBfrtip",
                stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, "desc"]
                ],
                initComplete: function() {
                    $(this.api().table().container()).find('input').parent().wrap('<form>').parent()
                        .attr('autocomplete', 'off');
                },
                ajax: {
                    url: "/hrm/employee",
                    data: function(d) {
                        d.employee_id = $("#employee_id").val();
                        d.method = $("#method").val();
                        d.start_date = $("#start_date").val();
                        d.start_time = $("#start_time").val();
                        d.end_date = $("#end_date").val();
                        d.end_time = $("#end_time").val();
                        d.created_by = $("#created_by").val();
                        d.payment_status = $("#payment_status").val();
                    },
                },
                columnDefs: [{
                        targets: "date",
                        type: "date-eu",
                    },
                    {
                        targets: [7],
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [{
                        data: "profile_photo",
                        name: "profile_photo"
                    },
                    {
                        data: "employee_name",
                        name: "employee_name"
                    },
                    {
                        data: "email",
                        name: "email"
                    },
                    {
                        data: "mobile",
                        name: "mobile"
                    },
                    {
                        data: "job_title",
                        name: "job_types.job_title"
                    },
                    {
                        data: "fixed_wage_value",
                        name: "employees.fixed_wage_value"
                    },
                    {
                        data: "annual_leave_balance",
                        name: "annual_leave_balance",
                        searchable: false
                    },
                    {
                        data: "age",
                        name: "age",

                    },
                    {
                        data: "date_of_start_working",
                        name: "date_of_start_working"
                    },
                    {
                        data: "current_status",
                        name: "current_status"
                    },
                    {
                        data: "store",
                        name: "store",
                    },
                    {
                        data: "store_pos",
                        name: "store_pos",
                    },
                    {
                        data: "commission",
                        name: "commission",
                    },
                    {
                        data: "total_paid",
                        name: "total_paid",
                    },
                    {
                        data: "due",
                        name: "due",
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                createdRow: function(row, data, dataIndex) {},
                footerCallback: function(row, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "") * 1 :
                            typeof i === "number" ?
                            i :
                            0;
                    };

                    this.api()
                        .columns(".currencies", {
                            page: "current"
                        }).every(function() {
                            var column = this;
                            let currencies_html = '';
                            $.each(currency_obj, function(key, value) {
                                currencies_html +=
                                    `<h6 class="footer_currency" data-is_default="${value.is_default}"  data-currency_id="${value.currency_id}">${value.symbol}</h6>`
                                $(column.footer()).html(currencies_html);
                            });
                        })


                    this.api()
                        .columns(".sum", {
                            page: "current"
                        })
                        .every(function() {
                            var column = this;
                            var currency_total = [];
                            $.each(currency_obj, function(key, value) {
                                currency_total[value.currency_id] = 0;
                            });
                            column.data().each(function(group, i) {
                                b = $(group).text();
                                currency_id = $(group).data('currency_id');

                                $.each(currency_obj, function(key, value) {
                                    if (currency_id == value.currency_id) {
                                        currency_total[value.currency_id] += intVal(
                                            b);
                                    }
                                });
                            });
                            var footer_html = '';
                            $.each(currency_obj, function(key, value) {
                                footer_html +=
                                    `<h6 class="currency_total currency_total_${value.currency_id}" data-currency_id="${value.currency_id}" data-is_default="${value.is_default}" data-conversion_rate="${value.conversion_rate}" data-base_conversion="${currency_total[value.currency_id] * value.conversion_rate}" data-orig_value="${currency_total[value.currency_id]}">${__currency_trans_from_en(currency_total[value.currency_id], false)}</h6>`
                            });
                            $(column.footer()).html(
                                footer_html
                            );
                        });
                },
            });
            $(document).on('change', '.filter', function() {
                employee_table.ajax.reload();
            });
        })
    </script>
@endsection
