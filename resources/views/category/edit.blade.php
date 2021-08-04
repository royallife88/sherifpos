<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CategoryController@update', $category->id), 'method' => 'put', 'id' => 'category_add_form', 'files' => true ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit_category' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', $category->name, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required' ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('image', __( 'lang.image' ) . ':') !!} <br>
                {!! Form::file('image', ['class' => '' ]) !!}
            </div>
            @if(empty($category->parent_id))
            <div class="form-group">
                {!! Form::label('product_class_id', __( 'lang.class' ) . ':') !!}
                {!! Form::select('product_class_id', $product_classes,
                $category->product_class_id, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            @endif
            @if(!empty($category->parent_id))
            <div class="form-group">
                {!! Form::label('parent_id', __( 'lang.parent_category' ) . ':') !!}
                {!! Form::select('parent_id', $categories,
                $category->parent_id, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select')]) !!}
            </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
