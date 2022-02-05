@extends('layouts.app')
@section('title', __('lang.quotation_list'))

@section('content')
<div class="container-fluid no-print">
    @can('sale.pos.create_and_edit')
    <a style="color: white" href="{{action('QuotationController@create')}}" class="btn btn-info"><i
            class="dripicons-plus"></i>
        @lang('lang.create_quotation')</a>
    @endcan
</div>
<br>
<div class="col-md-12 no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.quotation_list')</h4>
        </div>
        <div class="card-body">
            <form action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
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
                        <div class="form-group">
                            {!! Form::label('status', __('lang.status'), []) !!}
                            {!! Form::select('status', ['approved' => 'Approved', 'rejected' => 'Rejected', 'expired' =>
                            'Expired', 'valid' => 'Valid'], request()->status, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

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
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('QuotationController@index')}}"
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
                <th>@lang('lang.created_by')</th>
                <th>@lang('lang.customer')</th>
                <th>@lang('lang.store')</th>
                <th>@lang('lang.stock_status')</th>
                <th>@lang('lang.quotation_status')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{@format_date($sale->transaction_date)}}</td>
                <td>{{$sale->invoice_no}}</td>
                <td>{{ucfirst($sale->created_by_user->name ?? '')}}</td>
                <td>@if(!empty($sale->customer)){{$sale->customer->name}}@endif</td>
                <td>{{ucfirst($sale->store->name ?? '')}}</td>
                <td>@if(!empty($sale->block_qty)) @lang('lang.blocked') @else @lang('lang.not_blocked')@endif</td>
                <td>{{ucfirst($sale->status)}}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @if($sale->status != 'expired')
                            @can('sale.sale.create_and_edit')
                            <li>
                                <a href="{{action('SellController@edit', $sale->id)}}" class="btn print-invoice"><i
                                        class="dripicons-document"></i> @lang('lang.create_invoice')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @endif
                            @can('squotation_for_customers.quotation.view')
                            <li>

                                <a data-href="{{action('SellController@print', $sale->id)}}"
                                    class="btn print-invoice"><i class="dripicons-print"></i>
                                    @lang('lang.print')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('squotation_for_customers.quotation.view')
                            <li>

                                <a data-href="{{action('QuotationController@show', $sale->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-eye"></i>
                                    @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('squotation_for_customers.quotation.create_and_edit')
                            <li>

                                <a href="{{action('QuotationController@edit', $sale->id)}}" class="btn"><i
                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan

                            @can('squotation_for_customers.quotation.delete')
                            <li>
                                <a data-href="{{action('QuotationController@destroy', $sale->id)}}"
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

        </tfoot>
    </table>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script>
    $(document).on('click', '.print-invoice', function(){
        $('.view_modal').modal('hide');
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
