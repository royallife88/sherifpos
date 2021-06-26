<div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['url' => action('ProductClassController@store'), 'method' => 'post', 'id' => $quick_add ? 'quick_add_product_class_form' : 'product_class_add_form' ]) !!}

      <div class="modal-header">

          <h4 class="modal-title">@lang( 'lang.add_class' )</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('name', __( 'lang.class_name' ) . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control',  'placeholder' => __( 'lang.class_name' ), 'required' ]); !!}
        </div>
        <input type="hidden" name="quick_add" value="{{$quick_add }}">
        <div class="form-group">
          {!! Form::label('description', __( 'lang.description' ) . ':') !!}
            {!! Form::text('description', null, ['class' => 'form-control','placeholder' => __( 'lang.description' )]); !!}
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
      </div>

      {!! Form::close() !!}

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
