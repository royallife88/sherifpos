@extends('layouts.app')
@section('title', __('lang.customer_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.customer_report')</h4>
        </div>
        <form action="">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('start_date', __('lang.start_date'), []) !!}
                            {!! Form::date('start_date', request()->start_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                            {!! Form::date('end_date', request()->end_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('lang.customer'), []) !!}
                            {!! Form::select('customer_id', $customers, request()->customer_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getCustomerReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#store-sale" role="tab"
                            data-toggle="tab">@lang('lang.sale')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-payment" role="tab"
                            data-toggle="tab">@lang('lang.payments')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-quotation" role="tab"
                            data-toggle="tab">@lang('lang.quotation')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-return" role="tab" data-toggle="tab">@lang('lang.return')</a>
                    </li>

                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="store-sale">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.customer')</th>
                                        <th>@lang('lang.product')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.paid')</th>
                                        <th>@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{@format_date($sale->transaction_date)}}</td>
                                        <td>{{$sale->invoice_no}}</td>
                                        <td>@if(!empty($sale->customer)){{$sale->customer->name}}@endif</td>
                                        <td>
                                            @foreach ($sale->transaction_sell_lines as $line)
                                            ({{@num_format($line->quantity)}})
                                            {{App\Models\Product::find($line->product_id)->name}} <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($sale->final_total)}}</td>
                                        <td>{{@num_format($sale->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($sale->final_total - $sale->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($sale->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($sale->status)}} @endif</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                                        <th>{{@num_format($sales->sum('amount'))}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="store-payment">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.payment_ref')</th>
                                        <th>@lang('lang.sale_ref')</th>
                                        <th>@lang('lang.purchase_ref')</th>
                                        <th>@lang('lang.paid_by')</th>
                                        <th>@lang('lang.amount')</th>
                                        <th>@lang('lang.created_by')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{@format_date($payment->paid_on)}}</td>
                                        <td>{{$payment->ref_number}}</td>
                                        <td>@if($payment->type == 'sell'){{$payment->invoice_no}}@endif</td>
                                        <td>@if($payment->type == 'add_stock'){{$payment->invoice_no}}@endif
                                        </td>
                                        <td>@if(!empty($payment_types[$payment->method])){{$payment_types[$payment->method]}}
                                            @endif</td>
                                        <td>{{@num_format($payment->amount)}}</td>
                                        <td>{{ucfirst($payment->created_by_name)}}</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                                        <th>{{@num_format($payments->sum('amount'))}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="store-quotation">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.customer')</th>
                                        <th>@lang('lang.product')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.paid')</th>
                                        <th>@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($quotations as $quotation)
                                    <tr>
                                        <td>{{@format_date($quotation->transaction_date)}}</td>
                                        <td>{{$quotation->invoice_no}}</td>
                                        <td>@if(!empty($quotation->customer)){{$quotation->customer->name}}@endif
                                        </td>
                                        <td>
                                            @foreach ($quotation->transaction_sell_lines as $line)
                                            ({{@num_format($line->quantity)}})
                                            {{App\Models\Product::find($line->product_id)->name}} <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($quotation->final_total)}}</td>
                                        <td>{{@num_format($quotation->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($quotation->final_total - $quotation->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($quotation->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($quotation->status)}} @endif</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                                        <th>{{@num_format($quotations->sum('amount'))}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="store-return">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.customer')</th>
                                        <th>@lang('lang.product')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.paid')</th>
                                        <th>@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($sell_returns as $return)
                                    <tr>
                                        <td>{{@format_date($return->transaction_date)}}</td>
                                        <td>{{$return->invoice_no}}</td>
                                        <td>@if(!empty($return->customer)){{$return->customer->name}}@endif</td>
                                        <td>
                                            @php
                                            $parent_return =
                                            App\Models\Transaction::find($return->return_parent_id);
                                            @endphp
                                            @foreach ($parent_return->transaction_sell_lines as $line)
                                            @if($line->quantity_returned == 0)
                                            @continue
                                            @endif
                                            ({{@num_format($line->quantity)}})
                                            {{App\Models\Product::find($line->product_id)->name}} <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($return->final_total)}}</td>
                                        <td>{{@num_format($return->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($return->final_total - $return->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($return->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($return->status)}} @endif</td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                                        <th>{{@num_format($sell_returns->sum('amount'))}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
