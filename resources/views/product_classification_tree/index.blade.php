@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')

<style>
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
<div class="container-fluid">
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.product_classification_tree')</h4>
            </div>
            <div class="card-body">

                @foreach ($product_classes as $class)


                <div class="accordion" id="{{@replace_space($class->name)}}">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse"
                                data-id="{{@replace_space($class->name)}}"
                                data-parent="#{{@replace_space($class->name)}}"
                                href="#collapse{{@replace_space($class->name)}}">
                                <i class="fa fa-angle-right angle-class-{{@replace_space($class->name)}}"></i>
                                {{$class->name}}
                                <div class="btn-group pull-right">
                                    <button data-container=".view_modal"
                                        data-href="{{action('ProductClassController@edit', $class->id)}}"
                                        class="pull-right btn btn-modal btn-primary btn-xs"><i
                                            class="dripicons-document-edit"></i> </button>
                                    <button data-href="{{action('ProductClassController@destroy', $class->id)}}"
                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                        class="pull-right btn delete_item btn-danger btn-xs"><i
                                            class="dripicons-trash"></i></button>

                                </div>
                            </a>
                        </div>
                        <div id="collapse{{@replace_space($class->name)}}" class="accordion-body collapse">
                            <div class="accordion-inner">
                                @php
                                $i = 0;
                                $categories = App\Models\Category::where('product_class_id', $class->id)->whereNotNull('categories.name')->select('categories.id',
                                'categories.name')->groupBy('categories.id')->get();
                                @endphp
                                @foreach ($categories as $category)
                                <div class="accordion" id="{{@replace_space($category->name.'_'.$i)}}"
                                    style="margin-left: 20px;">
                                    <div class="accordion-group">
                                        <div class="accordion-heading">
                                            <a class="accordion-toggle" data-toggle="collapse"
                                                data-id="{{@replace_space($category->name.'_'.$i)}}"
                                                data-parent="#{{@replace_space($category->name.'_'.$i)}}"
                                                href="#collapse{{@replace_space($category->name.'_'.$i)}}">
                                                <i
                                                    class="fa fa-angle-right angle-class-{{@replace_space($category->name.'_'.$i)}}"></i>
                                                {{$category->name}}
                                                <div class="btn-group pull-right">
                                                    <button data-container=".view_modal"
                                                        data-href="{{action('CategoryController@edit', $category->id)}}"
                                                        class="pull-right btn btn-modal btn-primary btn-xs"><i
                                                            class="dripicons-document-edit"></i> </button>
                                                    <button
                                                        data-href="{{action('CategoryController@destroy', $category->id)}}"
                                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                        class="pull-right btn delete_item btn-danger btn-xs"><i
                                                            class="dripicons-trash"></i></button>
                                                </div>
                                            </a>
                                        </div>
                                        <div id="collapse{{@replace_space($category->name.'_'.$i)}}"
                                            class="accordion-body collapse in">
                                            <div class="accordion-inner">
                                                @php
                                                $sub_categories =  App\Models\Category::where('parent_id', $category->id)->whereNotNull('categories.name')->select('categories.id','categories.name')->groupBy('categories.id')->get();
                                                @endphp
                                                @foreach ($sub_categories as $sub_category)
                                                <div class="accordion"
                                                    id="{{@replace_space($sub_category->name.'_'.$i)}}"
                                                    style="margin-left: 20px;">
                                                    <div class="accordion-group">
                                                        <div class="accordion-heading">
                                                            <a class="accordion-toggle" data-toggle="collapse"
                                                                data-id="{{@replace_space($sub_category->name.'_'.$i)}}"
                                                                data-parent="#{{@replace_space($sub_category->name.'_'.$i)}}"
                                                                href="#collapse{{@replace_space($sub_category->name.'_'.$i)}}">
                                                                <i
                                                                    class="fa fa-angle-right angle-class-{{@replace_space($sub_category->name.'_'.$i)}}"></i>
                                                                {{$sub_category->name}}
                                                                <div class="btn-group pull-right">
                                                                    <button data-container=".view_modal"
                                                                        data-href="{{action('CategoryController@edit', $sub_category->id)}}"
                                                                        class="btn btn-modal btn-primary btn-xs"><i
                                                                            class="dripicons-document-edit"></i>
                                                                    </button>
                                                                    <button
                                                                        data-href="{{action('CategoryController@destroy', $sub_category->id)}}"
                                                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                                        class="btn delete_item btn-danger btn-xs"><i
                                                                            class="dripicons-trash"></i></button>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div id="collapse{{@replace_space($sub_category->name.'_'.$i)}}"
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
                                                                        <div class="accordion-heading">
                                                                            <a class="accordion-toggle"
                                                                                data-toggle="collapse"
                                                                                data-id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                data-parent="#{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                                href="#collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}">
                                                                                <i
                                                                                    class="fa fa-angle-right angle-class-{{@replace_space('brand_'.$brand->name.'_'.$i)}}"></i>
                                                                                {{$brand->name}}
                                                                                <div class="btn-group pull-right">
                                                                                    <button data-container=".view_modal"
                                                                                        data-href="{{action('BrandController@edit', $brand->id)}}"
                                                                                        class="btn btn-modal btn-primary btn-xs"><i
                                                                                            class="dripicons-document-edit"></i>
                                                                                    </button>
                                                                                    <button
                                                                                        data-href="{{action('BrandController@destroy', $brand->id)}}"
                                                                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                                                        class="btn delete_item btn-danger btn-xs"><i
                                                                                            class="dripicons-trash"></i></button>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                        <div id="collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                                                                            class="accordion-body collapse in">
                                                                            <div class="accordion-inner">
                                                                                @php
                                                                                $products =
                                                                                App\Models\Product::where('brand_id',
                                                                                $brand->id)->select('products.id',
                                                                                'products.name', 'products.expiry_date')->groupBy('products.id')->get();
                                                                                @endphp
                                                                                @foreach ($products as $product)
                                                                                <div class="accordion"
                                                                                    id="{{$product->name}}"
                                                                                    style="margin-left: 20px;">
                                                                                    <div class="accordion-group">
                                                                                        <div class="accordion-heading">
                                                                                            <a class="accordion-toggle"
                                                                                                data-toggle="collapse"
                                                                                                data-id="{{$product->name}}"
                                                                                                data-parent="#{{$product->name}}"
                                                                                                href="#collapse{{$product->name}}">
                                                                                                <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('images/default.jpg')}}@endif"
                                                                                                alt="photo" width="50" height="50"> {{$product->name}} <span style="color: #737475">@if(!empty($product->expiry_date)) @lang('lang.expiry') {{@format_date($product->expiry_date)}}@endif</span>
                                                                                                <div
                                                                                                    class="btn-group pull-right">
                                                                                                    <button
                                                                                                        data-href="{{action('ProductController@edit', $product->id)}}"
                                                                                                        class="btn btn-primary btn-xs product_edit"><i
                                                                                                            class="dripicons-document-edit"></i>
                                                                                                    </button>
                                                                                                    <button
                                                                                                        data-href="{{action('ProductController@destroy', $product->id)}}"
                                                                                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                                                                        class="btn delete_item btn-danger btn-xs"><i
                                                                                                            class="dripicons-trash"></i></button>
                                                                                                </div>
                                                                                            </a>
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

@endsection

@section('javascript')
<script>
    $(document).on('click', '.accordion-toggle', function (){
        let id = $(this).data('id');
        console.log($('.angle-class-'+id).hasClass('fa-angle-right'));
        if($('.angle-class-'+id).hasClass('fa-angle-right')){
            $('.angle-class-'+id).removeClass('fa-angle-right');
            $('.angle-class-'+id).addClass('fa-angle-down');
        }
        else if($('.angle-class-'+id).hasClass('fa-angle-down')){
            $('.angle-class-'+id).removeClass('fa-angle-down');
            $('.angle-class-'+id).addClass('fa-angle-right');
        }
    });

    $(document).on('click', '.product_edit', function(){
        let href = $(this).data('href');

        if(href){
            window.location = href;
        }
    })
</script>

@endsection
