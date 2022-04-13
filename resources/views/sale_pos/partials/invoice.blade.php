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
        #header_invoice_img{
            max-width: 80mm;
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
<div style="width:350px;margin:0; padding: 0 15x; color: black !important;">

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
                {{$transaction->customer->name}} <br>
                {{$transaction->customer->address}} <br>
                {{$transaction->customer->mobile_number}} <br>
                @endif
                @if(!empty($transaction->sale_note))
                @lang('lang.sale_note', [], $invoice_lang): {{$transaction->sale_note}} <br>
                @endif
            </p>
            @if(session('system_mode') == 'garments')
            <p>
                @if(!empty($transaction->customer_size))@lang('lang.customer_size'):
                {{$transaction->customer_size->name}} <br>@endif
                @if(!empty($transaction->fabric_name))@lang('lang.fabric_name'): {{$transaction->fabric_name}} <br>
                @endif
                @if(!empty($transaction->fabric_squatch))@lang('lang.fabric_squatch'): {{$transaction->fabric_squatch}}
                <br> @endif
                @if(!empty($transaction->prova_datetime))@lang('lang.prova'):
                {{@format_datetime($transaction->prova_datetime)}} <br> @endif
                @if(!empty($transaction->delivery_datetime))@lang('lang.delivery'):
                {{@format_datetime($transaction->delivery_datetime)}} <br>@endif

            </p>
            @endif
            @if(session('system_mode') == 'restaurant')
                @if(!empty($transaction->dining_room))@lang('lang.dining_room'):
                {{$transaction->dining_room->name}} <br>
                @endif
                @if(!empty($transaction->dining_table))@lang('lang.dining_table'):
                {{$transaction->dining_table->name}} <br>
                @endif
            @endif
            @if(!empty($transaction->deliveryman))
            <p>{{$transaction->deliveryman->employee_name}}</p>
            @endif
        </div>
        @if(session('system_mode') == 'restaurant')
        <div style="width: 30%; float:right; text-align:center;">
            <p
                style="width: 75px; height:75px; border: 4px solid #111; border-radius: 50%; padding: 20px; font-size: 23px; font-weight: bold;">
                {{$transaction->ticket_number}}</p>
        </div>
        @endif
        <div class="table_div" style=" padding: 0 7px; width:100%; height:100%;">
            <table style="margin: 0 auto; text-align: center !important">
                <thead>
                    <tr>
                        <th style="width: 30%; padding: 0 50px !important;">@lang('lang.item', [], $invoice_lang) </th>
                        @if(empty($print_gift_invoice))
                        <th style="width: 20%; text-align:center !important;"> @lang('lang.price', [], $invoice_lang)
                        </th>
                        @endif
                        <th style="width: 20%; text-algin: center;">@lang('lang.qty', [], $invoice_lang) </th>
                        @if(empty($print_gift_invoice))
                        <th style="width: 30%; text-algin: center;">@lang('lang.amount', [], $invoice_lang) </th>
                        @endif
                    </tr>
                </thead>
                <tbody>

                    @foreach($transaction->transaction_sell_lines as $line)
                    <tr>
                        <td style="width: 30%; text-algin: right !important;">
                            {{$line->product->name}}
                            @if(!empty($line->variation))
                            @if($line->variation->name != "Default")
                            <b>{{$line->variation->name}}</b>
                            @endif
                            @endif
                        </td>
                        @if(empty($print_gift_invoice))
                        <td style="text-align:center !important;vertical-align:bottom; width: 20%;">
                            {{@num_format($line->sell_price)}}</td>
                        @endif
                        <td style="text-align:center;vertical-align:bottom; width: 20%;">
                            {{@num_format($line->quantity)}}</td>
                        @if(empty($print_gift_invoice))
                        <td style="text-align:center;vertical-align:bottom; width: 30%;">
                            {{@num_format($line->sub_total)}}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                @if(empty($print_gift_invoice))
                <tfoot>
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.total', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->grand_total)}}</th>
                    </tr>
                    @if($transaction->total_item_tax != 0)
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.tax', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->total_item_tax)}}
                        </th>
                    </tr>
                    @endif
                    @if($transaction->total_tax != 0)
                    <tr>
                        <th style="font-size: 16px;" colspan="3">{{$transaction->tax->name ?? ''}}</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->total_tax)}}</th>
                    </tr>
                    @endif
                    @if($transaction->discount_amount != 0)
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.order_discount', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->discount_amount)}}
                        </th>
                    </tr>
                    @endif
                    @if($transaction->total_sp_discount != 0)
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.sales_promotion', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->total_sp_discount)}}
                        </th>
                    </tr>
                    @endif
                    @if($transaction->transaction_sell_lines->sum('coupon_discount'))
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.coupon_discount', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">
                            {{@num_format($transaction->transaction_sell_lines->sum('coupon_discount'))}}</th>
                    </tr>
                    @endif
                    @if(!empty($transaction->delivery_cost) && $transaction->delivery_cost != 0)
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.delivery_cost' , [], $invoice_lang) @if(!empty($transaction->deliveryman->employee_name)) ({{$transaction->deliveryman->employee_name}}) @endif</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->delivery_cost)}}
                        </th>
                    </tr>
                    @endif
                    @if(!empty($transaction->rp_redeemed_value))
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.redeemed_point_value', [], $invoice_lang)
                        </th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->rp_redeemed_value)}}
                        </th>
                    </tr>
                    @endif
                    <tr>
                        <th style="font-size: 16px;" colspan="3">@lang('lang.grand_total', [], $invoice_lang)</th>
                        <th style="font-size: 16px; text-align:right;">{{@num_format($transaction->final_total)}}</th>
                    </tr>
                    <tr>

                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div style="padding: 0 7px;">
            <table>
                <tbody>
                    @if(empty($print_gift_invoice))
                    @foreach($transaction->transaction_payments as $payment_data)
                    @if($payment_data->method != 'deposit')
                    <tr style="background-color:#ddd;">
                        <td style="font-size: 16px; padding: 5px;width:30%">
                            @if(!empty($payment_data->method)){{__('lang.'. $payment_data->method, [],
                            $invoice_lang)}}@endif</td>
                        <td style="font-size: 16px; padding: 5px;width:40%; text-align: right;" colspan="2">
                            {{@num_format($payment_data->amount + $payment_data->change_amount)}}</td>
                    </tr>
                    @endif
                    @if(!empty($payment_data->change_amount) && $payment_data->change_amount > 0 &&
                    $payment_data->method != 'deposit')
                    <tr>
                        <td style="font-size: 16px; padding: 5px;width:30%">@lang('lang.change', [], $invoice_lang)</td>
                        <td colspan="2" style="font-size: 16px; padding: 5px;width:40%; text-align: right;">
                            {{@num_format($payment_data->change_amount)}}</td>
                    </tr>
                    @endif
                    @endforeach
                    @if(!empty($transaction->add_to_deposit) && $transaction->add_to_deposit > 0)
                    <tr>
                        <td style="font-size: 16px; padding: 7px;width:30%">@lang('lang.deposit', [], $invoice_lang)
                        </td>
                        <td colspan="2" style="font-size: 16px; padding: 7px;width:40%; text-align: right;">
                            {{@num_format($transaction->add_to_deposit)}}</td>
                    </tr>
                    @endif
                    @if(!empty($transaction->used_deposit_balance) && $transaction->used_deposit_balance > 0)
                    <tr>
                        <td style="font-size: 16px; padding: 7px;width:30%">@lang('lang.used_deposit_balance', [],
                            $invoice_lang)</td>
                        <td colspan="2" style="font-size: 16px; padding: 7px;width:40%; text-align: right;">
                            {{@num_format($transaction->used_deposit_balance)}}</td>
                    </tr>
                    @endif
                    @if($transaction->is_quotation != 1)
                    @if($transaction->payment_status != 'paid' && $transaction->final_total - $transaction->transaction_payments->sum('amount') > 0)
                    <tr>
                        <td style="font-size: 16px; padding: 5px;width:30%">@lang('lang.due', [], $invoice_lang)</td>
                        <td colspan="2" style="font-size: 16px; padding: 5px;width:40%; text-align: right;">
                            {{@num_format($transaction->final_total -
                            $transaction->transaction_payments->sum('amount'))}}</td>
                    </tr>
                    @endif
                    @endif
                    @endif <!-- end of print gift invoice -->
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
