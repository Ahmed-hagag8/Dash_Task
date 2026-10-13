<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'orders' => Order::count(),
                'customers' => Customer::count(),
                'products' => Product::count(),
                'revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            ],
            'recentOrders' => Order::with('customer')
                ->latest('date')
                ->limit(5)
                ->get(),
        ]);
    }
}
