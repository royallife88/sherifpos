<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CategoryController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_category_form' : 'category_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_category' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.class_name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.class_name' ), 'required' ]);
                !!}
            </div>
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            <div class="form-group">
                {!! Form::label('image', __( 'lang.image' ) . ':') !!}
                {!! Form::file('image', ['class' => 'form-control' ]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('parent_id', __( 'lang.parent_category' ) . ':') !!}
                {!! Form::select('parent_id', $categories,
                false, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
