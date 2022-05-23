<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Size;
use App\Models\Store;
use App\Models\System;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
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
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes_array = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $discount_customer_types = Customer::getCustomerTreeArray();
        $stores  = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('barcode.create')->with(compact(
            'products',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes_array',
            'customer_types',
            'stores',
            'users',
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
        //
    }

    /**
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($product_id, $variation_id);

                return view('barcode.partials.show_table_rows')
                    ->with(compact('products', 'index'));
            }
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

    public function printBarcode(Request $request)
    {
        // try {
        $products = $request->get('products');


        $product_details = [];
        $total_qty = 0;
        foreach ($products as $value) {
            $details = $this->productUtil->getDetailsFromVariation($value['variation_id'],  null, false);
            $product_details[] = ['details' => $details, 'qty' => $this->commonUtil->num_uf($value['quantity'])];
            $total_qty += $this->commonUtil->num_uf($value['quantity']);
        }

        $page_height = null;
        $rows = ceil($total_qty / 3) + 0.4;
        $page_height = $request->paper_size;

        $print['name'] = !empty($request->product_name) ? 1 : 0;
        $print['price'] = !empty($request->price) ? 1 : 0;
        $print['variations'] = !empty($request->variations) ? 1 : 0;
        $print['size'] = !empty($request->size) ? 1 : 0;
        $print['color'] = !empty($request->color) ? 1 : 0;
        $print['grade'] = !empty($request->grade) ? 1 : 0;
        $print['unit'] = !empty($request->unit) ? 1 : 0;
        $print['size_variations'] = !empty($request->size_variations) ? 1 : 0;
        $print['color_variations'] = !empty($request->color_variations) ? 1 : 0;
        $print['site_title'] = !empty($request->site_title) ? System::getProperty('site_title') : null;
        $store = [];
        if (!empty($request->store)) {
            foreach ($request->store as $store_id) {
                $store[] = !empty($store_id) ? Store::where('id', $store_id)->first()->name . ' ' : null;
            }
        }
        $print['store'] = !empty($store) ? implode(',', $store) : null;
        $print['free_text'] = !empty($request->free_text) ? $request->free_text : null;


        $output = view('barcode.partials.print_barcode')
            ->with(compact('print', 'product_details',  'page_height'))->render();
        // } catch (\Exception $e) {
        //     Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

        //     $output = __('lang.something_went_wrong');
        // }

        return $output;
    }
}
