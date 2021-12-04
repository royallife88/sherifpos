<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\System;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
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
        return view('settings.index');
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
        //
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

    public function getGeneralSetting()
    {
        $settings = System::pluck('value', 'key');
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        $currencies  = $this->commonUtil->allCurrencies();

        $timezone_list = $this->commonUtil->allTimeZones();

        return view('settings.general_setting')->with(compact(
            'settings',
            'currencies',
            'timezone_list',
            'languages'
        ));
    }
    public function updateGeneralSetting(Request $request)
    {
        try {
            System::updateOrCreate(
                ['key' => 'site_title'],
                ['value' => $request->site_title, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'developed_by'],
                ['value' => $request->developed_by, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'time_format'],
                ['value' => $request->time_format, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'timezone'],
                ['value' => $request->timezone, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'language'],
                ['value' => $request->language, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'currency'],
                ['value' => $request->currency, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'invoice_lang'],
                ['value' => $request->invoice_lang, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );
            System::updateOrCreate(
                ['key' => 'help_page_content'],
                ['value' => $request->help_page_content, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
            );

            if (!empty($request->language)) {
                session()->put('language', $request->language);
            }

            $data['letter_header'] = null;
            if ($request->hasFile('letter_header')) {
                $file = $request->file('letter_header');
                $data['letter_header'] = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $data['letter_header']);
            }
            $data['letter_footer'] = null;
            if ($request->hasFile('letter_footer')) {
                $file = $request->file('letter_footer');
                $data['letter_footer'] = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $data['letter_footer']);
            }
            $data['login_screen'] = null;
            if ($request->hasFile('login_screen')) {
                $file = $request->file('login_screen');
                $data['login_screen'] = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $data['login_screen']);
            }
            $data['logo'] = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $data['logo'] = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $data['logo']);
            }

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    System::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]
                    );
                }
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

    public function callTesting()
    {
        Artisan::call('migrate:reset', ['--force' => true]);
        print_r('done');
        die();
    }

    public function removeImage($type)
    {
        try {
            System::where('key', $type)->update(['value' => null]);
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
}
