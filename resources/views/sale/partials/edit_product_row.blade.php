@forelse ($products as $product)
<tr class="product_row">
    <td style="width: 20%">
        {{$product->product->name}}

        @if($product->variation->name != "Default")
        <b>{{$product->variation->name}}</b>
        @endif
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][transaction_sell_line_id]"
            class="transaction_sell_line_id" value="{{$product->id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][product_id]" class="product_id"
            value="{{$product->product_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][variation_id]" class="variation_id"
            value="{{$product->variation_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][purchase_price]" class="purchase_price"
            value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][price_hidden]" class="price_hidden"
            value="@if(isset($product->variation->default_sell_price)){{@num_format($product->variation->default_sell_price)}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][tax_id]" class="tax_id"
            value="{{$product->tax_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][tax_rate]" class="tax_rate"
            value="{{@num_format($product->tax_rate)}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][item_tax]" class="item_tax"
            value="{{@num_format($product->item_tax)}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][coupon_discount]"
            class="coupon_discount_value" value="{{$product->coupon_discount_value}}">
        <!-- value is percentage or fixed value from coupon data -->
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][coupon_discount_type]"
            class="coupon_discount_type" value="{{$product->coupon_discount_type}}">
        <!-- value is percentage or fixed value from coupon data -->
        <input type="hidden" name="transaction_sell_line[{{$loop->index}}][coupon_discount_amount]"
            class="coupon_discount_amount" value="{{$product->coupon_discount_amount}}">
        <!-- after calculation actual discounted amount for row product row -->
    </td>
    <td style="width: 15%">
        <div class="input-group"><span class="input-group-btn">
                <button type="button" class="btn btn-danger minus">
                    <span class="dripicons-minus"></span>
                </button>
            </span>
            <input type="text" class="form-control quantity  qty numkey input-number" min=1 step="any"
                name="transaction_sell_line[{{$loop->index}}][quantity]" required
                value="@if(isset($product->quantity)){{@num_format($product->quantity)}}@else{{1}}@endif">
            <span class="input-group-btn">
                <button type="button" class="btn btn-success plus">
                    <span class="dripicons-plus"></span>
                </button>
            </span>
        </div>

    </td>
    <td style="width: 15%">
        <input type="text" class="form-control sell_price" name="transaction_sell_line[{{$loop->index}}][sell_price]"
            required value="@if(isset($product->sell_price)){{@num_format($product->sell_price)}}@else{{0}}@endif">
    </td>
    <td style="width: 15%">
        <input type="hidden" class="form-control product_discount_type"
            name="transaction_sell_line[{{$loop->index}}][product_discount_type]"
            value="@if(!empty($product->product_discount_type)){{$product->product_discount_type}}@else{{0}}@endif">
        <input type="hidden" class="form-control product_discount_value"
            name="transaction_sell_line[{{$loop->index}}][product_discount_value]"
            value="@if(!empty($product->product_discount_value)){{@num_format($product->product_discount_value)}}@else{{0}}@endif">
        <input type="text" class="form-control product_discount_amount"
            name="transaction_sell_line[{{$loop->index}}][product_discount_amount]" readonly
            value="@if(!empty($product->product_discount_amount)){{@num_format($product->product_discount_amount)}}@else{{0}}@endif">
    </td>
    <td style="width: 15%">
        <span class="sub_total_span">{{@num_format($product->sub_total)}}</span>
        <input type="hidden" class="form-control sub_total" name="transaction_sell_line[{{$loop->index}}][sub_total]"
            value="{{@num_format($product->sub_total)}}">
    </td>
    <td style="width: 15%">
        <button type="button" class="btn btn-danger btn-sx remove_row"><i class="fa fa-times"></i></button>
    </td>
</tr>
@empty

@endforelse
