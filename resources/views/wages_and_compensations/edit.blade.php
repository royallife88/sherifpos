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
                        {!! Form::open(['url' => action('WagesAndCompensationController@update', $wages_and_compensation->id), 'method' => 'put', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee_id">@lang('lang.employee')</label>
                                    {!! Form::select('employee_id', $employees, $wages_and_compensation->employee_id, ['class' => 'form-control selectpicker calculate_salary', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'id' => 'employee_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type">@lang('lang.payment_type')</label>
                                    {!! Form::select('payment_type', $payment_types, $wages_and_compensation->payment_type, ['class' => 'form-control selectpicker calculate_salary', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'id' => 'payment_type']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="other_payment">@lang('lang.other_payment')</label>
                                    {!! Form::text('other_payment', $wages_and_compensation->other_payment, ['class' => 'form-control', 'placeholder' => __('lang.other_payment'), 'id' => 'other_payment']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 account_period">
                                <div class="form-group">
                                    <label for="account_period">@lang('lang.account_period')</label>
                                    {!! Form::month('account_period', $wages_and_compensation->account_period, ['class' => 'form-control', 'placeholder' => __('lang.account_period'), 'id' => 'account_period']) !!}
                                </div>
                            </div>

                            <div class="col-md-8 account_period_dates @if ($wages_and_compensation->payment_type == 'salary') hide @endif">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acount_period_start_date">@lang('lang.acount_period_start_date')</label>
                                            {!! Form::text('acount_period_start_date', !empty($wages_and_compensation->acount_period_start_date) ? @format_date($wages_and_compensation->acount_period_start_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.acount_period_start_date')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="acount_period_end_date">@lang('lang.acount_period_end_date')</label>
                                            {!! Form::text('acount_period_end_date', !empty($wages_and_compensation->acount_period_end_date) ? @format_date($wages_and_compensation->acount_period_end_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.acount_period_end_date')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="deductibles">@lang('lang.deductibles')</label>
                                    {!! Form::text('deductibles', $wages_and_compensation->deductibles, ['class' => 'form-control', 'placeholder' => __('lang.deductibles'), 'id' => 'deductibles']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reasons_of_deductibles">@lang('lang.reasons_of_deductibles')</label>
                                    {!! Form::text('reasons_of_deductibles', $wages_and_compensation->reasons_of_deductibles, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang.reasons_of_deductibles')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="net_amount">@lang('lang.net_amount')</label>
                                    {!! Form::text('net_amount', $wages_and_compensation->net_amount, ['class' => 'form-control', 'placeholder' => __('lang.net_amount'), 'id' => 'net_amount']) !!}
                                </div>
                            </div>
                            <input type="hidden" name="amount" id="amount" value="{{ $wages_and_compensation->amount }}">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_date">@lang('lang.payment_date')</label>
                                    {!! Form::text('payment_date', !empty($wages_and_compensation->payment_date) ? @format_date($wages_and_compensation->payment_date) : @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'placeholder' => __('lang.payment_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                                    {!! Form::select('source_type', ['user' => __('lang.user'), 'pos' => __('lang.pos'), 'store' => __('lang.store'), 'safe' => __('lang.safe')], $wages_and_compensation->source_type, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                                    {!! Form::select('source_id', $users, $wages_and_compensation->source_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select'), 'id' => 'source_id', 'required']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="upload_files">@lang('lang.upload_files')</label> <br>
                                    {!! Form::file('upload_files', null, ['class' => 'form-control', 'placeholder' => __('lang.upload_files')]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">@lang('lang.notes')</label>
                                    {!! Form::textarea('notes', $wages_and_compensation->notes, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang.notes')]) !!}
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-12">
                                    <input type="submit" class="btn btn-primary" value="@lang('lang.save')" name="submit">
                                </div>
                            </div>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade second_modal" role="dialog" aria-hidden="true"></div>

@endsection

@section('javascript')
    <script>
        $('#payment_type').change(function() {
            if ($(this).val() === 'salary') {
                $('#account_period').attr('required', true);
                $('.account_period_dates').addClass('hide');
                $('.account_period').removeClass('hide');
            } else {
                $('#account_period').attr('required', false);
                $('.account_period').addClass('hide');
                $('.account_period_dates').removeClass('hide');
            }
        })

        $('.calculate_salary').change(function() {
            let employee_id = $('#employee_id').val();
            let payment_type = $('#payment_type').val();

            if (employee_id != null && employee_id != undefined && payment_type != null && payment_type !=
                undefined) {

                if (payment_type === 'salary' || payment_type === 'commission') {
                    $.ajax({
                        method: 'get',
                        url: `/hrm/wages-and-compensations/calculate-salary-and-commission/${employee_id}/${payment_type}`,
                        data: {
                            acount_period_end_date: $('#acount_period_end_date').val(),
                            acount_period_start_date: $('#acount_period_start_date').val()
                        },
                        success: function(result) {
                            $('#amount').val(result.amount);
                            let amount = result.amount
                            if ($('#deductibles').val() != '' && $('#deductibles').val() != undefined) {
                                let deductibles = parseFloat($('#deductibles').val());
                                amount = amount - deductibles;
                            }
                            console.log(amount);
                            $('#net_amount').val(amount);
                        },
                    });
                }
            }
        })

        $('#net_amount').change(function() {
            if ($('#payment_type').val() !== 'salary' && $('#payment_type').val() !== 'commission') {
                $('#amount').val($(this).val());
            }
        })
        $('#deductibles').change(function() {
            if ($('#deductibles').val() != '' && $('#deductibles').val() != undefined) {
                let deductibles = parseFloat($('#deductibles').val());
                let amount = 0;
                if ($('#amount').val() != '' && $('#amount').val() != undefined) {
                    amount = parseFloat($('#amount').val());
                }
                amount = amount - deductibles;
                $('#net_amount').val(amount);
            }
        });
        $(document).ready(function() {
            $('#source_type').change();
        })
        $('#source_type').change(function() {
            if ($(this).val() !== '') {
                $.ajax({
                    method: 'get',
                    url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                    data: {},
                    success: function(result) {
                        $("#source_id").empty().append(result);
                        $("#source_id").val("{{ $wages_and_compensation->source_id }}");
                        $("#source_id").selectpicker("refresh");
                    },
                });
            }
        });

        $('.selectpicker').selectpicker();
    </script>
@endsection
