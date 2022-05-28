@extends('layouts.app')
@section('title', __('lang.quotation'))

@section('content')

    <div class="container-fluid no-print">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.quotation')</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'files' => true, 'class' => 'pos-form', 'id' => 'add_pos_form']) !!}
                        <input type="hidden" name="row_count" id="row_count" value="0">
                        <input type="hidden" name="default_customer_id" id="default_customer_id"
                            value="@if (!empty($walk_in_customer)) {{ $walk_in_customer->id }} @endif">
                        <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('customer_id', $customers, !empty($walk_in_customer) ? $walk_in_customer->id : null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'customer_id', 'required', 'placeholder' => __('lang.please_select')]) !!}
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
                                    <div class="col-md-3">
                                        <label style="margin-top: 36px;">@lang('lang.customer_type'): <span
                                                class="customer_type_name"></span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::label('store_id', __('lang.store'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('store_id', $stores, session('user.store_id'), ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'store_id', 'required']) !!}
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
                                        <table id="product_table" style="width: 100%;" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">{{ __('lang.product') }}</th>
                                                    <th style="width: 20%">{{ __('lang.quantity') }}</th>
                                                    <th style="width: 20%">{{ __('lang.price') }}</th>
                                                    <th style="width: 20%">{{ __('lang.discount') }}</th>
                                                    <th style="width: 10%">{{ __('lang.sub_total') }}</th>
                                                    <th style="width: 10%">{{ __('lang.current_stock') }}</th>
                                                    <th style="width: 20%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th style="text-align: right">@lang('lang.total')</th>
                                                    <th><span class="grand_total_span"></span></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row" style="display: none;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" id="final_total" name="final_total" />
                                            <input type="hidden" id="grand_total" name="grand_total" />
                                            <input type="hidden" id="gift_card_id" name="gift_card_id" />
                                            <input type="hidden" id="coupon_id" name="coupon_id">
                                            <input type="hidden" id="total_tax" name="total_tax" value="0.00">
                                            <input type="hidden" name="discount_amount" id="discount_amount">
                                            <input type="hidden" id="store_pos_id" name="store_pos_id"
                                                value="{{ $store_pos->id }}" />
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
                                        <input id="block_qty" name="block_qty" type="checkbox" value="1"
                                            class="form-control-custom">
                                        <label for="block_qty"><strong>@lang('lang.block_qty')</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('block_for_days', __('lang.block_for_days') . ':') !!}
                                    {!! Form::text('block_for_days', 1, ['class' => 'form-control', 'placeholder' => __('lang.block_for_days')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('validity_days', __('lang.validity_days') . ':') !!}
                                    {!! Form::text('validity_days', null, ['class' => 'form-control', 'placeholder' => __('lang.validity_days')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_id">@lang('lang.tax')</label>
                                    <select class="form-control" name="tax_id" id="tax_id">
                                        <option value="" selected>No Tax</option>
                                        @foreach ($taxes as $tax)
                                            <option data-rate="{{ $tax->rate }}" value="{{ $tax->id }}">
                                                {{ $tax->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('lang.discount_type') . ':') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], 'fixed', ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __('lang.discount_value') . ':') !!}
                                    {!! Form::text('discount_value', null, ['class' => 'form-control', 'placeholder' => __('lang.discount_value')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                    {!! Form::select('terms_and_condition_id', $tac, null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'id' => 'terms_and_condition_id']) !!}
                                </div>
                                <div class="tac_description_div"><span></span></div>
                            </div>

                        </div>
                        <input type="hidden" name="submit_type" id="submit_type" value="save">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="submit-btn">@lang('lang.save')</button>
                                <button type="button" class="btn btn-secondary" id="print-btn">@lang('lang.print')</button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#email_modal">@lang('lang.send')</button>
                            </div>
                        </div>

                        <div class="modal fade" role="dialog" id="email_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">@lang('lang.email')</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">@lang('lang.emails')
                                                    <small>@lang('lang.separated_by_comma')</small></label>
                                                <input type="emails" name="emails" id="emails" value=""
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger"
                                            id="send-btn">@lang('lang.send')</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">@lang('lang.close')</button>
                                    </div>
                                </div>
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
        $('#print-btn').click(function() {
            $('#submit_type').val('print');
            pos_form_obj.submit();
        })
        $('#send-btn').click(function() {
            $('#submit_type').val('send');
            pos_form_obj.submit();
        });
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
