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

<div style="max-width:350px;margin:0 auto;color: black !important;">

    <div id="receipt-data">
        <div class="centered">
            @include('layouts.partials.print_header')

            <p>{{$transaction->store->name}} {{$transaction->store->location}}</p>
            <p>{{$transaction->store->phone_number}} </p>

        </div>
        <div style="width: 70%; float:left;">
            <p style="padding: 0 7px;">@lang('lang.date',[], 'en'): {{$transaction->transaction_date}}
                {{__('lang.date',[],
                'ar')}}<br>
                @lang('lang.reference',[], 'en'): {{$transaction->invoice_no}} {{__('lang.reference',[], 'ar')}}<br>
                @if(!empty($transaction->customer) && $transaction->customer->is_default == 0)
                {{$transaction->customer->name}}<br>
                {{$transaction->customer->address}}<br>
                {{$transaction->customer->mobile_number}}<br>
                @endif
                @if(!empty($transaction->sale_note))
                @lang('lang.sale_note', [], 'en'): {{$transaction->sale_note}} @lang('lang.address',[], 'ar') <br>
                @endif
            </p>
            @if(session('system_mode') == 'garments')
            <p>
                @if(!empty($transaction->customer_size))@lang('lang.customer_size',[], 'en'): {{$transaction->customer_size->name}} {{__('lang.customer_size',[], 'ar')}} <br>@endif
                @if(!empty($transaction->fabric_name))@lang('lang.fabric_name',[], 'en'): {{$transaction->fabric_name}} {{__('lang.fabric_name',[], 'ar')}} <br>@endif
                @if(!empty($transaction->fabric_squatch))@lang('lang.fabric_squatch',[], 'en'): {{$transaction->fabric_squatch}} {{__('lang.fabric_squatch',[], 'ar')}} <br>@endif
                @if(!empty($transaction->prova_datetime))@lang('lang.prova',[], 'en'): {{@format_datetime($transaction->prova_datetime)}} {{__('lang.prova',[], 'ar')}} <br>@endif
                @if(!empty($transaction->delivery_datetime))@lang('lang.delivery',[], 'en'): {{@format_datetime($transaction->delivery_datetime)}} {{__('lang.delivery',[], 'ar')}} <br>@endif

            </p>
            @endif
        </div>
        @if(session('system_mode') == 'restaurant')
        <div style="width: 30%; float:right; text-align:center;">
            <p
                style="width: 75px; height:75px; border: 4px solid #111; border-radius: 50%; padding: 20px; font-size: 23px; font-weight: bold;">
                {{$transaction->ticket_number}}</p>
        </div>
        @endif
        <div class="table_div" style=" width:100%; height:100%; padding: 0 7px;">
            <table style="margin: 0 auto; width: 100%">
                <thead>
                    <tr>
                        <th style="width: 20%">{{__('lang.amount',[], 'ar')}} <br> @lang('lang.amount',[], 'en') </th>
                        <th style="width: 20%">{{__('lang.qty',[], 'ar')}} <br> @lang('lang.qty',[], 'en') </th>
                        <th style="width: 20%;text-align:center !important;">{{__('lang.price',[], 'ar')}} <br>
                            @lang('lang.price',[], 'en') </th>
                        <th style="width: 40%; padding: 0 50px !important;">{{__('lang.item',[], 'ar')}} <br>
                            @lang('lang.item',[], 'en') </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($transaction->transaction_sell_lines as $line)
                    <tr>
                        <td style="text-align:left;vertical-align:bottom">{{@num_format($line->sub_total)}}</td>
                        <td style="text-align:left;vertical-align:bottom">{{@num_format($line->quantity)}}</td>
                        <td style="text-align:center !important;vertical-align:bottom">
                            {{@num_format($line->sell_price)}}</td>
                        <td style="width: 40% !important;padding: 0 5px 0 10px !important;">
                            {{$line->product->name}}
                            @if(!empty($line->variation))
                            @if($line->variation->name != "Default")
                            <b>{{$line->variation->name}}</b>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->grand_total)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.total',[], 'ar')}} <br>
                            @lang('lang.total',[], 'en') </th>
                    </tr>
                    @if($transaction->total_item_tax != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->total_item_tax)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.tax',[], 'ar')}} <br>
                            @lang('lang.tax',[], 'en') </th>
                    </tr>
                    @endif
                    @if($transaction->total_tax != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->total_tax)}}</th>
                        <th colspan="2" style="text-align:right">{{$transaction->tax->name ?? ''}}</th>
                    </tr>
                    @endif
                    @if($transaction->discount_amount != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->discount_amount)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.order_discount',[], 'ar')}} <br>
                            @lang('lang.order_discount',[],
                            'en')
                        </th>
                    </tr>
                    @endif
                    @if($transaction->total_sp_discount != 0)
                    <tr>
                        <th colspan="3">{{__('lang.sales_promotion',[], 'ar')}} <br>
                            @lang('lang.sales_promotion' , [], 'en')</th>
                        <th style="text-align:right">{{@num_format($transaction->total_sp_discount)}}</th>
                    </tr>
                    @endif
                    @if($transaction->transaction_sell_lines->sum('coupon_discount'))
                    <tr>
                        <th colspan="2" style="text-align:left">
                            {{@num_format($transaction->transaction_sell_lines->sum('coupon_discount'))}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.coupon_discount',[], 'ar')}} <br>
                            @lang('lang.coupon_discount',[],
                            'en')
                        </th>
                    </tr>
                    @endif
                    @if(!empty($transaction->delivery_cost) && $transaction->delivery_cost != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->delivery_cost)}}
                        </th>
                        <th colspan="2" style="text-align:right">{{__('lang.delivery_cost',[], 'ar')}} <br>
                            @lang('lang.delivery_cost' , [], 'en') @if(!empty($transaction->deliveryman->employee_name)) ({{$transaction->deliveryman->employee_name}}) @endif</th>
                    </tr>
                    @endif
                    @if(!empty($transaction->rp_redeemed_value))
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->rp_redeemed_value)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.redeemed_point_value',[], 'ar')}} <br>
                            @lang('lang.redeemed_point_value',[], 'en') </th>
                    </tr>
                    @endif
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->final_total)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.grand_total',[], 'ar')}} <br>
                            @lang('lang.grand_total',[], 'en')</th>
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
                    @if($payment_data->method != 'deposit')
                    <tr style="background-color:#ddd;">
                        <td style="padding: 7px;width:30%">
                            @if(!empty($payment_data->method)){{__('lang.'. $payment_data->method, [], 'ar')}} <br>
                            {{__('lang.'. $payment_data->method, [], 'en')}}@endif</td>
                        <td style="padding: 7px;width:40%; text-align: right;" colspan="2">
                            {{@num_format($payment_data->amount + $payment_data->change_amount)}}</td>
                    </tr>
                    @endif
                    @if(!empty($payment_data->change_amount) && $payment_data->change_amount > 0 &&
                    $payment_data->method != 'deposit')
                    <tr>
                        <td style="padding: 7px;width:30%">{{__('lang.change', [], 'ar')}} <br> {{__('lang.change', [], 'en')}}</td>
                        <td colspan="2" style="padding: 7px;width:40%; text-align: right;">
                            {{@num_format($payment_data->change_amount)}}</td>
                    </tr>
                    @endif
                    @endforeach
                    @if(!empty($payment_data->add_to_deposit) && $payment_data->add_to_deposit > 0 &&
                    $payment_data->method == 'deposit')
                    <tr>
                        <td style="padding: 7px;width:30%">{{__('lang.deposit', [], 'ar')}} <br> {{__('lang.deposit', [], 'en')}}</td>
                        <td colspan="2" style="padding: 7px;width:40%; text-align: right;">
                            {{@num_format($payment_data->add_to_deposit)}}</td>
                    </tr>
                    @endif
                    @if(!empty($payment_data->used_deposit_balance) && $payment_data->used_deposit_balance > 0 &&
                    $payment_data->method == 'deposit')
                    <tr>
                        <td style="padding: 7px;width:30%">{{__('lang.used_deposit_balance', [], 'ar')}} <br> {{__('lang.used_deposit_balance', [], 'en')}}</td>
                        <td colspan="2" style="padding: 7px;width:40%; text-align: right;">
                            {{@num_format($payment_data->used_deposit_balance)}}</td>
                    </tr>
                    @endif
                    @if($transaction->payment_status != 'paid' && $transaction->final_total - $transaction->transaction_payments->sum('amount') > 0)
                    <tr>
                        <td style="padding: 7px;width:30%">{{__('lang.due', [], 'ar')}} <br> {{__('lang.due', [], 'en')}}</td>
                        <td colspan="2" style="padding: 7px;width:40%; text-align: right;">
                            {{@num_format($transaction->final_total -
                            $transaction->transaction_payments->sum('amount'))}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="centered" colspan="3">
                            @if(session('system_mode') == 'restaurant')
                            @lang('lang.enjoy_your_meal_please_come_again',[], 'en')
                            {{__('lang.enjoy_your_meal_please_come_again',[], 'ar')}}
                            @else
                            @lang('lang.thank_you_and_come_again',[], 'en')
                            {{__('lang.thank_you_and_come_again',[], 'ar')}}
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
                                src="data:image/png;base64,{{DNS1D::getBarcodePNG($transaction->invoice_no ?? '1', 'C128')}}"
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
