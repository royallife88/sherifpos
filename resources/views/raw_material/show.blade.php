<div class="modal-dialog" role="document" style="max-width: 65%;">
    <div class="modal-content">


        <div class="modal-header">

            <h4 class="modal-title">{{$product->name}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-weight: bold;" for="">@lang('lang.sku'): </label>
                            {{$product->sku}} <br>
                            <label style="font-weight: bold;" for="">@lang('lang.brand'): </label>
                            @if(!empty($product->brand)){{$product->brand->name}}@endif<br>

                        </div>
                        <div class="col-md-6">
                            <label style="font-weight: bold;" for="">@lang('lang.unit'): </label>
                            {{implode(', ', $product->units->pluck('name')->toArray())}}<br>
                            @can('product_module.purchase_price.view')
                            <label style="font-weight: bold;" for="">@lang('lang.purchase_price'): </label>
                            {{@num_format($product->purchase_price)}}<br>
                            @endcan
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="col-sm-12 col-md-12 invoice-col">
                        <div class="thumbnail">
                            <img class="img-fluid"
                                src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                alt="Product Image">
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div class="col-md-12 mt-5">
                    <table class="table table-bordered" id="consumption_table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">@lang('lang.used_in')</th>
                                <th style="width: 30%;">@lang('lang.used_amount')</th>
                                <th style="width: 30%;">@lang('lang.unit')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->consumption_products as $item)
                            <tr>
                                <td>
                                    {{$item->variation->product->name ?? ''}}
                                    @if(!empty($item->variation) &&
                                    $item->variation->name != 'Default')
                                    - {{$item->variation->name}}
                                    @endif
                                </td>
                                <td>
                                    {{@num_format($item->amount_used)}}
                                </td>
                                <td>
                                    {{$item->unit->name ?? ''}}
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
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
                                <td>@if(!empty($stock_detial->variation->name) &&
                                    $stock_detial->variation->name != 'Default'){{$stock_detial->variation->name}} @else
                                    {{$stock_detial->product->name}} @endif
                                </td>
                                <td>{{$stock_detial->variation->sub_sku ?? ''}}</td>
                                <td>{{$stock_detial->store->name ?? ''}}</td>
                                <td>{{@num_format($stock_detial->qty_available)}}</td>
                                <td>{{@num_format($stock_detial->price)}}</td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
