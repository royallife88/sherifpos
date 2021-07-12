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
                        {!! Form::open(['url' => action('ProductController@update', $product->id), 'id' =>
                        'product-edit-form', 'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::label('product_class_id', __('lang.class') . ' *', []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('product_class_id', $product_classes,
                                    $product->product_class_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'required',
                                    'required']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.product_class.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('ProductClassController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                                <div class="error-msg text-red"></div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('category_id', __('lang.category') . ' *', []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('category_id', $categories,
                                    $product->category_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'required']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.category.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('CategoryController@create')}}?quick_add=1"
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
                                    {!! Form::select('sub_category_id', $sub_categories,
                                    $product->sub_category_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'required']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.sub_category.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('CategoryController@create')}}?quick_add=1"
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
                                    {!! Form::select('brand_id', $brands,
                                    $product->brand_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'required']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.brand.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('BrandController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                                <div class="error-msg text-red"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __('lang.name') . ' *', []) !!}
                                    {!! Form::text('name', $product->name, ['class' => 'form-control', 'required',
                                    'placeholder'
                                    => __('lang.name')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('sku', __('lang.sku') . ' *', []) !!}
                                    {!! Form::text('sku', $product->sku, ['class' => 'form-control', 'required',
                                    'placeholder'
                                    => __('lang.sku')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('multiple_units', __('lang.unit'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('multiple_units[]', $units,
                                    $product->multiple_units, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'multiple', 'id' => 'multiple_units']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.unit.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('UnitController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('multiple_colors', __('lang.color'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('multiple_colors[]', $colors,
                                    $product->multiple_colors, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'multiple', 'id' => 'multiple_colors']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.color.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('ColorController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('multiple_sizes', __('lang.size'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('multiple_sizes[]', $sizes,
                                    $product->multiple_sizes, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'multiple', 'id' => 'multiple_sizes']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.size.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('SizeController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('multiple_grades', __('lang.grade'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('multiple_grades[]', $grades,
                                    $product->multiple_grades, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'multiple', 'id' => 'multiple_grades']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.grade.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('GradeController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="i-checks"  style="margin-top: 40px">
                                    <input id="is_service" name="is_service" type="checkbox" @if(!empty($product->is_service)) checked @endif value="1"
                                        class="form-control-custom">
                                    <label for="is_service"><strong>@lang('lang.is_service')</strong></label>
                                </div>
                            </div>

                            <div class="col-md-12 " style="margin-top: 10px;">
                                <div class="dropzone" id="my-dropzone">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('lang.product_details')</label>
                                    <textarea name="product_details" id="product_details" class="form-control"
                                        rows="3">{{$product->product_details}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('add_stock', __('lang.add_stock'), []) !!}
                                    {!! Form::text('add_stock', null, ['class' => 'form-control', 'placeholder' =>
                                    __('lang.add_stock')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('batch_number', __('lang.batch_number'), []) !!}
                                    {!! Form::text('batch_number', $product->batch_number, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('lang.batch_number')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('barcode_type', __('lang.barcode_type') . ' *', []) !!}
                                    {!! Form::select('barcode_type', ['C128' => 'Code 128' , 'C39' => 'Code 39', 'UPCA'
                                    => 'UPC-A', 'UPCE' => 'UPC-E', 'EAN8' => 'EAN-8', 'EAN13' => 'EAN-13'],
                                    $product->barcode_type,
                                    ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('manufacturing_date', __('lang.manufacturing_date') , []) !!}
                                    {!! Form::text('manufacturing_date', !empty($product->manufacturing_date) ?
                                    @format_date($product->manufacturing_date) : null, ['class' => 'form-control
                                    datepicker', 'id' => 'manufacturing_date',
                                    'placeholder'
                                    => __('lang.manufacturing_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('expiry_date', __('lang.expiry_date') , []) !!}
                                    {!! Form::text('expiry_date', !empty($product->expiry_date) ?
                                    @format_date($product->expiry_date) : null, ['class' => 'form-control datepicker',
                                    'placeholder' =>
                                    __('lang.expiry_date'), 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 warning hide">
                                <div class="form-group">
                                    {!! Form::label('expiry_warning', __('lang.warning'), []) !!}
                                    {!! Form::text('expiry_warning', $product->expiry_warning, ['class' =>
                                    'form-control', 'placeholder' =>
                                    __('lang.days_before_the_expiry_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4 convert_status_expire hide">
                                <div class="form-group">
                                    {!! Form::label('convert_status_expire', __('lang.convert_status_expire') ,
                                    []) !!}
                                    {!! Form::text('convert_status_expire', $product->convert_status_expire, ['class' =>
                                    'form-control',
                                    'placeholder' => __('lang.days_before_the_expiry_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('alert_quantity', __('lang.alert_quantity') . ' *', []) !!}
                                    {!! Form::text('alert_quantity', $product->alert_quantity, ['class' =>
                                    'form-control', 'placeholder' =>
                                    __('lang.alert_quantity')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('purchase_price', __('lang.purchase_price') . ' *', []) !!}
                                    {!! Form::text('purchase_price', @num_format($product->purchase_price), ['class' =>
                                    'form-control', 'placeholder' =>
                                    __('lang.purchase_price'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('sell_price', __('lang.sell_price') . ' *', []) !!}
                                    {!! Form::text('sell_price', @num_format($product->sell_price), ['class' =>
                                    'form-control', 'placeholder' =>
                                    __('lang.sell_price'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('tax_id', __('lang.tax') , []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('tax_id', $taxes,
                                    $product->tax_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.tax.create')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('TaxController@create')}}?quick_add=1"
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
                                    {!! Form::select('tax_method', ['inclusive' => __('lang.inclusive'), 'exclusive' =>
                                    __('lang.exclusive')],
                                    $product->tax_method, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <br>
                            <div class="clearfix"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('lang.discount_type'), []) !!}
                                    {!! Form::select('discount_type', ['fixed' => __('lang.fixed'), 'percentage' =>
                                    __('lang.percentage')],
                                    $product->discount_type, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount', __('lang.discount'), []) !!}
                                    {!! Form::text('discount', @num_format( $product->discount), ['class' =>
                                    'form-control', 'placeholder' =>
                                    __('lang.discount')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_start_date', __('lang.discount_start_date'), []) !!}
                                    {!! Form::text('discount_start_date', $product->discount_start_date, ['class' =>
                                    'form-control datepicker',
                                    'placeholder' => __('lang.discount_start_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_end_date', __('lang.discount_end_date'), []) !!}
                                    {!! Form::text('discount_end_date', $product->discount_end_date, ['class' =>
                                    'form-control datepicker',
                                    'placeholder' => __('lang.discount_end_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('discount_customers', __('lang.customers'), []) !!} <i
                                        class="dripicons-question" data-toggle="tooltip"
                                        title="@lang('lang.discount_customer_info')"></i>
                                    {!! Form::select('discount_customers[]', $customers_tree_arry,
                                    $product->discount_customers, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%', 'multiple']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="i-checks">
                                    <input id="show_to_customer" name="show_to_customer" type="checkbox"
                                        @if($product->show_to_customer) checked @endif value="1"
                                    class="form-control-custom">
                                    <label
                                        for="show_to_customer"><strong>@lang('lang.show_to_customer')</strong></label>
                                </div>
                            </div>

                            <div class="col-md-12 show_to_customer_type_div">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('show_to_customer_types', __('lang.show_to_customer_types'), [])
                                        !!}
                                        <i class="dripicons-question" data-toggle="tooltip"
                                            title="@lang('lang.show_to_customer_types_info')"></i>
                                        {!! Form::select('show_to_customer_types[]', $customer_types,
                                        $product->show_to_customer_types, ['class' => 'selectpicker form-control',
                                        'data-live-search'=>"true",
                                        'style' =>'width: 80%', 'multiple']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-top: 10px">
                                <div class="i-checks">
                                    <input id="different_prices_for_stores" name="different_prices_for_stores"
                                        @if($product->different_prices_for_stores) checked @endif type="checkbox"
                                    value="1"
                                    class="form-control-custom">
                                    <label
                                        for="different_prices_for_stores"><strong>@lang('lang.different_prices_for_stores')</strong></label>
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
                                        <tr>
                                            <td>{{$product_store->store->name}}</td>
                                            <td><input type="text" class="form-control store_prices"
                                                    style="width: 200px;"
                                                    name="product_stores[{{$product_store->store_id}}][price]"
                                                    value="{{$product_store->price}}"></td>
                                        </tr>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>


                            <div class="col-md-12" style="margin-top: 10px">
                                <div class="i-checks">
                                    <input id="this_product_have_variant" name="this_product_have_variant"
                                        type="checkbox" @if($product->type == 'variable') checked @endif value="1"
                                    class="form-control-custom">
                                    <label
                                        for="this_product_have_variant"><strong>@lang('lang.this_product_have_variant')</strong></label>
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
                                        @include('product.partial.variation_row', ['row_id' => $loop->index, 'item' =>
                                        $item])

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="row_id" id="row_id" value="{{$product->variations->count()}}">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mt-5">
                                <div class="form-group">
                                    <input type="submit" value="{{trans('lang.submit')}}" class="btn btn-primary">
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
<script src="{{asset('js/product.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#different_prices_for_stores').change();
        $('#this_product_have_variant').change();
    })
</script>
@endsection