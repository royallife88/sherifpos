@extends('layouts.app')
@section('title', __('lang.earning_of_point_system'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_earning_of_point_system')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('EarningOfPointController@update', $earning_of_point->id), 'id'
                        => 'customer-type-form',
                        'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store_ids', __( 'lang.store' ) . ':*') !!}
                                    {!! Form::select('store_ids[]', $stores, $earning_of_point->store_ids, ['class' =>
                                    'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_type_ids', __( 'lang.customer_type' ) . ':*') !!}
                                    {!! Form::select('customer_type_ids[]', $customer_types,
                                    $earning_of_point->customer_type_ids, ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('product_ids', __( 'lang.product' ) . ':*') !!}
                                    {!! Form::select('product_ids[]', $products, $earning_of_point->product_ids,
                                    ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('points_on_per_amount', __( 'lang.points_on_per_amount_sale' ) .
                                    ':*') !!} <i class="dripicons-question" data-toggle="tooltip"
                                        title="@lang('lang.points_on_per_amount_info')"></i>
                                    {!! Form::text('points_on_per_amount', $earning_of_point->points_on_per_amount,
                                    ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}
                                    {!! Form::date('start_date', $earning_of_point->start_date, ['class' =>
                                    'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', __( 'lang.end_date' ) . ':') !!}
                                    {!! Form::date('end_date', $earning_of_point->end_date, ['class' => 'form-control'])
                                    !!}
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="submit" value="{{trans('lang.submit')}}" id="submit-btn"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
</script>
@endsection
