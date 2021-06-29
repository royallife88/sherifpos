@extends('layouts.app')
@section('title', __('lang.customer_type'))

@section('content')
<div class="container-fluid">
    <a style="color: white" href="{{action('CustomerTypeController@create')}}"
        class="btn btn-info"><i class="dripicons-plus"></i>
        @lang('lang.customer_type')</a>

</div>
<div class="table-responsive">
    <table id="store_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.name')</th>
                <th>@lang('lang.stores')</th>
                <th>@lang('lang.number_of_customer')</th>
                <th>@lang('lang.discount')</th>
                <th>@lang('lang.points')</th>
                <th>@lang('lang.date_and_time')</th>
                <th>@lang('lang.created_by')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer_types as $customer_type)
            <tr>
                <td>{{$customer_type->name}}</td>
                <td>
                    @php
                        $this_stores = [];
                    @endphp
                    @foreach ($customer_type->customer_type_store as $item)
                    @php
                        $this_stores[] = $stores[$item->store_id];
                    @endphp
                    @endforeach
                    {{implode(',' , $this_stores)}}
                </td>
                <td>{{$customer_type->number_of_customer}}</td>
                <td>{{$customer_type->discount}}</td>
                <td>{{$customer_type->points}}</td>
                <td>{{$customer_type->created_at}}</td>
                <td>{{$customer_type->created_by_user->name}}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('customer_module.customer_type.delete')
                            <li>

                                <a data-href="{{action('CustomerTypeController@show', $customer_type->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i
                                        class="dripicons-document"></i> @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.customer_type.view')
                            <li>
                                <a href="{{action('CustomerTypeController@edit', $customer_type->id)}}"><i
                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('customer_module.customer_type.delete')
                            <li>
                                <a data-href="{{action('CustomerTypeController@destroy', $customer_type->id)}}"
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

@section('javascript')
    <script>

    </script>
@endsection
