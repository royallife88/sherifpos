@extends('layouts.app')
@section('title', __('lang.add_stock'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_stock')</h4>
                    </div>
                    {!! Form::open(['url' => action('PurchaseOrderController@store'), 'method' => 'post', 'id' =>
                    'add_stock_form']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                                    {!! Form::select('store_id', $stores,
                                    null, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                                    'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'). ':*', []) !!}
                                    {!! Form::select('supplier_id', $suppliers,
                                    null, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_no', __('lang.po_no'), []) !!} <i
                                    class="dripicons-question" data-toggle="tooltip"
                                    title="@lang('lang.po_no_add_stock_info')"></i>
                                    {!! Form::select('po_no', $po_nos,
                                    null, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                                    {!! Form::select('status', $status_array,
                                    'received', ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>


                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="search-box input-group">
                                    <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                            class="fa fa-search"></i></button>
                                    <input type="text" name="search_product" id="search_product"
                                        placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                        class="form-control ui-autocomplete-input" autocomplete="off">
                                        <button type="button" class="btn btn-success btn-lg btn-modal" data-href="{{action('ProductController@create')}}?quick_add=1" data-container=".view_modal"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-3 offset-md-8 text-right">
                                <h3> @lang('lang.total'): <span class="final_total_span"></span> </h3>
                                <input type="hidden" name="final_total" id="final_total" value="0">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('files', __('lang.files'), []) !!} <br>
                                    {!! Form::file('files[]', null, ['class' => '']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('invoice_no', __('lang.invoice_no'), []) !!} <br>
                                    {!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' => __('lang.invoice_no')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('lang.date'), []) !!} <br>
                                    {!! Form::text('transaction_date', null, ['class' => 'form-control datepicker', 'readonly', 'placeholder' => __('lang.date')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_statuspayment_status', __('lang.payment_statuspayment_status'). ':*', []) !!}
                                    {!! Form::select('payment_statuspayment_status', $payment_status_array,
                                    'received', ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="print"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.print' )</button>
                        @can('add_stock.send_to_supplier.create')
                        <button type="button" id="send_to_supplier" style="margin: 10px" disabled
                            class="btn btn-warning pull-right btn-flat submit" data-toggle="modal"
                            data-target="#supplier_modal">@lang(
                            'lang.send_to_supplier' )</button>
                        @endcan
                        @can('add_stock.send_to_admin.create')
                        <button type="submit" name="submit" id="send_to_admin" style="margin: 10px"
                            value="sent_admin" class="btn btn-primary pull-right btn-flat submit">@lang(
                            'lang.send_to_admin' )</button>
                        @endcan
                        <div class="modal fade supplier_modal" id="supplier_modal" role="dialog" aria-hidden="true">
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script src="{{asset('js/purchase.js')}}"></script>
<script type="text/javascript">

    $('#po_no').change(function () {
        let po_no = $(this).val();

        if(po_no){
            $.ajax({
                method: 'get',
                url: '/add-stock/get-purchase-order-details/'+po_no,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    $("table#product_table tbody").empty().append(result);
                    calculate_sub_totals()
                },
            });
        }
    });
    $(document).on("click", '#submit-btn', function (e) {
        e.preventDefault();
        console.log('click');
        var sku = $('#sku').val();
        if ($("#product-form-quick-add").valid()) {
            tinyMCE.triggerSave();
            $.ajax({
                type: "POST",
                url: "/product",
                data: $("#product-form-quick-add").serialize(),
                success: function (response) {
                    if (response.success) {
                        swal("Success", response.msg, "success");;
                        $("#search_product").val(sku);
                        $('input#search_product').autocomplete("search");
                        $('.view_modal').modal('hide');
                    }
                },
                error: function (response) {
                    if (!response.success) {
                        swal("Error", response.msg, "error");
                    }
                },
            });
        }
    });
    $(document).on("change", "#category_id", function () {
        $.ajax({
            method: "get",
            url:
                "/category/get-sub-category-dropdown?category_id=" +
                $("#category_id").val(),
            data: {},
            contentType: "html",
            success: function (result) {
                $("#sub_category_id").empty().append(result).change();
                $("#sub_category_id").selectpicker("refresh");

                if (sub_category_id) {
                    $("#sub_category_id").selectpicker("val", sub_category_id);
                }
            },
        });
    });
</script>
@endsection
