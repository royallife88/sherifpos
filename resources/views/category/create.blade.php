<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CategoryController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_category_form' : 'category_add_form', 'files' => true ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_category' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required' ]);
                !!}
            </div>
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            <div class="form-group">
                {!! Form::label('image', __( 'lang.image' ) . ':') !!} <br>
                {!! Form::file('image', ['class' => '' ]) !!}
            </div>
            @if($type == 'category')
            <div class="form-group hide">
                {!! Form::label('product_class_id', __( 'lang.class' ) . ':') !!}
                {!! Form::select('product_class_id', $product_classes,
                false, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select'), 'required', 'id' => 'cat_product_class_id']) !!}
            </div>
            @endif
            @if($type == 'sub_category')
            <div class="form-group hide">
                {!! Form::label('parent_id', __( 'lang.parent_category' ) . ':') !!}
                {!! Form::select('parent_id', $categories,
                false, ['class' => 'form-control', 'data-live-search'=>"true",
                'style' =>'width: 100%' , 'placeholder' => __('lang.please_select'), 'id' => 'parent_id']) !!}
            </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('#cat_product_class_id').selectpicker('render');
    $('#parent_id').selectpicker('render');

    @if($type == 'category')
    $('.view_modal').on('shown.bs.modal', function () {
        $("#cat_product_class_id").selectpicker("val", $('#product_class_id').val());
    })
    @endif
    @if($type == 'sub_category')
    $('.view_modal').on('shown.bs.modal', function () {
        $("#parent_id").selectpicker("val", $('#category_id').val());
    })
    @endif
</script>
