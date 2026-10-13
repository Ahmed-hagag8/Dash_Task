<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'ordersByStatus' => Order::query()
                ->selectRaw('status, COUNT(*) as order_count, SUM(total) as revenue')
                ->groupBy('status')
                ->orderBy('status')
                ->get(),
        ]);
    }
}
