<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print</title>
</head>

<body style="background-color:transparent !important;">
    <style>
        @media print {
            * {
                font-size: 12px;
                line-height: 20px;
                font-family: 'Times New Roman';
                background-color: transparent !important;
            }

            td,
            th {
                padding: 5px 0;
            }

            .hidden-print {
                display: none !important;
            }

            #payment_table_view {
                background: transparent !important;
            }

            table tr td,
            table tr th {
                background-color: rgba(210, 130, 240, 0.3) !important;
            }

            #watermark {
                position: fixed;
                width: 100%;
                height: auto;
                top: 10%;
                left: 0%;
                opacity: 0.2;
            }

            #watermark img {
                width: 100%;
                height: 100%;
            }

            #header_invoice_img {
                margin: auto;
            }

            #invoice_heaer_div {
                width: 100%;
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
            background-color: transparent !important;
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

        #product_table_view.table-bordered {
            border: 1px solid #333 !important;
            margin-top: 20px;
        }

        #product_table_view.table-bordered>thead>tr>th {
            border: 1px solid #333 !important;
        }

        #product_table_view.table-bordered>tbody>tr>td {
            border: 1px solid #333 !important;
        }

        #payment_table_view.table-bordered {
            border: 1px solid #333 !important;
            margin-top: 20px;
        }

        #payment_table_view.table-bordered>thead>tr>th {
            border: 1px solid #333 !important;
        }

        #payment_table_view.table-bordered>tbody>tr>td {
            border: 1px solid #333 !important;
        }

    </style>
    @if (empty($create_pdf))
        <style>
            @media print {

                @page {
                    size: A4;
                    margin-top: 0px;
                    margin-bottom: 40px;
                }

                body * {
                    background-color: transparent !important;
                }
            }

        </style>
    @endif
    @if (!empty($create_pdf))
        <style>
            .col-xs-1,
            .col-sm-1,
            .col-md-1,
            .col-lg-1,
            .col-xs-2,
            .col-sm-2,
            .col-md-2,
            .col-lg-2,
            .col-xs-3,
            .col-sm-3,
            .col-md-3,
            .col-lg-3,
            .col-xs-4,
            .col-sm-4,
            .col-md-4,
            .col-lg-4,
            .col-xs-5,
            .col-sm-5,
            .col-md-5,
            .col-lg-5,
            .col-xs-6,
            .col-sm-6,
            .col-md-6,
            .col-lg-6,
            .col-xs-7,
            .col-sm-7,
            .col-md-7,
            .col-lg-7,
            .col-xs-8,
            .col-sm-8,
            .col-md-8,
            .col-lg-8,
            .col-xs-9,
            .col-sm-9,
            .col-md-9,
            .col-lg-9,
            .col-xs-10,
            .col-sm-10,
            .col-md-10,
            .col-lg-10,
            .col-xs-11,
            .col-sm-11,
            .col-md-11,
            .col-lg-11,
            .col-xs-12,
            .col-sm-12,
            .col-md-12,
            .col-lg-12 {
                border: 0;
                padding: 0;
                margin-left: -0.00001;
            }

        </style>
    @endif
    @php
        $logo = App\Models\System::getProperty('logo');
    @endphp
    @if (empty($create_pdf))
        <div id="watermark"><img src="{{ asset('/uploads/' . $logo) }}" alt=""></div>
    @endif

    <div class="row header_div" id="header_div" style="width: 100%;">
        @include('layouts.partials.print_header')

    </div>
    <div class="col-md-12 content_div" id="content_div">
        <div class="row">
            <div class="col-md-6" style="width: 50%;@if (!empty($create_pdf)) float:left; @endif">
                <div class="col-md-12">
                    <h5>
                        @if ($sale->status == 'draft' && $sale->is_quotation == 1)
                            @lang('lang.quotation_no')
                        @else
                            @lang('lang.invoice_no')
                        @endif: {{ $sale->invoice_no }}
                    </h5>
                    <h5>@lang('lang.date'): {{ @format_datetime($sale->transaction_date) }}</h5>
                    <h5>@lang('lang.store'): {{ $sale->store->name ?? '' }}</h5>
                    <h5>@lang('lang.address'): {{ $sale->store->location ?? '' }}</h5>
                    <h5>@lang('lang.phone'): {{ $sale->store->phone_number ?? '' }}</h5>
                </div>
            </div>
            <br>
            <div class="col-md-6" style="width: 50%;@if (!empty($create_pdf)) float:right; @endif">
                <div class="col-md-12">
                    @lang('lang.name'): <b>{{ $sale->customer->name ?? '' }}</b>
                </div>
                <div class="col-md-12">
                    @lang('lang.email'): <b>{{ $sale->customer->email ?? '' }}</b>
                </div>
                <div class="col-md-12">
                    @lang('lang.phone'): <b>{{ $sale->customer->mobile_number ?? '' }}</b>
                </div>
                <div class="col-md-12">
                    @lang('lang.address'): <b>{{ $sale->customer->address ?? '' }}</b>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="table-bordered" style="text-align: center; background-color:transparent !important;"
                    id="product_table_view">
                    <thead class="">
                        <tr>
                            <th style="width: 5% !important;">#</th>
                            <th style="width: 10% !important;">@lang('lang.image')</th>
                            <th style="width: 20% !important;">@lang('lang.products')</th>
                            <th style="width: 10% !important;">@lang('lang.sku')</th>
                            <th style="width: 10% !important;">@lang('lang.batch_number')</th>
                            <th style="width: 10% !important;">@lang('lang.quantity')</th>
                            <th style="width: 10% !important;">@lang('lang.sell_price')</th>
                            <th style="width: 8% !important;">@lang('lang.discount')</th>
                            <th style="width: 15% !important;">@lang('lang.sub_total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($sale->transaction_sell_lines as $line)
                            <tr>
                                <td style="width: 5% !important;">{{ $loop->iteration }}</td>
                                <td style="width: 10% !important;"><img
                                        src="@if (!empty($line->product) && !empty($line->product->getFirstMediaUrl('product'))) {{ $line->product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                        alt="photo" width="50" height="50"></td>
                                <td style="width: 20% !important;">
                                    {{ $line->product->name ?? '' }}
                                    @if (!empty($line->variation))
                                        @if ($line->variation->name != 'Default')
                                            <b>{{ $line->variation->name }}</b>
                                        @endif
                                    @endif
                                    @if (empty($line->variation) && empty($line->product))
                                        <span class="text-red">@lang('lang.deleted')</span>
                                    @endif

                                </td>
                                <td style="width: 10% !important;">
                                    @if (!empty($line->variation))
                                        @if ($line->variation->name != 'Default')
                                            {{ $line->variation->sub_sku }}
                                        @else
                                            {{ $line->product->sku ?? '' }}
                                        @endif
                                    @else
                                        {{ $line->product->sku ?? '' }}
                                    @endif
                                </td>
                                <td style="width: 10% !important;">
                                    {{ $line->product->batch_number ?? '' }}
                                </td>
                                <td style="width: 10% !important;">
                                    @if (isset($line->quantity))
                                        {{ @num_format($line->quantity) }}@else{{ 1 }}
                                    @endif
                                </td>
                                <td style="width: 10% !important;">
                                    @if (isset($line->sell_price))
                                        {{ @num_format($line->sell_price) }}@else{{ 0 }}
                                    @endif
                                </td>
                                <td style="width: 8% !important;">
                                    @if ($line->product_discount_type != 'surplus')
                                        @if (isset($line->product_discount_amount))
                                            {{ @num_format($line->product_discount_amount) }}@else{{ @num_format(0) }}
                                        @endif
                                        @else{{ @num_format(0) }}
                                    @endif
                                </td>
                                <td style="width: 15% !important;">
                                    {{ @num_format($line->sub_total) }}
                                </td>
                            </tr>
                            @if ($i == 20)
                                <div class="pageBreak"></div>
                            @endif
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="width: 5% !important;"></td>
                            <td style="width: 10% !important;"></td>
                            <td style="width: 20% !important;"></td>
                            <td style="width: 10% !important;"></td>
                            <td style="width: 10% !important;"></td>
                            <td style="width: 10% !important;"></td>
                            <th style="width: 10% !important;"> @lang('lang.total') </th>
                            <th style="width: 8% !important;">
                                {{ @num_format($sale->transaction_sell_lines->sum('product_discount_amount')) }}</th>
                            <th style="width: 15% !important;">{{ @num_format($sale->grand_total) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <br>
        <br>

        @if ($sale->status != 'draft')
            <div class="row text-center" style="background-color:transparent !important;">
                <div class="col-md-12">
                    <h4>@lang('lang.payment_details')</h4>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table-bordered" id="payment_table_view"
                            style="text-align: center; background-color:transparent !important;">
                            <thead>
                                <tr>
                                    <th style="width: 10% !important;">@lang('lang.amount')</th>
                                    <th style="width: 10% !important;">@lang('lang.payment_date')</th>
                                    <th style="width: 10% !important;">@lang('lang.payment_type')</th>
                                    <th style="width: 10% !important;">@lang('lang.bank_name')</th>
                                    <th style="width: 10% !important;">@lang('lang.ref_number')</th>
                                    <th style="width: 10% !important;">@lang('lang.bank_deposit_date')</th>
                                    <th style="width: 10% !important;">@lang('lang.card_number')</th>
                                    <th style="width: 10% !important;">@lang('lang.year')</th>
                                    <th style="width: 10% !important;">@lang('lang.month')</th>
                                </tr>
                            </thead>

                            @foreach ($sale->transaction_payments as $payment)
                                <tr>
                                    <td style="width: 10% !important;">{{ @num_format($payment->amount) }}</td>
                                    <td style="width: 10% !important;">{{ @format_date($payment->paid_on) }}</td>
                                    <td style="width: 10% !important;">{{ $payment_type_array[$payment->method] }}
                                    </td>
                                    <td style="width: 10% !important;">{{ $payment->bank_name }}</td>
                                    <td style="width: 10% !important;">{{ $payment->ref_number }}</td>
                                    <td style="width: 10% !important;">
                                        @if (!empty($payment->bank_deposit_date && ($payment->method == 'bank_transfer' || $payment->method == 'cheque')))
                                            {{ @format_date($payment->bank_deposit_date) }}
                                        @endif
                                    </td>
                                    <td style="width: 10% !important;">{{ $payment->card_number }}</td>
                                    <td style="width: 10% !important;">{{ $payment->card_year }}</td>
                                    <td style="width: 10% !important;">{{ $payment->card_month }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif
        <br>
        <br>
        <div class="row">
            <div class="col-md-8">
            </div>
            <div class="col-md-4">
                <table class="table-bordered" style="background-color:transparent !important;">
                    <tr>
                        <th>@lang('lang.total_tax'):</th>
                        <td style="text-align: right;">{{ @num_format($sale->total_tax) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('lang.discount'):</th>
                        <td style="text-align: right;">{{ @num_format($sale->discount_amount) }}</td>
                    </tr>
                    @if (!empty($sale->rp_earned))
                        <tr>
                            <th>@lang('lang.point_earned'):</th>
                            <td style="text-align: right;">{{ @num_format($sale->rp_earned) }}</td>
                        </tr>
                    @endif
                    @if (!empty($sale->rp_redeemed_value))
                        <tr>
                            <th>@lang('lang.redeemed_point_value'):</th>
                            <td style="text-align: right;">{{ @num_format($sale->rp_redeemed_value) }}</td>
                        </tr>
                    @endif
                    @if ($sale->total_coupon_discount > 0)
                        <tr>
                            <th>@lang('lang.coupon_discount')</th>
                            <td style="text-align: right;">{{ @num_format($sale->total_coupon_discount) }}</td>
                        </tr>
                    @endif
                    @if ($sale->delivery_cost > 0)
                        <tr>
                            <th>@lang('lang.delivery_cost')</th>
                            <td style="text-align: right;">{{ @num_format($sale->delivery_cost) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>@lang('lang.grand_total'):</th>
                        <td style="text-align: right;">{{ @num_format($sale->final_total) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('lang.paid_amount'):</th>
                        <td style="text-align: right;">{{ @num_format($sale->transaction_payments->sum('amount')) }}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang.due'):</th>
                        <td style="text-align: right;">
                            {{ @num_format($sale->final_total - $sale->transaction_payments->sum('amount')) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                <b>@lang('lang.terms_and_conditions'):</b>
                @if (!empty($sale->terms_and_conditions))
                    {!! $sale->terms_and_conditions->description !!}
                @endif
            </div>
        </div>
    </div>


    <div class="row footer_div" id="footer_div" style=" width: 100%;">
        @include('layouts.partials.print_footer')
    </div>

</body>

</html>
