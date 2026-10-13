@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <x-admin.page-heading
        title="Products"
        description="Review your catalog, stock levels, and product status."
    >
        <x-slot:actions>
            <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white no-underline">
                Add product
            </a>
        </x-slot:actions>
    </x-admin.page-heading>

    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1">
            <label for="product-search">Search products</label>
            <input id="product-search" name="search" type="search" value="{{ $search }}" placeholder="Product name or SKU">
        </div>
        <div>
            <label for="category-filter">Category</label>
            <select id="category-filter" name="category_id">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status-filter">Status</label>
            <select id="status-filter" name="status">
                <option value="">All statuses</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="out_of_stock" value="1" @checked($outOfStock)>
            <span>Out of stock</span>
        </label>
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Apply filters</button>
            @if ($search !== '' || $categoryId > 0 || $status !== '' || $outOfStock)
                <a href="{{ route('admin.products.index') }}">Clear filters</a>
            @endif
        </div>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Product</th>
                        <th scope="col" class="px-5 py-3 font-medium">SKU</th>
                        <th scope="col" class="px-5 py-3 font-medium">Category</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-medium">Stock</th>
                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                        <th scope="col" class="px-5 py-3 font-medium">Actions</th>
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
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs">{{ $product->sku }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $product->category->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($product->price, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right tabular-nums">{{ number_format($product->stock) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <x-admin.status-badge :status="$product->is_active ? 'Active' : 'Inactive'" />
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                    <details>
                                        <summary class="text-red-600">Delete</summary>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <p>Delete {{ $product->name }}?</p>
                                            <button type="submit" class="bg-red-600">Confirm delete</button>
                                        </form>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center sm:px-6">
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
