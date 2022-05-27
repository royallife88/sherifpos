@extends('layouts.app')
@section('title', __('lang.customer'))

@section('content')
    <div class="container-fluid">
        <a style="color: white" href="{{ action('CustomerController@create') }}" class="btn btn-info"><i
                class="dripicons-plus"></i>
            @lang('lang.customer')</a>

    </div>
    <div class="table-responsive">
        <table id="store_table" class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.customer_type')</th>
                    <th>@lang('lang.name')</th>
                    <th>@lang('lang.photo')</th>
                    <th>@lang('lang.mobile_number')</th>
                    <th>@lang('lang.address')</th>
                    <th class="sum">@lang('lang.balance')</th>
                    <th>@lang('lang.purchases')</th>
                    <th>@lang('lang.discount')</th>
                    <th>@lang('lang.points')</th>
                    <th>@lang('lang.joining_date')</th>
                    <th>@lang('lang.created_by')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>
                            @if (!empty($customer->customer_type))
                                {{ $customer->customer_type->name }}
                            @endif
                        </td>
                        <td>{{ $customer->name }}</td>
                        <td><img src="@if (!empty($customer->getFirstMediaUrl('customer_photo'))) {{ $customer->getFirstMediaUrl('customer_photo') }}@else{{ asset('/uploads/' . session('logo')) }} @endif"
                                alt="photo" width="50" height="50">
                        </td>
                        <td>{{ $customer->mobile_number }}</td>
                        <td>{{ $customer->address }}</td>
                        <td class="@if ($balances[$customer->id] < 0) text-red @endif">
                            {{ @num_format($balances[$customer->id]) }}
                        </td>
                        <td><a href="{{ action('CustomerController@show', $customer->id) }}?show=purchases"
                                class="btn">{{ @num_format($customer->total_purchase - $customer->total_return) }}</a>
                        </td>
                        <td><a href="{{ action('CustomerController@show', $customer->id) }}?show=discounts"
                                class="btn">{{ @num_format($customer->total_sp_discount + $customer->total_product_discount + $customer->total_coupon_discount) }}</a>
                        </td>
                        <td><a href="{{ action('CustomerController@show', $customer->id) }}?show=points"
                                class="btn">{{ @num_format($customer->total_rp) }}</a></td>
                        <td>{{ @format_date($customer->created_at) }}</td>
                        <td>{{ $customer->created_by_user->name ?? '' }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    @can('customer_module.customer.view')
                                        <li>
                                            <a href="{{ action('CustomerController@show', $customer->id) }}"
                                                class="btn">
                                                <i class="dripicons-document"></i> @lang('lang.view')</a>
                                        </li>
                                        <li class="divider"></li>
                                    @endcan
                                    @can('customer_module.customer.create_and_edit')
                                        <li>
                                            <a href="{{ action('CustomerController@edit', $customer->id) }}"><i
                                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                        </li>
                                        <li class="divider"></li>
                                    @endcan
                                    @can('customer_module.add_payment.create_and_edit')
                                        @if ($balances[$customer->id] < 0)
                                            <li>
                                                <a data-href="{{ action('TransactionPaymentController@getCustomerDue', $customer->id) }}"
                                                    class="btn-modal" data-container=".view_modal"><i
                                                        class="fa fa-money btn"></i>@lang('lang.pay_customer_due')</a>
                                            </li>
                                            <li class="divider"></li>
                                        @endif
                                    @endcan
                                    @can('adjustment.customer_balance_adjustment.create_and_edit')
                                        <li>
                                            <a href="{{ action('CustomerBalanceAdjustmentController@create', ['customer_id' => $customer->id]) }}"
                                                class="btn"><i class="fa fa-adjust"></i> @lang('lang.adjust_customer_balance')</a>
                                        </li>
                                        <li class="divider"></li>
                                    @endcan
                                    @can('adjustment.customer_point_adjustment.create_and_edit')
                                        <li>

                                            <a href="{{ action('CustomerPointAdjustmentController@create', ['customer_id' => $customer->id]) }}"
                                                class="btn"><i class="fa fa-adjust"></i> @lang('lang.adjust_customer_points')</a>
                                        </li>
                                        <li class="divider"></li>
                                    @endcan
                                    @if (session('system_mode') == 'garments')
                                        @can('customer_module.customer_sizes.create_and_edit')
                                            <li>
                                                <a data-href="{{ action('CustomerSizeController@add', $customer->id) }}"
                                                    class="btn-modal" data-container=".view_modal"><i
                                                        class="fa fa-plus btn"></i>@lang('lang.add_size')</a>
                                            </li>
                                            <li class="divider"></li>
                                        @endcan
                                        @can('customer_module.customer_sizes.view')
                                            <li>
                                                <a href="{{ action('CustomerController@show', $customer->id) }}?show=sizes"
                                                    class=""><i
                                                        class="fa fa-user-secret btn"></i>@lang('lang.view_sizes')</a>
                                            </li>
                                            <li class="divider"></li>
                                        @endcan
                                    @endif
                                    @if ($customer->is_default == 0)
                                        @can('customer_module.customer.delete')
                                            <li>
                                                <a data-href="{{ action('CustomerController@destroy', $customer->id) }}"
                                                    data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                    class="btn text-red delete_customer"><i class="fa fa-trash"></i>
                                                    @lang('lang.delete')</a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th style="text-align: right">@lang('lang.total')</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).on('click', '.delete_customer', function(e) {
            e.preventDefault();
            swal({
                title: 'Are you sure?',
                text: "@lang('lang.all_customer_transactions_will_be_deleted')",
                icon: 'warning',
            }).then(willDelete => {
                if (willDelete) {
                    var check_password = $(this).data('check_password');
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    swal({
                        title: 'Please Enter Your Password',
                        content: {
                            element: "input",
                            attributes: {
                                placeholder: "Type your password",
                                type: "password",
                                autocomplete: "off",
                                autofocus: true,
                            },
                        },
                        inputAttributes: {
                            autocapitalize: 'off',
                            autoComplete: 'off',
                        },
                        focusConfirm: true
                    }).then((result) => {
                        if (result) {
                            $.ajax({
                                url: check_password,
                                method: 'POST',
                                data: {
                                    value: result
                                },
                                dataType: 'json',
                                success: (data) => {

                                    if (data.success == true) {
                                        swal(
                                            'Success',
                                            'Correct Password!',
                                            'success'
                                        );

                                        $.ajax({
                                            method: 'DELETE',
                                            url: href,
                                            dataType: 'json',
                                            data: data,
                                            success: function(result) {
                                                if (result.success ==
                                                    true) {
                                                    swal(
                                                        'Success',
                                                        result.msg,
                                                        'success'
                                                    );
                                                    setTimeout(() => {
                                                        location
                                                            .reload();
                                                    }, 1500);
                                                    location.reload();
                                                } else {
                                                    swal(
                                                        'Error',
                                                        result.msg,
                                                        'error'
                                                    );
                                                }
                                            },
                                        });

                                    } else {
                                        swal(
                                            'Failed!',
                                            'Wrong Password!',
                                            'error'
                                        )

                                    }
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
