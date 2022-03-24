<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Size;
use App\Models\Variation;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
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
     * @param transactionUtil $transactionUtil
     * @param Util $commonUtil
     * @param ProductUtils $productUtil
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
        $product = Product::where('is_raw_material', 0)->get();
        return $this->handleResponse(ProductResource::collection($product), 'Products have been retrieved!');
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
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        try {
            $product_data = [
                'name' => $input['name'],
                'translations' => $input['translations'],
                'product_class_id' => $input['product_class_id'],
                'product_details' => $input['product_details'],
                'purchase_price' => $input['purchase_price'],
                'sell_price' => $input['sell_price'],
                'discount_type' => $input['discount_type'],
                'discount' => $input['discount'],
                'discount_start_date' => $input['discount_start_date'],
                'discount_end_date' => $input['discount_end_date'],
                'type' => $input['type'],
                'active' => $input['active'],
                'created_by' => 1
            ];

            DB::beginTransaction();
            $product = Product::create($product_data);

            if (!empty($input['image'])) {
                $product->addMediaFromUrl($input['image'])->toMediaCollection('product');
            }

            $v = $input['variations'];

            foreach ($v as $vv) {
                $variation_data = $vv;
                $variation_data['restaurant_model_id'] = $vv['id'];
                $variation_data['product_id'] = $product->id;

                if (!empty($vv['size']['pos_model_id'])) {
                    $size = Size::where('id', $vv['size']['pos_model_id'])->first();
                    $variation_data['size_id'] = !empty($size)  ? $size->id : null;
                }
                unset($vv['id']);
                Variation::create($variation_data);
            }

            DB::commit();

            return $this->handleResponse(new ProductResource($product), 'Product created!');
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            return $this->handleError($e->getMessage(), [__('lang.something_went_wrong')], 503);
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
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->handleError('Product not found!');
        }
        return $this->handleResponse(new ProductResource($product), 'Product retrieved.');
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
        $product = Product::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        try {
            $product_data = [
                'name' => $input['name'],
                'translations' => $input['translations'],
                'product_class_id' => $input['product_class_id'],
                'product_details' => $input['product_details'],
                'purchase_price' => $input['purchase_price'],
                'sell_price' => $input['sell_price'],
                'discount_type' => $input['discount_type'],
                'discount' => $input['discount'],
                'discount_start_date' => $input['discount_start_date'],
                'discount_end_date' => $input['discount_end_date'],
                'type' => $input['type'],
                'active' => $input['active'],
            ];

            DB::beginTransaction();
            $product = Product::where('id', $id)->first();
            $product->update($product_data);

            if (!empty($input['image'])) {
                $product->addMediaFromUrl($input['image'])->toMediaCollection('product');
            }

            $v = $input['variations'];

            foreach ($v as $vv) {
                $variation_data = $vv;
                $variation_data['restaurant_model_id'] = $vv['id'];
                $variation_data['product_id'] = $product->id;

                if (!empty($vv['size']['pos_model_id'])) {
                    $size = Size::where('id', $vv['size']['pos_model_id'])->first();
                    $variation_data['size_id'] = !empty($size)  ? $size->id : null;
                }
                unset($vv['id']);
                if (!empty($vv['pos_model_id'])) {
                    $variation = Variation::where('id', $vv['pos_model_id'])->first();
                    $variation->update($variation_data);
                } else {
                    Variation::create($variation_data);
                }
            }
            DB::commit();

            return $this->handleResponse(new ProductResource($product), 'Product successfully updated!');
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            return $this->handleError($e->getMessage(), [__('lang.something_went_wrong')], 503);
        }
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
            $product = Product::find($id);
            Variation::where('product_id', $id)->delete();
            $product->delete();
            return $this->handleResponse([], 'Product deleted!');
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            return $this->handleError($e->getMessage(), [__('lang.something_went_wrong')], 503);
        }
    }
}
