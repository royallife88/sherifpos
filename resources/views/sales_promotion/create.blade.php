@extends('layouts.app')
@section('title', __('lang.sales_promotion_formal_discount'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_sales_promotion_formal_discount')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('SalesPromotionController@store'), 'id' => 'customer-type-form',
                        'method' =>
                        'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
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
                                    {!! Form::select('customer_type_ids[]', $customer_types, false, ['class' =>
                                    'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="i-checks" style="margin-top: 30px">
                                                <input id="product_condition" name="product_condition" type="checkbox"
                                                    value="1" class="form-control-custom">
                                                <label
                                                    for="product_condition"><strong>@lang('lang.product_condition')</strong></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @include('product_classification_tree.partials.product_selection_tree')
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <input id="purchase_condition" name="purchase_condition" type="checkbox"
                                            value="1" class="form-control-custom">
                                        <label
                                            for="purchase_condition"><strong>@lang('lang.purchase_condition')</strong></label>
                                    </div>
                                    {!! Form::text('purchase_condition_amount', 0, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __( 'lang.discount_type' ) . ':*') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' =>
                                    'Percentage'], false,['class' => 'form-control selecpicker', 'required',
                                    'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __( 'lang.discount' ) . ':*') !!}
                                    {!! Form::text('discount_value', 0, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}
                                    {!! Form::date('start_date', 1, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', __( 'lang.end_date' ) . ':') !!}
                                    {!! Form::date('end_date', 1, ['class' => 'form-control', 'required']) !!}
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
<script src="{{asset('js/product_selection_tree.js')}}"></script>
<script type="text/javascript">
    $('.selectpicker').selectpicker('selectAll');
</script>
@endsection
