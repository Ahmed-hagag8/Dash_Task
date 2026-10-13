@extends('layouts.admin')

@section('title', 'Edit customer')

@section('content')
    <x-admin.page-heading title="Edit customer" description="Update this customer's contact details." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.customers._form')
    </section>
@endsection
