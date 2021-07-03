@extends('layouts.app')
@section('title', __('lang.leave_type'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.leave_type')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <button type="button" class="btn btn-primary btn-modal"
                            data-href="{{action('LeaveTypeController@create')}}" data-container=".view_modal">
                            <i class="fa fa-plus"></i> @lang( 'lang.add_leave_type' )</button>
                        <div class="col-sm-12">
                            <br>
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.type_name')</th>
                                        <th>@lang('lang.number_of_days_per_year')</th>
                                        <th>@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($leave_types as $leave_type)
                                    <tr>
                                        <td>
                                            {{$leave_type->name}}
                                        </td>
                                        <td>
                                            {{$leave_type->number_of_days_per_year}}
                                        </td>

                                        <td>
                                            @can('hr_management.leave_types.create_and_edit')
                                            <a data-href="{{action('LeaveTypeController@edit', $leave_type->id)}}"
                                                data-container=".view_modal"
                                                class="btn btn-primary btn-modal text-white edit_leave_type"><i
                                                    class="fa fa-pencil-square-o"></i></a>
                                            @endcan
                                            @can('hr_management.leave_types.delete')
                                            <a data-href="{{action('LeaveTypeController@destroy', $leave_type->id)}}"
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
