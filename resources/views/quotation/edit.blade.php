@extends('layouts.app')
@section('title', __('lang.edit_quotation'))

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_quotation')</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['url' => action('QuotationController@update', $sale->id), 'method' => 'put', 'files' => true, 'class' => 'pos-form', 'id' => 'edit_quotation_form']) !!}
                        <input type="hidden" name="row_count" id="row_count"
                            value="{{ $sale->transaction_sell_lines->count() }}">
                        <input type="hidden" name="store_id" id="store_id" value="{{ $sale->store_id }}">
                        <input type="hidden" name="default_customer_id" id="default_customer_id"
                            value="@if (!empty($walk_in_customer)) {{ $walk_in_customer->id }} @endif">
                        <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                        <div class="row">
                            <div class="col-md-12">
                                <b>@lang('lang.reference'): {{ $sale->invoice_no }}</b>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('customer_id', $customers, $sale->customer_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'customer_id', 'required']) !!}
                                            <span class="input-group-btn">
                                                @can('customer_module.customer.create_and_edit')
                                                    <button class="btn-modal btn btn-default bg-white btn-flat"
                                                        data-href="{{ action('CustomerController@create') }}?quick_add=1"
                                                        data-container=".view_modal"><i
                                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('store_id', __('lang.store'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('store_id', $stores, $sale->store_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'store_id', 'required']) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-8 offset-md-1" style="margin-top: 10px;">
                                        <div class="search-box input-group">
                                            <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                                    class="fa fa-search"></i></button>
                                            <input type="text" name="search_product" id="search_product"
                                                placeholder="@lang('lang.enter_product_name_to_print_labels')" class="form-control ui-autocomplete-input"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        @include('quotation.partial.product_selection')
                                    </div>
                                </div>
                                <div class="col-md-12" style="margin-top: 20px ">
                                    <div class="table-responsive transaction-list">
                                        <table id="product_table" style="width: 100% " class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">{{ __('lang.product') }}</th>
                                                    <th style="width: 20%">{{ __('lang.quantity') }}</th>
                                                    <th style="width: 20%">{{ __('lang.price') }}</th>
                                                    <th style="width: 20%">{{ __('lang.discount') }}</th>
                                                    <th style="width: 10%">{{ __('lang.sub_total') }}</th>
                                                    <th style="width: 20%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @include('sale.partials.edit_product_row', [
                                                    'products' => $sale->transaction_sell_lines,
                                                ])
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <th style="text-align: right">@lang('lang.total')</th>
                                                    <th><span
                                                            class="grand_total_span">{{ @num_format($sale->grand_total) }}</span>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row" style="display: none;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" id="final_total" name="final_total"
                                                value="{{ $sale->final_total }}" />
                                            <input type="hidden" id="grand_total" name="grand_total"
                                                value="{{ $sale->grand_total }}" />
                                            <input type="hidden" id="gift_card_id" name="gift_card_id"
                                                value="{{ $sale->gift_card_id }}" />
                                            <input type="hidden" id="coupon_id" name="coupon_id"
                                                value="{{ $sale->coupon_id }}">
                                            <input type="hidden" id="total_tax" name="total_tax"
                                                value="{{ $sale->total_tax }}">
                                            <input type="hidden" name="discount_amount" id="discount_amount"
                                                value="{{ $sale->discount_amount }}">
                                            <input type="hidden" id="store_pos_id" name="store_pos_id"
                                                value="{{ $sale->store_pos_id }}" />
                                            <input type="hidden" id="is_quotation" name="is_quotation" value="1" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <input id="block_qty" name="block_qty" type="checkbox"
                                            @if ($sale->block_qty) checked @endif value="1"
                                            class="form-control-custom">
                                        <label for="block_qty"><strong>@lang('lang.block_qty')</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('block_for_days', __('lang.block_for_days') . ':') !!}
                                    {!! Form::text('block_for_days', $sale->block_for_days, ['class' => 'form-control', 'placeholder' => __('lang.block_for_days'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('validity_days', __('lang.validity_days') . ':') !!}
                                    {!! Form::text('validity_days', $sale->validity_days, ['class' => 'form-control', 'placeholder' => __('lang.validity_days'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_id">@lang('lang.tax')</label>
                                    <select class="form-control" name="tax_id" id="tax_id">
                                        <option value="" selected>No Tax</option>
                                        @foreach ($taxes as $tax)
                                            <option @if ($tax->id == $sale->tax_id) selected @endif
                                                data-rate="{{ $tax->rate }}" value="{{ $tax->id }}">
                                                {{ $tax->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('lang.type') . ':*') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], $sale->discount_type, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __('lang.discount_value') . ':*') !!}
                                    {!! Form::text('discount_value', @num_format($sale->discount_value), ['class' => 'form-control', 'placeholder' => __('lang.discount_value'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('status', __('lang.status'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('status', ['draft' => 'Draft', 'approved' => 'Approved', 'rejected' => 'Rejected', 'expired' => 'Expired', 'valid' => 'Valid'], $sale->status, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'status', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('terms_and_condition_id', $tac, $sale->terms_and_condition_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'terms_and_condition_id']) !!}
                                </div>
                                <div class="tac_description_div"><span></span></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">@lang('lang.update')</button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This will be printed -->
    <section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/product_selection.js') }}"></script>
    <script>
        $(document).on('click', '#add-selected-btn', function() {
            $('#select_products_modal').modal('hide');
            $.each(product_selected, function(index, value) {
                get_label_product_row(value.product_id, value.variation_id);
            });
            product_selected = [];
            product_table.ajax.reload();
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
