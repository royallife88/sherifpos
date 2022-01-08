<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('RemoveStockController@postUpdateStatusAsCompensated', $transaction->id),
        'method'
        => 'post', 'id' =>
        'update_status_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.compensated' ) ({{$transaction->invoice_no}})</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                    <b>{{$transaction->supplier->name}}</b>
                </div>
                <div class="col-md-4">
                    {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$transaction->supplier->email}}</b>
                </div>
                <div class="col-md-4">
                    {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                    <b>{{$transaction->supplier->mobile_number}}</b>
                </div>
                <div class="col-md-4">
                    {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$transaction->supplier->address}}</b>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('compensated_at', __('lang.date'). ':*', []) !!}
                    {!! Form::text('compensated_at', date('Y-m-d'), ['class' => 'form-control  datepicker', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('compensated_invoice_no', __('lang.invoice_no'). ':*', []) !!}
                    {!! Form::text('compensated_invoice_no', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('compensated_value', __('lang.value'), []) !!}
                    {!! Form::text('compensated_value', 0, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="update-status">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker()
</script>
