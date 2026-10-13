@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <x-admin.page-heading
        title="Customers"
        description="Your customer directory and their order activity."
    >
        <x-slot:actions>
            <a href="{{ route('admin.customers.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white no-underline">
                Add customer
            </a>
        </x-slot:actions>
    </x-admin.page-heading>

    <form method="GET" action="{{ route('admin.customers.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1">
            <label for="customer-search">Search customers</label>
            <input id="customer-search" name="search" type="search" value="{{ $search }}" placeholder="Name or email">
        </div>
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Search</button>
        @if ($search !== '')
            <a href="{{ route('admin.customers.index') }}">Clear</a>
        @endif
    </form>

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
                        <th scope="col" class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 sm:px-6">
                                <a class="font-medium text-slate-900" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a>
                                <span class="mt-1 block text-xs text-slate-500">{{ $customer->email }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->phone ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->city ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($customer->orders_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
                                    <details>
                                        <summary class="text-red-600">Delete</summary>
                                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <p>Delete {{ $customer->name }}?</p>
                                            <button type="submit" class="bg-red-600">Confirm delete</button>
                                        </form>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center sm:px-6">
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
