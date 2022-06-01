<div class="row">
    @if (!empty($payment))
        <input type="hidden" name="transaction_payment_id" value="{{ $payment->id }}">
    @endif

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('amount', __('lang.amount') . ':*', []) !!} <br>
            {!! Form::text('amount', !empty($payment) ? @num_format($payment->amount) : null, ['class' => 'form-control', 'placeholder' => __('lang.amount')]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('method', __('lang.payment_type') . ':*', []) !!}
            {!! Form::select('method', $payment_type_array, !empty($payment) ? $payment->method : 'cash', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
        </div>
    </div>

    <div class="col-md-4 @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
        <div class="form-group">
            {!! Form::label('paid_on', __('lang.accounting_period') . ':', []) !!} <br>
            <input type="datetime-local" name="paid_on"
                value="@if (!empty(!empty($payment))){{ Carbon\Carbon::parse($payment->paid_on)->format('Y-m-d\TH:i') }}@else{{ date('Y-m-d\TH:i') }}@endif"
                class="form-control payment_date">
            <input type="hidden" name="cash_register_id" class="cash_register_id" value="">
            {{-- {!! Form::text('paid_on', !empty($payment) ? @format_datetime($payment->paid_on) : @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'placeholder' => __('lang.payment_date')]) !!} --}}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('upload_documents', __('lang.upload_documents') . ':', []) !!} <br>
            {!! Form::file('upload_documents[]', null, ['class' => '']) !!}
        </div>
    </div>
    <div class="col-md-4 not_cash_fields hide">
        <div class="form-group">
            {!! Form::label('ref_number', __('lang.ref_number') . ':', []) !!} <br>
            {!! Form::text('ref_number', !empty($payment) ? $payment->ref_number : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.ref_number')]) !!}
        </div>
    </div>
    <div class="col-md-4 not_cash_fields hide">
        <div class="form-group">
            {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date') . ':', []) !!} <br>
            {!! Form::text('bank_deposit_date', null, ['class' => 'form-control not_cash datepicker', 'placeholder' => __('lang.bank_deposit_date')]) !!}
        </div>
    </div>
    <div class="col-md-4 not_cash_fields hide">
        <div class="form-group">
            {!! Form::label('bank_name', __('lang.bank_name') . ':', []) !!} <br>
            {!! Form::text('bank_name', !empty($payment) ? $payment->bank_name : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.bank_name')]) !!}
        </div>
    </div>
    <div class="col-md-4 card_field hide">
        <label>@lang('lang.card_number') *</label>
        <input type="text" name="card_number" class="form-control">
    </div>
    <div class="col-md-2 card_field hide">
        <label>@lang('lang.month')</label>
        <input type="text" name="card_month" class="form-control">
    </div>
    <div class="col-md-2 card_field hide">
        <label>@lang('lang.year')</label>
        <input type="text" name="card_year" class="form-control">
    </div>

</div>
