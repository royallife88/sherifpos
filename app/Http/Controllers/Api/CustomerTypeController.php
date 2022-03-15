<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerTypeResource;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_type = CustomerType::all();
        return $this->handleResponse(CustomerTypeResource::collection($customer_type), 'Customer Types have been retrieved!');
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

        $customer_type = CustomerType::create($input);
        return $this->handleResponse(new CustomerTypeResource($customer_type), 'Customer Type created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer_type = CustomerType::find($id);
        if (is_null($customer_type)) {
            return $this->handleError('Customer Type not found!');
        }
        return $this->handleResponse(new CustomerTypeResource($customer_type), 'Customer Type retrieved.');
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
        $customer_type = CustomerType::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $customer_type->name = $input['name'];
        $customer_type->save();

        return $this->handleResponse(new CustomerTypeResource($customer_type), 'Customer Type successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer_type = CustomerType::find($id);

        $customer_type->delete();
        return $this->handleResponse([], 'Customer Type deleted!');
    }
}
