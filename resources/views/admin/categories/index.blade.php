@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <x-admin.page-heading
        title="Categories"
        description="Browse product categories and the products in each one."
    >
        <x-slot:actions>
            <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white no-underline">
                Add category
            </a>
        </x-slot:actions>
    </x-admin.page-heading>

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1">
            <label for="category-search">Search categories</label>
            <input id="category-search" name="search" type="search" value="{{ $search }}" placeholder="Category name">
        </div>
        <div>
            <label for="category-status-filter">Status</label>
            <select id="category-status-filter" name="status">
                <option value="">All statuses</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Search</button>
        @if ($search !== '' || $status !== '')
            <a href="{{ route('admin.categories.index') }}">Clear</a>
        @endif
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Category</th>
                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                        <th scope="col" class="px-5 py-3 font-medium">Products</th>
                        <th scope="col" class="px-5 py-3 font-medium">Created</th>
                        <th scope="col" class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 font-medium text-slate-900 sm:px-6">{{ $category->name }}</td>
                            <td class="px-5 py-4"><x-admin.status-badge :status="$category->is_active ? 'Active' : 'Inactive'" /></td>
                            <td class="px-5 py-4">{{ number_format($category->products_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $category->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                    <details>
                                        <summary class="text-red-600">Delete</summary>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <p>Delete {{ $category->name }}?</p>
                                            <button type="submit" class="bg-red-600">Confirm delete</button>
                                        </form>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center sm:px-6">
                                <p class="font-medium text-slate-700">No categories yet</p>
                                <p class="mt-1 text-sm text-slate-500">Categories will appear here once they are added.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $categories->links() }}</div>
        @endif
    </section>
@endsection
