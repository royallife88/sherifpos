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

            <p>@lang('lang.address', [], $invoice_lang): <br> {{$transaction->store->name}}
                {{$transaction->store->location}}</p>
            <p>@lang('lang.phone_number', [], $invoice_lang): <br> {{$transaction->store->phone_number}} </p>

        </div>
        <p>@lang('lang.date', [], $invoice_lang): {{$transaction->created_at}}<br>
            @lang('lang.reference', [], $invoice_lang): {{$transaction->invoice_no}}<br>
            @lang('lang.customer', [], $invoice_lang):
            @if(!empty($transaction->customer)){{$transaction->customer->name}}@endif
        </p>
        <div class="table_div" style=" padding: 0 7px; width:100%; height:100%;">
            <table style="margin: 0 auto;">
                <tbody>

                    @foreach($transaction->transaction_sell_lines as $line)
                    <tr>
                        <td colspan="2">
                            {{$line->product->name}}
                            @if(!empty($line->variation))
                            @if($line->variation->name != "Default")
                            <b>{{$line->variation->name}}</b>
                            @endif
                            @endif
                            {{-- <br>{{@num_format($line->quantity)}} x {{@num_format($line->sell_price)}}
                            @if(!empty((float)$line->product_discount_amount))-{{@num_format($line->product_discount_amount)}}@endif--}}

                        </td>
                        <td style="text-align:center;vertical-align:bottom">{{@num_format($line->sub_total)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">@lang('lang.total', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->grand_total)}}</th>
                    </tr>
                    @if($transaction->total_tax != 0)
                    <tr>
                        <th colspan="2">@lang('lang.order_tax', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->total_tax)}}</th>
                    </tr>
                    @endif
                    @if($transaction->discount_amount != 0)
                    <tr>
                        <th colspan="2">@lang('lang.order_discount', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->discount_amount)}}</th>
                    </tr>
                    @endif
                    @if($transaction->transaction_sell_lines->sum('coupon_discount'))
                    <tr>
                        <th colspan="2">@lang('lang.coupon_discount', [], $invoice_lang)</th>
                        <th style="text-align:right">
                            {{@num_format($transaction->transaction_sell_lines->sum('coupon_discount'))}}</th>
                    </tr>
                    @endif
                    @if(!empty($transaction->delivery_cost))
                    <tr>
                        <th colspan="2">@lang('lang.delivery_cost' , [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->delivery_cost)}}
                        </th>
                    </tr>
                    @endif
                    @if(!empty($transaction->rp_redeemed_value))
                    <tr>
                        <th colspan="2">@lang('lang.redeemed_point_value', [], $invoice_lang)</th>
                        <th style="text-align:right">{{@num_format($transaction->rp_redeemed_value)}}</th>
                    </tr>
                    @endif
                    <tr>
                        <th colspan="2">@lang('lang.grand_total', [], $invoice_lang)</th>
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
                    <tr>
                        <td colspan="2">@lang('lang.paid_by', [], $invoice_lang):</td>
                    </tr>
                    @foreach($transaction->transaction_payments as $payment_data)
                    <tr style="background-color:#ddd;">
                        <td style="padding: 5px;width:30%">
                            @if(!empty($payment_data->method)){{$payment_types[$payment_data->method]}}@endif</td>
                        <td style="padding: 5px;width:40%">{{@num_format($payment_data->amount)}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="centered" colspan="3">@lang('lang.thank_you_and_come_again', [], $invoice_lang)
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
                            <br>
                            <img style="margin-top:10px;"
                                src="data:image/png;base64,{{DNS2D::getBarcodePNG($transaction->invoice_no, 'QRCODE')}}"
                                alt="barcode" />
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
