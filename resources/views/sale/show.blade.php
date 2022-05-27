<div class="modal-dialog no-print" role="document" style="max-width: 55%">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.sale')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <h5>@lang('lang.invoice_no'): {{ $sale->invoice_no }} @if (!empty($sale->return_parent))
                                <a data-href="{{ action('SellReturnController@show', $sale->id) }}"
                                    data-container=".view_modal" class="btn btn-modal" style="color: #007bff;">R</a>
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.date'): {{ @format_datetime($sale->transaction_date) }}</h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.store'): {{ $sale->store->name ?? '' }}</h5>
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('supplier_name', __('lang.customer_name'), []) !!}:
                        <b>{{ $sale->customer->name ?? '' }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('email', __('lang.email'), []) !!}: <b>{{ $sale->customer->email ?? '' }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                        <b>{{ $sale->customer->mobile_number ?? '' }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('address', __('lang.address'), []) !!}: <b>{{ $sale->customer->address ?? '' }}</b>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped table-condensed" id="product_sale_table">
                        <thead class="bg-success" style="color: white">
                            <tr>
                                <th style="width: 25%" class="col-sm-8">@lang('lang.image')</th>
                                <th style="width: 25%" class="col-sm-8">@lang('lang.products')</th>
                                <th style="width: 25%" class="col-sm-4">@lang('lang.sku')</th>
                                <th style="width: 25%" class="col-sm-4">@lang('lang.batch_number')</th>
                                <th style="width: 25%" class="col-sm-4">@lang('lang.quantity')</th>
                                <th style="width: 12%" class="col-sm-4">@lang('lang.sell_price')</th>
                                <th style="width: 12%" class="col-sm-4">@lang('lang.discount')</th>
                                <th style="width: 12%" class="col-sm-4">@lang('lang.sub_total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->transaction_sell_lines as $line)
                                <tr>
                                    <td><img src="@if (!empty($line->product) && !empty($line->product->getFirstMediaUrl('product'))) {{ $line->product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                            alt="photo" width="50" height="50"></td>
                                    <td>
                                        {{ $line->product->name ?? '' }}
                                        @if (!empty($line->variation))
                                            @if ($line->variation->name != 'Default')
                                                <b>{{ $line->variation->name }}</b>
                                            @endif
                                        @endif
                                        @if (empty($line->variation) && empty($line->product))
                                            <span class="text-red">@lang('lang.deleted')</span>
                                        @endif

                                    </td>
                                    <td>
                                        @if (!empty($line->variation))
                                            @if ($line->variation->name != 'Default')
                                                {{ $line->variation->sub_sku }}
                                            @else
                                                {{ $line->product->sku ?? '' }}
                                            @endif
                                        @else
                                            {{ $line->product->sku ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $line->product->batch_number ?? '' }}
                                    </td>
                                    <td>
                                        @if (isset($line->quantity))
                                            {{ @num_format($line->quantity) }}@else{{ 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($line->sell_price))
                                            {{ @num_format($line->sell_price) }}@else{{ 0 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($line->product_discount_type != 'surplus')
                                            @if (isset($line->product_discount_amount))
                                                {{ @num_format($line->product_discount_amount) }}@else{{ 0 }}
                                            @endif
                                        @else
                                            {{ @num_format(0) }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ @num_format($line->sub_total) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th style="text-align: right"> @lang('lang.total')</th>
                                <td>{{ @num_format($sale->transaction_sell_lines->where('product_discount_type', '!=', 'surplus')->sum('product_discount_amount')) }}
                                </td>
                                <td>{{ @num_format($sale->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <br>
            @if (!empty($sale->transaction_customer_size))
                @php
                    $customer_size = $sale->transaction_customer_size;
                @endphp
                <div class="row text-center">
                    <div class="col-md-12">
                        <h4>@lang('lang.customer_size_details')</h4>
                    </div>
                </div>
                <div class="col-md-12">
                    @if (!empty($sale->customer_size))
                        <label for=""><b>@lang('lang.customer_size'):
                                {{ $sale->customer_size->name }} </b></label><br>
                    @endif
                    @if (!empty($sale->fabric_name))
                        <label for=""><b>@lang('lang.fabric_name'): {{ $sale->fabric_name }}
                            </b></label><br>
                    @endif
                    @if (!empty($sale->fabric_squatch))
                        <label for=""><b>@lang('lang.fabric_squatch'):
                                {{ $sale->fabric_squatch }} </b></label><br>
                    @endif
                    @if (!empty($sale->prova_datetime))
                        <label for=""><b>@lang('lang.prova'):
                                {{ @format_datetime($sale->prova_datetime) }} </b></label><br>
                    @endif
                    @if (!empty($sale->delivery_datetime))
                        <label for=""><b>@lang('lang.delivery'):
                                {{ @format_datetime($sale->delivery_datetime) }} </b></label><br>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th>@lang('lang.length_of_the_dress')</th>
                                    <th>@lang('lang.cm')</th>
                                    <th>@lang('lang.inches')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($getAttributeListArray as $key => $value)
                                    <tr>
                                        <td>
                                            <label for="">{{ $value }}</label>
                                        </td>
                                        <td>
                                            {{ @num_format($customer_size->$key['cm']) }}
                                        </td>
                                        <td>
                                            {{ @num_format($customer_size->$key['inches']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @include('customer_size.partial.body_graph', ['customer_size' => $customer_size])
                    </div>
                </div>
            @endif
            @include('transaction_payment.partials.payment_table', [
                'payments' => $sale->transaction_payments,
            ])

            <br>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <h4>@lang('lang.sale_note'):</h4>
                        <p>{{ $sale->sale_note }}</p>
                    </div>
                    <div class="col-md-12">
                        <h4>@lang('lang.staff_note'):</h4>
                        <p>{{ $sale->staff_note }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('lang.total_tax'):</th>
                            <td>{{ @num_format($sale->total_tax + $sale->total_item_tax) }}</td>
                        </tr>
                        @if ($sale->transaction_sell_lines->where('product_discount_type', '!=', 'surplus')->sum('product_discount_amount') > 0)
                            <tr>
                                <th>@lang('lang.discount')</th>
                                <td>
                                    {{ @num_format($sale->transaction_sell_lines->where('product_discount_type', '!=', 'surplus')->sum('product_discount_amount')) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>@lang('lang.order_discount'):</th>
                            <td>{{ @num_format($sale->discount_amount) }}</td>
                        </tr>
                        @if (!empty($sale->rp_earned))
                            <tr>
                                <th>@lang('lang.point_earned'):</th>
                                <td>{{ @num_format($sale->rp_earned) }}</td>
                            </tr>
                        @endif
                        @if (!empty($sale->rp_redeemed_value))
                            <tr>
                                <th>@lang('lang.redeemed_point_value'):</th>
                                <td>{{ @num_format($sale->rp_redeemed_value) }}</td>
                            </tr>
                        @endif
                        @if ($sale->total_coupon_discount > 0)
                            <tr>
                                <th>@lang('lang.coupon_discount')</th>
                                <td>{{ @num_format($sale->total_coupon_discount) }}</td>
                            </tr>
                        @endif
                        @if ($sale->delivery_cost > 0)
                            <tr>
                                <th>@lang('lang.delivery_cost')</th>
                                <td>{{ @num_format($sale->delivery_cost) }}</td>
                            </tr>
                        @endif
                        @if ($sale->service_fee_value > 0)
                            <tr>
                                <th>@lang('lang.service')</th>
                                <td>{{ @num_format($sale->service_fee_value) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>@lang('lang.grand_total'):</th>
                            <td>{{ @num_format($sale->final_total) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.paid_amount'):</th>
                            <td>{{ @num_format($sale->transaction_payments->sum('amount')) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.due'):</th>
                            <td> {{ @num_format($sale->final_total - $sale->transaction_payments->sum('amount')) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12">
                    <b>@lang('lang.terms_and_conditions'):</b>
                    @if (!empty($sale->terms_and_conditions))
                        {!! $sale->terms_and_conditions->description !!}
                    @endif
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <a data-href="{{ action('SellController@print', $sale->id) }}"
                class="btn btn-primary text-white print-invoice"><i class="dripicons-print"></i> @lang('lang.print')</a>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
