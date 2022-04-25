<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('ProductController@store'), 'method' => 'post', 'id' => $quick_add ?
        'product-form-quick-add': 'product-form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_product' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <input type="hidden" name="active" value="1">
            <div class="row">
                @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('product_class_id', __('lang.class') . ' *', []) !!}
                        {!! Form::select('product_class_id', $product_classes,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}

                    </div>
                    <div class="error-msg text-red"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __('lang.category') . ' *', []) !!}
                        {!! Form::select('category_id', $categories,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}

                    </div>
                    <div class="error-msg text-red"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('lang.sub_category') . ' *', []) !!}
                        {!! Form::select('sub_category_id', [],
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}

                    </div>
                    <div class="error-msg text-red"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('lang.brand') . ' *', []) !!}
                        {!! Form::select('brand_id', $brands,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}

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
                <div class="col-md-4  @if(session('system_mode') == 'restaurant') hide @endif">
                    <div class="form-group">
                        {!! Form::label('sku', __('lang.sku') . ' *', []) !!}
                        {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder'
                        => __('lang.sku')]) !!}
                    </div>
                </div>
                @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('multiple_units', __('lang.unit'), []) !!}
                        {!! Form::select('multiple_units[]', $units,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true", 'placeholder' =>
                        __('lang.please_select'), 'id' => 'multiple_units']) !!}

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('multiple_colors', __('lang.color'), []) !!}
                        {!! Form::select('multiple_colors[]', $colors,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true", 'placeholder' =>
                        __('lang.please_select'), 'id' => 'multiple_colors']) !!}

                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('multiple_sizes', __('lang.size'), []) !!}
                        {!! Form::select('multiple_sizes[]', $sizes,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true", 'placeholder' =>
                        __('lang.please_select'), 'id' => 'multiple_sizes']) !!}

                    </div>
                </div>
                @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('multiple_grades', __('lang.grade'), []) !!}
                        {!! Form::select('multiple_grades[]', $grades,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true", 'placeholder' =>
                        __('lang.please_select'), 'id' => 'multiple_grades']) !!}

                    </div>
                </div>
                @endif
                <div class="col-md-12 " style="margin-top: 10px;">
                    <div class="form-group">
                        {!! Form::label('image', __('lang.image'), []) !!} <br>
                        {!! Form::file('images[]', ['multilple']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        @if(session('system_mode' ) == 'restaurant')
                        {!! Form::label('recipe', __('lang.recipe'), []) !!}
                        @else
                        <label>@lang('lang.product_details')</label>
                        @endif
                        <textarea name="product_details" id="product_details" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('barcode_type', __('lang.barcode_type') . ' *', []) !!}
                        {!! Form::select('barcode_type', ['C128' => 'Code 128' , 'C39' => 'Code 39', 'UPCA'
                        => 'UPC-A', 'UPCE' => 'UPC-E', 'EAN8' => 'EAN-8', 'EAN13' => 'EAN-13'], false,
                        ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('alert_quantity', __('lang.alert_quantity') . ' *', []) !!}
                        {!! Form::text('alert_quantity', null, ['class' => 'form-control', 'placeholder' =>
                        __('lang.alert_quantity')]) !!}
                    </div>
                </div>
                @endif
                @can('product_module.purchase_price.create_and_edit')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('purchase_price', session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket' ? __('lang.purchase_price') :
                        __('lang.cost') . ' *', []) !!}
                        {!! Form::text('purchase_price', null, ['class' => 'form-control', 'placeholder' =>
                        session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket' ? __('lang.purchase_price') : __('lang.cost'), 'required']) !!}
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
                    <div class="form-group">
                        {!! Form::label('tax_id', __('lang.tax') , []) !!}
                        {!! Form::select('tax_id', $taxes,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}

                    </div>
                    <div class="error-msg text-red"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('tax_method', __('lang.tax_method'), []) !!}
                        {!! Form::select('tax_method', ['inclusive' => __('lang.inclusive'), 'exclusive' =>
                        __('lang.exclusive')],
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'placeholder' => __('lang.please_select')]) !!}
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
                        'placeholder' => __('lang.please_select')]) !!}
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
                        {!! Form::label('discount_customer_types', __('lang.customers'), []) !!} <i
                            class="dripicons-question" data-toggle="tooltip"
                            title="@lang('lang.discount_customer_info')"></i>
                        {!! Form::select('discount_customer_types[]', $discount_customer_types,
                        false, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                        'style' =>'width: 80%', 'multiple']) !!}
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

                <div class="col-md-12" style="margin-top: 10px; display:none">
                    <div class="i-checks">
                        <input id="different_prices_for_stores" name="different_prices_for_stores" type="checkbox"
                            value="1" class="form-control-custom">
                        <label
                            for="different_prices_for_stores"><strong>@lang('lang.different_prices_for_stores')</strong></label>
                    </div>
                </div>

                <div class="col-md-12 different_prices_for_stores_div" style="display:none">
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

                <div class="col-md-12" style="margin-top: 10px; display:none">
                    <div class="i-checks">
                        <input id="this_product_have_variant" name="this_product_have_variant" type="checkbox" value="1"
                            class="form-control-custom">
                        <label
                            for="this_product_have_variant"><strong>@lang('lang.this_product_have_variant')</strong></label>
                    </div>
                </div>

                <div class="col-md-12 this_product_have_variant_div" style="display:none">
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

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="submit-btn-add-product">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    });
    $('.selectpicker').selectpicker('render');
    tinymce.init({
        selector: "#product_details",
        height: 130,
        plugins: [
            "advlist autolink lists link charmap print preview anchor textcolor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste code wordcount",
        ],
        toolbar:
            "insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
        branding: false,
    });
    $(".show_to_customer_type_div").slideUp();
    $("#show_to_customer").change(function () {
        if ($(this).prop("checked")) {
            $(".show_to_customer_type_div").slideUp();
        } else {
            $(".show_to_customer_type_div").slideDown();
        }
    });

</script>
