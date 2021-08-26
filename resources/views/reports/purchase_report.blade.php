@extends('layouts.app')
@section('title', __('lang.purchase_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.purchase_report')</h4>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('pos_id', __('lang.pos'), []) !!}
                            {!! Form::select('pos_id', $store_pos, request()->pos_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    @endif
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
                        <a href="{{action('ReportController@getPurchaseReport')}}"
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
                                <th>@lang('lang.product_name')</th>
                                <th>@lang('lang.purchased_amount')</th>
                                <th>@lang('lang.purchased_qty')</th>
                                <th>@lang('lang.in_stock')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $purchased_amount = 0;
                                $purchased_qty = 0;
                                $in_stock = 0;
                            @endphp
                            @foreach ($products as $key => $value)
                            @if(!empty($transactions[$key]))
                            <tr>
                                <td>{{$value}}</td>
                                <td> {{@num_format($transactions[$key]->total_purchase)}}</td>
                                <td> {{@num_format($transactions[$key]->total_qty)}}</td>
                                <td> {{@num_format($transactions[$key]->in_stock)}}</td>
                            </tr>
                            @php
                                $purchased_amount += $transactions[$key]->total_purchase;
                                $purchased_qty += $transactions[$key]->total_qty;
                                $in_stock += $transactions[$key]->in_stock;
                            @endphp
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="" style="text-align: right">@lang('lang.total')</th>
                                <th>{{@num_format($purchased_amount)}}</th>
                                <th>{{@num_format($purchased_qty)}}</th>
                                <th>{{@num_format($in_stock)}}</th>
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
