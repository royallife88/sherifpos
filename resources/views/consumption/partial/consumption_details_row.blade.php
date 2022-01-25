<td colspan="3">
    <table style="border: 0px; width: 100%;">
        <tr>
            <input type="hidden" name="consumption_raw_materials[current_stock]" value="{{$current_stock}}">
            <td colspan="2"><label for="">@lang('lang.current_stock'): {{@num_format($current_stock)}}</label></td>
        </tr>
        @foreach ($raw_material_details as $raw_material_detail)
        <tr>
            @if(!empty($raw_material_detail->batch_number))
            <td><label for="">@lang('lang.batch_number'):
                    {{$raw_material_detail->batch_number}}</label></td>
            @endif
            @if(!empty($raw_material_detail->expiry_date))
            <td><label for="">@lang('lang.expiry_date'):
                    @if(!empty($raw_material_detail->expiry_date)){{@format_date($raw_material_detail->expiry_date)}}@endif</label>
            </td>
            @endif
        </tr>
        @endforeach
    </table>
    <table style="border: 0px; width: 100%;">
        @foreach ($consumption_products as $consumption_product)
        <tr>
            @if(!empty($edit))
            <input type="hidden" name="consumption_details[{{$loop->index}}][id]" value="{{$consumption_product->id}}">
            @endif
            <td style="width: 33%;">
                <input type="hidden" name="consumption_details[{{$loop->index}}][variation_id]"
                    value="{{$consumption_product->variation_id}}"> {{$consumption_product->variation->product->name ??
                ''}}
            </td>
            @if(!empty($edit))
            <td style="width: 33%;">
                {!! Form::text('consumption_details['.$loop->index.'][quantity]',
                !empty($consumption_product) ?
                @num_format($consumption_product->quantity) : 0, ['class' => 'form-control']) !!}
            </td>
            @else
            <td style="width: 33%;">
                {!! Form::text('consumption_details['.$loop->index.'][quantity]',
                !empty($consumption_product) ?
                @num_format($consumption_product->amount_used) : 0, ['class' => 'form-control']) !!}
            </td>
            @endif
            <td style="width: 33%;">
                <input type="hidden" name="consumption_details[{{$loop->index}}][unit_id]"
                    value="{{$consumption_product->unit_id}}"> {{$consumption_product->unit->name ?? ''}}
            </td>
        </tr>
        @endforeach
    </table>

</td>
