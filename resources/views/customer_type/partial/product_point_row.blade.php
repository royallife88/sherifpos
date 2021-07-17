<tr class="row_{{$row_id}}">
    <td>{!! Form::text('product_point['.$row_id.'][point]', !empty($data->point) ? $data->point : null, ['class' => 'form-control']) !!}</td>
    <td> {!! Form::select('product_point['.$row_id.'][product_id]', $products, !empty($data->product_id) ? $data->product_id : false, ['class' => 'selectpicker
        form-control product_id_'. $row_id, 'data-live-search' => "true", 'placeholder' => __('lang.please_select'), 'data-row_id' => $row_id]) !!}</td>
    <td> <button type="button" class="btn btn-danger btn-xs remove_row mt-2"><i class="dripicons-cross"></i></button> <button type="button" class="btn btn-success btn-xs add_row_point mt-2"><i class="dripicons-plus"></i></button></td>
</tr>
