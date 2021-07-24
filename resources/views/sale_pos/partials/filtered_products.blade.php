@forelse ($products->chunk(5) as $chunk)
<tr>
    @foreach ($chunk as $product)
    <td class="product-img sound-btn filter_product_add" data-qty_available="{{$product->qty_available}}" data-product_id="{{$product->id}}" data-variation_id="{{$product->variation_id}}" title="{{$product->name}}"
        data-product="{{$product->name . ' (' . $product->variation_name . ')'}}">
        <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('images/default.jpg')}}@endif"
            width="100%" />
        <p>{{$product->name}} <br> <span>{{$product->sub_sku}}</span></p>
    </td>
    @endforeach
</tr>
@empty
<tr class="text-center">
    <td colspan="5">@lang('lang.no_item_found')</td>
</tr>
@endforelse
