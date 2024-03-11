<?php

namespace App\Http\Controllers\PaymentMerchant;

use App\Http\Controllers\Controller;
use App\Models\PaymentMerchant;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_merchants = PaymentMerchant::with('paymentMethod')->paginate(10);
        return view('payment-merchants.index', compact('payment_merchants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $payment_methods = PaymentMethod::all();
        return view('payment-merchants.create', compact('payment_methods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payment_merchant = PaymentMerchant::create([
            'name' => $request->name,
            'payment_method_id' => $request->payment_method_id,
        ]);
        return redirect()->route('payment-merchants.index')->with('success', 'Great! Payment Merchant ' . $payment_merchant->name . ' created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment_merchant = PaymentMerchant::with('paymentMethod')->findOrFail($id);
        return view('payment-merchants.show', compact('payment_merchant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment_merchant = PaymentMerchant::with('paymentMethod')->findOrFail($id);
        $payment_methods = PaymentMethod::all();
        return view('payment-merchants.edit', compact('payment_methods', 'payment_merchant'));
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
        $payment_merchant = PaymentMerchant::findOrFail($id);
        $payment_merchant->update([
            'name' => $request->name,
            'payment_method_id' => $request->payment_method_id,
        ]);
        return redirect()->route('payment-merchants.index')->with('success', 'Excellence! Payment Merchant ' . $payment_merchant->name . ' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment_merchant = PaymentMerchant::findOrFail($id);
        $payment_merchant->delete();
        return redirect()->route('payment-merchants.index')->with('success', 'Well done! Payment Merchant ' . $payment_merchant->name . ' deleted successfully!');
    }
}
