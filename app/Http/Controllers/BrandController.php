<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
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
        $brands = Brand::get();

        return view('brand.index')->with(compact(
            'brands'
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

        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');

        return view('brand.create')->with(compact(
            'quick_add',
            'brands'
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

        $brand_exist = Brand::where('name', $request->name)->first();

        if (!empty($brand_exist)) {
            if ($request->ajax()) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'msg' => 'Brand name already taken'
                ));
            }
        }

        try {
            $data = $request->except('_token', 'quick_add');
            DB::beginTransaction();
            $brand = Brand::create($data);


            if ($request->has('uploaded_image_name')) {
                if (!empty($request->input('uploaded_image_name'))) {
                    $brand->addMediaFromDisk($request->input('uploaded_image_name'), 'temp')->toMediaCollection('brand');
                }
            }

            $brand_id = $brand->id;

            DB::commit();
            $output = [
                'success' => true,
                'brand_id' => $brand_id,
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
        $brand = Brand::find($id);
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');

        return view('brand.edit')->with(compact(
            'brand',
            'brands',
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

            DB::beginTransaction();
            $brand = Brand::find($id);

            $brand->update($data);

            if ($request->has('uploaded_image_name')) {
                if (!empty($request->input('uploaded_image_name'))) {
                    $brand->clearMediaCollection('brand');
                    $brand->addMediaFromDisk($request->input('uploaded_image_name'), 'temp')->toMediaCollection('brand');
                }
            }

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
            Brand::find($id)->delete();
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

    public function getDropdown()
    {
        if (!empty(request()->sub_category_id)) {
            $brand = Brand::where('category_id', request()->sub_category_id)->orderBy('name', 'asc')->pluck('name', 'id');
        } else if (!empty(request()->category_id)) {
            $brand = Brand::where('category_id', request()->category_id)->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $brand = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        }

        $brand_dp = $this->commonUtil->createDropdownHtml($brand, 'Please Select');

        return $brand_dp;
    }
}
