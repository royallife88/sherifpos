@foreach ($brands as $brand)
<div class="accordion" id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}" style="margin-left: 20px;">
    <div class="accordion-group  brand_level level">
        <div class="row">
            <input id="brand_selected{{$brand->id}}" name="pct[brand_selected][]" type="checkbox" value="{{$brand->id}}"
                @if(in_array($brand->id, $brand_selected)) checked @endif
            class="my-new-checkbox">
            <div class="accordion-heading" style="width: 80%">
                <a class="accordion-toggle" data-toggle="collapse"
                    data-id="{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                    data-parent="#{{@replace_space('brand_'.$brand->name.'_'.$i)}}"
                    href="#collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}">
                    <i class="fa fa-angle-right angle-class-{{@replace_space('brand_'.$brand->name.'_'.$i)}}"></i>
                    {{$brand->name}}

                </a>
            </div>
        </div>
        <div id="collapse{{@replace_space('brand_'.$brand->name.'_'.$i)}}" class="accordion-body collapse in">
            <div class="accordion-inner">
                @php
                $products =
                App\Models\Product::leftjoin('variations', 'products.id', 'variations.product_id')->where('brand_id',
                $brand->id)->select('products.id',
                'products.name', 'variations.id as variation_id')->groupBy('products.id')->get();
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
                                    <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                        alt="photo" width="50" height="50">
                                    {{$product->name}}
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
