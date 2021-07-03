@extends('layouts.app')
@section('title', __('lang.attendance'))

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.attendance')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            {!! Form::open(['url' => action('AttendanceController@store'), 'method' => 'post']) !!}
                            <input type="hidden" name="index" id="index" value="0">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary add_row" id="add_row">+
                                        @lang('lang.add_row')</button>
                                </div>
                            </div>
                            <br>
                            <table class="table" id="attendance_table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.employee')</th>
                                        <th>@lang('lang.checkin')</th>
                                        <th>@lang('lang.checkout')</th>
                                        <th>@lang('lang.status')</th>
                                        <th>@lang('lang.created_by')</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <tr>
                                        <td><input type="date" class="form-control date" name="attendances[0][date]"
                                                required></td>
                                        <td>
                                            {!! Form::select('attendances[0][employee_id]', $employees, null , ['class'
                                            => 'form-control selectpicker', 'placeholder' => __('lang.please_select'),
                                            'data-live-search' => 'true', 'required']) !!}
                                        </td>
                                        <td>
                                            <input type="time" class="form-control time" name="attendances[0][check_in]"
                                                required>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control time"
                                                name="attendances[0][check_out]" required>
                                        </td>
                                        <td>
                                            {!! Form::select('attendances[0][status]', ['present' => 'Present', 'late'
                                            => 'Late', 'on_leave' => 'On Leave'], null , ['class' => 'form-control
                                            selectpicker', 'data-live-search' => 'true', 'placeholder' =>
                                            __('lang.please_select'), 'required']) !!}
                                        </td>
                                        <td>
                                            {{ucfirst(Auth::user()->name)}}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
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
    $('#add_row').click(function(){
        row_index = parseInt($('#index').val());
        row_index = row_index + 1;
        $('#index').val(row_index);
        $.ajax({
            method: 'get',
            url: '/hrm/attendance/get-attendance-row/'+row_index,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('#attendance_table tbody').append(result);
            },
        });
    })
</script>
@endsection
