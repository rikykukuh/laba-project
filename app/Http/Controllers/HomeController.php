<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activeUserCount = User::where('active', true)->count();
        $permissionCount = Role::count();
        return view('home',compact('activeUserCount', 'permissionCount'));
    }
}
