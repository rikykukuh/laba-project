<?php


namespace App\Http\Controllers\OrderItemTeknisi;

use App\Http\Controllers\Controller;
use App\Models\OrderItemTeknisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderItemTeknisiExport;
use App\Exports\SummaryTeknisiExport;


class OrderItemTeknisiController extends Controller
{

    public function index(Request $request)
    {
        $query = OrderItemTeknisi::with(['user', 'orderItem', 'orderItem.order']);

        if ($request->search) {
            $query->where('order_item_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->latest()->paginate(10);

        $summaryQuery = OrderItemTeknisi::select(
                    'order_item_teknisi.user_id',
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN order_items.state = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                    DB::raw("SUM(CASE WHEN order_items.state = 'proses' THEN 1 ELSE 0 END) as proses"),
                    DB::raw("SUM(CASE WHEN order_items.state = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                    DB::raw("SUM(CASE WHEN order_items.state = 'cancel' THEN 1 ELSE 0 END) as cancel")
                    )
                    ->join('order_items', 'order_items.id', '=', 'order_item_teknisi.order_item_id');

        if ($request->start_date && $request->end_date) {
            $summaryQuery->whereBetween('order_item_teknisi.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        $summary = $summaryQuery
                    ->with('user')
                    ->groupBy('order_item_teknisi.user_id')
                    ->get();


        return view('OrderItemTeknisi.index', compact('data', 'summary'));

    }

    public function export(Request $request)
    {
        $query = OrderItemTeknisi::with(['user', 'orderItem.order']);

        if ($request->search) {
            $query->where('order_item_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->get();

        return Excel::download(new OrderItemTeknisiExport($data), 'list_teknisi.xlsx');
    }

    public function exportSummary(Request $request)
    {
        $query = OrderItemTeknisi::select(
                'order_item_teknisi.user_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN order_items.state = 'masuk' THEN 1 ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN order_items.state = 'proses' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN order_items.state = 'selesai' THEN 1 ELSE 0 END) as selesai"),
                DB::raw("SUM(CASE WHEN order_items.state = 'cancel' THEN 1 ELSE 0 END) as cancel")
            )
            ->join('order_items', 'order_items.id', '=', 'order_item_teknisi.order_item_id');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_item_teknisi.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $summary = $query->with('user')
            ->groupBy('order_item_teknisi.user_id')
            ->get();

        return Excel::download(new SummaryTeknisiExport($summary), 'summary_teknisi.xlsx');
    }

}