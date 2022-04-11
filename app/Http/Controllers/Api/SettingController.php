<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = System::pluck('value', 'key');
        return $this->handleResponse($settings, 'Settings have been retrieved!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        try {
            if (!empty($input['logo'])) {
                $logo_image = file_get_contents($input['logo_url']);
                file_put_contents(public_path('uploads/' . $input['logo']), $logo_image);
                $setting['logo'] = System::updateOrCreate(['key' => 'logo'], ['value' => $input['logo']]);
            }
            if (!empty($input['sender_email'])) {
                $setting['sender_email'] = System::updateOrCreate(['key' => 'sender_email'], ['value' => $input['sender_email']]);
            }

            return $this->handleResponse($setting, 'Setting updated!');
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            return $this->handleError($e->getMessage(), [__('lang.something_went_wrong')], 503);
        }
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
}
