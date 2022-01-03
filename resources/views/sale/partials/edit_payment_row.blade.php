<div class="payment_row  row pl-3  pr-3">
    <input type="hidden" name="payments[{{$index}}][transaction_payment_id]" value="{{!empty($payment)?$payment->id:''}}">
    <div class="col-md-6 mt-1">
        <label>@lang('lang.received_amount'): *</label>
        <input type="text" name="payments[{{$index}}][amount]" class="form-control numkey received_amount" required
            step="any" value="@if(!empty($payment)){{@num_format($payment->amount)}}@endif">
    </div>
    <div class="col-md-6 mt-1">
        <label>@lang('lang.payment_method'):</label>
        {!! Form::select('payments['.$index.'][method]', $payment_types, !empty($payment)?$payment->method:null, ['class' => 'form-control method',
        'required']) !!}
    </div>
    <div class="col-md-6 mt-1">
        {{-- <label class="change_text">@lang('lang.change') : </label> --}}
        {{-- <span class="change" class="ml-2">0.00</span> --}}
        <input type="hidden" name="payments[{{$index}}][change_amount]" class="change_amount" value="{{!empty($payment)?$payment->change_amount:''}}">
    </div>
    <div class="form-group col-md-12 mt-3 hide card_field">
        <div class="row">
            <div class="col-md-4">
                <label>@lang('lang.card_number') *</label>
                <input type="text" name="payments[{{$index}}][card_number]" class="form-control"  value="{{!empty($payment)?$payment->card_number:''}}">
            </div>
            <div class="col-md-2">
                <label>@lang('lang.month')</label>
                <input type="text" name="payments[{{$index}}][card_month]" class="form-control"  value="{{!empty($payment)?$payment->card_month:''}}">
            </div>
            <div class="col-md-2">
                <label>@lang('lang.year')</label>
                <input type="text" name="payments[{{$index}}][card_year]" class="form-control"  value="{{!empty($payment)?$payment->card_year:''}}">
            </div>
        </div>
    </div>

    <div class="form-group col-md-12 bank_field hide">
        <label>@lang('lang.bank_name')</label>
        <input type="text" name="payments[{{$index}}][bank_name]" class="form-control"  value="{{!empty($payment)?$payment->bank_name:''}}">
    </div>
    <div class="form-group col-md-12 card_bank_field hide">
        <label>@lang('lang.ref_number') </label>
        <input type="text" name="payments[{{$index}}][ref_number]" class="form-control"  value="{{!empty($payment)?$payment->ref_number:''}}">
    </div>
    <div class="form-group col-md-12 cheque_field hide">
        <label>@lang('lang.cheque_number')</label>
        <input type="text" name="payments[{{$index}}][cheque_number]" class="form-control"  value="{{!empty($payment)?$payment->cheque_number:''}}">
    </div>
</div>
<hr>
