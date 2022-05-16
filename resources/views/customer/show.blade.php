@extends('layouts.app')
@section('title', __('lang.customer_details'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.customer_details')</h4>
            </div>
            <form action="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                            <a href="{{ action('CustomerController@show', $customer->id) }}"
                                class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body">
                <div class="col-md-12">
                    <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if (empty(request()->show)) active @endif" href="#info-sale" role="tab"
                                data-toggle="tab">@lang('lang.info')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'purchases') active @endif" href="#purchases" role="tab"
                                data-toggle="tab">@lang('lang.purchases')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'sell_return') active @endif" href="#sell_return"
                                role="tab" data-toggle="tab">@lang('lang.sell_return')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'discounts') active @endif" href="#store-discount"
                                role="tab" data-toggle="tab">@lang('lang.discounts')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'points') active @endif" href="#store-point"
                                role="tab" data-toggle="tab">@lang('lang.points')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->show == 'rewards') active @endif" href="#store-rewards"
                                role="tab" data-toggle="tab">@lang('lang.rewards')</a>
                        </li>
                        @if (session('system_mode') == 'garments')
                            @can('customer_module.customer_sizes.view')
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->show == 'sizes') active @endif" href="#store-sizes"
                                        role="tab" data-toggle="tab">@lang('lang.sizes')</a>
                                </li>
                            @endcan
                        @endif

                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade @if (empty(request()->show)) show active @endif"
                            id="info-sale">
                            <br>
                            @if ($balance < 0)
                                <div class="col-md-12">
                                    <button data-href="{{ action('CustomerController@getPayContactDue', $customer->id) }}"
                                        class="btn btn-primary btn-modal"
                                        data-container=".view_modal">@lang('lang.pay')</button>
                                </div>
                            @endif
                            <br>
                            <div class="col-md-12 text-muted">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12 ">
                                            <b>@lang('lang.name'):</b> <span
                                                class="customer_name_span">{{ $customer->name }}</span>
                                        </div>

                                        <div class="col-md-12">
                                            <b>@lang('lang.customer_type'):</b> <span
                                                class="customer_customer_type_span">{{ $customer->customer_type->name }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.mobile'):</b> <span
                                                class="customer_mobile_span">{{ $customer->mobile_number }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.address'):</b> <span
                                                class="customer_address_span">{{ $customer->address }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.email'):</b> <span
                                                class="customer_email_span">{{ $customer->email }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.balance'):</b> <span
                                                class="balance @if ($balance < 0) text-red @endif">{{ $balance }}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <b>@lang('lang.referred_by'):</b> {{ $referred_by }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="thumbnail">
                                            <img style="width: 200px; height: 200px;" class="img-fluid"
                                                src="@if (!empty($customer->getFirstMediaUrl('customer_photo'))) {{ $customer->getFirstMediaUrl('customer_photo') }} @endif"
                                                alt="Customer photo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'purchases') show active @endif"
                            id="purchases">
                            <div class="table-responsive">
                                <table class="table" id="sales_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th>@lang('lang.customer')</th>
                                            <th>@lang('lang.product')</th>
                                            <th class="currencies">@lang('lang.received_currency')</th>
                                            <th class="sum">@lang('lang.discount')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th class="sum">@lang('lang.paid')</th>
                                            <th class="sum">@lang('lang.due')</th>
                                            <th>@lang('lang.payment_date')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.points_earned')</th>
                                            <th>@lang('lang.cashier')</th>
                                            <th>@lang('lang.files')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <th class="table_totals" style="text-align: right">@lang('lang.total')</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'sell_return') show active @endif"
                            id="sell_return">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th>@lang('lang.customer')</th>
                                            <th class="sum">@lang('lang.discount')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th class="sum">@lang('lang.paid')</th>
                                            <th class="sum">@lang('lang.due')</th>
                                            <th>@lang('lang.payment_date')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.cashier')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_purchase_payments = 0;
                                            $total_purchase_due = 0;
                                        @endphp
                                        @foreach ($sale_returns as $return)
                                            <tr>
                                                <td>{{ @format_date($return->transaction_date) }}</td>
                                                <td>{{ $return->invoice_no }}</td>
                                                <td>
                                                    @if (!empty($return->customer))
                                                        {{ $return->customer->name }}
                                                    @endif
                                                </td>

                                                <td>{{ @num_format($return->discount_amount) }}</td>
                                                <td>{{ @num_format($return->final_total) }}</td>
                                                <td>{{ @num_format($return->transaction_payments->sum('amount')) }}</td>
                                                <td>{{ @num_format($return->final_total - $return->transaction_payments->sum('amount')) }}
                                                </td>
                                                <td>
                                                    @if ($return->transaction_payments->count() > 0)
                                                        {{ @format_date($return->transaction_payments->last()->paid_on) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($return->status == 'final')
                                                        <span class="badge badge-success">@lang('lang.completed')</span>
                                                    @else
                                                        {{ ucfirst($return->status) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($return->transaction_payments->count() > 0)
                                                        {{ $return->transaction_payments->last()->created_by_user->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">@lang('lang.action')
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                            user="menu">
                                                            @can('sale.pos.view')
                                                                <li>

                                                                    <a data-href="{{ action('SellReturnController@show', $return->return_parent_id) }}"
                                                                        data-container=".view_modal" class="btn btn-modal"><i
                                                                            class="fa fa-eye"></i>
                                                                        @lang('lang.view')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan
                                                            @can('sale.pos.create_and_edit')
                                                                <li>
                                                                    <a href="{{ action('SellReturnController@add', $return->return_parent_id) }}"
                                                                        class="btn"><i
                                                                            class="dripicons-document-edit"></i>
                                                                        @lang('lang.edit')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan

                                                            @can('sale.pos.delete')
                                                                <li>
                                                                    <a data-href="{{ action('SellController@destroy', $return->id) }}"
                                                                        data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                        class="btn text-red delete_item"><i
                                                                            class="fa fa-trash"></i>
                                                                        @lang('lang.delete')</a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                            </tr>
                                            @php
                                                $total_purchase_payments += $return->transaction_payments->sum('amount');
                                                $total_purchase_due += $return->final_total - $return->transaction_payments->sum('amount');
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <th style="text-align: right">@lang('lang.total')</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'discounts') show active @endif"
                            id="store-discount">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th>@lang('lang.customer')</th>
                                            <th>@lang('lang.product')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_discount_payments = 0;
                                            $total_discount_due = 0;
                                        @endphp
                                        @foreach ($discounts as $discount)
                                            <tr>
                                                <td>{{ @format_date($discount->transaction_date) }}</td>
                                                <td>{{ $discount->invoice_no }}</td>
                                                <td>
                                                    @if (!empty($discount->customer))
                                                        {{ $discount->customer->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($discount->transaction_sell_lines as $line)
                                                        ({{ @num_format($line->quantity) }})
                                                        @if (!empty($line->product))
                                                            {{ $line->product->name }}
                                                        @endif <br>
                                                    @endforeach
                                                </td>
                                                <td>{{ @num_format($discount->final_total) }}</td>
                                                </td>
                                                <td>
                                                    @if ($discount->status == 'final')
                                                        <span class="badge badge-success">@lang('lang.completed')</span>
                                                    @else
                                                        {{ ucfirst($discount->status) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">@lang('lang.action')
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                            user="menu">
                                                            @can('sale.pos.view')
                                                                <li>
                                                                    <a data-href="{{ action('SellController@show', $discount->id) }}"
                                                                        data-container=".view_modal" class="btn btn-modal"><i
                                                                            class="fa fa-eye"></i> @lang('lang.view')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan
                                                            @can('sale.pos.create_and_edit')
                                                                <li>
                                                                    <a href="{{ action('SellController@edit', $discount->id) }}"
                                                                        class="btn"><i
                                                                            class="dripicons-document-edit"></i>
                                                                        @lang('lang.edit')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan

                                                            @can('sale.pos.delete')
                                                                <li>
                                                                    <a data-href="{{ action('SellController@destroy', $discount->id) }}"
                                                                        data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                        class="btn text-red delete_item"><i
                                                                            class="fa fa-trash"></i>
                                                                        @lang('lang.delete')</a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                            </tr>
                                            @php
                                                $total_discount_payments += $discount->transaction_payments->sum('amount');
                                                $total_discount_due += $discount->final_total - $discount->transaction_payments->sum('amount');
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <th style="text-align: right">@lang('lang.total')</th>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'points') show active @endif"
                            id="store-point">
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th>@lang('lang.customer')</th>
                                            <th>@lang('lang.product')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.points_earned')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_point_payments = 0;
                                            $total_point_due = 0;
                                        @endphp
                                        @foreach ($points as $point)
                                            <tr>
                                                <td>{{ @format_date($point->transaction_date) }}</td>
                                                <td>{{ $point->invoice_no }}</td>
                                                <td>
                                                    @if (!empty($point->customer))
                                                        {{ $point->customer->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($point->transaction_sell_lines as $line)
                                                        ({{ @num_format($line->quantity) }})
                                                        @if (!empty($line->product))
                                                            {{ $line->product->name }}
                                                        @endif <br>
                                                    @endforeach
                                                </td>
                                                <td>{{ @num_format($point->final_total) }}</td>
                                                </td>
                                                <td>
                                                    @if ($point->status == 'final')
                                                        <span class="badge badge-success">@lang('lang.completed')</span>
                                                    @else
                                                        {{ ucfirst($point->status) }}
                                                    @endif
                                                </td>
                                                <td>{{ @num_format($point->rp_earned) }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-default btn-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">@lang('lang.action')
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                            user="menu">
                                                            @can('sale.pos.view')
                                                                <li>
                                                                    <a data-href="{{ action('SellController@show', $point->id) }}"
                                                                        data-container=".view_modal" class="btn btn-modal"><i
                                                                            class="fa fa-eye"></i> @lang('lang.view')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan
                                                            @can('sale.pos.create_and_edit')
                                                                <li>
                                                                    <a href="{{ action('SellController@edit', $point->id) }}"
                                                                        class="btn"><i
                                                                            class="dripicons-document-edit"></i>
                                                                        @lang('lang.edit')</a>
                                                                </li>
                                                                <li class="divider"></li>
                                                            @endcan

                                                            @can('sale.pos.delete')
                                                                <li>
                                                                    <a data-href="{{ action('SellController@destroy', $point->id) }}"
                                                                        data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                        class="btn text-red delete_item"><i
                                                                            class="fa fa-trash"></i>
                                                                        @lang('lang.delete')</a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                            </tr>
                                            @php
                                                $total_point_payments += $point->transaction_payments->sum('amount');
                                                $total_point_due += $point->final_total - $point->transaction_payments->sum('amount');
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: right">@lang('lang.total')</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade @if (request()->show == 'rewards') show active @endif"
                            id="store-rewards">
                            <div class="table-responsive">
                                <table class="table" id="rewards_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.type')</th>
                                            <th>@lang('lang.amount')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            {{-- <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: right">@lang('lang.total')</th>
                                            <th></th> --}}
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @if (session('system_mode') == 'garments')
                            <div role="tabpanel"
                                class="tab-pane fade @if (request()->show == 'sizes') show active @endif"
                                id="store-sizes">
                                @can('customer_module.customer_sizes.create_and_edit')
                                    <div class="col-md-12 mt-4 ml-2">
                                        <a data-href="{{ action('CustomerSizeController@add', $customer->id) }}"
                                            class="btn-modal btn btn-primary" style="color: #fff;"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus"></i>@lang('lang.add_size')</a>
                                    </div>
                                @endcan
                                <div class="table-responsive">
                                    <table class="table dataTable">
                                        <thead>
                                            <tr>
                                                <th>@lang('lang.name')</th>
                                                <th>@lang('lang.created_by')</th>
                                                <th>@lang('lang.date_and_time')</th>
                                                <th class="notexport">@lang('lang.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($customer_sizes as $customer_size)
                                                <tr>
                                                    <td>{{ $customer_size->name }}</td>
                                                    <td>{{ $customer_size->created_by_user ? $customer_size->created_by_user->name : '' }}
                                                    </td>
                                                    <td>{{ @format_datetime($customer_size->created_at) }}</td>

                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-default btn-sm dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">@lang('lang.action')
                                                                <span class="caret"></span>
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                                user="menu">
                                                                @can('customer_module.customer_sizes.view')
                                                                    <li>

                                                                        <a data-href="{{ action('CustomerSizeController@print', $customer_size->id) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn print-btn"><i
                                                                                class="dripicons-print"></i>
                                                                            @lang('lang.print')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('customer_module.customer_sizes.view')
                                                                    <li>

                                                                        <a data-href="{{ action('CustomerSizeController@show', $customer_size->id) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="fa fa-eye"></i>
                                                                            @lang('lang.view')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('customer_module.customer_sizes.create_and_edit')
                                                                    <li>

                                                                        <a data-href="{{ action('CustomerSizeController@edit', $customer_size->id) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="dripicons-document-edit"></i>
                                                                            @lang('lang.edit')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('customer_module.customer_sizes.delete')
                                                                    <li>
                                                                        <a data-href="{{ action('CustomerSizeController@destroy', $customer_size->id) }}"
                                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                            class="btn text-red delete_item"><i
                                                                                class="fa fa-trash"></i>
                                                                            @lang('lang.delete')</a>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="invoice print_section print-only" id="print_section"> </section>
    <section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
    <script>
        $(document).on('click', '.print-invoice', function() {
            $(".modal").modal("hide");
            $.ajax({
                method: "get",
                url: $(this).data("href"),
                data: {},
                success: function(result) {
                    if (result.success) {
                        pos_print(result.html_content);
                    }
                },
            });
        })

        function pos_print(receipt) {
            $("#receipt_section").html(receipt);
            __currency_convert_recursively($("#receipt_section"));
            __print_receipt("receipt_section");
        }

        $(document).ready(function() {
            sales_table = $("#sales_table").DataTable({
                lengthChange: true,
                paging: true,
                info: false,
                bAutoWidth: false,
                // order: [],
                language: {
                    url: dt_lang_url,
                },
                lengthMenu: [
                    [10, 25, 50, 75, 100, 200, 500, -1],
                    [10, 25, 50, 75, 100, 200, 500, "All"],
                ],
                dom: "lBfrtip",
                stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, "desc"]
                ],
                initComplete: function() {
                    $(this.api().table().container()).find('input').parent().wrap('<form>').parent()
                        .attr('autocomplete', 'off');
                },
                ajax: {
                    url: "/customer/{{ $customer->id }}",
                    data: function(d) {},
                },
                columnDefs: [{
                    targets: "date",
                    type: "date-eu",
                }],
                columns: [{
                        data: "transaction_date",
                        name: "transaction_date"
                    },
                    {
                        data: "invoice_no",
                        name: "invoice_no"
                    },
                    {
                        data: "customer_name",
                        name: "customers.name"
                    },
                    {
                        data: "products",
                        name: "products.name"
                    },
                    {
                        data: "received_currency_symbol",
                        name: "received_currency_symbol",
                        searchable: false
                    },
                    {
                        data: "discount_amount",
                        name: "discount_amount"
                    },
                    {
                        data: "final_total",
                        name: "final_total"
                    },
                    {
                        data: "paid",
                        name: "transaction_payments.amount",
                        searchable: false
                    },
                    {
                        data: "due",
                        name: "transaction_payments.amount",
                        searchable: false
                    },
                    {
                        data: "paid_on",
                        name: "transaction_payments.paid_on"
                    },
                    {
                        data: "status",
                        name: "transactions.status"
                    },
                    {
                        data: "rp_earned",
                        name: "rp_earned"
                    },

                    {
                        data: "created_by",
                        name: "users.name"
                    },
                    {
                        data: "files",
                        name: "files"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                createdRow: function(row, data, dataIndex) {},
                footerCallback: function(row, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "") * 1 :
                            typeof i === "number" ?
                            i :
                            0;
                    };

                    this.api()
                        .columns(".currencies", {
                            page: "current"
                        }).every(function() {
                            var column = this;
                            let currencies_html = '';
                            $.each(currency_obj, function(key, value) {
                                currencies_html +=
                                    `<h6 class="footer_currency" data-is_default="${value.is_default}"  data-currency_id="${value.currency_id}">${value.symbol}</h6>`
                                $(column.footer()).html(currencies_html);
                            });
                        })
                    this.api()
                        .columns(".sum", {
                            page: "current"
                        })
                        .every(function() {
                            var column = this;
                            var currency_total = [];
                            $.each(currency_obj, function(key, value) {
                                currency_total[value.currency_id] = 0;
                            });
                            column.data().each(function(group, i) {
                                b = $(group).text();
                                currency_id = $(group).data('currency_id');

                                $.each(currency_obj, function(key, value) {
                                    if (currency_id == value.currency_id) {
                                        currency_total[value.currency_id] += intVal(
                                            b);
                                    }
                                });
                            });
                            var footer_html = '';
                            $.each(currency_obj, function(key, value) {
                                footer_html +=
                                    `<h6 class="currency_total currency_total_${value.currency_id}" data-currency_id="${value.currency_id}" data-is_default="${value.is_default}" data-conversion_rate="${value.conversion_rate}" data-base_conversion="${currency_total[value.currency_id] * value.conversion_rate}" data-orig_value="${currency_total[value.currency_id]}">${__currency_trans_from_en(currency_total[value.currency_id], false)}</h6>`
                            });
                            $(column.footer()).html(
                                footer_html
                            );
                        });
                },
            });
        });

        $(document).ready(function() {
            rewards_table = $("#rewards_table").DataTable({
                lengthChange: true,
                paging: true,
                searching: true,
                info: false,
                bAutoWidth: false,
                language: {
                    url: dt_lang_url,
                },
                dom: "lBfrtip",
                stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                ordering: true,
                aaSorting: [
                    // [0, "desc"]
                ],
                initComplete: function() {
                    $(this.api().table().container()).find('input').parent().wrap('<form>').parent()
                        .attr('autocomplete', 'off');
                },
                ajax: {
                    url: "/reward-system/get-details-by-customer/{{ $customer->id }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                },
                columns: [{
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "type",
                        name: "type"
                    },
                    {
                        data: "value",
                        name: "value"
                    },
                ],
                createdRow: function(row, data, dataIndex) {},
                footerCallback: function(row, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "") * 1 :
                            typeof i === "number" ?
                            i :
                            0;
                    };
                    this.api()
                        .columns(".sum", {
                            page: "current"
                        })
                        .every(function() {
                            var column = this;
                            if (column.data().count()) {
                                var sum = column.data().reduce(function(a, b) {
                                    a = intVal(a);
                                    if (isNaN(a)) {
                                        a = 0;
                                    }

                                    b = intVal(b);
                                    if (isNaN(b)) {
                                        b = 0;
                                    }

                                    return a + b;
                                });
                                $(column.footer()).html(
                                    __currency_trans_from_en(sum, false)
                                );
                            }
                        });
                },
            });
            $(document).on('click', '.clear_filter', function() {
                $('.sale_filter').val('');
                $('.sale_filter').selectpicker('refresh');
                rewards_table.ajax.reload();
            });
            $(document).on('change', '.sale_filter', function() {
                rewards_table.ajax.reload();
            });
        })
    </script>
@endsection
