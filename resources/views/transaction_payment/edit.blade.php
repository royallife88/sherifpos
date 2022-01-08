<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('TransactionPaymentController@update', $payment->id), 'method' => 'put', 'add_payment_form' ])
        !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit_payment' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">

            @include('transaction_payment.partials.payment_form', ['payment' => $payment])
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render');
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    });
    $('#method').change(function(){
        var method = $(this).val();

        if(method === 'cash'){
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
        }else{
            $('.not_cash_fields').removeClass('hide');
            $('.not_cash').attr('required', true);
        }
    })
</script>
