@extends('layouts.app')
@section('title', __('lang.expense'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.add_new_expense')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(['url' => action('ExpenseController@store'), 'method' => 'post', 'enctype' =>
                            'multipart/form-data']) !!}

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expense_category_id">@lang('lang.expense_category')</label>
                                        {!! Form::select('expense_category_id', $expense_categories, null, ['class' =>
                                        'form-control selectpicker', 'required', 'id' => 'expense_category_id', 'placeholder' =>
                                        __('lang.please_select'), 'data-live-search' => 'true']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expense_beneficiary_id">@lang('lang.beneficiary')</label>
                                        {!! Form::select('expense_beneficiary_id', [], null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true',
                                        'required', 'id' => 'expense_beneficiary_id', 'placeholder' =>
                                        __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @include('transaction_payment.partials.payment_form')

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="next_payment_date">@lang('lang.next_payment_date')</label>
                                        <input type="date" class="form-control" name="next_payment_date" id="next_payment_date">
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4 notify_field hide">
                                    <div class="i-checks">
                                        <input id="notify_me" name="notify_me" type="checkbox" value="1"
                                            class="form-control-custom">
                                        <label for="notify_me"><strong>@lang('lang.notify_me')</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-4 notify_field hide">
                                    <div class="form-group">
                                        <label for="notify_before_days">@lang('lang.notify_before_days')</label>
                                        <input type="text" class="form-control" name="notify_before_days" id="notify_before_days">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="details">@lang('lang.details')</label> <br>
                                        <textarea class="form-control" name="details" id="details" rows="3"></textarea>
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
    $('#method').change(function(){
        var method = $(this).val();

        if(method === 'cash'){
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
        }else{
            $('.not_cash_fields').removeClass('hide');
            $('.not_cash').attr('required', true);
        }
    })

    $(document).on('change', '#next_payment_date', function(){
        if($(this).val()){
            $('.notify_field').removeClass('hide');
        }else{
            $('.notify_field').addClass('hide');
        }
    });
    $(document).on('change', '#expense_category_id', function(){
        expense_category_id = parseInt($(this).val());

        if(!isNaN(expense_category_id)){
            $.ajax({
                method: 'get',
                url: '/expense-categories/get-beneficiary-dropdown/'+expense_category_id,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    $('#expense_beneficiary_id').empty().append(result);
                    $('#expense_beneficiary_id').selectpicker('refresh')
                },
            });
        }
    })
</script>
@endsection