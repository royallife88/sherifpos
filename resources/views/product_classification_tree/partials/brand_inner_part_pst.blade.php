@foreach ($brands as $brand)
<div class="accordion" id="{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
    style="margin-left: 20px;">
    <div class="accordion-group  brand_level level">
        <div class="row">
            <input id="brand_selected{{$brand->id}}" name="pct[brand_selected][]" type="checkbox" value="{{$brand->id}}"
                @if(in_array($brand->id, $brand_selected)) checked @endif
            class="my-new-checkbox">
            <div class="accordion-heading" style="width: 80%">
                <a class="accordion-toggle" data-toggle="collapse"
                    data-id="{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
                    data-parent="#{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
                    href="#collapse{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}">
                    <i
                        class="fa fa-angle-right angle-class-{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"></i>
                    {{$brand->name}}

                </a>
            </div>
        </div>
        <div id="collapse{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
            class="accordion-body collapse in">
            <div class="accordion-inner">
                @php
                $query =
                App\Models\Product::leftjoin('variations', 'products.id', 'variations.product_id')->where('brand_id',
                $brand->id);
                if(!empty($category_id)){
                $query->where('category_id', $category_id);
                }
                if(!empty($sub_category_id)){
                $query->where('sub_category_id', $sub_category_id);
                }
                $products = $query->select('products.id',
                'products.name', 'variations.id as variation_id', 'products.sku',
                'products.sell_price')->groupBy('products.id')->get();
                @endphp
                @foreach ($products as
                $product)
                <div class="accordion" id="{{$product->name}}" style="margin-left: 20px;">
                    <div class="accordion-group  product_level level">
                        <div class="row">
                            <input id="product_selected{{$product->id}}" name="pct[product_selected][]" type="checkbox"
                                value="{{$product->id}}" @if(in_array($product->id, $product_selected)) checked @endif
                            class="my-new-checkbox product_checkbox">
                            <div class="accordion-heading" style="width: 80%">
                                <a class="accordion-toggle" data-toggle="collapse" data-id="{{$product->name}}"
                                    data-parent="#{{$product->name}}" href="#collapse{{$product->name}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                                alt="photo" width="50" height="50">
                                            {{$product->name}}
                                        </div>
                                        @php
                                            $expiry_date = App\Models\AddStockLine::where('product_id', $product->id)->whereDate('expiry_date', '>=', date('Y-m-d'))->select('expiry_date')->orderBy('expiry_date', 'asc')->first();
                                            $current_stock = App\Models\ProductStore::where('product_id', $product->id)->select(DB::raw('SUM(product_stores.qty_available) as current_stock'))->first();
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-md-12">
                                                        <label style="color: #222;">@lang('lang.sku'): {{$product->sku}}</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label style="color: #222;">@lang('lang.expiry'): @if(!empty($expiry_date)){{@format_date($expiry_date->expiry_date)}}@else{{'N/A'}}@endif</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="col-md-12">
                                                        <label
                                                            style="color: #222;">@lang('lang.stock'): @if(!empty($current_stock)){{@num_format($current_stock->current_stock)}}@endif</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label style="color: #222;">@lang('lang.price'): {{@num_format($product->sell_price)}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div id="collapse{{$product->name}}" class="accordion-body collapse in">
                            <div class="accordion-inner">
                            </div>
                        </div>
                    </div>

                </div>
                @php
                $i++;
                @endphp
                @endforeach
            </div>
        </div>
    </div>

</div>
@php
$i++;
@endphp
@endforeach
