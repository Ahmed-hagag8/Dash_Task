@extends('layouts.admin')

@section('title', 'Create category')

@section('content')
    <x-admin.page-heading title="Create category" description="Add a category to organize products." />

    <section class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('admin.categories._form')
    </section>
@endsection
