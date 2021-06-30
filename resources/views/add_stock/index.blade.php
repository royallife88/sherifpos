@extends('layouts.app')
@section('title', __('lang.add_stock'))

@section('content')
<section class="">
    <div class="col-md-12 card pt-3 pb-3">
        <form action="">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                        {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' =>
                        'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>

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
                    <br>
                    <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                    <a href="{{action('AddStockController@index')}}"
                        class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                </div>

            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.po_ref_no')</th>
                    <th>@lang('lang.invoice_no')</th>
                    <th>@lang('lang.date_and_time')</th>
                    <th>@lang('lang.invoice_date')</th>
                    <th>@lang('lang.supplier')</th>
                    <th>@lang('lang.value')</th>
                    <th>@lang('lang.created_by')</th>
                    <th>@lang('lang.paid_amount')</th>
                    <th>@lang('lang.pending_amount')</th>
                    <th>@lang('lang.due_date')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($add_stocks as $add_stock)
                <tr>
                    <td>@if(!empty($add_stock->po_no)&& !empty($add_stock->purchase_order_id))<a href="{{action('PurchaseOrderController@show', $add_stock->purchase_order_id)}}">{{$add_stock->po_no}}</a> @endif</td>
                    <td>{{$add_stock->invoice_no}}</td>
                    <td> {{\Carbon\Carbon::parse($add_stock->created_at)->format('m/d/Y H:i:s')}}</td>
                    <td> {{@format_date($add_stock->transaction_date)}}</td>
                    <td>
                        {{$add_stock->supplier->name}}
                    </td>
                    <td>
                        {{@num_format($add_stock->final_total)}}
                    </td>
                    <td>
                        {{ucfirst($add_stock->created_by_user->name)}}
                    </td>
                    <td>
                        {{@num_format($add_stock->transaction_payments->sum('amount'))}}
                    </td>
                    <td>
                        {{@num_format($add_stock->final_total - $add_stock->transaction_payments->sum('amount'))}}
                    </td>
                    <td>@if(!empty($add_stock->due_date) && $add_stock->payment_status != 'paid') {{@format_date($add_stock->due_date)}} @endif</td>
                    <td>

                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @can('add_stock.add_stock.view')
                                <li>
                                    <a href="{{action('AddStockController@show', $add_stock->id)}}"
                                        class=""><i class="fa fa-eye btn"></i> @lang('lang.view')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('add_stock.add_stock.create_and_edit')
                                <li>
                                    <a href="{{action('AddStockController@edit', $add_stock->id)}}"><i
                                        class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('add_stock.add_stock.delete')
                                <li>
                                    <a data-href="{{action('AddStockController@destroy', $add_stock->id)}}"
                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                        class="btn text-red delete_item"><i class="dripicons-trash"></i> @lang('lang.delete')</a>
                                </li>
                                @endcan
                                @can('add_stock.pay.create_and_edit')
                                @if($add_stock->payment_status != 'paid')
                                <li>
                                    <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $add_stock->id])}}" data-container=".view_modal"
                                        class="btn btn-modal"><i class="fa fa-money"></i> @lang('lang.pay')</a>
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

            </tfoot>
        </table>

    </div>



</section>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
