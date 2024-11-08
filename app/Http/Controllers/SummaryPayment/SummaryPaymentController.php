<?php

namespace App\Http\Controllers\SummaryPayment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SummaryPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
 */
    public function getPayments(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfDay();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $amountOrder = $request->get('amount_order', 'highest');

        $payments = Payment::selectRaw('
                SUM(value) as total_value,
                payment_merchants.name as merchant_name,
                payment_methods.name as method_name,
                CONCAT(payment_methods.name, " - ", REPLACE(payment_merchants.name, "-", "")) as merchant_method
            ')
            ->join('payment_merchants', 'payments.payment_merchant_id', '=', 'payment_merchants.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->groupBy('payment_methods.name', 'payment_merchants.name');

        if ($amountOrder === 'highest') {
            $payments = $payments->orderByDesc('total_value');
        } else {
            $payments = $payments->orderBy('total_value');
        }

        $payments = $payments->get();

        if ($request->ajax()) {
            return response()->json(['data' => $payments]);
        }

        return view('summary-payment.index', compact('payments'));
    }
}
