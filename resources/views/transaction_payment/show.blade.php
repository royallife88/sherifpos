<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('TransactionPaymentController@store'), 'method' => 'post', 'add_payment_form' ])
        !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.view_payments' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">

           @include('transaction_payment.partials.payment_table', ['payments' => $transaction->transaction_payments, 'show_action' => 'yes'])
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

