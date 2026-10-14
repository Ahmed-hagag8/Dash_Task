@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <x-admin.page-heading
        title="Reports"
        description="Report Data for top sales, customers,products"
    />

    <div class="space-y-8">
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">1. Total sales and orders per category</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Category</th>
                            <th class="px-5 py-3 text-right font-medium">Orders</th>
                            <th class="px-5 py-3 text-right font-medium">Total sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categorySales as $row)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $row->category_name }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format($row->orders_count) }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $row->total_sales, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">No category sales yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">2. Top 5 customers by total spent</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Customer</th>
                            <th class="px-5 py-3 font-medium">City</th>
                            <th class="px-5 py-3 text-right font-medium">Orders</th>
                            <th class="px-5 py-3 text-right font-medium">Total spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($topCustomers as $customer)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $customer->customer_name }}</td>
                                <td class="px-5 py-4">{{ $customer->customer_city }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format($customer->order_count) }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $customer->total_spent, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">No customer totals yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">3. Customers who have never placed an order</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Customer</th>
                            <th class="px-5 py-3 font-medium">Email</th>
                            <th class="px-5 py-3 font-medium">City</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customersWithoutOrders as $customer)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $customer->name }}</td>
                                <td class="px-5 py-4">{{ $customer->email }}</td>
                                <td class="px-5 py-4">{{ $customer->city }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">Every customer has placed an order</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">4. Products with zero sales and total sold for others</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Product</th>
                            <th class="px-5 py-3 text-right font-medium">Total quantity sold</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($productsWithoutOrders as $product)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $product->product_name }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $product->total_quantity_sold) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">No product data yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">5. Sales per month for the current year</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Month</th>
                            <th class="px-5 py-3 text-right font-medium">Total sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($monthlySales as $row)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $row['month_name'] }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $row['total_sales'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">No monthly sales data yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">6. Categories above $10,000 in sales</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium sm:px-6">Category</th>
                            <th class="px-5 py-3 text-right font-medium">Total sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categoriesAboveThreshold as $row)
                            <tr class="text-slate-700">
                                <td class="px-5 py-4 sm:px-6">{{ $row->category_name }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $row->total_sales, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-12 text-center">
                                    <p class="font-medium text-slate-700">No categories above the threshold</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
