@foreach ($products as $product)
<tr>
    <td><img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
            alt="photo" width="50" height="50"></td>
    <td>{{$product->name}}</td>
    <td>{{$product->sku}}</td>
    <td>{{@num_format($product->purchase_price)}}</td>
    <td>{{@num_format($product->sell_price)}}</td>
    <td>@if($product->is_service){{'-'}}@else{{@num_format($product->current_stock)}}@endif</td>
    <td>@if(!empty($product->expiry_date)){{@format_date($product->expiry_date)}}@endif</td>
    <td>@if(!empty($product->date_of_purchase)){{@format_date($product->date_of_purchase)}}@endif</td>

    <td class="qty_hide @if ($type != 'package_promotion') hide @endif"><input type="text" class="qty form-control"
            name="package_promotion_qty[{{$product->id}}]" id=""
            value="@if(!empty($package_promotion_qty) && array_key_exists($product->id, $package_promotion_qty)){{$package_promotion_qty[$product->id]}}@else{{'1'}}@endif">
    </td>
    <td><button type="button" class="btn btn-xs btn-danger text-white remove_row_sp"
            data-product_id="{{$product->id}}"><i class="fa fa-times"></i></button></td>
    <input type="hidden" class="purchase_price" name="purchase_price" value="{{$product->purchase_price}}">
    <input type="hidden" class="sell_price" name="sell_price" value="{{$product->sell_price}}">
</tr>
@endforeach
<input type="hidden" name="sell_price_total" id="sell_price_total" value="{{$products->sum('sell_price')}}">
<input type="hidden" name="purchase_price_total" id="purchase_price_total" value="{{$products->sum('purchase_price')}}">
