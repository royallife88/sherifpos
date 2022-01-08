@extends('layouts.app')
@section('title', __('lang.forfeit_leaves'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" style="text-align: center;">
            <h2 class="mb-4">@lang('lang.list_of_employees_in_forfeit_leave')</h2>
        </div>
    </div>
    <div class="row" id="sales">

        <br>
        <div class="col-md-12">
            <form action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('start_date', __('lang.start_date'), []) !!}
                            {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('end_date', __('lang.end_date'), []) !!}
                            {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <br>
                        <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                        <a href="{{action('ForfeitLeaveController@index')}}"
                            class="btn btn-success mt-2">@lang('lang.clear_filter')</a>
                    </div>

                </div>
            </form>
        </div>
        <div class="col-sm-12">
            <br>
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>@lang('lang.employee_name')</th>
                        <th>@lang('lang.leave_type')</th>
                        <th>@lang('lang.year')</th>
                        <th>@lang('lang.number_of_days')</th>
                        <th>@lang('lang.who_forfeited')</th>
                        <th>@lang('lang.upload_files')</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($forfeit_leaves as $leave)
                    <tr>
                        <td>
                            {{$leave->name}}
                        </td>
                        <td>
                            {{$leave->leave_type_name}}
                        </td>
                        <td>
                            {{$leave->start_date}}
                        </td>
                        <td>
                            {{@num_format($leave->number_of_days)}}
                        </td>
                        <td>{{ucfirst($leave->created_by)}}</td>
                        <td>
                            <a data-href="{{action('GeneralController@viewUploadedFiles', ['model_name' => 'ForfeitLeave', 'model_id' => $leave->id, 'collection_name' => 'forfeit_leave'])}}"
                                data-container=".view_modal"
                                class="btn btn-danger btn-modal text-white">@lang('lang.view')</a>
                        </td>
                    </tr>

                    @endforeach


                </tbody>

            </table>

        </div>
    </div>
</div>
<div class="modal fade second_modal" role="dialog" aria-hidden="true"></div>

@endsection

@section('javascript')
<script>

</script>
@endsection
