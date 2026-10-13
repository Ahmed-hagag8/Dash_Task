@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <x-admin.page-heading
        title="Overview"
        description="A quick look at your store's activity."
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total orders" :value="number_format($stats['orders'])" description="All orders recorded" />
        <x-admin.stat-card label="Customers" :value="number_format($stats['customers'])" description="Registered customers" />
        <x-admin.stat-card label="Products" :value="number_format($stats['products'])" description="Products in your catalog" />
        <x-admin.stat-card label="Revenue" :value="number_format($stats['revenue'], 2)" description="Excludes cancelled orders" />
    </div>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-900">Recent orders</h3>
            <p class="mt-1 text-sm text-slate-500">The latest orders by order date.</p>
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
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900 sm:px-6">#{{ $order->id }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $order->customer->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4"><x-admin.status-badge :status="$order->status" /></td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($order->total, 2) }}</td>
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
@endsection
