<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->reportService->dashboardStats(),
            'recentOrders' => $this->reportService->recentOrders(),
            'lowStockProducts' => $this->reportService->lowStockProducts(),
        ]);
    }
}
