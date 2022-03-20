@extends('layouts.app')
@section('title', __('lang.wages_and_compensations'))
<style>
    label {
        font-weight: bold !important;
    }

</style>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee_id">@lang('lang.employee'):</label>
                                    {{ $wages_and_compensation->employee->employee_name }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type">@lang('lang.payment_type'):</label>
                                    @if (!empty($payment_types[$wages_and_compensation->payment_type]))
                                        {{ $payment_types[$wages_and_compensation->payment_type] }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="other_payment">@lang('lang.other_payment'):</label>
                                    {{ @num_format($wages_and_compensation->other_payment) }}
                                </div>
                            </div>

                            <div class="col-md-4 account_period">
                                <div class="form-group">
                                    <label for="account_period">@lang('lang.account_period'):</label>
                                    {{ $wages_and_compensation->account_period }}
                                </div>
                            </div>

                            <div class="col-md-8 account_period_dates @if ($wages_and_compensation->payment_type == 'salary') hide @endif">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="acount_period_start_date">@lang('lang.acount_period_start_date'):</label>
                                            {{ $wages_and_compensation->acount_period_start_date }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="acount_period_end_date">@lang('lang.acount_period_end_date'):</label>
                                            {{ $wages_and_compensation->acount_period_end_date }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="deductibles">@lang('lang.deductibles'):</label>
                                    {{ @num_format($wages_and_compensation->deductibles) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reasons_of_deductibles">@lang('lang.reasons_of_deductibles'):</label>
                                    {{ $wages_and_compensation->reasons_of_deductibles }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="net_amount">@lang('lang.net_amount'):</label>
                                    {{ @num_format($wages_and_compensation->net_amount) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_date">@lang('lang.payment_date'):</label>
                                    @if (!empty($wages_and_compensation->payment_date))
                                        {{ @format_date($wages_and_compensation->payment_date) }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                                    @if (!empty($wages_and_compensation->transaction))
                                        {{ $wages_and_compensation->transaction->source->name }}
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">@lang('lang.notes'):</label>
                                    {{ $wages_and_compensation->notes }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="files">@lang('lang.files'):</label>
                                    <a data-href="{{ action('GeneralController@viewUploadedFiles', ['model_name' => 'WagesAndCompensation','model_id' => $wages_and_compensation->id,'collection_name' => 'wages_and_compensation']) }}"
                                        data-container=".view_modal"
                                        class="btn btn-default btn-modal">@lang('lang.view')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade second_modal" role="dialog" aria-hidden="true"></div>

@endsection

@section('javascript')

@endsection
