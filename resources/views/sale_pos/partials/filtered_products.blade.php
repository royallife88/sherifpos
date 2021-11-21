@forelse ($products->chunk(4) as $chunk)
<tr>
    @foreach ($chunk as $product)
    <td class="product-img sound-btn filter_product_add" data-is_service="{{$product->is_service}}" data-qty_available="{{$product->qty_available-$product->block_qty}}" data-product_id="{{$product->id}}" data-variation_id="{{$product->variation_id}}" title="{{$product->name}}"
        data-product="{{$product->name . ' (' . $product->variation_name . ')'}}">
        <img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
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
