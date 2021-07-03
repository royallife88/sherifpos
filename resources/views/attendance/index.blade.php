@extends('layouts.app')
@section('title', __('lang.forfeit_leaves'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.attendance_list')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <a class="btn btn-primary ml-3" href="{{action('AttendanceController@create')}}"><i
                                class="fa fa-plus"></i>
                            @lang(
                            'lang.attendance' )</a>

                        <div class="col-sm-12">
                            <br>
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.employee_name')</th>
                                        <th>@lang('lang.check_in_time')</th>
                                        <th>@lang('lang.check_out_time')</th>
                                        <th>@lang('lang.status')</th>
                                        <th>@lang('lang.created_by')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($attendances as $attendance)
                                    <tr>
                                        <td>
                                            {{@format_date($attendance->date)}}
                                        </td>
                                        <td>
                                            {{$attendance->employee_name}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($attendance->check_in)->format('h:i:s A')}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($attendance->check_out)->format('h:i:s A')}}
                                        </td>
                                        <td>
                                            <span
                                                class="badge @attendance_status($attendance->status)">{{__('lang.' . $attendance->status)}}</span>
                                            @if($attendance->status == 'late')
                                            @php
                                            $check_in_data = [];
                                            $employee = App\Models\Employee::find($attendance->employee_id);
                                            if(!empty($employee)){
                                            $check_in_data = $employee->check_in;
                                            }
                                            $day_name =
                                            Illuminate\Support\Str::lower(\Carbon\Carbon::parse($attendance->date)->format('l'));
                                            $late_time = 0;
                                            if(!empty($check_in_data[$day_name])){
                                            $check_in_time =$check_in_data[$day_name];
                                            $late_time =
                                            \Carbon\Carbon::parse($attendance->check_in)->diffInMinutes($check_in_time);
                                            }
                                            @endphp
                                            @if($late_time > 0)
                                            +{{$late_time}}
                                            @endif
                                            @endif
                                            @if($attendance->status == 'on_leave')
                                            @php
                                            $leave = App\Models\Leave::leftjoin('leave_types', 'leave_type_id',
                                            'leave_types.id')
                                            ->where('employee_id', $attendance->employee_id)
                                            ->where('start_date', '>=', $attendance->date)
                                            ->where('start_date', '<=', $attendance->date)
                                                ->select('leave_types.name')
                                                ->first()
                                                @endphp
                                                @if(!empty($leave))
                                                {{$leave->name}}
                                                @endif
                                                @endif
                                        </td>
                                        <td>
                                            {{ucfirst($attendance->created_by)}}
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
