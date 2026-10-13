@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <x-admin.page-heading
        title="Orders"
        description="Track order status, customer details, and order totals."
    />

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
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
                    @forelse ($orders as $order)
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
                                <p class="mt-1 text-sm text-slate-500">Orders will appear here once they are created.</p>
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
