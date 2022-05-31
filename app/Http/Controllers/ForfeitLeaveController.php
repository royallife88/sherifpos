<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ForfeitLeave;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForfeitLeaveController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = ForfeitLeave::leftjoin('employees', 'forfeit_leaves.employee_id', 'employees.id')
        ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
        ->leftjoin('users', 'employees.user_id', 'users.id')
        ->leftjoin('users as cb', 'forfeit_leaves.created_by', 'cb.id')
        ->leftjoin('leave_types', 'forfeit_leaves.leave_type_id', 'leave_types.id');

    if (!empty(request()->start_date) && !empty(request()->end_date)) {
        $query->whereDate('start_date', '>=', request()->start_date);
        $query->whereDate('end_date', '<=', request()->end_date);
    }
    $forfeit_leaves = $query->select(
        'users.name',
        'employees.date_of_start_working',
        'employees.annual_leave_per_year',
        'employees.sick_leave_per_year',
        'job_types.job_title',
        'forfeit_leaves.*',
        'leave_types.name as leave_type_name',
        'cb.name as created_by'
    )->groupBy('forfeit_leaves.id')->get();

    return view('forfeit_leave.index')->with(compact(
        'forfeit_leaves',
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



        return view('forfeit_leave.create')->with(compact(
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
            $data  = $request->except('_token', 'upload_files');

            $data['created_by'] = Auth::user()->id;

            $forfeit_leave = ForfeitLeave::create($data);

            if ($request->hasFile('upload_files')) {
                $forfeit_leave->addMedia($request->file('upload_files'))->toMediaCollection('forfeit_leave');
            }

            $output = [
                'success' => true,
                'msg' => __('lang.success')
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getLeaveTypeBalanceForEmployee($employee_id, $leave_type_id){
        $balance_number_of_days = Employee::getBalanceLeaveByLeaveType($employee_id, $leave_type_id);

        return ['balance_number_of_days' => $balance_number_of_days];
    }
}
