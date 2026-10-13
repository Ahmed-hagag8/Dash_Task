@extends('layouts.admin')

@section('title', 'Edit product')

@section('content')
    <x-admin.page-heading title="Edit product" description="Update this catalog item." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.products._form')
    </section>
@endsection
