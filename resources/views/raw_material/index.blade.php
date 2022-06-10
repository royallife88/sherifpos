@extends('layouts.app')
@section('title', __('lang.raw_materials'))

@section('content')
    <div class="container-fluid">
        @can('product_module.raw_material.create_and_edit')
            <a style="color: white" href="{{ action('RawMaterialController@create') }}" class="btn btn-info"><i
                    class="dripicons-plus"></i>
                @lang('lang.add_new_raw_material')</a>
        @endcan

        <div class="card mt-3">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label(
    'variation_id',
    __('lang.product') .
        ':
                        (' .
        __('lang.that_raw_materials_are_used_for') .
        ')',
    [],
) !!}
                            {!! Form::select('variation_id', $products, request()->variation_id, [
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

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control filter_product', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
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
                        <button class="btn btn-danger mt-4 clear_filters">@lang('lang.clear_filters')</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" value="0"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.image')</button>

                <button type="button" value="2"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.product_code')</button>

                <button type="button" value="3"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_history')</button>
                <button type="button" value="4"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.batch_number')</button>
                <button type="button" value="5"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.brand')</button>
                <button type="button" value="6"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock')</button>
                <button type="button" value="7"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.unit')</button>
                <button type="button" value="8"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.manufacturing_date')</button>
                <button type="button" value="9"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.expiry_date')</button>
                <button type="button" value="10"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.created_by')</button>
                <button type="button" value="11"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.edited_by')</button>
                <button type="button" value="12"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.products')</button>
                <button type="button" value="13"
                    class="badge badge-pill badge-primary column-toggle">@lang('lang.supplier')</button>
                @can('product_module.purchase_price.view')
                    <button type="button" value="14"
                        class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
                @endcan
            </div>
        </div>


    </div>
    <div class="table-responsive">
        <table id="raw_material_table" class="table ajax_view" style="width: auto">
            <thead>
                <tr>
                    <th>@lang('lang.image')</th>
                    <th>@lang('lang.name')</th>
                    <th>@lang('lang.product_code')</th>
                    <th>@lang('lang.purchase_history')</th>
                    <th>@lang('lang.batch_number')</th>
                    <th>@lang('lang.brand')</th>
                    <th class="sum">@lang('lang.current_stock')</th>
                    <th>@lang('lang.unit')</th>
                    <th>@lang('lang.manufacturing_date')</th>
                    <th>@lang('lang.expiry_date')</th>
                    <th>@lang('lang.created_by')</th>
                    <th>@lang('lang.edited_by')</th>
                    <th>@lang('lang.products')</th>
                    <th>@lang('lang.supplier')</th>
                    @can('product_module.purchase_price.view')
                        <th>@lang('lang.purchase_price')</th>
                    @endcan
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
                    <th style="text-align: right">@lang('lang.total')</th>
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
            raw_material_table = $('#raw_material_table').DataTable({
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
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [2, 'asc']
                ],
                "ajax": {
                    "url": "/raw-material",
                    "data": function(d) {
                        d.variation_id = $('#variation_id').val();
                        d.brand_id = $('#brand_id').val();
                        d.store_id = $('#store_id').val();
                        d.created_by = $('#created_by').val();
                    }
                },
                columnDefs: [{
                    "targets": [0, 3, 15],
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
                        data: 'purchase_history',
                        name: 'purchase_history'
                    },
                    {
                        data: 'batch_number',
                        name: 'add_stock_lines.batch_number'
                    },
                    {
                        data: 'brand',
                        name: 'brands.name'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock',
                        searchable: false
                    },
                    {
                        data: 'unit',
                        name: 'units.name'
                    },
                    {
                        data: 'manufacturing_date',
                        name: 'add_stock_lines.manufacturing_date'
                    },
                    {
                        data: 'exp_date',
                        name: 'add_stock_lines.expiry_date'
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
                        data: 'products_view',
                        name: 'products_view',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'supplier_name',
                        name: 'suppliers.name'
                    },
                    @can('product_module.purchase_price.view')
                        {
                            data: 'default_purchase_price',
                            name: 'variations.default_purchase_price'
                        },
                    @endcan {
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

                    __currency_convert_recursively($('#raw_material_table'));
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
            column = raw_material_table.column(column_index);
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
            raw_material_table.ajax.reload();
        })
        $(document).on('click', '.clear_filters', function() {
            $('.filter_product').val('');
            $('.filter_product').selectpicker('refresh')
            raw_material_table.ajax.reload();
        })
    </script>
@endsection
