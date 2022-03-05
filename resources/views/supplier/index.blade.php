@extends('layouts.app')
@section('title', __('lang.supplier'))

@section('content')
<div class="container-fluid">
    <a style="color: white" href="{{action('SupplierController@create')}}"
        class="btn btn-info"><i class="dripicons-plus"></i>
        @lang('lang.add_supplier')</a>

</div>
<div class="table-responsive">
    <table id="store_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.name')</th>
                <th>@lang('lang.photo')</th>
                <th>@lang('lang.mobile_number')</th>
                <th>@lang('lang.address')</th>
                <th>@lang('lang.joining_date')</th>
                <th>@lang('lang.total_purchase')</th>
                <th>@lang('lang.pending_orders')</th>
                <th class="sum">@lang('lang.overdue_amount')</th>
                <th>@lang('lang.created_by')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_due = 0;
            @endphp
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{$supplier->name}}</td>
                <td><img src="@if(!empty($supplier->getFirstMediaUrl('supplier_photo'))){{$supplier->getFirstMediaUrl('supplier_photo')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                    alt="photo" width="50" height="50">
                </td>
                <td>{{$supplier->mobile_number}}</td>
                <td>{{$supplier->address}}</td>
                <td>{{@format_date($supplier->created_at)}}</td>
                <td><a href="{{action('SupplierController@show', $supplier->id)}}?show=statement_of_account" class="btn">{{@num_format($supplier->total_purchase)}}</a></td>
                <td>@if($supplier->pending_orders > 0) <a href="{{action('SupplierController@show', $supplier->id)}}?show=pending_orders" class=""> @lang('lang.yes') </a> @else @lang('lang.no') @endif</td>
                <td>{{@num_format($supplier->total_invoice - $supplier->total_paid)}}</td>
                <td>{{$supplier->created_by_user->name ?? ''}}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('supplier_module.supplier.view')
                            <li>

                                <a href="{{action('SupplierController@show', $supplier->id)}}"
                                    class="btn"><i
                                        class="fa fa-eye"></i> @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('supplier_module.supplier.view')
                            <li>
                                <a href="{{action('SupplierController@show', $supplier->id)}}?show=statement_of_account"
                                    class="btn"><i
                                        class="dripicons-document"></i> @lang('lang.statement_of_account')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('supplier_module.supplier.create_and_edit')
                            <li>
                                <a href="{{action('SupplierController@edit', $supplier->id)}}"><i
                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('supplier_module.supplier.delete')
                            <li>
                                <a data-href="{{action('SupplierController@destroy', $supplier->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i> @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>
            @php
                $total_due += $supplier->total_invoice - $supplier->total_paid;
            @endphp
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
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('javascript')
    <script>

    </script>
@endsection
