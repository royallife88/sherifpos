@extends('layouts.app')
@section('title', __('lang.import_add_stock'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.import_add_stock')</h4>
                    </div>
                    {!! Form::open(['url' => action('AddStockController@saveImport'), 'method' => 'post', 'id' =>
                    'import_add_stock_form', 'enctype' => 'multipart/form-data' ]) !!}
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
                                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                                    {!! Form::select('status', ['received' => 'Received', 'partially_received' =>
                                    'Partially Received', 'pending' => 'Pending'],
                                    'received', ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('file', __('lang.file'), []) !!} <br>
                                        {!! Form::file('file', []) !!}
                                        <p>@lang('lang.download_info_add_stock')</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <a class="btn btn-block btn-primary"
                                        href="{{asset('sample_files/add_stock_import.csv')}}"><i
                                            class="fa fa-download"></i>@lang('lang.download_sample_file')</a>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="final_total" id="final_total" value="0">

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
                                    {!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' =>
                                    __('lang.invoice_no')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('lang.date'). ':*', []) !!} <br>
                                    {!! Form::text('transaction_date', @format_date(date('Y-m-d')), ['class' => 'form-control datepicker',
                                    'required',
                                    'readonly', 'placeholder' => __('lang.date')]) !!}
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('notes', __('lang.notes'). ':', []) !!} <br>
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
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
    $(document).on("click", '#submit-btn-add-product', function (e) {
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
        }
        if(payment_status === 'paid'){
            $('.due_fields').addClass('hide');
        }
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
