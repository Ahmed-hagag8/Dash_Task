<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="space-y-5">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif

    <div>
        <label for="name">Product name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" maxlength="255" required autofocus>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sku">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" maxlength="100" required>
        @error('sku')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" maxlength="5000">{{ old('description', $product->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                {{ $category->name }}{{ ! $category->is_active ? ' (inactive - current category)' : '' }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="price">Price</label>
            <input id="price" name="price" type="number" min="0.01" step="0.01" value="{{ old('price', $product->price) }}" required>
            @error('price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock">Stock</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required>
            @error('stock')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $product->exists ? $product->is_active : true))>
            <span>Active product</span>
        </label>
        @error('is_active')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
        <a href="{{ route('admin.products.index') }}">Cancel</a>
    </div>
</form>
