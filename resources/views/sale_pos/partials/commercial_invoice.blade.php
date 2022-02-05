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
<div class="row">
    @include('layouts.partials.print_header')
</div>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">
                <h5>@if($sale->status == 'draft' && $sale->is_quotation == 1)@lang('lang.quotation_no')@else @lang('lang.invoice_no') @endif: {{$sale->invoice_no}}
                </h5>
            </div>
            <div class="col-md-12">
                <h5>@lang('lang.date'): {{@format_datetime($sale->transaction_date)}}</h5>
            </div>
            <div class="col-md-12">
                <h5>@lang('lang.store'): {{$sale->store->name ?? ''}}</h5>
            </div>
        </div>
        <br>
        <div class="col-md-6">
            <div class="col-md-12">
                {!! Form::label('supplier_name', __('lang.customer_name'), []) !!}:
                <b>{{$sale->customer->name ?? ''}}</b>
            </div>
            <div class="col-md-12">
                {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$sale->customer->email ?? ''}}</b>
            </div>
            <div class="col-md-12">
                {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                <b>{{$sale->customer->mobile_number ?? ''}}</b>
            </div>
            <div class="col-md-12">
                {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$sale->customer->address ??
                    ''}}</b>
            </div>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" id="">
                <thead class="bg-success" style="color: white">
                    <tr>
                        <th style="width: 20% !important;">@lang( 'lang.image' )</th>
                        <th style="width: 20% !important;">@lang( 'lang.products' )</th>
                        <th style="width: 10% !important;">@lang( 'lang.sku' )</th>
                        <th style="width: 10% !important;">@lang( 'lang.batch_number' )</th>
                        <th style="width: 10% !important;">@lang( 'lang.quantity' )</th>
                        <th style="width: 10% !important;">@lang( 'lang.sell_price' )</th>
                        <th style="width: 9% !important;">@lang( 'lang.discount' )</th>
                        <th style="width: 10% !important;">@lang( 'lang.sub_total' )</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->transaction_sell_lines as $line)
                    <tr>
                        <td style="width: 20% !important;"><img src="@if(!empty($line->product) && !empty($line->product->getFirstMediaUrl('product'))){{$line->product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                alt="photo" width="50" height="50"></td>
                        <td style="width: 20% !important;">
                            {{$line->product->name ?? ''}}
                            @if(!empty($line->variation))
                            @if($line->variation->name != "Default")
                            <b>{{$line->variation->name}}</b>
                            @endif
                            @endif
                            @if(empty($line->variation) && empty($line->product))
                            <span class="text-red">@lang('lang.deleted')</span>
                            @endif

                        </td>
                        <td style="width: 10% !important;">
                            {{$line->product->sku ?? ''}}
                        </td>
                        <td style="width: 10% !important;">
                            {{$line->product->batch_number ?? ''}}
                        </td>
                        <td style="width: 10% !important;">
                            @if(isset($line->quantity)){{@num_format($line->quantity)}}@else{{1}}@endif
                        </td>
                        <td style="width: 10% !important;">
                            @if(isset($line->sell_price)){{@num_format($line->sell_price)}}@else{{0}}@endif
                        </td>
                        <td style="width: 9% !important;">
                            @if(isset($line->product_discount_amount)){{@num_format($line->product_discount_amount)}}@else{{0}}@endif
                        </td>
                        <td style="width: 10% !important;">
                            {{@num_format($line->sub_total)}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right"> @lang('lang.total')</th>
                        <td>{{@num_format($sale->transaction_sell_lines->sum('product_discount_amount'))}}</td>
                        <td>{{@num_format($sale->grand_total)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <br>
    <br>

    @if($sale->status != 'draft')
    <div class="row text-center">
        <div class="col-md-12">
            <h4>@lang('lang.payment_details')</h4>
        </div>

    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
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
                    <td style="width: 10% !important;">{{@num_format($payment->amount)}}</td>
                    <td style="width: 10% !important;">{{@format_date($payment->paid_on)}}</td>
                    <td style="width: 10% !important;">{{$payment_type_array[$payment->method]}}</td>
                    <td style="width: 10% !important;">{{$payment->bank_name}}</td>
                    <td style="width: 10% !important;">{{$payment->ref_number}}</td>
                    <td style="width: 10% !important;">@if(!empty($payment->bank_deposit_date && ($payment->method == 'bank_transfer' ||
                        $payment->method == 'cheque'))){{@format_date($payment->bank_deposit_date)}} @endif</td>
                    <td style="width: 10% !important;">{{$payment->card_number}}</td>
                    <td style="width: 10% !important;">{{$payment->card_year}}</td>
                    <td style="width: 10% !important;">{{$payment->card_month}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif
    <br>
    <br>
    <div class="row">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th>@lang('lang.total_tax'):</th>
                    <td>{{@num_format($sale->total_tax)}}</td>
                </tr>
                <tr>
                    <th>@lang('lang.discount'):</th>
                    <td>{{@num_format($sale->discount_amount)}}</td>
                </tr>
                @if(!empty($sale->rp_earned))
                <tr>
                    <th>@lang('lang.point_earned'):</th>
                    <td>{{@num_format($sale->rp_earned)}}</td>
                </tr>
                @endif
                @if(!empty($sale->rp_redeemed_value))
                <tr>
                    <th>@lang('lang.redeemed_point_value'):</th>
                    <td>{{@num_format($sale->rp_redeemed_value)}}</td>
                </tr>
                @endif
                @if($sale->total_coupon_discount > 0)
                <tr>
                    <th>@lang('lang.coupon_discount')</th>
                    <td>{{@num_format($sale->total_coupon_discount)}}</td>
                </tr>
                @endif
                @if($sale->delivery_cost > 0)
                <tr>
                    <th>@lang('lang.delivery_cost')</th>
                    <td>{{@num_format($sale->delivery_cost)}}</td>
                </tr>
                @endif
                <tr>
                    <th>@lang('lang.grand_total'):</th>
                    <td>{{@num_format($sale->final_total)}}</td>
                </tr>
                @if($sale->status == 'final')
                <tr>
                    <th>@lang('lang.paid_amount'):</th>
                    <td>{{@num_format($sale->transaction_payments->sum('amount'))}}</td>
                </tr>
                <tr>
                    <th>@lang('lang.due'):</th>
                    <td> {{@num_format($sale->final_total - $sale->transaction_payments->sum('amount'))}}</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-12">
            <b>@lang('lang.terms_and_conditions'):</b>
            @if(!empty($sale->terms_and_conditions)){!!$sale->terms_and_conditions->description!!} @endif
        </div>
    </div>
</div>
<div class="row">
    @include('layouts.partials.print_footer')
</div>
