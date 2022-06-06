<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiningTableResource;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiningTableController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dining_tables = DiningTable::all();
        return $this->handleResponse(DiningTableResource::collection($dining_tables), 'Dining Tables have been retrieved!');
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
            'name' => 'required',
            'dining_room_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $dining_table = DiningTable::create($input);
        return $this->handleResponse(new DiningTableResource($dining_table), 'Dining Table created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dining_table = DiningTable::find($id);
        if (is_null($dining_table)) {
            return $this->handleError('Dining Table not found!');
        }
        return $this->handleResponse(new DiningTableResource($dining_table), 'Dining Table retrieved.');
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
        $dining_table = DiningTable::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'dining_room_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $dining_table->name = $input['name'];
        $dining_table->dining_room_id = $input['dining_room_id'];
        $dining_table->save();

        return $this->handleResponse(new DiningTableResource($dining_table), 'Dining Table successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dining_table = DiningTable::find($id);

        $dining_table->delete();
        return $this->handleResponse([], 'Dining Table deleted!');
    }
}
