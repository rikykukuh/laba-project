<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('customers.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->ajax()) {
            return response()->json($customer);
        }
        return redirect()->route('customers.index')->with('success', 'Sukses! Pelanggan ' . $customer->name . ' berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = CLient::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $cities = City::all();
        return view('customers.edit', compact('customer', 'cities'));
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
        $customer = Customer::findOrFail($id);
        $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'phone_number' => $request->phone_number,
        ]);
        return redirect()->route('customers.index')->with('success', 'Sukses! Pelanggan ' . $customer->name . ' berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Sukses! Pelanggan ' . $customer->name . ' berhasil dihapus!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCustomers(Request $request): \Illuminate\Http\JsonResponse
    {
        $searchTerm = $request->input('q');
        $customers = Customer::where('id', 'like', "%$searchTerm%")
            ->orWhere('name', 'like', "%$searchTerm%")
            ->get(['id', 'name', 'address', 'phone_number']);

        return response()->json($customers);
    }
}
