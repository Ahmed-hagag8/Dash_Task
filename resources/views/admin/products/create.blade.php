@extends('layouts.admin')

@section('title', 'Create product')

@section('content')
    <x-admin.page-heading title="Create product" description="Add a product to your store catalog." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.products._form')
    </section>
@endsection
