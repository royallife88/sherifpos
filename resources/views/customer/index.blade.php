@extends('layouts.app')
@section('title', __('lang.customer'))

@section('content')
<div class="container-fluid">
    <a style="color: white" href="{{action('CustomerController@create')}}" class="btn btn-info"><i
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
                <th>@lang('lang.balance')</th>
                <th>@lang('lang.purchases')</th>
                <th>@lang('lang.discount')</th>
                <th>@lang('lang.points')</th>
                <th>@lang('lang.joining_date')</th>
                <th>@lang('lang.created_by')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_balances = 0;
                $total_discounts = 0;
            @endphp
            @foreach($customers as $customer)
            <tr>
                <td>@if(!empty($customer->customer_type)){{$customer->customer_type->name}}@endif</td>
                <td>{{$customer->name}}</td>
                <td>@if(!empty($customer->getFirstMediaUrl('customer_photo')))<img
                        src="{{$customer->getFirstMediaUrl('customer_photo')}}" alt="photo" width="50"
                        height="50">@endif</td>
                <td>{{$customer->mobile_number}}</td>
                <td>{{$customer->address}}</td>
                <td>{{@num_format($balances[$customer->id])}}</td>
                <td><a href="{{action('CustomerController@show', $customer->id)}}?show=purchases" class="btn">{{@num_format($customer->total_purchase)}}</a></td>
                <td><a href="{{action('CustomerController@show', $customer->id)}}?show=discounts" class="btn">{{@num_format($customer->total_sp_discount + $customer->total_product_discount +$customer->total_coupon_discount)}}</a></td>
                <td><a href="{{action('CustomerController@show', $customer->id)}}?show=points" class="btn">{{@num_format($customer->total_rp)}}</a></td>
                <td>{{@format_date($customer->created_at)}}</td>
                <td>{{$customer->created_by_user->name}}</td>
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
                                <a href="{{action('CustomerController@show', $customer->id)}}" class="btn">
                                    <i class="dripicons-document"></i> @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.customer.create_and_edit')
                            <li>
                                <a href="{{action('CustomerController@edit', $customer->id)}}"><i
                                        class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.add_payment.create_and_edit')
                            @if($balances[$customer->id] > 0)
                            <li>
                                <a data-href="{{action('TransactionPaymentController@getCustomerDue', $customer->id)}}" class="btn-modal" data-container=".view_modal"><i
                                        class="fa fa-money btn"></i>@lang('lang.pay_customer_due')</a>
                            </li>
                            <li class="divider"></li>
                            @endif
                            @endcan
                            @can('adjustment.customer_balance_adjustment.create_and_edit')
                            <li>
                                <a href="{{action('CustomerBalanceAdjustmentController@create', ['customer_id' => $customer->id])}}"
                                    class="btn"><i class="fa fa-adjust"></i> @lang('lang.adjust_customer_balance')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('adjustment.customer_point_adjustment.create_and_edit')
                            <li>

                                <a href="{{action('CustomerPointAdjustmentController@create', ['customer_id' => $customer->id])}}"
                                     class="btn"><i
                                        class="fa fa-adjust"></i> @lang('lang.adjust_customer_points')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.customer.delete')
                            <li>
                                <a data-href="{{action('CustomerController@destroy', $customer->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>
            @php
                $total_balances += $balances[$customer->id];
                $total_discounts += $customer->total_sp_discount + $customer->total_product_discount +$customer->total_coupon_discount;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                <td>{{@num_format($total_balances)}}</td>
                <td>{{@num_format($customers->sum('total_purchase'))}}</td>
                <td>{{@num_format($total_discounts)}}</td>
                <td>{{@num_format($customers->sum('total_rp'))}}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('javascript')
<script>

</script>
@endsection
