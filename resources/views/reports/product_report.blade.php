@extends('layouts.app')
@section('title', __('lang.product_report'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.product_report')</h4>
            </div>
            <form action="">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                {!! Form::text('start_time', request()->start_time, [
    'class' => 'form-control
                            time_picker sale_filter',
]) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                {!! Form::text('end_time', request()->end_time, [
    'class' => 'form-control time_picker
                            sale_filter',
]) !!}
                            </div>
                        </div>
                        @if (session('user.is_superadmin'))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('pos_id', __('lang.pos'), []) !!}
                                    {!! Form::select('pos_id', $store_pos, request()->pos_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('product_id', __('lang.product'), []) !!}
                                {!! Form::select('product_id', $products, request()->product_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <br>
                            <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                            <a href="{{ action('ReportController@getProductReport') }}"
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
                                    <th>@lang('lang.product_name')</th>
                                    <th>@lang('lang.sku')</th>
                                    <th class="sum">@lang('lang.purchased_amount')</th>
                                    <th class="sum">@lang('lang.purchased_qty')</th>
                                    <th class="sum">@lang('lang.sold_amount')</th>
                                    <th class="sum">@lang('lang.sold_qty')</th>
                                    <th class="sum">@lang('lang.profit')</th>
                                    <th class="sum">@lang('lang.in_stock')</th>
                                    <th class="sum">@lang('lang.employee')</th>
                                    <th class="sum">@lang('lang.commission')</th>
                                    <th class="notexport">@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->product_name }}</td>
                                        <td>{{ $transaction->sku }}</td>
                                        <td> {{ @num_format($transaction->purchased_amount) }}</td>
                                        <td> {{ @num_format($transaction->purchased_qty) }}</td>
                                        <td> {{ @num_format($transaction->sold_amount) }}</td>
                                        <td> {{ @num_format($transaction->sold_qty) }}</td>
                                        <td> {{ @num_format($transaction->sold_amount - $transaction->purchased_amount) }}
                                        </td>
                                        <td> {{ @num_format($transaction->in_stock) }}</td>
                                        <td>
                                            @php
                                                $product_id = (string) $transaction->id;
                                                $employee = App\Models\Employee::whereJsonContains('commissioned_products', $product_id)->first();
                                                $employee_id = !empty($employee) ? $employee->id : '';
                                            @endphp
                                            {{ $employee->employee_name ?? '' }}
                                        </td>
                                        <td>
                                            @php
                                                $product_id = (string) $transaction->id;
                                                $commission = 0;
                                                if (!empty($employee_id)) {
                                                    $commission = App\Models\Transaction::leftjoin('transaction_sell_lines', 'transactions.parent_sale_id', 'transaction_sell_lines.transaction_id')
                                                        ->where('transactions.employee_id', $employee_id)
                                                        ->where('transaction_sell_lines.product_id', $product_id)
                                                        ->where('transactions.type', 'employee_commission')
                                                        ->select(DB::raw('SUM(transactions.final_total) as commission'))
                                                        ->first();
                                                }
                                            @endphp
                                            @if (!empty($commission->commission))
                                                {{ @num_format($commission->commission) }}
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
                                                    @can('product_module.product.view')
                                                        <li>
                                                            <a data-href="{{ action('ReportController@viewProductDetails', $transaction->id) }}?store_id={{ request()->store_id }}"
                                                                data-container=".view_modal" class="btn btn-modal"><i
                                                                    class="fa fa-eye"></i>
                                                                @lang('lang.view')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('product_module.product.create_and_edit')
                                                        <li>

                                                            <a href="{{ action('ProductController@edit', $transaction->id) }}"
                                                                class="btn"><i class="dripicons-document-edit"></i>
                                                                @lang('lang.edit')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('product_module.product.delete')
                                                        <li>
                                                            <a data-href="{{ action('ProductController@destroy', $transaction->id) }}"
                                                                data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
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
                                <tr>
                                    <th style="text-align: right">@lang('lang.total')</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
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
