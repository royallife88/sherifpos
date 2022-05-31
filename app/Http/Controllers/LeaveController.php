<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeaveController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $query = Leave::leftjoin('employees', 'leaves.employee_id', 'employees.id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
            ->leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('leave_types', 'leaves.leave_type_id', 'leave_types.id');

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $query->whereDate('start_date', '>=', request()->start_date);
            $query->whereDate('end_date', '<=', request()->end_date);
        }
        $leaves = $query->select(
            'users.name',
            'employees.date_of_start_working',
            'employees.annual_leave_per_year',
            'employees.sick_leave_per_year',
            'job_types.job_title',
            'leaves.*',
            'leave_types.name as leave_type_name'
        )->get();

        return view('leave.index')->with(compact(
            'leaves',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = Employee::leftjoin('users', 'employees.user_id', 'users.id');
        if (!auth()->user()->can('superadmin') || auth()->user()->is_admin != 1) {
            $query->where('users.id', Auth::user()->id);
        }
        $employees =  $query->pluck('users.name', 'employees.id');

        $this_employee = Employee::where('user_id', Auth()->user()->id)->first();

        $this_employee_id = null;
        if (!empty($this_employee)) {
            $this_employee_id = $this_employee->id;
        }
        if (!empty(request()->employee_id)) {
            //using from employee list page
            $this_employee_id = request()->employee_id;
        }

        $leave_types = LeaveType::orderBy('name', 'asc')->pluck('name', 'id');



        return view('leave.create')->with(compact(
            'employees',
            'this_employee_id',
            'leave_types'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->except('_token');
            $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
            $data['end_date'] = !empty($data['end_date']) ? $data['end_date'] : null;
            $data['rejoining_date'] = !empty($data['rejoining_date']) ? $data['rejoining_date'] : null;
            $data['payment_date'] = !empty($data['payment_date']) ? $data['payment_date'] : null;
            $data['number_of_days'] = Carbon::parse($data['start_date'])->diff(Carbon::parse($data['end_date']))->format('%d') + 1;
            $data['status'] = 'pending';
            $data['created_by'] = Auth::user()->id;

            $leave = Leave::create($data);
            if ($request->hasFile('upload_files')) {
                $leave->addMedia($request->file('upload_files'))->toMediaCollection('leave');
            }

            $output = [
                'success' => true,
                'msg' => __('lang.leave_submited')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave = Leave::leftjoin('employees', 'leaves.employee_id', 'employees.id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
            ->leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('leave_types', 'leaves.leave_type_id', 'leave_types.id')
            ->where('leaves.id', $id)
            ->select('leaves.*', 'users.name', 'leave_types.name as leave_type', 'job_types.job_title')
            ->first();

        $employee = Employee::find($leave->employee_id);
        $no_of_emplyee_same_job = Employee::where('job_type_id', $employee->job_type_id)->count();
        $leave_balance = Employee::getBalanceLeave($leave->employee_id);

        return view('leave.show')->with(compact(
            'leave',
            'leave_balance',
            'employee',
            'no_of_emplyee_same_job'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leave = Leave::find($id);
        $query = Employee::leftjoin('users', 'employees.user_id', 'users.id');
        if (!auth()->user()->can('superadmin') || auth()->user()->is_admin != 1) {
            $query->where('users.id', Auth::user()->id);
        }
        $employees =  $query->pluck('users.name', 'employees.id');

        $this_employee = Employee::where('user_id', Auth()->user()->id)->first();

        $this_employee_id = null;
        if (!empty($this_employee)) {
            $this_employee_id = $this_employee->id;
        }

        $leave_types = LeaveType::orderBy('name', 'asc')->pluck('name', 'id');

        return view('leave.edit')->with(compact(
            'employees',
            'this_employee_id',
            'leave_types',
            'leave'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
            $data['end_date'] = !empty($data['end_date']) ? $data['end_date'] : null;
            $data['rejoining_date'] = !empty($data['rejoining_date']) ? $data['rejoining_date'] : null;
            $data['payment_date'] = !empty($data['payment_date']) ? $data['payment_date'] : null;
            $data['number_of_days'] = Carbon::parse($data['start_date'])->diff(Carbon::parse($data['end_date']))->format('%d') + 1;

            $leave = Leave::find($id);
            $leave->update($data);
            if ($request->hasFile('upload_files')) {
                $leave->addMedia($request->file('upload_files'))->toMediaCollection('leave');
            }

            $output = [
                'success' => true,
                'msg' => __('lang.leave_updated')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Leave::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('lang.leave_deleted')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return $output;
    }

    public function getLeaveDetails($employee_id)
    {
        $employee = Employee::leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')->where('employees.id', $employee_id)
            ->select( 'users.name', 'employees.*', 'job_types.job_title')
            ->first();

        $query = Leave::leftjoin('leave_types', 'leaves.leave_type_id', 'leave_types.id')
            ->where('employee_id', $employee_id);

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $query->whereDate('leaves.start_date', '>=', request()->start_date);
            $query->whereDate('leaves.end_date', '<=', request()->end_date);
        }

        $leaves =  $query->select('start_date', 'end_date', 'leave_types.name as leave_type_name', 'leaves.number_of_days')->get();

        $attendance_leaves_query = Attendance::where('employee_id', $employee_id)->where('status', 'on_leave');

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $query->whereDate('attendances.date', '>=', request()->start_date);
            $query->whereDate('attendances.date', '<=', request()->end_date);
        }
        $attendance_leaves = $attendance_leaves_query->get();

        return view('leave.partials.leave_details')->with(compact(
            'employee',
            'leaves',
            'attendance_leaves'
        ));
    }
}
