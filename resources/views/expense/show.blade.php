@extends('layouts.app')
@section('title', __('lang.expense'))
<style>
    label {
        font-weight: bold;
    }

</style>
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.expense')</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_category_id">@lang('lang.expense_category'):
                                            </label> {{ $expense->expense_category->name }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_beneficiary_id">@lang('lang.beneficiary'):
                                            </label> {{ $expense->expense_beneficiary->name }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="store_id">@lang('lang.store'): </label> {{ $expense->store->name }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('source_type', __('lang.source_type'), []) !!}: {{ ucfirst($expense->source_type) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('amount', __('lang.amount'), []) !!}: {{ @num_format($expense->final_total) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('payment_method', __('lang.payment_method'), []) !!}:
                                            {{ ucfirst($expense->transaction_payments->first()->method) }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="details">@lang('lang.details'):</label>
                                            {{ $expense->details }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="files">@lang('lang.files'):</label> <a
                                                data-href="{{ action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $expense->id, 'collection_name' => 'expense']) }}"
                                                data-container=".view_modal"
                                                class="btn btn-modal">@lang('lang.view')</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection
