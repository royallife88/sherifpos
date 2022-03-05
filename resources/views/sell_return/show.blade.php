<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.return_sale' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        @lang('lang.invoice_no'):<b> {{$sale->invoice_no}}</b>
                    </div>
                    <div class="col-md-12">
                        @lang('lang.date'): <b>{{@format_date($sale->transaction_date)}}</b>
                    </div>
                    <div class="col-md-12">
                        @lang('lang.store'): <b>{{$sale->store->name ?? ''}}</b>
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('return_invoice', __('lang.return_invoice'), []) !!}:
                        <b>{{$sale->return_parent->invoice_no}}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('return_invoice_date', __('lang.return_invoice_date'), []) !!}:
                        <b>{{@format_date($sale->return_parent->transaction_date)}}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('supplier_name', __('lang.customer_name'), []) !!}:
                        <b>{{$sale->customer->name ?? ''}}</b>
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
                        <thead class="bg-success">
                            <tr style="color: white">
                                <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sell_price' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total = 0;
                            @endphp
                            @foreach ($sale->transaction_sell_lines as $line)
                            @if($line->quantity_returned == 0)
                            @continue
                            @endif

                            <tr>
                                <td>
                                    {{$line->product->name}}
                                    @if(!empty($line->variation))
                                    @if($line->variation->name != "Default")
                                    <b>{{$line->variation->name}}</b>
                                    @endif
                                    @endif

                                </td>
                                <td>
                                    {{$line->variation->sub_sku}}
                                </td>
                                <td>
                                    @if(isset($line->quantity_returned)){{@num_format($line->quantity_returned)}}@else{{0}}@endif
                                </td>
                                <td>
                                    @if(isset($line->sell_price)){{@num_format($line->sell_price)}}@else{{0}}@endif
                                </td>
                                <td>
                                    {{@num_format($line->sell_price * $line->quantity_returned)}}
                                </td>
                            </tr>
                            @php
                            $total += $line->sell_price * $line->quantity_returned;
                            @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th style="text-align: right"> @lang('lang.total')</th>
                                <td>{{@num_format($total)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <br>
            @include('transaction_payment.partials.payment_table', ['payments' =>
            $sale->return_parent->transaction_payments])

            <br>
            <br>
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('lang.grand_total'):</th>
                            <td>{{@num_format($sale->return_parent->final_total)}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.paid_amount'):</th>
                            <td>{{@num_format($sale->return_parent->transaction_payments->sum('amount'))}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.due'):</th>
                            <td> {{@num_format($sale->return_parent->final_total - $sale->return_parent->transaction_payments->sum('amount'))}}
                            </td>
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
