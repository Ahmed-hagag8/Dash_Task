@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <x-admin.page-heading
        title="Customers"
        description="Your customer directory and their order activity."
    />

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Customer</th>
                        <th scope="col" class="px-5 py-3 font-medium">Phone</th>
                        <th scope="col" class="px-5 py-3 font-medium">City</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Orders</th>
                        <th scope="col" class="px-5 py-3 font-medium">Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 sm:px-6">
                                <span class="block font-medium text-slate-900">{{ $customer->name }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $customer->email }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->phone ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->city ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($customer->orders_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center sm:px-6">
                                <p class="font-medium text-slate-700">No customers yet</p>
                                <p class="mt-1 text-sm text-slate-500">Customers will appear here once they are added.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $customers->links() }}</div>
        @endif
    </section>
@endsection
