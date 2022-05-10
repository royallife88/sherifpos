@extends('layouts.app')
@section('title', __('lang.payable_report'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.payable_report')</h4>
            </div>
            <form action="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                {!! Form::text('start_time', request()->start_time, [
    'class' => 'form-control
                            time_picker sale_filter',
]) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                {!! Form::text('end_time', request()->end_time, [
    'class' => 'form-control time_picker
                            sale_filter',
]) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                            </div>
                        </div>
                        @if (session('user.is_superadmin'))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('pos_id', __('lang.pos'), []) !!}
                                    {!! Form::select('pos_id', $store_pos, request()->pos_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('product_id', __('lang.product'), []) !!}
                                {!! Form::select('product_id', $products, request()->product_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <br>
                            <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                            <a href="{{ action('ReportController@getPayableReport') }}"
                                class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table" id="add_stock_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang.invoice_no')</th>
                                    <th>@lang('lang.date_and_time')</th>
                                    <th>@lang('lang.invoice_date')</th>
                                    <th>@lang('lang.supplier')</th>
                                    <th class="currencies">@lang('lang.paying_currency')</th>
                                    <th class="sum">@lang('lang.amount')</th>
                                    <th>@lang('lang.created_by')</th>
                                    <th class="notexport">@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- @foreach ($add_stocks as $add_stock)
                                    <tr>
                                        <td>{{ $add_stock->invoice_no }}</td>
                                        <td>{{ @format_datetime($add_stock->created_at) }}</td>
                                        <td> {{ @format_date($add_stock->transaction_date) }}</td>
                                        <td>
                                            {{ $add_stock->supplier->name }}
                                        </td>
                                        <td>
                                            {{ @num_format($add_stock->final_total) }}
                                        </td>
                                        <td>
                                            {{ ucfirst($add_stock->created_by_user->name ?? '') }}
                                        </td>
                                        <td>

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @can('stock.add_stock.view')
                                                        <li>
                                                            <a href="{{ action('AddStockController@show', $add_stock->id) }}"
                                                                class=""><i class="fa fa-eye btn"></i>
                                                                @lang('lang.view')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.create_and_edit')
                                                        <li>
                                                            <a href="{{ action('AddStockController@edit', $add_stock->id) }}"><i
                                                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('stock.add_stock.delete')
                                                        <li>
                                                            <a data-href="{{ action('AddStockController@destroy', $add_stock->id) }}"
                                                                data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                class="btn text-red delete_item"><i class="dripicons-trash"></i>
                                                                @lang('lang.delete')</a>
                                                        </li>
                                                    @endcan
                                                    @can('stock.pay.create_and_edit')
                                                        @if ($add_stock->payment_status != 'paid')
                                                            <li>
                                                                <a data-href="{{ action('TransactionPaymentController@addPayment', ['id' => $add_stock->id]) }}"
                                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                                        class="fa fa-money"></i>
                                                                    @lang('lang.pay')</a>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">@lang('lang.total')</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
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
                    "url": "/report/get-payable-report",
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
                columns: [
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
                        data: 'supplier_name',
                        name: 'suppliers.name'
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
                        data: 'created_by',
                        name: 'users.name'
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
