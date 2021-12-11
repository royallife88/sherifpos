@extends('layouts.app')
@section('title', __('lang.terms_and_conditions'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            @can('settings.terms_and_conditions.create_and_edit')
            <button type="button" class="btn btn-primary btn-modal ml-3"
                data-href="{{action('TermsAndConditionsController@create')}}?type={{$type}}"
                data-container=".view_modal">
                <i class="fa fa-plus"></i> @lang( 'lang.add_terms_and_conditions' )</button>
            @endcan
        </div>
        <div class="col-md-12">
            {!! Form::open(['url' => action('TermsAndConditionsController@updateInvoiceTacSetting'), 'method' => 'POST'])
            !!}
            <div class="row ml-2">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('invoice_terms_and_conditions', __('lang.tac_to_be_printed'), []) !!}
                        {!! Form::select('invoice_terms_and_conditions',
                        $tac,!empty($invoice_terms_and_conditions) ?
                        $invoice_terms_and_conditions : null, ['class' =>
                        'form-control selectpicker', 'data-live-search' => "true", 'placeholder' =>
                        __('lang.please_select')])
                        !!}
                    </div>
                </div>
                <div class="col-md-1" style="margin-top: 32px;">
                    <button class="btn btn-success" type="submit">@lang('lang.save')</button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>

        <div class="card-body">
            <div class="col-sm-12">
                <br>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>@lang('lang.name')</th>
                            <th>@lang('lang.description')</th>
                            <th>@lang('lang.name_of_creator')</th>
                            <th class="notexport">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($terms_and_conditions as $terms_and_condition)
                        <tr>
                            <td>
                                {{$terms_and_condition->name}}
                            </td>
                            <td>
                                {!! $terms_and_condition->description !!}
                            </td>
                            <td>
                                {{$terms_and_condition->created_by}}
                            </td>
                            <td>


                                @can('settings.terms_and_conditions.view')
                                <a data-href="{{action('TermsAndConditionsController@show', $terms_and_condition->id)}}"
                                    data-container=".view_modal"
                                    class="btn btn-danger btn-modal text-white show_terms_and_condition"><i
                                        class="fa fa-eye"></i></a>
                                @endcan
                                @can('settings.terms_and_conditions.create_and_edit')
                                <a data-href="{{action('TermsAndConditionsController@edit', $terms_and_condition->id)}}"
                                    data-container=".view_modal"
                                    class="btn btn-danger btn-modal text-white edit_terms_and_condition"><i
                                        class="fa fa-pencil-square-o"></i></a>
                                @endcan
                                @can('settings.terms_and_conditions.delete')
                                <a data-href="{{action('TermsAndConditionsController@destroy', $terms_and_condition->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn btn-danger text-white delete_item"><i class="fa fa-trash"></i></a>
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
@endsection

@section('javascript')
<script>
    $('.view_modal').on('hidden.bs.modal', function () {
        tinymce.remove();
    });
</script>
@endsection
