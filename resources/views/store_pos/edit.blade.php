<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('StorePosController@update', $store_pos->id), 'method' => 'put', 'id' => 'store_pos_edit_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit_pos_for_store' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                {!! Form::select('store_id', $stores,
                $store_pos->store_id, ['class' => 'selectpicker form-control',
                'data-live-search'=>"true", 'required',
                'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', $store_pos->name, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('user_id', __('lang.user'). ':*', []) !!}
                {!! Form::select('user_id', $users,
                $store_pos->user_id, ['class' => 'selectpicker form-control',
                'data-live-search'=>"true", 'required',
                'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
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
    $('.selectpicker').selectpicker('render');
</script>
