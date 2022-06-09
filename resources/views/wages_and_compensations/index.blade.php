@extends('layouts.app')
@section('title', __('lang.wages_and_compensations'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.wages_and_compensations')</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <a class="btn btn-primary ml-4" href="{{ action('WagesAndCompensationController@create') }}">
                                <i class="fa fa-plus"></i> @lang('lang.add')</a>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <form action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.date_of_creation') @lang('lang.start_date')</label>
                                            {!! Form::text('doc_start_date', null, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.date_of_creation') @lang('lang.end_date')</label>
                                            {!! Form::text('doc_end_date', null, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.employees')</label>
                                            {!! Form::select('employee_id', $employees, null, ['class' => 'form-control', 'placeholder' => __('lang.all')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.store')</label>
                                            {!! Form::select('store_id', $stores, null, ['class' => 'form-control', 'placeholder' => __('lang.all')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.job')</label>
                                            {!! Form::select('job_type_id', $jobs, null, ['class' => 'form-control', 'placeholder' => __('lang.all')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.payment_type')</label>
                                            {!! Form::select('payment_type', $payment_types, null, ['class' => 'form-control', 'placeholder' => __('lang.all')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.status')</label>
                                            {!! Form::select('status', ['paid' => 'Paid', 'pending' => 'Pending'], null, ['class' => 'form-control', 'placeholder' => __('lang.all')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.payment_start_date')</label>
                                            {!! Form::text('payment_start_date', null, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">@lang('lang.payment_end_date')</label>
                                            {!! Form::text('payment_end_date', null, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <br>
                                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                                        <a href="{{ action('WagesAndCompensationController@index') }}"
                                            class="btn btn-danger mt-2">@lang('lang.clear_filter')</a>
                                    </div>
                                </div>
                            </form>

                        </div>


                        <div class="row">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date_of_creation')</th>
                                            <th>@lang('lang.employee_name')</th>
                                            <th>@lang('lang.photo')</th>
                                            <th>@lang('lang.account_period')</th>
                                            <th>@lang('lang.job_title')</th>
                                            <th>@lang('lang.amount_paid')</th>
                                            <th>@lang('lang.type_of_payment')</th>
                                            <th>@lang('lang.date_of_payment')</th>
                                            <th>@lang('lang.paid_by')</th>
                                            <th>@lang('lang.view_uploads')</th>
                                            <th>@lang('lang.status')</th>
                                            <th class="notexport">@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($wages_and_compensations as $wages_and_compensation)
                                            <tr>
                                                <td>{{ @format_date($wages_and_compensation->date_of_creation) }}</td>
                                                <td>
                                                    {{ $wages_and_compensation->employee_name }}
                                                </td>
                                                @php
                                                    $employee = App\Models\Employee::find($wages_and_compensation->employee_id);
                                                @endphp
                                                <td>
                                                    <img src="@if (!empty($employee->getFirstMediaUrl('employee_photo'))) {{ $employee->getFirstMediaUrl('employee_photo') }}@else{{ asset('uploads/' . session('logo')) }} @endif"
                                                        style="width: 50px; border: 2px solid #fff;" />
                                                </td>
                                                <td>
                                                    @if ($wages_and_compensation->payment_type == 'salary')
                                                        {{ \Carbon\Carbon::parse($wages_and_compensation->account_period)->format('F') }}
                                                    @else
                                                        @if (!empty($wages_and_compensation->acount_period_start_date))
                                                            {{ @format_date($wages_and_compensation->acount_period_start_date) }}
                                                        @endif
                                                        -
                                                        @if (!empty($wages_and_compensation->acount_period_end_date))
                                                            {{ @format_date($wages_and_compensation->acount_period_end_date) }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $wages_and_compensation->job_title }}</td>
                                                <td>
                                                    {{ session()->get('currency.symbol') }}
                                                    {{ @num_format($wages_and_compensation->net_amount) }}
                                                </td>
                                                <td>
                                                    @if (!empty($payment_types[$wages_and_compensation->payment_type]))
                                                        {{ $payment_types[$wages_and_compensation->payment_type] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ @format_date($wages_and_compensation->payment_date) }}
                                                </td>
                                                <td>
                                                    @if (!empty($wages_and_compensation->transaction))
                                                        {{ $wages_and_compensation->transaction->source->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a data-href="{{ action('GeneralController@viewUploadedFiles', ['model_name' => 'WagesAndCompensation', 'model_id' => $wages_and_compensation->id, 'collection_name' => 'wages_and_compensation']) }}"
                                                        data-container=".view_modal"
                                                        class="btn btn-danger btn-modal text-white">@lang('lang.view')</a>
                                                </td>
                                                <td>
                                                    {{ ucfirst($wages_and_compensation->status) }}
                                                </td>
                                                <td>
                                                    @if ($wages_and_compensation->status != 'paid')
                                                        @can('hr_management.wages_and_compensation.create_and_edit')
                                                            <a href="{{ action('WagesAndCompensationController@changeStatusToPaid', $wages_and_compensation->id) }}"
                                                                class="btn btn-danger text-white">@lang('lang.paid')</a>
                                                        @endcan
                                                    @endif
                                                    @can('hr_management.wages_and_compensation.view')
                                                        <a href="{{ action('WagesAndCompensationController@show', $wages_and_compensation->id) }}"
                                                            class="btn btn-danger text-white edit_job"><i
                                                                class="fa fa-eye"></i></a>
                                                    @endcan
                                                    @can('hr_management.wages_and_compensation.create_and_edit')
                                                        <a href="{{ action('WagesAndCompensationController@edit', $wages_and_compensation->id) }}"
                                                            class="btn btn-danger text-white edit_job"><i
                                                                class="fa fa-pencil-square-o"></i></a>
                                                    @endcan
                                                    @can('hr_management.wages_and_compensation.delete')
                                                        <a data-href="{{ action('WagesAndCompensationController@destroy', $wages_and_compensation->id) }}"
                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                            class="btn btn-danger text-white delete_item"><i
                                                                class="fa fa-trash"></i></a>
                                                    @endcan

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
@endsection

@section('javascript')
    <script></script>
@endsection
