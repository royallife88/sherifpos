@extends('layouts.app')
@section('title', __('lang.profit_loss_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.profit_loss_report')</h4>
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
                        <div class="form-group">
                            {!! Form::label('employee_id', __('lang.employee'), []) !!}
                            {!! Form::select('employee_id', $employees, request()->employee_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">@lang('lang.wages_type')</label>
                            {!! Form::select('payment_type', $wages_payment_types, null, ['class' =>
                            'form-control', 'placeholder'
                            => __('lang.all')]) !!}
                        </div>
                    </div>


                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getProfitLoss')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <h4>@lang('lang.income')</h4>
                <div class="table-responsive">
                    <table id="store_table" class="table">
                        <thead>
                            <tr>
                                <th>@lang('lang.income')</th>
                                <th>@lang('lang.amount')</th>
                                <th>@lang('lang.information')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{$sale->store_name}}</td>
                                <td>{{@num_format($sale->total_amount)}}</td>
                                <td>
                                    <a href="{{action('SellController@index')}}?store_id={{$sale->store_id}}"
                                        class="btn btn-primary">@lang('lang.details')</a>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <label for="">@lang('lang.total_income'): {{@num_format($sales->sum('total_amount'))}}</label>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <h4>@lang('lang.expendatures')</h4>
                <div class="table-responsive">
                    <table id="store_table" class="table">
                        <thead>
                            <tr>
                                <th>@lang('lang.expense')</th>
                                <th>@lang('lang.amount')</th>
                                <th>@lang('lang.information')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                            <tr>
                                <td>{{$expense->expense_category_name}}</td>
                                <td>{{@num_format($expense->total_amount)}}</td>
                                <td>
                                    <a href="{{action('ExpenseController@index')}}?expense_category_id={{$expense->expense_category_id}}"
                                        class="btn btn-primary">@lang('lang.details')</a>
                                </td>
                            </tr>
                            @endforeach
                            @foreach($wages as $wage)
                            <tr>
                                <td>{{ucfirst($wages_payment_types[$wage->payment_type])}}</td>
                                <td>{{@num_format($wage->total_amount)}}</td>
                                <td>
                                    <a href="{{action('WagesAndCompensationController@index')}}?payment_type={{$wage->payment_type}}"
                                        class="btn btn-primary">@lang('lang.details')</a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <label for="">@lang('lang.total_expenses'): {{@num_format($expenses->sum('total_amount') + $wages->sum('total_amount'))}}</label>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <h2><b>@lang('lang.profit_and_loss'):
                        {{@num_format($sales->sum('total_amount') - $expenses->sum('total_amount') + $wages->sum('total_amount'))}}</b></h2>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection