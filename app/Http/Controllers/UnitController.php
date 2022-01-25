<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
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
        $units = Unit::get();

        return view('unit.index')->with(compact(
            'units'
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
        $is_raw_material_unit = request()->is_raw_material_unit ?? 0;

        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');

        return view('unit.create')->with(compact(
            'quick_add',
            'is_raw_material_unit',
            'units'
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
            ['name' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['base_unit_multiplier'] = !empty($data['base_unit_multiplier']) ? $this->commonUtil->num_uf($data['base_unit_multiplier']) : 1;

            DB::beginTransaction();
            $unit = Unit::create($data);

            $unit_id = $unit->id;

            DB::commit();
            $output = [
                'success' => true,
                'unit_id' => $unit_id,
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
        $unit = Unit::find($id);

        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');

        return view('unit.edit')->with(compact(
            'unit',
            'units'
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
            ['name' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');
            $data['base_unit_multiplier'] = !empty($data['base_unit_multiplier']) ? $this->commonUtil->num_uf($data['base_unit_multiplier']) : 1;

            DB::beginTransaction();
            Unit::where('id', $id)->update($data);


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
            Unit::find($id)->delete();
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
     * get unit drop down list
     *
     * @return void
     */
    public function getDropdown()
    {
        $unit = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $unit_dp = $this->commonUtil->createDropdownHtml($unit, 'Please Select');

        return $unit_dp;
    }

    /**
     * get unit details
     *
     * @param int $id
     * @return void
     */
    public function getUnitDetails($id)
    {
        $unit = Unit::find($id);
        return ['unit' => $unit];
    }
}
