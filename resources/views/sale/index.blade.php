@extends('layouts.app')
@section('title', __('lang.sales_list'))

@section('content')
<div class="container-fluid no-print">
    {{-- @can('sale.pos.create_and_edit')
    <a style="color: white" href="{{action('SellPosController@create')}}" class="btn btn-info"><i
            class="dripicons-plus"></i>
        @lang('lang.add_sale')</a>
    @endcan --}}
</div>
<br>
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.sales_list')</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('product_class_id', session('system_mode') == 'restaurant' ? __('lang.category') : __('lang.product_class') . ':', []) !!}
                        {!! Form::select('product_class_id[]', $product_classes, request()->product_class_id, ['class'
                        => 'form-control sale_filter
                        selectpicker', 'data-live-search' =>'true', "data-actions-box"=>"true", 'placeholder' => __('lang.all'), 'multiple', 'id' => 'product_class_id']) !!}
                    </div>
                </div>
                @if(session('system_mode') != 'restaurant')
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('lang.category') . ':', []) !!}
                        {!! Form::select('category_id[]', $categories, request()->category_id, ['class' =>
                        'form-control sale_filter
                        selectpicker', 'data-live-search' =>'true', "data-actions-box"=>"true", 'placeholder' => __('lang.all'), 'multiple', 'id' => 'category_id']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('lang.sub_category') . ':', []) !!}
                        {!! Form::select('sub_category_id[]', $sub_categories, request()->sub_category_id, ['class' =>
                        'form-control sale_filter
                        selectpicker', 'data-live-search' =>'true', "data-actions-box"=>"true", 'placeholder' => __('lang.all'), 'multiple', 'id' => 'sub_category_id']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('lang.brand') . ':', []) !!}
                        {!! Form::select('brand_id[]', $brands, request()->brand_id, ['class' => 'form-control
                        sale_filter
                        selectpicker', 'data-live-search' =>'true', "data-actions-box"=>"true", 'placeholder' => __('lang.all'), 'multiple', 'id' => 'brand_id']) !!}
                    </div>
                </div>
                @endif

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                        {!! Form::select('customer_id', $customers, request()->customer_id, ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_type_id', __('lang.customer_type'), []) !!}
                        {!! Form::select('customer_type_id', $customer_types, request()->customer_type_id, ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('store_id', __('lang.store'), []) !!}
                        {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status', __('lang.status'), []) !!}
                        {!! Form::select('status', ['final' => 'Completed', 'pending' => 'Pending'],
                        request()->status, ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('method', __('lang.payment_type'), []) !!}
                        {!! Form::select('method', $payment_types, request()->method,
                        ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_status', __('lang.payment_status'), []) !!}
                        {!! Form::select('payment_status', $payment_status_array, request()->payment_status,
                        ['class' =>
                        'form-control sale_filter', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('start_date', __('lang.start_date'), []) !!}
                        {!! Form::text('start_date', request()->start_date, ['class' => 'form-control sale_filter']) !!}
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
                        {!! Form::text('end_date', request()->end_date, ['class' => 'form-control sale_filter']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('end_time', __('lang.end_time'), []) !!}
                        {!! Form::text('end_time', null, ['class' => 'form-control time_picker sale_filter']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('created_by', __('lang.cashier'), []) !!}
                        {!! Form::select('created_by', $cashiers, false, ['class' =>
                        'form-control sale_filter selectpicker', 'id' =>
                        'created_by', 'data-live-search' => 'true', 'placeholder' =>
                        __('lang.all')]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button"
                        class="btn btn-danger mt-2 ml-2 clear_filter">@lang('lang.clear_filter')</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive no-print">
    <table id="sales_table" class="table" style="min-height: 300px;">
        <thead>
            <tr>
                <th>@lang('lang.date_and_time')</th>
                <th>@lang('lang.reference')</th>
                <th>@lang('lang.store')</th>
                <th>@lang('lang.customer')</th>
                <th>@lang('lang.phone')</th>
                <th>@lang('lang.sale_status')</th>
                <th>@lang('lang.payment_status')</th>
                <th>@lang('lang.payment_type')</th>
                <th>@lang('lang.ref_number')</th>
                <th class="sum">@lang('lang.grand_total')</th>
                <th class="sum">@lang('lang.paid')</th>
                <th class="sum">@lang('lang.due')</th>
                <th>@lang('lang.cashier')</th>
                <th class="hidden">@lang('lang.products')</th>
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
                <th style="text-align: right">@lang('lang.totals')</th>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script>
    $(document).ready(function(){
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
        buttons: buttons,
        processing: true,
        serverSide: true,
        aaSorting: [[0, "desc"]],
        initComplete: function() {
            $(this.api().table().container()).find('input').parent().wrap('<form>').parent().attr('autocomplete', 'off');
        },
        ajax: {
            url: "/sale",
            data: function (d) {
                d.product_class_id = $("#product_class_id").val();
                d.category_id = $("#category_id").val();
                d.sub_category_id = $("#sub_category_id").val();
                d.brand_id = $("#brand_id").val();
                d.customer_id = $("#customer_id").val();
                d.customer_type_id = $("#customer_type_id").val();
                d.store_id = $("#store_id").val();
                d.status = $("#status").val();
                d.method = $("#method").val();
                d.payment_status = $("#payment_status").val();
                d.start_date = $("#start_date").val();
                d.start_time = $("#start_time").val();
                d.end_date = $("#end_date").val();
                d.end_time = $("#end_time").val();
                d.created_by = $("#created_by").val();
            },
        },
        columnDefs: [
            {
                targets: "date",
                type: "date-eu",
            },
            {
                targets: [14],
                orderable: false,
                searchable: false,
            },
            {
                targets: [13],
                visible: false,
                orderable: false,
                searchable: false,
            },
        ],
        columns: [
            { data: "transaction_date", name: "transaction_date" },
            { data: "invoice_no", name: "invoice_no" },
            { data: "store_name", name: "stores.name" },
            { data: "customer_name", name: "customers.name" },
            { data: "mobile_number", name: "customers.mobile_number" },
            { data: "status", name: "transactions.status" },
            { data: "payment_status", name: "transactions.payment_status" },
            { data: "method", name: "transaction_payments.method" },
            { data: "ref_number", name: "transaction_payments.ref_number" },
            { data: "final_total", name: "final_total" },
            { data: "paid", name: "transaction_payments.amount", searchable: false },
            { data: "due", name: "transaction_payments.amount", searchable: false },
            { data: "created_by", name: "users.name" },
            { data: "products", name: "products.name" },
            { data: "action", name: "action" },
        ],
        createdRow: function (row, data, dataIndex) {},
        footerCallback: function (row, data, start, end, display) {
            var intVal = function (i) {
                return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                    ? i
                    : 0;
            };

            this.api()
                .columns(".sum", { page: "current" })
                .every(function () {
                    var column = this;
                    if (column.data().count()) {
                        var sum = column.data().reduce(function (a, b) {
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
    $(document).on('change', '.sale_filter', function(){
        sales_table.ajax.reload();
    });
})
    $('.time_picker').focusout(function (event) {
        sales_table.ajax.reload();
    });

    $(document).on('click', '.clear_filter', function(){
        $('.sale_filter').val('');
        $('.sale_filter').selectpicker('refresh');
        sales_table.ajax.reload();
    });
    $(document).on('click', '.print-invoice', function(){
        $('.view_modal').modal('hide')
        $.ajax({
            method: 'get',
            url: $(this).data('href'),
            data: {  },
            success: function(result) {
                if(result.success){
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
