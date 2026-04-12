<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use App\Models\Order;
use App\Models\Role;
use App\Models\OrderItem;

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

        // dd($request->session());
        return view('home',compact('activeUserCount', 
        'permissionCount', 
        'totalOrderToday', 
        'totalPenjualanToday',
        'barangMasuk'));
    }
}
