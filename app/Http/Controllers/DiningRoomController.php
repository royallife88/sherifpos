<?php

namespace App\Http\Controllers;

use App\Models\DiningRoom;
use App\Models\DiningTable;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiningRoomController extends Controller
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
        $dining_rooms = DiningRoom::get();

        return view('dining_room.index')->with(compact(
            'dining_rooms'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dining_room.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:dining_rooms,name'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return $output;
        }
        try {
            $data = $request->only('name');

            $dining_room = DiningRoom::create($data);
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

        if ($request->ajax()) {
            return $output;
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

        $dining_room = DiningRoom::find($id);

        return view('dining_room.edit')->with(compact(
            'dining_room'
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
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            DiningRoom::where('id', $id)->update($data);


            DB::commit();
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
            DiningRoom::find($id)->delete();
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
     * Retrieve the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiningRooms()
    {
        //
    }

    public function getDiningContent(Request $request)
    {
        $dining_rooms = DiningRoom::all();

        $active_tab_id = null;
        $dining_table_id = $request->dining_table_id;
        if (!empty($dining_table_id)) {
            $dining_table = DiningTable::find($dining_table_id);
            $active_tab_id = $dining_table->dining_room_id;
        }

        return view('sale_pos.partials.dining_content')->with(compact(
            'dining_rooms',
            'active_tab_id',
        ));
    }

    public function checkDiningRoomName(Request $request)
    {
        $name = $request->name;

        $dining_room = DiningRoom::where('name', $name)->first();

        if ($dining_room) {
            $output = [
                'success' => false,
                'msg' => __('lang.dining_room_name_already_exist')
            ];
            return $output;
        }
    }


    public function getDiningModal()
    {
        $dining_rooms = DiningRoom::all();
        $active_tab_id = null;

        return view('sale_pos.partials.dining_modal')->with(compact(
            'dining_rooms',
            'active_tab_id'
        ));
    }
}
