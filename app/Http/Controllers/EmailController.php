<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailsJob;
use App\Models\Email;
use App\Models\Employee;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emails = Email::leftjoin('users', 'emails.created_by', 'users.id')->select('emails.*', 'users.name as sent_by')->get();


        return view('email.index')->with(compact(
            'emails'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->pluck('name', 'email');
        $email = null;
        if(!empty(request()->employee_id)){
            $employee = Employee::leftjoin('users', 'employees.user_id', 'users.id')->where('employees.id', request()->employee_id)->first();
            if(!empty($employee)){
                $email = $employee->email;
            }
        }

        return view('email.create')->with(compact(
            'employees',
            'email'
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
            $emails = explode(',', $request->to);
            $data["subject"] = $request->subject;
            $data["body"] = $request->body;
            $files = [];
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move(public_path() . '/emails/', $name);
                    $files[] = public_path() . '/emails/' . $name;
                    $attachments[] = '/emails/' . $name;
                }
            }

            $from = System::getProperty('sender_email');

            foreach ($emails as $email) {
                $data["email"] = trim($email);

                dispatch(new SendEmailsJob($data, $files, $from));
            }
            $email_data['emails'] =  $request->to;
            $email_data['subject'] =  $request->subject;
            $email_data['body'] =  $request->body;
            $email_data['attachments'] =  $attachments;
            $email_data['notes'] =  $request->notes;
            $email_data['created_by'] =  Auth::user()->id;

            Email::create($email_data);


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
        $email = Email::find($id);
        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')->pluck('name', 'email');

        return view('email.edit')->with(compact(
            'email',
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
            $edit_email = Email::find($id);
            $emails = explode(',', $request->to);
            $data["subject"] = $request->subject;
            $data["body"] = $request->body;
            $files = [];
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move(public_path() . '/emails/', $name);
                    $files[] = public_path() . '/emails/' . $name;
                    $attachments[] = '/emails/' . $name;
                }
            } else {
                $atts = $edit_email->attachments;
                foreach ($atts as $att) {
                    $files[] = public_path() . $att;
                    $attachments[] = $att;
                }
            }
            $from = System::getProperty('sender_email');

            foreach ($emails as $email) {
                $data["email"] = trim($email);

                dispatch(new SendEmailsJob($data, $files, $from));
            }
            $email_data['emails'] =  $request->to;
            $email_data['subject'] =  $request->subject;
            $email_data['body'] =  $request->body;
            $email_data['attachments'] =  $attachments;
            $email_data['notes'] =  $request->notes;
            $email_data['created_by'] =  Auth::user()->id;

            $edit_email->update($email_data);


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
            Email::find($id)->delete();
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

        $settings['sender_email'] = System::getProperty('sender_email');


        return view('email.setting')->with(compact(
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
            $settings['sender_email'] = System::saveProperty('sender_email', $request->sender_email);

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
            $email = Email::find($id);


            $emails = explode(',', $email->emails);
            $data["subject"] = $email->subject;
            $data["body"] = $email->body;
            $files = [];
            $attachments = [];

            $atts = $email->attachments;
            foreach ($atts as $att) {
                $files[] = public_path() . $att;
                $attachments[] = $att;
            }

            $from = System::getProperty('sender_email');
            foreach ($emails as $email) {
                $data["email"] = trim($email);

                dispatch(new SendEmailsJob($data, $files, $from));
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
