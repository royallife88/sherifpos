@extends('layouts.app')
@section('title', __('lang.due_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.due_report')</h4>
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
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getDueReport')}}"
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
                                <th>@lang('lang.reference')</th>
                                <th>@lang('lang.customer')</th>
                                <th>@lang('lang.amount')</th>
                                <th>@lang('lang.paid')</th>
                                <th>@lang('lang.due')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $total_paid = 0;
                                $total_due = 0;
                            @endphp
                            @foreach ($dues as $due)
                            <tr>
                                <td>{{@format_date($due->transaction_date)}}</td>
                                <td> {{$due->invoice_no}}</td>
                                <td> {{$due->customer->name}}</td>
                                <td> {{@num_format($due->final_total)}}</td>
                                <td> {{@num_format($due->transaction_payments->sum('amount'))}}</td>
                                <td> {{@num_format($due->final_total - $due->transaction_payments->sum('amount'))}}</td>
                            </tr>
                            @php
                                $total_paid += $due->transaction_payments->sum('amount');
                                $total_due += $due->final_total - $due->transaction_payments->sum('amount');
                            @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right">@lang('lang.total')</th>
                                <td>{{@num_format($dues->sum('final_total'))}}</td>
                                <td>{{@num_format($total_paid)}}</td>
                                <td>{{@num_format($total_due)}}</td>
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
