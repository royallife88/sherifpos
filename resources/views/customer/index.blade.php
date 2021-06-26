@extends('layouts.app')
@section('title', __('lang.customer'))

@section('content')
<div class="container-fluid">
    <a style="color: white" href="{{action('CustomerController@create')}}"
        class="btn btn-info"><i class="dripicons-plus"></i>
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
                <th>@lang('lang.discount')</th>
                <th>@lang('lang.balance')</th>
                <th>@lang('lang.joining_date')</th>
                <th>@lang('lang.created_by')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{$customer->customer_type->name}}</td>
                <td>{{$customer->name}}</td>
                <td><img src="{{$customer->getFirstMediaUrl('customer_photo')}}" alt="photo" width="50" height="50"></td>
                <td>{{$customer->mobile_number}}</td>
                <td>{{$customer->address}}</td>
                <td>{{$customer->fixed_discount}}</td>
                <td>{{$customer->balance}}</td>
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
                            {{-- @can('customer_module.customer.view')
                            <li>

                                <a data-href="{{action('CustomerController@show', $customer->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i
                                        class="dripicons-document"></i> @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan --}}
                            @can('customer_module.customer.edit')
                            <li>
                                <a href="{{action('CustomerController@edit', $customer->id)}}"><i
                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.customer.delete')
                            <li>
                                <a data-href="{{action('CustomerController@destroy', $customer->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i> @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('javascrpt')
    <script>

    </script>
@endsection
