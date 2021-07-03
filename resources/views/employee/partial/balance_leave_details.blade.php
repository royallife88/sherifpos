<div class="row">
    @foreach ($leaves_details as $leaves_detail)
    <div class="col-md-3">
        <label>{{$leaves_detail->name }}: </label> {{App\Models\Employee::getBalanceLeaveByLeaveType($leaves_detail->employee_id, $leaves_detail->leave_type_id)}}
    </div>

    @endforeach
</div>
