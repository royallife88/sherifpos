@extends('layouts.app')
@section('title', __('lang.sale_return'))

@section('content')
    <div class="container-fluid no-print">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.sale_return')</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                    {!! Form::select('customer_id', $customers, request()->customer_id, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_status', __('lang.payment_status'), []) !!}
                                    {!! Form::select('payment_status', $payment_status_array, request()->payment_status, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('pos_id', __('lang.pos'), []) !!}
                                    {!! Form::select('pos_id', $store_pos, request()->pos_id, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            @if (session('system_mode') == 'restaurant')
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('dining_room_id', __('lang.dining_room'), []) !!}
                                        {!! Form::select('dining_room_id', $dining_rooms, request()->dining_room_id, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('dining_table_id', __('lang.dining_table'), []) !!}
                                        {!! Form::select('dining_table_id', $dining_tables, request()->dining_table_id, ['class' => 'form-control sale_filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                    {!! Form::text('start_date', request()->start_date, ['class' => 'form-control sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                    {!! Form::text('start_time', request()->start_time, ['class' => 'form-control time_picker sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                    {!! Form::text('end_date', request()->end_date, ['class' => 'form-control sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                    {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <br>
                                <button type="button" href="{{ action('SellReturnController@index') }}"
                                    class="btn btn-danger mt-2 ml-2 clear_filter">@lang('lang.clear_filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive no-print">
            <table id="sell_return_table" class="table">
                <thead>
                    <tr>
                        <th class="date">@lang('lang.date')</th>
                        <th>@lang('lang.reference')</th>
                        <th>@lang('lang.customer')</th>
                        <th>@lang('lang.payment_status')</th>
                        <th>@lang('lang.payment_type')</th>
                        <th class="currencies">@lang('lang.paying_currency')</th>
                        <th class="sum">@lang('lang.grand_total')</th>
                        <th class="sum">@lang('lang.paid')</th>
                        <th class="sum">@lang('lang.due')</th>
                        <th>@lang('lang.notes')</th>
                        <th>@lang('lang.files')</th>
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
                        <th class="table_totals" style="text-align: right">@lang('lang.totals')</th>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- This will be printed -->
    <section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            sell_return_table = $("#sell_return_table").DataTable({
                lengthChange: true,
                paging: true,
                info: false,
                bAutoWidth: false,
                // order: [],
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
                    [0, "desc"]
                ],
                initComplete: function() {
                    $(this.api().table().container()).find('input').parent().wrap('<form>').parent()
                        .attr('autocomplete', 'off');
                },
                ajax: {
                    url: "/sale-return",
                    data: function(d) {
                        d.customer_id = $("#customer_id").val();
                        d.dining_room_id = $("#dining_room_id").val();
                        d.dining_table_id = $("#dining_table_id").val();
                        d.store_id = $("#store_id").val();
                        d.payment_status = $("#payment_status").val();
                        d.start_date = $("#start_date").val();
                        d.start_time = $("#start_time").val();
                        d.end_date = $("#end_date").val();
                        d.end_time = $("#end_time").val();
                        d.payment_start_date = $("#payment_start_date").val();
                        d.payment_start_time = $("#payment_start_time").val();
                        d.payment_end_date = $("#payment_end_date").val();
                        d.payment_end_time = $("#payment_end_time").val();
                        d.created_by = $("#created_by").val();
                    },
                },
                columnDefs: [{
                        targets: "date",
                        type: "date-eu",
                    },
                    {
                        targets: [10],
                        orderable: false,
                        searchable: false,
                    }
                ],
                columns: [{
                        data: "transaction_date",
                        name: "transaction_date"
                    },
                    {
                        data: "invoice_no",
                        name: "invoice_no"
                    },
                    {
                        data: "customer_name",
                        name: "customers.name"
                    },
                    {
                        data: "payment_status",
                        name: "transactions.payment_status"
                    },
                    {
                        data: "method",
                        name: "transaction_payments.method"
                    },
                    {
                        data: "received_currency_symbol",
                        name: "received_currency_symbol",
                        searchable: false
                    },
                    {
                        data: "final_total",
                        name: "final_total"
                    },
                    {
                        data: "paid",
                        name: "transaction_payments.amount",
                        searchable: false
                    },
                    {
                        data: "due",
                        name: "due",
                        searchable: false
                    },
                    {
                        data: "notes",
                        name: "notes"
                    },
                    {
                        data: "files",
                        name: "files",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                createdRow: function(row, data, dataIndex) {},
                footerCallback: function(row, data, start, end, display) {
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
            $(document).on('change', '.sale_filter', function() {
                sell_return_table.ajax.reload();
            });
        })

        $(document).on('click', '.clear_filter', function() {
            $('.sale_filter').val('');
            $('.sale_filter').selectpicker('refresh');
            sell_return_table.ajax.reload();
        });

        $(document).on('click', '.print-invoice', function() {
            $.ajax({
                method: 'get',
                url: $(this).data('href'),
                data: {},
                success: function(result) {
                    if (result.success) {
                        pos_print(result.html_content);
                    }
                },
            });
        });
        $(document).on('change', '#dining_room_id', function() {
            let dining_room_id = $(this).val();

            $.ajax({
                method: 'GET',
                url: '/dining-table/get-dropdown-by-dining-room/' + dining_room_id,
                data: {},
                success: function(result) {
                    $('#dining_table_id').html(result);
                    $('#dining_table_id').selectpicker('refresh');
                },
            });

        });

        function pos_print(receipt) {
            $("#receipt_section").html(receipt);
            __currency_convert_recursively($("#receipt_section"));
            __print_receipt("receipt_section");
        }
    </script>
@endsection
