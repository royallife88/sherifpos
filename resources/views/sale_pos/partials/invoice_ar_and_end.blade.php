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

            <p>@lang('lang.address',[], 'en'): {{__('lang.address',[], 'ar')}} <br> {{$transaction->store->name}}
                {{$transaction->store->location}}</p>
            <p>@lang('lang.phone_number',[], 'en'): {{__('lang.phone_number',[], 'ar')}} <br>
                {{$transaction->store->phone_number}} </p>

        </div>
        <p style="padding: 0 7px;">@lang('lang.date',[], 'en'): {{$transaction->created_at}} {{__('lang.date',[],
            'ar')}}<br>
            @lang('lang.reference',[], 'en'): {{$transaction->invoice_no}} {{__('lang.reference',[], 'ar')}}<br>
            @lang('lang.customer',[], 'en'): @if(!empty($transaction->customer)){{$transaction->customer->name}}@endif
            {{__('lang.customer',[], 'ar')}}
        </p>
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
                            {{-- <br>{{@num_format($line->quantity)}} x {{@num_format($line->sell_price)}}
                            @if(!empty((float)$line->product_discount_amount))-{{@num_format($line->product_discount_amount)}}@endif
                            --}}

                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->grand_total)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.total',[], 'ar')}} <br> @lang('lang.total',[], 'en') </th>
                    </tr>
                    @if($transaction->total_tax != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->total_tax)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.order_tax',[], 'ar')}} <br> @lang('lang.order_tax',[], 'en') </th>
                    </tr>
                    @endif
                    @if($transaction->discount_amount != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->discount_amount)}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.order_discount',[], 'ar')}} <br> @lang('lang.order_discount',[],
                            'en')
                        </th>
                    </tr>
                    @endif
                    @if($transaction->transaction_sell_lines->sum('coupon_discount'))
                    <tr>
                        <th colspan="2" style="text-align:left">
                            {{@num_format($transaction->transaction_sell_lines->sum('coupon_discount'))}}</th>
                        <th colspan="2" style="text-align:right">{{__('lang.coupon_discount',[], 'ar')}} <br> @lang('lang.coupon_discount',[],
                            'en')
                        </th>
                    </tr>
                    @endif
                    @if(!empty($transaction->delivery_cost) && $transaction->delivery_cost != 0)
                    <tr>
                        <th colspan="2" style="text-align:left">{{@num_format($transaction->delivery_cost)}}
                        </th>
                        <th colspan="2" style="text-align:right">{{__('lang.delivery_cost',[], 'ar')}} <br> @lang('lang.delivery_cost' , [], 'en')</th>
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
                        <th colspan="2" style="text-align:right">{{__('lang.grand_total',[], 'ar')}} <br> @lang('lang.grand_total',[], 'en')</th>
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
                        <td colspan="2">@lang('lang.paid_by',[], 'en'): {{__('lang.paid_by',[], 'ar')}}</td>
                    </tr>
                    @foreach($transaction->transaction_payments as $payment_data)
                    <tr style="background-color:#ddd;">
                        <td style="padding: 7px;width:30%">
                            @if(!empty($payment_data->method)){{__('lang.'. $payment_data->method, [], 'ar')}} <br>
                            {{__('lang.'. $payment_data->method, [], 'en')}}@endif</td>
                        <td style="padding: 7px;width:40%">
                            {{@num_format($payment_data->amount)}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="centered" colspan="3">@lang('lang.thank_you_and_come_again',[], 'en')
                            {{__('lang.thank_you_and_come_again',[], 'ar')}}
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
