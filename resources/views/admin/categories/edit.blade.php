@extends('layouts.admin')

@section('title', 'Edit category')

@section('content')
    <x-admin.page-heading title="Edit category" description="Update this product category." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.categories._form')
    </section>
@endsection
