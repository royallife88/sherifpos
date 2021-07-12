@extends('layouts.app')
@section('title', __('lang.sales_promotion_formal_discount'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_sales_promotion')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('SalesPromotionController@update', $sales_promotion->id), 'id'
                        => 'customer-type-form',
                        'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                                    {!! Form::text('name', $sales_promotion->name, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store_ids', __( 'lang.store' ) . ':*') !!}
                                    {!! Form::select('store_ids[]', $stores, $sales_promotion->store_ids, ['class' =>
                                    'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_type_ids', __( 'lang.customer_type' ) . ':*') !!}
                                    {!! Form::select('customer_type_ids[]', $customer_types,
                                    $sales_promotion->customer_type_ids, ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <input id="product_condition" name="product_condition" @if($sales_promotion->product_condition) checked @endif type="checkbox" value="1"
                                            class="form-control-custom">
                                        <label for="product_condition"><strong>@lang('lang.product_condition')</strong></label>
                                    </div>
                                    {!! Form::select('product_ids[]', $products, $sales_promotion->product_ids,
                                    ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <input id="purchase_condition" name="purchase_condition" @if($sales_promotion->purchase_condition) checked @endif type="checkbox" value="1"
                                            class="form-control-custom">
                                        <label for="purchase_condition"><strong>@lang('lang.purchase_condition')</strong></label>
                                    </div>
                                    {!! Form::text('purchase_condition_amount', @num_format($sales_promotion->purchase_condition_amount), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __( 'lang.discount_type' ) . ':*') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], $sales_promotion->discount_type,['class' => 'form-control selecpicker', 'required', 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __( 'lang.discount' ) . ':*') !!}
                                    {!! Form::text('discount_value', @num_format($sales_promotion->discount_value), ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}
                                    {!! Form::date('start_date', $sales_promotion->start_date, ['class' =>
                                    'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', __( 'lang.end_date' ) . ':') !!}
                                    {!! Form::date('end_date', $sales_promotion->end_date, ['class' => 'form-control'])
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
