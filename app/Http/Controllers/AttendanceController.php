<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::leftjoin('employees', 'attendances.employee_id', 'employees.id')
            ->leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('users as created_by', 'attendances.created_by', 'created_by.id')
            ->select(
                'attendances.*',
                'users.name as employee_name',
                'created_by.name as created_by'
            )->orderBy('attendances.id', 'desc')
            ->get();

        return view('attendance.index')->with(compact(
            'attendances'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->select('users.name', 'employees.id')->pluck('name', 'id');

        return view('attendance.create')->with(compact(
            'employees'
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
            $attendances = $request->attendances;
            DB::beginTransaction();
            foreach ($attendances as $attendance) {
                $data = [
                    'date' => $attendance['date'],
                    'employee_id' => $attendance['employee_id'],
                    'check_in' => $attendance['check_in'],
                    'check_out' => $attendance['check_out'],
                    'status' => $attendance['status'],
                    'created_by' => Auth::user()->id
                ];

                if ($attendance['status'] == 'on_leave') {
                    $employee = Employee::find($attendance['employee_id']);
                    $employee->number_of_days_any_leave_added = $employee->number_of_days_any_leave_added + 1;
                    $employee->save();
                }

                Attendance::create($data);
            }
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang.attendance_added')
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

    public function getAttendanceRow($row_index)
    {
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->select('users.name', 'employees.id')->pluck('name', 'id');

        return view('attendance.partials.attendance_row')->with(compact(
            'employees',
            'row_index'
        ));
    }
}
