@extends('layouts.app')
@section('title', __('lang.employee'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.add_new_employee')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form class="form-group" id="new_employee_form"
                                action="{{action('EmployeeController@store')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="fname">@lang('lang.full_name')</label>
                                        <input type="text" class="form-control" name="name" id="name" required
                                            placeholder="Enter Full Name">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="store_id">@lang('lang.store')</label>
                                        {!! Form::select('store_id', $stores, null, ['class' => 'form-control
                                        selectpicker',
                                        'placeholder'
                                        => __('lang.please_select'), 'data-live-search' => 'true', 'id' => 'store_id'])
                                        !!}
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="email">@lang('lang.email')</label>
                                        <input type="email" class="form-control" name="email" id="email" required
                                            placeholder="Enter Email Address">
                                    </div>
                                </div>

                                <div class="row mt-4">

                                    <div class="col-sm-6">
                                        <label for="password">@lang('lang.password')</label>
                                        <input type="password" class="form-control" name="password" id="password"
                                            required placeholder="Create New Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="pass">@lang('lang.confirm_password')</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required placeholder="Conform Password">
                                    </div>

                                </div>
                                <div class="row mt-4">

                                    <div class="col-sm-6">
                                        <label for="date_of_start_working">@lang('lang.date_of_start_working')</label>
                                        <input type="date_of_start_working" class="form-control"
                                            name="date_of_start_working" id="date_of_start_working"
                                            placeholder="@lang('lang.date_of_start_working')">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="date_of_birth">@lang('lang.date_of_birth')</label>
                                        <input type="date_of_birth" class="form-control" name="date_of_birth"
                                            id="date_of_birth" placeholder="@lang('lang.date_of_birth')">
                                    </div>

                                </div>
                                <div class="row mt-4">

                                    <div class="col-sm-6">
                                        <label for="job_type">@lang('lang.job_type')</label>
                                        {!! Form::select('job_type_id', $jobs, null, ['class' => 'form-control
                                        selectpicker',
                                        'placeholder'
                                        =>
                                        __('lang.select_job_type'), 'data-live-search' => 'true']) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="mobile">@lang('lang.mobile')</label>
                                        <input type="mobile" class="form-control" name="mobile" id="mobile" required
                                            placeholder="@lang('lang.mobile')">
                                    </div>

                                </div>
                                <div class="row mt-4">

                                    <div class="col-sm-6">
                                        <label for="upload_files">@lang('lang.upload_files')</label>
                                        {!! Form::file('upload_files[]', ['class' => 'form-control', 'multiple']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label for="photo">@lang('lang.profile_photo')</label>
                                        <input type="file" name="photo" id="photo" class="form-control" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    @foreach ($leave_types as $leave_type)
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="i-checks">
                                                <input id="number_of_leaves{{$leave_type->id}}"
                                                    name="number_of_leaves[{{$leave_type->id}}][enabled]"
                                                    type="checkbox" value="1" class="form-control-custom">
                                                <label
                                                    for="number_of_leaves{{$leave_type->id}}"><strong>{{$leave_type->name}}</strong></label>
                                                <input type="number" class="form-control"
                                                    name="number_of_leaves[{{$leave_type->id}}][number_of_days]"
                                                    id="number_of_leaves" placeholder="{{$leave_type->name}}" readonly
                                                    value="{{$leave_type->number_of_days_per_year}}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="row mt-4">
                                    <!-- Button trigger modal -->
                                    <button type="button" style="margin-left: 15px;" class="btn btn-primary"
                                        data-toggle="modal" data-target="#salary_details">
                                        @lang('lang.salary_details')
                                    </button>

                                    @include('employee.partial.salary_details')
                                </div>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label
                                            for="working_day_per_week">@lang('lang.select_working_day_per_week')</label>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>@lang('lang.check_in')</th>
                                                    <th> @lang('lang.check_out')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($week_days as $key => $week_day)
                                                <tr>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="i-checks">
                                                                <input id="working_day_per_week{{$key}}"
                                                                    name="working_day_per_week[{{$key}}]"
                                                                    type="checkbox" value="1"
                                                                    class="form-control-custom">
                                                                <label
                                                                    for="working_day_per_week{{$key}}"><strong>{{$week_day}}</strong></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {!! Form::text('check_in['.$key.']', null, ['class' =>
                                                        'form-control input-md
                                                        time_picker']) !!}

                                                    </td>
                                                    <td>
                                                        {!! Form::text('check_out['.$key.']', null, ['class' =>
                                                        'form-control input-md
                                                        time_picker']) !!}

                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h3>@lang('lang.user_rights')</h3>
                                    </div>
                                    <div class="col-md-12">
                                        @include('employee.partial.permission')
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-sm-12">
                                        <input type="submit" class="btn btn-primary" value="@lang('lang.save')"
                                            name="submit">
                                        <input type="submit" class="btn btn-primary"
                                            value="@lang('lang.send_credentials')" name="submit">
                                    </div>
                                </div>
                            </form>
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
    $('#date_of_start_working').datepicker();
        $('#date_of_birth').datepicker();

    $('#fixed_wage').change(function(){
        console.log($(this).prop('checked'));
    })

    $('.checked_all').change(function(){
        tr = $(this).closest('tr');
        var checked_all = $(this).prop('checked');
        console.log(checked_all);

        tr.find('.check_box').each(function(item) {
            if(checked_all === true){
                $(this).prop('checked', true)
            }else{
                $(this).prop('checked', false)
            }
        })
    })
</script>
@endsection
