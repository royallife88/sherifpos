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
                @if($product->is_service == 0)
                @if ($add_stocks->count() > 0)
                    <div class="col-md-12">
                        <br>
                        <br>
                        <h4>@lang('lang.expiry_details')</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-success text-white">
                                    <th>@lang('lang.store_name')</th>
                                    <th>@lang('lang.current_stock')</th>
                                    <th>@lang('lang.expiry_date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($add_stocks as $add_stock)
                                    <tr>
                                        <td>{{ $add_stock->store->name ?? '' }}</td>
                                        <td>{{ @num_format($add_stock->current_stock) }}</td>
                                        <td>{{ @format_date($add_stock->expiry_date) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
