@extends('layouts.app')
@section('title', __('lang.expense'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_expense')</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::open(['url' => action('ExpenseController@update', $expense->id), 'method' => 'put', 'enctype' => 'multipart/form-data']) !!}

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_category_id">@lang('lang.expense_category')</label>
                                            {!! Form::select('expense_category_id', $expense_categories, $expense->expense_category_id, ['class' => 'form-control selectpicker', 'required', 'id' => 'expense_category_id', 'placeholder' => __('lang.please_select'), 'data-live-search' => 'true']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_beneficiary_id">@lang('lang.beneficiary')</label>
                                            {!! Form::select('expense_beneficiary_id', $expense_beneficiaries, $expense->expense_beneficiary_id, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required', 'id' => 'expense_beneficiary_id', 'placeholder' => __('lang.please_select')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="store_id">@lang('lang.store')</label>
                                            {!! Form::select('store_id', $stores, $expense->store_id, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required', 'id' => 'store_id', 'placeholder' => __('lang.please_select')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                                            {!! Form::select('source_type', ['user' => __('lang.user'), 'pos' => __('lang.pos'), 'store' => __('lang.store'), 'safe' => __('lang.safe')], $expense->source_type, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                                            {!! Form::select('source_id', $users, $expense->source_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select'), 'id' => 'source_id', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 hide">
                                        <div class="form-group">
                                            {!! Form::label('transaction_date', __('lang.creation_date') . ':', []) !!} <br>
                                            {!! Form::text('transaction_date', !empty($expense->transaction_date) ? @format_date($expense->transaction_date) : @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'placeholder' => __('lang.payment_date')]) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        @include('expense.partial.payment_form', [
                                            'payment' => $expense->transaction_payments->first(),
                                        ])

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="next_payment_date">@lang('lang.next_payment_date')</label>
                                            <input type="date" class="form-control" name="next_payment_date"
                                                id="next_payment_date" value="{{ $expense->next_payment_date }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-4 notify_field @if (empty($expense->next_payment_date)) hide @endif">
                                        <div class="i-checks">
                                            <input id="notify_me" name="notify_me" type="checkbox"
                                                @if ($expense->notify_me) checked @endif value="1"
                                                class="form-control-custom">
                                            <label for="notify_me"><strong>@lang('lang.notify_me')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 notify_field @if (empty($expense->next_payment_date)) hide @endif">
                                        <div class="form-group">
                                            <label for="notify_before_days">@lang('lang.notify_before_days')</label>
                                            <input type="text" class="form-control" name="notify_before_days"
                                                id="notify_before_days" value="{{ $expense->notify_before_days }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="details">@lang('lang.details')</label> <br>
                                            <textarea class="form-control" name="details" id="details" rows="3">{{ $expense->details }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-12">
                                        <input type="submit" class="btn btn-primary" value="@lang('lang.save')"
                                            name="submit">
                                    </div>
                                </div>
                                {!! Form::close() !!}
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
        $(document).ready(function() {
            $('#method').change();
            $('#source_type').change();
        })
        $('#method').change(function() {
            var method = $(this).val();

            if (method === 'card') {
                $('.card_field').removeClass('hide');
                $('.not_cash_fields').addClass('hide');
                $('.not_cash').attr('required', false);
            } else if (method === 'cash') {
                $('.not_cash_fields').addClass('hide');
                $('.card_field').addClass('hide');
                $('.not_cash').attr('required', false);
            } else {
                $('.not_cash_fields').removeClass('hide');
                $('.card_field').addClass('hide');
                $('.not_cash').attr('required', true);
            }
        })
        $(document).on('change', '#next_payment_date', function() {
            if ($(this).val()) {
                $('.notify_field').removeClass('hide');
            } else {
                $('.notify_field').addClass('hide');
            }
        });
        $(document).on('change', '#expense_category_id', function() {
            expense_category_id = parseInt($(this).val());

            if (!isNaN(expense_category_id)) {
                $.ajax({
                    method: 'get',
                    url: '/expense-categories/get-beneficiary-dropdown/' + expense_category_id,
                    data: {},
                    contentType: 'html',
                    success: function(result) {
                        $('#expense_beneficiary_id').empty().append(result);
                        $('#expense_beneficiary_id').selectpicker('refresh')
                    },
                });
            }
        });

        $('#source_type').change(function() {
            if ($(this).val() !== '') {
                $.ajax({
                    method: 'get',
                    url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                    data: {},
                    success: function(result) {
                        $("#source_id").empty().append(result);
                        $('#source_id').val('{{ $expense->source_id }}');
                        $("#source_id").selectpicker("refresh");
                    },
                });
            }
        });

        $(document).on('change', '.payment_date', function() {
            let payment_date = $(this).val();

            $.ajax({
                method: 'GET',
                url: '/cash-register/get-available-cash-register/{{ $expense->created_by }}',
                data: {
                    payment_date
                },
                success: function(result) {
                    if (!result.success) {
                        swal("Error", result.msg, 'error');
                        $('.cash_register_id').val('')
                    } else {
                        $('.cash_register_id').val(result.cash_register.id)
                    }
                },
            });
        })
    </script>
@endsection
