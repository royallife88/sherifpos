@extends('layouts.app')
@section('title', __('lang.cash_in_adjustment'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.cash_in_adjustment')</h4>
                    </div>
                    {!! Form::open(['url' => action('CashInAdjustmentController@update', $cash_in_adjustment->id),
                    'method' => 'put', 'id' =>
                    'sms_form', 'files' => true
                    ]) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, $cash_in_adjustment->store_id, ['class' =>
                                    'form-control
                                    selectpicker', 'id' => 'store_id' ,'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('user_id', __('lang.cashier'), []) !!}
                                    {!! Form::select('user_id', $users, $cash_in_adjustment->user_id, ['class' =>
                                    'form-control
                                    selectpicker', 'id' => 'user_id' ,'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('current_cash', __('lang.current_cash'), []) !!}
                                    {!! Form::text('current_cash', @num_format($cash_in_adjustment->current_cash),
                                    ['class' => 'form-control', 'id' => 'current_cash' ,'placeholder' =>
                                    __('lang.current_cash'), 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('amount', __('lang.amount'), []) !!}
                                    {!! Form::text('amount', @num_format($cash_in_adjustment->amount), ['class' =>
                                    'form-control', 'id' => 'amount' ,'placeholder' =>
                                    __('lang.amount')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discrepancy', __('lang.discrepancy'), []) !!}
                                    {!! Form::text('discrepancy', @num_format($cash_in_adjustment->discrepancy),
                                    ['class' => 'form-control', 'id' => 'discrepancy' ,'placeholder' =>
                                    __('lang.discrepancy')]) !!}
                                </div>
                            </div>
                            <input type="hidden" name="cash_register_id" id="cash_register_id"
                                value="{{$cash_in_adjustment->cash_register_id}}">

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button type="submit" id="print" style="margin: 10px"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.update' )</button>

                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $('.selectpicker').selectpicker()
    $(document).on('change', '#amount', function(){
        let amount = __read_number($('#amount'));
        let current_cash = __read_number($('#current_cash'));

        let discrepancy = amount - current_cash;

        __write_number($('#discrepancy'), discrepancy);

    });
    $(document).on('change', '#user_id', function(){
        let user_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/cash-in-adjustment/get-cash-details/'+user_id,
            data: {  },
            success: function(result) {
                if(result.store_id){
                    $('#store_id').val(result.store_id).selectpicker('refresh');
                }
                if(result.current_cash){
                    __write_number($('#current_cash'), result.current_cash);
                }
                if(result.cash_register_id){
                   $('#cash_register_id').val( result.cash_register_id);
                }
            },
        });
    })
</script>
@endsection
