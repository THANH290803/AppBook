<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Models\Publisher;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Publisher::all();
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
     * @param  \App\Http\Requests\StorePublisherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePublisherRequest $request)
    {
        $messages = [
            'name.unique' => 'Tên nhà xuất bản đã được sử dụng.',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng.',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
            'email.email' => 'Địa chỉ email phải là một địa chỉ email hợp lệ.',
        ];

        // Validate the request data with custom messages
        $request->validate([
            'name' => 'required|unique:publishers,name',
            'phone_number' => 'nullable|unique:publishers,phone_number',
            'email' => 'nullable|email|unique:publishers,email',
            'address' => 'nullable',
        ], $messages);

        // Create the publisher
        $data = Publisher::create($request->all());

        // Return the created data with a 201 status code
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show(Publisher $publisher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function edit(Publisher $publisher)
    {
        return $publisher;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePublisherRequest  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        $messages = [
            'name.unique' => 'Tên nhà xuất bản đã được sử dụng.',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng.',
            'email.unique' => 'Địa chỉ email đã được sử dụng.',
            'email.email' => 'Địa chỉ email phải là một địa chỉ email hợp lệ.',
        ];

        // Validate the request data with custom messages
        $request->validate([
            'name' => 'required|unique:publishers,name,'.$publisher->id,
            'phone_number' => 'nullable|unique:publishers,phone_number,'.$publisher->id,
            'email' => 'nullable|email|unique:publishers,email,'.$publisher->id,
            'address' => 'nullable',
        ], $messages);

        // Update the publisher
        $publisher->update($request->all());

        // Return success response if update is successful
        return response()->json($publisher, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publisher $publisher)
    {
        $publisher->delete();
        return response()->json(null, 204);
    }
}
