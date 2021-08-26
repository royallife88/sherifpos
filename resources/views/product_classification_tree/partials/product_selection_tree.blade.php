<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="margin-top: 30px;">
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('lang.products')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="pct_modal_body">
                <div class="col-md-12  no-print">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.product_classification_tree')</h4>
                        </div>
                        <div class="card-body">

                            @foreach ($product_classes as $class)


                            <div class="accordion top_accordion" id="{{@replace_space($class->name)}}">
                                <div class="accordion-group">
                                    <div class="row">
                                        <input id="product_class_selected{{$class->id}}"
                                            name="pct[product_class_selected][]" type="checkbox" value="{{$class->id}}" @if(in_array($class->id, $product_class_selected)) checked @endif
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
                                        <div class="accordion-inner">
                                            @php
                                            $i = 0;
                                            $categories = App\Models\Category::where('product_class_id',
                                            $class->id)->whereNotNull('categories.name')->select('categories.id',
                                            'categories.name')->groupBy('categories.id')->get();
                                            @endphp
                                            @foreach ($categories as $category)
                                            <div class="accordion" id="{{@replace_space('category_'.$category->name.'_'.$i)}}"
                                                style="margin-left: 20px;">
                                                <div class="accordion-group">
                                                    <div class="row">
                                                        <input id="category_selected{{$category->id}}"
                                                            name="pct[category_selected][]" type="checkbox" @if(in_array($category->id, $category_selected)) checked @endif
                                                            value="{{$category->id}}" class="my-new-checkbox">
                                                        <div class="accordion-heading" style="width: 80%">
                                                            <a class="accordion-toggle" data-toggle="collapse"
                                                                data-id="{{@replace_space('category_'.$category->name.'_'.$i)}}"
                                                                data-parent="#{{@replace_space('category_'.$category->name.'_'.$i)}}"
                                                                href="#collapse{{@replace_space('category_'.$category->name.'_'.$i)}}">
                                                                <i
                                                                    class="fa fa-angle-right angle-class-{{@replace_space('category_'.$category->name.'_'.$i)}}"></i>
                                                                {{$category->name}}

                                                            </a>
                                                        </div>

                                                    </div>
                                                    <div id="collapse{{@replace_space('category_'.$category->name.'_'.$i)}}"
                                                        class="accordion-body collapse in">
                                                        <div class="accordion-inner">
                                                            @php
                                                            $sub_categories = App\Models\Category::where('parent_id',
                                                            $category->id)->whereNotNull('categories.name')->select('categories.id','categories.name')->groupBy('categories.id')->get();
                                                            @endphp
                                                            @foreach ($sub_categories as $sub_category)
                                                            <div class="accordion"
                                                                id="{{@replace_space('sub_category_'.$sub_category->name.'_'.$i)}}"
                                                                style="margin-left: 20px;">
                                                                <div class="accordion-group">
                                                                    <div class="row">
                                                                        <input
                                                                            id="sub_category_selected{{$sub_category->id}}"
                                                                            name="pct[sub_category_selected][]"
                                                                            type="checkbox"
                                                                            value="{{$sub_category->id}}" @if(in_array($sub_category->id, $sub_category_selected)) checked @endif
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
                                                                            $brands = App\Models\Product::leftjoin('brands', 'products.brand_id', 'brands.id')->where('products.sub_category_id',
                                                                            $sub_category->id)->select('brands.id', 'brands.name')->groupBy('brands.id')->get();
                                                                            @endphp
                                                                            @foreach ($brands as $brand)
                                                                            <div class="accordion"
                                                                                id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                style="margin-left: 20px;">
                                                                                <div class="accordion-group">
                                                                                    <div class="row">
                                                                                        <input
                                                                                            id="brand_selected{{$brand->id}}"
                                                                                            name="pct[brand_selected][]"
                                                                                            type="checkbox"
                                                                                            value="{{$brand->id}}" @if(in_array($brand->id, $brand_selected)) checked @endif
                                                                                            class="my-new-checkbox">
                                                                                        <div class="accordion-heading"
                                                                                            style="width: 80%">
                                                                                            <a class="accordion-toggle"
                                                                                                data-toggle="collapse"
                                                                                                data-id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                                data-parent="#{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                                href="#collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}">
                                                                                                <i
                                                                                                    class="fa fa-angle-right angle-class-{{@replace_space('brand_'.$brand->name.'_'.$i)}}"></i>
                                                                                                {{$brand->name}}

                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                        class="accordion-body collapse in">
                                                                                        <div class="accordion-inner">
                                                                                            @php
                                                                                            $products =
                                                                                            App\Models\Product::where('brand_id',
                                                                                            $brand->id)->select('products.id',
                                                                                            'products.name',
                                                                                            'products.expiry_date')->groupBy('products.id')->get();
                                                                                            @endphp
                                                                                            @foreach ($products as
                                                                                            $product)
                                                                                            <div class="accordion"
                                                                                                id="{{$product->name}}"
                                                                                                style="margin-left: 20px;">
                                                                                                <div
                                                                                                    class="accordion-group">
                                                                                                    <div class="row">
                                                                                                        <input
                                                                                                            id="product_selected{{$product->id}}"
                                                                                                            name="pct[product_selected][]"
                                                                                                            type="checkbox"
                                                                                                            value="{{$product->id}}" @if(in_array($product->id, $product_selected)) checked @endif
                                                                                                            class="my-new-checkbox">
                                                                                                        <div class="accordion-heading"
                                                                                                            style="width: 80%">
                                                                                                            <a class="accordion-toggle"
                                                                                                                data-toggle="collapse"
                                                                                                                data-id="{{$product->name}}"
                                                                                                                data-parent="#{{$product->name}}"
                                                                                                                href="#collapse{{$product->name}}">
                                                                                                                <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('images/default.jpg')}}@endif"
                                                                                                                    alt="photo"
                                                                                                                    width="50"
                                                                                                                    height="50">
                                                                                                                {{$product->name}}
                                                                                                                <span
                                                                                                                    style="color: #737475">@if(!empty($product->expiry_date))
                                                                                                                    @lang('lang.expiry')
                                                                                                                    {{@format_date($product->expiry_date)}}@endif</span>

                                                                                                            </a>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div id="collapse{{$product->name}}"
                                                                                                        class="accordion-body collapse in">
                                                                                                        <div
                                                                                                            class="accordion-inner">
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
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
            </div>
        </div>
    </div>
</div>
