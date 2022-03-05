@extends('layouts.app')
@section('title', __('lang.supplier_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.supplier_report')</h4>
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
                            {!! Form::label('supplier_id', __('lang.user'), []) !!}
                            {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getUserReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#store-purchase" role="tab"
                            data-toggle="tab">@lang('lang.purchase')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-purchase-order" role="tab"
                            data-toggle="tab">@lang('lang.purchase_order')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-payment" role="tab"
                            data-toggle="tab">@lang('lang.payments')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-return" role="tab" data-toggle="tab">@lang('lang.return')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="store-purchase">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.supplier')</th>
                                        <th>@lang('lang.product')</th>
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($add_stocks as $add_stock)
                                    <tr>
                                        <td>{{@format_date($add_stock->transaction_date)}}</td>
                                        <td>{{$add_stock->invoice_no}}</td>
                                        <td>@if(!empty($add_stock->supplier)){{$add_stock->supplier->name}}@endif</td>
                                        <td>
                                            @foreach ($add_stock->add_stock_lines as $line)
                                            ({{@num_format($line->quantity)}})
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($add_stock->final_total)}}</td>
                                        <td>{{@num_format($add_stock->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($add_stock->final_total - $add_stock->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($add_stock->status == 'received')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@else
                                            {{ucfirst($add_stock->status)}} @endif</td>
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
                                                    @can('stock.add_stock.view')
                                                    <li>
                                                        <a href="{{action('AddStockController@show', $add_stock->id)}}"
                                                            class=""><i class="fa fa-eye btn"></i>
                                                            @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.create_and_edit')
                                                    <li>
                                                        <a href="{{action('AddStockController@edit', $add_stock->id)}}"><i
                                                                class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.delete')
                                                    <li>
                                                        <a data-href="{{action('AddStockController@destroy', $add_stock->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i
                                                                class="dripicons-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                    @can('stock.pay.create_and_edit')
                                                    @if($add_stock->payment_status != 'paid')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $add_stock->id])}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-money"></i>
                                                            @lang('lang.pay')</a>
                                                    </li>
                                                    @endif
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
                                        <td>@if($payment->type == 'add_stock'){{$payment->invoice_no}}@endif
                                        </td>
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
                                                    @can('stock.add_stock.view')
                                                    <li>
                                                        <a href="{{action('AddStockController@show', $payment->id)}}"
                                                            class=""><i class="fa fa-eye btn"></i>
                                                            @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.create_and_edit')
                                                    <li>
                                                        <a href="{{action('AddStockController@edit', $payment->id)}}"><i
                                                                class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.delete')
                                                    <li>
                                                        <a data-href="{{action('AddStockController@destroy', $payment->id)}}"
                                                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                            class="btn text-red delete_item"><i
                                                                class="dripicons-trash"></i>
                                                            @lang('lang.delete')</a>
                                                    </li>
                                                    @endcan
                                                    @can('stock.pay.create_and_edit')
                                                    @if($payment->payment_status != 'paid')
                                                    <li>
                                                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $payment->id])}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-money"></i>
                                                            @lang('lang.pay')</a>
                                                    </li>
                                                    @endif
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

                    <div role="tabpanel" class="tab-pane fade" id="store-purchase-order">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.supplier')</th>
                                        <th>@lang('lang.product')</th>
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($purchase_orders as $purchase_order)
                                    <tr>
                                        <td>{{@format_date($purchase_order->transaction_date)}}</td>
                                        <td>{{$purchase_order->po_no}}</td>
                                        <td>@if(!empty($purchase_order->supplier)){{$purchase_order->supplier->name}}@endif
                                        </td>
                                        <td>
                                            @foreach ($purchase_order->purchase_order_lines as $line)
                                            ({{@num_format($line->quantity)}})
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
                                        </td>
                                        <td>{{@num_format($purchase_order->final_total)}}</td>
                                        <td>{{@num_format($purchase_order->transaction_payments->sum('amount'))}}</td>
                                        <td>{{@num_format($purchase_order->final_total - $purchase_order->transaction_payments->sum('amount'))}}
                                        </td>
                                        <td>@if($purchase_order->status == 'final')<span
                                                class="badge badge-success">@lang('lang.completed')</span>@elseif($purchase_order->status == 'sent_admin') Sent Admin @else
                                            {{ucfirst($purchase_order->status)}} @endif</td>
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
                                                    @can('purchase_order.purchase_order.view')
                                                    <li>
                                                        <a href="{{action('PurchaseOrderController@show', $purchase_order->id)}}"
                                                            class=""><i class="fa fa-eye btn"></i>
                                                            @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('purchase_order.purchase_order.create_and_edit')
                                                    <li>
                                                        <a
                                                            href="{{action('PurchaseOrderController@edit', $purchase_order->id)}}"><i
                                                                class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('purchase_order.purchase_order.delete')
                                                    <li>
                                                        <a data-href="{{action('PurchaseOrderController@destroy', $purchase_order->id)}}"
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
                                        <th>@lang('lang.supplier')</th>
                                        <th>@lang('lang.product')</th>
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th class="sum">@lang('lang.paid')</th>
                                        <th class="sum">@lang('lang.due')</th>
                                        <th>@lang('lang.status')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($purchase_returns as $return)
                                    <tr>
                                        <td>{{@format_date($return->transaction_date)}}</td>
                                        <td>{{$return->invoice_no}}</td>
                                        <td>@if(!empty($return->supplier)){{$return->supplier->name}}@endif</td>
                                        <td>

                                            @foreach ($return->add_stock_lines as $line)
                                            @if($line->quantity_returned == 0)
                                            @continue
                                            @endif
                                            ({{@num_format($line->quantity)}})
                                            @if(!empty($line->product)){{$line->product->name}}@endif <br>
                                            @endforeach
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

                                                        <a data-href="{{action('PurchaseReturnController@show', $return->id)}}"
                                                            data-container=".view_modal" class="btn btn-modal"><i
                                                                class="fa fa-eye"></i>
                                                            @lang('lang.view')</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    @endcan
                                                    @can('return.sell_return.create_and_edit')
                                                    <li>
                                                        <a href="{{action('PurchaseReturnController@edit', $return->id)}}"
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
