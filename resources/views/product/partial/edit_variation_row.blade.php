<tr class="row_{{$row_id}}" data-row_id="{{$row_id}}">
    @if(!empty($item))
    {!! Form::hidden('variations['.$row_id.'][id]', !empty($item) ? $item->id : null, ['class' => 'form-control'])
    !!}
    @endif
    <td>{!! Form::text('variations['.$row_id.'][name]', !empty($item) ? $item->name : null, ['class' =>
        'form-control'])
        !!}</td>
    <td>{!! Form::text('variations['.$row_id.'][sub_sku]', !empty($item) ? $item->sub_sku : null, ['class' =>
        'form-control']) !!}</td>
    <td>{!! Form::select('variations['.$row_id.'][color_id]', $colors, !empty($item) ? $item->color_id: false,
        ['class'
        => 'form-control selectpicker', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>{!! Form::select('variations['.$row_id.'][size_id]', $sizes, !empty($item) ? $item->size_id: false, ['class'
        =>
        'form-control selectpicker', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>{!! Form::select('variations['.$row_id.'][grade_id]', $grades, !empty($item) ? $item->grade_id: false,
        ['class'
        => 'form-control selectpicker', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>{!! Form::select('variations['.$row_id.'][unit_id]', $units, !empty($item) ? $item->unit_id: false, ['class'
        =>
        'form-control selectpicker', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>{!! Form::text('variations['.$row_id.'][default_purchase_price]', !empty($item) ?
        @num_format($item->default_purchase_price) :
        null, ['class' => 'form-control default_purchase_price', 'required']) !!}</td>
    <td>{!! Form::text('variations['.$row_id.'][default_sell_price]', !empty($item) ? @num_format($item->default_sell_price) :
        null,
        ['class' => 'form-control default_sell_price', 'required']) !!}</td>
    <td> <button type="button" class="btn btn-danger btn-xs remove_row mt-2"><i class="dripicons-cross"></i></button>
    </td>
</tr>
<tr class="variant_store_checkbox_{{$row_id}}">
    <td colspan="9">
        <input name="variant_different_prices_for_stores" type="checkbox" value="1" data-row_id="{{$row_id}}"
        class="variant_different_prices_for_stores"><strong>@lang('lang.variant_different_prices_for_stores')</strong>
    </td>
</tr>

@foreach ($stores as $store)
<tr class="variant_store_prices_{{$row_id}}" style="display: none;">
    <td>
        {{$store->name}}
    </td>
    @php
    $variant_store = null;
    if(!empty($item)){
    $variant_store = $item->product_stores->where('store_id', $store->id)->first();
    }
    @endphp
    <td colspan="4">
        @if(!empty($variant_store ))
        <input type="hidden" class="form-control" name="variations[{{$row_id}}][variant_stores][{{$store->id}}][id]"
            value="{{$variant_store->id}}">
        @endif

        <input type="hidden" class="form-control"
            name="variations[{{$row_id}}][variant_stores][{{$store->id}}][store_id]" value="{{$store->id}}">
        <input type="text" class="form-control store_prices" style="width: 200px;"
            name="variations[{{$row_id}}][variant_stores][{{$store->id}}][price]"
            value="@if(!empty($variant_store)){{@num_format($variant_store->price)}}@endif">

    </td>
</tr>
@endforeach
