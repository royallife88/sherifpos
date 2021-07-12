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
                                                {!! Form::select('expense_category_id', $expense_categories,
                                                request()->expense_category_id, ['class'
                                                =>
                                                'form-control', 'placeholder' =>
                                                __('lang.all'),'data-live-search'=>"true"]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('expense_beneficiary_id', __('lang.expense_beneficiary'), []) !!}
                                                {!! Form::select('expense_beneficiary_id', $expense_beneficiaries,
                                                request()->expense_beneficiary_id, ['class'
                                                =>
                                                'form-control', 'placeholder' =>
                                                __('lang.all'),'data-live-search'=>"true"]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                                {!! Form::date('start_date', request()->start_date, ['class' =>
                                                'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                                {!! Form::date('end_date', request()->end_date, ['class' =>
                                                'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <br>
                                            <button type="submit"
                                                class="btn btn-success mt-2">@lang('lang.filter')</button>
                                            <a href="{{action('ExpenseController@index')}}"
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
                                        <th>@lang('lang.amount_paid')</th>
                                        <th>@lang('lang.created_by')</th>
                                        <th>@lang('lang.payment_date')</th>
                                        <th>@lang('lang.next_payment_date')</th>
                                        <th>@lang('lang.files')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($expenses as $expense)
                                    <tr>
                                        <td>
                                            {{$expense->expense_category->name}}
                                        </td>
                                        <td>
                                            {{$expense->expense_beneficiary->name}}
                                        </td>
                                        <td>{{@num_format($expense->final_total)}}</td>
                                        <td>{{ucfirst($expense->created_by)}}</td>
                                        <td>@if(!empty($expense->transaction_payments)){{@format_date($expense->transaction_payments->first()->paid_on)}}@endif
                                        </td>
                                        <td>@if(!empty($expense->next_payment_date)){{@format_date($expense->next_payment_date)}}@endif
                                        </td>
                                        <td>
                                            <a data-href="{{action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $expense->id, 'collection_name' => 'expense'])}}"
                                                data-container=".view_modal"
                                                class="btn btn-danger btn-modal text-white">@lang('lang.view')</a>
                                        </td>
                                        <td>
                                            @can('account_management.expenses.create_and_edit')
                                            <a href="{{action('ExpenseController@edit', $expense->id)}}"
                                                class="btn btn-danger text-white edit_job"><i
                                                    class="fa fa-pencil-square-o"></i></a>
                                            @endcan
                                            @can('account_management.expenses.delete')
                                            <a data-href="{{action('ExpenseController@destroy', $expense->id)}}"
                                                data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                class="btn btn-danger text-white delete_item"><i
                                                    class="fa fa-trash"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>@lang('lang.total')</strong></td>
                                        <td colspan="2">{{@num_format($expenses->sum('final_total'))}}</td>
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
<script>

</script>
@endsection