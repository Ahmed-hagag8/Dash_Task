@extends('layouts.admin')

@section('title', 'Customer details')

@section('content')
    <x-admin.page-heading
        :title="$customer->name"
        description="Customer details and order history."
    >
        <x-slot:actions>
            <a href="{{ route('admin.customers.edit', $customer) }}">Edit customer</a>
            <a href="{{ route('admin.customers.index') }}">Back to customers</a>
        </x-slot:actions>
    </x-admin.page-heading>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Email</h3>
            <p class="mt-2">{{ $customer->email }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Phone</h3>
            <p class="mt-2">{{ $customer->phone ?: '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">City</h3>
            <p class="mt-2">{{ $customer->city ?: '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500">Customer since</h3>
            <p class="mt-2">{{ $customer->created_at->format('d M Y') }}</p>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-900">Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Order</th>
                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Total</th>
                        <th scope="col" class="px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 font-medium text-slate-900 sm:px-6">#{{ $order->id }}</td>
                            <td class="px-5 py-4"><x-admin.status-badge :status="$order->status" /></td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-4">{{ $order->date->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">No orders for this customer</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $orders->links() }}</div>
        @endif
    </section>
@endsection
