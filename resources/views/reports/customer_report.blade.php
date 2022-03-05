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
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('start_date', __('lang.start_date'), []) !!}
                            {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('start_time', __('lang.start_time'), []) !!}
                            {!! Form::text('start_time', request()->start_time, ['class' => 'form-control
                            time_picker sale_filter']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                            {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('end_time', __('lang.end_time'), []) !!}
                            {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker
                            sale_filter']) !!}
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
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                    $total_purchase_payments = 0;
                                    $total_purchase_due = 0;
                                    @endphp
                                    @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{@format_date($sale->transaction_date)}}</td>
                                        <td>{{$sale->invoice_no}} @if(!empty($sale->return_parent))<a data-href="{{action('SellReturnController@show', $sale->id)}}"
                                            data-container=".view_modal" class="btn btn-modal" style="color: #007bff;">R</a>@endif</td>
                                        <td>@if(!empty($sale->customer)){{$sale->customer->name}}@endif</td>
                                        <td>
                                            @foreach ($sale->transaction_sell_lines as $line)
                                            ({{@num_format($line->quantity)}})
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($sale->final_total)}}</td>
                                        <td>{{@num_format($sale->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($sale->final_total - $sale->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($sale->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($sale->status)}} @endif</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    {{-- @can('sale.pos.create_and_edit')
                                                            <li>

                                                                <a data-href="{{action('SellController@print', $sale->id)}}"
                                                    class="btn print-invoice"><i class="dripicons-print"></i>
                                                    @lang('lang.generate_invoice')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan --}}
                                                    @can('sale.pos.view')
                                                    <li>

                                                        <a data-href="{{action('SellController@show', $sale->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-eye"></i> @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('sale.pos.create_and_edit')
                                                    <li>

                                                        <a href="{{action('SellController@edit', $sale->id)}}"
                                                            class="btn"><i class="dripicons-document-edit"></i>
                                                            @lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('return.sell_return.create_and_edit')
                                                    <li>
                                                        <a href="{{action('SellReturnController@add', $sale->id)}}"
                                                            class="btn"><i class="fa fa-undo"></i>
                                                            @lang('lang.sale_return')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('sale.pay.create_and_edit')
                                                    @if($sale->payment_status != 'paid')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $sale->id])}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-plus"></i> @lang('lang.add_payment')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('sale.pay.view')
                                                    @if($sale->payment_status != 'pending')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@show', $sale->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-money"></i> @lang('lang.view_payments')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('sale.pos.delete')
                                                    <li>
                                                        <a data-href="{{action('SellController@destroy', $sale->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                    $total_purchase_payments += $sale->transaction_payments->sum('amount');
                                    $total_purchase_due += $sale->final_total -
                                    $sale->transaction_payments->sum('amount');
                                    @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">@lang('lang.total')</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th>@lang('lang.paid_by')</th>
                                        <th class="sum">@lang('lang.amount')</th>
                                        <th>@lang('lang.created_by')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{@format_date($payment->paid_on)}}</td>
                                        <td>{{$payment->ref_number}}</td>
                                        <td>@if($payment->type == 'sell'){{$payment->invoice_no}}@endif</td>
                                        <td>@if(!empty($payment_types[$payment->method])){{$payment_types[$payment->method]}}
                                            @endif</td>
                                        <td>{{@num_format($payment->amount)}}</td>
                                        <td>{{ucfirst($payment->created_by_name)}}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    {{-- @can('sale.pos.create_and_edit')
                                                        <li>

                                                            <a data-href="{{action('SellController@print', $payment->id)}}"
                                                    class="btn print-invoice"><i class="dripicons-print"></i>
                                                    @lang('lang.generate_invoice')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan --}}
                                                    @can('sale.pos.view')
                                                    <li>

                                                        <a data-href="{{action('SellController@show', $payment->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-eye"></i> @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('sale.pos.create_and_edit')
                                                    <li>

                                                        <a href="{{action('SellController@edit', $payment->id)}}"
                                                            class="btn"><i class="dripicons-document-edit"></i>
                                                            @lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('return.sell_return.create_and_edit')
                                                    <li>
                                                        <a href="{{action('SellReturnController@add', $payment->id)}}"
                                                            class="btn"><i class="fa fa-undo"></i>
                                                            @lang('lang.sale_return')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('sale.pay.create_and_edit')
                                                    @if($payment->payment_status != 'paid')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $payment->id])}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-plus"></i> @lang('lang.add_payment')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('sale.pay.view')
                                                    @if($payment->payment_status != 'pending')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@show', $payment->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-money"></i> @lang('lang.view_payments')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('sale.pos.delete')
                                                    <li>
                                                        <a data-href="{{action('SellController@destroy', $payment->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">@lang('lang.total')</th>
                                        <th></th>
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
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
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
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($quotation->final_total)}}</td>
                                        <td>{{@num_format($quotation->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($quotation->final_total - $quotation->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($quotation->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($quotation->status)}} @endif</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @if($quotation->status != 'expired')
                                                    @can('sale.sale.create_and_edit')
                                                    <li>
                                                        <a href="{{action('SellController@edit', $quotation->id)}}"
                                                            class="btn print-invoice"><i class="dripicons-document"></i>
                                                            @lang('lang.create_invoice')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @endif
                                                    @can('squotation_for_customers.quotation.view')
                                                    <li>

                                                        <a data-href="{{action('QuotationController@show', $quotation->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-eye"></i> @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('squotation_for_customers.quotation.create_and_edit')
                                                    <li>

                                                        <a href="{{action('QuotationController@edit', $quotation->id)}}"
                                                            class="btn"><i class="dripicons-document-edit"></i>
                                                            @lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan

                                                    @can('squotation_for_customers.quotation.delete')
                                                    <li>
                                                        <a data-href="{{action('QuotationController@destroy', $quotation->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">@lang('lang.total')</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
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
                                            @if(!empty($parent_return))
                                            @foreach ($parent_return->transaction_sell_lines as $line)
                                            @if($line->quantity_returned == 0)
                                            @continue
                                            @endif
                                            ({{@num_format($line->quantity)}})
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td>{{@num_format($return->final_total)}}</td>
                                        <td>{{@num_format($return->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($return->final_total - $return->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($return->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($return->status)}} @endif</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @can('return.sell_return.view')
                                                    <li>

                                                        <a data-href="{{action('SellReturnController@show', $return->return_parent_id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-eye"></i>
                                                            @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('return.sell_return.create_and_edit')
                                                    <li>
                                                        <a href="{{action('SellReturnController@add', $return->return_parent_id)}}"
                                                            class="btn"><i class="dripicons-document-edit"></i>
                                                            @lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('return.sell_return_pay.create_and_edit')
                                                    @if($return->payment_status != 'paid')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $return->id])}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-plus"></i>
                                                            @lang('lang.add_payment')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('return.sell_return_pay.view')
                                                    @if($return->payment_status != 'pending')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@show', $return->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-money"></i>
                                                            @lang('lang.view_payments')</a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                    @can('sale.pos.delete')
                                                    <li>
                                                        <a data-href="{{action('SellController@destroy', $return->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">@lang('lang.total')</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
