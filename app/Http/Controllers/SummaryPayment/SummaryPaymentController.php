<?php

namespace App\Http\Controllers\SummaryPayment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

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
                CONCAT(payment_methods.name, (IF(payment_methods.name = "Cash", "", " - ")), REPLACE(payment_merchants.name, "-", "")) as merchant_method
            ')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->join('payment_merchants', 'payments.payment_merchant_id', '=', 'payment_merchants.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->whereNotIn('orders.status', ['CANCEL', 'GAGAL'])
            ->whereNull('payments.deleted_at')
            ->groupBy('payment_methods.name', 'payment_merchants.name');

        if ($amountOrder === 'highest') {
            $payments = $payments->orderByDesc('total_value');
        } else {
            $payments = $payments->orderBy('total_value');
        }

        $payment_details = Payment::selectRaw('
                SUM(value) as total_value,
                payment_methods.name as method_name
            ')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->join('payment_merchants', 'payments.payment_merchant_id', '=', 'payment_merchants.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->whereNotIn('orders.status', ['CANCEL', 'GAGAL'])
            ->whereNull('payments.deleted_at')
            ->groupBy('payment_methods.name');

        if ($amountOrder === 'highest') {
            $payment_details = $payment_details->orderByDesc('total_value');
        } else {
            $payment_details = $payment_details->orderBy('total_value');
        }

        $query = Payment::selectRaw("
                orders.number_ticket as order_number,
                payments.created_at,
                payment_methods.name as payment_method_name,
                payment_merchants.name as payment_merchant_name,
                payments.value as payment_amount,
                orders.status as order_status
            ")
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->join('payment_merchants', 'payments.payment_merchant_id', '=', 'payment_merchants.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->where('payments.value','!=',0)
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->whereNull('payments.deleted_at'); // Tambahkan kondisi jika diperlukan

        // Hitung total data sebelum filter
        $totalData = $query->count();

        // Filter pencarian jika ada
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.number_ticket', 'like', "%{$search}%")
                ->orWhere('payment_methods.name', 'like', "%{$search}%")
                ->orWhere('payment_merchants.name', 'like', "%{$search}%");
            });
        }

        // Hitung total data setelah filter
        $totalFiltered = $query->count();

         // Sorting jika ada
         if ($order = $request->input('order.0.column')) {
            $column = $request->input("columns.{$order}.data");
            $dir = $request->input('order.0.dir', 'asc');
            $query->orderBy($column, $dir);
        }

        // Paginasi
        $start = $request->input('start', 0); // Offset
        $length = $request->input('length', 10);
        if ($length == -1) {
            $list_payments = $query->get(); // Ambil semua data jika length = -1
        } else {
            $list_payments = $query->skip($start)->take($length)->get();
        }
        // $query->skip($start)->take($length);

       

        // $list_payments = $query->get();

        $payment_details = $payment_details->get();
        $payments = $payments->get();

        // dd($payemnt_details);

        if ($request->ajax()) {
            // return response()->json(['data' => $payments, 'data_list' => $list_payments]);
            // Return data dalam format DataTables
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => $totalFiltered,
                "data" => $payments,
                "data_list" => $list_payments,
                "data_detail" => $payment_details,
            ]);

        }

        return view('summary-payment.index', compact('payments', 'list_payments', 'payment_details'));
    }
}
