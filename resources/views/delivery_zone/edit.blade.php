<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('DeliveryZoneController@update', $delivery_zone->id), 'method' => 'put', 'id' => 'delivery_zone_add_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('lang.name') . ':*') !!}
                {!! Form::text('name', $delivery_zone->name, ['class' => 'form-control', 'placeholder' => __('lang.name'), 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('coverage_area', __('lang.coverage_area')) !!}
                {!! Form::text('coverage_area', $delivery_zone->coverage_area, ['class' => 'form-control', 'placeholder' => __('lang.coverage_area')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('deliveryman_id', __('lang.deliveryman')) !!}
                {!! Form::select('deliveryman_id', $deliverymen, $delivery_zone->deliveryman_id, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('cost', __('lang.cost') . ':*') !!}
                {!! Form::text('cost', @num_format($delivery_zone->cost), ['class' => 'form-control', 'placeholder' => __('lang.cost'), 'required']) !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('refresh');
</script>
