<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('ProductClassController@update', $product_class->id), 'method' => 'put', 'id' => 'product_class_add_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('lang.name') . ':*') !!}
                <div class="input-group my-group">
                    {!! Form::text('name', $product_class->name, ['class' => 'form-control', 'placeholder' => __('lang.name'), 'required', 'readonly' => $product_class->name == 'Extras' ? true : false]) !!}
                    <span class="input-group-btn">
                        <button class="btn btn-default bg-white btn-flat translation_btn" type="button"
                            data-type="product_class"><i class="dripicons-web text-primary fa-lg"></i></button>
                    </span>
                </div>
            </div>
            @include('layouts.partials.translation_inputs', [
                'attribute' => 'name',
                'translations' => $product_class->translations,
                'type' => 'product_class',
            ])
            <div class="form-group">
                {!! Form::label('description', __('lang.description') . ':') !!}
                {!! Form::text('description', $product_class->description, ['class' => 'form-control', 'placeholder' => __('lang.description')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('sort', __('lang.sort') . ':*') !!}
                {!! Form::number('sort', $product_class->sort, ['class' => 'form-control', 'placeholder' => __('lang.sort'), 'required']) !!}
            </div>
            <div class="form-group">
                <div class="i-checks">
                    <input id="status" name="status" type="checkbox" @if ($product_class->status == 1) checked @endif
                        value="1" class="form-control-custom">
                    <label for="status"><strong>
                            @lang('lang.active')
                        </strong></label>
                </div>
            </div>
            @include('layouts.partials.image_crop', [
                'image_url' => $product_class->getFirstMediaUrl('product_class') ?? null,
            ])
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
