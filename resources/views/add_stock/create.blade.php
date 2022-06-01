@extends('layouts.app')
@if (!empty($is_raw_material))
    @section('title', __('lang.add_stock_for_raw_material'))
@else
    @section('title', __('lang.add_stock'))
@endif

@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            @if (!empty($is_raw_material))
                                <h4>@lang('lang.add_stock_for_raw_material')</h4>
                            @else
                                <h4>@lang('lang.add_stock')</h4>
                            @endif
                        </div>
                        {!! Form::open(['url' => action('AddStockController@store'), 'method' => 'post', 'id' => 'add_stock_form', 'enctype' => 'multipart/form-data']) !!}
                        <input type="hidden" name="row_count" id="row_count" value="0">
                        <input type="hidden" name="is_raw_material" id="is_raw_material" value="{{ $is_raw_material }}">
                        <input type="hidden" name="is_add_stock" id="is_add_stock" value="1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('store_id', __('lang.store') . ':*', []) !!}
                                        {!! Form::select('store_id', $stores, session('user.store_id'), ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('supplier_id', __('lang.supplier') . ':*', []) !!}
                                        {!! Form::select('supplier_id', $suppliers, array_key_first($suppliers), ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('po_no', __('lang.po_no'), []) !!} <i class="dripicons-question" data-toggle="tooltip"
                                            title="@lang('lang.po_no_add_stock_info')"></i>
                                        {!! Form::select('po_no', $po_nos, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('status', __('lang.status') . ':*', []) !!}
                                        {!! Form::select('status', ['received' => 'Received', 'partially_received' => 'Partially Received', 'pending' => 'Pending'], 'received', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::label('transaction_date', __('lang.date_and_time'), []) !!}
                                    <input type="datetime-local" id="transaction_date" name="transaction_date"
                                        value="{{ date('Y-m-d\TH:i') }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                        <input type="hidden" name="default_currency_id" id="default_currency_id"
                                            value="{{ !empty(App\Models\System::getProperty('currency')) ? App\Models\System::getProperty('currency') : '' }}">
                                        {!! Form::label('paying_currency_id', __('lang.paying_currency') . ':', []) !!}
                                        {!! Form::select('paying_currency_id', $exchange_rate_currencies, !empty(App\Models\System::getProperty('currency')) ? App\Models\System::getProperty('currency') : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required']) !!}
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-8 offset-md-1">
                                    <div class="search-box input-group">
                                        <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                                class="fa fa-search"></i></button>
                                        <input type="text" name="search_product" id="search_product"
                                            placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                            class="form-control ui-autocomplete-input" autocomplete="off">
                                        <button type="button" class="btn btn-success btn-lg btn-modal"
                                            data-href="{{ action('ProductController@create') }}?quick_add=1"
                                            data-container=".view_modal"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    @include(
                                        'quotation.partial.product_selection'
                                    )
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-10 offset-md-1">
                                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 7%" class="col-sm-8">@lang( 'lang.image' )</th>
                                                <th style="width: 20%" class="col-sm-8">@lang( 'lang.products' )</th>
                                                <th style="width: 20%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                                <th style="width: 20%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                                <th style="width: 20%" class="col-sm-4">@lang( 'lang.unit' )</th>
                                                <th style="width: 20%" class="col-sm-4">@lang( 'lang.purchase_price' )
                                                </th>
                                                <th style="width: 10%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                                                <th style="width: 10%" class="col-sm-4">@lang( 'lang.new_stock' )</th>
                                                <th style="width: 10%" class="col-sm-4">@lang( 'lang.action' )</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <h4>@lang('lang.items_count'): <span class="items_count_span"
                                        style="margin-right: 15px;">0</span> @lang('lang.items_quantity'): <span
                                        class="items_quantity_span" style="margin-right: 15px;">0</span></h4>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="col-md-3 offset-md-8 text-right">
                                    <h3> @lang('lang.total'): <span class="final_total_span"></span> </h3>
                                    <input type="hidden" name="grand_total" id="grand_total" value="0">
                                    <input type="hidden" name="final_total" id="final_total" value="0">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('files', __('lang.files'), []) !!} <br>
                                        <input type="file" name="files[]" id="files" multiple>
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
                                        {!! Form::label('other_expenses', __('lang.other_expenses'), []) !!} <br>
                                        {!! Form::text('other_expenses', null, ['class' => 'form-control', 'placeholder' => __('lang.other_expenses'), 'id' => 'other_expenses']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('discount_amount', __('lang.discount'), []) !!} <br>
                                        {!! Form::text('discount_amount', null, ['class' => 'form-control', 'placeholder' => __('lang.discount'), 'id' => 'discount_amount']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('other_payments', __('lang.other_payments'), []) !!} <br>
                                        {!! Form::text('other_payments', null, ['class' => 'form-control', 'placeholder' => __('lang.other_payments'), 'id' => 'other_payments']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                                        {!! Form::select('source_type', ['user' => __('lang.user'), 'pos' => __('lang.pos'), 'store' => __('lang.store'), 'safe' => __('lang.safe')], 'user', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                                        {!! Form::select('source_id', $users, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select'), 'id' => 'source_id', 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('payment_status', __('lang.payment_status') . ':*', []) !!}
                                        {!! Form::select('payment_status', $payment_status_array, 'paid', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>

                                @include('add_stock.partials.payment_form')

                                <div class="col-md-3 due_amount_div hide">
                                    <label for="due_amount" style="margin-top: 25px;">@lang('lang.due'): <span
                                            class="due_amount_span">{{ @num_format(0) }}</span></label>
                                </div>

                                <div class="col-md-3 due_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('due_date', __('lang.due_date') . ':', []) !!} <br>
                                        {!! Form::text('due_date', !empty($payment) ? $payment->due_date : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.due_date')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 due_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('notify_before_days', __('lang.notify_before_days') . ':', []) !!}
                                        <br>
                                        {!! Form::text('notify_before_days', !empty($payment) ? $payment->notify_before_days : null, ['class' => 'form-control', 'placeholder' => __('lang.notify_before_days')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('notes', __('lang.notes') . ':', []) !!} <br>
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
    <script src="{{ asset('js/add_stock.js') }}"></script>
    <script src="{{ asset('js/product_selection.js') }}"></script>
    <script type="text/javascript">
        $(document).on('click', '#add-selected-btn', function() {
            $('#select_products_modal').modal('hide');
            $.each(product_selected, function(index, value) {
                get_label_product_row(value.product_id, value.variation_id);
            });
            product_selected = [];
            product_table.ajax.reload();
        })
        @if (!empty($product_id) && !empty($variation_id))
            $(document).ready(function(){
            get_label_product_row({{ $product_id }},{{ $variation_id }});
            })
        @endif
        $('#po_no').change(function() {
            let po_no = $(this).val();

            if (po_no) {
                $.ajax({
                    method: 'get',
                    url: '/add-stock/get-purchase-order-details/' + po_no,
                    data: {},
                    contentType: 'html',
                    success: function(result) {
                        $("table#product_table tbody").empty().append(result);
                        calculate_sub_totals()
                    },
                });
            }
        });
        $(document).on("click", '#submit-btn-add-product', function(e) {
            e.preventDefault();
            var sku = $('#sku').val();
            if ($("#product-form-quick-add").valid()) {
                tinyMCE.triggerSave();
                $.ajax({
                    type: "POST",
                    url: "/product",
                    data: $("#product-form-quick-add").serialize(),
                    success: function(response) {
                        if (response.success) {
                            swal("Success", response.msg, "success");;
                            $("#search_product").val(sku);
                            $('input#search_product').autocomplete("search");
                            $('.view_modal').modal('hide');
                        }
                    },
                    error: function(response) {
                        if (!response.success) {
                            swal("Error", response.msg, "error");
                        }
                    },
                });
            }
        });
        $(document).on("change", "#category_id", function() {
            $.ajax({
                method: "get",
                url: "/category/get-sub-category-dropdown?category_id=" +
                    $("#category_id").val(),
                data: {},
                contentType: "html",
                success: function(result) {
                    $("#sub_category_id").empty().append(result).change();
                    $("#sub_category_id").selectpicker("refresh");

                    if (sub_category_id) {
                        $("#sub_category_id").selectpicker("val", sub_category_id);
                    }
                },
            });
        });

        //payment related script

        $('#payment_status').change(function() {
            var payment_status = $(this).val();

            if (payment_status === 'paid' || payment_status === 'partial') {
                $('.not_cash_fields').addClass('hide');
                $('#method').change();
                $('#method').attr('required', true);
                $('#paid_on').attr('required', true);
                $('.payment_fields').removeClass('hide');
            } else {
                $('.payment_fields').addClass('hide');
            }
            if (payment_status === 'pending' || payment_status === 'partial') {
                $('.due_fields').removeClass('hide');
            } else {
                $('.due_fields').addClass('hide');
            }
            if (payment_status === 'pending') {
                $('.not_cash_fields').addClass('hide');
                $('.not_cash').attr('required', false);
                $('#method').attr('required', false);
                $('#paid_on').attr('required', false);
            } else {
                $('#method').attr('required', true);
            }
            if (payment_status === 'paid') {
                $('.due_fields').addClass('hide');
            }
        })
        $('#method').change(function() {
            var method = $(this).val();

            if (method === 'cash') {
                $('.not_cash_fields').addClass('hide');
                $('.not_cash').attr('required', false);
            } else {
                $('.not_cash_fields').removeClass('hide');
                $('.not_cash').attr('required', true);
            }
        });

        $(document).ready(function() {
            $('#payment_status').change();
            $('#source_type').change();
        })
        $('#source_type').change(function() {
            if ($(this).val() !== '') {
                $.ajax({
                    method: 'get',
                    url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                    data: {},
                    success: function(result) {
                        $("#source_id").empty().append(result);
                        $("#source_id").selectpicker("refresh");
                    },
                });
            }
        });

        $(document).on('change', '.expiry_date', function() {
            if ($(this).val() != '') {
                let tr = $(this).parents('tr');
                tr.find('.days_before_the_expiry_date').val(15);
            }
        })
    </script>
@endsection
