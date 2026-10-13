@extends('layouts.admin')

@section('title', 'Order details')

@section('content')
    <x-admin.page-heading
        :title="$order->order_number"
        description="Order details, customer information, and item totals."
    >
        <x-slot:actions>
            <a href="{{ route('admin.orders.index') }}">Back to orders</a>
        </x-slot:actions>
    </x-admin.page-heading>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-500">Customer</h2>
            <p class="mt-2 font-medium text-slate-900">{{ $order->customer->name }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $order->customer->email }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $order->customer->phone ?: 'No phone provided' }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $order->customer->city ?: 'No city provided' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-500">Order summary</h2>
            <p class="mt-2">Date: {{ $order->date->format('d M Y') }}</p>
            <p class="mt-2">Status: <x-admin.status-badge :status="$order->status" /></p>
            <p class="mt-2 text-lg font-semibold text-slate-900">Total: {{ number_format($order->total, 2) }}</p>
        </div>
    </section>

    <section class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-900">Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Product</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Quantity</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Unit price</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Line total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 sm:px-6">{{ $item->product->name }} <span class="text-slate-500">({{ $item->product->sku }})</span></td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ $item->quantity }}</td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <th colspan="3" scope="row" class="px-5 py-4 text-right font-semibold text-slate-900 sm:px-6">Order total</th>
                        <td class="px-5 py-4 text-right font-semibold tabular-nums text-slate-900">{{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Update status</h2>
        @if ($order->status === 'cancelled')
            <p class="mt-2 text-sm text-slate-600">This order is cancelled and cannot be reopened. Its items have been returned to stock.</p>
        @else
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <div class="min-w-48 flex-1">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach (['pending', 'processing', 'shipped', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit">Save status</button>
            </form>
        @endif
    </section>
@endsection
