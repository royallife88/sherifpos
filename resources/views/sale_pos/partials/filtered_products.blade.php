@forelse ($products->chunk(5) as $chunk)
    <tr style="font-size: 11px; padding: 5px;">
        @foreach ($chunk as $product)
            <td style="padding-top: 0px; padding-bottom: 0px;" class="product-img sound-btn filter_product_add"
                data-is_service="{{ $product->is_service }}"
                data-qty_available="{{ $product->qty_available - $product->block_qty }}"
                data-product_id="{{ $product->id }}" data-variation_id="{{ $product->variation_id }}"
                title="{{ $product->name }}"
                data-product="{{ $product->name . ' (' . $product->variation_name . ')' }}">
                <img src="@if (!empty($product->getFirstMediaUrl('product'))) {{ $product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                    width="100%" />
                <p><span
                        style="font-size:12px !important; font-weight: bold; color: black;">{{ $product->name }}</span>
                    <br> <span style="color: black; font-weight: bold;">{{ $product->sub_sku }}</span> <br>
                    @if (!empty($currency))
                        <span
                            style="font-size:12px !important; font-weight: bold; color: black;">{{ @num_format($product->sell_price / $exchange_rate) . ' ' . $currency->symbol }}
                        </span>
                    @else
                        <span
                            style="font-size:12px !important; font-weight: bold; color: black;">{{ @num_format($product->sell_price / $exchange_rate) . ' ' . session('currency.symbol') }}
                        </span>
                    @endif
                </p>
            </td>
        @endforeach
    </tr>
@empty
    <tr class="text-center">
        <td colspan="5">@lang('lang.no_item_found')</td>
    </tr>
@endforelse
