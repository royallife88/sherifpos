<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Sms;
use App\Models\Supplier;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sms = Sms::leftjoin('users', 'sms.created_by', 'users.id')->select('sms.*', 'users.name as sent_by')->get();


        return view('sms.index')->with(compact(
            'sms'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->select('name', 'mobile')->pluck('name', 'mobile')->toArray();
        $customers = Customer::select('name', 'mobile_number as mobile')->pluck('name', 'mobile')->toArray();
        $suppliers = Supplier::select('name', 'mobile_number as mobile')->pluck('name', 'mobile')->toArray();

        $employee_mobile_number = null;
        if (!empty(request()->employee_id)) {
            $employee = Employee::find(request()->employee_id);
            if (!empty($employee)) {
                $employee_mobile_number = $employee->mobile;
            }
        }
        $customer_mobile_number = null;
        if (!empty(request()->customer_id)) {
            $customer = Customer::find(request()->customer_id);
            if (!empty($customer)) {
                $customer_mobile_number = $customer->mobile;
            }
        }
        $supplier_mobile_number = null;
        if (!empty(request()->supplier_id)) {
            $supplier = Supplier::find(request()->supplier_id);
            if (!empty($supplier)) {
                $supplier_mobile_number = $supplier->mobile;
            }
        }

        return view('sms.create')->with(compact(
            'employees',
            'customers',
            'suppliers',
            'employee_mobile_number',
            'customer_mobile_number',
            'supplier_mobile_number',
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
            $mobile_numbers = explode(',', $request->to);

            $data['message'] = urlencode($request->message);
            foreach ($mobile_numbers as $number) {
                $data['mobile_number'] = $number;
                dispatch(new SendSmsJob($data));
            }


            $sms_data['mobile_numbers'] =  $request->to;
            $sms_data['message'] =  $request->message;
            $sms_data['notes'] =  $request->notes;
            $sms_data['created_by'] =  Auth::user()->id;

            Sms::create($sms_data);

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
        $sms = Sms::find($id);
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->pluck('employee_name', 'mobile');

        return view('sms.edit')->with(compact(
            'sms',
            'employees'
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
            $mobile_numbers = explode(',', $request->to);

            $data['message'] = urlencode($request->message);
            foreach ($mobile_numbers as $number) {
                $data['mobile_number'] = $number;
                dispatch(new SendSmsJob($data));
            }


            $sms_data['mobile_numbers'] =  $request->to;
            $sms_data['message'] =  $request->message;
            $sms_data['notes'] =  $request->notes;

            Sms::where('id', $id)->update($sms_data);

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Sms::find($id)->delete();
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

        return $output;
    }

    /**
     * get the sms setting from storage
     *
     * @return void
     */
    public function getSetting()
    {

        $settings['sms_username'] = System::getProperty('sms_username');
        $settings['sms_password'] = System::getProperty('sms_password');
        $settings['sms_sender_name'] = System::getProperty('sms_sender_name');


        return view('sms.setting')->with(compact(
            'settings'
        ));
    }

    /**
     * save the sms setting from storage
     *
     * @return void
     */
    public function saveSetting(Request $request)
    {

        try {
            $settings['sms_username'] = System::saveProperty('sms_username', $request->sms_username);
            $settings['sms_password'] = System::saveProperty('sms_password', $request->sms_password);
            $settings['sms_sender_name'] = System::saveProperty('sms_sender_name', $request->sms_sender_name);

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

    public function resend($id)
    {
        try {
            $sms = Sms::find($id);


            $mobile_numbers = explode(',', $sms->mobile_numbers);

            $data['message'] = urlencode($sms->message);
            foreach ($mobile_numbers as $number) {
                $data['mobile_number'] = $number;
                dispatch(new SendSmsJob($data));
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
}
