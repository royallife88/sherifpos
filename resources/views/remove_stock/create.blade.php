@extends('layouts.app')
@section('title', __('lang.remove_stock'))

@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.remove_stock')</h4>
                        </div>
                        {!! Form::open(['url' => action('RemoveStockController@store'), 'method' => 'post', 'id' => 'remove_stock_form', 'enctype' => 'multipart/form-data']) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('store_id', __('lang.store') . ':*', []) !!}
                                        {!! Form::select('store_id', $stores, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('supplier_id', __('lang.supplier') . ':*', []) !!}
                                        {!! Form::select('supplier_id', $suppliers, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('invoice_id', __('lang.invoice_no') . ':*', []) !!} <i class="dripicons-question" data-toggle="tooltip"
                                            title="@lang('lang.invoice_no_remove_stock_info')"></i>
                                        {!! Form::select('invoice_id', $invoice_nos, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" value="1"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.image')</button>
                                    <button type="button" value="4"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.class')</button>
                                    <button type="button" value="5"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.category')</button>
                                    <button type="button" value="6"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.sub_category')</button>
                                    <button type="button" value="7"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.color')</button>
                                    <button type="button" value="8"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.size')</button>
                                    <button type="button" value="9"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.grade')</button>
                                    <button type="button" value="10"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.unit')</button>
                                    <button type="button" value="11"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock')</button>
                                    <button type="button" value="12"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.supplier')</button>
                                    <button type="button" value="13"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.email')</button>
                                    <button type="button" value="14"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.invoice_no')</button>
                                    <button type="button" value="15"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.invoice_date')</button>
                                    <button type="button" value="16"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.payment_status')</button>
                                    <button type="button" value="17"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.notes')</button>
                                    <button type="button" value="18"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.quantity')</button>
                                    <button type="button" value="19"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.remove_quantity')</button>
                                    @can('product_module.purchase_price.view')
                                        <button type="button" value="20"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
                                    @endcan
                                    <button type="button" value="21"
                                        class="badge badge-pill badge-primary column-toggle">@lang('lang.sell_price')</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div id="product_table_div" class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed"
                                            id="product_table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>@lang('lang.image')</th>
                                                    <th>@lang('lang.products')</th>
                                                    <th>@lang('lang.sku')</th>
                                                    <th>@lang('lang.class')</th>
                                                    <th>@lang('lang.category')</th>
                                                    <th>@lang('lang.sub_category')</th>
                                                    <th>@lang('lang.color')</th>
                                                    <th>@lang('lang.size')</th>
                                                    <th>@lang('lang.grade')</th>
                                                    <th>@lang('lang.unit')</th>
                                                    <th>@lang('lang.current_stock')</th>
                                                    <th>@lang('lang.supplier')</th>
                                                    <th style="width: 100px !important">@lang('lang.email')</th>
                                                    <th>@lang('lang.invoice_no')</th>
                                                    <th>@lang('lang.invoice_date')</th>
                                                    <th>@lang('lang.payment_status')</th>
                                                    <th>@lang('lang.notes')</th>
                                                    <th>@lang('lang.quantity')</th>
                                                    <th>@lang('lang.remove_quantity')</th>
                                                    <th>@lang('lang.purchase_price')</th>
                                                    <th>@lang('lang.sell_price')</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="final_total" id="final_total" value="0">
                            <div class="row">
                                <div class="col-md-12"
                                    style="text-align: right; font-size: 20px; font-weight: bold; padding: 20px;">
                                    @lang('lang.total'):<span class="final_total_span"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('files', __('lang.files'), []) !!} <br>
                                        {!! Form::file('files[]', null, ['class' => '']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('reason', __('lang.reason') . ':', []) !!} <br>
                                        {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('notes', __('lang.notes') . ':', []) !!} <br>
                                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                    </div>
                                </div>

                            </div>

                        </div>
                        <input type="hidden" id="is_raw_material" name="is_raw_material" value="{{ $is_raw_material }}">
                        <input type="hidden" id="product_data" name="product_data" value="[]">
                        <input type="hidden" id="transaction_array" name="transaction_array" value="[]">
                        <div class="col-sm-12">
                            <button type="submit" name="submit" id="print" style="margin: 10px" value="print"
                                class="btn btn-danger pull-right btn-flat submit">@lang('lang.print')</button>
                            <button type="submit" name="submit" id="send_to_supplier" style="margin: 10px"
                                value="send_to_supplier"
                                class="btn btn-warning pull-right btn-flat submit">@lang('lang.send_to_supplier')</button>
                            <button type="submit" name="submit" id="save" style="margin: 10px" value="save"
                                class="btn btn-primary pull-right btn-flat submit">@lang('lang.delete')</button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/add_stock.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
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
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [2, 'asc']
                ],
                "ajax": {
                    "url": "/remove-stock/get-invoice-details",
                    "data": function(d) {
                        d.supplier_id = $('#supplier_id').val();
                        d.invoice_id = $('#invoice_id').val();
                        d.store_id = $('#store_id').val();
                        @if ($is_raw_material)
                            d.is_raw_material = 1;
                        @endif

                    }
                },
                columnDefs: [{
                    "targets": [0,1],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'selected_product',
                        name: 'selected_product'
                    },
                    {
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
                    {
                        data: 'category',
                        name: 'categories.name'
                    },
                    {
                        data: 'sub_category',
                        name: 'sub_categories.name'
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
                        data: 'unit',
                        name: 'units.name'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock'
                    },
                    {
                        data: 'supplier',
                        name: 'suppliers.name'
                    },
                    {
                        data: 'supplier_email',
                        name: 'suppliers.email'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'quantity',
                        name: 'add_stock_lines.quantity'
                    },
                    {
                        data: 'remove_qauntity',
                        name: 'remove_qauntity'
                    },
                    {
                        data: 'purchase_price',
                        name: 'add_stock_lines.purchase_price'
                    },
                    {
                        data: 'sell_price',
                        name: 'products.sell_price'
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

        var data_array = [];
        var transaction_array = [];
        $(document).on('change', '.quantity', function() {
            let tr = $(this).closest('tr');

            let qty = parseFloat($(tr).find('.quantity').val());
            let max_stock = parseFloat($(this).attr('max'));
            $(tr).find('.stock_error').addClass('hide');
            $(tr).find('.product_checkbox').prop('checked', false);

            if (qty < 0) {
                $(tr).find('.qty').val(0);
                return;
            }
            if (qty > max_stock) {
                $(tr).find('.stock_error').removeClass('hide');
                return;

            }
            if (qty) {
                $(tr).find('.product_checkbox').prop('checked', true);
                let row_index = $(tr).find('.row_index').val();
                let product_id = $(tr).find('.product_id').val();
                let variation_id = $(tr).find('.variation_id').val();
                let store_id = $(tr).find('.store_id').val();
                let qty = $(tr).find('.quantity').val();
                let purchase_price = $(tr).find('.purchase_price').val();
                let transaction_id = $(tr).find('.transaction_id').val();
                let email = $(tr).find('.email').val();
                let notes = $(tr).find('.notes').val();
                transaction_array.push(transaction_id);

                transaction_array = $.grep(transaction_array, function(v, i) {
                    return $.inArray(v, transaction_array) === i;
                });

                data_array[row_index] = {
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'store_id': store_id,
                    'qty': qty,
                    'purchase_price': purchase_price,
                    'transaction_id': transaction_id,
                    'email': email,
                    'notes': notes,
                }
                console.log(data_array, 'data_array');
                $('#product_data').val(JSON.stringify(data_array));
                $('#transaction_array').val(JSON.stringify(transaction_array));
            } else {
                $(tr).find('.product_checkbox').prop('checked', false);
            }
            calculate_sub_totals()
        })

        $('#invoice_id, #supplier_id, #store_id').change(function() {
            product_table.ajax.reload();
        });

        var hidden_column_array = $.cookie('column_visibility_remove_stock') ? JSON.parse($.cookie(
            'column_visibility_remove_stock')) : [];

        function toggleColumnInCookie() {
            $.each(hidden_column_array, function(index, value) {
                $('.column-toggle').each(function() {
                    if ($(this).val() == value) {
                        toggleColumnVisibility(value, $(this));
                    }
                });

            });
        }

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

            $.cookie('column_visibility_remove_stock', JSON.stringify(hidden_column_array));
        })
        $(document).ready(function() {
            toggleColumnInCookie()
        });
        $('#supplier_id').change(function() {
            let supplier_id = $(this).val();

            if (supplier_id) {
                $.ajax({
                    method: 'get',
                    url: '/remove-stock/get-supplier-invoices-dropdown/' + supplier_id,
                    data: {},
                    dataType: 'html',
                    success: function(result) {
                        $('#invoice_id').html(result);
                        $('#invoice_id').selectpicker('refresh');

                    },
                });
            }
        });
    </script>
@endsection
