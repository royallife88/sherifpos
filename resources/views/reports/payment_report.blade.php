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
                                <th>@lang('lang.amount')</th>
                                <th>@lang('lang.created_by')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{@format_date($transaction->paid_on)}}</td>
                                <td>{{$transaction->ref_number}}</td>
                                <td>@if($transaction->type == 'sell'){{$transaction->invoice_no}}@endif</td>
                                <td>@if($transaction->type == 'add_stock'){{$transaction->invoice_no}}@endif</td>
                                <td>@if(!empty($payment_types[$transaction->method])){{$payment_types[$transaction->method]}} @endif</td>
                                <td>{{@num_format($transaction->amount)}}</td>
                                <td>{{ucfirst($transaction->created_by_name)}}</td>
                            </tr>

                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                                <th>{{@num_format($transactions->sum('amount'))}}</th>
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
