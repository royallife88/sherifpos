@extends('layouts.app')
@section('title', __('lang.customer_point_adjustment'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.customer_point_adjustment')</h4>
                    </div>
                    {!! Form::open(['url' => action('CustomerPointAdjustmentController@update', $customer_point_adjustment->id),
                    'method' => 'put', 'id' =>
                    'sms_form', 'files' => true
                    ]) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, $customer_point_adjustment->store_id, ['class' =>
                                    'form-control
                                    selectpicker', 'id' => 'store_id' ,'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                    {!! Form::select('customer_id', $customers, $customer_point_adjustment->customer_id, ['class' => 'form-control
                                    selectpicker', 'id' => 'customer_id', 'data-live-search' => "true", 'placeholder' =>
                                    __('lang.please_select'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('current_balance', __('lang.current_balance'), []) !!}
                                    {!! Form::text('current_balance', @num_format($customer_point_adjustment->current_balance), ['class' => 'form-control', 'id' =>
                                    'current_balance' ,'placeholder' =>
                                    __('lang.current_balance'), 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('add_new_balance', __('lang.add_new_balance'), []) !!}
                                    {!! Form::text('add_new_balance', @num_format($customer_point_adjustment->add_new_balance), ['class' => 'form-control', 'id' =>
                                    'add_new_balance' ,'placeholder' =>
                                    __('lang.add_new_balance')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('new_balance', __('lang.new_balance'), []) !!}
                                    {!! Form::text('new_balance', @num_format($customer_point_adjustment->new_balance), ['class' => 'form-control', 'id' =>
                                    'new_balance' ,'placeholder' =>
                                    __('lang.new_balance')]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                {!! Form::label('notes', __('lang.notes'), []) !!}
                                {!! Form::textarea('notes', $customer_point_adjustment->notes, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>

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
    });
    $(document).on('change', '#customer_id', function(){
        let customer_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/customer/get-customer-balance/'+customer_id,
            data: {  },
            success: function(result) {
                __write_number($('#current_balance'), result.balance);

                $('#add_new_balance').change()
            },
        });
    })
    $(document).on('change', '#add_new_balance', function(){
        if(!$('#customer_id').val()){
            alert('Please select customer first');
            $(this).val('');
            return false;
        }
        let add_new_balance = __read_number($('#add_new_balance'));
        let current_balance = __read_number($('#current_balance'));

        let new_balance = add_new_balance + current_balance;

        __write_number($('#new_balance'), new_balance);

    });

</script>
@endsection
