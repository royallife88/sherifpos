<?php

namespace App\Http\Controllers;

use App\Models\ContactUsDetail;
use App\Models\System;
use App\Utils\NotificationUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{


    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param NotificationUtils $notificationUtil
     * @return void
     */
    public function __construct(Util $commonUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->notificationUtil = $notificationUtil;
    }

    /**
     * create the contact us page
     *
     * @return \Illuminate\Http\Response
     */
    public function getContactUs()
    {
        return view('contact_us.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContactUs(Request $request)
    {
        try {
            $this->validate($request, [
                'country_code' => 'required',
                'phone_number' => 'required',
                'email' => 'required|email'
            ]);

            $contact_us = new ContactUsDetail();
            $contact_us->country_code = $request->country_code;
            $contact_us->phone_number = $request->phone_number;
            $contact_us->email = $request->email;
            $contact_us->message = $request->message;
            $contact_us->save();

            $data['country_code'] = $request->country_code;
            $data['phone_number'] = $request->phone_number;
            $data['message'] = $request->message;
            $data['email'] = $request->email;
            $data['email_body'] = view('contact_us.form_data')->with(compact(
                'data'
            ))->render();
            $this->notificationUtil->sendContactUs($data);


            $output = [
                'success' => true,
                'msg' => __('lang.your_message_sent_sccuessfully')
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
     * get contact us email only logged in user
     *
     * @return void
     */
    public function getUserContactUs()
    {
        return view('contact_us.user_contact_us');
    }

    /**
     * send contact us email only logged in user
     *
     * @return void
     */
    public function sendUserContactUs(Request $request)
    {
        try {

            $data['site_title'] = System::getProperty('site_title');
            $data['user_name'] = Auth::user()->name;
            $data['email'] = $request->email ?? Auth::user()->email;
            $data['phone_number'] = $request->phone_number;
            $data['message'] = $request->message;
            $data['email_body'] = view('contact_us.form_data')->with(compact(
                'data'
            ))->render();


            $data['files'] = $request->file('files', []);

            $this->notificationUtil->sendUserContactUs($data);


            $output = [
                'success' => true,
                'msg' => __('lang.your_message_sent_sccuessfully')
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
