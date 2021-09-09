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
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('start_date', __('lang.start_date'), []) !!}
                            {!! Form::date('start_date', request()->start_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                            {!! Form::date('end_date', request()->end_date, ['class' => 'form-control']) !!}
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
                            <th>@lang('lang.cashier')</th>
                            <th>@lang('lang.notes')</th>
                            <th>@lang('lang.status')</th>
                            <th>@lang('lang.cash_sales')</th>
                            <th>@lang('lang.cash_in')</th>
                            <th>@lang('lang.cash_out')</th>
                            <th>@lang('lang.closing_cash')</th>
                            <th class="notexport">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cash_registers as $cash_register)
                        <tr>
                            <td>{{@format_datetime($cash_register->created_at)}}</td>
                            <td>{{ucfirst($cash_register->cashier->name)}}</td>
                            <td>{{ucfirst($cash_register->notes)}}</td>
                            <td>{{ucfirst($cash_register->status)}}</td>
                            <td>{{@num_format($cash_register->total_cash_sales)}}</td>
                            <td>{{@num_format($cash_register->total_cash_in)}}</td>
                            <td>{{@num_format($cash_register->total_cash_out)}}</td>
                            <td>{{@num_format($cash_register->closing_amount)}}</td>
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
                            <th colspan="4" style="text-align: right">@lang('lang.total')</th>
                            <td>{{@num_format($cash_registers->sum('total_cash_sales'))}}</td>
                            <td>{{@num_format($cash_registers->sum('total_cash_in'))}}</td>
                            <td>{{@num_format($cash_registers->sum('total_cash_out'))}}</td>
                            <td>{{@num_format($cash_registers->sum('closing_amount'))}}</td>
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
