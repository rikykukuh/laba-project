<?php


namespace App\Http\Controllers\ReportMatching;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportMatchingController extends Controller
{   

    public function index(Request $request)
    {
        return view('report-matching.index');
    }
    
    public function data(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        /* ================= LIST DATA ================= */
        $list = DB::table('orders as o')
            ->leftJoin('payments as p', 'o.id', '=', 'p.order_id')
            ->leftJoin('payment_methods as pm', 'pm.id', '=', 'p.payment_method_id')
            ->leftJoin('payment_merchants as pm2', 'pm2.id', '=', 'p.payment_merchant_id')
            ->select(
                'o.number_ticket as order_number',
                'p.created_at',
                'pm.name as payment_method_name',
                'pm2.name as payment_merchant_name',
                'p.value as payment_amount',
                DB::raw("
                    CASE 
                        WHEN p.payment_type = 0 THEN 'DP'
                        WHEN p.payment_type = 1 THEN 'LUNAS'
                        ELSE '-'
                    END AS payment_status
                ")
            );

        if ($startDate && $endDate) {
            $list->whereBetween('p.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        /* ================= SUMMARY PER MERCHANT ================= */
        $summaryMerchant = DB::table('payments as p')
            ->leftJoin('payment_methods as pm', 'pm.id', '=', 'p.payment_method_id')
            ->leftJoin('payment_merchants as pm2', 'pm2.id', '=', 'p.payment_merchant_id')
            ->select(
                'pm.name as method_name',
                'pm2.name as merchant_name',
                DB::raw('SUM(p.value) as total_value')
            )
            ->groupBy('p.payment_method_id', 'p.payment_merchant_id', 'pm.name', 'pm2.name');

        if ($startDate && $endDate) {
            $summaryMerchant->whereBetween('p.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        /* ================= SUMMARY PER METHOD ================= */
        $summaryMethod = DB::table('payments as p')
            ->leftJoin('payment_methods as pm', 'pm.id', '=', 'p.payment_method_id')
            ->select(
                'pm.name as method_name',
                DB::raw('SUM(p.value) as total_value')
            )
            ->groupBy('p.payment_method_id', 'pm.name');

        if ($startDate && $endDate) {
            $summaryMethod->whereBetween('p.created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        return response()->json([
            'data_list'   => $list->get(),
            'data'        => $summaryMerchant->get(),
            'data_detail' => $summaryMethod->get(),
        ]);
    }
}
