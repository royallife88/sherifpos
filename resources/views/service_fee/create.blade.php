<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('ServiceFeeController@store'), 'method' => 'post', 'id' => $quick_add ? 'quick_add_service_fee_form' : 'service_fee_add_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_service_fee' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('lang.name') . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('lang.name'), 'required']) !!}
            </div>
            <input type="hidden" name="quick_add" value="{{ $quick_add }}">
            <div class="form-group">
                {!! Form::label('rate', __('lang.rate') . ':*') !!}
                {!! Form::text('rate', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang.rate')]) !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
