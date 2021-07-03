@extends('layouts.app')
@section('title', __('lang.employee'))

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.edit_employee')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('EmployeeController@update', $employee->id), 'method' => 'put', 'id'
                    =>
                    'edit_employee_form', 'enctype' => 'multipart/form-data' ]) !!}

                    <div class="row">

                        <div class="col-sm-6">
                            <label for="fname">@lang('lang.full_name')</label>
                            <input type="text" class="form-control" name="name" value="{{$employee->name}}" id="name"
                                required placeholder="Enter Full Name">
                        </div>
                        <div class="col-sm-6">
                            <label for="store_id">@lang('lang.store')</label>
                            {!! Form::select('store_id', $stores, $employee->store_id, ['class' => 'form-control
                            selectpicker',
                            'placeholder'
                            => __('lang.please_select'), 'data-live-search' => 'true', 'id' => 'store_id']) !!}
                        </div>
                        <div class="col-sm-6">
                            <label for="email">@lang('lang.email')</label>
                            <input type="email" class="form-control" name="email" value="{{$employee->email}}"
                                id="email" required placeholder="Enter Email Address">
                        </div>

                    </div>

                    <div class="row mt-4">

                        <div class="col-sm-6">
                            <label for="password">@lang('lang.password')</label>
                            <input type="password" class="form-control" name="password" id="password"
                                placeholder="Create New Password">
                        </div>
                        <div class="col-sm-6">
                            <label for="pass">@lang('lang.confirm_password')</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Conform Password">
                        </div>

                    </div>
                    <div class="row mt-4">

                        <div class="col-sm-6">
                            <label for="date_of_start_working">@lang('lang.date_of_start_working')</label>
                            <input type="date_of_start_working" class="form-control" name="date_of_start_working"
                                value="@if(!empty($employee->date_of_start_working)){{@format_date($employee->date_of_start_working)}}@endif"
                                id="date_of_start_working" placeholder="@lang('lang.date_of_start_working')">
                        </div>
                        <div class="col-sm-6">
                            <label for="date_of_birth">@lang('lang.date_of_birth')</label>
                            <input type="date_of_birth" class="form-control" name="date_of_birth" id="date_of_birth"
                                value="@if(!empty($employee->date_of_birth)){{@format_date($employee->date_of_birth)}}@endif"
                                placeholder="@lang('lang.date_of_birth')">
                        </div>

                    </div>
                    <div class="row mt-4">

                        <div class="col-sm-6">
                            <label for="job_type">@lang('lang.job_type')</label>
                            {!! Form::select('job_type_id', $jobs, $employee->job_type_id, ['class' => 'form-control',
                            'placeholder' =>
                            __('lang.select_job_type')])
                            !!}
                        </div>
                        <div class="col-sm-6">
                            <label for="mobile">@lang('lang.mobile')</label>
                            <input type="mobile" class="form-control" name="mobile" id="mobile" required
                                value="{{$employee->mobile}}" placeholder="@lang('lang.mobile')">
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
                        @foreach ($number_of_leaves as $number_of_leave)
                        <div class="col-sm-6">
                            <div class="i-checks">
                                <input id="number_of_leaves{{$number_of_leave->id}}"
                                    name="number_of_leaves[{{$number_of_leave->id}}][enabled]"
                                    @if($number_of_leave->enabled == 1)
                                checked @endif
                                type="checkbox" value="1" class="form-control-custom">
                                <label
                                    for="number_of_leaves{{$number_of_leave->id}}"><strong>{{$number_of_leave->name}}</strong></label>
                                <input type="number" class="form-control"
                                    name="number_of_leaves[{{$number_of_leave->id}}][number_of_days]"
                                    id="number_of_leaves" readonly placeholder="{{$number_of_leave->name}}"
                                    value="{{$number_of_leave->number_of_days}}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row mt-4">
                        <!-- Button trigger modal -->
                        <button type="button" style="margin-left: 15px;" class="btn btn-primary" data-toggle="modal"
                            data-target="#salary_details">
                            @lang('lang.salary_details')
                        </button>

                        @include('employee.partial.salary_details')
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <label for="working_day_per_week">@lang('lang.select_working_day_per_week')</label>
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
                                                        @if(!empty($employee->working_day_per_week[$key]))
                                                    checked @endif name="working_day_per_week[{{$key}}]"
                                                    type="checkbox" value="1" class="form-control-custom">
                                                    <label
                                                        for="working_day_per_week{{$key}}"><strong>{{$week_day}}</strong></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('check_in['.$key.']', !empty($employee->check_in[$key]) ?
                                            $employee->check_in[$key] :
                                            null, ['class' => 'form-control input-md time_picker']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('check_out['.$key.']', !empty($employee->check_out[$key]) ?
                                            $employee->check_out[$key] : null, ['class' => 'form-control input-md
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
                            {{-- @include('employee.partial.permission') --}}
                        </div>
                    </div>

                    <div class="row mt-4">

                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary" id="update_employee_btn">@lang(
                                'lang.update_employee'
                                )</button>
                        </div>

                    </div>
                    {!! Form::close() !!}

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
