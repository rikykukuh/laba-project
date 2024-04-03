<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::paginate(10);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('clients.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = Client::create([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->ajax()) {
            return response()->json($client);
        }
        return redirect()->route('clients.index')->with('success', 'Great! Client ' . $client->name . ' created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = CLient::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $cities = City::all();
        return view('clients.edit', compact('client', 'cities'));
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
        $client = Client::findOrFail($id);
        $client->update([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);
        return redirect()->route('clients.index')->with('success', 'Excellence! Client ' . $client->name . ' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Well done! Client ' . $client->name . ' deleted successfully!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCustomers(Request $request): \Illuminate\Http\JsonResponse
    {
        $searchTerm = $request->input('q');
        $customers = Client::where('id', 'like', "%$searchTerm%")
            ->orWhere('name', 'like', "%$searchTerm%")
            ->get(['id', 'name', 'address', 'phone_number']);

        return response()->json($customers);
    }
}
