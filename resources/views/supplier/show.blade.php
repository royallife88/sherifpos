@extends('layouts.app')
@section('title', __('lang.supplier_details'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.supplier_details')</h4>
            </div>
            <form action="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                            <a href="{{ action('SupplierController@show', $supplier->id) }}"
                                class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body">
                <div class="col-md-12">
                    <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if (empty(request()->show)) active @endif" href="#info-sale" role="tab"
                                data-toggle="tab">@lang('lang.info')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'pending_orders') active @endif" href="#pending-orders"
                                role="tab" data-toggle="tab">@lang('lang.pending_orders')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'statement_of_account') active @endif" href="#statement-of-account"
                                role="tab" data-toggle="tab">@lang('lang.statement_of_account')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'service_provided') active @endif" href="#service-provided"
                                role="tab" data-toggle="tab">@lang('lang.service_provided')</a>
                        </li>


                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade @if (empty(request()->show)) show active @endif"
                            id="info-sale">
                            <br>
                            <br>
                            <div class="col-md-12 text-muted">
                                <div class="row">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $supplier->id }}">
                                    <div class="col-md-6">
                                        <div class="col-md-12 ">
                                            <b>@lang('lang.name'):</b> <span
                                                class="customer_name_span">{{ $supplier->name }}</span>
                                        </div>

                                        <div class="col-md-12">
                                            <b>@lang('lang.company_name'):</b> <span
                                                class="customer_company_name_span">{{ $supplier->company_name }}</span>
                                        </div>

                                        <div class="col-md-12">
                                            <b>@lang('lang.vat_number'):</b> <span
                                                class="customer_vat_number_span">{{ $supplier->vat_number }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.email'):</b> <span
                                                class="customer_email_span">{{ $supplier->email }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.mobile'):</b> <span
                                                class="customer_mobile_span">{{ $supplier->mobile }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.address'):</b> <span
                                                class="customer_address_span">{{ $supplier->address }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.city'):</b> <span
                                                class="customer_city_span">{{ $supplier->city }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.state'):</b> <span
                                                class="customer_state_span">{{ $supplier->state }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.postal_code'):</b> <span
                                                class="customer_postal_code_span">{{ $supplier->postal_code }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.country'):</b> <span
                                                class="customer_country_span">{{ $supplier->country }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <div class="thumbnail">
                                                <img style="width: 200px; height: 200px;" class="img-fluid"
                                                    src="@if (!empty($supplier->getFirstMediaUrl('supplier_photo'))) {{ $supplier->getFirstMediaUrl('supplier_photo') }} @endif"
                                                    alt="Supplier photo">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'pending_orders') show active @endif"
                            id="pending-orders">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.po_ref_no')</th>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.created_by')</th>
                                            <th>@lang('lang.supplier')</th>
                                            <th>@lang('lang.value')</th>
                                            <th>@lang('lang.status')</th>
                                            <th class="notexport">@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($purchase_orders as $purchase_order)
                                            <tr>
                                                <td>{{ $purchase_order->po_no }}</td>
                                                <td> {{ @format_date($purchase_order->transaction_date) }}</td>
                                                <td>
                                                    {{ ucfirst($purchase_order->created_by_user->name ?? '') }}
                                                </td>
                                                <td>
                                                    @if (!empty($purchase_order->supplier))
                                                        {{ $purchase_order->supplier->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ @num_format($purchase_order->final_total) }}
                                                </td>
                                                <td>
                                                    {{ $status_array[$purchase_order->status] }}
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
                                                            @can('purchase_order.purchase_order.view')
                                                                <li>
                                                                    <a href="{{ action('PurchaseOrderController@show', $purchase_order->id) }}"
                                                                        class=""><i class="fa fa-eye btn"></i>
                                                                        @lang('lang.view')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan
                                                            @can('purchase_order.purchase_order.create_and_edit')
                                                                <li>
                                                                    <a
                                                                        href="{{ action('PurchaseOrderController@edit', $purchase_order->id) }}"><i
                                                                            class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan
                                                            @can('purchase_order.purchase_order.delete')
                                                                <li>
                                                                    <a data-href="{{ action('PurchaseOrderController@destroy', $purchase_order->id) }}"
                                                                        data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                        class="btn text-red delete_item"><i
                                                                            class="fa fa-trash"></i>
                                                                        @lang('lang.delete')</a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'statement_of_account') show active @endif"
                            id="statement-of-account">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th class="sum">@lang('lang.paid')</th>
                                            <th class="sum">@lang('lang.due')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.due_date')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_purchase_payments = 0;
                                            $total_purchase_due = 0;
                                        @endphp
                                        @foreach ($add_stocks as $add_stock)
                                            <tr>
                                                <td>{{ @format_date($add_stock->transaction_date) }}</td>
                                                <td>{{ $add_stock->invoice_no }}</td>
                                                <td>
                                                    @if ($add_stock->type == 'purchase_return')
                                                        {{ @num_format(-$add_stock->final_total) }}@else{{ @num_format($add_stock->final_total) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($add_stock->type == 'purchase_return')
                                                        {{ @num_format(-$add_stock->transaction_payments->sum('amount')) }}@else{{ @num_format($add_stock->transaction_payments->sum('amount')) }}
                                                    @endif
                                                </td>
                                                <td>{{ @num_format($add_stock->final_total - $add_stock->transaction_payments->sum('amount')) }}
                                                </td>
                                                <td>{{ ucfirst($add_stock->status) }}</td>
                                                <td>
                                                    @if ($add_stock->payment_status != 'paid')
                                                        @if (!empty($add_stock->due_date))
                                                            {{ @format_date($add_stock->due_date) }}
                                                        @endif
                                                    @endif
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
                                                            @if ($add_stock->type == 'add_stock')
                                                                @can('stock.add_stock.view')
                                                                    <li>
                                                                        <a target="_blank"
                                                                            href="{{ action('AddStockController@show', $add_stock->id) }}"
                                                                            class="btn "><i
                                                                                class="fa fa-eye"></i>
                                                                            @lang('lang.view')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan

                                                                @can('stock.add_stock.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('AddStockController@edit', $add_stock->id) }}"
                                                                            class="btn"><i
                                                                                class="dripicons-document-edit"></i>
                                                                            @lang('lang.edit')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan

                                                                @can('stock.add_stock.delete')
                                                                    <li>
                                                                        <a data-href="{{ action('AddStockController@destroy', $add_stock->id) }}"
                                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                            class="btn text-red delete_item"><i
                                                                                class="fa fa-trash"></i>
                                                                            @lang('lang.delete')</a>
                                                                    </li>
                                                                @endcan
                                                                @if ($add_stock->payment_status != 'paid')
                                                                    <li>
                                                                        <a data-href="{{ action('TransactionPaymentController@addPayment', ['id' => $add_stock->id]) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="fa fa-money"></i>
                                                                            @lang('lang.pay')</a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                            @if ($add_stock->type == 'purchase_return')
                                                                @can('return.purchase_return.view')
                                                                    <li>

                                                                        <a data-href="{{ action('PurchaseReturnController@show', $add_stock->id) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="fa fa-eye"></i>
                                                                            @lang('lang.view')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('return.purchase_return.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('PurchaseReturnController@edit', $add_stock->id) }}"
                                                                            class="btn"><i
                                                                                class="dripicons-document-edit"></i>
                                                                            @lang('lang.edit')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('return.purchase_return_pay.create_and_edit')
                                                                    @if ($add_stock->payment_status != 'paid')
                                                                        <li>
                                                                            <a data-href="{{ action('TransactionPaymentController@addPayment', ['id' => $add_stock->id]) }}"
                                                                                data-container=".view_modal"
                                                                                class="btn btn-modal"><i
                                                                                    class="fa fa-plus"></i>
                                                                                @lang('lang.add_payment')</a>
                                                                        </li>
                                                                    @endif
                                                                @endcan
                                                                @can('return.purchase_return_pay.view')
                                                                    @if ($add_stock->payment_status != 'pending')
                                                                        <li>
                                                                            <a data-href="{{ action('TransactionPaymentController@show', $add_stock->id) }}"
                                                                                data-container=".view_modal"
                                                                                class="btn btn-modal"><i
                                                                                    class="fa fa-money"></i>
                                                                                @lang('lang.view_payments')</a>
                                                                        </li>
                                                                    @endif
                                                                @endcan
                                                                @can('return.purchase_return.delete')
                                                                    <li>
                                                                        <a data-href="{{ action('PurchaseReturnController@destroy', $add_stock->id) }}"
                                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                            class="btn text-red delete_item"><i
                                                                                class="fa fa-trash"></i>
                                                                            @lang('lang.delete')</a>
                                                                    </li>
                                                                @endcan
                                                            @endif
                                                        </ul>
                                                    </div>
                                            </tr>
                                            @php
                                                $total_purchase_payments += $add_stock->transaction_payments->sum('amount');
                                                $total_purchase_due += $add_stock->final_total - $add_stock->transaction_payments->sum('amount');
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
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
                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'service_provided') show active @endif"
                            id="service-provided">
                            <div class="table-responsive">
                                <table class="table" id="supplier_service_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date_and_time')</th>
                                            <th>@lang('lang.supplier')</th>
                                            <th>@lang('lang.cashier')</th>
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
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            supplier_service_table = $('#supplier_service_table').DataTable({
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
                    "url": "/supplier-service",
                    "data": function(d) {
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
                supplier_service_table.ajax.reload();
            })
            $('#end_date, #start_date').change(function() {
                supplier_service_table.ajax.reload();
            })
        });



        $('.time_picker').focusout(function(event) {
            supplier_service_table.ajax.reload();
        });

        $(document).on('click', '.clear_filters', function() {
            $('.sale_filter').val('');
            $('.filters').val('');
            $('.filters').selectpicker('refresh')
            supplier_service_table.ajax.reload();
        })
    </script>
@endsection
