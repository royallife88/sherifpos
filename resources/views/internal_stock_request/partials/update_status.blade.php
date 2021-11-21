<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('InternalStockRequestController@postUpdateStatus', $transaction->id), 'method'
        => 'post', 'id' =>
        'update_status' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.update_status' ) ({{$transaction->invoice_no}})</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('status', __('lang.status'). ':*', []) !!}
                        {!! Form::select('status', ['received' => __('lang.received'), 'approved' =>
                        __('lang.approved'), 'pending' => __('lang.pending')],
                        $transaction->status, ['class' => 'selectpicker form-control',
                        'data-live-search'=>"true", 'required',
                        'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker()
</script>
