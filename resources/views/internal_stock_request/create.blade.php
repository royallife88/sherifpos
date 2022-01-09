@extends('layouts.app')
@section('title', __('lang.internal_stock_request'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.internal_stock_request')</h4>
                    </div>
                    {!! Form::open(['url' => action('InternalStockRequestController@store'), 'method' => 'post', 'id' =>
                    'internal_stock_request_form', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('receiver_store_id', __('lang.receiver_store'). ':*', []) !!}
                                    {!! Form::select('receiver_store_id', $stores,
                                    session('user.store_id'), ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                                    {!! Form::select('status', ['received' => __('lang.received'), 'approved' =>
                                    __('lang.approved'), 'pending' => __('lang.pending'), 'declined' =>
                                    __('lang.declined')],
                                    'pending', ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class=" mt-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('product_class_id', __('lang.product_class') .
                                                ':', []) !!}
                                                {!! Form::select('product_class_id', $product_classes,
                                                request()->product_class_id, ['class'
                                                => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('category_id', __('lang.category') . ':', [])
                                                !!}
                                                {!! Form::select('category_id', $categories,
                                                request()->category_id, ['class' =>
                                                'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('sub_category_id', __('lang.sub_category') .
                                                ':', []) !!}
                                                {!! Form::select('sub_category_id', $sub_categories,
                                                request()->sub_category_id, ['class' =>
                                                'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('brand_id', __('lang.brand') . ':', []) !!}
                                                {!! Form::select('brand_id', $brands, request()->brand_id,
                                                ['class' => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('unit_id', __('lang.unit') . ':', []) !!}
                                                {!! Form::select('unit_id', $units, request()->unit_id, ['class'
                                                => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('color_id', __('lang.color') . ':', []) !!}
                                                {!! Form::select('color_id', $colors, request()->color_id,
                                                ['class' => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('size_id', __('lang.size') . ':', []) !!}
                                                {!! Form::select('size_id', $sizes, request()->size_id, ['class'
                                                => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('grade_id', __('lang.grade') . ':', []) !!}
                                                {!! Form::select('grade_id', $grades, request()->grade_id,
                                                ['class' => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('tax_id', __('lang.tax') . ':', []) !!}
                                                {!! Form::select('tax_id', $taxes, request()->tax_id, ['class'
                                                => 'form-control filter
                                                selectpicker', 'data-live-search' =>'true', 'placeholder' =>
                                                __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('store_id', __('lang.store'), []) !!}
                                                {!! Form::select('store_id', $stores, request()->store_id,
                                                ['class' =>
                                                'form-control filter', 'placeholder' =>
                                                __('lang.all'),'data-live-search'=>"true"]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <a class="btn btn-danger mt-4"
                                                href="{{action('InternalStockRequestController@create')}}">@lang('lang.clear_filters')</a>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" value="1"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.image')</button>
                                        <button type="button" value="7"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.class')</button>
                                        <button type="button" value="8"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.category')</button>
                                        <button type="button" value="9"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.sub_category')</button>
                                        <button type="button" value="10"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_history')</button>
                                        <button type="button" value="11"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.batch_number')</button>
                                        <button type="button" value="12"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.selling_price')</button>
                                        <button type="button" value="13"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.tax')</button>
                                        <button type="button" value="14"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.brand')</button>
                                        <button type="button" value="15"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.unit')</button>
                                        <button type="button" value="16"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.color')</button>
                                        <button type="button" value="17"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.size')</button>
                                        <button type="button" value="18"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.grade')</button>
                                        <button type="button" value="19"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.customer_type')</button>
                                        <button type="button" value="20"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.expiry_date')</button>
                                        <button type="button" value="21"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.manufacturing_date')</button>
                                        <button type="button" value="22"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.discount')</button>
                                        @can('product_module.purchase_price.view')
                                        <button type="button" value="23"
                                            class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
                                        @endcan
                                    </div>
                                </div>

                                <div id="product_table_div" class="table-responsive">
                                    <table id="product_table" class="table dataTable" style="width: auto">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>@lang('lang.image')</th>
                                                <th>@lang('lang.name')</th>
                                                <th>@lang('lang.product_code')</th>
                                                <th>@lang('lang.store')</th>
                                                <th class="sum">@lang('lang.current_stock')</th>
                                                <th style="width: 100px;">@lang('lang.qty')</th>
                                                <th>@lang('lang.class')</th>
                                                <th>@lang('lang.category')</th>
                                                <th>@lang('lang.sub_category')</th>
                                                <th>@lang('lang.purchase_history')</th>
                                                <th>@lang('lang.batch_number')</th>
                                                <th>@lang('lang.selling_price')</th>
                                                <th>@lang('lang.tax')</th>
                                                <th>@lang('lang.brand')</th>
                                                <th>@lang('lang.unit')</th>
                                                <th>@lang('lang.color')</th>
                                                <th>@lang('lang.size')</th>
                                                <th>@lang('lang.grade')</th>
                                                <th>@lang('lang.customer_type')</th>
                                                <th>@lang('lang.expiry_date')</th>
                                                <th>@lang('lang.manufacturing_date')</th>
                                                <th>@lang('lang.discount')</th>
                                                @can('product_module.purchase_price.view')
                                                <th>@lang('lang.purchase_price')</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('internal_stock_request.partials.product_table', ['products'
                                            =>
                                            $products])
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="product_data" name="product_data" value="[]">
                        <input type="hidden" id="store_array" name="store_array" value="[]">
                        <div class="row">
                            <div class="col-md-12 p-5" style="text-align: right; font-size: 22px; font-weight: bold;">
                                @lang('lang.total'): <span class="final_total_span">{{@num_format(0)}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('notes', __('lang.notes'). ':', []) !!} <br>
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="save" style="margin: 10px" value="save"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.send' )</button>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script type="text/javascript">
    var hidden_column_array = $.cookie('column_visibility') ? JSON.parse($.cookie('column_visibility')) : [];
    function toggleColumnInCookie(){
        $.each( hidden_column_array, function( index, value ) {
            $('.column-toggle').each(function(){
                if($(this).val() == value){
                    toggleColumnVisibility(value, $(this));
                }
            });

        });
    }
    $(document).ready(function(){
        toggleColumnInCookie()

    });
    $(document).on('click', '.column-toggle', function(){
        let column_index = parseInt($(this).val());
        toggleColumnVisibility(column_index, $(this));
        if(hidden_column_array.includes(column_index)){
            hidden_column_array.splice(hidden_column_array.indexOf(column_index), 1);
        }else{
            hidden_column_array.push(column_index);
        }

        //unique array javascript
        hidden_column_array = $.grep(hidden_column_array, function(v, i) {
            return $.inArray(v, hidden_column_array) === i;
        });

        $.cookie('column_visibility', JSON.stringify(hidden_column_array));
    })

    function toggleColumnVisibility(column_index, this_btn){
        column = table.column(column_index);
        column.visible(! column.visible());

        if(column.visible()){
            $(this_btn).addClass('badge-primary')
            $(this_btn).removeClass('badge-warning')
        }else{
            $(this_btn).removeClass('badge-primary')
            $(this_btn).addClass('badge-warning')

        }
    }
    function getProductTableAjax(){
        return $.ajax({
            method: 'get',
            url: '/internal-stock-request/get-product-table',
            data: {
                'product_class_id': $('#product_class_id').val(),
                'category_id': $('#category_id').val(),
                'sub_category_id': $('#sub_category_id').val(),
                'brand_id': $('#brand_id').val(),
                'unit_id': $('#unit_id').val(),
                'color_id': $('#color_id').val(),
                'size_id': $('#size_id').val(),
                'grade_id': $('#grade_id').val(),
                'tax_id': $('#tax_id').val(),
                'store_id': $('#store_id').val(),
            },
            dataType: 'html',
            success: function(result) {

            },
        });
    }
    async function getProducts() {
        const res = await getProductTableAjax().then(function(result) {
            if ( $.fn.DataTable.isDataTable('#product_table') ) {
                $("#product_table").DataTable().destroy();
            }
            $('table#product_table tbody').html(result);
            table = $("#product_table").DataTable(datatable_params);
            toggleColumnInCookie()
        })

    }
    $(document).on('change', '.filter', function(){
        getProducts()
    })
    var data_array = [];
    var store_array = [];
    $(document).on('change', '.qty', function(){
       let tr = $(this).closest('tr');
       let qty = parseFloat($(tr).find('.qty').val());
       let current_stock = parseFloat($(tr).find('.current_stock').val());
       $(tr).find('.stock_error').addClass('hide');
       $(tr).find('.product_checkbox').prop('checked', false);
       if(qty < 0){
           $(tr).find('.qty').val(0);

           return;
       }
       if(qty > current_stock){
           $(tr).find('.stock_error').removeClass('hide');
           return;

       }
       if(qty){
           $(tr).find('.product_checkbox').prop('checked', true);
           let row_index = $(tr).find('.row_index').val();
           let product_id = $(tr).find('.product_id').val();
           let variation_id = $(tr).find('.variation_id').val();
           let store_id = $(tr).find('.store_id').val();
           let qty = $(tr).find('.qty').val();
           let purchase_price = $(tr).find('.purchase_price').val();
           store_array.push(store_id);

            store_array = $.grep(store_array, function(v, i) {
            return $.inArray(v, store_array) === i;
            });
            $('#store_array').val(JSON.stringify(store_array));
           data_array[row_index] = {
                'product_id': product_id,
                'variation_id': variation_id,
                'store_id': store_id,
                'qty': qty,
                'purchase_price': purchase_price,
           }
           $('#product_data').val(JSON.stringify(data_array));
        }else{
           $(tr).find('.product_checkbox').prop('checked', false);
        }
        calculateTotal()
    })

    function calculateTotal(){
        let final_total = 0;
        data_array.forEach(function(item, index){
            if(item){
                final_total += parseFloat(item.qty) * parseFloat(item.purchase_price);
            }
        });
        $(".final_total_span").text(
            __currency_trans_from_en(final_total, false)
        );
    }

</script>
@endsection
