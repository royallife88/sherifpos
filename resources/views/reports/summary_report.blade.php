@extends('layouts.app')
@section('title', __('lang.summary_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.summary_report')</h4>
        </div>
        <form action="">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="from-group">
                            {!! Form::label('date', __('lang.date'), []) !!}
                            <input type="text" class="daterangepicker-field form-control"
                                value="{{request()->start_date}} To {{request()->end_date}}" required />
                            <input type="hidden" name="start_date" value="{{request()->start_date}}" />
                            <input type="hidden" name="end_date" value="{{request()->end_date}}" />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                            {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class'
                            =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    @if(session('user.is_superadmin'))
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('pos_id', __('lang.pos'), []) !!}
                            {!! Form::select('pos_id', $store_pos, request()->pos_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_id', __('lang.product'), []) !!}
                            {!! Form::select('product_id', $products, request()->product_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getPayableReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.purchase')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($add_stocks->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.purchase')</td>
                                    <td style="text-align: right">{{@num_format($add_stocks->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.paid')</td>
                                    <td style="text-align: right">{{@num_format($add_stocks->total_paid)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.tax')</td>
                                    <td style="text-align: right">{{@num_format($add_stocks->total_taxes)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.sale')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.sale')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.paid')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_paid)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.tax')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_taxes)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.sale_return')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($sale_returns->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.return')</td>
                                    <td style="text-align: right">{{@num_format($sale_returns->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.tax')</td>
                                    <td style="text-align: right">{{@num_format($sale_returns->total_taxes)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.purchase_return')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($purchase_returns->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.return')</td>
                                    <td style="text-align: right">{{@num_format($purchase_returns->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.tax')</td>
                                    <td style="text-align: right">{{@num_format($purchase_returns->total_taxes)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.profit_loss')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.sale')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.purchase')</td>
                                    <td style="text-align: right">{{@num_format($add_stocks->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.profit')</td>
                                    <td style="text-align: right">
                                        {{@num_format($sales->total_amount - $add_stocks->total_amount)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.profit_loss')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.sale')</td>
                                    <td style="text-align: right">{{@num_format($sales->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.purchase')</td>
                                    <td style="text-align: right">-{{@num_format($add_stocks->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.sale_return')</td>
                                    <td style="text-align: right">-{{@num_format($sale_returns->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.purchase_return')</td>
                                    <td style="text-align: right">{{@num_format($purchase_returns->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.profit')</td>
                                    <td style="text-align: right">
                                        {{@num_format($sales->total_amount - $add_stocks->total_amount - $sale_returns->total_amount + $purchase_returns->total_amount)}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.net_profit_net_loss')</h3>
                                    </th>

                                </tr>
                            </thead>
                            @php
                            $net_profit_loss = ($sales->total_amount-$sales->total_taxes) -
                            ($add_stocks->total_amount - $add_stocks->total_taxes) -
                            ($sale_returns->total_amount - $sale_returns->total_taxes) +
                            ($purchase_returns->total_amount - $purchase_returns->total_taxes);
                            @endphp
                            <tbody>
                                <tr>

                                    <td colspan="2" style="text-align: center"><b>{{@num_format($net_profit_loss)}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center">
                                        (@lang('lang.sale') {{@num_format($sales->total_amount)}} -
                                        @lang('lang.tax'){{@num_format($sales->total_taxes)}}) -
                                        (@lang('lang.purchase') {{@num_format($add_stocks->total_amount) }} -
                                        {{@num_format($add_stocks->total_taxes)}}) -
                                        (@lang('lang.sale_return') {{@num_format($sale_returns->total_amount)}} -
                                        @lang('lang.tax') {{@num_format($sale_returns->total_taxes)}}) +
                                        (@lang('lang.purchase_return') {{@num_format($purchase_returns->total_amount)}}
                                        - @lang('lang.tax') {{@num_format($purchase_returns->total_taxes)}})
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.payment_receied')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.received')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.cash')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_cash)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.cheque')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_cheque)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.bank_transfer')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_bank_transfer)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.credit_card')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_card)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.gift_card')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_gift_card)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.paypal')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_paypal)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.deposit')</td>
                                    <td style="text-align: right">{{@num_format($payment_received->total_deposit)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.payment_sent')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.sent')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_count)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.cash')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_cash)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.cheque')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_cheque)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.bank_transfer')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_bank_transfer)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.credit_card')</td>
                                    <td style="text-align: right">{{@num_format($payment_sent->total_card)}}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.expense')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($expenses->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.expense')</td>
                                    <td style="text-align: right">{{@num_format($expenses->total_count)}}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="font-size: 1.2 rem; color: #7c5cc4;">
                                        <h3>@lang('lang.payroll')</h3>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>@lang('lang.amount')</td>
                                    <td style="text-align: right">{{@num_format($wages->total_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('lang.payroll')</td>
                                    <td style="text-align: right">{{@num_format($wages->total_count)}}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(".daterangepicker-field").daterangepicker({
	  callback: function(startDate, endDate, period){
	    var start_date = startDate.format('YYYY-MM-DD');
	    var end_date = endDate.format('YYYY-MM-DD');
	    var title = start_date + ' To ' + end_date;
	    $(this).val(title);
	    $('input[name="start_date"]').val(start_date);
	    $('input[name="end_date"]').val(end_date);
	  }
	});
</script>

@endsection
