@extends('layouts.app')
@section('title', __('lang.sale_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.sale_report')</h4>
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
                        <a href="{{action('ReportController@getSaleReport')}}"
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
                                <th class="sum">@lang('lang.sold_amount')</th>
                                <th class="sum">@lang('lang.sold_qty')</th>
                                <th class="sum">@lang('lang.in_stock')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->product_name}}</td>
                                <td> {{@num_format($transaction->sold_amount)}}</td>
                                <td> {{@num_format($transaction->sold_qty)}}</td>
                                <td> {{@num_format($transaction->in_stock)}}</td>
                            </tr>

                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="text-align: right">@lang('lang.total')</th>
                                <th></th>
                                <th></th>
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

@endsection
