<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('ExchangeRateController@store'), 'method' => 'post', 'id' => 'exchange_rate_add_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_new_rate' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('store_id', __('lang.store') . ':*') !!}
                        {!! Form::select('store_id', $stores, !empty($default_store) ? $default_store->id : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'store_id', 'placeholder' => __('lang.please_select'), 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('received_currency_id', __('lang.received_currency') . ':*') !!}
                        {!! Form::select('received_currency_id', $currencies_excl, false, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'received_currency_id', 'placeholder' => __('lang.please_select'), 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('conversion_rate', __('lang.enter_the_rate') . ':*') !!}
                        {!! Form::text('conversion_rate', null, ['class' => 'form-control', 'placeholder' => __('lang.enter_the_rate'), 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('default_currency_id', __('lang.default_currency') . ':*') !!}
                        {!! Form::select('default_currency_id', $currencies_all, $default_currency->id, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'default_currency_id', 'placeholder' => __('lang.please_select'), 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('expiry_date', __('lang.expiry_date')) !!}
                        {!! Form::date('expiry_date', null, ['class' => 'form-control', 'placeholder' => __('lang.expiry_date')]) !!}
                    </div>
                </div>
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
    $('.selectpicker').selectpicker('refresh');
</script>
