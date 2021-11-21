@foreach ($products as $product)
<tr>
    <td><img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
            alt="photo" width="50" height="50"></td>
    <td>{{$product->name}}</td>
    <td>{{$product->sku}}</td>
    <td>{{@num_format($product->purchase_price)}}</td>
    <td>{{@num_format($product->sell_price)}}</td>
    <td>{{@num_format($product->current_stock)}}</td>
    <td>@if(!empty($product->expiry_date)){{@format_date($product->expiry_date)}}@endif</td>
    <td>@if(!empty($product->date_of_purchase)){{@format_date($product->date_of_purchase)}}@endif</td>

    <td class="qty_hide @if ($type != 'package_promotion') hide @endif"><input type="text" class="form-control"
            name="qty[{{$product->id}}]" id="" value="0"></td>
    <td><button type="button" class="btn btn-xs btn-danger text-white remove_row" data-product_id="{{$product->id}}"><i class="fa fa-times"></i></button></td>

</tr>
@endforeach
