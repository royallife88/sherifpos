<tr>
    <td>
        @if(!empty(!empty($consumption_product)))
        <input type="hidden" name="consumption_details[{{$row_id}}][id]" value="{{$consumption_product->id}}">
        @endif
        {!! Form::select('consumption_details['.$row_id.'][variation_id]', $products,
        !empty($consumption_product) ? $consumption_product->variation_id : false, ['class' => 'selectpicker
        form-control
        variation_id', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td>
        {!! Form::text('consumption_details['.$row_id.'][amount_used]', !empty($consumption_product) ?
        @num_format($consumption_product->amount_used) : 0, ['class' => 'form-control']) !!}
    </td>
    <td>
        <p class="hide info_text text-red"></p>
        <div class="col-md-6">
            <label for="" class="unit_label">{{!empty($consumption_product->unit) ? $consumption_product->unit->name : false}}</label>
        </div>
        {!! Form::select('consumption_details['.$row_id.'][unit_id]', $units_all,
        !empty($consumption_product) ? $consumption_product->unit_id : false, ['class' => 'selectpicker form-control hide
        unit_id', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    <td><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-times"></i></button></td>
</tr>
