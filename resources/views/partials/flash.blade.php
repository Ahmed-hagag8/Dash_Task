@if (session('success'))
    <div class="mx-6 mt-4 flex items-start justify-between rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800"
         role="alert">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mx-6 mt-4 flex items-start justify-between rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800"
         role="alert">
        <span>{{ session('error') }}</span>
    </div>
@endif