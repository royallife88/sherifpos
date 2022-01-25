<tr>
    <input type="hidden" name="this_row_id" class="this_row_id" value="{{$row_id}}">
    <td>
        {!! Form::select('consumption_raw_materials[raw_material_id]', $raw_materials,
        !empty($consumption) ? $consumption->raw_material_id : request()->raw_material_id, ['class' => 'selectpicker
        form-control
        raw_material_id', 'data-live-search'=>"true", 'placeholder' => __('lang.please_select')]) !!}
    </td>
    @if(!empty($consumption))
    @include('consumption.partial.consumption_details_row', ['consumption_products' =>
    $consumption->consumption_details, 'raw_material_details' => $raw_material_details, 'edit' => 1])
    @else
    <td></td>
    <td></td>
    <td></td>
    @endif
    {{-- <td><button type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-times"></i></button></td>
    --}}
</tr>
