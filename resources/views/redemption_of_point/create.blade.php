@extends('layouts.app')
@section('title', __('lang.redemption_of_point_system'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_redemption_of_point_system')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('RedemptionOfPointController@store'), 'id' => 'customer-type-form',
                        'method' =>
                        'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store_ids', __( 'lang.store' ) . ':*') !!}
                                    {!! Form::select('store_ids[]', $stores, false, ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_type_ids', __( 'lang.customer_type' ) . ':*') !!}
                                    {!! Form::select('customer_type_ids[]', $customer_types, false, ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('product_ids', __( 'lang.product' ) . ':*') !!}
                                    {!! Form::select('product_ids[]', $products, false, ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('value_of_1000_points', __( 'lang.value_of_1000_points' ) . ':*') !!}
                                    {!! Form::text('value_of_1000_points', 1, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}
                                    {!! Form::date('start_date', 1, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', __( 'lang.end_date' ) . ':') !!}
                                    {!! Form::date('end_date', 1, ['class' => 'form-control']) !!}
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
   $('.selectpicker').selectpicker('selectAll');
</script>
@endsection
