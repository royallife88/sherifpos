@extends('layouts.app')
@section('title', __('lang.store_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.store_report')</h4>
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
                    @if(session('user.is_superadmin'))
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getStoreReport')}}"
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
                        <a class="nav-link" href="#store-purchase" role="tab"
                            data-toggle="tab">@lang('lang.purchase')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-quotation" role="tab"
                            data-toggle="tab">@lang('lang.quotation')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-return" role="tab" data-toggle="tab">@lang('lang.return')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#store-expense" role="tab" data-toggle="tab">@lang('lang.expense')</a>
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

                    <div role="tabpanel" class="tab-pane fade" id="store-purchase">
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
                                        <td>@if(!empty($quotation->customer)){{$quotation->customer->name}}@endif</td>
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
                                            @foreach ($return->transaction_sell_lines as $line)
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


                    <div role="tabpanel" class="tab-pane fade" id="store-expense">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.category')</th>
                                        <th class="sum">@lang('lang.grand_total')</th>
                                        <th>@lang('lang.notes')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{@format_date($expense->transaction_date)}}</td>
                                        <td>{{$expense->invoice_no}}</td>
                                        <td>@if(!empty($expense->expense_category)){{$expense->expense_category->name}}@endif
                                        </td>
                                        <td>{{@num_format($expense->final_total)}}</td>
                                        <td>{{$expense->details}}</td>
                                        <td>
                                            @can('account_management.expenses.create_and_edit')
                                            <a href="{{action('ExpenseController@edit', $expense->id)}}"
                                                class="btn btn-danger text-white edit_job"><i
                                                    class="fa fa-pencil-square-o"></i></a>
                                            @endcan
                                            @can('account_management.expenses.delete')
                                            <a data-href="{{action('ExpenseController@destroy', $expense->id)}}"
                                                data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                class="btn btn-danger text-white delete_item"><i
                                                    class="fa fa-trash"></i></a>
                                            @endcan
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">@lang('lang.total')</th>
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
<script>
    $(document).on('click', '.print-invoice', function(){
     $.ajax({
         method: 'get',
         url: $(this).data('href'),
         data: {  },
         success: function(result) {
             if(result.success){
                 pos_print(result.html_content);
             }
         },
     });
 })
</script>
@endsection
