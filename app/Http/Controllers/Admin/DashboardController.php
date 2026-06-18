<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.dashboard', [
            'admin' => $admin,
        ]);
    }
}
