@extends('layouts.app')
@section('title', __('lang.purchase_order'))

@section('content')
<section class="">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('store_id', __('lang.store'), []) !!}
                                {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                                'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' =>
                                'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>

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
                            <a href="{{action('PurchaseOrderController@index')}}"
                                class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.po_ref_no')</th>
                    <th>@lang('lang.date')</th>
                    <th>@lang('lang.created_by')</th>
                    <th>@lang('lang.supplier')</th>
                    <th class="sum">@lang('lang.value')</th>
                    <th>@lang('lang.status')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($purchase_orders as $purchase_order)
                <tr>
                    <td>{{$purchase_order->po_no}}</td>
                    <td> {{@format_date($purchase_order->transaction_date)}}</td>
                    <td>
                        {{ucfirst($purchase_order->created_by_user->name ?? '')}}
                    </td>
                    <td>
                        @if(!empty($purchase_order->supplier)){{$purchase_order->supplier->name}}@endif
                    </td>
                    <td>
                        {{@num_format($purchase_order->final_total)}}
                    </td>
                    <td>
                        {{$status_array[$purchase_order->status]}}
                    </td>

                    <td>

                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @can('purchase_order.purchase_order.view')
                                <li>
                                    <a href="{{action('PurchaseOrderController@show', $purchase_order->id)}}?print=true" target="_blank"
                                        class=""><i class="dripicons-print btn"></i> @lang('lang.print')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('purchase_order.purchase_order.view')
                                <li>
                                    <a href="{{action('PurchaseOrderController@show', $purchase_order->id)}}" target="_blank"
                                        class=""><i class="fa fa-eye btn"></i> @lang('lang.view')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('purchase_order.purchase_order.create_and_edit')
                                <li>
                                    <a href="{{action('PurchaseOrderController@edit', $purchase_order->id)}}"><i
                                            class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('purchase_order.purchase_order.delete')
                                <li>
                                    <a data-href="{{action('PurchaseOrderController@destroy', $purchase_order->id)}}"
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
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th style="text-align: right">@lang('lang.total')</th>
                        <td></td>
                    </tr>
                </tfoot>
            </tbody>
            <tfoot>

            </tfoot>
        </table>

    </div>



</section>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
