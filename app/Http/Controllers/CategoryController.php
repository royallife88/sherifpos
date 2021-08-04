<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductClass;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;
        $type = request()->type ?? null;

        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $product_classes = ProductClass::pluck('name', 'id');

        return view('category.create')->with(compact(
            'type',
            'quick_add',
            'categories',
            'product_classes'
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

            DB::beginTransaction();
            $category = Category::create($data);
            if ($request->has('image')) {
                $category->addMedia($request->image)->toMediaCollection('category');
            }

            $category_id = $category->id;
            $sub_category_id = null;
            if ($request->parent_id) {
                $category_id = $request->parent_id;
                $sub_category_id = $category->id;
            }


            DB::commit();
            $output = [
                'success' => true,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
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
        $category = Category::find($id);
        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $product_classes = ProductClass::pluck('name', 'id');

        return view('category.edit')->with(compact(
            'category',
            'categories',
            'product_classes'
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
            $category = Category::find($id);

            $category->update($data);
            if ($request->has('image')) {
                $category->addMedia($request->image)->toMediaCollection('category');
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
            Category::find($id)->delete();
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
        if(!empty(request()->product_class_id)){
            $categories = Category::where('product_class_id', request()->product_class_id)->pluck('name', 'id');
        }else{
            $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        }
        $categories_dp = $this->commonUtil->createDropdownHtml($categories, 'Please Select');

        return $categories_dp;
    }

    public function getSubCategoryDropdown()
    {
        if (!empty(request()->category_id)) {
            $categories = Category::where('parent_id', request()->category_id)->pluck('name', 'id');
        } else {
            $categories = Category::whereNotNull('parent_id')->pluck('name', 'id');
        }
        $categories_dp = $this->commonUtil->createDropdownHtml($categories, 'Please Select');

        return $categories_dp;
    }
}
