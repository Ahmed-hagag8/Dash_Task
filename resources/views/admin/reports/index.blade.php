@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <x-admin.page-heading
        title="Reports"
        description="Order volume and revenue grouped by current order status."
    />

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-900">Orders by status</h3>
            <p class="mt-1 text-sm text-slate-500">Revenue totals include orders in each status.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Status</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Orders</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ordersByStatus as $row)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 sm:px-6"><x-admin.status-badge :status="$row->status" /></td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($row->order_count) }}</td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">No report data yet</p>
                                <p class="mt-1 text-sm text-slate-500">Order totals will appear here when orders are created.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
