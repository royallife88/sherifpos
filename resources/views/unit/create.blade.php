<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('UnitController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_unit_form' : 'unit_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_unit' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required' ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('unit_code', __( 'lang.unit_code' ) . ':*') !!}
                {!! Form::text('unit_code', null, ['class' => 'form-control', 'placeholder' => __( 'lang.unit_code' ), 'required' ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('base_unit', __( 'lang.base_unit' ) . ':') !!}
                {!! Form::select('base_unit', $units,
                false, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('operator', __( 'lang.operator' ) . ':') !!}
                {!! Form::select('operator', ['/' => '/', '*' => '*'],
                false, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('operation_value', __( 'lang.operation_value' )) !!}
                {!! Form::text('operation_value', null, ['class' => 'form-control', 'placeholder' => __( 'lang.operation_value' ) ]);
                !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
