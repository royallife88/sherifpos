<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutorials = Tutorial::get();

        return view('tutorial.index')->with(compact(
            'tutorials'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tutorial.create');
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
            $data['name'] = $request->name;
            $data['description'] = $request->description;

            $tutorial = Tutorial::create($data);

            if ($request->video) {
                $tutorial->addMedia($request->video)->toMediaCollection('tutorial');
            }
            if ($request->thumbnail) {
                $tutorial->addMedia($request->thumbnail)->toMediaCollection('thumbnail');
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
        $tutorial = Tutorial::find($id);

        return view('tutorial.edit')->with(compact(
            'tutorial'
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
        // try {
        $data['name'] = $request->name;
        $data['description'] = $request->description;
        $tutorial = Tutorial::where('id', $id)->first();
        $tutorial->update($data);

        if ($request->video) {
            $tutorial->clearMediaCollection('tutorial');
            $tutorial->addMedia($request->video)->toMediaCollection('tutorial');
        }
        if ($request->thumbnail) {
            $tutorial->clearMediaCollection('thumbnail');
            $tutorial->addMedia($request->thumbnail)->toMediaCollection('thumbnail');
        }

        $output = [
            'success' => true,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }

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
            $tutorial = Tutorial::find($id);
            $tutorial->clearMediaCollection('tutorial');
            $tutorial->clearMediaCollection('thumbnail');
            $tutorial->delete();
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
     * get tutorials data array
     *
     * @return array
     */
    public function getTutorialsDataArray()
    {
        $tutorials = Tutorial::get();
        $tutorialsDataArray = [];
        foreach ($tutorials as $tutorial) {
            $tutorialsDataArray[] = [
                'id' => $tutorial->id,
                'name' => $tutorial->name,
                'description' => $tutorial->description,
                'video' => $tutorial->getFirstMediaUrl('tutorial'),
                'thumbnail' => $tutorial->getFirstMediaUrl('thumbnail'),
            ];
        }
        return response()->json($tutorialsDataArray, 200);
    }

    /**
     * get tutorials data array
     *
     * @return array
     */
    public function getTutorialsGuide()
    {
        $tutorials = Tutorial::get();
        $tutorialsDataArray = [];

        $url = System::getProperty('tutorial_guide_url');

        $client = new \GuzzleHttp\Client();
        $res = $client->get($url . '/api/tutorials/get-tutorials-data-array');

        if ($res->getStatusCode() == 200) {
            $tutorialsDataArray = json_decode($res->getBody(), true);
        }

        return view('tutorial.guide')->with(compact(
            'tutorialsDataArray'
        ));
    }
}