<style>
    @media print {
        * {
            font-size: 12px;
            line-height: 20px;
            font-family: 'Times New Roman';
        }

        td,
        th {
            padding: 5px 0;
        }

        .hidden-print {
            display: none !important;
        }

        @page {
            margin: 0;
        }

        body {
            margin: 0.5cm;
            margin-bottom: 1.6cm;
        }
    }

    #receipt_section * {
        font-size: 14px;
        line-height: 24px;
        font-family: 'Ubuntu', sans-serif;
        text-transform: capitalize;
        color: black !important;
    }

    #receipt_section .btn {
        padding: 7px 10px;
        text-decoration: none;
        border: none;
        display: block;
        text-align: center;
        margin: 7px;
        cursor: pointer;
    }

    #receipt_section .btn-info {
        background-color: #999;
        color: #FFF;
    }

    #receipt_section .btn-primary {
        background-color: #6449e7;
        color: #FFF;
        width: 100%;
    }

    #receipt_section td,
    #receipt_section th,
    #receipt_section tr,
    #receipt_section table {
        border-collapse: collapse;
    }

    #receipt_section tr {
        border-bottom: 1px dotted #ddd;
    }

    #receipt_section td,
    #receipt_section th {
        padding: 7px 0;
        width: 50%;
    }

    #receipt_section table {
        width: 100%;
    }

    #receipt_section tfoot tr th:first-child {
        text-align: left;
    }

    .centered {
        text-align: center;
        align-content: center;
    }

    small {
        font-size: 11px;
    }
