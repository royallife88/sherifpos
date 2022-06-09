@extends('layouts.app')
@section('title', __('lang.sales_per_employee'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.sales_per_employee')</h4>
            </div>
            <form action="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                {!! Form::text('start_time', null, ['class' => 'form-control time_picker filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                {!! Form::text('end_time', null, ['class' => 'form-control time_picker filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('employee_id', __('lang.employee'), []) !!}
                                {!! Form::select('employee_id', $employees, request()->employee_id, ['class' => 'form-control filter', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button href="{{ action('ReportController@getDueReport') }}" type="button"
                                class="btn btn-danger mt-2 ml-2 clear_filter">@lang('lang.clear_filter')</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table" id="sales_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.reference')</th>
                                    <th>@lang('lang.employee')</th>
                                    <th class="currencies">@lang('lang.currency')</th>
                                    <th class="sum">@lang('lang.commission')</th>
                                    <th class="sum">@lang('lang.paid')</th>
                                    <th class="sum">@lang('lang.due')</th>
                                    <th>@lang('lang.payment_type')</th>
                                    <th>@lang('lang.payment_status')</th>
                                    <th class="notexport">@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <th style="text-align: right">@lang('lang.total')</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This will be printed -->
    <section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            sales_table = $("#sales_table").DataTable({
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
                    url: "/report/get-sales-per-employee-report",
                    data: function(d) {
                        d.employee_id = $("#employee_id").val();
                        d.method = $("#method").val();
                        d.start_date = $("#start_date").val();
                        d.start_time = $("#start_time").val();
                        d.end_date = $("#end_date").val();
                        d.end_time = $("#end_time").val();
                        d.created_by = $("#created_by").val();
                    },
                },
                columnDefs: [{
                        targets: "date",
                        type: "date-eu",
                    },
                    {
                        targets: [7],
                        orderable: false,
                        searchable: false,
                    },
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
                        data: "employee_name",
                        name: "employees.employee_name"
                    },
                    {
                        data: "paying_currency_symbol",
                        name: "paying_currency_symbol",
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
                        name: "transaction_payments.amount",
                        searchable: false
                    },
                    {
                        data: "method",
                        name: "transaction_payments.method",
                    },
                    {
                        data: "payment_status",
                        name: "payment_status",
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
            $(document).on('change', '.filter', function() {
                sales_table.ajax.reload();
            });
        })
        $(document).on('click', '.clear_filter', function() {
            $('.filter').val('');
            $('.filter').selectpicker('refresh');
            sales_table.ajax.reload();
        });
        $('.time_picker').focusout(function(event) {
            sales_table.ajax.reload();
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

        $(document).on('click', '.print-invoice', function() {
            $('.view_modal').modal('hide')
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
        })

        function pos_print(receipt) {
            $("#receipt_section").html(receipt);
            __currency_convert_recursively($("#receipt_section"));
            __print_receipt("receipt_section");
        }
    </script>
@endsection
