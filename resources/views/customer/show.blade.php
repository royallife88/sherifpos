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
                                                class="customer_mobile_span">{{ $customer->mobile }}</span>
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
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.reference_no')</th>
                                            <th>@lang('lang.customer')</th>
                                            <th>@lang('lang.product')</th>
                                            <th class="sum">@lang('lang.discount')</th>
                                            <th class="sum">@lang('lang.grand_total')</th>
                                            <th class="sum">@lang('lang.paid')</th>
                                            <th class="sum">@lang('lang.due')</th>
                                            <th>@lang('lang.payment_date')</th>
                                            <th>@lang('lang.status')</th>
                                            <th>@lang('lang.points_earned')</th>
                                            <th>@lang('lang.cashier')</th>
                                            <th>@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_purchase_payments = 0;
                                            $total_purchase_due = 0;
                                        @endphp
                                        @foreach ($sales as $sale)
                                            @if (empty($sale->return_parent))
                                                <tr>
                                                    <td>{{ @format_date($sale->transaction_date) }}</td>
                                                    <td>{{ $sale->invoice_no }}</td>
                                                    <td>
                                                        @if (!empty($sale->customer))
                                                            {{ $sale->customer->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @foreach ($sale->transaction_sell_lines as $line)
                                                            ({{ @num_format($line->quantity) }})
                                                            @if (!empty($line->product))
                                                                {{ $line->product->name }}
                                                            @endif <br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ @num_format($sale->discount_amount) }}</td>
                                                    <td>{{ @num_format($sale->final_total) }}</td>
                                                    <td>{{ @num_format($sale->transaction_payments->sum('amount')) }}
                                                    </td>
                                                    <td>{{ @num_format($sale->final_total - $sale->transaction_payments->sum('amount')) }}
                                                    </td>
                                                    <td>
                                                        @if ($sale->transaction_payments->count() > 0)
                                                            {{ @format_date($sale->transaction_payments->last()->paid_on) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($sale->status == 'final' && $sale->payment_status == 'paid')
                                                            <span class="badge badge-success">@lang('lang.completed')</span>
                                                        @else
                                                            @if ($sale->payment_status == 'pending')
                                                                <span
                                                                    class="badge badge-danger">@lang('lang.pay_later')</span>
                                                            @elseif($sale->payment_status == 'partial')
                                                            <span
                                                                    class="badge badge-danger">@lang('lang.partial')</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ @num_format($sale->rp_earned) }}</td>
                                                    <td>
                                                        @if ($sale->transaction_payments->count() > 0)
                                                            {{ $sale->transaction_payments->last()->created_by_user->name }}
                                                        @endif
                                                    </td>
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
                                                                        <a data-href="{{ action('SellController@show', $sale->id) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="fa fa-eye"></i>
                                                                            @lang('lang.view')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @can('sale.pos.create_and_edit')
                                                                    <li>
                                                                        <a href="{{ action('SellController@edit', $sale->id) }}"
                                                                            class="btn"><i
                                                                                class="dripicons-document-edit"></i>
                                                                            @lang('lang.edit')</a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                @endcan
                                                                @if ($sale->payment_status != 'paid')
                                                                    <li>
                                                                        <a data-href="{{ action('TransactionPaymentController@addPayment', ['id' => $sale->id]) }}"
                                                                            data-container=".view_modal"
                                                                            class="btn btn-modal"><i
                                                                                class="fa fa-plus"></i>
                                                                            @lang('lang.add_payment')</a>
                                                                    </li>
                                                                @endif
                                                                @can('sale.pos.delete')
                                                                    <li>
                                                                        <a data-href="{{ action('SellController@destroy', $sale->id) }}"
                                                                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                            class="btn text-red delete_item"><i
                                                                                class="fa fa-trash"></i>
                                                                            @lang('lang.delete')</a>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                </tr>
                                            @endif
                                            @php
                                                $total_purchase_payments += $sale->transaction_payments->sum('amount');
                                                $total_purchase_due += $sale->final_total - $sale->transaction_payments->sum('amount');
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align: right">@lang('lang.total')</th>
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
                                                        <span
                                                        class="badge badge-success">@lang('lang.completed')</span>@else
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
                                            <th colspan="3" style="text-align: right">@lang('lang.total')</th>
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
                                                        <span
                                                        class="badge badge-success">@lang('lang.completed')</span>@else
                                                        {{ ucfirst($discount->status) }}
                                                    @endif
                                                </td>
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
                                            <th colspan="4" style="text-align: right">@lang('lang.total')</th>
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
                                                        <span
                                                        class="badge badge-success">@lang('lang.completed')</span>@else
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
                                            <th colspan="4" style="text-align: right">@lang('lang.total')</th>
                                            <th></th>
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
@endsection

@section('javascript')
    <script>

    </script>
@endsection
