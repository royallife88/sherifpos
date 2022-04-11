<table class="table">
    <tr>
        <td><b>@lang('lang.cash_in')</b></td>
        <td>{{ @num_format($cash_register->total_cash_in) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.cash_out')</b></td>
        <td>{{ @num_format($cash_register->total_cash_out) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.total_sales')</b></td>
        <td>{{ @num_format($cash_register->total_sale - $cash_register->total_refund) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.total_cash_sale')</b></td>
        <td>{{ @num_format($cash_register->total_cash_sales) }}</td>
    </tr>
    @if (session('system_mode') == 'restaurant')
        <tr>
            <td><b>@lang('lang.dining_in')</b></td>
            <td>{{ @num_format($cash_register->total_dining_in) }}</td>
        </tr>
    @endif
    <tr>
        <td><b>@lang('lang.total_card_sale')</b></td>
        <td>{{ @num_format($cash_register->total_card_sales) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.total_cheque_sale')</b></td>
        <td>{{ @num_format($cash_register->total_cheque_sales) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.total_bank_transfer_sale')</b></td>
        <td>{{ @num_format($cash_register->total_bank_transfer_sales) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.total_gift_card_sale')</b></td>
        <td>{{ @num_format($cash_register->total_gift_card_sales) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.return_sales')</b></td>
        <td>{{ @num_format($cash_register->total_sell_return) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.purchases')</b></td>
        <td>{{ @num_format($cash_register->total_purchases) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.expenses')</b></td>
        <td>{{ @num_format($cash_register->total_expenses) }}</td>
    </tr>
    <tr>
        <td><b>@lang('lang.current_cash')</b></td>
        <td>{{ @num_format($cash_register->total_cash_sales -$cash_register->total_refund_cash +$cash_register->total_cash_in -$cash_register->total_cash_out -$cash_register->total_purchases -$cash_register->total_expenses -$cash_register->total_sell_return) }}
        </td>
    </tr>
</table>
