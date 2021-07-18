@extends('layouts.app')
@section('title', __('lang.add_sale'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.add_sale')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'files' =>
                    true, 'class' => 'pos-form', 'id' => 'add_sale_form']) !!}
                    <input type="hidden" name="store_id" id="store_id" value="{{$store_pos->store_id}}">
                    <input type="hidden" name="default_customer_id" id="default_customer_id"
                        value="@if(!empty($walk_in_customer)){{$walk_in_customer->id}}@endif">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('customer_id', $customers,
                                        !empty($walk_in_customer) ? $walk_in_customer->id : null, ['class' =>
                                        'selectpicker form-control', 'data-live-search'=>"true",
                                        'style' =>'width: 80%' , 'id' => 'customer_id']) !!}
                                        <span class="input-group-btn">
                                            @can('customer_module.customer.create_and_edit')
                                            <button class="btn-modal btn btn-default bg-white btn-flat"
                                                data-href="{{action('CustomerController@create')}}?quick_add=1"
                                                data-container=".view_modal"><i
                                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-8 offset-md-2" style="margin-top: 10px;">
                                    <div class="search-box input-group">
                                        <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                                class="fa fa-search"></i></button>
                                        <input type="text" name="search_product" id="search_product"
                                            placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                            class="form-control ui-autocomplete-input" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 20px ">
                                <div class="table-responsive transaction-list">
                                    <table id="product_table" style="width: 100% "
                                        class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">{{__('lang.product')}}</th>
                                                <th style="width: 20%">{{__('lang.quantity')}}</th>
                                                <th style="width: 20%">{{__('lang.price')}}</th>
                                                <th style="width: 10%">{{__('lang.sub_total')}}</th>
                                                <th style="width: 20%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <th style="text-align: right">@lang('lang.total')</th>
                                                <th><span class="grand_total_span"></span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row" style="display: none;">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="hidden" id="final_total" name="final_total" />
                                        <input type="hidden" id="grand_total" name="grand_total" />
                                        <input type="hidden" id="gift_card_id" name="gift_card_id" />
                                        <input type="hidden" id="coupon_id" name="coupon_id">
                                        <input type="hidden" id="total_tax" name="total_tax" value="0.00">
                                        <input type="hidden" id="is_direct_sale" name="is_direct_sale" value="1">
                                        <input type="hidden" name="discount_amount" id="discount_amount">
                                        <input type="hidden" id="store_pos_id" name="store_pos_id"
                                            value="{{$store_pos->id}}" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tax_id">@lang('lang.tax')</label>
                                <select class="form-control" name="tax_id" id="tax_id">
                                    <option value="" selected>No Tax</option>
                                    @foreach ($taxes as $tax)
                                    <option data-rate="{{$tax->rate}}" value="{{$tax->id}}">{{$tax->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('discount_type', __( 'lang.discount_type' ) . ':*') !!}
                                {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'],
                                'fixed', ['class' =>
                                'form-control', 'data-live-search' => 'true']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('discount_value', __( 'lang.discount_value' ) . ':*') !!}
                                {!! Form::text('discount_value', null, ['class' => 'form-control', 'placeholder' => __(
                                'lang.discount_value' ),
                                'required' ]);
                                !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status', __( 'lang.status' ) . ':*') !!}
                                {!! Form::select('status', ['final' => 'Completed', 'pending' => 'Pending'],
                                'final', ['class' =>
                                'form-control', 'data-live-search' => 'true']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('payment_status', __('lang.payment_status'). ':*', [])
                                !!}
                                {!! Form::select('payment_status', $payment_status_array,
                                null, ['class' => 'selectpicker form-control',
                                'data-live-search'=>"true", 'required',
                                'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                            </div>
                        </div>
                        <div class="col-md-3 mt-1 payment_fields hide">
                            <label>@lang('lang.payment_method')</label>
                            {!! Form::select('method', $payment_types, null, ['class' => 'form-control', 'id' =>
                            'method', 'required', 'data-live-search' => 'true']) !!}
                        </div>
                        <div class="col-md-3 mt-1 payment_fields hide">
                            <label>@lang('lang.received_amount') *</label>
                            <input type="text" name="amount" class="form-control numkey" required id="amount"
                                step="any">
                        </div>
                        <div class="col-md-3 mt-1 payment_fields hide">
                            <label>@lang('lang.paying_amount') *</label>
                            <input type="text" name="paying_amount" class="form-control numkey" id="paying_amount"
                                step="any">
                        </div>
                        <div class="col-md-3 mt-1 payment_fields hide">
                            <label>@lang('lang.change') : </label>
                            <p id="change" class="ml-2">0.00</p>
                        </div>

                        <div class="col-md-12 hide">
                            <div class="i-checks">
                                <input id="print_the_transaction" name="print_the_transaction" type="checkbox" checked value="1"
                                    class="form-control-custom">
                                <label for="print_the_transaction"><strong>@lang('lang.print_the_transaction')</strong></label>
                            </div>
                        </div>
                        <div class="form-group col-md-12 mt-3 card_field payment_fields hide">
                           <div class="row">
                            <div class="col-md-4">
                                <label>@lang('lang.card_number') *</label>
                                <input type="text" name="card_number" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>@lang('lang.card_security')</label>
                                <input type="text" name="card_security" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>@lang('lang.month')</label>
                                <input type="text" name="card_month" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>@lang('lang.year')</label>
                                <input type="text" name="card_year" class="form-control">
                            </div>
                           </div>
                        </div>

                        <div class="form-group col-md-12 cheque_field payment_fields hide">
                            <label>@lang('lang.cheque_number') *</label>
                            <input type="text" name="cheque_number" class="form-control">
                        </div>
                        <div class="form-group col-md-12 gift_card_field hide">
                            <div class="col-md-12">
                                <label>@lang('lang.gift_card_number') *</label>
                                <input type="text" name="gift_card_number" id="gift_card_number"
                                    class="form-control" placeholder="@lang('lang.enter_gift_card_number')">
                                <span class="gift_card_error" style="color: red;"></span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label><b>@lang('lang.current_balance'):</b> </label><br>
                                    <span class="gift_card_current_balance"></span>
                                    <input type="hidden" name="gift_card_current_balance"
                                        id="gift_card_current_balance">
                                </div>
                                <div class="col-md-4">
                                    <label>@lang('lang.enter_amount_to_be_used') </label>
                                    <input type="text" name="amount_to_be_used" id="amount_to_be_used"
                                        class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label>@lang('lang.remaining_balance') </label>
                                    <input type="text" name="remaining_balance" id="remaining_balance"
                                        class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label><b>@lang('lang.final_total'):</b> </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="gift_card_final_total" id="gift_card_final_total"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>@lang('lang.payment_note')</label>
                            <textarea id="payment_note" rows="2" class="form-control"
                                name="payment_note"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>@lang('lang.sale_note')</label>
                            <textarea rows="3" class="form-control" name="sale_note"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>@lang('lang.staff_note')</label>
                            <textarea rows="3" class="form-control" name="staff_note"></textarea>
                        </div>
                        <div class="col-md-4">
                            {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                            <div class="input-group my-group">
                                {!! Form::select('terms_and_condition_id', $tac,
                                null, ['class' =>
                                'selectpicker form-control', 'data-live-search'=>"true",
                                'style' =>'width: 80%' , 'id' => 'terms_and_condition_id', 'required']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script src="{{asset('js/pos.js')}}"></script>
<script>
$(document).on("change", "#method", function (e) {
    let method = $(this).val();


    if (method === "cheque") {
        $(".cheque_field").removeClass("hide");
    } else {
        $(".cheque_field").addClass("hide");
    }
    if (method === "card") {
        $(".card_field").removeClass("hide");
    } else {
        $(".card_field").addClass("hide");
    }
    if (method === "gift_card") {
        $(".gift_card_field").removeClass("hide");
    } else {
        $(".gift_card_field").addClass("hide");
    }
    if (method === "cash") {
        $(".qc").removeClass("hide");
    } else {
        $(".qc").addClass("hide");
    }
});

 //payment related script

 $('#payment_status').change(function(){
        var payment_status = $(this).val();

        if(payment_status === 'paid' || payment_status === 'partial'){
            $('.not_cash_fields').addClass('hide');
            $('#method').change();
            $('.payment_fields').removeClass('hide');
        }else{
            $('.payment_fields').addClass('hide');
        }
        if(payment_status === 'pending' || payment_status === 'partial'){
            $('.due_fields').removeClass('hide');
        }else{
            $('.due_fields').addClass('hide');
        }
        if(payment_status === 'pending'){
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
        }
        if(payment_status === 'paid'){
            $('.due_fields').addClass('hide');
        }

        $('#method').change();
    })
</script>
@endsection