</style>
@php
if(empty($invoice_lang)){
$invoice_lang = request()->session()->get('language');
}
@endphp
<div style="max-width:350px;margin:0 auto; padding: 0 15x; color: black !important;">

    <div id="receipt-data">
        <div class="centered">
            @include('layouts.partials.print_header')

            <p>{{$transaction->store->name}}
                {{$transaction->store->location}}</p>
            <p>{{$transaction->store->phone_number}} </p>

        </div>
        <div style="width: 70%; float:left;">
            <p>@lang('lang.date', [], $invoice_lang): {{$transaction->transaction_date}}<br>
                @lang('lang.reference', [], $invoice_lang): {{$transaction->invoice_no}}<br>
                @if(!empty($transaction->customer) && $transaction->customer->is_default == 0)
                @lang('lang.customer', [], $invoice_lang): {{$transaction->customer->name}} <br>
                @lang('lang.address', [], $invoice_lang): {{$transaction->customer->address}} <br>
                @lang('lang.mobile_number', [], $invoice_lang): {{$transaction->customer->mobile_number}} <br>
                @endif
            </p>
        </div>
        @if(session('system_mode') == 'restaurant')
        <div style="width: 30%; float:right; text-align:center;">
            <p
                style="width: 75px; height:75px; border: 4px solid #111; border-radius: 50%; padding: 20px; font-size: 23px; font-weight: bold;">
                {{$transaction->ticket_number}}</p>
        </div>
        @endif
        <div class="table_div" style=" padding: 0 7px; width:100%; height:100%;">
            <table style="margin: 0 auto;">
                <thead>
                    <tr>
                        <th style="width: 40%; padding: 0 50px !important;">@lang('lang.item', [], $invoice_lang) </th>
                        <th style="width: 20%;text-align:center !important;"> @lang('lang.price', [], $invoice_lang)
                        </th>
                        <th style="width: 20%">@lang('lang.qty', [], $invoice_lang) </th>
                        <th style="width: 20%">@lang('lang.amount', [], $invoice_lang) </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($transaction->transaction_sell_lines as $line)
                    <tr>
                        <td>
                            {{$line->product->name}}
                            @if(!empty($line->variation))
                            @if($line->variation->name != "Default")
                            <b>{{$line->variation->name}}</b>
                            @endif
                            @endif
                        </td>
                        <td style="text-align:center !important;vertical-align:bottom">
                            {{@num_format($line->sell_price)}}</td>
                        <td style="text-align:center;vertical-align:bottom">{{@num_format($line->quantity)}}</td>
                        <td style="text-align:center;vertical-align:bottom">{{@num_format($line->sub_total)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">@lang('lang.total', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->grand_total)}}</th>
                    </tr>
                    @if($transaction->total_tax != 0)
                    <tr>
                        <th colspan="3">@lang('lang.order_tax', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->total_tax)}}</th>
                    </tr>
                    @endif
                    @if($transaction->discount_amount != 0)
                    <tr>
                        <th colspan="3">@lang('lang.order_discount', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->discount_amount)}}</th>
                    </tr>
                    @endif
                    @if($transaction->total_sp_discount != 0)
                    <tr>
                        <th colspan="3">@lang('lang.sales_promotion', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->total_sp_discount)}}</th>
                    </tr>
                    @endif
                    @if($transaction->transaction_sell_lines->sum('coupon_discount'))
                    <tr>
                        <th colspan="3">@lang('lang.coupon_discount', [], $invoice_lang)</th>
                        <th style="text-align:right">
                            {{@num_format($transaction->transaction_sell_lines->sum('coupon_discount'))}}</th>
                    </tr>
                    @endif
                    @if(!empty($transaction->delivery_cost) && $transaction->delivery_cost != 0)
                    <tr>
                        <th colspan="3">@lang('lang.delivery_cost' , [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->delivery_cost)}}
                        </th>
                    </tr>
                    @endif
                    @if(!empty($transaction->rp_redeemed_value))
                    <tr>
                        <th colspan="3">@lang('lang.redeemed_point_value', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->rp_redeemed_value)}}</th>
                    </tr>
                    @endif
                    <tr>
                        <th colspan="3">@lang('lang.grand_total', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->final_total)}}</th>
                    </tr>
                    <tr>

                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="padding: 0 7px;">
            <table>
                <tbody>
                    @foreach($transaction->transaction_payments as $payment_data)
                    <tr style="background-color:#ddd;">
                        <td style="padding: 5px;width:30%">
                            @if(!empty($payment_data->method)){{$payment_types[$payment_data->method]}}@endif</td>
                        <td style="padding: 5px;width:40%; text-align: right;" colspan="2">
                            {{@num_format($payment_data->amount + $payment_data->change_amount)}}</td>
                    </tr>
                    @if(!empty($payment_data->change_amount) && $payment_data->change_amount > 0 && $payment_data->method != 'deposit')
                    <tr>
                        <td style="padding: 5px;width:30%">@lang('lang.change')</td>
                        <td colspan="2" style="padding: 5px;width:40%; text-align: right;">
                            {{@num_format($payment_data->change_amount)}}</td>
                    </tr>
                    @endif
                    @endforeach
                    @if($transaction->payment_status != 'paid')
                    <tr>
                        <td style="padding: 5px;width:30%">@lang('lang.due')</td>
                        <td colspan="2" style="padding: 5px;width:40%; text-align: right;">
                            {{@num_format($transaction->final_total -
                            $transaction->transaction_payments->sum('amount'))}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="centered" colspan="3">
                            @if(session('system_mode') == 'restaurant')
                            @lang('lang.enjoy_your_meal_please_come_again', [], $invoice_lang)
                            @else
                            @lang('lang.thank_you_and_come_again', [], $invoice_lang)
                            @endif
                        </td>
                    </tr>
                    @if(!empty($transaction->terms_and_conditions))
                    <tr>
                        <td>{!!$transaction->terms_and_conditions->description!!}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="centered" colspan="3">
                            <img style="margin-top:10px;"
                                src="data:image/png;base64,{{DNS1D::getBarcodePNG($transaction->invoice_no, 'C128')}}"
                                width="300" alt="barcode" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @include('layouts.partials.print_footer')
        <div style="width: 100%; text-align: center;">
            <p><span class="">Proudly Developed at <a style="text-decoration: none;" target="_blank"
                        href="http://sherifshalaby.tech">sherifshalaby.tech</a></span></p>
        </div>
    </div>
</div>
