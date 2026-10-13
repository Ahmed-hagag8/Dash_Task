@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <x-admin.page-heading
        title="Orders"
        description="Track order status, customer details, and order totals."
    >
        <x-slot:actions>
            <a href="{{ route('admin.orders.create') }}">Create order</a>
        </x-slot:actions>
    </x-admin.page-heading>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 gap-4 border-b border-slate-200 p-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'processing', 'shipped', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from">From date</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div>
                <label for="date_to">To date</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit">Filter</button>
                <a href="{{ route('admin.orders.index') }}">Clear</a>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Order number</th>
                        <th scope="col" class="px-5 py-3 font-medium">Customer</th>
                        <th scope="col" class="px-5 py-3 font-medium">Date</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Total</th>
                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="text-slate-700">
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900 sm:px-6">
                                <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $order->customer->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $order->date->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($order->total, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4"><x-admin.status-badge :status="$order->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center sm:px-6">
                                <p class="font-medium text-slate-700">No orders yet</p>
                                <p class="mt-1 text-sm text-slate-500">No orders match these filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $orders->withQueryString()->links() }}</div>
        @endif
    </section>
@endsection
