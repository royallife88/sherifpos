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
            <input type="hidden" name="is_raw_material_unit"
                value="@if(!empty($is_raw_material_unit)){{1}}@else{{0}}@endif">
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            {{-- @if(!empty($is_raw_material_unit)) --}}
            <div class="form-group">
                {!! Form::label('info', __( 'lang.info' ). ':') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __( 'lang.info' ),
                'rows' => 3 ]);
                !!}
            </div>
            {{-- <div class="form-group">
                {!! Form::label('base_unit_multiplier', __( 'lang.times_of' ). ':') !!}
                {!! Form::text('base_unit_multiplier', null, ['class' => 'form-control', 'placeholder' => __(
                'lang.times_of' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('base_unit_id', __( 'lang.base_unit' ). ':') !!}
                {!! Form::select('base_unit_id', $units, false, ['class' => 'form-control selectpicker', 'placeholder'
                => __('lang.select_base_unit'), 'data-live-search' => 'true']) !!}
            </div> --}}
            {{-- @endif --}}

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.selectpicker').selectpicker('render');
</script>
