<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('StorePosController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_store_form' : 'store_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_pos_for_store' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                {!! Form::select('store_id', $stores,
                null, ['class' => 'selectpicker form-control',
                'data-live-search'=>"true", 'required',
                'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('user_id', __('lang.user'). ':*', []) !!}
                {!! Form::select('user_id', $users,
                null, ['class' => 'selectpicker form-control',
                'data-live-search'=>"true", 'required',
                'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>

            <input type="hidden" name="quick_add" value="{{$quick_add }}">
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
