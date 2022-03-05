@extends('layouts.app')
@section('title', __('lang.delivery_list'))

@section('content')

<br>
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.delivery_list')</h4>
        </div>
        <div class="card-body">
            <form action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('lang.customer'), []) !!}
                            {!! Form::select('customer_id', $customers, request()->customer_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('status', __('lang.status'), []) !!}
                            {!! Form::select('status', ['final' => 'Completed', 'pending' => 'Pending'], request()->status, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('payment_status', __('lang.payment_status'), []) !!}
                            {!! Form::select('payment_status', $payment_status_array, request()->payment_status, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('start_date', __('lang.start_date'), []) !!}
                            {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                            {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('SellController@index')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="table-responsive no-print">
    <table id="sales_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.date')</th>
                <th>@lang('lang.reference')</th>
                <th>@lang('lang.customer')</th>
                <th>@lang('lang.sale_status')</th>
                <th>@lang('lang.delivery_status')</th>
                <th>@lang('lang.payment_status')</th>
                <th class="sum">@lang('lang.grand_total')</th>
                <th class="sum">@lang('lang.paid')</th>
                <th class="sum">@lang('lang.due')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{@format_date($sale->transaction_date)}}</td>
                <td>{{$sale->invoice_no}} @if(!empty($sale->return_parent))<a data-href="{{action('SellReturnController@show', $sale->id)}}"
                    data-container=".view_modal" class="btn btn-modal" style="color: #007bff;">R</a>@endif</td>
                <td>@if(!empty($sale->customer)){{$sale->customer->name}}@endif</td>
                <td>{{ucfirst($sale->status)}}</td>
                <td>{{ucfirst($sale->delivery_status)}}</td>
                <td>@if(!empty($payment_status_array[$sale->payment_status])){{$payment_status_array[$sale->payment_status]}}@endif</td>
                <td>{{@num_format($sale->final_total)}}</td>
                <td>{{@num_format($sale->transaction_payments->sum('amount'))}}</td>
                <td>{{@num_format($sale->final_total - $sale->transaction_payments->sum('amount'))}}</td>

                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('sale.pos.create_and_edit')
                            <li>

                                <a data-href="{{action('SellController@print', $sale->id)}}"
                                     class="btn print-invoice"><i
                                        class="dripicons-print"></i> @lang('lang.generate_invoice')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
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
                                     class="btn"><i
                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('sale.pay.create_and_edit')
                            @if($sale->payment_status != 'paid')
                            <li>
                                <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $sale->id])}}" data-container=".view_modal"
                                    class="btn btn-modal"><i class="fa fa-plus"></i> @lang('lang.add_payment')</a>
                            </li>
                            @endif
                            @endcan
                            @can('sale.pay.view')
                            @if($sale->payment_status != 'pending')
                            <li>
                                <a data-href="{{action('TransactionPaymentController@show', $sale->id)}}" data-container=".view_modal"
                                    class="btn btn-modal"><i class="fa fa-money"></i> @lang('lang.view_payments')</a>
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
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th style="text-align: right">@lang('lang.totals')</th>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
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

    function pos_print(receipt) {
        $("#receipt_section").html(receipt);
        __currency_convert_recursively($("#receipt_section"));
        __print_receipt("receipt_section");
    }

    table
    .column( '0:visible' )
    .order( 'desc' )
    .draw();
</script>
@endsection
