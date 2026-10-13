<form method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}" class="space-y-5">
    @csrf
    @if ($customer->exists)
        @method('PUT')
    @endif

    <div>
        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $customer->name) }}" maxlength="255" required autofocus>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" maxlength="255" required>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $customer->phone) }}" maxlength="255">
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="city">City</label>
        <input id="city" name="city" type="text" value="{{ old('city', $customer->city) }}" maxlength="255">
        @error('city')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit">{{ $customer->exists ? 'Save changes' : 'Create customer' }}</button>
        <a href="{{ route('admin.customers.index') }}">Cancel</a>
    </div>
</form>
