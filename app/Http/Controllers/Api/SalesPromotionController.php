<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalesPromotionResource;
use App\Models\SalesPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesPromotionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales_promotions = SalesPromotion::where('type', 'item_discount')->where('purchase_condition', 0)->where('product_condition', 0)->get();
        return $this->handleResponse(SalesPromotionResource::collection($sales_promotions), 'Sales Promotion have been retrieved!');
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

        $sales_promotion = SalesPromotion::create($input);
        return $this->handleResponse(new SalesPromotionResource($sales_promotion), 'Sales Promotion created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sales_promotion = SalesPromotion::find($id);
        if (is_null($sales_promotion)) {
            return $this->handleError('Sales Promotion not found!');
        }
        return $this->handleResponse(new SalesPromotionResource($sales_promotion), 'Sales Promotion retrieved.');
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
        $sales_promotion = SalesPromotion::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'code' => $request->code,
            'product_ids' => $request->product_ids,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];
        $sales_promotion->update($data);

        return $this->handleResponse(new SalesPromotionResource($sales_promotion), 'Sales Promotion successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sales_promotion = SalesPromotion::find($id);

        $sales_promotion->delete();
        return $this->handleResponse([], 'Sales Promotion deleted!');
    }
}
