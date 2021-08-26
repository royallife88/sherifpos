@extends('layouts.app')
@section('title', __('lang.store_stock_chart'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.store_stock_chart')</h4>
        </div>
        @if(session('user.is_superadmin'))
        <form action="">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getStoreStockChart')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        @endif
        <div class="col-md-12">
            <div class="col-md-6 offset-md-3 mt-3 mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <span>Total @lang('lang.items')</span>
                        <h2><strong>{{@num_format($total_item)}}</strong></h2>
                    </div>
                    <div class="col-md-6">
                        <span>Total @lang('lang.quantity')</span>
                        <h2><strong>{{@num_format($total_qty)}}</strong></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @php
                $color = '#733686';
                $color_rgba = 'rgba(115, 54, 134, 0.8)';

                @endphp
                <div class="col-md-5 offset-md-3 mt-2">
                    <div class="pie-chart">
                        <canvas id="pieChart" data-color="{{$color}}" data-color_rgba="{{$color_rgba}}"
                            data-price={{$total_price}} data-cost={{$total_cost}} width="5" height="5"
                            data-label1="@lang('lang.stock_value_by_price')"
                            data-label2="@lang('lang.stock_value_by_cost')" data-label3="@lang('lang.estimate_profit')">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
