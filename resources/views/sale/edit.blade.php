@extends('layouts.app')
@section('title', __('lang.edit_sale'))

@section('content')

    <div class="container-fluid no-print">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_sale') ({{ $sale->invoice_no }})</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['url' => action('SellPosController@update', $sale->id), 'method' => 'put', 'files' => true, 'class' => 'pos-form', 'id' => 'edit_pos_form']) !!}
                        <input type="hidden" name="is_edit" id="is_edit" value="1">
                        <input type="hidden" name="store_id" id="store_id" value="{{ $sale->store_id }}">
                        <input type="hidden" name="default_customer_id" id="default_customer_id"
                            value="@if (!empty($walk_in_customer)) {{ $walk_in_customer->id }} @endif">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('customer_id', $customers, $sale->customer_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'customer_id']) !!}
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
                                    <div class="col-md-2 hide">
                                        <div class="form-group">
                                            <input type="hidden" name="exchange_rate" id="exchange_rate"
                                                value="@if (!empty($sale->exchange_rate)) {{ $sale->exchange_rate }}@else{{ 1 }} @endif">
                                            <input type="hidden" name="default_currency_id" id="default_currency_id"
                                                value="{{ !empty($sale->default_currency_id) ? $sale->default_currency_id : App\Models\System::getProperty('currency') }}">
                                            {!! Form::label('received_currency_id', __('lang.received_currency') . ':', []) !!}
                                            {!! Form::select('received_currency_id', $exchange_rate_currencies, !empty($sale->received_currency_id) ? $sale->received_currency_id : App\Models\System::getProperty('currency'), ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('status', __('lang.status') . ':*') !!}
                                            {!! Form::select('status', ['final' => __('lang.completed'), 'pending' => __('lang.pending')], $sale->status, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::label('transaction_date', __('lang.date_and_time'), []) !!}
                                        <input type="datetime-local" id="transaction_date" name="transaction_date"
                                            value="{{ Carbon\Carbon::parse($sale->transaction_date)->format('Y-m-d\TH:i') }}"
                                            class="form-control">
                                    </div>
                                    @if (session('system_mode') == 'garments')
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-default" style="margin-top: 30px;"
                                                data-toggle="modal" data-target="#customer_sizes_modal"><img
                                                    style="width: 20px; height: 25px;"
                                                    src="{{ asset('images/269 Garment Icon.png') }}"
                                                    alt="@lang('lang.customer_size')" data-toggle="tooltip"
                                                    title="@lang('lang.customer_size')"></button>
                                        </div>
                                    @endif
                                    <div class="col-md-8 offset-md-2" style="margin-top: 10px;">
                                        <div class="search-box input-group">
                                            <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                                    class="fa fa-search"></i></button>
                                            <input type="text" name="search_product" id="search_product"
                                                placeholder="@lang('lang.enter_product_name_to_print_labels')" class="form-control ui-autocomplete-input"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="margin-top: 20px ">
                                    <div class="table-responsive transaction-list">
                                        <table id="product_table" style="width: 100% " class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%">{{ __('lang.product') }}</th>
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
                                            <input type="hidden" name="row_count" id="row_count"
                                                value="{{ $sale->transaction_sell_lines->count() }}">
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
                                            <input type="hidden" id="total_item_tax" name="total_item_tax"
                                                value="{{ $sale->total_item_tax }}">
                                            <input type="hidden" id="total_tax" name="total_tax"
                                                value="{{ $sale->total_tax }}">
                                            <input type="hidden" id="is_direct_sale" name="is_direct_sale"
                                                value="{{ $sale->is_direct_sale }}">
                                            <input type="hidden" name="discount_amount" id="discount_amount"
                                                value="{{ $sale->discount_amount }}">
                                            <input type="hidden" id="store_pos_id" name="store_pos_id"
                                                value="{{ $sale->store_pos_id }}" />
                                            <input type="hidden" name="service_fee_id_hidden" id="service_fee_id_hidden"
                                                value="{{ $sale->service_fee_id }}">
                                            <input type="hidden" name="service_fee_rate" id="service_fee_rate"
                                                value="{{ $sale->service_fee_rate }}">
                                            <input type="hidden" name="service_fee_value" id="service_fee_value"
                                                value="{{ $sale->service_fee_value }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_id">@lang('lang.tax')</label>
                                    <select class="form-control" name="tax_id" id="tax_id">
                                        <option value="" selected>No Tax</option>
                                        @foreach ($taxes as $tax)
                                            <option @if ($tax['id'] == $sale->tax_id) selected @endif
                                                data-rate="{{ $tax['rate'] }}" value="{{ $tax['id'] }}">
                                                {{ $tax['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="tax_id_hidden" id="tax_id_hidden" value="{{$sale->tax_id}}">
                                    <input type="hidden" name="tax_method" id="tax_method" value="{{$sale->tax_method}}">
                                    <input type="hidden" name="tax_rate" id="tax_rate" value="{{$sale->tax_rate}}">
                                    <input type="hidden" name="tax_type" id="tax_type" value="{{$sale->tax->type??''}}">
                                </div>
                            </div>
                            <div class="col-md-3 @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('lang.type') . ':*') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], $sale->discount_type, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3  @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __('lang.discount_value') . ':*') !!}
                                    {!! Form::text('discount_value', @num_format($sale->discount_value), ['class' => 'form-control', 'placeholder' => __('lang.discount_value'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3  @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <label for="deliveryman_id">@lang('lang.deliveryman'):</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" name="deliveryman_id" id="deliveryman_id"
                                        data-live-search="true">
                                        <option value="" selected>@lang('lang.please_select')</option>
                                        @foreach ($deliverymen as $key => $name)
                                            <option @if ($sale->deliveryman_id == $key) selected @endif
                                                value="{{ $key }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="deliveryman_id_hidden" id="deliveryman_id_hidden"
                                    value="{{ $sale->deliveryman_id }}">
                            </div>
                            <div class="col-md-3  @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <div class="form-group">
                                    {!! Form::label('delivery_cost', __('lang.delivery_cost') . ':*') !!}
                                    {!! Form::text('delivery_cost', @num_format($sale->delivery_cost), ['class' => 'form-control', 'placeholder' => __('lang.delivery_cost'), 'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="delivery_cost_paid_by_customer"
                                        name="delivery_cost_paid_by_customer" value="1"
                                        @if ($sale->delivery_cost_paid_by_customer == 1) checked @endif
                                        id="delivery_cost_paid_by_customer">
                                    @lang('lang.delivery_cost_paid_by_customer')
                                </label>
                            </div>
                            <div class="col-md-3 @if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) hide @endif">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="delivery_cost_given_to_deliveryman"
                                        name="delivery_cost_given_to_deliveryman" value="1"
                                        @if ($sale->delivery_cost_given_to_deliveryman == 1) checked @endif
                                        id="delivery_cost_given_to_deliveryman">
                                    @lang('lang.delivery_cost_given_to_deliveryman')
                                </label>
                            </div>
                        </div>
                        <div class="payment-amount @if (!auth()->user()->can('superadmin')) hide @endif">
                            <h2>{{ __('lang.grand_total') }} <span class="final_total_span">0.00</span></h2>
                        </div>
                        <div class="sales_payments">
                            <br>
                            <br>
                            <h4>@lang('lang.payments'):</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('payment_status', __('lang.payment_status') . ':*', []) !!}
                                        {!! Form::select('payment_status', $payment_status_array, $sale->payment_status, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="payment_rows" id="payment_rows" style="width: 100%;">
                                    @forelse ($sale->transaction_payments as $payment)
                                        @include('sale.partials.edit_payment_row', [
                                            'payment' => $payment,
                                            'index' => $loop->index,
                                        ])
                                        <hr>
                                    @empty
                                        @include('sale.partials.edit_payment_row', [
                                            'payment' => null,
                                            'index' => 0,
                                        ])
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 btn-add-payment">
                                <button type="button" id="add_payment_row" class="btn btn-primary btn-block">
                                    @lang('lang.add_payment_row')</button>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>@lang('lang.sale_note')</label>
                                    <textarea rows="3" class="form-control" name="sale_note">{{ $sale->sale_note }}</textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>@lang('lang.staff_note')</label>
                                    <textarea rows="3" class="form-control" name="staff_note">{{ $sale->staff_note }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <input type="hidden" name="terms_and_condition_hidden" id="terms_and_condition_hidden"
                                    value="{{ $sale->terms_and_condition_id }}">
                                <div class="col-md-4">
                                    {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('terms_and_condition_id', $tac, $sale->terms_and_condition_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'terms_and_condition_id']) !!}
                                    </div>
                                    <div class="tac_description_div"><span></span></div>
                                </div>
                                <div class="col-md-4 @if (!auth()->user()->can('hr_management.employee_commission.create_and_edit')) hide @endif">
                                    <div class="form-group">
                                        {!! Form::label('commissioned_employees', __('lang.commissioned_employees'), []) !!}
                                        {!! Form::select('commissioned_employees[]', $employees, $sale->commissioned_employees, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'multiple', 'id' => 'commissioned_employees']) !!}
                                    </div>
                                </div>
                                <div
                                    class="col-md-4  @if (!auth()->user()->can('hr_management.employee_commission.create_and_edit')) hide @endif @if ($sale->shared_commission != 1) hide @endif shared_commission_div">
                                    <div class="i-checks" style="margin-top: 37px;">
                                        <input id="shared_commission" name="shared_commission" type="checkbox"
                                            value="1" @if ($sale->shared_commission) checked @endif
                                            class="form-control-custom">
                                        <label for="shared_commission"><strong>
                                                @lang('lang.shared_commission')
                                            </strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <button id="update-btn" type="button"
                                    class="btn btn-primary">@lang('lang.update')</button>
                                <button id="submit-btn" type="button" class="btn btn-danger">@lang('lang.print')</button>
                            </div>
                        </div>
                        <input type="hidden" name="customer_size_id_hidden" id="customer_size_id_hidden"
                            value="{{ $sale->customer_size_id }}">
                        @include('sale_pos.partials.customer_sizes_modal', [
                            'transaction' => $sale,
                        ])
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
    <script>
        $(document).ready(function() {
            $('.method').change()
            @if ($sale->payment_status == 'pending')
                $('.received_amount').attr('required', false);
            @endif
            $(document).on('change', 'select#payment_status', function() {
                if ($(this).val() == 'pending') {
                    $('.received_amount').attr('required', false);
                } else {
                    $('.received_amount').attr('required', true);
                }
            })
        });

        $(document).on('change', '.payment_date', function() {
            let payment_date = $(this).val();
            let payment_row = $(this).closest('.payment_row');

            $.ajax({
                method: 'GET',
                url: '/cash-register/get-available-cash-register/{{ $sale->created_by }}',
                data: {
                    payment_date: payment_date,
                    transaction_id: {{ $sale->id }}
                },
                success: function(result) {
                    if (!result.success) {
                        swal("Error", result.msg, 'error');
                        $(payment_row).find('.cash_register_id').val('')
                    } else {
                        if (!jQuery.isEmptyObject(result.cash_register)) {
                            $(payment_row).find('.cash_register_id').val(result.cash_register.id)
                        } else {
                            $(payment_row).find('.cash_register_id').val('')
                        }
                    }
                },
            });
        })
    </script>
@endsection
