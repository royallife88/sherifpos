@foreach ($brands as $brand)
<div class="accordion" id="{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}" style="margin-left: 20px;">
    <div class="accordion-group  pci_brand_level level">
        <div class="row">
            <input id="brand_selected{{$brand->id}}" name="pci[brand_selected][]" type="checkbox" value="{{$brand->id}}"
                @if(in_array($brand->id, $brand_selected)) checked @endif
            class="pci-my-new-checkbox">
            <div class="accordion-heading" style="width: 80%">
                <a class="pci-accordion-toggle" data-toggle="collapse"
                    data-id="{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
                    data-parent="#{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"
                    href="#pci-collapse{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}">
                    <i class="fa fa-angle-right angle-class-{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}"></i>
                    {{$brand->name}}

                </a>
            </div>
        </div>
        <div id="pci-collapse{{@replace_space($product_class_id . '_brand_'.$brand->name.'_'.$i)}}" class="accordion-body collapse in">
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
                'products.name', 'variations.id as variation_id')->groupBy('products.id')->get();
                @endphp
                @foreach ($products as
                $product)
                <div class="accordion" id="{{$product->name}}" style="margin-left: 20px;">
                    <div class="accordion-group  pci_product_level level">
                        <div class="row">
                            <input id="product_selected{{$product->id}}" name="pci[product_selected][]" type="checkbox"
                                value="{{$product->id}}" @if(in_array($product->id, $product_selected)) checked @endif
                            class="pci-my-new-checkbox product_checkbox">
                            <div class="accordion-heading" style="width: 80%">
                                <a class="pci-accordion-toggle" data-toggle="collapse" data-id="{{$product->name}}"
                                    data-parent="#{{$product->name}}" href="#pci-collapse{{$product->name}}">
                                    <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                        alt="photo" width="50" height="50">
                                    {{$product->name}}
                                </a>
                            </div>
                        </div>
                        <div id="pci-collapse{{$product->name}}" class="accordion-body collapse in">
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
