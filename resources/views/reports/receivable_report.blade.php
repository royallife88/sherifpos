@extends('layouts.app')
@section('title', __('lang.receivable_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.receivable_report')</h4>
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
                            {!! Form::select('customer_id', $customers, request()->customer_id, ['class'
                            =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_type_id', __('lang.customer_type'), []) !!}
                            {!! Form::select('customer_type_id', $customer_types, request()->customer_type_id, ['class'
                            =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
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
                        <a href="{{action('ReportController@getReceivableReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="sales_table" class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.date')</th>
                                <th>@lang('lang.reference')</th>
                                <th>@lang('lang.customer')</th>
                                <th>@lang('lang.sale_status')</th>
                                <th>@lang('lang.payment_status')</th>
                                <th>@lang('lang.grand_total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total_paid = 0;
                            $total_due = 0;
                            @endphp
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{@format_date($sale->transaction_date)}}</td>
                                <td>{{$sale->invoice_no}}</td>
                                <td>@if(!empty($sale->customer)){{$sale->customer->name}}@endif</td>
                                <td>{{ucfirst($sale->status)}}</td>
                                <td>@if(!empty($payment_status_array[$sale->payment_status])){{$payment_status_array[$sale->payment_status]}}@endif
                                </td>
                                <td>{{@num_format($sale->final_total)}}</td>
                                </td>
                            </tr>
                            @php
                            $total_paid += $sale->transaction_payments->sum('amount');
                            $total_due += $sale->final_total - $sale->transaction_payments->sum('amount');
                            @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" style="text-align: right">@lang('lang.totals')</th>
                                <td>{{@num_format($sales->sum('final_total'))}}</td>
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

@endsection