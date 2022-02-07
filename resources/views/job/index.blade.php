@extends('layouts.app')
@section('title', __('lang.jobs'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.jobs')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <button type="button" class="btn btn-primary btn-modal"
                            data-href="{{action('JobController@create')}}" data-container=".view_modal">
                            <i class="fa fa-plus"></i> @lang( 'lang.add_job' )</button>

                        <div class="col-sm-12">
                            <br>
                            <div class="table-responsive">
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.job_title')</th>
                                            <th>@lang('lang.date_of_creation')</th>
                                            <th>@lang('lang.name_of_creator')</th>
                                            <th class="notexport">@lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($jobs as $job)
                                        <tr>
                                            <td>
                                                {{$job->job_title}}
                                            </td>
                                            <td>
                                                {{@format_date($job->date_of_creation)}}
                                            </td>
                                            <td>
                                                {{$job->created_by}}
                                            </td>
                                            <td>

                                                @if(!in_array($job->job_title, ['Cashier', 'Deliveryman', 'Chef']) )
                                                @can('hr_management.jobs.create_and_edit')
                                                <a data-href="{{action('JobController@edit', $job->id)}}"
                                                    data-container=".view_modal"
                                                    class="btn btn-primary btn-modal text-white edit_job"><i
                                                        class="fa fa-pencil-square-o"></i></a>
                                                @endcan
                                                @can('hr_management.jobs.delete')
                                                <a data-href="{{action('JobController@destroy', $job->id)}}"
                                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                                    class="btn btn-danger text-white delete_item"><i
                                                        class="fa fa-trash"></i></a>
                                                @endcan
                                                @endif
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
</div>
@endsection

@section('javascript')
<script>

</script>
@endsection
