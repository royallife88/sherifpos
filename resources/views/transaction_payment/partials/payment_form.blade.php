<div class="row">
    @if(!empty($payment))
    <input type="hidden" name="transaction_payment_id" value="{{$payment->id}}">
    @endif

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('amount', __('lang.amount'). ':*', []) !!} <br>
            {!! Form::text('amount', !empty($payment) ? @num_format($payment->amount) : null, ['class' => 'form-control', 'placeholder'
            => __('lang.amount')]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('method', __('lang.payment_type'). ':*', []) !!}
            {!! Form::select('method', $payment_type_array,
            !empty($payment) ? $payment->method : null, ['class' => 'selectpicker form-control',
            'data-live-search'=>"true", 'required',
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('paid_on', __('lang.payment_date'). ':', []) !!} <br>
            {!! Form::date('paid_on', !empty($payment) ? \Carbon\Carbon::parse($payment->paid_on)->format('Y-m-d') : null, ['class' => 'form-control',
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
            {!! Form::text('ref_number', !empty($payment) ? $payment->ref_number : null, ['class' => 'form-control not_cash',
            'placeholder' => __('lang.ref_number')]) !!}
        </div>
    </div>
    <div class="col-md-4 not_cash_fields hide">
        <div class="form-group">
            {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date'). ':', []) !!} <br>
            {!! Form::date('bank_deposit_date', null, ['class' => 'form-control not_cash',

            'placeholder' => __('lang.bank_deposit_date')]) !!}
        </div>
    </div>
    <div class="col-md-4 not_cash_fields hide">
        <div class="form-group">
            {!! Form::label('bank_name', __('lang.bank_name'). ':', []) !!} <br>
            {!! Form::text('bank_name', !empty($payment) ? $payment->bank_name : null, ['class' => 'form-control not_cash',
            'placeholder' => __('lang.bank_name')]) !!}
        </div>
    </div>
</div>