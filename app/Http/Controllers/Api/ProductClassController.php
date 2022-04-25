<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductClassResource;
use App\Models\ProductClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductClassController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_classes = ProductClass::all();
        return $this->handleResponse(ProductClassResource::collection($product_classes), 'Product Classs have been retrieved!');
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

        $product_class = ProductClass::create($input);
        if (!empty($input['image'])) {
            $product_class->addMediaFromUrl($input['image'])->toMediaCollection('product_class');
        }

        return $this->handleResponse(new ProductClassResource($product_class), 'Product Class created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product_class = ProductClass::find($id);
        if (is_null($product_class)) {
            return $this->handleError('Product Class not found!');
        }
        return $this->handleResponse(new ProductClassResource($product_class), 'Product Class retrieved.');
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
        $product_class = ProductClass::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $product_class->name = $input['name'];
        $product_class->translations = $input['translations'];
        $product_class->description = $input['description'];
        $product_class->sort = $input['sort'];
        $product_class->status = $input['status'];
        $product_class->save();

        if (!empty($input['image'])) {
            $product_class->clearMediaCollection('product_class');
            $product_class->addMediaFromUrl($input['image'])->toMediaCollection('product_class');
        }

        return $this->handleResponse(new ProductClassResource($product_class), 'Product Class successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product_class = ProductClass::find($id);

        $product_class->delete();
        return $this->handleResponse([], 'Product Class deleted!');
    }
}
