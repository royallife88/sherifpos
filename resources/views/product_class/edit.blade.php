<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('ProductClassController@update', $product_class->id), 'method' => 'put', 'id' =>
        'product_class_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit_class' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.class_name' ) . ':*') !!}
                {!! Form::text('name', $product_class->name, ['class' => 'form-control', 'placeholder' => __(
                'lang.class_name' ), 'required' ]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', __( 'lang.description' ) . ':') !!}
                {!! Form::text('description', $product_class->description, ['class' => 'form-control','placeholder' =>
                __( 'lang.description' )]); !!}
            </div>
            @include('layouts.partials.image_crop', ['image_url' => $product_class->getFirstMediaUrl('product_class') ??
            null])
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
