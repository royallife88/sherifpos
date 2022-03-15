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
                            {!! Form::open(['url' => action('SalesPromotionController@store'), 'id' => 'customer-type-form', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('name', __('lang.name') . ':*') !!}
                                        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('store_ids', __('lang.store') . ':*') !!}
                                        {!! Form::select('store_ids[]', $stores, false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'data-actions-box' => 'true', 'multiple', 'required', 'id' => 'store_ids']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('customer_type_ids', __('lang.customer_type') . ':*') !!}
                                        {!! Form::select('customer_type_ids[]', $customer_types, false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'data-actions-box' => 'true', 'multiple', 'required',]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('type', __('lang.type') . ':*') !!}
                                        {!! Form::select('type', ['item_discount' => __('lang.item_discount'), 'package_promotion' => __('lang.package_promotion')], false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'required',]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-6">
                                        @include('product_classification_tree.partials.product_selection_tree')
                                    </div>
                                </div>
                                <div class="col-md-4 product_condition_div hide">
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
                                            @include('sales_promotion.partials.product_condition_tree')
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 product_details_div mt-5 mb-5">
                                    <table class="table" id="sale_promotion_table">
                                        <thead class="bg-success" style="color: white">
                                            <tr>
                                                <th>@lang('lang.image')</th>
                                                <th>@lang('lang.name')</th>
                                                <th>@lang('lang.sku')</th>
                                                <th class="sum">@lang('lang.purchase_price')</th>
                                                <th class="sum">@lang('lang.sell_price')</th>
                                                <th>@lang('lang.stock')</th>
                                                <th>@lang('lang.expiry_date')</th>
                                                <th>@lang('lang.date_of_purchase')</th>
                                                <th class="qty_hide hide">@lang('lang.qty')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align: right">@lang('lang.total')</th>
                                                <td class="footer_purchase_price_total"></td>
                                                <td class="footer_sell_price_total"></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                        <input type="hidden" name="actual_sell_price" id="actual_sell_price" value="0">
                                    </table>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('discount_type', __('lang.discount_type') . ':*') !!}
                                        {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], false, ['class' => 'form-control selecpicker', 'required', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('discount_value', __('lang.discount') . ':*') !!}
                                        {!! Form::text('discount_value', 0, ['class' => 'form-control', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="" style="margin-top: 40px;" class="new_price hide">@lang('lang.new_price'):
                                        <span class="new_price_span">{{ @num_format(0) }}</span></label>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('start_date', __('lang.start_date') . ':') !!}
                                        {!! Form::text('start_date', null, ['class' => 'form-control datepicker', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('end_date', __('lang.end_date') . ':') !!}
                                        {!! Form::text('end_date', null, ['class' => 'form-control datepicker', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 mt-5">
                                    <div class="form-group">
                                        <div class="i-checks">
                                            <input id="generate_barcode" name="generate_barcode" type="checkbox" value="1"
                                                class="form-control-custom">
                                            <label
                                                for="generate_barcode"><strong>@lang('lang.generate_barcode')</strong></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
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
    <script src="{{ asset('js/product_selection_tree.js') }}"></script>
    <script src="{{ asset('js/product_condition_tree.js') }}"></script>
    <script type="text/javascript">
        $('.selectpicker').selectpicker('selectAll');

        $(document).on('change', '#type', function() {
            if ($(this).val() === 'package_promotion') {
                $('.new_price').removeClass('hide');
            } else {
                $('.new_price').addClass('hide');
                $('.qty').val(1);
            }
        })
        $(document).on('change', '#discount_type, #discount_value', function() {
            let type = $('#type').val()
            let discount_type = $('#discount_type').val();
            let discount_value = __read_number($('#discount_value'))

            let new_price = 0;
            if (type == 'package_promotion') {
                if (discount_type == 'fixed') {
                    new_price = discount_value;
                }
                if (discount_type == 'percentage') {
                    let actual_sell_price = __read_number($('#actual_sell_price'))
                    new_price = (actual_sell_price * discount_value) / 100;
                }
            }
            $('.new_price_span').text(__currency_trans_from_en(new_price, false))

        })
    </script>
@endsection
