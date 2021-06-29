@forelse ($products as $product)
<tr>
    <td style="width: 30%">
        {{$product->product_name}}

        @if($product->variation_name != "Default")
        <b>{{$product->variation_name}} {{$product->sub_sku}}</b>
        @endif
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][product_id]"
            value="{{$product->product_id}}">
        <input type="hidden" name="transaction_sell_line[{{$loop->index + $index}}][variation_id]"
            value="{{$product->variation_id}}">
    </td>
    <td style="width: 20%">
        <div class="input-group"><span class="input-group-btn">
                <button type="button" class="btn btn-danger minus">
                    <span class="dripicons-minus"></span>
                </button>
            </span>
            <input type="text" class="form-control quantity  qty numkey input-number" min=1
                name="transaction_sell_line[{{$loop->index + $index}}][quantity]" required
                value="@if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif">
            <span class="input-group-btn">
                <button type="button" class="btn btn-success plus">
                    <span class="dripicons-plus"></span>
                </button>
            </span>
        </div>

    </td>
    <td style="width: 20%">
        <input type="text" class="form-control sell_price"
            name="transaction_sell_line[{{$loop->index + $index}}][sell_price]" required
            value="@if(isset($product->default_sell_price)){{@num_format($product->default_sell_price)}}@else{{0}}@endif">
    </td>
    <td style="width: 10%">
        <span class="sub_total_span"></span>
        <input type="hidden" class="form-control sub_total"
            name="transaction_sell_line[{{$loop->index + $index}}][sub_total]" value="">
    </td>
    <td style="width: 20%">
        <button type="button" class="btn btn-danger btn-sx remove_row"><i class="fa fa-times"></i></button>
        <button type="button" class="btn btn-danger btn-sx quick_add_purchase_order" title="@lang('lang.add_draft_purchase_order')" data-href="{{action('PurchaseOrderController@quickAddDraft')}}?variation_id={{$product->variation_id}}&product_id={{$product->product_id}}" ><i class="fa fa-plus"></i> @lang('lang.po')</button>
    </td>
</tr>
@empty

@endforelse
