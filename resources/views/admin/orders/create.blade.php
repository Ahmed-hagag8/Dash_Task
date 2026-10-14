@extends('layouts.admin')

@section('title', 'Create order')

@section('content')
    <x-admin.page-heading title="Create order" description="Choose a customer and add the products and quantities to this order." />

    <section class="max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id" required>
                    <option value="">Select a customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                            {{ $customer->name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @php($formItems = old('items') ?: [['product_id' => '', 'quantity' => 1]])
            <div>
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-slate-900">Order items</h2>
                        <p class="mt-1 text-sm text-slate-500">Prices and totals are calculated from the current catalog prices.</p>
                    </div>
                    <button id="add-order-item" type="button">Add product</button>
                </div>

                <div id="order-items" data-next-index="{{ count($formItems) }}" class="space-y-3">
                    @foreach ($formItems as $index => $item)
                        @php($selectedProduct = $products->firstWhere('id', $item['product_id'] ?? null))
                        <div class="order-item-row grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-[1fr_10rem_auto]">
                            <div>
                                <label for="items-{{ $index }}-product">Product</label>
                                <select id="items-{{ $index }}-product" name="items[{{ $index }}][product_id]" data-order-product required>
                                    <option value="">Select a product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                                            {{ $product->name }} ({{ $product->sku }}) · {{ number_format($product->price, 2) }} · {{ $product->stock }} in stock
                                        </option>
                                    @endforeach
                                </select>
                                @error("items.$index.product_id")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="items-{{ $index }}-quantity">Quantity</label>
                                <input id="items-{{ $index }}-quantity" name="items[{{ $index }}][quantity]" type="number" min="1" @if ($selectedProduct) max="{{ $selectedProduct->stock }}" @endif step="1" value="{{ $item['quantity'] ?? 1 }}" data-order-quantity required>
                                <p class="mt-1 text-sm text-slate-500" data-stock-message>
                                    @if ($selectedProduct)
                                        {{ $selectedProduct->stock > 0 ? "Available: {$selectedProduct->stock} units." : 'No units are currently in stock.' }}
                                    @else
                                        Select a product to view available stock.
                                    @endif
                                </p>
                                @error("items.$index.quantity")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="button" data-remove-order-item>Remove</button>
                        </div>
                    @endforeach
                </div>
                @error('items')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <template id="order-item-template">
                <div class="order-item-row grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-[1fr_10rem_auto]">
                    <div>
                        <label for="items-__INDEX__-product">Product</label>
                        <select id="items-__INDEX__-product" name="items[__INDEX__][product_id]" data-order-product required>
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                    {{ $product->name }} ({{ $product->sku }}) · {{ number_format($product->price, 2) }} · {{ $product->stock }} in stock
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="items-__INDEX__-quantity">Quantity</label>
                        <input id="items-__INDEX__-quantity" name="items[__INDEX__][quantity]" type="number" min="1" step="1" value="1" data-order-quantity required>
                        <p class="mt-1 text-sm text-slate-500" data-stock-message>Select a product to view available stock.</p>
                    </div>
                    <button type="button" data-remove-order-item>Remove</button>
                </div>
            </template>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit">Create order</button>
                <a href="{{ route('admin.orders.index') }}">Cancel</a>
            </div>
        </form>
    </section>

    <script>
        const orderItems = document.getElementById('order-items');
        const orderItemTemplate = document.getElementById('order-item-template');
        const addOrderItemButton = document.getElementById('add-order-item');

        const syncStockLimit = (row) => {
            const productSelect = row.querySelector('[data-order-product]');
            const quantityInput = row.querySelector('[data-order-quantity]');
            const stockMessage = row.querySelector('[data-stock-message]');
            const selectedOption = productSelect.selectedOptions[0];

            if (!productSelect.value || !selectedOption.hasAttribute('data-stock')) {
                quantityInput.removeAttribute('max');
                stockMessage.textContent = 'Select a product to view available stock.';
                return;
            }

            const stock = Number(selectedOption.dataset.stock);
            quantityInput.max = String(stock);
            stockMessage.textContent = stock > 0
                ? `Available: ${stock} units.`
                : 'No units are currently in stock.';
        };

        orderItems.querySelectorAll('.order-item-row').forEach(syncStockLimit);
        orderItems.addEventListener('change', (event) => {
            if (event.target.matches('[data-order-product]')) {
                syncStockLimit(event.target.closest('.order-item-row'));
            }
        });

        addOrderItemButton.addEventListener('click', () => {
            const index = Number(orderItems.dataset.nextIndex);
            const item = document.importNode(orderItemTemplate.content, true);

            item.querySelectorAll('[name], [id], [for]').forEach((element) => {
                ['name', 'id', 'for'].forEach((attribute) => {
                    if (element.hasAttribute(attribute)) {
                        element.setAttribute(attribute, element.getAttribute(attribute).replaceAll('__INDEX__', index));
                    }
                });
            });

            orderItems.appendChild(item);
            orderItems.dataset.nextIndex = String(index + 1);
        });

        orderItems.addEventListener('click', (event) => {
            if (event.target.closest('[data-remove-order-item]')) {
                event.target.closest('.order-item-row').remove();
            }
        });
    </script>
@endsection
