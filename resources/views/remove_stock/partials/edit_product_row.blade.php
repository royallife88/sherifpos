@forelse ($remove_stock->remove_stock_lines as $product)
<tr>
    <td><img src="@if(!empty($product->product->getFirstMediaUrl('product'))){{$product->product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
            alt="photo" width="50" height="50"></td>
    <td>
        {{$product->product->name}}

        @if($product->variation->name != "Default")
        <b>{{$product->variation->name}}</b>
        @endif
        <input type="hidden" name="remove_stock_lines[{{$loop->index}}][remove_stock_line_id]" value="{{$product->id}}">
        <input type="hidden" name="remove_stock_lines[{{$loop->index}}][product_id]" value="{{$product->product_id}}">
        <input type="hidden" name="remove_stock_lines[{{$loop->index}}][purchase_price]" value="{{$product->purchase_price}}">
        <input type="hidden" name="remove_stock_lines[{{$loop->index}}][variation_id]"
            value="{{$product->variation_id}}">
    </td>
    <td>
        {{$product->variation->sub_sku}}
    </td>
    <td>
        @if(!empty($product->product->product_class)) {{$product->product->product_class->name}} @endif
    </td>

    <td>
        @if(!empty($product->product->category)) {{$product->product->category->name}} @endif
    </td>

    <td>
        @if(!empty($product->product->sub_category)) {{$product->product->sub_category->name}} @endif
    </td>

    <td>
        @if(!empty($product->variation->color)) {{$product->variation->color->name}} @endif
    </td>

    <td>
        @if(!empty($product->variation->size)) {{$product->variation->size->name}} @endif
    </td>

    <td>
        @if(!empty($product->variation->grade)) {{$product->variation->grade->name}} @endif
    </td>

    <td>
        @if(!empty($product->variation->unit)) {{$product->variation->unit->name}} @endif
    </td>
    <td>
        <input type="text" class="form-control quantity" min=1 name="remove_stock_lines[{{$loop->index}}][quantity]"
            required value="{{$product->quantity}}">
        <input type="hidden" class="form-control sub_total" min=1 name="remove_stock_lines[{{$loop->index}}][sub_total]"
            required value="{{$product->sub_total}}">
        <input type="hidden" class="form-control purchase_price" min=1 name="remove_stock_lines[{{$loop->index}}][purchase_price]"
            required value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
    </td>
    <td>
        @if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif
    </td>
    <td>
        @if(isset($product->variation->default_sell_price)){{@num_format($product->variation->default_sell_price)}}@else{{0}}@endif
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sx remove_row"><i class="fa fa-times"></i></button>
    </td>
</tr>
@empty

@endforelse
