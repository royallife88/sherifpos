<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="leave_details">@lang('lang.leave_details')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <label for=""> @lang('lang.employee_name'): {{$employee->name}}</label>
                        </div>
                        <div class="col-md-6">
                            <label for=""> @lang('lang.job_title'): {{$employee->job_title}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('lang.leave_type')</th>
                            <th>@lang('lang.start_date')</th>
                            <th>@lang('lang.end_date')</th>
                            <th>@lang('lang.number_of_days')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaves as $leave)
                        <tr>
                            <td>
                                {{$leave->leave_type_name}}
                            </td>
                            <td>
                                {{@format_date($leave->start_date)}}
                            </td>
                            <td>
                                {{@format_date($leave->end_date)}}
                            </td>
                            <td>{{$leave->number_of_days}}</td>
                        </tr>

                        @endforeach
                        @foreach ($attendance_leaves as $attendance_leave)
                        <tr>
                            <td>
                                @lang('lang.attendance')
                            </td>
                            <td>
                                {{@format_date($attendance_leave->date)}}
                            </td>
                            <td>
                                {{@format_date($attendance_leave->date)}}
                            </td>
                            <td>1</td>
                        </tr>

                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
