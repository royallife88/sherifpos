<div class="row text-center">
    <div class="col-md-12">
        <h4>@lang('lang.payment_details')</h4>
    </div>

</div>
<div class="col-md-12">

    <table class="table">
        <thead>
            <tr>
                <th>@lang('lang.amount')</th>
                <th>@lang('lang.payment_date')</th>
                <th>@lang('lang.payment_type')</th>
                <th>@lang('lang.bank_name')</th>
                <th>@lang('lang.ref_number')</th>
                <th>@lang('lang.bank_deposit_date')</th>
            </tr>
        </thead>

        @foreach ($payments as $payment)
        <tr>
            <td>{{@num_format($payment->amount)}}</td>
            <td>{{@format_date($payment->paid_on)}}</td>
            <td>{{$payment_type_array[$payment->method]}}</td>
            <td>{{$payment->ref_number}}</td>
            <td>@if(!empty($payment->bank_deposit_date)){{@format_date($payment->bank_deposit_date)}} @endif</td>
        </tr>
        @endforeach
    </table>
</div>
