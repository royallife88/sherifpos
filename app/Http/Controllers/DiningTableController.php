<?php

namespace App\Http\Controllers;

use App\Models\DiningRoom;
use App\Models\DiningTable;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiningTableController extends Controller
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
        $dining_tables = DiningTable::get();

        return view('dining_table.index')->with(compact(
            'dining_tables'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $from_setting = !empty(request()->from_setting) ? true : false;
        $dining_room = DiningRoom::find(request()->room_id);
        $dining_rooms = DiningRoom::pluck('name', 'id');

        return view('dining_table.create')->with(compact(
            'dining_room',
            'dining_rooms',
            'from_setting',
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:dining_tables,name',
            'dining_room_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => false,
                'msg' => $validator->getMessageBag()->first()
            ];
            return $output;
        }
        try {
            $data = $request->only('name', 'dining_room_id');
            $data['status'] = 'available';
            $dining_table = DiningTable::create($data);
            $output = [
                'success' => true,
                'dining_table_id' => $dining_table->id,
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

        $dining_rooms = DiningRoom::pluck('name', 'id');
        $dining_table = DiningTable::find($id);

        return view('dining_table.edit')->with(compact(
            'dining_rooms',
            'dining_table',
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
            DiningTable::where('id', $id)->update($data);


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
            DiningTable::find($id)->delete();
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

    public function checkDiningTableName(Request $request)
    {
        $name = $request->name;

        $dining_table = DiningTable::where('name', $name)->first();

        if ($dining_table) {
            $output = [
                'success' => false,
                'msg' => __('lang.dining_table_name_already_exist')
            ];
            return $output;
        }
    }

    /**
     * get the table action modal
     *
     * @param int $id
     * @return void
     */
    public function getDiningAction($id)
    {
        $dining_table = DiningTable::find($id);
        $status_array = ['order' => __('lang.order'), 'reserve' => __('lang.reserve')];
        if ($dining_table->status == 'reserve') {
            $status_array = ['order' => __('lang.order'), 'cancel_reservation' => __('lang.cancel_reservation')];
        }

        return view('sale_pos.partials.dining_table_action')->with(compact(
            'dining_table',
            'status_array',
        ));
    }
    /**
     * update table data
     *
     * @param int $id
     * @return void
     */
    public function updateDiningTableData($id, Request $request)
    {
        $data = $request->except('_token');

        try {
            $dining_table = DiningTable::find($id);
            if ($data['status'] == 'reserve') {
                if (!empty($data['customer_name'])) {
                    $dining_table->customer_name = $data['customer_name'];
                }
                if (!empty($data['customer_mobile_number'])) {
                    $dining_table->customer_mobile_number = $data['customer_mobile_number'];
                }
                if (!empty($data['status'])) {
                    $dining_table->status = $data['status'];
                }
                if (!empty($data['date_and_time'])) {
                    $dining_table->date_and_time = Carbon::createFromTimestamp(strtotime($data['date_and_time']))->format('Y-m-d H:i:s');
                }
            }
            if ($data['status'] == 'cancel_reservation') {
                $dining_table->customer_name = null;
                $dining_table->customer_mobile_number = null;
                $dining_table->date_and_time = null;
                $dining_table->status = 'available';
            }
            $dining_table->save();


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
     * get the table details
     *
     * @param int $id
     * @return void
     */
    public function getTableDetails($id)
    {
        $dining_table = DiningTable::find($id);
        $dining_room = DiningRoom::find($dining_table->dining_room_id);

        return [
            'dining_table' => $dining_table,
            'dining_room' => $dining_room
        ];
    }

    /**
     * get dropdown by dining room
     *
     * @param int $id
     * @return void
     */
    public function getDropdownByDiningRoom($id)
    {
        $dining_tables = DiningTable::where('dining_room_id', $id)->pluck('name', 'id');

        return $this->commonUtil->createDropdownHtml($dining_tables, __('lang.all'));
    }
}
