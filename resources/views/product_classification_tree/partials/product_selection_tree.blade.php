<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pctModal" style="margin-top: 30px;">
    @lang('lang.select_products')
</button>
<style>
    .my-new-checkbox {
        margin-top: 22px;
        margin-right: 10px;
    }

    .accordion-toggle {
        color: #1391ff !important;
        width: 100%;
        border: 1px solid #d1cece;
        padding: 15px;

    }

    .accordion-toggle:hover {
        text-decoration: none;
    }

    .accordion-toggle:focus {
        text-decoration: none;
    }
</style>
<!-- Modal -->
@php
$product_class_selected = !empty($pct_data['product_class_selected']) ? $pct_data['product_class_selected'] : [];
$category_selected = !empty($pct_data['category_selected']) ? $pct_data['category_selected'] : [];
$sub_category_selected = !empty($pct_data['sub_category_selected']) ? $pct_data['sub_category_selected'] : [];
$brand_selected = !empty($pct_data['brand_selected']) ? $pct_data['brand_selected'] : [];
$product_selected = !empty($pct_data['product_selected']) ? $pct_data['product_selected'] : [];

@endphp
<div class="modal fade" id="pctModal" tabindex="-1" role="dialog" aria-labelledby="pctModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pctModalLabel">@lang('lang.products')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="pct_modal_body">
                <div class="col-md-12  no-print">
                    <div class="card">
                        <div class="card-header align-items-center">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>@lang('lang.product_classification_tree')</h4>

                                </div>
                                <div class="col-md-6">
                                    {!! Form::select('search_product[]', $products, '', ['class' => 'form-control
                                    selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.search_product'), 'id' => 'search_pct', 'data-actions-box' => 'true', 'multiple']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="accordian_div">

                            @foreach ($product_classes as $class)


                            <div class="accordion top_accordion" id="{{@replace_space($class->name)}}">
                                <div class="accordion-group class_level level">
                                    <div class="row">
                                        <input id="product_class_selected{{$class->id}}"
                                            name="pct[product_class_selected][]" type="checkbox" value="{{$class->id}}"
                                            @if(in_array($class->id, $product_class_selected)) checked @endif
                                        class="my-new-checkbox">

                                        <div class="accordion-heading" style="width: 80%">
                                            <a class="accordion-toggle" data-toggle="collapse"
                                                data-id="{{@replace_space($class->name)}}"
                                                data-parent="#{{@replace_space($class->name)}}"
                                                href="#collapse{{@replace_space($class->name)}}">
                                                <i
                                                    class="fa fa-angle-right angle-class-{{@replace_space($class->name)}}"></i>
                                                {{$class->name}}

                                            </a>
                                        </div>
                                    </div>
                                    <div id="collapse{{@replace_space($class->name)}}" class="accordion-body collapse">
                                        @if(session('system_mode') != 'restaurant')
                                        <div class="accordion-inner">
                                            @php
                                            $i = 0;
                                            $categories = App\Models\Category::where('product_class_id',
                                            $class->id)->whereNotNull('categories.name')->select('categories.id',
                                            'categories.name')->groupBy('categories.id')->get();
                                            @endphp
                                            @foreach ($categories as $category)
                                            <div class="accordion"
                                                id="{{@replace_space($class->id. 'category_'.$category->name.'_'.$i)}}"
                                                style="margin-left: 20px;">
                                                <div class="accordion-group  category_level level">
                                                    <div class="row">
                                                        <input id="category_selected{{$category->id}}"
                                                            name="pct[category_selected][]" type="checkbox"
                                                            @if(in_array($category->id, $category_selected)) checked
                                                        @endif
                                                        value="{{$category->id}}" class="my-new-checkbox">
                                                        <div class="accordion-heading" style="width: 80%">
                                                            <a class="accordion-toggle" data-toggle="collapse"
                                                                data-id="{{@replace_space($class->id . 'category_'.$category->name.'_'.$i)}}"
                                                                data-parent="#{{@replace_space($class->id . 'category_'.$category->name.'_'.$i)}}"
                                                                href="#collapse{{@replace_space($class->id . 'category_'.$category->name.'_'.$i)}}">
                                                                <i
                                                                    class="fa fa-angle-right angle-class-{{@replace_space($class->id . 'category_'.$category->name.'_'.$i)}}"></i>
                                                                {{$category->name}}

                                                            </a>
                                                        </div>

                                                    </div>
                                                    <div id="collapse{{@replace_space($class->id . 'category_'.$category->name.'_'.$i)}}"
                                                        class="accordion-body collapse in">
                                                        <div class="accordion-inner">
                                                            @php
                                                            $sub_categories = App\Models\Category::where('parent_id',
                                                            $category->id)->whereNotNull('categories.name')->select('categories.id','categories.name')->groupBy('categories.id')->get();

                                                            $brands = null;
                                                            $brands = App\Models\Product::leftjoin('brands',
                                                            'products.brand_id',
                                                            'brands.id')->where('products.category_id',
                                                            $category->id)->whereNull('products.sub_category_id')->whereNotNull('brands.id')->whereNotNull('brands.name')->select('brands.id',
                                                            'brands.name')->groupBy('brands.id')->get();
                                                            @endphp

                                                            @if($brands->count() == 0 && $sub_categories->count() == 0)
                                                            @php
                                                                $products = App\Models\Product::leftjoin('variations', 'products.id', 'variations.product_id')->where('category_id', $category->id)->whereNotNull('products.name')->select('products.id', 'products.name', 'variations.name as variation_name','variations.sub_sku as sku', 'variations.default_sell_price as sell_price')->groupBy('variations.id')->get();
                                                            @endphp
                                                            @foreach ($products as $product)
                                                            @include('product_classification_tree.partials.product_inner_part_pst', [
                                                                'product' => $product,
                                                            ])
                                                            @endforeach
                                                            @endif

                                                            @if (!empty($brands) && $brands->count() > 0)
                                                            @include('product_classification_tree.partials.brand_inner_part_pst',
                                                            ['brands' => $brands, 'brand_selected' => $brand_selected,
                                                            'product_class_id' => $class->id, 'category_id' => $category->id])
                                                            @endif
                                                            @foreach ($sub_categories as $sub_category)
                                                            <div class="accordion"
                                                                id="{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"
                                                                style="margin-left: 20px;">
                                                                <div class="accordion-group  sub_category_level level">
                                                                    <div class="row">
                                                                        <input
                                                                            id="sub_category_selected{{$sub_category->id}}"
                                                                            name="pct[sub_category_selected][]"
                                                                            type="checkbox"
                                                                            value="{{$sub_category->id}}"
                                                                            @if(in_array($sub_category->id,
                                                                        $sub_category_selected)) checked @endif
                                                                        class="my-new-checkbox">
                                                                        <div class="accordion-heading"
                                                                            style="width: 80%">
                                                                            <a class="accordion-toggle"
                                                                                data-toggle="collapse"
                                                                                data-id="{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"
                                                                                data-parent="#{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"
                                                                                href="#collapse{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}">
                                                                                <i
                                                                                    class="fa fa-angle-right angle-class-{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"></i>
                                                                                {{$sub_category->name}}

                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div id="collapse{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"
                                                                        class="accordion-body collapse in">
                                                                        <div class="accordion-inner">
                                                                            @php
                                                                            $brands = null;
                                                                            $brands =
                                                                            App\Models\Product::leftjoin('brands',
                                                                            'products.brand_id',
                                                                            'brands.id')->where('products.sub_category_id',
                                                                            $sub_category->id)->whereNotNull('brands.id')->whereNotNull('brands.name')->select('brands.id',
                                                                            'brands.name')->groupBy('brands.id')->get();
                                                                            @endphp
                                                                            @if (!empty($brands) && $brands->count() >
                                                                            0)
                                                                            @include('product_classification_tree.partials.brand_inner_part_pst',
                                                                            ['brands' => $brands, 'brand_selected' =>
                                                                            $brand_selected, 'product_class_id' =>
                                                                            $class->id, 'category_id' => $category->id, 'sub_category_id' =>
                                                                            $sub_category->id])
                                                                            @endif
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

                                        </div>
                                        @else
                                        <div class="accordion-inner">
                                            @php
                                            $query =
                                            App\Models\Product::where('product_class_id',
                                            $class->id);

                                            $products = $query->select('products.id',
                                            'products.name', 'products.sku',
                                            'products.sell_price')->groupBy('products.id')->get();
                                            @endphp
                                            @foreach ($products as
                                            $product)

                                            <div class="row product_row">
                                                <div class="col-md-1">
                                                    <input id="product_selected{{$product->id}}"
                                                        name="pct[product_selected][]" type="checkbox"
                                                        value="{{$product->id}}" @if(in_array($product->id,
                                                    $product_selected)) checked @endif
                                                    class="my-new-checkbox product_checkbox">
                                                </div>
                                                <div class="col-md-5">
                                                    <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                                        alt="photo" width="50" height="50">
                                                    <a href="">{{$product->name}}</a>
                                                </div>
                                                @php
                                                $expiry_date =
                                                App\Models\AddStockLine::where('product_id',
                                                $product->id)->whereDate('expiry_date', '>=',
                                                date('Y-m-d'))->select('expiry_date')->orderBy('expiry_date',
                                                'asc')->first();
                                                $current_stock =
                                                App\Models\ProductStore::where('product_id',
                                                $product->id)->select(DB::raw('SUM(product_stores.qty_available)
                                                as current_stock'))->first();
                                                @endphp
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="col-md-12">
                                                                <label style="color: #222;">@lang('lang.sku'):
                                                                    {{$product->sku}}</label>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label style="color: #222;">@lang('lang.expiry'):
                                                                    @if(!empty($expiry_date)){{@format_date($expiry_date->expiry_date)}}@else{{'N/A'}}@endif</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="col-md-12">
                                                                <label style="color: #222;">@lang('lang.stock'):
                                                                    @if(!empty($current_stock)){{@num_format($current_stock->current_stock)}}@endif</label>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label style="color: #222;">@lang('lang.price'):
                                                                    {{@num_format($product->sell_price)}}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @endforeach
                                        </div>

                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>


                        {{-- @php
                            $products_no_class = App\Models\Product::whereNull('product_class_id')->get();

                        @endphp
                        @foreach ($products_no_class as $product)


                        <div class="row product_row">
                            <div class="col-md-1">
                                <input id="product_selected{{$product->id}}"
                                    name="pct[product_selected][]" type="checkbox"
                                    value="{{$product->id}}" @if(in_array($product->id,
                                $product_selected)) checked @endif
                                class="my-new-checkbox product_checkbox">
                            </div>
                            <div class="col-md-5">
                                <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                    alt="photo" width="50" height="50">
                                <a href="">{{$product->name}}</a>
                            </div>
                            @php
                            $expiry_date =
                            App\Models\AddStockLine::where('product_id',
                            $product->id)->whereDate('expiry_date', '>=',
                            date('Y-m-d'))->select('expiry_date')->orderBy('expiry_date',
                            'asc')->first();
                            $current_stock =
                            App\Models\ProductStore::where('product_id',
                            $product->id)->select(DB::raw('SUM(product_stores.qty_available)
                            as current_stock'))->first();
                            @endphp
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <label style="color: #222;">@lang('lang.sku'):
                                                {{$product->sku}}</label>
                                        </div>
                                        <div class="col-md-12">
                                            <label style="color: #222;">@lang('lang.expiry'):
                                                @if(!empty($expiry_date)){{@format_date($expiry_date->expiry_date)}}@else{{'N/A'}}@endif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <label style="color: #222;">@lang('lang.stock'):
                                                @if(!empty($current_stock)){{@num_format($current_stock->current_stock)}}@endif</label>
                                        </div>
                                        <div class="col-md-12">
                                            <label style="color: #222;">@lang('lang.price'):
                                                {{@num_format($product->sell_price)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach --}}


                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.next')</button>
            </div>
        </div>
    </div>
</div>
