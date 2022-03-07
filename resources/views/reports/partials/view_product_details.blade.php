<div class="modal-dialog" role="document" style="max-width: 65%;">
    <div class="modal-content">


        <div class="modal-header">

            <h4 class="modal-title">{{ $product->name }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-weight: bold;" for="">@lang('lang.sku'): </label>
                            {{ $product->sku }} <br>
                            <label style="font-weight: bold;" for="">@lang('lang.class'): </label>
                            @if (!empty($product->product_class))
                                {{ $product->product_class->name }}
                            @endif <br>
                            <label style="font-weight: bold;" for="">@lang('lang.category'): </label>
                            @if (!empty($product->category))
                                {{ $product->category->name }}
                            @endif <br>
                            <label style="font-weight: bold;" for="">@lang('lang.sub_category'): </label>
                            @if (!empty($product->sub_category))
                                {{ $product->sub_category->name }}
                            @endif
                            <br>
                            <label style="font-weight: bold;" for="">@lang('lang.brand'): </label>
                            @if (!empty($product->brand))
                                {{ $product->brand->name }}
                            @endif
                            <br>
                            <label style="font-weight: bold;" for="">@lang('lang.batch_number'): </label>
                            {{ $product->batch_number }}<br>
                            <label style="font-weight: bold;" for="">@lang('lang.selling_price'): </label>
                            {{ @num_format($product->sell_price) }}<br>
                            <label style="font-weight: bold;" for="">@lang('lang.automatic_consumption'): </label>
                            @if (!empty($product->automatic_consumption))
                                {{ __('lang.yes') }}@else{{ __('lang.no') }}
                            @endif
                            <br>
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight: bold;" for="">@lang('lang.tax'): </label>
                            @if (!empty($product->tax->name))
                                {{ $product->tax->name }}
                            @endif <br>
                            <label style="font-weight: bold;" for="">@lang('lang.unit'): </label>
                            {{ implode(', ', $product->units->pluck('name')->toArray()) }}<br>
                            <label style="font-weight: bold;" for="">@lang('lang.color'): </label>
                            {{ implode(', ', $product->colors->pluck('name')->toArray()) }}<br>
                            <label style="font-weight: bold;" for="">@lang('lang.size'): </label>
                            {{ implode(', ', $product->sizes->pluck('name')->toArray()) }}<br>
                            <label style="font-weight: bold;" for="">@lang('lang.grade'): </label>
                            {{ implode(', ', $product->grades->pluck('name')->toArray()) }}<br>
                            @can('product_module.purchase_price.view')
                                <label style="font-weight: bold;" for="">@lang('lang.purchase_price'): </label>
                                {{ @num_format($product->purchase_price) }}<br>
                            @endcan
                            <label style="font-weight: bold;" for="">@lang('lang.is_service'): </label>
                            @if (!empty($product->is_service))
                                @lang('lang.yes')
                            @else
                                @lang('lang.no')
                            @endif
                            <br>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="col-sm-12 col-md-12 invoice-col">
                        <div class="thumbnail">
                            <img class="img-fluid"
                                src="@if (!empty($product->getFirstMediaUrl('product'))) {{ $product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                alt="Product Image">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <br>
                    <h4>@lang('lang.stock_details')</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-success text-white">
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.sku')</th>
                                <th>@lang('lang.store_name')</th>
                                <th>@lang('lang.current_stock')</th>
                                <th>@lang('lang.selling_price')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stock_detials as $stock_detial)
                                <tr>
                                    <td>
                                        @if (!empty($stock_detial->variation->name) && $stock_detial->variation->name != 'Default')
                                            {{ $stock_detial->variation->name }}
                                        @else
                                            {{ $stock_detial->product->name }}
                                        @endif
                                    </td>
                                    <td>{{ $stock_detial->variation->sub_sku ?? '' }}</td>
                                    <td>{{ $stock_detial->store->name ?? '' }}</td>
                                    <td>{{ @num_format($stock_detial->qty_available) }}</td>
                                    <td>{{ @num_format($stock_detial->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <br>
                    <br>
                    <h4>@lang('lang.sales')</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-success text-white">
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.sku')</th>
                                <th>@lang('lang.invoice_no')</th>
                                <th>@lang('lang.date')</th>
                                <th>@lang('lang.quantity')</th>
                                <th>@lang('lang.price')</th>
                                <th>@lang('lang.discount')</th>
                                <th>@lang('lang.sub_total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>
                                        @if (!empty($sale->variation->name) && $sale->variation->name != 'Default')
                                            {{ $sale->variation->name }}
                                        @else
                                            {{ $sale->product->name }}
                                        @endif
                                    </td>
                                    <td>{{ $sale->variation->sub_sku ?? '' }}</td>
                                    <td>{{ $sale->transaction->invoice_no ?? '' }}</td>
                                    <td>{{ !empty($sale->transaction->transaction_date) ? @format_date($sale->transaction->transaction_date) : '' }}
                                    </td>
                                    <td>{{ @num_format($sale->quantity) }}</td>
                                    <td>{{ @num_format($sale->sell_price) }}</td>
                                    <td>{{ @num_format($sale->product_discount_amount) }}</td>
                                    <td>{{ @num_format($sale->sub_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">@lang('lang.total'):</th>
                                <td>{{ @num_format($sales->sum('quantity')) }}</td>
                                <td></td>
                                <td>{{ @num_format($sales->sum('product_discount_amount')) }}</td>
                                <td>{{ @num_format($sales->sum('sub_total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12">
                    <br>
                    <br>
                    <h4>@lang('lang.add_stock')</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-success text-white">
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.sku')</th>
                                <th>@lang('lang.invoice_no')</th>
                                <th>@lang('lang.date')</th>
                                <th>@lang('lang.quantity')</th>
                                <th>@lang('lang.price')</th>
                                <th>@lang('lang.sub_total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($add_stocks as $add_stock)
                                <tr>
                                    <td>
                                        @if (!empty($add_stock->variation->name) && $add_stock->variation->name != 'Default')
                                            {{ $add_stock->variation->name }}
                                        @else
                                            {{ $add_stock->product->name }}
                                        @endif
                                    </td>
                                    <td>{{ $add_stock->variation->sub_sku ?? '' }}</td>
                                    <td>{{ $add_stock->transaction->invoice_no ?? '' }}</td>
                                    <td>{{ !empty($add_stock->transaction->transaction_date)? @format_date($add_stock->transaction->transaction_date): '' }}
                                    </td>
                                    <td>{{ @num_format($add_stock->quantity) }}</td>
                                    <td>{{ @num_format($add_stock->purchase_price) }}</td>
                                    <td>{{ @num_format($add_stock->sub_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">@lang('lang.total'):</th>
                                <td>{{ @num_format($add_stocks->sum('quantity')) }}</td>
                                <td></td>
                                <td>{{ @num_format($add_stocks->sum('sub_total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
