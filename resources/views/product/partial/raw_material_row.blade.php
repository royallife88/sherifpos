<tr>
    <td>
        @if(!empty(!empty($consumption_product)))
        <input type="hidden" name="consumption_details[{{$row_id}}][id]" value="{{$consumption_product->id}}">
        @endif
        {!! Form::select('consumption_details['.$row_id.'][raw_material_id]', $raw_materials,
        !empty($consumption_product) ? $consumption_product->raw_material_id : false, ['class' => 'selectpicker
        form-control
        raw_material_id', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>
        {!! Form::text('consumption_details['.$row_id.'][amount_used]', !empty($consumption_product) ?
        @num_format($consumption_product->amount_used) : 0, ['class' => 'form-control raw_material_quantity']) !!}
    </td>
    <td>
        <p class="hide info_text text-red"></p>
        <div class="col-md-6">
            <label for="" class="unit_label">{{!empty($consumption_product->unit) ? $consumption_product->unit->name : false}}</label>
        </div>
        {!! Form::select('consumption_details['.$row_id.'][unit_id]', $raw_material_units,
        !empty($consumption_product) ? $consumption_product->unit_id : false, ['class' => 'selectpicker form-control hide
        raw_material_unit_id', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td> <label for="" class="cost_label"></label></td>
    <td><button type="button" class="btn btn-xs btn-danger remove_row remove_raw_material_btn"><i class="fa fa-times"></i></button></td>
    @if(!empty(!empty($consumption_product)))
    @php
        $this_raw_material = App\Models\Product::find($consumption_product->raw_material_id);
    @endphp
    <input type="hidden" name="raw_material_price" class="raw_material_price" value="{{$this_raw_material->purchase_price}}">
    @else
    <input type="hidden" name="raw_material_price" class="raw_material_price" value="0">
    @endif
</tr>
