<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiningRoomResource;
use App\Models\DiningRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiningRoomController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dining_rooms = DiningRoom::all();
        return $this->handleResponse(DiningRoomResource::collection($dining_rooms), 'Dining Rooms have been retrieved!');
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

        $dining_room = DiningRoom::create($input);
        return $this->handleResponse(new DiningRoomResource($dining_room), 'Dining Room created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dining_room = DiningRoom::find($id);
        if (is_null($dining_room)) {
            return $this->handleError('Dining Room not found!');
        }
        return $this->handleResponse(new DiningRoomResource($dining_room), 'Dining Room retrieved.');
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
        $dining_room = DiningRoom::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $dining_room->name = $input['name'];
        $dining_room->save();

        return $this->handleResponse(new DiningRoomResource($dining_room), 'Dining Room successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dining_room = DiningRoom::find($id);

        $dining_room->delete();
        return $this->handleResponse([], 'Dining Room deleted!');
    }
}
