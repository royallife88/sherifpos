@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.edit_product')</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                            {!! Form::open(['url' => action('ProductController@update', $product->id), 'id' => 'product-edit-form', 'method' => 'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="is_service" name="is_service" type="checkbox"
                                            @if (!empty($product->is_service)) checked @endif value="1"
                                            class="form-control-custom">
                                        <label for="is_service"><strong>
                                                @if (session('system_mode') == 'restaurant')
                                                    @lang('lang.or_add_new_product')
                                                @else
                                                    @lang('lang.add_new_service')
                                                @endif
                                            </strong></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="active" name="active" type="checkbox"
                                            @if (!empty($product->active)) checked @endif value="1"
                                            class="form-control-custom">
                                        <label for="active"><strong>
                                                @lang('lang.active')
                                            </strong></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group supplier_div @if (empty($product->is_service)) hide @endif">
                                        {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('supplier_id', $suppliers, !empty($product->supplier) ? $product->supplier->id : false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            <span class="input-group-btn">
                                                @can('supplier_module.supplier.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('SupplierController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    @if (session('system_mode') == 'restaurant')
                                        {!! Form::label('product_class_id', __('lang.category') . ' *', []) !!}
                                    @else
                                        {!! Form::label('product_class_id', __('lang.class') . ' *', []) !!}
                                    @endif
                                    <div class="input-group my-group">
                                        {!! Form::select('product_class_id', $product_classes, $product->product_class_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                        <span class="input-group-btn">
                                            @can('product_module.product_class.create_and_edit')
                                                <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{ action('ProductClassController@create') }}?quick_add=1"
                                                    data-container=".view_modal"><i
                                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                    <div class="error-msg text-red"></div>
                                </div>
                                @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                    <div class="col-md-4">
                                        {!! Form::label('category_id', __('lang.category') . ' *', []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('category_id', $categories, $product->category_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.category.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('CategoryController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                        <div class="error-msg text-red"></div>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('sub_category_id', __('lang.sub_category') . ' *', []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('sub_category_id', $sub_categories, $product->sub_category_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.sub_category.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('CategoryController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                        <div class="error-msg text-red"></div>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('brand_id', __('lang.brand') . ' *', []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('brand_id', $brands, $product->brand_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.brand.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('BrandController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                        <div class="error-msg text-red"></div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('name', __('lang.name') . ' *', []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::text('name', $product->name, ['class' => 'form-control', 'required', 'placeholder' => __('lang.name')]) !!}
                                            <span class="input-group-btn">
                                                <button type="button"
                                                    class="btn btn-default bg-white btn-flat translation_btn" type="button"
                                                    data-type="product"><i
                                                        class="dripicons-web text-primary fa-lg"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    @include('layouts.partials.translation_inputs', [
                                        'attribute' => 'name',
                                        'translations' => $product->translations,
                                        'type' => 'product',
                                    ])
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('sku', __('lang.sku') . ' *', []) !!}
                                        {!! Form::text('sku', $product->sku, ['class' => 'form-control', 'required', 'placeholder' => __('lang.sku')]) !!}
                                    </div>
                                </div>
                                @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                    <div class="col-md-4">
                                        {!! Form::label('multiple_units', __('lang.unit'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('multiple_units[]', $units, $product->multiple_units, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'disabled' => $product->type == 'variable' ? true : false, 'style' => 'width: 80%', 'multiple', 'id' => 'multiple_units']) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.unit.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('UnitController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('multiple_colors', __('lang.color'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('multiple_colors[]', $colors, $product->multiple_colors, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'disabled' => $product->type == 'variable' ? true : false, 'style' => 'width: 80%', 'multiple', 'id' => 'multiple_colors']) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.color.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('ColorController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    {!! Form::label('multiple_sizes', __('lang.size'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('multiple_sizes[]', $sizes, $product->multiple_sizes, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'disabled' => $product->type == 'variable' ? true : false, 'style' => 'width: 80%', 'multiple', 'id' => 'multiple_sizes']) !!}
                                        <span class="input-group-btn">
                                            @can('product_module.size.create_and_edit')
                                                <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{ action('SizeController@create') }}?quick_add=1"
                                                    data-container=".view_modal"><i
                                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                </div>
                                @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                    <div class="col-md-4">
                                        {!! Form::label('multiple_grades', __('lang.grade'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('multiple_grades[]', $grades, $product->multiple_grades, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'disabled' => $product->type == 'variable' ? true : false, 'style' => 'width: 80%', 'multiple', 'id' => 'multiple_grades']) !!}
                                            <span class="input-group-btn">
                                                @can('product_module.grade.create_and_edit')
                                                    <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('GradeController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-12 mt-3">
                                    @if (!empty($product->getFirstMediaUrl('product')))
                                        <div style="width: 120px;" class="images_div">
                                            <button type="button" class="delete-image btn btn-danger btn-xs"
                                                data-href="{{ action('ProductController@deleteProductImage', $product->id) }}"
                                                style="margin-left: 100px; border-radius: 50%"><i
                                                    class="fa fa-times"></i></button>
                                            <img src="@if (!empty($product->getFirstMediaUrl('product'))) {{ $product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                                alt="photo" style="width: 120px;">
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12 " style="margin-top: 10px;">
                                    <div class="dropzone" id="my-dropzone">
                                        <div class="dz-message" data-dz-message>
                                            <span>@lang('lang.drop_file_here_to_upload')</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        @if (session('system_mode') == 'restaurant')
                                            {!! Form::label('recipe', __('lang.recipe'), []) !!}
                                        @else
                                            <label>@lang('lang.product_details')</label>
                                        @endif
                                        <button type="button" class="translation_textarea_btn btn btn-sm"><i
                                                class="dripicons-web text-primary fa-lg"></i></button>
                                        <textarea name="product_details" id="product_details" class="form-control"
                                            rows="3">{{ $product->product_details }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        @include('layouts.partials.translation_textarea', [
                                            'attribute' => 'product_details',
                                            'translations' => $product->translations,
                                        ])
                                    </div>
                                </div>
                                @if (session('system_mode') == 'restaurant' || session('system_mode') == 'garments' || session('system_mode') == 'pos')
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="automatic_consumption" name="automatic_consumption" type="checkbox"
                                                @if (!empty($product) && $product->automatic_consumption == 1) checked @endif value="1"
                                                class="form-control-custom">
                                            <label for="automatic_consumption"><strong>@lang('lang.automatic_consumption')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="price_based_on_raw_material" name="price_based_on_raw_material"
                                                type="checkbox" @if ($product->price_based_on_raw_material == 1) checked @endif value="1"
                                                class="form-control-custom">
                                            <label
                                                for="price_based_on_raw_material"><strong>@lang('lang.price_based_on_raw_material')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="buy_from_supplier" name="buy_from_supplier" type="checkbox"
                                                @if ($product->buy_from_supplier == 1) checked @endif value="1"
                                                class="form-control-custom">
                                            <label for="buy_from_supplier"><strong>@lang('lang.buy_from_supplier')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-bordered" id="consumption_table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%;">@lang('lang.raw_materials')</th>
                                                    <th style="width: 30%;">@lang('lang.used_amount')</th>
                                                    <th style="width: 30%;">@lang('lang.unit')</th>
                                                    <th style="width: 30%;">@lang('lang.cost')</th>
                                                    <th style="width: 10%;"><button
                                                            class="btn btn-xs btn-success add_raw_material_row"
                                                            type="button"><i class="fa fa-plus"></i></button></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $first_variation = $product->variations->first();
                                                    $consumption_products = App\Models\ConsumptionProduct::where('variation_id', $first_variation->id)->get();
                                                @endphp
                                                @foreach ($consumption_products as $consumption_product)
                                                    @include('product.partial.raw_material_row', [
                                                        'row_id' => $loop->index,
                                                        'consumption_product' => $consumption_product,
                                                    ])
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="raw_material_row_index" id="raw_material_row_index"
                                            value="@if (!empty($consumption_products) && $consumption_products->count() > 0) {{ $consumption_products->count() }}@else{{ 0 }} @endif">
                                    </div>
                                @endif
                                @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('barcode_type', __('lang.barcode_type') . ' *', []) !!}
                                            {!! Form::select('barcode_type', ['C128' => 'Code 128', 'C39' => 'Code 39', 'UPCA' => 'UPC-A', 'UPCE' => 'UPC-E', 'EAN8' => 'EAN-8', 'EAN13' => 'EAN-13'], $product->barcode_type, ['class' => 'form-control', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('alert_quantity', __('lang.alert_quantity') . ' *', []) !!}
                                            {!! Form::text('alert_quantity', $product->alert_quantity, ['class' => 'form-control', 'placeholder' => __('lang.alert_quantity')]) !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('other_cost', __('lang.other_cost'), []) !!}
                                        {!! Form::text('other_cost', @num_format($product->other_cost), ['class' => 'form-control', 'placeholder' => __('lang.other_cost')]) !!}
                                    </div>
                                </div>
                                @can('product_module.purchase_price.create_and_edit')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('purchase_price', session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket' ? __('lang.purchase_price') : __('lang.cost') . ' *', []) !!}
                                            {!! Form::text('purchase_price', @num_format($product->purchase_price), ['class' => 'form-control', 'placeholder' => session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket' ? __('lang.purchase_price') : __('lang.cost'), 'required']) !!}
                                        </div>
                                    </div>
                                @endcan
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('sell_price', __('lang.sell_price') . ' *', []) !!}
                                        {!! Form::text('sell_price', @num_format($product->sell_price), ['class' => 'form-control', 'placeholder' => __('lang.sell_price'), 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::label('tax_id', __('lang.tax'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('tax_id', $taxes, $product->tax_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                        <span class="input-group-btn">
                                            @can('product_module.tax.create')
                                                <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{ action('TaxController@create') }}?quick_add=1"
                                                    data-container=".view_modal"><i
                                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                    <div class="error-msg text-red"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('tax_method', __('lang.tax_method'), []) !!}
                                        {!! Form::select('tax_method', ['inclusive' => __('lang.inclusive'), 'exclusive' => __('lang.exclusive')], $product->tax_method, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <br>
                                <div class="clearfix"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_type', __('lang.discount_type'), []) !!}
                                        {!! Form::select('discount_type', ['fixed' => __('lang.fixed'), 'percentage' => __('lang.percentage')], $product->discount_type, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('discount', __('lang.discount'), []) !!}
                                        {!! Form::text('discount', @num_format($product->discount), ['class' => 'form-control', 'placeholder' => __('lang.discount')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_start_date', __('lang.discount_start_date'), []) !!}
                                        {!! Form::text('discount_start_date', !empty($product->discount_start_date) ? @format_date($product->discount_start_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.discount_start_date')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_end_date', __('lang.discount_end_date'), []) !!}
                                        {!! Form::text('discount_end_date', !empty($product->discount_end_date) ? @format_date($product->discount_end_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.discount_end_date')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('discount_customer_types', __('lang.customer_type'), []) !!} <i class="dripicons-question" data-toggle="tooltip"
                                            title="@lang('lang.discount_customer_info')"></i>
                                        {!! Form::select('discount_customer_types[]', $discount_customer_types, $product->discount_customer_types, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'multiple', 'data-actions-box' => 'true', 'id' => 'discount_customer_types']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="show_to_customer" name="show_to_customer" type="checkbox"
                                            @if ($product->show_to_customer) checked @endif value="1"
                                            class="form-control-custom">
                                        <label for="show_to_customer"><strong>@lang('lang.show_to_customer')</strong></label>
                                    </div>
                                </div>

                                <div class="col-md-12 show_to_customer_type_div">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('show_to_customer_types', __('lang.show_to_customer_types'), []) !!}
                                            <i class="dripicons-question" data-toggle="tooltip"
                                                title="@lang('lang.show_to_customer_types_info')"></i>
                                            {!! Form::select('show_to_customer_types[]', $customer_types, $product->show_to_customer_types, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'multiple']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-top: 10px">
                                    <div class="i-checks">
                                        <input id="different_prices_for_stores" name="different_prices_for_stores"
                                            @if ($product->different_prices_for_stores) checked @endif type="checkbox" value="1"
                                            class="form-control-custom">
                                        <label for="different_prices_for_stores"><strong>@lang('lang.different_prices_for_stores')</strong></label>
                                    </div>
                                </div>

                                <div class="col-md-12 different_prices_for_stores_div">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    @lang('lang.store')
                                                </th>
                                                <th>
                                                    @lang('lang.price')
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->product_stores as $product_store)
                                                @if (!empty($product_store->store))
                                                    <tr>
                                                        <td>{{ $product_store->store->name }}</td>
                                                        <td><input type="text" class="form-control store_prices"
                                                                style="width: 200px;"
                                                                name="product_stores[{{ $product_store->store_id }}][price]"
                                                                value="{{ $product_store->price }}"></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>


                                <div class="col-md-12" style="margin-top: 10px">
                                    <div class="i-checks">
                                        <input id="this_product_have_variant" name="this_product_have_variant"
                                            type="checkbox" @if ($product->type == 'variable') checked @endif value="1"
                                            class="form-control-custom">
                                        <label for="this_product_have_variant"><strong>@lang('lang.this_product_have_variant')</strong></label>
                                    </div>
                                </div>

                                <div class="col-md-12 this_product_have_variant_div">
                                    <table class="table" id="variation_table">
                                        <thead>
                                            <tr>
                                                <th>@lang('lang.name')</th>
                                                <th>@lang('lang.sku')</th>
                                                <th>@lang('lang.color')</th>
                                                <th>@lang('lang.size')</th>
                                                <th>@lang('lang.grade')</th>
                                                <th>@lang('lang.unit')</th>
                                                <th>@lang('lang.purchase_price')</th>
                                                <th>@lang('lang.sell_price')</th>
                                                <th><button type="button" class="btn btn-success btn-xs add_row mt-2"><i
                                                            class="dripicons-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variations as $item)
                                                @include('product.partial.edit_variation_row', [
                                                    'row_id' => $loop->index,
                                                    'item' => $item,
                                                ])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" name="row_id" id="row_id"
                                    value="{{ $product->variations->count() }}">
                            </div>

                            <div class="row">
                                <div class="col-md-4 mt-5">
                                    <div class="form-group">
                                        <input type="button" id="submit-btn" value="@lang('lang.submit')"
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


    <div class="modal fade" id="product_cropper_modal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('lang.crop_image_before_upload')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8">
                                <img src="" id="product_sample_image" />
                            </div>
                            <div class="col-md-4">
                                <div class="product_preview_div"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="product_crop" class="btn btn-primary">@lang('lang.crop')</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/product_edit.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#different_prices_for_stores').change();
            $('#this_product_have_variant').change();
        })
    </script>
@endsection
