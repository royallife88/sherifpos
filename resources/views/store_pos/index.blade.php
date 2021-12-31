@extends('layouts.app')
@section('title', __('lang.pos_for_the_stores'))

@section('content')

<div class="container-fluid">
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                @can('settings.store.create_and_edit')
                <a style="color: white" data-href="{{action('StorePosController@create')}}" data-container=".view_modal"
                    class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
                    @lang('lang.add_pos_for_store')</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="store_table" class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.store')</th>
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.user')</th>
                                <th>@lang('lang.date_and_time')</th>
                                <th>@lang('lang.total_sales')</th>
                                <th>@lang('lang.cash_sales')</th>
                                <th>@lang('lang.credit_card_sales')</th>
                                <th>@lang('lang.delivery_sales')</th>
                                <th>@lang('lang.pending_orders')</th>
                                <th>@lang('lang.pay_later_sales')</th>
                                <th>@lang('lang.return_sale_of_this_pos')</th>
                                <th>@lang('lang.last_session_closed_at')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($store_poses as $store_pos)
                            <tr>
                                <td>{{$store_pos->store->name ?? ''}}</td>
                                <td>{{$store_pos->name}}</td>
                                <td>{{$store_pos->user->name}}</td>
                                <td>{{@format_datetime($store_pos->created_at)}}</td>
                                <td>{{@num_format($store_pos->total_sales)}}</td>
                                <td>{{@num_format($store_pos->total_cash)}}</td>
                                <td>{{@num_format($store_pos->total_card)}}</td>
                                <td>{{@num_format($store_pos->total_delivery_sales)}}</td>
                                <td>{{@num_format($store_pos->pending_orders)}}</td>
                                <td>{{@num_format($store_pos->pay_later_sales)}}</td>
                                <td>{{@num_format($store_pos->total_sales_return)}}</td>
                                @php
                                    $last_session = App\Models\CashRegister::where('store_pos_id', $store_pos->id)->where('status', 'close')->orderBy('closed_at', 'desc')->first();
                                @endphp
                                <td>{{!empty($last_session) ? @format_datetime($last_session->closed_at): ''}}</td>
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
                                            @can('settings.store_pos.create_and_edit')
                                            <li>

                                                <a data-href="{{action('StorePosController@edit', $store_pos->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('settings.store_pos.delete')
                                            <li>
                                                <a data-href="{{action('StorePosController@destroy', $store_pos->id)}}"
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
