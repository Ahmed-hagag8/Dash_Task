<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(): View
    {
        return view('admin.reports.index', [
            'categorySales' => $this->reportService->categorySales(),
            'topCustomers' => $this->reportService->topCustomers(),
            'customersWithoutOrders' => $this->reportService->customersWithoutOrders(),
            'productsWithoutOrders' => $this->reportService->productsWithoutOrders(),
            'monthlySales' => $this->reportService->monthlySalesCurrentYear(),
            'categoriesAboveThreshold' => $this->reportService->categoriesAboveThreshold(),
        ]);
    }
}
