<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('TransactionPaymentController@store'), 'method' => 'post', 'add_payment_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_payment' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="transaction_id" value="{{$transaction_id }}">

           <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('amount', __('lang.amount'). ':*', []) !!} <br>
                    {!! Form::text('amount', @num_format($transaction->final_total - $transaction->transaction_payments->sum('amount')), ['class' => 'form-control', 'placeholder'
                    => __('lang.amount')]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('method', __('lang.payment_type'). ':*', []) !!}
                    {!! Form::select('method', $payment_type_array,
                    null, ['class' => 'selectpicker form-control',
                    'data-live-search'=>"true", 'required',
                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('paid_on', __('lang.payment_date'). ':', []) !!} <br>
                    {!! Form::text('paid_on', null, ['class' => 'form-control datepicker', 'readonly',
                    'placeholder' => __('lang.payment_date')]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('upload_documents', __('lang.upload_documents'). ':', []) !!} <br>
                    {!! Form::file('upload_documents[]', null, ['class' => '']) !!}
                </div>
            </div>
            <div class="col-md-4 not_cash_fields hide">
                <div class="form-group">
                    {!! Form::label('ref_number', __('lang.ref_number'). ':', []) !!} <br>
                    {!! Form::text('ref_number', null, ['class' => 'form-control not_cash',
                    'placeholder' => __('lang.ref_number')]) !!}
                </div>
            </div>
            <div class="col-md-4 not_cash_fields hide">
                <div class="form-group">
                    {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date'). ':', []) !!} <br>
                    {!! Form::text('bank_deposit_date', null, ['class' => 'form-control not_cash datepicker',
                    'readonly',
                    'placeholder' => __('lang.bank_deposit_date')]) !!}
                </div>
            </div>
            <div class="col-md-4 not_cash_fields hide">
                <div class="form-group">
                    {!! Form::label('bank_name', __('lang.bank_name'). ':', []) !!} <br>
                    {!! Form::text('bank_name', null, ['class' => 'form-control not_cash',
                    'placeholder' => __('lang.bank_name')]) !!}
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
    $('.datepicker').datepicker();
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
