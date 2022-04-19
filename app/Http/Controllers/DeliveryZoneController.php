<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Employee;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryZoneController extends Controller
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
        $delivery_zones = DeliveryZone::get();

        return view('delivery_zone.index')->with(compact(
            'delivery_zones'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;

        $delivery_zones = DeliveryZone::orderBy('name', 'asc')->pluck('name', 'id');
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');

        return view('delivery_zone.create')->with(compact(
            'quick_add',
            'delivery_zones',
            'deliverymen'
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

        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['cost' => ['required', 'numeric']],
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['deliveryman_id'] = $request->deliveryman_id ?? null;
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $delivery_zone = DeliveryZone::create($data);

            $delivery_zone_id = $delivery_zone->id;

            DB::commit();
            $output = [
                'success' => true,
                'delivery_zone_id' => $delivery_zone_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->quick_add) {
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

        $delivery_zone = DeliveryZone::find($id);
        $deliverymen = Employee::getDropdownByJobType('Deliveryman');

        return view('delivery_zone.edit')->with(compact(
            'delivery_zone',
            'deliverymen'
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
            ['cost' => ['required', 'numeric']],
        );

        try {
            $data = $request->except('_token', '_method');
            $data['deliveryman_id'] = $request->deliveryman_id ?? null;
            $data['edited_by'] = Auth::user()->id;

            DB::beginTransaction();
            DeliveryZone::where('id', $id)->update($data);
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
            DeliveryZone::find($id)->delete();
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
     * get details of resource
     *
     * @param int $id
     * @return void
     */
    public function getDetails($id)
    {
        $delivery_zone = DeliveryZone::find($id);

        return $delivery_zone;
    }
}
