@extends('layouts.app')
@section('title', __('lang.supplier'))
@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.edit_supplier')</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                            {!! Form::open(['url' => action('SupplierController@update', $supplier->id), 'id' => 'supplier-form', 'method' => 'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('supplier_category_id', __('lang.category') . ':*') !!}
                                        {!! Form::select('supplier_category_id', $supplier_categories, $supplier->supplier_category_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select'), 'required', 'id' => 'supplier_category_id']) !!}

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('name', __('lang.representative_name') . ':*') !!}
                                        {!! Form::text('name', $supplier->name, ['class' => 'form-control', 'placeholder' => __('lang.name'), 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('products', __('lang.products')) !!}
                                        {!! Form::select('products[]', $products, $supplier->supplier_products->pluck('product_id'), ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'required', 'id' => 'products', 'multiple']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('photo', __('lang.photo') . ':') !!} <br>
                                        {!! Form::file('image', ['class']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('company_name', __('lang.company_name') . ':') !!}
                                        {!! Form::text('company_name', $supplier->company_name, ['class' => 'form-control', 'placeholder' => __('lang.company_name')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('vat_number', __('lang.vat_number') . ':') !!}
                                        {!! Form::text('vat_number', $supplier->vat_number, ['class' => 'form-control', 'placeholder' => __('lang.vat_number')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('email', __('lang.email') . ':') !!}
                                        {!! Form::email('email', $supplier->email, ['class' => 'form-control', 'placeholder' => __('lang.email')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('mobile_number', __('lang.mobile_number') . ':') !!}
                                        {!! Form::text('mobile_number', $supplier->mobile_number, ['class' => 'form-control', 'placeholder' => __('lang.mobile_number')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('address', __('lang.address') . ':') !!}
                                        {!! Form::text('address', $supplier->address, ['class' => 'form-control', 'placeholder' => __('lang.balance')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('city', __('lang.city') . ':') !!}
                                        {!! Form::text('city', $supplier->city, ['class' => 'form-control', 'placeholder' => __('lang.balance')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('state', __('lang.state') . ':') !!}
                                        {!! Form::text('state', $supplier->state, ['class' => 'form-control', 'placeholder' => __('lang.balance')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('postal_code', __('lang.postal_code') . ':') !!}
                                        {!! Form::text('postal_code', $supplier->postal_code, ['class' => 'form-control', 'placeholder' => __('lang.balance')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('country ', __('lang.country') . ':') !!}
                                        {!! Form::text('country ', $supplier->country, ['class' => 'form-control', 'placeholder' => __('lang.balance')]) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="submit" value="{{ trans('lang.submit') }}" id="submit-btn"
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
        $('#supplier-type-form').submit(function() {
            $(this).validate();
            if ($(this).valid()) {
                $(this).submit();
            }
        })
    </script>
@endsection
