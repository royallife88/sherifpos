@foreach ($brands as $brand)
<div class="accordion" id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}" style="margin-left: 20px;">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse"
                data-id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                data-parent="#{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                href="#collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}">
                <i class="fa fa-angle-right angle-class-{{@replace_space('brand_'.$brand->name.'_'.$i)}}"></i>
                {{$brand->name}}
                <div class="btn-group pull-right">
                    <button data-container=".view_modal" data-href="{{action('BrandController@edit', $brand->id)}}"
                        class="btn btn-modal btn-primary btn-xs"><i class="dripicons-document-edit"></i>
                    </button>
                    <button data-href="{{action('BrandController@destroy', $brand->id)}}"
                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                        class="btn delete_item btn-danger btn-xs"><i class="dripicons-trash"></i></button>
                </div>
            </a>
        </div>
        <div id="collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}" class="accordion-body collapse in">
            <div class="accordion-inner">
                @php
                $products =
                App\Models\Product::where('brand_id',
                $brand->id)->select('products.id',
                'products.name')->groupBy('products.id')->get();
                @endphp
                @foreach ($products as $product)
                <div class="accordion" id="{{$product->name}}" style="margin-left: 20px;">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-id="{{$product->name}}"
                                data-parent="#{{$product->name}}" href="#collapse{{$product->name}}">
                                <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                    alt="photo" width="50" height="50">
                                {{$product->name}}
                                <div class="btn-group pull-right">
                                    <button data-href="{{action('ProductController@edit', $product->id)}}"
                                        class="btn btn-primary btn-xs product_edit"><i
                                            class="dripicons-document-edit"></i>
                                    </button>
                                    <button data-href="{{action('ProductController@destroy', $product->id)}}"
                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                        class="btn delete_item btn-danger btn-xs"><i
                                            class="dripicons-trash"></i></button>
                                </div>
                            </a>
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
