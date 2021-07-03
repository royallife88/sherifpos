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
                        <div class="col-md-12">
                            {!! Form::open(['url' => action('ExpenseBeneficiaryController@store'), 'method' => 'post'])
                            !!}

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expense_category_id">@lang('lang.expense_category')</label>
                                        {!! Form::select('expense_category_id', $expense_categories, null, ['class' =>
                                        'form-control', 'required', 'placeholder' => __('lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">@lang('lang.beneficiary_name')</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-12">
                                    <input type="submit" class="btn btn-primary" value="@lang('lang.save')"
                                        name="submit">
                                </div>
                            </div>
                            {!! Form::close() !!}
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
