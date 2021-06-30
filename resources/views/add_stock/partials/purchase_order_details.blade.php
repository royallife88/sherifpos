@forelse ($purchase_order->purchase_order_lines as $product)
<tr>
    <td>
        {{$product->product->name}}

        @if($product->variation->name != "Default")
        <b>{{$product->variation->name}}</b>
        @endif
        <input type="hidden" name="add_stock_lines[{{$loop->index}}][purchase_order_line_id]"
            value="{{$product->id}}">
        <input type="hidden" name="add_stock_lines[{{$loop->index}}][product_id]" value="{{$product->product_id}}">
        <input type="hidden" name="add_stock_lines[{{$loop->index}}][variation_id]"
            value="{{$product->variation_id}}">
    </td>
    <td>
        {{$product->variation->sub_sku}}
    </td>
    <td>
        <input type="text" class="form-control quantity" min=1 name="add_stock_lines[{{$loop->index}}][quantity]"
            required value="@if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif">
    </td>
    <td>
        <input type="text" class="form-control purchase_price"
            name="add_stock_lines[{{$loop->index}}][purchase_price]" required
            value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
    </td>
    <td>
        <span class="sub_total_span"></span>
        <input type="hidden" class="form-control sub_total" name="add_stock_lines[{{$loop->index}}][sub_total]"
            value="">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sx remove_row"><i class="fa fa-times"></i></button>
    </td>
</tr>
@empty

@endforelse
