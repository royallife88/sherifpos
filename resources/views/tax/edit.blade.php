<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('TaxController@update', $tax->id), 'method' => 'put', 'id' => 'tax_add_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.edit')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('lang.name') . ':*') !!}
                {!! Form::text('name', $tax->name, ['class' => 'form-control', 'placeholder' => __('lang.name'), 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('rate', __('lang.rate_percentage') . ':*') !!}
                {!! Form::text('rate', $tax->rate, ['class' => 'form-control', 'placeholder' => __('lang.rate'), 'required']) !!}
            </div>
            <input type="hidden" name="type" value="{{ $tax->type }}">
            @if ($tax->type == 'general_tax')
                <div class="form-group">
                    {!! Form::label('tax_method', __('lang.tax_method') . ':*') !!}
                    {!! Form::select('tax_method', ['inclusive' => __('lang.inclusive'), 'exclusive' => __('lang.exclusive')], $tax->tax_method, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                </div>
                <div class="col-md-4">
                    <div class="i-checks">
                        <input id="status" name="status" type="checkbox" value="1"
                            @if ($tax->status) checked @endif class="form-control-custom">
                        <label for="status">
                            <strong>
                                @lang('lang.active')
                            </strong>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('store_ids', __('lang.stores') . ':', []) !!}  <i class="dripicons-question" data-toggle="tooltip" title="@lang('lang.tax_status_info')"></i>
                    {!! Form::select('store_ids[]', $stores, $tax->store_ids ?? [], ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'data-actions-box' => 'true', 'multiple']) !!}
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('lang.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('refresh');
</script>
