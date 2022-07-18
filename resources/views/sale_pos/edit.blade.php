@extends('layouts.app')
@section('title', __('lang.pos'))

@section('content')
    <section class="forms pos-section no-print">
        <div class="container-fluid">
            <div class="row">
                <audio id="mysoundclip1" preload="auto">
                    <source src="{{ asset('audio/beep-timber.mp3') }}">
                    </source>
                </audio>
                <audio id="mysoundclip2" preload="auto">
                    <source src="{{ asset('audio/beep-07.mp3') }}">
                    </source>
                </audio>
                <div class="@if (session('system_mode') == 'pos') col-md-7 @else col-md-6 @endif">
                    {!! Form::open(['url' => action('SellPosController@update', $transaction->id), 'method' => 'PUT', 'files' => true, 'class' => 'pos-form', 'id' => 'edit_pos_form']) !!}
                    <div class="card">
                        <div class="card-body" style="padding: 0px 10px; !important">
                            <input type="hidden" name="transaction_id" id="transaction_id" value="{{ $transaction->id }}">
                            <input type="hidden" name="store_id" id="store_id" value="{{ $transaction->store_id }}">
                            <input type="hidden" name="customer_size_id_hidden" id="customer_size_id_hidden"
                                value="{{ $transaction->customer_size_id }}">
                            <input type="hidden" name="default_customer_id" id="default_customer_id"
                                value="@if (!empty($walk_in_customer)) {{ $walk_in_customer->id }} @endif">
                            <input type="hidden" name="row_count" id="row_count"
                                value="{{ $transaction->transaction_sell_lines->count() }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {!! Form::label('store_id', __('lang.store') . ':*', []) !!}
                                                {!! Form::select('store_id', $stores, $transaction->store_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {!! Form::label('store_pos_id', __('lang.pos') . ':*', []) !!}
                                                {!! Form::select('store_pos_id', $store_poses, $transaction->store_pos_id, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'required', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden" name="setting_invoice_lang" id="setting_invoice_lang"
                                                    value="{{ !empty(App\Models\System::getProperty('invoice_lang')) ? App\Models\System::getProperty('invoice_lang') : 'en' }}">
                                                {!! Form::label('invoice_lang', __('lang.invoice_lang') . ':', []) !!}
                                                {!! Form::select('invoice_lang', $languages + ['ar_and_en' => 'Arabic and English'], !empty(App\Models\System::getProperty('invoice_lang')) ? App\Models\System::getProperty('invoice_lang') : 'en', ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden" name="exchange_rate" id="exchange_rate"
                                                    value="@if (!empty($transaction->exchange_rate)) {{ $transaction->exchange_rate }}@else{{ 1 }} @endif">
                                                <input type="hidden" name="default_currency_id" id="default_currency_id"
                                                    value="{{ !empty($transaction->default_currency_id) ? $transaction->default_currency_id : App\Models\System::getProperty('currency') }}">
                                                {!! Form::label('received_currency_id', __('lang.received_currency') . ':', []) !!}
                                                {!! Form::select('received_currency_id', $exchange_rate_currencies, !empty($transaction->received_currency_id) ? $transaction->received_currency_id : App\Models\System::getProperty('currency'), ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group" style="margin-top: 31px;">
                                                <select class="form-control" name="tax_id" id="tax_id">
                                                    <option value="">No Tax</option>
                                                    @foreach ($taxes as $tax)
                                                        <option data-rate="{{ $tax['rate'] }}"
                                                            @if (!empty($transaction) && $transaction->tax_id == $tax['id']) selected @endif
                                                            value="{{ $tax['id'] }}">{{ $tax['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="tax_id_hidden" id="tax_id_hidden" value="{{$transaction->tax_id}}">
                                                <input type="hidden" name="tax_method" id="tax_method" value="">
                                                <input type="hidden" name="tax_rate" id="tax_rate" value="0">
                                                <input type="hidden" name="tax_type" id="tax_type" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-link btn-sm" style="margin-top: 24px;"
                                                data-toggle="modal" data-target="#delivery-cost-modal"><img
                                                    src="{{ asset('images/delivery.jpg') }}" alt="delivery"
                                                    style="height: 35px; width: 40px;"></button>
                                        </div>
                                        {{-- @if (session('system_mode') == 'restaurant')
                                            <div class="col-md-3">
                                                <button type="button"
                                                    data-href="{{ action('DiningRoomController@getDiningModal') }}"
                                                    data-container="#dining_model"
                                                    class="btn btn-modal pull-right mt-4"><img
                                                        src="{{ asset('images/black-table.jpg') }}" alt="black-table"
                                                        style="width: 50px; height: 35px;"></button>
                                            </div>
                                        @endif --}}
                                    </div>
                                </div>
                                <div class="col-md-12 main_settings">
                                    <div class="row table_room_hide">
                                        <div class="col-md-3">
                                            {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                            <div class="input-group my-group">
                                                {!! Form::select('customer_id', $customers, !empty($transaction) ? $transaction->customer_id : null, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'id' => 'customer_id']) !!}
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
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" style="margin-top: 30px;"
                                                data-toggle="modal"
                                                data-target="#contact_details_modal">@lang('lang.details')</button>
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
                                        <div class="col-md-2">
                                            <label for="" style="margin-top: 40px;">@lang('lang.customer_type'): <span
                                                    class="customer_type_name"></span></label>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="customer_balance"
                                                style="margin-top: 30px; margin-bottom: 0px;">@lang('lang.balance'):
                                                <span class="customer_balance">{{ @num_format(0) }}</span></label>
                                            <label for="points" style="margin: 0px;">@lang('lang.points'):
                                                <span class="points"><span
                                                        class="customer_points_span">{{ @num_format(0) }}</span></span></label>
                                        </div>
                                        @if (session('system_mode') == 'pos' || session('system_mode') == 'restaurant')
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-xs pull-right"
                                                    style="margin-top: 38px;" data-toggle="modal"
                                                    data-target="#non_identifiable_item_modal">@lang('lang.non_identifiable_item')</button>
                                            </div>
                                        @endif
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-xs  pull-right"
                                                style="margin-top: 38px;" id="print_and_draft"><i
                                                    class="dripicons-print"></i></button>
                                            <input type="hidden" id="print_and_draft_hidden" name="print_and_draft_hidden"
                                                value="">
                                        </div>
                                    </div>
                                    <div class="row table_room_show hide">
                                        <div class="col-md-4">
                                            <div class=""
                                                style="padding: 5px 5px; background:#0082ce; color: #fff; font-size: 20px; font-weight: bold; text-align: center; border-radius: 5px;">
                                                <span class="room_name">
                                                    @if (!empty($transaction->dining_room))
                                                        {{ $transaction->dining_room->name }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for=""
                                                style="font-size: 20px !important; font-weight: bold; text-align: center; margin-top: 3px;">@lang('lang.table'):
                                                <span class="table_name">
                                                    @if (!empty($transaction->dining_table))
                                                        {{ $transaction->dining_table->name }}
                                                    @endif
                                                </span></label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group my-group">
                                                {!! Form::select('service_fee_id', $service_fees, $transaction->service_fee_id, ['class' => 'form-control', 'placeholder' => __('lang.select_service'), 'id' => 'service_fee_id']) !!}
                                            </div>
                                        </div>
                                        <input type="hidden" name="service_fee_id_hidden" id="service_fee_id_hidden"
                                            value="{{ $transaction->service_fee_id }}">
                                        <input type="hidden" name="service_fee_rate" id="service_fee_rate"
                                            value="{{ $transaction->service_fee_rate }}">
                                        <input type="hidden" name="service_fee_value" id="service_fee_value"
                                            value="{{ $transaction->service_fee_value }}">
                                    </div>

                                    <div class="col-md-12" style="margin-top: 10px;">
                                        <div class="search-box input-group">
                                            <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                                    class="fa fa-search"></i></button>
                                            <input type="text" name="search_product" id="search_product"
                                                placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                                class="form-control ui-autocomplete-input" autocomplete="off">
                                            @if (isset($weighing_scale_setting['enable']) && $weighing_scale_setting['enable'])
                                                <button type="button" class="btn btn-default bg-white btn-flat"
                                                    id="weighing_scale_btn" data-toggle="modal"
                                                    data-target="#weighing_scale_modal"
                                                    title="@lang('lang.weighing_scale')"><i
                                                        class="fa fa-balance-scale text-primary fa-lg"></i></button>
                                            @endif
                                            <button type="button" class="btn btn-success btn-lg btn-modal"
                                                data-href="{{ action('ProductController@create') }}?quick_add=1"
                                                data-container=".view_modal"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="margin-top: 5px; padding: 0px;">
                                        <div class="table-responsive transaction-list">
                                            <table id="product_table" style="width: 100% "
                                                class="table table-hover table-striped order-list table-fixed">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 18% @else 20% @endif; font-size: 12px !important;">
                                                            @lang('lang.product')</th>
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 18% @else 20% @endif; font-size: 12px !important;">
                                                            @lang('lang.quantity')</th>
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 16% @else 15% @endif; font-size: 12px !important;">
                                                            @lang('lang.price')</th>
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 13% @else 15% @endif; font-size: 12px !important;">
                                                            @lang('lang.discount')</th>
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 10% @else 15% @endif; font-size: 12px !important;">
                                                            @lang('lang.sub_total')</th>
                                                        @if (session('system_mode') != 'restaurant')
                                                            <th
                                                                style="width: @if (session('system_mode') != 'restaurant') 10% @else 15% @endif; font-size: 12px !important;">
                                                                @lang('lang.current_stock')</th>
                                                        @endif
                                                        <th
                                                            style="width: @if (session('system_mode') != 'restaurant') 10% @else 15% @endif; font-size: 12px !important;">
                                                            @lang('lang.action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
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
                                                <input type="hidden" id="total_item_tax" name="total_item_tax" value="0.00">
                                                <input type="hidden" id="total_tax" name="total_tax" value="0.00">

                                                <input type="hidden" id="store_pos_id" name="store_pos_id"
                                                    value="{{ $store_pos->id }}" />
                                                <input type="hidden" id="status" name="status"
                                                    value="@if (!empty(request()->status)) {{ request()->status }}@else{{ 'final' }} @endif" />
                                                <input type="hidden" id="total_sp_discount" name="total_sp_discount"
                                                    value="0" />
                                                <input type="hidden" id="total_pp_discount" name="total_pp_discount"
                                                    value="0" />
                                                <input type="hidden" name="dining_table_id" id="dining_table_id"
                                                    value="{{ $transaction->dining_table_id }}">
                                                <input type="hidden" name="dining_action_type" id="dining_action_type"
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 totals table_room_hide"
                                        style="border-top: 2px solid #e4e6fc; padding-top: 10px;">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <span class="totals-title">{{ __('lang.items') }}</span><span
                                                    id="item">0</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="totals-title">{{ __('lang.total') }}</span><span
                                                    id="subtotal">0.00</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="totals-title">{{ __('lang.random_discount') }} <button
                                                        type="button" class="btn btn-link btn-sm" data-toggle="modal"
                                                        data-target="#discount_modal"> <i
                                                            class="dripicons-document-edit"></i></button></span><span
                                                    id="discount">0.00</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="totals-title">{{ __('lang.tax') }}</span><span
                                                    id="tax">0.00</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="totals-title">{{ __('lang.delivery') }}</span><span
                                                    id="delivery-cost">
                                                    @if (!empty($transaction))
                                                        {{ @num_format($transaction->delivery_cost) }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-12 table_room_show hide"
                                        style="border-top: 2px solid #e4e6fc; margin-top: 10px;">
                                        <div class="row">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <b>@lang('lang.total'): <span class="subtotal">0.00</span></b>
                                                </div>
                                                <div class="row">
                                                    <b>@lang('lang.discount'): <span class="discount_span">0.00</span></b>
                                                </div>
                                                <div class="row">
                                                    <b>@lang('lang.service'): <span
                                                            class="service_value_span">0.00</span></b>
                                                </div>
                                                <div class="row">
                                                    <b>@lang('lang.grand_total'): <span
                                                            class="final_total_span">0.00</span></b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-4">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <button type="button" name="action" value="print"
                                                        id="dining_table_print" class="btn mr-2 text-white"
                                                        style="background: orange;">@lang('lang.print')</button>
                                                    <button type="button" name="action" value="save" id="dining_table_save"
                                                        class="btn mr-2 text-white btn-success">@lang('lang.save')</button>
                                                    <button data-method="cash" style="background: #0082ce" type="button"
                                                        class="btn mr-2 payment-btn text-white" data-toggle="modal"
                                                        data-target="#add-payment"
                                                        id="cash-btn">@lang('lang.pay_and_close')</button>
                                                    <button style="background-color: #d63031" type="button"
                                                        class="btn mr-2 btn-md payment-btn text-white" data-toggle="modal"
                                                        data-target="#discount_modal">@lang('lang.random_discount')</button>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button style="background-color: #ff0000;" type="button"
                                                    class="btn text-white" id="cancel-btn" onclick="return confirmCancel()">
                                                    @lang('lang.cancel')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payment-amount table_room_hide">
                            <h2>{{ __('lang.grand_total') }} <span class="final_total_span">0.00</span></h2>
                        </div>
                        <input type="hidden" name="terms_and_condition_hidden" id="terms_and_condition_hidden"
                            value="{{ $transaction->terms_and_condition_id }}">
                        <div class="row table_room_hide">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                            <select name="terms_and_condition_id" id="terms_and_condition_id"
                                                class="form-control selectpicker" data-live-search="true">
                                                <option value="">@lang('lang.please_select')</option>
                                                @foreach ($tac as $key => $item)
                                                    <option @if ($transaction->terms_and_condition_id == $key) selected @endif
                                                        value="{{ $key }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="tac_description_div"><span></span></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('commissioned_employees', __('lang.commissioned_employees'), []) !!}
                                            {!! Form::select('commissioned_employees[]', $employees, $transaction->commissioned_employees, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'multiple', 'id' => 'commissioned_employees']) !!}
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-4 @if ($transaction->shared_commission != 1) hide @endif shared_commission_div">
                                        <div class="i-checks" style="margin-top: 37px;">
                                            <input id="shared_commission" name="shared_commission" type="checkbox" value="1"
                                                @if ($transaction->shared_commission == 1) checked @endif
                                                class="form-control-custom">
                                            <label for="shared_commission"><strong>
                                                    @lang('lang.shared_commission')
                                                </strong></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-options row table_room_hide"
                            style=" width: @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket') 100%; @else 50%; @endif">
                            <div class="column-5">
                                <button data-method="cash" style="background: #0094ce" type="button"
                                    class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                    id="cash-btn"><i class="fa fa-money"></i>
                                    @lang('lang.pay')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="coupon" style="background: #00cec9" type="button"
                                    class="btn btn-custom" data-toggle="modal" data-target="#coupon_modal"
                                    id="coupon-btn"><i class="fa fa-tag"></i>
                                    @lang('lang.coupon')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="paypal" style="background-color: #213170" type="button"
                                    class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                    id="paypal-btn"><i class="fa fa-paypal"></i>
                                    @lang('lang.other_online_payments')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="draft" style="background-color: #e28d02" type="button"
                                    class="btn btn-custom" id="draft-btn"><i class="dripicons-flag"></i>
                                    @lang('lang.draft')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="draft" style="background-color: #0952a5" type="button"
                                    class="btn btn-custom" id="view-draft-btn"
                                    data-href="{{ action('SellPosController@getDraftTransactions') }}"><i
                                        class="dripicons-flag"></i>
                                    @lang('lang.view_draft')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="online-order" style="background-color: #69a509" type="button"
                                    class="btn btn-custom" id="view-online-order-btn"
                                    data-href="{{ action('SellPosController@getOnlineOrderTransactions') }}"><img
                                        src="{{ asset('images/online_order.png') }}" style="height: 25px; width: 35px;"
                                        alt="icon">
                                    @lang('lang.online_orders') <span
                                        class="badge badge-danger online-order-badge hide">0</span></button>
                            </div>
                            <div class="column-5">
                                <button data-method="pay-later" style="background-color: #cf2929" type="button"
                                    class="btn btn-custom" id="pay-later-btn"><i class="fa fa-hourglass-start"></i>
                                    @lang('lang.pay_later')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="cash" style="background-color: #d63031;" type="button"
                                    class="btn btn-custom" id="cancel-btn" onclick="return confirmCancel()"><i
                                        class="fa fa-close"></i>
                                    @lang('lang.cancel')</button>
                            </div>
                            <div class="column-5">
                                <button data-method="cash" style="background-color: #ffc107;" type="button"
                                    class="btn btn-custom" id="recent-transaction-btn"
                                    data-href="{{ action('SellPosController@getRecentTransactions') }}"><i
                                        class="dripicons-clock"></i>
                                    @lang('lang.recent_transactions')</button>
                            </div>
                        </div>
                    </div>


                    @include('sale_pos.partials.payment_modal')
                    @include('sale_pos.partials.discount_modal')
                    {{-- @include('sale_pos.partials.tax_modal') --}}
                    @include('sale_pos.partials.delivery_cost_modal')
                    @include('sale_pos.partials.coupon_modal')
                    @include('sale_pos.partials.contact_details_modal')
                    @include('sale_pos.partials.weighing_scale_modal')
                    @include('sale_pos.partials.customer_sizes_modal')



                    {!! Form::close() !!}
                </div>
                <!-- product list -->
                <div class="@if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket') col-md-5 @else col-md-6 @endif">
                    <!-- navbar-->
                    <header class="header">
                        <nav class="navbar">
                            <div class="container-fluid">
                                <div class="navbar-holder d-flex align-items-center justify-content-between">
                                    <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>
                                    <div class="navbar-header">

                                        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                                            <li class="nav-item">
                                                <a href="{{ action('SellController@create') }}"
                                                    id="commercial_invoice_btn" data-toggle="tooltip"
                                                    data-title="@lang('lang.add_sale')" class="btn no-print"><img
                                                        src="{{ asset('images/396 Commercial Invoice Icon.png') }}"
                                                        alt="" style="height: 40px; width: 35px;">
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a target="_blank"
                                                    href="{{ action('ContactUsController@getUserContactUs') }}"
                                                    id="contact_us_btn" data-toggle="tooltip"
                                                    data-title="@lang('lang.contact_us')"
                                                    style="background-image: url('{{ asset('images/handshake.jpg') }}');"
                                                    class="btn no-print">
                                                </a>
                                            </li>
                                            <li class="nav-item"><button class="btn-danger btn-sm hide"
                                                    id="power_off_btn"><i class="fa fa-power-off"></i></button></li>
                                            <li class="nav-item"><a id="btnFullscreen" title="Full Screen"><i
                                                        class="dripicons-expand"></i></a></li>
                                            @include(
                                                'layouts.partials.notification_list'
                                            )
                                            @php
                                                $config_languages = config('constants.langs');
                                                $languages = [];
                                                foreach ($config_languages as $key => $value) {
                                                    $languages[$key] = $value['full_name'];
                                                }
                                            @endphp
                                            <li class="nav-item">
                                                <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    class="nav-link dropdown-item"><i class="dripicons-web"></i>
                                                    <span>{{ __('lang.language') }}</span> <i
                                                        class="fa fa-angle-down"></i></a>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @foreach ($languages as $key => $lang)
                                                        <li>
                                                            <a href="{{ action('GeneralController@switchLanguage', $key) }}"
                                                                class="btn btn-link">
                                                                {{ $lang }}</a>
                                                        </li>
                                                    @endforeach

                                                </ul>
                                            </li>
                                            {{-- <li class="nav-item">
                                            <a class="dropdown-item" href="{{action('HomeController@getHelp')}}"
                                                target="_blank"><i class="dripicons-information"></i>
                                                {{__('lang.help')}}</a>
                                        </li>&nbsp; --}}
                                            <li class="nav-item">
                                                <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    class="nav-link dropdown-item"><i class="dripicons-user"></i>
                                                    <span>{{ ucfirst(Auth::user()->name) }}</span> <i
                                                        class="fa fa-angle-down"></i>
                                                </a>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @php
                                                        $employee = App\Models\Employee::where('user_id', Auth::user()->id)->first();
                                                    @endphp
                                                    <li style="text-align: center">
                                                        <img src="@if (!empty($employee->getFirstMediaUrl('employee_photo'))) {{ $employee->getFirstMediaUrl('employee_photo') }}@else{{ asset('images/default.jpg') }} @endif"
                                                            style="width: 60px; border: 2px solid #fff; padding: 4px; border-radius: 50%;" />
                                                    </li>
                                                    <li>
                                                        <a href="{{ action('UserController@getProfile') }}"><i
                                                                class="dripicons-user"></i> @lang('lang.profile')</a>
                                                    </li>
                                                    @can('settings.general_settings.view')
                                                        <li>
                                                            <a href="{{ action('SettingController@getGeneralSetting') }}"><i
                                                                    class="dripicons-gear"></i> @lang('lang.settings')</a>
                                                        </li>
                                                    @endcan
                                                    <li>
                                                        <a
                                                            href="{{ url('my-transactions/' . date('Y') . '/' . date('m')) }}"><i
                                                                class="dripicons-swap"></i>
                                                            @lang('lang.my_transactions')</a>
                                                    </li>
                                                    @if (Auth::user()->role_id != 5)
                                                        <li>
                                                            <a
                                                                href="{{ url('my-holidays/' . date('Y') . '/' . date('m')) }}"><i
                                                                    class="dripicons-vibrate"></i>
                                                                @lang('lang.my_holidays')</a>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <a href="#" id="logout-btn"><i class="dripicons-power"></i>
                                                            @lang('lang.logout')
                                                        </a>
                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                        </nav>
                    </header>
                    @include('sale_pos.partials.right_side')
                </div>

                <!-- recent transaction modal -->
                <div id="recentTransaction" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                    class="modal fade text-left">

                    <div class="modal-dialog modal-xl" role="document" style="max-width: 65%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">@lang( 'lang.recent_transactions' )</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                                {!! Form::text('start_date', null, ['class' => 'form-control', 'id' => 'rt_start_date']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                                {!! Form::text('end_date', null, ['class' => 'form-control', 'id' => 'rt_end_date']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('rt_customer_id', __('lang.customer'), []) !!}
                                                {!! Form::select('rt_customer_id', $customers, false, ['class' => 'form-control selectpicker', 'id' => 'rt_customer_id', 'data-live-search' => 'true', 'placeholder' => __('lang.all')]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('rt_created_by', __('lang.cashier'), []) !!}
                                                {!! Form::select('rt_created_by', $cashiers, false, ['class' => 'form-control selectpicker', 'id' => 'rt_created_by', 'data-live-search' => 'true', 'placeholder' => __('lang.all')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @include(
                                        'sale_pos.partials.recent_transactions'
                                    )
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang(
                                    'lang.close')</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <!-- draft transaction modal -->
                <div id="draftTransaction" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                    class="modal fade text-left">

                    <div class="modal-dialog" role="document" style="width: 65%">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">@lang( 'lang.draft_transactions' )</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('draft_start_date', __('lang.start_date'), []) !!}
                                                {!! Form::text('start_date', null, ['class' => 'form-control', 'id' => 'draft_start_date']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('draft_end_date', __('lang.end_date'), []) !!}
                                                {!! Form::text('end_date', null, ['class' => 'form-control', 'id' => 'draft_end_date']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @include('sale_pos.partials.view_draft')
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang(
                                    'lang.close')</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <!-- onlineOrder transaction modal -->
                <div id="onlineOrderTransaction" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                    class="modal text-left">

                    <div class="modal-dialog" role="document" style="width: 65%">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">@lang( 'lang.online_order_transactions' )</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('online_order_start_date', __('lang.start_date'), []) !!}
                                                {!! Form::text('start_date', null, ['class' => 'form-control', 'id' => 'online_order_start_date']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('online_order_end_date', __('lang.end_date'), []) !!}
                                                {!! Form::text('end_date', null, ['class' => 'form-control', 'id' => 'online_order_end_date']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @include(
                                        'sale_pos.partials.view_online_order'
                                    )
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang(
                                    'lang.close')</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>

                <div id="dining_model" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                    class="modal text-left">
                </div>
                <div id="dining_table_action_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                    class="modal fade text-left">
                </div>

            </div>
        </div>


    </section>


    <!-- This will be printed -->
    <section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/onscan.min.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/dining_table.js') }}"></script>
    <script>
        $(document).ready(function() {
            @foreach ($transaction->transaction_sell_lines as $line)
                get_label_product_row({{ $line->product_id }}, {{ $line->variation_id }},
                    {{ $line->quantity }},
                    {{ $loop->index }})
            @endforeach
        })
        @if (!empty($transaction->dining_table))
            $('.table_room_hide').addClass('hide');
            $('.table_room_show').removeClass('hide');
        @endif
    </script>

@endsection
