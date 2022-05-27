<div class="modal-dialog" role="document" style="max-width: 65%">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.quotation')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <h5>@lang('lang.quotation_no'): {{ $sale->invoice_no }}</h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.date'): {{ @format_date($sale->transaction_date) }}</h5>
                    </div>
                    <div class="col-md-12">
                        <h5>@lang('lang.store'): {{ $sale->store->name ?? '' }}</h5> {{ $sale->store->address ?? '' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('customer_name', __('lang.customer_name'), []) !!}:
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
                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                        <thead class="bg-success" style="color: white">
                            <tr>
                                <th>@lang('lang.products')</th>
                                <th>@lang('lang.sku')</th>
                                <th>@lang('lang.class')</th>
                                <th>@lang('lang.category')</th>
                                <th>@lang('lang.sub_category')</th>
                                <th>@lang('lang.color')</th>
                                <th>@lang('lang.size')</th>
                                <th>@lang('lang.grade')</th>
                                <th>@lang('lang.unit')</th>
                                <th>@lang('lang.quantity')</th>
                                <th>@lang('lang.sell_price')</th>
                                <th>@lang('lang.sub_total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->transaction_sell_lines as $line)
                                <tr>
                                    <td>
                                        {{ $line->product->name }}
                                        @if (!empty($line->variation))
                                            @if ($line->variation->name != 'Default')
                                                <b>{{ $line->variation->name }}</b>
                                            @endif
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
                                        @if (!empty($line->product->product_class))
                                            {{ $line->product->product_class->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->product->category))
                                            {{ $line->product->category->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->product->sub_category))
                                            {{ $line->product->sub_category->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->variation->color))
                                            {{ $line->variation->color->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->variation->size))
                                            {{ $line->variation->size->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->variation->grade))
                                            {{ $line->variation->grade->name }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (!empty($line->variation->unit))
                                            {{ $line->variation->unit->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($line->quantity))
                                            {{ $line->quantity }}@else{{ 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($line->sell_price))
                                            {{ @num_format($line->sell_price) }}@else{{ 0 }}
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th style="text-align: right"> @lang('lang.total')</th>
                                <td>{{ @num_format($sale->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

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
                            <td>{{ @num_format($sale->total_tax) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.discount'):</th>
                            <td>{{ @num_format($sale->discount_amount) }}</td>
                        </tr>
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
                    @lang('lang.terms_and_conditions'):
                    @if (!empty($sale->terms_and_conditions))
                        {{ $sale->terms_and_conditions->description }}
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
