@extends('layouts.app')

@section('title', __('lang.expense_beneficiary'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.add_expense_beneficiary')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <a class="btn btn-primary ml-3" href="{{action('ExpenseBeneficiaryController@create')}}">
                            <i class="fa fa-plus"></i> @lang( 'lang.add_expense_beneficiary' )</a>

                        <div class="col-sm-12">
                            <br>
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.sr_no')</th>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.expense_category')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($expense_beneficiaries as $expense_beneficiary)
                                    <tr>
                                        <td>{{$loop->index +1}}</td>
                                        <td>
                                            {{$expense_beneficiary->name}}
                                        </td>
                                        <td>
                                            {{$expense_beneficiary->expense_category->name ?? ''}}
                                        </td>
                                        <td>
                                            @can('account_management.expense_beneficiaries.create_and_edit')
                                            <a data-href="{{action('ExpenseBeneficiaryController@edit', $expense_beneficiary->id)}}"
                                                data-container=".view_modal"
                                                class="btn btn-danger btn-modal text-white edit_job"><i
                                                    class="fa fa-pencil-square-o"></i></a>
                                            @endcan
                                            @can('account_management.expense_beneficiaries.delete')
                                            <a data-href="{{action('ExpenseBeneficiaryController@destroy', $expense_beneficiary->id)}}"
                                                data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                class="btn btn-danger text-white delete_item"><i
                                                    class="fa fa-trash"></i></a>
                                            @endcan
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
    </div>
</div>
@endsection

@section('javascript')
<script>

</script>
@endsection
