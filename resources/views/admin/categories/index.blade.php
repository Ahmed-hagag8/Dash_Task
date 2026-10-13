@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <x-admin.page-heading
        title="Categories"
        description="Browse product categories and the products in each one."
    />

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium sm:px-6">Category</th>
                        <th scope="col" class="px-5 py-3 font-medium">Products</th>
                        <th scope="col" class="px-5 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="text-slate-700">
                            <td class="px-5 py-4 font-medium text-slate-900 sm:px-6">{{ $category->name }}</td>
                            <td class="px-5 py-4">{{ number_format($category->products_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $category->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center sm:px-6">
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
