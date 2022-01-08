@forelse ($products as $product)
@php
    $i = $index;
@endphp
<tr>
    <td>
        {{$product->product_name}}

        @if($product->variation_name != "Default")
        <b>{{$product->variation_name}}</b>
        @endif
        <input type="hidden" name="add_stock_lines[{{$i}}][product_id]" class="product_id"
            value="{{$product->product_id}}">
        <input type="hidden" name="add_stock_lines[{{$i}}][variation_id]" class="variation_id"
            value="{{$product->variation_id}}">
    </td>
    <td>
        {{$product->sub_sku}}
    </td>
    <td>
        <input type="text" class="form-control quantity" min=0 max="{{$product->qty_available}}"
            name="add_stock_lines[{{$i}}][quantity]" required
            value="@if(isset($product->qty_available)){{$product->qty_available}}@else{{0}}@endif">
    </td>
    <td>
        <input type="text" class="form-control purchase_price"
            name="add_stock_lines[{{$i}}][purchase_price]" required
            value="@if(isset($product->default_purchase_price)){{@num_format($product->default_purchase_price)}}@else{{0}}@endif">
    </td>
    <td>
        <span class="sub_total_span"></span>
        <input type="hidden" class="form-control sub_total" name="add_stock_lines[{{$i}}][sub_total]"
            value="">
    </td>
    <td>
        <button style="margin-top: 33px;" type="button" class="btn btn-danger btn-sx remove_row"
            data-index="{{$i}}"><i class="fa fa-times"></i></button>
    </td>
</tr>
@empty

@endforelse
<script>
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    })
</script>
