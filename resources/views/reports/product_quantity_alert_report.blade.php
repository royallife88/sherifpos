@extends('layouts.app')
@section('title', __('lang.product_quantity_alert_report'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.product_quantity_alert_report')</h4>
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
                    @if(session('user.is_superadmin'))
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_id', __('lang.product'), []) !!}
                            {!! Form::select('product_id', $products, request()->product_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ReportController@getProductQuantityAlertReport')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.image')</th>
                                <th>@lang('lang.product_name')</th>
                                <th>@lang('lang.sku')</th>
                                <th>@lang('lang.quantity')</th>
                                <th>@lang('lang.alert_quantity')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td><img src="@if(!empty($item->getFirstMediaUrl('product'))){{$item->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                        alt="photo" width="50" height="50"></td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->sku}}</td>
                                <td> {{@num_format($item->qty)}}</td>
                                <td> {{@num_format($item->alert_quantity)}}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                            @can('product_module.product.view')
                                            <li>
                                                <a data-href="{{action('ProductController@show', $item->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-eye"></i>
                                                    @lang('lang.view')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('product_module.product.create_and_edit')
                                            <li>

                                                <a href="{{action('ProductController@edit', $item->id)}}" class="btn"><i
                                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('product_module.product.delete')
                                            <li>
                                                <a data-href="{{action('ProductController@destroy', $item->id)}}"
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
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
