<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="leave">@lang('lang.leave')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {!! Form::open(['url' => action('LeaveController@update', $leave->id), 'method' => 'put', 'enctype' =>
        'multipart/form-data']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employee_id">@lang('lang.employee')</label>
                        @if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1)
                        {!! Form::select('employee_id', $employees, $leave->employee_id, ['class' => 'form-control select2', 'id' =>
                        'employee_id', 'required', 'placeholder' => 'Please Select']) !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mb-2 jobtypes hide">
                <h5 id="employee_name" class="col-md-6"></h5>
                <h5 id="joing_date" class="col-md-6"></h5>
                <h5 id="job_title" class="col-md-6"></h5>
                <h5 id="no_of_emplyee_same_job" class="col-md-6"></h5>
                <h5 id="leave_balance" class="col-md-6"></h5>

                <div class="leave_balance_details col-md-12"></div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="leave_type_id">@lang('lang.select_type_of_leave')</label>
                        {!! Form::select('leave_type_id', $leave_types, $leave->leave_type_id, ['class' => 'form-control', 'required',
                        'placeholder' => 'Please Select']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="start_date">@lang('lang.start_date')</label>
                    <input class="form-control" type="date" value="{{$leave->start_date}}" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date">@lang('lang.end_date')</label>
                    <input class="form-control" type="date" value="{{$leave->end_date}}" id="end_date" name="end_date" required>
                </div>
                <div class="col-md-4">
                    <label for="rejoining_date">@lang('lang.rejoining_date')</label>
                    <input class="form-control" type="date" value="{{$leave->rejoining_date}}" id="rejoining_date" name="rejoining_date" required>
                </div>
                <div class="col-md-4">
                    <label for="paid_or_not_paid">@lang('lang.paid_not_paid')</label>
                    {!! Form::select('paid_or_not_paid', ['paid' => 'Paid', 'not_paid' => 'Not Paid'], $leave->paid_or_not_paid, ['class' =>
                    'form-control', 'placeholder' => 'Please Select', 'id' => 'paid_or_not_paid', 'required']) !!}
                </div>
                <div class="col-md-4 if_paid hide">
                    <label for="amount_to_paid">@lang('lang.amount_to_paid')</label>
                    {!! Form::text('amount_to_paid', $leave->amount_to_paid, ['class' => 'form-control', 'placeholder' =>
                    __('lang.amount_to_paid'), 'id' => 'amount_to_paid']) !!}
                </div>
                <div class="col-md-4 if_paid hide">
                    <label for="payment_date">@lang('lang.payment_date')</label>
                    {!! Form::text('payment_date', $leave->payment_date, ['class' => 'form-control datepicker', 'placeholder' =>
                    __('lang.payment_date'), 'id' => 'payment_date']) !!}
                </div>
                <div class="col-md-4 ">
                    <label for="status">@lang('lang.status')</label>
                    {!! Form::select('status', ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'], $leave->status, ['class' => 'form-control', 'placeholder' =>
                    __('lang.status'), 'id' => 'status']) !!}
                </div>

                <div class="col-md-4">
                    <label for="upload_files">@lang('lang.upload_files')</label><br>
                    {!! Form::file('upload_files', null, ['class' => 'form-control', 'placeholder' =>
                    __('lang.upload_files'), 'id' => 'upload_files']) !!}
                </div>
                @if(!empty($leave->upload_files))
                    <div class="col-md-8">
                        <img height="300px" style="padding: 10px" src="{{asset('uploads/'.$leave->upload_files)}}" alt="">
                    </div>
                @endif
                <div class="col-md-12">
                    <label for="details">@lang('lang.details')</label>
                    {!! Form::textarea('details', $leave->details, ['class' => 'form-control', 'rows' => 3, 'placeholder' =>
                    __('lang.details'), 'id' => 'details']) !!}
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $('#employee_id').change(function(){
        employee_id = $(this).val();
        if(employee_id != '' &&  employee_id != null &&  employee_id != undefined){
            $('.jobtypes').removeClass('hide');
        }else{
            $('.jobtypes').addClass('hide');
        }

        $.ajax({
            method: 'get',
            url: '/get-employee-details-by-id/'+employee_id,
            data: {  },
            success: function(result) {
                $('#employee_name').text('Name: '+result.employee.name);
                $('#joing_date').text('Joining Date: '+result.employee.date_of_start_working);
                $('#job_title').text('Job Type: ' + result.employee.job_title);
                $('#no_of_emplyee_same_job').html(`Number of colleagues in same job: <a style="cursor: pointer;" data-href="/get-same-job-employee-details/${employee_id}" data-container=".second_modal" class="btn-modal"> ${result.no_of_emplyee_same_job} </a>`) ;
                $('#leave_balance').text('Leave Balance: ' + result.leave_balance);
            },
        });
        $.ajax({
            method: 'get',
            url: '/get-balance-leave-details/'+employee_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('.leave_balance_details').empty().append(result);
            },
        });
    })

    $(document).ready(function(){
        $('#employee_id').change()
    })

    $('#paid_or_not_paid').change(function(){
        if($(this).val() === 'paid'){
            $('#amount_to_paid').attr('required', true);
            $('#payment_date').attr('required', true);
            $('.if_paid').removeClass('hide');
        }else{
            $('#amount_to_paid').attr('required', false);
            $('#payment_date').attr('required', false);
            $('.if_paid').addClass('hide');
        }
    })
</script>
