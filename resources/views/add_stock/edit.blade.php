@extends('layouts.app')
@section('title', __('lang.edit_stock'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_stock')</h4>
                    </div>
                    {!! Form::open(['url' => action('AddStockController@update', $add_stock->id), 'method' => 'put',
                    'id' =>
                    'add_stock_form', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                                    {!! Form::select('store_id', $stores,
                                    $add_stock->store_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'). ':*', []) !!}
                                    {!! Form::select('supplier_id', $suppliers,
                                    $add_stock->supplier_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_no', __('lang.po_no'), []) !!} <i class="dripicons-question"
                                        data-toggle="tooltip" title="@lang('lang.po_no_add_stock_info')"></i>
                                    {!! Form::select('po_no', $po_nos,
                                    $add_stock->purchase_order_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                                    {!! Form::select('status', ['received' => 'Received', 'partially_received' =>
                                    'Partially Received', 'pending' => 'Pending'],
                                    $add_stock->status, ['class' => 'selectpicker form-control',
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
                                    <button type="button" class="btn btn-success btn-lg btn-modal"
                                        data-href="{{action('ProductController@create')}}?quick_add=1"
                                        data-container=".view_modal"><i class="fa fa-plus"></i></button>
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
                                        @foreach ($add_stock->add_stock_lines as $product)
                                        <tr>
                                            <td>
                                                {{$product->product->name}}

                                                @if($product->variation->name != "Default")
                                                <b>{{$product->variation->name}}</b>
                                                @endif
                                                <input type="hidden"
                                                    name="add_stock_lines[{{$loop->index}}][add_stock_line_id]"
                                                    value="{{$product->id}}">
                                                <input type="hidden"
                                                    name="add_stock_lines[{{$loop->index}}][product_id]"
                                                    value="{{$product->product_id}}">
                                                <input type="hidden"
                                                    name="add_stock_lines[{{$loop->index}}][variation_id]"
                                                    value="{{$product->variation_id}}">
                                            </td>
                                            <td>
                                                {{$product->variation->sub_sku}}
                                            </td>
                                            <td>
                                                <input type="text" class="form-control quantity" min=1
                                                    name="add_stock_lines[{{$loop->index}}][quantity]" required
                                                    value="@if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control purchase_price"
                                                    name="add_stock_lines[{{$loop->index}}][purchase_price]" required
                                                    value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
                                            </td>
                                            <td>
                                                <span class="sub_total_span">{{@num_format($product->sub_total)}}</span>
                                                <input type="hidden" class="form-control sub_total"
                                                    name="add_stock_lines[{{$loop->index}}][sub_total]"
                                                    value="{{$product->sub_total}}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sx remove_row"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-3 offset-md-8 text-right">
                                <h3> @lang('lang.total'): <span
                                        class="final_total_span">{{@num_format($add_stock->final_total)}}</span> </h3>
                                <input type="hidden" name="final_total" id="final_total"
                                    value="{{$add_stock->final_total}}">
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
                                    {!! Form::text('invoice_no', $add_stock->invoice_no, ['class' => 'form-control',
                                    'placeholder' =>
                                    __('lang.invoice_no')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('lang.date'). ':*', []) !!} <br>
                                    {!! Form::text('transaction_date', @format_date($add_stock->transaction_date),
                                    ['class' => 'form-control datepicker', 'required',
                                    'placeholder' => __('lang.date')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_status', __('lang.payment_status'). ':*', [])
                                    !!}
                                    {!! Form::select('payment_status', $payment_status_array,
                                    $add_stock->payment_status, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3 payment_fields hide">
                                <div class="form-group">
                                    {!! Form::label('amount', __('lang.amount'). ':*', []) !!} <br>
                                    {!! Form::text('amount', @num_format($add_stock->amount), ['class' =>
                                    'form-control', 'placeholder'
                                    => __('lang.amount')]) !!}
                                </div>
                            </div>
                            @php
                            $transaction_payment = $add_stock->transaction_payments->first();
                            @endphp
                            <input type="hidden" name="transaction_payment_id" value="{{$transaction_payment->id}}">
                            <div class="col-md-3 payment_fields hide">
                                <div class="form-group">
                                    {!! Form::label('method', __('lang.payment_type'). ':*', []) !!}
                                    {{$transaction_payment->method}}
                                    {!! Form::select('method', $payment_type_array,
                                    $transaction_payment->method, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3 payment_fields hide">
                                <div class="form-group">
                                    {!! Form::label('paid_on', __('lang.payment_date'). ':', []) !!} <br>
                                    {!! Form::text('paid_on', @format_date($transaction_payment->paid_on), ['class' =>
                                    'form-control datepicker',
                                    'placeholder' => __('lang.payment_date')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3 payment_fields hide">
                                <div class="form-group">
                                    {!! Form::label('upload_documents', __('lang.upload_documents'). ':', []) !!} <br>
                                    {!! Form::file('upload_documents[]', null, ['class' => '']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 not_cash_fields hide">
                                <div class="form-group">
                                    {!! Form::label('ref_number', __('lang.ref_number'). ':', []) !!} <br>
                                    {!! Form::text('ref_number', $transaction_payment->ref_number, ['class' =>
                                    'form-control not_cash',
                                    'placeholder' => __('lang.ref_number')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3 not_cash_fields hide">
                                <div class="form-group">
                                    {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date'). ':', []) !!} <br>
                                    {!! Form::text('bank_deposit_date',
                                    @format_date($transaction_payment->bank_deposit_date), ['class' => 'form-control
                                    not_cash datepicker',
                                    'placeholder' => __('lang.bank_deposit_date')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3 not_cash_fields hide">
                                <div class="form-group">
                                    {!! Form::label('bank_name', __('lang.bank_name'). ':', []) !!} <br>
                                    {!! Form::text('bank_name', $transaction_payment->bank_name, ['class' =>
                                    'form-control not_cash',
                                    'placeholder' => __('lang.bank_name')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3 due_fields hide">
                                <div class="form-group">
                                    {!! Form::label('due_date', __('lang.due_date'). ':', []) !!} <br>
                                    {!! Form::text('due_date', !empty($add_stock->due_date) ?
                                    @format_date($add_stock->due_date) : null, ['class' => 'form-control datepicker',
                                    'placeholder' => __('lang.due_date')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3 due_fields hide">
                                <div class="form-group">
                                    {!! Form::label('notify_before_days', __('lang.notify_before_days'). ':', []) !!}
                                    <br>
                                    {!! Form::text('notify_before_days', $add_stock->notify_before_days, ['class' =>
                                    'form-control',
                                    'placeholder' => __('lang.notify_before_days')]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('notes', __('lang.notes'). ':', []) !!} <br>
                                    {!! Form::textarea('notes', $add_stock->notes, ['class' => 'form-control', 'rows' =>
                                    3]) !!}
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="save"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.save' )</button>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script src="{{asset('js/add_stock.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#payment_status').change();
    })
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

    //payment related script

    $('#payment_status').change(function(){
        var payment_status = $(this).val();

        if(payment_status === 'paid' || payment_status === 'partial'){
            $('.not_cash_fields').addClass('hide');
            $('#method').change();
            $('#method').attr('required', true);
            $('#paid_on').attr('required', true);
            $('.payment_fields').removeClass('hide');
        }else{
            $('.payment_fields').addClass('hide');
        }
        if(payment_status === 'pending' || payment_status === 'partial'){
            $('.due_fields').removeClass('hide');
        }else{
            $('.due_fields').addClass('hide');
        }
        if(payment_status === 'pending'){
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
            $('#method').attr('required', false);
            $('#paid_on').attr('required', false);
        }else{
            $('#method').attr('required', true);
        }
        if(payment_status === 'paid'){
            $('.due_fields').addClass('hide');
        }

        $
    })
    $('#method').change(function(){
        var method = $(this).val();

        if(method === 'cash'){
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
        }else{
            $('.not_cash_fields').removeClass('hide');
            $('.not_cash').attr('required', true);
        }
    })
</script>
@endsection
