@extends('layouts.app')
@section('title', __('lang.edit_stock'))

@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.edit_stock')</h4>
                        </div>
                        {!! Form::open(['url' => action('AddStockController@update', $add_stock->id), 'method' => 'put', 'id' => 'edit_stock_form', 'enctype' => 'multipart/form-data']) !!}
                        <input type="hidden" name="row_count" id="row_count"
                            value="{{ $add_stock->add_stock_lines->count() }}">
                        <input type="hidden" name="is_add_stock" id="is_add_stock" value="1">
                        <input type="hidden" name="is_raw_material" id="is_raw_material"
                            value="{{ $add_stock->is_raw_material }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('store_id', __('lang.store') . ':*', []) !!}
                                        {!! Form::select('store_id', $stores, $add_stock->store_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('supplier_id', __('lang.supplier') . ':*', []) !!}
                                        {!! Form::select('supplier_id', $suppliers, $add_stock->supplier_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('po_no', __('lang.po_no'), []) !!} <i class="dripicons-question" data-toggle="tooltip"
                                            title="@lang('lang.po_no_add_stock_info')"></i>
                                        {!! Form::select('po_no', $po_nos, $add_stock->purchase_order_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('status', __('lang.status') . ':*', []) !!}
                                        {!! Form::select('status', ['received' => 'Received', 'partially_received' => 'Partially Received', 'pending' => 'Pending'], $add_stock->status, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                        <input type="hidden" name="default_currency_id" id="default_currency_id"
                                            value="{{ !empty($add_stock->default_currency_id) ? $add_stock->default_currency_id : App\Models\System::getProperty('currency') }}">
                                        {!! Form::label('paying_currency_id', __('lang.paying_currency') . ':', []) !!}
                                        {!! Form::select('paying_currency_id', $exchange_rate_currencies, !empty($add_stock->paying_currency_id) ? $add_stock->paying_currency_id : App\Models\System::getProperty('currency'), ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required']) !!}
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
                                                <th style="width: 7%" class="col-sm-8">@lang( 'lang.image' )</th>
                                                <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.unit' )</th>
                                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )
                                                </th>
                                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.new_stock' )</th>
                                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.action' )</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($add_stock->add_stock_lines as $product)
                                                <tr>
                                                    <td><img src="@if (!empty($product->product->getFirstMediaUrl('product'))) {{ $product->product->getFirstMediaUrl('product') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                                            alt="photo" width="50" height="50"></td>
                                                    <td>
                                                        {{ $product->product->name }}

                                                        @if ($product->variation->name != 'Default')
                                                            <b>{{ $product->variation->name }}</b>
                                                        @endif
                                                        <input type="hidden"
                                                            name="add_stock_lines[{{ $loop->index }}][add_stock_line_id]"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden"
                                                            name="add_stock_lines[{{ $loop->index }}][product_id]"
                                                            value="{{ $product->product_id }}">
                                                        <input type="hidden"
                                                            name="add_stock_lines[{{ $loop->index }}][variation_id]"
                                                            value="{{ $product->variation_id }}">
                                                    </td>
                                                    <td>
                                                        {{ $product->variation->sub_sku }}
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control quantity" min=1
                                                            name="add_stock_lines[{{ $loop->index }}][quantity]" required
                                                            value="@if (isset($product->quantity)) {{ @num_format($product->quantity) }}@else{{ 1 }} @endif">
                                                    </td>
                                                    <td> {{ $product->product->units->pluck('name')[0] ?? '' }}</td>
                                                    <td>
                                                        <input type="text" class="form-control purchase_price"
                                                            name="add_stock_lines[{{ $loop->index }}][purchase_price]"
                                                            required
                                                            value="@if (isset($product->purchase_price)) {{ @num_format($product->purchase_price) }}@else{{ 0 }} @endif">
                                                        <input class="final_cost" type="hidden"
                                                            name="add_stock_lines[{{ $loop->index }}][final_cost]"
                                                            value="@if (isset($product->final_cost)) {{ @num_format($product->final_cost) }}@else{{ 0 }} @endif">
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="sub_total_span">{{ @num_format($product->sub_total) }}</span>
                                                        <input type="hidden" class="form-control sub_total"
                                                            name="add_stock_lines[{{ $loop->index }}][sub_total]"
                                                            value="{{ $product->sub_total }}">
                                                    </td>
                                                    @php
                                                        $current_stock = App\Models\ProductStore::where('product_id', $product->product_id)
                                                            ->where('store_id', $add_stock->store_id)
                                                            ->sum('qty_available');
                                                    @endphp
                                                    <td>
                                                        <input type="hidden" name="current_stock" class="current_stock"
                                                            value="@if (isset($current_stock)) {{ $current_stock }}@else{{ 0 }} @endif">
                                                        <span class="current_stock_text">
                                                            @if (isset($current_stock))
                                                                {{ @num_format($current_stock) }}@else{{ 0 }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sx remove_row"
                                                            data-index="{{ $loop->index }}"><i
                                                                class="fa fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                <tr class="row_details_{{ $loop->index }}">
                                                    <td> {!! Form::text('add_stock_lines[' . $loop->index . '][batch_number]', $product->batch_number, ['class' => 'form-control', 'placeholder' => __('lang.batch_number')]) !!}</td>
                                                    <td>
                                                        {!! Form::text('add_stock_lines[' . $loop->index . '][manufacturing_date]', !empty($product->manufacturing_date) ? @format_date($product->manufacturing_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.manufacturing_date'), 'readonly']) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::text('add_stock_lines[' . $loop->index . '][expiry_date]', !empty($product->expiry_date) ? @format_date($product->expiry_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.expiry_date'), 'readonly']) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::text('add_stock_lines[' . $loop->index . '][expiry_warning]', $product->expiry_warning, ['class' => 'form-control', 'placeholder' => __('lang.days_before_the_expiry_date')]) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::text('add_stock_lines[' . $loop->index . '][convert_status_expire]', $product->convert_status_expire, ['class' => 'form-control', 'placeholder' => __('lang.convert_status_expire')]) !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <h4>@lang('lang.items_count'): <span class="items_count_span"
                                        style="margin-right: 15px;">{{ $add_stock->add_stock_lines->count() }}</span>
                                    @lang('lang.items_quantity'): <span class="items_quantity_span"
                                        style="margin-right: 15px;">{{ $add_stock->add_stock_lines->sum('quantity') }}</span>
                                </h4>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="col-md-3 offset-md-8 text-right">
                                    <h3> @lang('lang.total'): <span
                                            class="final_total_span">{{ @num_format($add_stock->final_total) }}</span>
                                    </h3>
                                    <input type="hidden" name="final_total" id="final_total"
                                        value="{{ $add_stock->final_total }}">
                                    <input type="hidden" name="grand_total" id="grand_total"
                                        value="{{ $add_stock->grand_total }}">
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
                                        {!! Form::text('invoice_no', $add_stock->invoice_no, ['class' => 'form-control', 'placeholder' => __('lang.invoice_no')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('other_expenses', __('lang.other_expenses'), []) !!} <br>
                                        {!! Form::text('other_expenses', @num_format($add_stock->other_expenses), ['class' => 'form-control', 'placeholder' => __('lang.other_expenses'), 'id' => 'other_expenses']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('discount_amount', __('lang.discount'), []) !!} <br>
                                        {!! Form::text('discount_amount', @num_format($add_stock->discount_amount), ['class' => 'form-control', 'placeholder' => __('lang.discount'), 'id' => 'discount_amount']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('other_payments', __('lang.other_payments'), []) !!} <br>
                                        {!! Form::text('other_payments', @num_format($add_stock->other_payments), ['class' => 'form-control', 'placeholder' => __('lang.other_payments'), 'id' => 'other_payments']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                                        {!! Form::select('source_type', ['user' => __('lang.user'), 'pos' => __('lang.pos'), 'store' => __('lang.store'), 'safe' => __('lang.safe')], $add_stock->source_type, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                                        {!! Form::select('source_id', $users, $add_stock->source_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select'), 'id' => 'source_id', 'required']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('transaction_date', __('lang.date') . ':*', []) !!} <br>
                                        {!! Form::text('transaction_date', @format_date($add_stock->transaction_date), ['class' => 'form-control datepicker', 'required', 'placeholder' => __('lang.date')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('payment_status', __('lang.payment_status') . ':*', []) !!}
                                        {!! Form::select('payment_status', $payment_status_array, $add_stock->payment_status, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>

                                @php
                                    $transaction_payment = $add_stock->transaction_payments->first();
                                @endphp
                                <div class="col-md-3 payment_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('amount', __('lang.amount') . ':*', []) !!} <br>
                                        {!! Form::text('amount', !empty($transaction_payment) ? @num_format($transaction_payment->amount) : 0, ['class' => 'form-control', 'placeholder' => __('lang.amount')]) !!}
                                    </div>
                                </div>
                                <input type="hidden" name="transaction_payment_id"
                                    value="@if (!empty($transaction_payment)) {{ $transaction_payment->id }} @endif">
                                <div class="col-md-3 payment_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('method', __('lang.payment_type') . ':*', []) !!}
                                        {!! Form::select('method', $payment_type_array, !empty($transaction_payment) ? $transaction_payment->method : null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 payment_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('paid_on', __('lang.payment_date') . ':', []) !!} <br>
                                        {!! Form::text('paid_on', !empty($transaction_payment) ? @format_date($transaction_payment->paid_on) : @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'placeholder' => __('lang.payment_date')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 payment_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('upload_documents', __('lang.upload_documents') . ':', []) !!} <br>
                                        <input type="file" name="upload_documents[]" id="upload_documents" multiple>
                                    </div>
                                </div>
                                <div class="col-md-3 not_cash_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('ref_number', __('lang.ref_number') . ':', []) !!} <br>
                                        {!! Form::text('ref_number', !empty($transaction_payment) ? $transaction_payment->ref_number : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.ref_number')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 not_cash_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date') . ':', []) !!} <br>
                                        {!! Form::text('bank_deposit_date', !empty($transaction_payment) ? @format_date($transaction_payment->bank_deposit_date) : @format_date(date('Y-m-d')), ['class' => 'form-control not_cash datepicker', 'placeholder' => __('lang.bank_deposit_date')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 not_cash_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('bank_name', __('lang.bank_name') . ':', []) !!} <br>
                                        {!! Form::text('bank_name', !empty($transaction_payment) ? $transaction_payment->bank_name : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.bank_name')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 due_amount_div @if ($add_stock->transaction_payments->sum('amount') == $add_stock->final_total) hide @endif">
                                    <label for="due_amount" style="margin-top: 25px;">@lang('lang.due'): <span
                                            class="due_amount_span">{{ @num_format($add_stock->final_total - $add_stock->transaction_payments->sum('amount')) }}</span></label>
                                </div>

                                <div class="col-md-3 due_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('due_date', __('lang.due_date') . ':', []) !!} <br>
                                        {!! Form::text('due_date', !empty($add_stock->due_date) ? @format_date($add_stock->due_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('lang.due_date')]) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 due_fields hide">
                                    <div class="form-group">
                                        {!! Form::label('notify_before_days', __('lang.notify_before_days') . ':', []) !!}
                                        <br>
                                        {!! Form::text('notify_before_days', $add_stock->notify_before_days, ['class' => 'form-control', 'placeholder' => __('lang.notify_before_days')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('notes', __('lang.notes') . ':', []) !!} <br>
                                        {!! Form::textarea('notes', $add_stock->notes, ['class' => 'form-control', 'rows' => 3]) !!}
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
        $(document).ready(function() {
            $('#payment_status').change();
            $('#source_type').change();
        })
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
            console.log('click');
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

            $
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
        })

        $('#source_type').change(function() {
            if ($(this).val() !== '') {
                $.ajax({
                    method: 'get',
                    url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                    data: {},
                    success: function(result) {
                        $("#source_id").empty().append(result);
                        $('#source_id').val('{{$add_stock->source_id}}');
                        $("#source_id").selectpicker("refresh");
                    },
                });
            }
        })
        $(document).on('click', '#add-selected-btn', function() {
            $('#select_products_modal').modal('hide');
            $.each(product_selected, function(index, value) {
                get_label_product_row(value.product_id, value.variation_id);
            });
            product_selected = [];
            product_table.ajax.reload();
        })
    </script>
@endsection
