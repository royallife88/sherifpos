<div class="modal-dialog" role="document" style="max-width: 55%">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.sale' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <h5>@lang('lang.invoice_no'): {{$sale->invoice_no}}</h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.date'): {{@format_date($sale->transaction_date)}}</h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.store'): {{$sale->store->name}}</h5>
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('supplier_name', __('lang.customer_name'), []) !!}:
                        <b>{{$sale->customer->name}}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$sale->customer->email}}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                        <b>{{$sale->customer->mobile_number}}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$sale->customer->address}}</b>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                        <thead class="bg-success"  style="color: white">
                            <tr>
                                <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sell_price' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->transaction_sell_lines as $line)
                            <tr>
                                <td>
                                    {{$line->product->name}}

                                    @if($line->variation->name != "Default")
                                    <b>{{$line->variation->name}}</b>
                                    @endif

                                </td>
                                <td>
                                    {{$line->variation->sub_sku}}
                                </td>
                                <td>
                                    @if(isset($line->quantity)){{$line->quantity}}@else{{1}}@endif
                                </td>
                                <td>
                                    @if(isset($line->sell_price)){{@num_format($line->sell_price)}}@else{{0}}@endif
                                </td>
                                <td>
                                    {{@num_format($line->sub_total)}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align: right"> @lang('lang.total')</th>
                                <td>{{@num_format($sale->grand_total)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <br>
            @include('transaction_payment.partials.payment_table', ['payments' => $sale->transaction_payments])

            <br>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <h4>@lang('lang.sale_note'):</h4>
                        <p>{{$sale->sale_note}}</p>
                    </div>
                    <div class="col-md-12">
                        <h4>@lang('lang.staff_note'):</h4>
                        <p>{{$sale->staff_note}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('lang.total_tax'):</th>
                            <td>{{@num_format($sale->total_tax)}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.discount'):</th>
                            <td>{{@num_format($sale->discount_amount)}}</td>
                        </tr>
                        @if(!empty($sale->rp_redeemed_value))
                        <tr>
                            <th>@lang('lang.redeemed_point_value'):</th>
                            <td>{{@num_format($sale->rp_redeemed_value)}}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>@lang('lang.grand_total'):</th>
                            <td>{{@num_format($sale->final_total)}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.paid_amount'):</th>
                            <td>{{@num_format($sale->transaction_payments->sum('amount'))}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.due'):</th>
                            <td> {{@num_format($sale->final_total - $sale->transaction_payments->sum('amount'))}}</td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
