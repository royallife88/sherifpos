@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')
    <div class="container-fluid">
        @if (empty($page))
            @can('product_module.product.create_and_edit')
                <a style="color: white" href="{{ action('ProductController@create') }}" class="btn btn-info"><i
                        class="dripicons-plus"></i>
                    @lang('lang.add_product')</a>
            @endcan
            <a style="color: white" href="{{ action('ProductController@getImport') }}" class="btn btn-primary"><i
                    class="fa fa-arrow-down"></i>
                @lang('lang.import')</a>
        @else
            <a style="color: white" href="{{ action('AddStockController@getImport') }}" class="btn btn-primary"><i
                    class="fa fa-arrow-down"></i>
                @lang('lang.import')</a>
        @endif
        <div class="card mt-3">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_class_id', session('system_mode') == 'restaurant' ? __('lang.category') : __('lang.product_class') . ':', []) !!}
                            {!! Form::select('product_class_id', $product_classes, request()->product_class_id, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    @if (session('system_mode') != 'restaurant')
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('category_id', __('lang.category') . ':', []) !!}
                                {!! Form::select('category_id', $categories, request()->category_id, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('sub_category_id', __('lang.sub_category') . ':', []) !!}
                                {!! Form::select('sub_category_id', $sub_categories, request()->sub_category_id, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('brand_id', __('lang.brand') . ':', []) !!}
                                {!! Form::select('brand_id', $brands, request()->brand_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('lang.supplier') . ':', []) !!}
                            {!! Form::select('supplier_id', $suppliers, request()->supplier_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('unit_id', __('lang.unit') . ':', []) !!}
                            {!! Form::select('unit_id', $units, request()->unit_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('color_id', __('lang.color') . ':', []) !!}
                            {!! Form::select('color_id', $colors, request()->color_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('size_id', __('lang.size') . ':', []) !!}
                            {!! Form::select('size_id', $sizes, request()->size_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('grade_id', __('lang.grade') . ':', []) !!}
                            {!! Form::select('grade_id', $grades, request()->grade_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tax_id', __('lang.tax') . ':', []) !!}
                            {!! Form::select('tax_id', $taxes, request()->tax_id, [
    'class' => 'form-control
                        filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control filter_product', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_type_id', __('lang.customer_type') . ':', []) !!}
                            {!! Form::select('customer_type_id', $customer_types, request()->customer_type_id, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('created_by', __('lang.created_by') . ':', []) !!}
                            {!! Form::select('created_by', $users, request()->created_by, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('active', __('lang.active') . ':', []) !!}
                            {!! Form::select('active', [0 => __('lang.no'), 1 => __('lang.yes')], request()->active, [
    'class' => 'form-control filter_product
                        selectpicker',
    'data-live-search' => 'true',
    'placeholder' => __('lang.all'),
]) !!}
                        </div>
                    </div>
                    <input type="hidden" name="product_id" id="product_id" value="">
                    <div class="col-md-3">
                        <button class="btn btn-danger mt-4 clear_filters">@lang('lang.clear_filters')</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" value="0"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.image')</button>
                <button type="button" value="3" class="badge badge-pill badge-primary column-toggle">
                    @if (session('system_mode') == 'restaurant')
                        @lang('lang.category')
                    @else
                        @lang('lang.class')
                    @endif
                </button>
                @if (session('system_mode') != 'restaurant')
                    <button type="button" value="4"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.category')</button>
                    <button type="button" value="5"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.sub_category')</button>
                @endif
                <button type="button" value="6"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_history')</button>
                <button type="button" value="7"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.batch_number')</button>
                <button type="button" value="8"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.selling_price')</button>
                <button type="button" value="9"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.tax')</button>
                @if (session('system_mode') != 'restaurant')
                    <button type="button" value="10"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.brand')</button>
                @endif
                <button type="button" value="11"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.unit')</button>
                <button type="button" value="12"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.color')</button>
                <button type="button" value="13"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.size')</button>
                <button type="button" value="14"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.grade')</button>
                @if (empty($page))
                    <button type="button" value="15"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock')</button>
                @endif
                @if (!empty($page))
                    <button type="button" value="16"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock_value')</button>
                @endif
                <button type="button" value="17"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.customer_type')</button>
                <button type="button" value="18"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.expiry_date')</button>
                <button type="button" value="19"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.manufacturing_date')</button>
                <button type="button" value="20"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.discount')</button>
                @can('product_module.purchase_price.view')
                    <button type="button" value="21"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
                @endcan
                <button type="button" value="21"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.supplier')</button>
                <button type="button" value="21"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.active')</button>
                <button type="button" value="22"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.created_by')</button>
                <button type="button" value="23"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.edited_by')</button>
            </div>
        </div>


    </div>
    <div class="table-responsive">
        <table id="product_table" class="table" style="width: auto">
            <thead>
                <tr>
                    <th>@lang('lang.image')</th>
                    <th>@lang('lang.name')</th>
                    <th>@lang('lang.product_code')</th>
                    <th>
                        @if (session('system_mode') == 'restaurant')
                            @lang('lang.category')
                        @else
                            @lang('lang.class')
                        @endif
                    </th>
                    @if (session('system_mode') != 'restaurant')
                        <th>@lang('lang.category')</th>
                        <th>@lang('lang.sub_category')</th>
                    @endif
                    <th>@lang('lang.purchase_history')</th>
                    <th>@lang('lang.batch_number')</th>
                    <th>@lang('lang.selling_price')</th>
                    <th>@lang('lang.tax')</th>
                    @if (session('system_mode') != 'restaurant')
                        <th>@lang('lang.brand')</th>
                    @endif
                    <th>@lang('lang.unit')</th>
                    <th>@lang('lang.color')</th>
                    <th>@lang('lang.size')</th>
                    <th>@lang('lang.grade')</th>
                    <th class="sum">@lang('lang.current_stock')</th>
                    <th class="sum">@lang('lang.current_stock_value')</th>
                    <th>@lang('lang.customer_type')</th>
                    <th>@lang('lang.expiry_date')</th>
                    <th>@lang('lang.manufacturing_date')</th>
                    <th>@lang('lang.discount')</th>
                    @can('product_module.purchase_price.view')
                        <th>@lang('lang.purchase_price')</th>
                    @endcan
                    <th>@lang('lang.supplier')</th>
                    <th>@lang('lang.active')</th>
                    <th>@lang('lang.created_by')</th>
                    <th>@lang('lang.edited_by')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th style="text-align: right">@lang('lang.total')</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('.column-toggle').each(function(i, obj) {
                if (i > 0) {
                    i = i + 2;
                }
                @if (session('system_mode') != 'restaurant')
                    @if (empty($page))
                        if (i > 15) {
                            i = i + 1;
                        }
                    @else
                        if (i > 14) {
                            i = i + 1;
                        }
                    @endif
                @else
                    @if (empty($page))
                        if (i > 12) {
                            i = i + 1;
                        }
                    @else
                        if (i > 11) {
                            i = i + 1;
                        }
                    @endif
                @endif
                $(obj).val(i)
            });
            product_table = $('#product_table').DataTable({
                lengthChange: true,
                paging: true,
                info: false,
                bAutoWidth: false,
                order: [],
                language: {
                    url: dt_lang_url,
                },
                lengthMenu: [
                    [10, 25, 50, 75, 100, 200, 500, -1],
                    [10, 25, 50, 75, 100, 200, 500, "All"],
                ],
                dom: "lBfrtip",
                // stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [2, 'asc']
                ],
                "ajax": {
                    "url": "/product",
                    "data": function(d) {
                        d.product_id = $('#product_id').val();
                        d.product_class_id = $('#product_class_id').val();
                        d.category_id = $('#category_id').val();
                        d.sub_category_id = $('#sub_category_id').val();
                        d.brand_id = $('#brand_id').val();
                        d.supplier_id = $('#supplier_id').val();
                        d.unit_id = $('#unit_id').val();
                        d.color_id = $('#color_id').val();
                        d.size_id = $('#size_id').val();
                        d.grade_id = $('#grade_id').val();
                        d.tax_id = $('#tax_id').val();
                        d.store_id = $('#store_id').val();
                        d.customer_type_id = $('#customer_type_id').val();
                        d.active = $('#active').val();
                        d.created_by = $('#created_by').val();
                    }
                },
                columnDefs: [{
                    "targets": [0, 3],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'variation_name',
                        name: 'products.name'
                    },
                    {
                        data: 'sub_sku',
                        name: 'variations.sub_sku'
                    },
                    {
                        data: 'product_class',
                        name: 'product_classes.name'
                    },
                    @if (session('system_mode') != 'restaurant')
                        {
                            data: 'category',
                            name: 'categories.name'
                        }, {
                            data: 'sub_category',
                            name: 'categories.name'
                        },
                    @endif {
                        data: 'purchase_history',
                        name: 'purchase_history'
                    },
                    {
                        data: 'batch_number',
                        name: 'add_stock_lines.batch_number'
                    },
                    {
                        data: 'default_sell_price',
                        name: 'variations.default_sell_price'
                    },
                    {
                        data: 'tax',
                        name: 'taxes.name'
                    },
                    @if (session('system_mode') != 'restaurant')
                        {
                            data: 'brand',
                            name: 'brands.name'
                        },
                    @endif {
                        data: 'unit',
                        name: 'units.name'
                    },
                    {
                        data: 'color',
                        name: 'colors.name'
                    },
                    {
                        data: 'size',
                        name: 'sizes.name'
                    },
                    {
                        data: 'grade',
                        name: 'grades.name'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock',
                        searchable: false
                    },
                    {
                        data: 'current_stock_value',
                        name: 'current_stock_value',
                        searchable: false
                        @if (empty($page))
                            , visible: false
                        @endif
                    },
                    {
                        data: 'customer_type',
                        name: 'customer_type'
                    },
                    {
                        data: 'exp_date',
                        name: 'add_stock_lines.expiry_date'
                    },
                    {
                        data: 'manufacturing_date',
                        name: 'add_stock_lines.manufacturing_date'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    @can('product_module.purchase_price.view')
                        {
                            data: 'default_purchase_price',
                            name: 'default_purchase_price',
                            searchable: false
                        },
                    @endcan {
                        data: 'supplier_name',
                        name: 'supplier.name'
                    },
                    {
                        data: 'active',
                        name: 'active'
                    },
                    {
                        data: 'created_by',
                        name: 'users.name'
                    },
                    {
                        data: 'edited_by_name',
                        name: 'edited.name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ],
                createdRow: function(row, data, dataIndex) {

                },
                fnDrawCallback: function(oSettings) {
                    var intVal = function(i) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "") * 1 :
                            typeof i === "number" ?
                            i :
                            0;
                    };

                    this.api()
                        .columns(".sum", {
                            page: "current"
                        })
                        .every(function() {
                            var column = this;
                            if (column.data().count()) {
                                var sum = column.data().reduce(function(a, b) {
                                    a = intVal(a);
                                    if (isNaN(a)) {
                                        a = 0;
                                    }

                                    b = intVal(b);
                                    if (isNaN(b)) {
                                        b = 0;
                                    }

                                    return a + b;
                                });
                                $(column.footer()).html(
                                    __currency_trans_from_en(sum, false)
                                );
                            }
                        });
                },
            });

        });



        var hidden_column_array = $.cookie('column_visibility') ? JSON.parse($.cookie('column_visibility')) : [];
        $(document).ready(function() {

            $.each(hidden_column_array, function(index, value) {
                $('.column-toggle').each(function() {
                    if ($(this).val() == value) {
                        toggleColumnVisibility(value, $(this));
                    }
                });

            });
        });

        $(document).on('click', '.column-toggle', function() {
            let column_index = parseInt($(this).val());
            toggleColumnVisibility(column_index, $(this));
            if (hidden_column_array.includes(column_index)) {
                hidden_column_array.splice(hidden_column_array.indexOf(column_index), 1);
            } else {
                hidden_column_array.push(column_index);
            }

            //unique array javascript
            hidden_column_array = $.grep(hidden_column_array, function(v, i) {
                return $.inArray(v, hidden_column_array) === i;
            });

            $.cookie('column_visibility', JSON.stringify(hidden_column_array));
        })

        function toggleColumnVisibility(column_index, this_btn) {
            column = product_table.column(column_index);
            column.visible(!column.visible());

            if (column.visible()) {
                $(this_btn).addClass('badge-primary')
                $(this_btn).removeClass('badge-warning')
            } else {
                $(this_btn).removeClass('badge-primary')
                $(this_btn).addClass('badge-warning')

            }
        }
        $(document).on('change', '.filter_product', function() {
            product_table.ajax.reload();
        })
        $(document).on('click', '.clear_filters', function() {
            $('.filter_product').val('');
            $('.filter_product').selectpicker('refresh');
            $('#product_id').val('');
            product_table.ajax.reload();
        });

        @if (!empty(request()->product_id))
            $(document).ready(function() {
                $('#product_id').val({{ request()->product_id }});
                product_table.ajax.reload();

                var container = '.view_modal';
                $.ajax({
                    method: 'get',
                    url: '/product/{{ request()->product_id }}',
                    dataType: 'html',
                    success: function(result) {
                        $(container).html(result).modal('show');
                    },
                });
            });
        @endif

        $(document).on('click', '.delete_product', function(e) {
            e.preventDefault();
            swal({
                title: 'Are you sure?',
                text: "@lang('lang.all_transactions_related_to_this_product_will_be_deleted')",
                icon: 'warning',
            }).then(willDelete => {
                if (willDelete) {
                    var check_password = $(this).data('check_password');
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    swal({
                        title: 'Please Enter Your Password',
                        content: {
                            element: "input",
                            attributes: {
                                placeholder: "Type your password",
                                type: "password",
                                autocomplete: "off",
                                autofocus: true,
                            },
                        },
                        inputAttributes: {
                            autocapitalize: 'off',
                            autoComplete: 'off',
                        },
                        focusConfirm: true
                    }).then((result) => {
                        if (result) {
                            $.ajax({
                                url: check_password,
                                method: 'POST',
                                data: {
                                    value: result
                                },
                                dataType: 'json',
                                success: (data) => {

                                    if (data.success == true) {
                                        swal(
                                            'Success',
                                            'Correct Password!',
                                            'success'
                                        );

                                        $.ajax({
                                            method: 'DELETE',
                                            url: href,
                                            dataType: 'json',
                                            data: data,
                                            success: function(result) {
                                                if (result.success ==
                                                    true) {
                                                    swal(
                                                        'Success',
                                                        result.msg,
                                                        'success'
                                                    );
                                                    setTimeout(() => {
                                                        location
                                                            .reload();
                                                    }, 1500);
                                                    location.reload();
                                                } else {
                                                    swal(
                                                        'Error',
                                                        result.msg,
                                                        'error'
                                                    );
                                                }
                                            },
                                        });

                                    } else {
                                        swal(
                                            'Failed!',
                                            'Wrong Password!',
                                            'error'
                                        )

                                    }
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
