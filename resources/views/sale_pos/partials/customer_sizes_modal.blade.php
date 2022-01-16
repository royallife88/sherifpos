<div class="modal fade" tabindex="-1" role="dialog" id="customer_sizes_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('lang.customer_sizes')</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i
                            class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('customer_size_id', __('lang.customer_size') . ':' ) !!}
                            {!! Form::select('customer_size_id', [], false, ['class' => 'form-control selectpicker'])
                            !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('fabric_name', __('lang.fabric_name') . ':' ) !!}
                            {!! Form::text('fabric_name', null, ['class' => 'form-control', 'id' =>
                            'fabric_name', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('fabric_squatch', __('lang.fabric_squatch') . ':' ) !!}
                            {!! Form::text('fabric_squatch', null, ['class' => 'form-control', 'id' => 'fabric_squatch',
                            'required']);
                            !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('prova_datetime', __('lang.prova') . ':' ) !!}
                            <input type="datetime-local" id="prova_datetime" name="prova_datetime"
                                value="{{date('Y-m-d\TH:i')}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('delivery_datetime', __('lang.delivery') . ':' ) !!}
                            <input type="datetime-local" id="delivery_datetime" name="delivery_datetime"
                                value="{{date('Y-m-d\TH:i')}}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="customer_size_submit">@lang('lang.submit')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
