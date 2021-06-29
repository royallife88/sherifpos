@extends('layouts.app')
@section('title', __('lang.pos'))

@section('content')
<section class="forms pos-section">
    <div class="container-fluid">
        <div class="row">
            <audio id="mysoundclip1" preload="auto">
                <source src="{{url('audio/beep-timber.mp3')}}">
                </source>
            </audio>
            <audio id="mysoundclip2" preload="auto">
                <source src="{{url('audio/beep-07.mp3')}}">
                </source>
            </audio>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0">
                        {!! Form::open(['url' => action('SellPosController@create'), 'method' => 'post', 'files' =>
                        true, 'class' => 'pos-form']) !!}
                        <input type="hidden" name="store_id" id="store_id" value="{{$store_pos->store_id}}">
                        {{-- @php
                            if($lims_pos_setting_data)
                                $keybord_active = $lims_pos_setting_data->keybord_active;
                            else
                                $keybord_active = 0;

                            $customer_active = DB::table('permissions')
                              ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                              ->where([
                                ['permissions.name', 'customers-add'],
                                ['role_id', \Auth::user()->role_id] ])->first();
                        @endphp --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                        <div class="input-group my-group">
                                            {!! Form::select('customer_id=', $customers,
                                            1, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                                            'style' =>'width: 80%' , 'id' => 'customer_id']) !!}
                                            <span class="input-group-btn">
                                                @can('customer_module.customer.create')
                                                <button class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{action('CustomerController@create')}}?quick_add=1"
                                                    data-container=".view_modal"><i
                                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                @endcan
                                            </span>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
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
                                                    <th style="width: 30%">{{__('lang.product')}}</th>
                                                    <th style="width: 20%">{{__('lang.quantity')}}</th>
                                                    <th style="width: 20%">{{__('lang.price')}}</th>
                                                    <th style="width: 10%">{{__('lang.sub_total')}}</th>
                                                    <th style="width: 20%"></th>
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
                                            <input type="hidden" name="total_qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_discount" value="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_tax" value="0.00" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="total_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="item" />
                                            <input type="hidden" name="order_tax" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="hidden" name="final_total" id="final_total" />
                                            <input type="hidden" name="coupon_discount" />
                                            <input type="hidden" name="sale_status" value="1" />
                                            <input type="hidden" name="coupon_active">
                                            <input type="hidden" name="coupon_id">
                                            <input type="hidden" name="coupon_discount" />

                                            <input type="hidden" name="pos" value="1" />
                                            <input type="hidden" name="draft" value="0" />
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
                                            <span class="totals-title">{{__('lang.discount')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#order-discount"> <i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="discount">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.coupon')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#coupon-modal"><i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="coupon-text">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.tax')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#order-tax"><i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="tax">0.00</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="totals-title">{{__('lang.shipping')}} <button type="button"
                                                    class="btn btn-link btn-sm" data-toggle="modal"
                                                    data-target="#shipping-cost-modal"><i
                                                        class="dripicons-document-edit"></i></button></span><span
                                                id="shipping-cost">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-amount">
                        <h2>{{__('lang.grand_total')}} <span class="final_total_span">0.00</span></h2>
                    </div>
                    <div class="payment-options">
                        <div class="column-5">
                            <button style="background: #0984e3" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="credit-card-btn"><i
                                    class="fa fa-credit-card"></i> Card</button>
                        </div>
                        <div class="column-5">
                            <button style="background: #00cec9" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="cash-btn"><i class="fa fa-money"></i>
                                Cash</button>
                        </div>
                        <div class="column-5">
                            <button style="background: #00cec9" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#coupon-modal" id="cash-btn"><i class="fa fa-tag"></i>
                                Coupon</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #213170" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="paypal-btn"><i
                                    class="fa fa-paypal"></i> Paypal</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #e28d02" type="button" class="btn btn-custom"
                                id="draft-btn"><i class="dripicons-flag"></i> Draft</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #fd7272" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="cheque-btn"><i
                                    class="fa fa-money"></i> Cheque</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #5f27cd" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="gift-card-btn"><i
                                    class="fa fa-credit-card-alt"></i> GiftCard</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #b33771" type="button" class="btn btn-custom payment-btn"
                                data-toggle="modal" data-target="#add-payment" id="deposit-btn"><i
                                    class="fa fa-university"></i> Deposit</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #d63031;" type="button" class="btn btn-custom"
                                id="cancel-btn" onclick="return confirmCancel()"><i class="fa fa-close"></i>
                                Cancel</button>
                        </div>
                        <div class="column-5">
                            <button style="background-color: #ffc107;" type="button" class="btn btn-custom"
                                data-toggle="modal" data-target="#recentTransaction"><i class="dripicons-clock"></i>
                                Recent transaction</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- payment modal -->
            <div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Finalize Sale')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6 mt-1">
                                            <label>{{__('lang.Recieved Amount')}} *</label>
                                            <input type="text" name="paying_amount" class="form-control numkey" required
                                                step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label>{{__('lang.Paying Amount')}} *</label>
                                            <input type="text" name="paid_amount" class="form-control numkey"
                                                step="any">
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <label>{{__('lang.Change')}} : </label>
                                            <p id="change" class="ml-2">0.00</p>
                                        </div>
                                        <div class="col-md-6 mt-1">
                                            <input type="hidden" name="paid_by_id">
                                            <label>{{__('lang.Paid By')}}</label>
                                            <select name="paid_by_id_select" class="form-control selectpicker">
                                                <option value="1">Cash</option>
                                                <option value="2">Gift Card</option>
                                                <option value="3">Credit Card</option>
                                                <option value="4">Cheque</option>
                                                <option value="5">Paypal</option>
                                                <option value="6">Deposit</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12 mt-3">
                                            <div class="card-element form-control">
                                            </div>
                                            <div class="card-errors" role="alert"></div>
                                        </div>
                                        <div class="form-group col-md-12 gift-card">
                                            <label> {{__('lang.Gift Card')}} *</label>
                                            <input type="hidden" name="gift_card_id">
                                            <select id="gift_card_id_select" name="gift_card_id_select"
                                                class="selectpicker form-control" data-live-search="true"
                                                data-live-search-style="begins" title="Select Gift Card..."></select>
                                        </div>
                                        <div class="form-group col-md-12 cheque">
                                            <label>{{__('lang.Cheque Number')}} *</label>
                                            <input type="text" name="cheque_no" class="form-control">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>{{__('lang.Payment Note')}}</label>
                                            <textarea id="payment_note" rows="2" class="form-control"
                                                name="payment_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>{{__('lang.Sale Note')}}</label>
                                            <textarea rows="3" class="form-control" name="sale_note"></textarea>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>{{__('lang.Staff Note')}}</label>
                                            <textarea rows="3" class="form-control" name="staff_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button id="submit-btn" type="button"
                                            class="btn btn-primary">{{__('lang.submit')}}</button>
                                    </div>
                                </div>
                                <div class="col-md-2 qc" data-initial="1">
                                    <h4><strong>{{__('lang.Quick Cash')}}</strong></h4>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="10"
                                        type="button">10</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="20"
                                        type="button">20</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="50"
                                        type="button">50</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="100"
                                        type="button">100</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="500"
                                        type="button">500</button>
                                    <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="1000"
                                        type="button">1000</button>
                                    <button class="btn btn-block btn-danger qc-btn sound-btn" data-amount="0"
                                        type="button">{{__('lang.Clear')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_discount modal -->
            <div id="order-discount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('lang.Order Discount')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="order_discount" class="form-control numkey">
                            </div>
                            <button type="button" name="order_discount_btn" class="btn btn-primary"
                                data-dismiss="modal">{{__('lang.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- coupon modal -->
            <div id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('lang.Coupon Code')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" id="coupon-code" class="form-control"
                                    placeholder="Type Coupon Code...">
                            </div>
                            <button type="button" class="btn btn-primary coupon-check"
                                data-dismiss="modal">{{__('lang.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- order_tax modal -->
            <div id="order-tax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('lang.Order Tax')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="order_tax_rate">
                                <select class="form-control" name="order_tax_rate_select">
                                    <option value="0">No Tax</option>
                                    {{-- @foreach($lims_tax_list as $tax)
                                    <option value="{{$tax->rate}}">{{$tax->name}}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <button type="button" name="order_tax_btn" class="btn btn-primary"
                                data-dismiss="modal">{{__('lang.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- shipping_cost modal -->
            <div id="shipping-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('lang.Shipping Cost')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="shipping_cost" class="form-control numkey" step="any">
                            </div>
                            <button type="button" name="shipping_cost_btn" class="btn btn-primary"
                                data-dismiss="modal">{{__('lang.submit')}}</button>
                        </div>
                    </div>
                </div>
            </div>

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
                                        <?php
                                // $general_setting_permission = DB::table('permissions')->where('name', 'general_setting')->first();
                                // $general_setting_permission_active = DB::table('role_has_permissions')->where([
                                //             ['permission_id', $general_setting_permission->id],
                                //             ['role_id', Auth::user()->role_id]
                                //         ])->first();

                                // $pos_setting_permission = DB::table('permissions')->where('name', 'pos_setting')->first();

                                // $pos_setting_permission_active = DB::table('role_has_permissions')->where([
                                //     ['permission_id', $pos_setting_permission->id],
                                //     ['role_id', Auth::user()->role_id]
                                // ])->first();
                            ?>
                                        {{-- @if($pos_setting_permission_active)
                            <li class="nav-item"><a class="dropdown-item" href="{{route('setting.pos')}}"
                                        title="{{__('lang.POS Setting')}}"><i class="dripicons-gear"></i></a> </li>
                                        @endif --}}
                                        <li class="nav-item">
                                            {{-- <a href="{{route('sales.printLastReciept')}}"
                                            title="{{__('lang.Print Last Reciept')}}"><i
                                                class="dripicons-print"></i></a> --}}
                                        </li>
                                        <li class="nav-item">
                                            {{-- <a href="" id="register-details-btn" title="{{__('lang.Cash Register Details')}}"><i
                                                class="dripicons-briefcase"></i></a> --}}
                                        </li>
                                        <?php
                                // $today_sale_permission = DB::table('permissions')->where('name', 'today_sale')->first();
                                // $today_sale_permission_active = DB::table('role_has_permissions')->where([
                                //             ['permission_id', $today_sale_permission->id],
                                //             ['role_id', Auth::user()->role_id]
                                //         ])->first();

                                // $today_profit_permission = DB::table('permissions')->where('name', 'today_profit')->first();
                                // $today_profit_permission_active = DB::table('role_has_permissions')->where([
                                //             ['permission_id', $today_profit_permission->id],
                                //             ['role_id', Auth::user()->role_id]
                                //         ])->first();
                            ?>

                                        {{-- @if($today_sale_permission_active)
                            <li class="nav-item">
                                <a href="" id="today-sale-btn" title="{{__('lang.Today Sale')}}"><i
                                            class="dripicons-shopping-bag"></i></a>
                                        </li>
                                        @endif
                                        @if($today_profit_permission_active)
                                        <li class="nav-item">
                                            <a href="" id="today-profit-btn" title="{{__('lang.Today Profit')}}"><i
                                                    class="dripicons-graph-line"></i></a>
                                        </li>
                                        @endif --}}
                                        {{-- @if(($alert_product + count(\Auth::user()->unreadNotifications)) > 0)
                            <li class="nav-item" id="notification-icon">
                                  <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-bell"></i><span class="badge badge-danger notification-number">{{$alert_product + count(\Auth::user()->unreadNotifications)}}</span>
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        </a>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications"
                                            user="menu">
                                            <li class="notifications">
                                                <a href="{{route('report.qtyAlert')}}"
                                                    class="btn btn-link">{{$alert_product}} product exceeds alert
                                                    quantity</a>
                                            </li>
                                            @foreach(\Auth::user()->unreadNotifications as $key => $notification)
                                            <li class="notifications">
                                                <a href="#"
                                                    class="btn btn-link">{{ $notification->data['message'] }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        </li>
                                        @endif --}}
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('read_me') }}" target="_blank"><i
                                                    class="dripicons-information"></i> {{__('lang.Help')}}</a>
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
                                                {{-- <li>
                                        <a href="{{route('user.profile', ['id' => Auth::id()])}}"><i
                                                    class="dripicons-user"></i> {{__('lang.profile')}}</a>
                                        </li>
                                        @if($general_setting_permission_active)
                                        <li>
                                            <a href="{{route('setting.general')}}"><i class="dripicons-gear"></i>
                                                {{__('lang.settings')}}</a>
                                        </li>
                                        @endif
                                        <li>
                                            <a href="{{url('my-transactions/'.date('Y').'/'.date('m'))}}"><i
                                                    class="dripicons-swap"></i> {{__('lang.My Transaction')}}</a>
                                        </li>
                                        <li>
                                            <a href="{{url('holidays/my-holiday/'.date('Y').'/'.date('m'))}}"><i
                                                    class="dripicons-vibrate"></i> {{__('lang.My Holiday')}}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();"><i
                                                    class="dripicons-power"></i>
                                                {{__('lang.logout')}}
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </li> --}}
                                    </ul>
                                    </li>
                                    </ul>
                                </div>
                            </div>
                    </nav>
                </header>
                <div class="filter-window">
                    <div class="category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">@lang('lang.choose_category')</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($categories as $category)
                            <div class="col-md-3 filter-by category-img text-center" data-id="{{$category->id}}"
                                data-type="category">
                                <img
                                    src="@if(!empty($category->getFirstMediaUrl('category'))){{$category->getFirstMediaUrl('category')}}@else{{asset('images/default.jpg')}}@endif" />
                                <p class="text-center">{{$category->name}}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="sub_category mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">@lang('lang.choose_sub_category')</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($sub_categories as $category)
                            <div class="col-md-3 filter-by category-img text-center" data-id="{{$category->id}}"
                                data-type="sub_category">
                                <img
                                    src="@if(!empty($category->getFirstMediaUrl('category'))){{$category->getFirstMediaUrl('category')}}@else{{asset('images/default.jpg')}}@endif" />
                                <p class="text-center">{{$category->name}}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="brand mt-3">
                        <div class="row ml-2 mr-2 px-2">
                            <div class="col-7">@lang('lang.choose_brand')</div>
                            <div class="col-5 text-right">
                                <span class="btn btn-default btn-sm">
                                    <i class="dripicons-cross"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row ml-2 mt-3">
                            @foreach($brands as $brand)

                            <div class="col-md-3 filter-by brand-img text-center" data-id="{{$brand->id}}"
                                data-type="brand">
                                <img
                                    src="@if(!empty($brand->getFirstMediaUrl('brand'))){{$brand->getFirstMediaUrl('brand')}}@else{{asset('images/default.jpg')}}@endif" />
                                <p class="text-center">{{$brand->name}}</p>
                            </div>

                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <button class="btn btn-block btn-primary" id="category-filter">{{__('lang.category')}}</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-primary"
                            id="sub-category-filter">{{__('lang.sub_category')}}</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-block btn-danger" id="brand-filter">{{__('lang.brand')}}</button>
                    </div>
                    <br>
                    <br>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="best_selling">
                            @lang('lang.best_selling')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="slow_moving_items">
                            @lang('lang.slow_moving_items')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="product_in_last_transactions">
                            @lang('lang.product_in_last_transactions')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="price_filter" value="highest_price">
                            @lang('lang.highest_price')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="price_filter" value="lowest_price"> @lang('lang.lowest_price')
                        </label>
                    </div>
                    <div class="col-md-2">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sorting_filter" value="a_to_z"> @lang('lang.a_to_z')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sorting_filter" value="z_to_a"> @lang('lang.z_to_a')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="expiry_filter" value="nearest_expiry">
                            @lang('lang.nearest_expiry')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="expiry_filter" value="longest_expiry">
                            @lang('lang.longest_expiry')
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sale_promo_filter" value="items_in_sale_promotion">
                            @lang('lang.items_in_sale_promotion')
                        </label>
                    </div>


                    <div class="col-md-12 mt-1 table-container">
                        <table id="filter-product-table" class="table no-shadow product-list">
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- product edit modal -->
            <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="modal_header" class="modal-title"></h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>{{__('lang.Quantity')}}</label>
                                    <input type="text" name="edit_qty" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label>{{__('lang.Unit Discount')}}</label>
                                    <input type="text" name="edit_discount" class="form-control numkey">
                                </div>
                                <div class="form-group">
                                    <label>{{__('lang.Unit Price')}}</label>
                                    <input type="text" name="edit_unit_price" class="form-control numkey" step="any">
                                </div>
                                <?php
                        // $tax_name_all[] = 'No Tax';
                        // $tax_rate_all[] = 0;
                        // foreach($lims_tax_list as $tax) {
                        //     $tax_name_all[] = $tax->name;
                        //     $tax_rate_all[] = $tax->rate;
                        // }
                    ?>
                                <div class="form-group">
                                    <label>{{__('lang.Tax Rate')}}</label>
                                    <select name="edit_tax_rate" class="form-control selectpicker">
                                        {{-- @foreach($tax_name_all as $key => $name)
                                            <option value="{{$key}}">{{$name}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div id="edit_unit" class="form-group">
                                    <label>{{__('lang.Product Unit')}}</label>
                                    <select name="edit_unit" class="form-control selectpicker">
                                    </select>
                                </div>
                                <button type="button" name="update_btn"
                                    class="btn btn-primary">{{__('lang.update')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- add customer modal -->
            <div id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Add Customer')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <p class="italic">
                                <small>{{__('lang.The field labels marked with * are required input fields')}}.</small>
                            </p>
                            <div class="form-group">
                                <label>{{__('lang.Customer Group')}} *</strong> </label>
                                <select required class="form-control selectpicker" name="customer_group_id">
                                    {{-- @foreach($lims_customer_group_all as $customer_group)
                                    <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{__('lang.name')}} *</strong> </label>
                                <input type="text" name="name" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{__('lang.Email')}}</label>
                                <input type="text" name="email" placeholder="example@example.com" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{__('lang.Phone Number')}} *</label>
                                <input type="text" name="phone_number" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{__('lang.Address')}} *</label>
                                <input type="text" name="address" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{__('lang.City')}} *</label>
                                <input type="text" name="city" required class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="pos" value="1">
                                <input type="submit" value="{{__('lang.submit')}}" class="btn btn-primary">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- recent transaction modal -->
            <div id="recentTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Recent Transaction')}} <div
                                    class="badge badge-primary">{{__('lang.latest')}} 10</div>
                            </h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#sale-latest" role="tab"
                                        data-toggle="tab">{{__('lang.Sale')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#draft-latest" role="tab"
                                        data-toggle="tab">{{__('lang.Draft')}}</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane show active" id="sale-latest">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{__('lang.date')}}</th>
                                                    <th>{{__('lang.reference')}}</th>
                                                    <th>{{__('lang.customer')}}</th>
                                                    <th>{{__('lang.grand total')}}</th>
                                                    <th>{{__('lang.action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach($recent_sale as $sale)
                                    <?php $customer = DB::table('customers')->find($sale->customer_id); ?>
                                    <tr>
                                      <td>{{date('d-m-Y', strtotime($sale->created_at))}}</td>
                                                <td>{{$sale->reference_no}}</td>
                                                <td>{{$customer->name}}</td>
                                                <td>{{$sale->grand_total}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if(in_array("sales-edit", $all_permission))
                                                        <a href="{{ route('sales.edit', $sale->id) }}"
                                                            class="btn btn-success btn-sm" title="Edit"><i
                                                                class="dripicons-document-edit"></i></a>&nbsp;
                                                        @endif
                                                        @if(in_array("sales-delete", $all_permission))
                                                        {{ Form::open(['route' => ['sales.destroy', $sale->id], 'method' => 'DELETE'] ) }}
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirmDelete()" title="Delete"><i
                                                                class="dripicons-trash"></i></button>
                                                        {{ Form::close() }}
                                                        @endif
                                                    </div>
                                                </td>
                                                </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="draft-latest">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{__('lang.date')}}</th>
                                                    <th>{{__('lang.reference')}}</th>
                                                    <th>{{__('lang.customer')}}</th>
                                                    <th>{{__('lang.grand total')}}</th>
                                                    <th>{{__('lang.action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach($recent_draft as $draft)
                                    <?php $customer = DB::table('customers')->find($draft->customer_id); ?>
                                    <tr>
                                      <td>{{date('d-m-Y', strtotime($draft->created_at))}}</td>
                                                <td>{{$draft->reference_no}}</td>
                                                <td>{{$customer->name}}</td>
                                                <td>{{$draft->grand_total}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if(in_array("sales-edit", $all_permission))
                                                        <a href="{{url('sales/'.$draft->id.'/create') }}"
                                                            class="btn btn-success btn-sm" title="Edit"><i
                                                                class="dripicons-document-edit"></i></a>&nbsp;
                                                        @endif
                                                        @if(in_array("sales-delete", $all_permission))
                                                        {{ Form::open(['route' => ['sales.destroy', $draft->id], 'method' => 'DELETE'] ) }}
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirmDelete()" title="Delete"><i
                                                                class="dripicons-trash"></i></button>
                                                        {{ Form::close() }}
                                                        @endif
                                                    </div>
                                                </td>
                                                </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- add cash register modal -->
            <div id="cash-register-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        {{-- {!! Form::open(['route' => 'cashRegister.store', 'method' => 'post']) !!} --}}
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Add Cash Register')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <p class="italic">
                                <small>{{__('lang.The field labels marked with * are required input fields')}}.</small>
                            </p>
                            <div class="row">
                                <div class="col-md-6 form-group warehouse-section">
                                    <label>{{__('lang.Warehouse')}} *</strong> </label>
                                    <select required name="warehouse_id" class="selectpicker form-control"
                                        data-live-search="true" data-live-search-style="begins"
                                        title="Select warehouse...">
                                        {{-- @foreach($lims_warehouse_list as $warehouse)
                                  <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{__('lang.Cash in Hand')}} *</strong> </label>
                                    <input type="number" name="cash_in_hand" required class="form-control">
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="submit" class="btn btn-primary">{{__('lang.submit')}}</button>
                                </div>
                            </div>
                        </div>
                        {{-- {{ Form::close() }} --}}
                    </div>
                </div>
            </div>
            <!-- cash register details modal -->
            <div id="register-details-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Cash Register Details')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <p>{{__('lang.Please review the transaction and payments.')}}</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <td>{{__('lang.Cash in Hand')}}:</td>
                                                <td id="cash_in_hand" class="text-right">0</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Sale Amount')}}:</td>
                                                <td id="total_sale_amount" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Payment')}}:</td>
                                                <td id="total_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Cash Payment')}}:</td>
                                                <td id="cash_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Credit Card Payment')}}:</td>
                                                <td id="credit_card_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Cheque Payment')}}:</td>
                                                <td id="cheque_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Gift Card Payment')}}:</td>
                                                <td id="gift_card_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Paypal Payment')}}:</td>
                                                <td id="paypal_payment" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Sale Return')}}:</td>
                                                <td id="total_sale_return" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Expense')}}:</td>
                                                <td id="total_expense" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{__('lang.Total Cash')}}:</strong></td>
                                                <td id="total_cash" class="text-right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6" id="closing-section">
                                    {{-- <form action="{{route('cashRegister.close')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="cash_register_id">
                                    <button type="submit" class="btn btn-primary">{{__('lang.Close Register')}}</button>
                                    </form> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- today sale modal -->
            <div id="today-sale-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Today Sale')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <p>{{__('lang.Please review the transaction and payments.')}}</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <td>{{__('lang.Total Sale Amount')}}:</td>
                                                <td class="total_sale_amount text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Cash Payment')}}:</td>
                                                <td class="cash_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Credit Card Payment')}}:</td>
                                                <td class="credit_card_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Cheque Payment')}}:</td>
                                                <td class="cheque_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Gift Card Payment')}}:</td>
                                                <td class="gift_card_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Paypal Payment')}}:</td>
                                                <td class="paypal_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Payment')}}:</td>
                                                <td class="total_payment text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Sale Return')}}:</td>
                                                <td class="total_sale_return text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Total Expense')}}:</td>
                                                <td class="total_expense text-right"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{__('lang.Total Cash')}}:</strong></td>
                                                <td class="total_cash text-right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- today profit modal -->
            <div id="today-profit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true" class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="exampleModalLabel" class="modal-title">{{__('lang.Today Profit')}}</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <select required name="warehouseId" class="form-control">
                                        <option value="0">{{__('lang.All Warehouse')}}</option>
                                        {{-- @foreach($lims_warehouse_list as $warehouse)
                                    <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <td>{{__('lang.Product Revenue')}}:</td>
                                                <td class="product_revenue text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Product Cost')}}:</td>
                                                <td class="product_cost text-right"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('lang.Expense')}}:</td>
                                                <td class="expense_amount text-right"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{__('lang.Profit')}}:</strong></td>
                                                <td class="profit text-right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script src="{{asset('js/pos.js')}}"></script>
@endsection
