@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <x-admin.page-heading
        title="Products"
        description="Review your catalog, stock levels, and product status."
    />

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Product</th>
                        <th scope="col" class="px-5 py-3 font-medium">Category</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Stock</th>
                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 font-medium text-slate-900 sm:px-6">
                                <span class="block">{{ $product->name }}</span>
                                @if ($product->description)
                                    <span class="mt-1 block max-w-xs truncate text-xs font-normal text-slate-500">{{ $product->description }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $product->category->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($product->price, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($product->stock) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <x-admin.status-badge :status="$product->is_active ? 'Active' : 'Inactive'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center sm:px-6">
                                <p class="font-medium text-slate-700">No products yet</p>
                                <p class="mt-1 text-sm text-slate-500">Products will appear here once they are added to your catalog.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $products->links() }}</div>
        @endif
    </section>
@endsection
