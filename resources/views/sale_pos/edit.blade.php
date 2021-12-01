@extends('layouts.app')
@section('title', __('lang.pos'))

@section('content')
<section class="forms pos-section no-print">
    <div class="container-fluid">
        <div class="row">
            <audio id="mysoundclip1" preload="auto">
                <source src="{{asset('audio/beep-timber.mp3')}}">
                </source>
            </audio>
            <audio id="mysoundclip2" preload="auto">
                <source src="{{asset('audio/beep-07.mp3')}}">
                </source>
            </audio>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0">
                        {!! Form::open(['url' => action('SellPosController@update', $transaction->id), 'method' => 'put', 'files' =>
                        true, 'class' => 'pos-form', 'id' => 'edit_pos_form']) !!}
                        <input type="hidden" name="store_id" id="store_id" value="{{$store_pos->store_id}}">
                        <input type="hidden" name="default_customer_id" id="default_customer_id"
                            value="@if(!empty($walk_in_customer)){{$walk_in_customer->id}}@endif">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('customer_id', $customers,
                                            !empty($transaction) ? $transaction->customer_id : null, ['class' =>
                                            'selectpicker form-control', 'data-live-search'=>"true",
                                            'style' =>'width: 80%' , 'id' => 'customer_id']) !!}
                                            <span class="input-group-btn">
                                                @can('customer_module.customer.create_and_edit')
                                                <button class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{action('CustomerController@create')}}?quick_add=1"
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
                                    <div class="col-md-4">
                                        <label for="" style="margin-top: 40px;">@lang('lang.customer_type'): <span class="customer_type_name"></span></label>
                                    </div>
                                    <div class="col-md-12" style="margin-top: 10px;">
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
                                <div class="col-md-12" style="margin-top: 20px ">
                                    <div class="table-responsive transaction-list">
                                        <table id="product_table" style="width: 100% "
                                            class="table table-hover table-striped order-list table-fixed">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%; font-size: 12px !important;">@lang('lang.product')</th>
                                                    <th style="width: 22%; font-size: 12px !important;">@lang('lang.quantity')</th>
                                                    <th style="width: 15%; font-size: 12px !important;">@lang('lang.price')</th>
                                                    <th style="width: 13%; font-size: 12px !important;">@lang('lang.discount')</th>
                                                    <th style="width: 15%; font-size: 12px !important;">@lang('lang.sub_total')</th>
                                                    <th style="width: 10%; font-size: 12px !important;">@lang('lang.action')</th>
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
                                            <input type="hidden" id="total_tax" name="total_tax" value="0.00">

                                            <input type="hidden" id="store_pos_id" name="store_pos_id"
                                                value="{{$store_pos->id}}" />
                                            <input type="hidden" id="status" name="status" value="@if(!empty(request()->status)){{request()->status}}@else{{'final'}}@endif" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 totals" style="border-top: 2px solid #e4e6fc; padding-top: 10px;">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.items')}}</span><span
                                                id="item">0</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.total')}}</span><span
                                                id="subtotal">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.random_discount')}} <button
                                                    type="button" class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#discount_modal"> <i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="discount">0.00</span>
                                        </div>

                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.tax')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#tax_modal"><i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="tax">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.delivery')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#delivery-cost-modal"><i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="delivery-cost">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-amount">
                        <h2>{{__('lang.grand_total')}} <span class="final_total_span">0.00</span></h2>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                    <select name="terms_and_condition_id" id="terms_and_condition_id"
                                        class="form-control selectpicker" data-live-searcg="true">
                                        <option value="">@lang('lang.please_select')</option>
                                        @foreach ($tac as $key => $item)
                                        <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tac_description_div"><span></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-options">
                        <div class="column-5">
                            <button data-method="card" style="background: #0984e3" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="credit-card-btn"><i class="fa fa-credit-card"></i> @lang('lang.card')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="cash" style="background: #00cec9" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="cash-btn"><i class="fa fa-money"></i>
                                @lang('lang.cash')</button>
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
                                data-href="{{action('SellPosController@getDraftTransactions')}}"><i
                                    class="dripicons-flag"></i>
                                @lang('lang.view_draft')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="cheque" style="background-color: #fd7272" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="cheque-btn"><i class="fa fa-money"></i> @lang('lang.cheque')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="bank_transfer" style="background-color: #56962b" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="bank-transfer-btn"><i class="fa fa-building-o"></i>
                                @lang('lang.bank_transfer')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="pay-later" style="background-color: #cf2929" type="button"
                                class="btn btn-custom" id="pay-later-btn"><i class="fa fa-hourglass-start"></i>
                                @lang('lang.pay_later')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="gift_card" style="background-color: #5f27cd" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="gift-card-btn"><i class="fa fa-credit-card-alt"></i>
                                @lang('lang.gift_card')</button>
                        </div>
                        <div class="column-5">
                            <button data-method="deposit" style="background-color: #b33771" type="button"
                                class="btn btn-custom payment-btn" data-toggle="modal" data-target="#add-payment"
                                id="deposit-btn"><i class="fa fa-university"></i> @lang('lang.deposit')</button>
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
                                data-href="{{action('SellPosController@getRecentTransactions')}}"><i
                                    class="dripicons-clock"></i>
                                @lang('lang.recent_transactions')</button>
                        </div>
                    </div>
                </div>
            </div>

            @include('sale_pos.partials.payment_modal')
            @include('sale_pos.partials.discount_modal')
            @include('sale_pos.partials.tax_modal')
            @include('sale_pos.partials.delivery_cost_modal')
            @include('sale_pos.partials.coupon_modal')
            @include('sale_pos.partials.contact_details_modal')



            {!! Form::close() !!}
            <!-- product list -->
            <div class="col-md-6">
                <!-- navbar-->
                <header class="header">
                    <nav class="navbar">
                        <div class="container-fluid">
                            <div class="navbar-holder d-flex align-items-center justify-content-between">
                                <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>
                                <div class="navbar-header">

                                    <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                                        <li class="nav-item"><a id="btnFullscreen" title="Full Screen"><i
                                                    class="dripicons-expand"></i></a></li>
                                        @include('layouts.partials.notification_list')
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{action('HomeController@getHelp')}}"
                                                target="_blank"><i class="dripicons-information"></i>
                                                {{__('lang.help')}}</a>
                                        </li>&nbsp;
                                        <li class="nav-item">
                                            <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                class="nav-link dropdown-item"><i class="dripicons-user"></i>
                                                <span>{{ucfirst(Auth::user()->name)}}</span> <i
                                                    class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                user="menu">
                                                @php
                                                $employee = App\Models\Employee::where('user_id',
                                                Auth::user()->id)->first();
                                                @endphp
                                                <li style="text-align: center">
                                                    <img src="@if(!empty($employee->getFirstMediaUrl('employee_photo'))){{$employee->getFirstMediaUrl('employee_photo')}}@else{{asset('images/default.jpg')}}@endif"
                                                        style="width: 60px; border: 2px solid #fff; padding: 4px; border-radius: 50%;" />
                                                </li>
                                                <li>
                                                    <a href="{{action('UserController@getProfile')}}"><i
                                                            class="dripicons-user"></i> @lang('lang.profile')</a>
                                                </li>
                                                @can('settings.general_settings.view')
                                                <li>
                                                    <a href="{{action('SettingController@getGeneralSetting')}}"><i
                                                            class="dripicons-gear"></i> @lang('lang.settings')</a>
                                                </li>
                                                @endcan
                                                <li>
                                                    <a href="{{url('my-transactions/'.date('Y').'/'.date('m'))}}"><i
                                                            class="dripicons-swap"></i>
                                                        @lang('lang.my_transactions')</a>
                                                </li>
                                                @if(Auth::user()->role_id != 5)
                                                <li>
                                                    <a href="{{url('my-holidays/'.date('Y').'/'.date('m'))}}"><i
                                                            class="dripicons-vibrate"></i> @lang('lang.my_holidays')</a>
                                                </li>
                                                @endif

                                                <li>
                                                    <a href="#" id="logout-btn"><i class="dripicons-power"></i>
                                                        @lang('lang.logout')
                                                    </a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        style="display: none;">
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
            <div id="recentTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">

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
                                            {!! Form::date('start_date', null, ['class' => 'form-control', 'id' =>
                                            'rt_start_date']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                            {!! Form::date('end_date', null, ['class' => 'form-control', 'id' =>
                                            'rt_end_date']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('rt_customer_id', __('lang.customer'), []) !!}
                                            {!! Form::select('rt_customer_id', $customers, false, ['class' =>
                                            'form-control selectpicker', 'id' =>
                                            'rt_customer_id', 'data-live-search' => 'true', 'placeholder' =>
                                            __('lang.all')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent_transaction_div col-md-12">
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
            <div id="draftTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">

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
                                            {!! Form::date('draft_start_date', null, ['class' => 'form-control', 'id' =>
                                            'draft_start_date']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('draft_end_date', __('lang.end_date'), []) !!}
                                            {!! Form::date('draft_end_date', null, ['class' => 'form-control', 'id' =>
                                            'draft_end_date']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="draft_transaction_div col-md-12">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close'
                                )</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>

        </div>
    </div>


</section>


<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script src="{{asset('js/pos.js')}}"></script>
<script>
    $(document).ready(function(){
        @foreach($transaction->transaction_sell_lines as $line)
        get_label_product_row({{$line->product_id}}, {{$line->variation_id}}, {{$line->quantity}}, {{$loop->index}})
        @endforeach
    })
</script>
@endsection