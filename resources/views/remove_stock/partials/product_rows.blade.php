@php
$i = 0;
@endphp
@foreach ($add_stocks as $add_stock)
    @forelse ($add_stock->add_stock_lines as $line)
        @if (!empty($line->product))
            <tr>
                {!! Form::hidden('row_index', $i, ['class' => 'row_index']) !!}
                {!! Form::hidden('product_ids[' . $i . ']', $line->product->id, ['class' => 'product_id']) !!}
                {!! Form::hidden('variation_id[' . $i . ']', $line->variation_id, ['class' => 'variation_id']) !!}
                {!! Form::hidden('purchase_price[' . $i . ']', $line->product->purchase_price, ['class' => 'purchase_price']) !!}
                {!! Form::hidden('transaction_id[' . $i . ']', $add_stock->id, ['class' => 'transaction_id']) !!}
                <td>
                    {!! Form::checkbox('product_selected[]', $i, false, ['class' => 'product_checkbox']) !!}
                </td>
                <td><img src="@if (!empty($line->product->getFirstMediaUrl('product'))) {{ $line->product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                        alt="photo" width="50" height="50"></td>
                <td>
                    {{ $line->product->name }}

                    @if (!empty($line->variation))
                        @if ($line->variation->name != 'Default')
                            )
                            <b>{{ $line->variation->name }}</b>
                        @endif
                    @endif
                </td>
                <td>
                    @if (!empty($line->variation))
                        @if ($line->variation->name != 'Default')
                            {{ $line->variation->sub_sku }}
                        @else
                            {{ $line->product->sku ?? '' }}
                        @endif
                    @else
                        {{ $line->product->sku ?? '' }}
                    @endif
                </td>
                <td>
                    @if (!empty($line->product->product_class))
                        {{ $line->product->product_class->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->product->category))
                        {{ $line->product->category->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->product->sub_category))
                        {{ $line->product->sub_category->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->variation->color))
                        {{ $line->variation->color->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->variation->size))
                        {{ $line->variation->size->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->variation->grade))
                        {{ $line->variation->grade->name }}
                    @endif
                </td>

                <td>
                    @if (!empty($line->variation->unit))
                        {{ $line->variation->unit->name }}
                    @endif
                </td>
                @php
                    $query = App\Models\ProductStore::where('product_id', $line->product_id)->where('variation_id', $line->variation_id);
                    if (!empty($store_id)) {
                        $query->where('store_id', $store_id);
                    }
                    $current_stock_query = $query->first();
                    $current_stock = 0;

                    if (!empty($current_stock_query)) {
                        $current_stock = $current_stock_query->qty_available;
                    }
                @endphp
                <td>
                    @if (isset($current_stock))
                        {{ @num_format($current_stock) }}@else{{ 0 }}
                    @endif
                </td>
                <td>{{ $add_stock->supplier->name }}</td>
                <td style="width: 100px !important"><input type="text" class="form-control email"
                        name="remove_stock_lines[{{ $i }}][email]" id=""
                        value="{{ $add_stock->supplier->email }}"></td>
                <td>{{ $add_stock->invoice_no }}</td>
                <td>{{ @format_date($add_stock->transaction_date) }}</td>
                <td>
                    @if (!empty($payment_status_array[$add_stock->payment_status]))
                        <a data-href="{{ action('TransactionPaymentController@show', $add_stock->id) }}"
                            data-container=".view_modal"
                            class="btn btn-modal">{{ $payment_status_array[$add_stock->payment_status] }}</a>
                    @endif
                </td>
                <td><input type="text" class="form-control notes" name="remove_stock_lines[{{ $i }}][notes]"
                        id="" value="">
                </td>
                <td>
                    @if (isset($line->quantity))
                        {{ @num_format($line->quantity) }}@else{{ 0 }}
                    @endif
                </td>
                <td>
                    <input type="text" class="form-control quantity" min=1
                        max="@if (isset($line->quantity)) {{ $line->quantity }}@else{{ 0 }} @endif"
                        name="remove_stock_lines[{{ $i }}][quantity]" required value="0">
                    <span class="error stock_error hide">@lang('lang.quantity_should_not_greater_than')
                        @if (isset($line->quantity))
                            {{ @num_format($line->quantity) }}
                        @endif
                    </span>
                    <input type="hidden" class="form-control sub_total" min=1
                        name="remove_stock_lines[{{ $i }}][sub_total]" required value="0">
                    <input type="hidden" class="form-control purchase_price" min=1
                        name="remove_stock_lines[{{ $i }}][purchase_price]" required
                        value="@if (isset($line->purchase_price)) {{ @num_format($line->purchase_price) }}@else{{ 0 }} @endif">
                </td>
                @can('product_module.purchase_price.view')
                    <td>
                        @if (isset($line->purchase_price))
                            {{ @num_format($line->purchase_price) }}@else{{ 0 }}
                        @endif
                    </td>
                @endcan
                <td>
                    @if (isset($line->product->sell_price))
                        {{ @num_format($line->product->sell_price) }}@else{{ 0 }}
                    @endif
                </td>

            </tr>
        @endif
        @php
            $i++;
        @endphp
    @empty
    @endforelse
@endforeach
