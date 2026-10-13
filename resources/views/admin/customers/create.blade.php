@extends('layouts.admin')

@section('title', 'Create customer')

@section('content')
    <x-admin.page-heading title="Create customer" description="Add a customer to your directory." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.customers._form')
    </section>
@endsection
