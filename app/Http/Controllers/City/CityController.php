<?php

namespace App\Http\Controllers\City;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $citys = City::all();
        return view('citys.index', compact('citys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('citys.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = City::create([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);
        return redirect()->route('citys.index')->with('success', 'Great! City ' . $city->name . ' created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = city::findOrFail($id);
        return view('citys.show', compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::findOrFail($id);
        $cities = City::all();
        return view('citys.edit', compact('city', 'cities'));
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
        $city = City::findOrFail($id);
        $city->update([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);
        return redirect()->route('citys.index')->with('success', 'Excellence! City ' . $city->name . ' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return redirect()->route('citys.index')->with('success', 'Well done! City ' . $city->name . ' deleted successfully!');
    }
}
