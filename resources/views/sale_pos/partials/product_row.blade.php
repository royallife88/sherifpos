@forelse ($products as $product)
<tr class="product_row">
    <td style="width: 18%">
        {{$product->product_name}}

        @if($product->variation_name != "Default")
        <b>{{$product->variation_name}} {{$product->sub_sku}}</b>
        @endif
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][is_service]" class="is_service"
            value="{{$product->is_service}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][product_id]" class="product_id"
            value="{{$product->product_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][variation_id]" class="variation_id"
            value="{{$product->variation_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][price_hidden]" class="price_hidden"
            value="@if(isset($product->default_sell_price)){{@num_format($product->default_sell_price)}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][coupon_discount]"
            class="coupon_discount_value" value="0"> <!-- value is percentage or fixed value from coupon data -->
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][coupon_discount_type]"
            class="coupon_discount_type" value=""> <!-- value is percentage or fixed value from coupon data -->
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][coupon_discount_amount]"
            class="coupon_discount_amount" value="0">
        <!-- after calculation actual discounted amount for row product row -->
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][promotion_purchase_condition]"
            class="promotion_purchase_condition"
            value="@if(!empty($sale_promotion_details)){{$sale_promotion_details->purchase_condition}}@else{{0}}@endif">
        <input type="hidden"
            name="transaction_sell_line[{{$loop->index + $index}}][promotion_purchase_condition_amount]"
            class="promotion_purchase_condition_amount"
            value="@if(!empty($sale_promotion_details)){{$sale_promotion_details->purchase_condition_amount}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][promotion_discount]"
            class="promotion_discount_value"
            value="@if(!empty($sale_promotion_details)){{$sale_promotion_details->discount_value}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][promotion_discount_type]"
            class="promotion_discount_type"
            value="@if(!empty($sale_promotion_details)){{$sale_promotion_details->discount_type}}@else{{0}}@endif">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][promotion_discount_amount]"
            class="promotion_discount_amount" value="0">
    </td>
    <td style="width: 18%">
        <div class="input-group"><span class="input-group-btn">
                <button type="button" class="btn btn-danger minus">
                    <span class="dripicons-minus"></span>
                </button>
            </span>
            <input type="text" class="form-control quantity  qty numkey input-number" min=1
                @if(!$product->is_service)max="{{$product->qty_available}}"@endif
            name="transaction_sell_line[{{$loop->index + $index}}][quantity]"
            required
            value="@if(!empty($edit_quantity)){{$edit_quantity}}@else
            @if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif @endif">
            <span class="input-group-btn">
                <button type="button" class="btn btn-success plus">
                    <span class="dripicons-plus"></span>
                </button>
            </span>
        </div>

    </td>
    <td style="width: 16%">
        <input type="text" class="form-control sell_price"
            name="transaction_sell_line[{{$loop->index + $index}}][sell_price]" required
            value="@if(isset($product->default_sell_price)){{@num_format($product->default_sell_price)}}@else{{0}}@endif">
    </td>
    <td style="width: 13%">
        <input type="hidden" class="form-control product_discount_type"
            name="transaction_sell_line[{{$loop->index + $index}}][product_discount_type]"
            value="@if(!empty($product_discount_details->discount_type)){{$product_discount_details->discount_type}}@else{{0}}@endif">
        <input type="hidden" class="form-control product_discount_value"
            name="transaction_sell_line[{{$loop->index + $index}}][product_discount_value]"
            value="@if(!empty($product_discount_details->discount)){{@num_format($product_discount_details->discount)}}@else{{0}}@endif">
        <div class="input-group">
            <button type="button" class="btn btn-lg" id="search_button"><span class="plus_sign_text"></span></button>
            <input type="text" class="form-control product_discount_amount"
                name="transaction_sell_line[{{$loop->index + $index}}][product_discount_amount]" readonly
                value="@if(!empty($product_discount_details->discount)){{@num_format($product_discount_details->discount)}}@else{{0}}@endif">
        </div>
    </td>
    <td style="width: 10%">
        <span class="sub_total_span"></span>
        <input type="hidden" class="form-control sub_total"
            name="transaction_sell_line[{{$loop->index + $index}}][sub_total]" value="">
    </td>
    <td style="width: 10%">
        @if(isset($product->qty_available)){{@num_format($product->qty_available)}}@else{{0}}@endif
    </td>
    <td style="width: 10%">
        <button type="button" class="btn btn-danger btn-sx remove_row"><i class="fa fa-times"></i></button>
        <button type="button" class="btn btn-danger btn-sx quick_add_purchase_order"
            title="@lang('lang.add_draft_purchase_order')"
            data-href="{{action('PurchaseOrderController@quickAddDraft')}}?variation_id={{$product->variation_id}}&product_id={{$product->product_id}}"><i
                class="fa fa-plus"></i> @lang('lang.po')</button>
    </td>
</tr>
@empty

@endforelse
