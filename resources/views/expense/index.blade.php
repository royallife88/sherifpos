@extends('layouts.app')
@section('title', __('lang.expenses'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.expenses')</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('expense_category_id', __('lang.expense_category'), []) !!}
                                                    {!! Form::select('expense_category_id', $expense_categories, request()->expense_category_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('expense_beneficiary_id', __('lang.expense_beneficiary'), []) !!}
                                                    {!! Form::select('expense_beneficiary_id', $expense_beneficiaries, request()->expense_beneficiary_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                                    {!! Form::select('store_id', $stores, request()->store_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('store_paid_id', __('lang.store') . ' ' . __('lang.paid_by'), []) !!}
                                                    {!! Form::select('store_paid_id', $stores, request()->store_paid_id, ['class' => 'form-control', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                                    {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                                    {!! Form::text('start_time', request()->start_time, ['class' => 'form-control time_picker sale_filter']) !!}
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
                                                    {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker sale_filter']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <br>
                                                <button type="submit"
                                                    class="btn btn-success mt-2">@lang('lang.filter')</button>
                                                <a href="{{ action('ExpenseController@index') }}"
                                                    class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-12">
                                <br>
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.expense_category')</th>
                                            <th>@lang('lang.beneficiary')</th>
                                            <th>@lang('lang.store')</th>
                                            <th class="sum">@lang('lang.amount_paid')</th>
                                            <th>@lang('lang.created_by')</th>
                                            <th>@lang('lang.creation_date')</th>
                                            <th>@lang('lang.payment_date')</th>
                                            <th>@lang('lang.next_payment_date')</th>
                                            <th>@lang('lang.store') @lang('lang.paid_by')</th>
                                            <th>@lang('lang.source_of_payment')</th>
                                            <th>@lang('lang.files')</th>
                                            <th class="notexport">@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($expenses as $expense)
                                            <tr>
                                                <td>
                                                    {{ $expense->expense_category->name ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $expense->expense_beneficiary->name ?? '' }}
                                                </td>
                                                <td>
                                                    @if (!empty($expense->store))
                                                        {{ $expense->store->name }}
                                                    @endif
                                                </td>
                                                <td>{{ @num_format($expense->final_total) }}</td>
                                                <td>{{ ucfirst($expense->created_by) }}</td>
                                                <td>{{ @format_date($expense->transaction_date) }}</td>
                                                <td>
                                                    @if (!empty($expense->transaction_payments->first()))
                                                        {{ @format_date($expense->transaction_payments->first()->paid_on) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($expense->next_payment_date))
                                                        {{ @format_date($expense->next_payment_date) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $store = '';
                                                        if (!empty($expense->source_id)) {
                                                            $employee = App\Models\Employee::where('user_id', $expense->source_id)->first();
                                                        }
                                                        if (!empty($employee)) {
                                                            $store = implode(',', $employee->store->pluck('name')->toArray());
                                                        }

                                                    @endphp
                                                    {{ $store }}
                                                </td>
                                                <td>{{ $expense->source_name }}</td>
                                                <td>
                                                    <a data-href="{{ action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $expense->id, 'collection_name' => 'expense']) }}"
                                                        data-container=".view_modal"
                                                        class="btn btn-default btn-modal">@lang('lang.view')</a>
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
                                                            @can('expense.expenses.view')
                                                                <li>
                                                                    <a href="{{ action('ExpenseController@show', $expense->id) }}"
                                                                        class="btn edit_job"><i
                                                                            class="fa fa-eye"></i>
                                                                        @lang('lang.view')</a>
                                                                </li>
                                                            @endcan
                                                            @can('expense.expenses.create_and_edit')
                                                                <li>
                                                                    <a href="{{ action('ExpenseController@edit', $expense->id) }}"
                                                                        class="btn edit_job"><i
                                                                            class="fa fa-pencil-square-o"></i>
                                                                        @lang('lang.edit')</a>
                                                                </li>
                                                            @endcan
                                                            @can('expense.expenses.delete')
                                                                <li>
                                                                    <a data-href="{{ action('ExpenseController@destroy', $expense->id) }}"
                                                                        data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                        class="btn delete_item text-red"><i
                                                                            class="fa fa-trash"></i> @lang('lang.delete')</a>
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
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>@lang('lang.total')</strong></td>
                                            <td>{{ @num_format($expenses->sum('final_total')) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script></script>
@endsection
