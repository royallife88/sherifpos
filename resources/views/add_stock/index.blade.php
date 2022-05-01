@extends('layouts.app')
@if (request()->segment(1) == 'raw-material')
    @section('title', __('lang.view_all_stock_for_raw_material'))
@else
    @section('title', __('lang.add_stock'))
@endif

@section('content')
    <section class="">
        <div class="col-md-22">
            <div class="card">
                <div class="card-body">
                    <form action="">
                        <input type="hidden" name="is_raw_material" id="is_raw_material"
                            value="@if (request()->segment(1) == 'raw-material') {{ 1 }}@else{{ 0 }} @endif">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control filters', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                    {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' => 'form-control filters', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('created_by', __('lang.added_by'), []) !!}
                                    {!! Form::select('created_by', $users, request()->created_by, ['class' => 'form-control filters', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('product_id', __('lang.product'), []) !!}
                                    {!! Form::select('product_id', $products, request()->product_id, ['class' => 'form-control filters', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                    {!! Form::text('start_date', request()->start_date, ['class' => 'form-control ', 'id' => 'start_date']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                    {!! Form::text('start_time', null, ['class' => 'form-control time_picker sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                    {!! Form::text('end_date', request()->end_date, ['class' => 'form-control ', 'id' => 'end_date']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                    {!! Form::text('end_time', null, ['class' => 'form-control time_picker sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button"
                                    class="btn btn-danger clear_filters mt-2 ml-2">@lang('lang.clear_filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="add_stock_table">
                <thead>
                    <tr>
                        <th>@lang('lang.po_ref_no')</th>
                        <th>@lang('lang.invoice_no')</th>
                        <th>@lang('lang.date_and_time')</th>
                        <th>@lang('lang.invoice_date')</th>
                        <th>@lang('lang.supplier')</th>
                        <th>@lang('lang.created_by')</th>
                        <th class="currencies">@lang('lang.paying_currency')</th>
                        <th class="sum">@lang('lang.value')</th>
                        <th class="sum">@lang('lang.paid_amount')</th>
                        <th class="sum">@lang('lang.pending_amount')</th>
                        <th>@lang('lang.due_date')</th>
                        <th>@lang('lang.notes')</th>
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
                        <th class="table_totals" style="text-align: right">@lang('lang.total')</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            add_stock_table = $('#add_stock_table').DataTable({
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
                stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [2, 'desc']
                ],
                "ajax": {
                    "url": "/add-stock",
                    "data": function(d) {
                        d.is_raw_material = $('#is_raw_material').val();
                        d.store_id = $('#store_id').val();
                        d.supplier_id = $('#supplier_id').val();
                        d.created_by = $('#created_by').val();
                        d.product_id = $('#product_id').val();
                        d.start_date = $('#start_date').val();
                        d.start_time = $("#start_time").val();
                        d.end_date = $('#end_date').val();
                        d.end_time = $("#end_time").val();
                    }
                },
                columnDefs: [{
                    "targets": [0, 3],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'po_no',
                        name: 'po_no'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'supplier',
                        name: 'suppliers.name'
                    },
                    {
                        data: 'created_by',
                        name: 'users.name'
                    },
                    {
                        data: 'paying_currency_symbol',
                        name: 'paying_currency_symbol',
                        searchable: false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount'
                    },
                    {
                        data: 'due',
                        name: 'due'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
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
                        .columns(".currencies", {
                            page: "current"
                        }).every(function() {
                            var column = this;
                            let currencies_html = '';
                            $.each(currency_obj, function(key, value) {
                                currencies_html +=
                                    `<h6 class="footer_currency" data-is_default="${value.is_default}"  data-currency_id="${value.currency_id}">${value.symbol}</h6>`
                                $(column.footer()).html(currencies_html);
                            });
                        })
                    this.api()
                        .columns(".sum", {
                            page: "current"
                        })
                        .every(function() {
                            var column = this;
                            var currency_total = [];
                            $.each(currency_obj, function(key, value) {
                                currency_total[value.currency_id] = 0;
                            });
                            column.data().each(function(group, i) {
                                b = $(group).text();
                                currency_id = $(group).data('currency_id');

                                $.each(currency_obj, function(key, value) {
                                    if (currency_id == value.currency_id) {
                                        currency_total[value.currency_id] += intVal(
                                            b);
                                    }
                                });
                            });
                            var footer_html = '';
                            $.each(currency_obj, function(key, value) {
                                footer_html +=
                                    `<h6 class="currency_total currency_total_${value.currency_id}" data-currency_id="${value.currency_id}" data-is_default="${value.is_default}" data-conversion_rate="${value.conversion_rate}" data-base_conversion="${currency_total[value.currency_id] * value.conversion_rate}" data-orig_value="${currency_total[value.currency_id]}">${__currency_trans_from_en(currency_total[value.currency_id], false)}</h6>`
                            });
                            $(column.footer()).html(
                                footer_html
                            );
                        });
                },
            });
            $(document).on('click', '.filters', function() {
                add_stock_table.ajax.reload();
            })
            $('#end_date, #start_date').change(function() {
                add_stock_table.ajax.reload();
            })
        });



        $('.time_picker').focusout(function(event) {
            add_stock_table.ajax.reload();
        });

        $(document).on('click', '.clear_filters', function() {
            $('.filters').val('');
            $('.filters').selectpicker('refresh')
            add_stock_table.ajax.reload();
        })
    </script>
@endsection
