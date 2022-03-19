@extends('layouts.app')
@section('title', __('lang.store'))

@section('content')
<div class="container-fluid">

    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                @can('settings.store.create_and_edit')
                <a style="color: white" data-href="{{action('StoreController@create')}}" data-container=".view_modal"
                    class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
                    @lang('lang.add_store')</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="store_table" class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.phone_number')</th>
                                <th>@lang('lang.email')</th>
                                <th>@lang('lang.manager_name')</th>
                                <th>@lang('lang.manager_mobile_number')</th>
                                <th>@lang('lang.number_of_pos')</th>
                                <th>@lang('lang.sales')</th>
                                <th>@lang('lang.stock_value')</th>
                                <th>@lang('lang.expire_stock')</th>
                                <th>@lang('lang.returned_stock')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                            <tr>
                                <td>{{$store->name}}</td>
                                <td>{{$store->phone_number}}</td>
                                <td>{{$store->email}}</td>
                                <td>{{$store->manager_name}}</td>
                                <td>{{$store->manager_mobile_number}}</td>
                                <td>{{$store->store_pos->count()}}</td>
                                <td>{{@num_format($store->total_sales)}}</td>
                                @php
                                    $stock_value = App\Models\Product::leftjoin('variations', 'products.id', 'variations.product_id')->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
                                        ->select(DB::raw('SUM(product_stores.qty_available * products.purchase_price) as stock_value'))->where('products.is_service', 0)->where('product_stores.store_id', $store->id)->first();
                                @endphp
                                <td>{{@num_format($stock_value->stock_value)}}</td>
                                @php
                                    $expired_stock = App\Models\ProductStore::where('store_id', $store->id)->sum('expired_qauntity');
                                @endphp
                                <td>{{$expired_stock}}</td>
                                <td>{{@num_format($store->total_sales_return)}}</td>
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
                                            @can('settings.store.create_and_edit')
                                            <li>

                                                <a data-href="{{action('StoreController@edit', $store->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('settings.store.delete')
                                            <li>
                                                <a data-href="{{action('StoreController@destroy', $store->id)}}"
                                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                    @lang('lang.delete')</a>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
