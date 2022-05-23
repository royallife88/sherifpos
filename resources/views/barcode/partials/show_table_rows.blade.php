@forelse ($products as $product)
    <tr>
        <td>


            @if ($product->variation_name != 'Default')
                {{ $product->variation_name }}
            @else
                {{ $product->product_name }}
            @endif
            <input type="hidden" name="products[{{ $loop->index + $index }}][product_id]"
                value="{{ $product->product_id }}">
            <input type="hidden" name="products[{{ $loop->index + $index }}][variation_id]"
                value="{{ $product->variation_id }}">
        </td>
        <td>
            {{ $product->sub_sku }}
        </td>
        <td>
            <input type="number" class="form-control" min=1 name="products[{{ $loop->index + $index }}][quantity]"
                value="1">
        </td>
        <td>
            <button type="button" class="btn btn-danger text-white remove_row"><i class="fa fa-times"></i></button>
        </td>
    </tr>
@empty
@endforelse
