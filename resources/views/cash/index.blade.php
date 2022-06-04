@extends('layouts.app')
@section('title', __('lang.cash'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.cash')</h4>
        </div>
        <div class="col-md-12 card pt-3 pb-3">
            <form action="">
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
                            {!! Form::text('start_time', request()->start_time, ['class' => 'form-control
                            time_picker sale_filter']) !!}
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
                            {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker
                            sale_filter']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_pos_id', __('lang.pos'), []) !!}
                            {!! Form::select('store_pos_id', $store_pos, request()->store_pos_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('user_id', __('lang.user'), []) !!}
                            {!! Form::select('user_id', $users, request()->user_id, ['class' =>
                            'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('CashController@index')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>

                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="store_table" class="table dataTable">
                    <thead>
                        <tr>
                            <th>@lang('lang.date_and_time')</th>
                            <th>@lang('lang.user')</th>
                            <th>@lang('lang.pos')</th>
                            <th>@lang('lang.notes')</th>
                            <th>@lang('lang.status')</th>
                            <th class="sum">@lang('lang.cash_sales')</th>
                            @if(session('system_mode') == 'restaurant')
                            <th class="sum">@lang('lang.dining_in')</th>
                            @endif
                            <th class="sum">@lang('lang.cash_in')</th>
                            <th class="sum">@lang('lang.cash_out')</th>
                            <th class="sum">@lang('lang.purchases')</th>
                            <th class="sum">@lang('lang.expenses')</th>
                            <th class="sum">@lang('lang.wages_and_compensation')</th>
                            <th class="sum">@lang('lang.current_cash')</th>
                            <th class="sum">@lang('lang.closing_cash')</th>
                            <th class="sum">@lang('lang.closing_date_and_time')</th>
                            <th>@lang('lang.cash_given_to')</th>
                            <th class="notexport">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cash_registers as $cash_register)
                        <tr>
                            <td>{{@format_datetime($cash_register->created_at)}}</td>
                            <td>{{ucfirst($cash_register->cashier->name ?? '')}}</td>
                            <td>{{ucfirst($cash_register->cashier->employee->store_pos ?? '')}}</td>
                            <td>{{ucfirst($cash_register->notes)}}</td>
                            <td>{{ucfirst($cash_register->status)}}</td>
                            <td>{{@num_format($cash_register->total_cash_sales - $cash_register->total_refund_cash - $cash_register->total_sell_return)}}
                            </td>
                            @if(session('system_mode') == 'restaurant')
                            <td>{{@num_format($cash_register->total_dining_in)}}</td>
                            @endif
                            <td>{{@num_format($cash_register->total_cash_in)}}</td>
                            <td>{{@num_format($cash_register->total_cash_out)}}</td>
                            <td>{{@num_format($cash_register->total_purchases)}}</td>
                            <td>{{@num_format($cash_register->total_expenses)}}</td>
                            <td>{{@num_format($cash_register->total_wages_and_compensation)}}</td>
                            <td>{{@num_format($cash_register->total_cash_sales - $cash_register->total_refund_cash +
                                $cash_register->total_cash_in - $cash_register->total_cash_out -
                                $cash_register->total_purchases - $cash_register->total_expenses - $cash_register->total_wages_and_compensation - $cash_register->total_sell_return)}}</td>
                            <td>{{@num_format($cash_register->closing_amount)}}</td>
                            <td>@if(!empty($cash_register->closed_at)){{@format_datetime($cash_register->closed_at)}}@endif</td>
                            <td>{{!empty($cash_register->cash_given) ? $cash_register->cash_given->name : ''}}</td>
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
                                        @if($cash_register->status == 'open')
                                        @can('cash.add_cash_in.create_and_edit')
                                        <li>
                                            <a data-href="{{action('CashController@addCashIn', $cash_register->id)}}"
                                                data-container=".view_modal" class="btn btn-modal"><i
                                                    class="fa fa-arrow-down"></i> @lang('lang.add_cash_in')</a>
                                        </li>
                                        <li class="divider"></li>
                                        @endcan
                                        @can('cash.add_cash_out.create_and_edit')
                                        <li>
                                            <a data-href="{{action('CashController@addCashOut', $cash_register->id)}}"
                                                data-container=".view_modal" class="btn btn-modal"><i
                                                    class="fa fa-arrow-up"></i> @lang('lang.add_cash_out')</a>
                                        </li>
                                        <li class="divider"></li>
                                        @endcan
                                        @can('cash.add_closing_cash.create_and_edit')
                                        <li>
                                            <a data-href="{{action('CashController@addClosingCash', $cash_register->id)}}"
                                                data-container=".view_modal" class="btn btn-modal"><i
                                                    class="fa fa-window-close"></i> @lang('lang.add_closing_cash')</a>
                                        </li>
                                        <li class="divider"></li>
                                        @endcan
                                        @endif
                                        @can('cash.view_details.view')
                                        <li>
                                            <a data-href="{{action('CashController@show', $cash_register->id)}}"
                                                data-container=".view_modal" class="btn btn-modal"><i
                                                    class="fa fa-eye"></i> @lang('lang.view_details')</a>
                                        </li>
                                        <li class="divider"></li>
                                        @endcan
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
                            <th style="text-align: right">@lang('lang.total')</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
