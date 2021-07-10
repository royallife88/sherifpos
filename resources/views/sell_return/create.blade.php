@extends('layouts.app')
@section('title', __('lang.sell_return'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.sell_return')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('SellReturnController@store'), 'method' => 'post', 'files' =>
                    true, 'class' => 'pos-form', 'id' => 'sell_return_form']) !!}
                    <input type="hidden" name="store_id" id="store_id" value="{{$sale->store_id}}">
                    <input type="hidden" name="default_customer_id" id="default_customer_id"
                        value="@if(!empty($walk_in_customer)){{$walk_in_customer->id}}@endif">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                @lang('lang.invoice_no'): {{$sale->invoice_no}}
                            </div>
                            <div class="col-md-4">
                                @lang('lang.customer'): {{$sale->customer->name}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12" style="margin-top: 20px ">
                                <div class="table-responsive">
                                    <table id="product_table" style="width: 100% " class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">{{__('lang.product')}}</th>
                                                <th style="width: 20%">{{__('lang.quantity')}}</th>
                                                <th style="width: 20%">{{__('lang.returned_quantity')}}</th>
                                                <th style="width: 20%">{{__('lang.price')}}</th>
                                                <th style="width: 10%">{{__('lang.sub_total')}}</th>
                                                <th style="width: 20%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('sell_return.partials.product_row', ['products' =>
                                            $sale->transaction_sell_lines])
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <th style="text-align: right">@lang('lang.total')</th>
                                                <th><span
                                                        class="grand_total_span">{{@num_format(0)}}</span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row" style="display: none;">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="hidden" id="transaction_id" name="transaction_id"
                                            value="{{$sale->id}}" />
                                        <input type="hidden" id="final_total" name="final_total"
                                            value="{{0}}" />
                                        <input type="hidden" id="grand_total" name="grand_total"
                                            value="{{0}}" />
                                        <input type="hidden" id="store_pos_id" name="store_pos_id"
                                            value="{{$sale->store_pos_id}}" />
                                        <input type="hidden" id="customer_id" name="customer_id"
                                            value="{{$sale->customer_id}}" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @if(!empty($sell_return))
                            @if($sell_return->transaction_payments->count() > 0)
                            @include('transaction_payment.partials.payment_form', ['payment' => $sell_return->transaction_payments->first()])
                            @endif
                            @else
                            @include('transaction_payment.partials.payment_form')
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="sbumit" class="btn btn-primary save-btn">@lang('lang.save')</button>
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
<script src="{{asset('js/sell_return.js')}}"></script>
<script>
    $(document).ready(function(){
        calculate_sub_totals()
    })
</script>
@endsection
