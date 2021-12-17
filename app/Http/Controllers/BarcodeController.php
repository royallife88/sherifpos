<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;

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
        $products = Product::pluck('name', 'id');

        return view('barcode.create')->with(compact('products'));
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
                $product_details[] = ['details' => $details, 'qty' => $value['quantity']];
                $total_qty += $value['quantity'];
            }

            $page_height = null;
            $rows = ceil($total_qty / 3) + 0.4;
            $page_height = $request->paper_size;

            $print['name'] = !empty($request->product_name) ? 1 : 0;
            $print['price'] = !empty($request->price) ? 1 : 0;
            $print['variations'] = !empty($request->variations) ? 1 : 0;


            $output = view('barcode.partials.print_barcode')
                ->with(compact('print', 'product_details',  'page_height'))->render();
        // } catch (\Exception $e) {
        //     \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

        //     $output = __('lang.something_went_wrong');
        // }

        return $output;
    }
}
