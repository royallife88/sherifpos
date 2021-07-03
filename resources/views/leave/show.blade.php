<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="leave">@lang('lang.leave')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {!! Form::open(['url' => action('LeaveController@store'), 'method' => 'post', 'enctype' =>
        'multipart/form-data']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employee_id">@lang('lang.employee'):</label>
                        {{$leave->name}}
                    </div>
                </div>
            </div>
            <div class="row mb-2 jobtypes">
                <h5 id="employee_name" class="col-md-6">@lang('lang.employee_name'): {{$leave->name}}</h5>
                <h5 id="joing_date" class="col-md-6">@lang('lang.joining_date'): {{@format_date($employee->date_of_start_working)}}</h5>
                <h5 id="job_title" class="col-md-6">@lang('lang.job_title'): {{$leave->job_title}}</h5>
                <h5 id="no_of_emplyee_same_job" class="col-md-6">@lang('lang.same_job_employee'): {{$no_of_emplyee_same_job}}</h5>
                <h5 id="leave_balance" class="col-md-6">@lang('lang.leave_balance'): {{$leave_balance}}</h5>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="leave_type_id">@lang('lang.leave_type'): </label>
                        {{$leave->leave_type}}
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="start_date">@lang('lang.start_date'): </label>{{@format_date($leave->start_date)}}
                </div>
                <div class="col-md-4">
                    <label for="end_date">@lang('lang.end_date'): </label>{{@format_date($leave->end_date)}}
                </div>
                <div class="col-md-4">
                    <label for="rejoining_date">@lang('lang.rejoining_date'):
                    </label>{{@format_date($leave->rejoining_date)}}
                </div>
                <div class="col-md-4">
                    <label for="paid_or_not_paid">@lang('lang.paid_not_paid'):
                    </label>@if($leave->paid_or_not_paid == 'paid') @lang('lang.paid') @endif @if($leave->paid_or_not_paid == 'not_paid') @lang('lang.not_paid') @endif
                </div>
                @if($leave->paid_or_not_paid == 'paid')
                <div class="col-md-4 if_paid">
                    <label for="amount_to_paid">@lang('lang.amount_to_paid'): </label>{{$leave->amount_to_paid}}
                </div>
                <div class="col-md-4 if_paid">
                    <label for="payment_date">@lang('lang.payment_date'): </label>{{@format_date($leave->payment_date)}}
                </div>
                @endif
                <br>
                @if(!empty($leave->upload_files))
                <div class="col-md-8">
                    <img height="300px" style="padding: 10px" src="{{asset('uploads/'.$leave->upload_files)}}" alt="">
                </div>
                @endif
                <div class="col-md-12">
                    <label for="details">@lang('lang.details'): </label> {{$leave->details}}
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>

</script>
