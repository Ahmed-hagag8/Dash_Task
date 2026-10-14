@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <x-admin.page-heading
        title="Overview"
        description="A quick look at your store's activity."
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total products" :value="number_format($stats['total_products'])" description="Products in your catalog" />
        <x-admin.stat-card label="Out-of-stock" :value="number_format($stats['out_of_stock_products'])" description="Products with no stock left" />
        <x-admin.stat-card label="Orders today" :value="number_format($stats['orders_today'])" description="Completed and pending orders today" />
        <x-admin.stat-card label="Total sales this month" :value="number_format($stats['sales_this_month'], 2)" description="Excludes cancelled orders" />
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">Latest orders</h3>
                <p class="mt-1 text-sm text-slate-500">The most recent order activity.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium sm:px-6">Order</th>
                            <th scope="col" class="px-5 py-3 font-medium">Customer</th>
                            <th scope="col" class="px-5 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Total</th>
                            <th scope="col" class="px-5 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentOrders as $order)
                            <tr class="text-slate-700">
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900 sm:px-6">
                                    <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $order->customer?->name ?? 'Unknown customer' }}</td>
                                <td class="whitespace-nowrap px-5 py-4"><x-admin.status-badge :status="$order->status" /></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format((float) $order->total, 2) }}</td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $order->date->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center sm:px-6">
                                    <p class="font-medium text-slate-700">No orders yet</p>
                                    <p class="mt-1 text-sm text-slate-500">Orders will appear here when they are created.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">Low-stock products</h3>
                <p class="mt-1 text-sm text-slate-500">Products with stock under 5 units.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium sm:px-6">Product</th>
                            <th scope="col" class="px-5 py-3 font-medium">Category</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($lowStockProducts as $product)
                            <tr class="text-slate-700">
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900 sm:px-6">{{ $product->name }}</td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $product->category?->name ?? 'Unassigned' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ $product->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center sm:px-6">
                                    <p class="font-medium text-slate-700">No low-stock products</p>
                                    <p class="mt-1 text-sm text-slate-500">All products have a healthy stock level.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
