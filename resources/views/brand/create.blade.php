<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('BrandController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_brand_form' : 'brand_add_form', 'files' => true ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_brand' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            @include('layouts.partials.image_crop')
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('#brand_category_id').selectpicker('render');

    $('.view_modal').on('shown.bs.modal', function () {
        let  brand_category_id = $('#sub_category_id').val();
        if(brand_category_id){
            $("#brand_category_id").selectpicker("val", brand_category_id);
        }else{
            let  brand_category_id = $('#category_id').val();
            $("#brand_category_id").selectpicker("val", brand_category_id);
        }
    })
</script>
