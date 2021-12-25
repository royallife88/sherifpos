<div class="row">
    <div class="col-md-12">
        <div class="i-checks">
            <input id="is_service" name="is_service" type="checkbox" @if(session('system_mode') == 'restaurant') checked @endif value="1" class="form-control-custom">
            <label for="is_service"><strong>@lang('lang.add_new_service')</strong></label>
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('product_class_id', __('lang.class') . ' *', []) !!}
        <div class="input-group my-group">
            {!! Form::select('product_class_id', $product_classes,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'required',
            'required']) !!}
            <span class="input-group-btn">
                @can('product_module.product_class.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('ProductClassController@create')}}?quick_add=1" data-container=".view_modal"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
        <div class="error-msg text-red"></div>
    </div>
    @if(session('system_mode') == 'pos')
    <div class="col-md-4">
        {!! Form::label('category_id', __('lang.category') . ' *', []) !!}
        <div class="input-group my-group">
            {!! Form::select('category_id', $categories,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            <span class="input-group-btn">
                @can('product_module.category.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('CategoryController@create')}}?quick_add=1&type=category"
                    data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
        <div class="error-msg text-red"></div>
    </div>
    <div class="col-md-4">
        {!! Form::label('sub_category_id', __('lang.sub_category'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('sub_category_id', [],
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            <span class="input-group-btn">
                @can('product_module.sub_category.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('CategoryController@create')}}?quick_add=1&type=sub_category"
                    data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
        <div class="error-msg text-red"></div>
    </div>
    <div class="col-md-4">
        {!! Form::label('brand_id', __('lang.brand'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('brand_id', $brands,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            <span class="input-group-btn">
                @can('product_module.brand.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('BrandController@create')}}?quick_add=1" data-container=".view_modal"><i
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
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder'
            => __('lang.name')]) !!}
        </div>
    </div>
    <div class="col-md-4 @if(session('system_mode') == 'restaurant') hide @endif">
        <div class="form-group">
            {!! Form::label('sku', __('lang.sku') , []) !!}
            {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder'
            => __('lang.sku')]) !!}
        </div>
    </div>
    @if(session('system_mode') == 'pos')
    <div class="col-md-4">
        {!! Form::label('multiple_units', __('lang.unit'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('multiple_units[]', $units,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'id' => 'multiple_units']) !!}
            <span class="input-group-btn">
                @can('product_module.unit.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('UnitController@create')}}?quick_add=1" data-container=".view_modal"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('multiple_colors', __('lang.color'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('multiple_colors[]', $colors,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'id' => 'multiple_colors']) !!}
            <span class="input-group-btn">
                @can('product_module.color.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('ColorController@create')}}?quick_add=1" data-container=".view_modal"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
    </div>
    @endif
    <div class="col-md-4">
        {!! Form::label('multiple_sizes', __('lang.size'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('multiple_sizes[]', $sizes,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'id' => 'multiple_sizes']) !!}
            <span class="input-group-btn">
                @can('product_module.size.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('SizeController@create')}}?quick_add=1" data-container=".view_modal"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
    </div>
    @if(session('system_mode') == 'pos')
    <div class="col-md-4">
        {!! Form::label('multiple_grades', __('lang.grade'), []) !!}
        <div class="input-group my-group">
            {!! Form::select('multiple_grades[]', $grades,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'id' => 'multiple_grades']) !!}
            <span class="input-group-btn">
                @can('product_module.grade.create_and_edit')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('GradeController@create')}}?quick_add=1" data-container=".view_modal"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                @endcan
            </span>
        </div>
    </div>
    @endif
    <div class="col-md-12 " style="margin-top: 10px;">
        <div class="dropzone" id="my-dropzone">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('lang.product_details')</label>
            <textarea name="product_details" id="product_details" class="form-control" rows="3"></textarea>
        </div>
    </div>
    @if(session('system_mode') == 'pos')
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('barcode_type', __('lang.barcode_type'), []) !!}
            {!! Form::select('barcode_type', ['C128' => 'Code 128' , 'C39' => 'Code 39', 'UPCA'
            => 'UPC-A', 'UPCE' => 'UPC-E', 'EAN8' => 'EAN-8', 'EAN13' => 'EAN-13'], false,
            ['class' => 'form-control', 'required']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('alert_quantity', __('lang.alert_quantity'), []) !!}
            {!! Form::text('alert_quantity', null, ['class' => 'form-control', 'placeholder' =>
            __('lang.alert_quantity')]) !!}
        </div>
    </div>
    @endif
    @can('product_module.purchase_price.create_and_edit')
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('purchase_price', session('system_mode') == 'pos' ? __('lang.purchase_price') : __('lang.cost') . ' *', []) !!}
            {!! Form::text('purchase_price', null, ['class' => 'form-control', 'placeholder' =>
            session('system_mode') == 'pos' ? __('lang.purchase_price') : __('lang.cost'), 'required']) !!}
        </div>
    </div>
    @endcan
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('sell_price', __('lang.sell_price') . ' *', []) !!}
            {!! Form::text('sell_price', null, ['class' => 'form-control', 'placeholder' =>
            __('lang.sell_price'), 'required']) !!}
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('tax_id', __('lang.tax') , []) !!}
        <div class="input-group my-group">
            {!! Form::select('tax_id', $taxes,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
            <span class="input-group-btn">
                @can('product_module.tax.create')
                <button class="btn-modal btn btn-default bg-white btn-flat"
                    data-href="{{action('TaxController@create')}}?quick_add=1" data-container=".view_modal"><i
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
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
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
            'fixed', ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount', __('lang.discount'), []) !!}
            {!! Form::text('discount', null, ['class' => 'form-control', 'placeholder' =>
            __('lang.discount')]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount_start_date', __('lang.discount_start_date'), []) !!}
            {!! Form::text('discount_start_date', null, ['class' => 'form-control datepicker',
            'placeholder' => __('lang.discount_start_date')]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount_end_date', __('lang.discount_end_date'), []) !!}
            {!! Form::text('discount_end_date', null, ['class' => 'form-control datepicker',
            'placeholder' => __('lang.discount_end_date')]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount_customers', __('lang.customers'), []) !!} <i class="dripicons-question"
                data-toggle="tooltip" title="@lang('lang.discount_customer_info')"></i>
            {!! Form::select('discount_customers[]', $customers_tree_arry,
            false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
            'style' =>'width: 80%', 'multiple', "data-actions-box"=>"true"]) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="i-checks">
            <input id="show_to_customer" name="show_to_customer" type="checkbox" checked value="1"
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
                {!! Form::select('show_to_customer_types[]', $customer_types,
                false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                'style' =>'width: 80%', 'multiple']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-12" style="margin-top: 10px">
        <div class="i-checks">
            <input id="different_prices_for_stores" name="different_prices_for_stores" type="checkbox" value="1"
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
                @foreach ($stores as $store)
                <tr>
                    <td>{{$store->name}}</td>
                    <td><input type="text" class="form-control store_prices" style="width: 200px;"
                            name="product_stores[{{$store->id}}][price]" value=""></td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-12" style="margin-top: 10px">
        <div class="i-checks">
            <input id="this_product_have_variant" name="this_product_have_variant" type="checkbox" value="1"
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

            </tbody>
        </table>
    </div>
    <input type="hidden" name="row_id" id="row_id" value="0">
</div>
