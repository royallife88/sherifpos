@extends('layouts.app')
@section('title', __('lang.monthly_purchase_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.monthly_purchase_report')</h4>
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
                        <a href="{{action('ReportController@getMonthlyPurchaseReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        @endif
        <div class="card-body">
            <div class="col-md-12">
                <table class="table table-bordered"
                    style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                    <thead>
                        @php
                        $next_year = $year + 1;
                        $pre_year = $year - 1;
                        @endphp
                        <tr>
                            <th><a href="{{url('report/get-monthly-purchase-report?year='.$pre_year)}}"><i
                                        class="fa fa-arrow-left"></i> {{trans('lang.previous')}}</a></th>
                            <th colspan="10" class="text-center">{{$year}}</th>
                            <th><a href="{{url('report/get-monthly-purchase-report?year='.$next_year)}}">{{trans('lang.next')}}
                                    <i class="fa fa-arrow-right"></i></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>@lang('lang.January')</strong></td>
                            <td><strong>@lang('lang.February')</strong></td>
                            <td><strong>@lang('lang.March')</strong></td>
                            <td><strong>@lang('lang.April')</strong></td>
                            <td><strong>@lang('lang.May')</strong></td>
                            <td><strong>@lang('lang.June')</strong></td>
                            <td><strong>@lang('lang.July')</strong></td>
                            <td><strong>@lang('lang.August')</strong></td>
                            <td><strong>@lang('lang.September')</strong></td>
                            <td><strong>@lang('lang.October')</strong></td>
                            <td><strong>@lang('lang.November')</strong></td>
                            <td><strong>@lang('lang.December')</strong></td>
                        </tr>
                        <tr>
                            @foreach($total_discount as $key => $discount)
                            <td>
                                @if($discount > 0)
                                <strong>{{trans("lang.product_discount")}}</strong><br>
                                <span>{{@num_format($discount)}}</span><br><br>
                                @endif
                                @if($total_tax[$key] > 0)
                                <strong>{{trans("lang.product_tax")}}</strong><br>
                                <span>{{@num_format($total_tax[$key])}}</span><br><br>
                                @endif
                                @if($shipping_cost[$key] > 0)
                                <strong>{{trans("lang.delivery_cost")}}</strong><br>
                                <span>{{@num_format($shipping_cost[$key])}}</span><br><br>
                                @endif
                                @if($total[$key] > 0)
                                <strong>{{trans("lang.grand_total")}}</strong><br>
                                <span>{{@num_format($total[$key])}}</span><br>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
