<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5">
    @csrf
    @if ($category->exists)
        @method('PUT')
    @endif

    <div>
        <label for="name">Category name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" maxlength="255" required autofocus>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $category->exists ? $category->is_active : true))>
            <span>Active category</span>
        </label>
        @error('is_active')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit">{{ $category->exists ? 'Save changes' : 'Create category' }}</button>
        <a href="{{ route('admin.categories.index') }}">Cancel</a>
    </div>
</form>
