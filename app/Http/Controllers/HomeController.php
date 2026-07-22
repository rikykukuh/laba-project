<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use App\Models\Order;
use App\Models\Role;
use App\Models\OrderItem;
use App\Models\OrderItemTeknisi;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activeUserCount = User::where('active', true)->count();
        $permissionCount = Role::count();
        $totalOrderToday = Order::whereDate('created_at', Carbon::today())
                            ->where('transaction_type', 0)->count();
        $totalPenjualanToday = Order::whereDate('created_at', Carbon::today())
                                ->where('transaction_type', 1)->count();
        $barangMasuk = OrderItem::join('orders', 'order_items.order_id','=','orders.id')->where('orders.transaction_type',0)->whereDate('order_items.created_at', Carbon::today())->count();

        $canViewProductivityCharts = auth()->user()->hasAnyRoles('Administrators');
        $cashierProductivity = collect();
        $technicianProductivity = collect();

        if ($canViewProductivityCharts) {
            $cashierProductivity = Order::select(
                'created_by',
                DB::raw('COUNT(*) as total')
            )
            ->with('creator:id,name')
            ->whereNotNull('created_by')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereRaw("LOWER(TRIM(status)) <> 'cancel'");
            })
            ->groupBy('created_by')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => optional($item->creator)->name ?: 'User #' . $item->created_by,
                    'total' => (int) $item->total,
                ];
            })
                ->values();

            $technicianProductivity = OrderItemTeknisi::select(
                'user_id',
                DB::raw('COUNT(*) as total')
            )
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => optional($item->user)->name ?: 'User #' . $item->user_id,
                    'total' => (int) $item->total,
                ];
            })
                ->values();
        }

        // dd($request->session());
        return view('home',compact('activeUserCount', 
        'permissionCount', 
        'totalOrderToday', 
        'totalPenjualanToday',
        'barangMasuk',
        'canViewProductivityCharts',
        'cashierProductivity',
        'technicianProductivity'));
    }
}
