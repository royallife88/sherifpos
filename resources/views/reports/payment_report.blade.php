@extends('layouts.app')
@section('title', __('lang.payment_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.payment_report')</h4>
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
                        <a href="{{action('ReportController@getPaymentReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.date')</th>
                                <th>@lang('lang.payment_ref')</th>
                                <th>@lang('lang.sale_ref')</th>
                                <th>@lang('lang.purchase_ref')</th>
                                <th>@lang('lang.paid_by')</th>
                                <th  class="sum">@lang('lang.amount')</th>
                                <th>@lang('lang.created_by')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{@format_date($transaction->paid_on)}}</td>
                                <td>{{$transaction->ref_number}}</td>
                                <td>@if($transaction->type == 'sell'){{$transaction->invoice_no}}@endif</td>
                                <td>@if($transaction->type == 'add_stock'){{$transaction->invoice_no}}@endif</td>
                                <td>@if(!empty($payment_types[$transaction->method])){{$payment_types[$transaction->method]}}
                                    @endif</td>
                                <td>{{@num_format($transaction->amount)}}</td>
                                <td>{{ucfirst($transaction->created_by_name)}}</td>

                                @if($transaction->type == 'sell')
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

                                                <a data-href="{{action('SellController@print', $transaction->id)}}"
                                            class="btn print-invoice"><i class="dripicons-print"></i>
                                            @lang('lang.generate_invoice')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan --}}
                                            @can('sale.pos.view')
                                            <li>

                                                <a data-href="{{action('SellController@show', $transaction->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="fa fa-eye"></i> @lang('lang.view')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('sale.pos.create_and_edit')
                                            <li>

                                                <a href="{{action('SellController@edit', $transaction->id)}}" class="btn"><i
                                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('return.sell_return.create_and_edit')
                                            <li>
                                                <a href="{{action('SellReturnController@add', $transaction->id)}}"
                                                    class="btn"><i class="fa fa-undo"></i> @lang('lang.sale_return')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('sale.pay.create_and_edit')
                                            @if($transaction->payment_status != 'paid')
                                            <li>
                                                <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $transaction->id])}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="fa fa-plus"></i> @lang('lang.add_payment')</a>
                                            </li>
                                            @endif
                                            @endcan
                                            @can('sale.pay.view')
                                            @if($transaction->payment_status != 'pending')
                                            <li>
                                                <a data-href="{{action('TransactionPaymentController@show', $transaction->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="fa fa-money"></i> @lang('lang.view_payments')</a>
                                            </li>
                                            @endif
                                            @endcan
                                            @can('sale.pos.delete')
                                            <li>
                                                <a data-href="{{action('SellController@destroy', $transaction->id)}}"
                                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                    @lang('lang.delete')</a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                                @endif
                                @if($transaction->type == 'add_stock')
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
                                                <a href="{{action('AddStockController@show', $transaction->id)}}"
                                                    class=""><i class="fa fa-eye btn"></i>
                                                    @lang('lang.view')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('stock.add_stock.create_and_edit')
                                            <li>
                                                <a href="{{action('AddStockController@edit', $transaction->id)}}"><i
                                                        class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('stock.add_stock.delete')
                                            <li>
                                                <a data-href="{{action('AddStockController@destroy', $transaction->id)}}"
                                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                    class="btn text-red delete_item"><i class="dripicons-trash"></i>
                                                    @lang('lang.delete')</a>
                                            </li>
                                            @endcan
                                            @can('stock.pay.create_and_edit')
                                            @if($transaction->payment_status != 'paid')
                                            <li>
                                                <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $transaction->id])}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="fa fa-money"></i>
                                                    @lang('lang.pay')</a>
                                            </li>
                                            @endif
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                                @endif
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
