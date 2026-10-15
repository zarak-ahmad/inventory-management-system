@extends('layouts.app')

@section('title', 'POS')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Point of Sale</h1>
        <p>Scan or search products, add to cart, and checkout quickly.</p>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-receipt me-1"></i> Sales History
    </a>
</div>

@if ($products->isEmpty())
    <div class="alert alert-warning">
        No sellable products with stock are available. Add stock via Purchases or Stock Adjustments first.
    </div>
@endif

<form method="POST" action="{{ route('sales.store') }}" id="posForm" novalidate>
    @csrf

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label for="barcodeInput" class="form-label">Barcode / SKU</label>
                            <div class="product-search" id="barcodeSearchWrap">
                                <input
                                    type="text"
                                    id="barcodeInput"
                                    class="form-control"
                                    placeholder="Scan or type barcode / SKU (min 3 chars)"
                                    autocomplete="off"
                                    spellcheck="false"
                                    autofocus
                                >
                                <div id="barcodeSuggestions" class="product-search-suggestions d-none" role="listbox" aria-label="Barcode matches"></div>
                            </div>
                            <div id="barcodeSearchHint" class="form-text">Type at least 3 characters to list matching products.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="productSearch" class="form-label">Search</label>
                            <div class="product-search" id="nameSearchWrap">
                                <input
                                    type="text"
                                    id="productSearch"
                                    class="form-control"
                                    placeholder="Product name (min 3 chars)"
                                    autocomplete="off"
                                    spellcheck="false"
                                >
                                <div id="nameSuggestions" class="product-search-suggestions d-none" role="listbox" aria-label="Name matches"></div>
                            </div>
                            <div id="nameSearchHint" class="form-text">Type at least 3 characters to list matching products.</div>
                        </div>
                        <div class="col-md-3">
                            <label for="categoryFilter" class="form-label">Category</label>
                            <select id="categoryFilter" class="form-select">
                                <option value="">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pos-product-list mt-3" id="productGrid">
                        @foreach ($products as $product)
                            @php
                                $sku = trim((string) $product->sku);
                                $barcode = trim((string) $product->barcode);
                                $codeLabel = ($sku !== '' && $barcode !== '' && strcasecmp($sku, $barcode) === 0)
                                    ? $barcode
                                    : trim(collect([$sku ?: null, $barcode ?: null])->filter()->implode(' · '));
                            @endphp
                            <div class="product-card pos-product-row"
                                 data-id="{{ $product->id }}"
                                 data-name="{{ strtolower($product->name) }}"
                                 data-sku="{{ strtolower($product->sku) }}"
                                 data-barcode="{{ strtolower($product->barcode) }}"
                                 data-display-name="{{ $product->name }}"
                                 data-display-sku="{{ $product->sku }}"
                                 data-price="{{ $product->selling_price }}"
                                 data-stock="{{ $product->current_stock }}"
                                 data-unit="{{ $product->unit?->short_name }}"
                                 data-category="{{ $product->category_id }}">
                                <button type="button" class="pos-product-row-btn add-product-btn w-100 text-start">
                                    <div class="pos-product-row-main">
                                        <div class="fw-semibold text-truncate">{{ $product->name }}</div>
                                        <div class="small text-muted text-truncate">
                                            @if ($codeLabel !== '')
                                                {{ $codeLabel }}
                                            @endif
                                            @if ($product->category?->name)
                                                @if ($codeLabel !== '') · @endif{{ $product->category->name }}
                                            @endif
                                            @if ($product->location)
                                                · <span class="text-body-secondary">{{ $product->location }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="pos-product-row-side text-end">
                                        <div class="fw-semibold">{{ number_format((float) $product->selling_price, 2) }}</div>
                                        <div class="small text-muted">Stock {{ number_format((float) $product->current_stock, 2) }} {{ $product->unit?->short_name }}</div>
                                    </div>
                                    <div class="pos-product-row-action">
                                        <span class="btn btn-sm btn-outline-primary">Add</span>
                                    </div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <div id="noProductsMsg" class="text-center text-muted py-4 d-none">No products match your search.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h5 mb-0">
                            Cart
                            <span class="badge text-bg-primary align-middle" id="cartCountBadge">0</span>
                        </h2>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearCartBtn" disabled>
                            <i class="bi bi-trash me-1"></i> Clear cart
                        </button>
                    </div>
                    <p class="small text-muted mb-3 mb-md-2">Add multiple products for one customer, then checkout once.</p>

                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', $walkInId) == $customer->id)>
                                    {{ $customer->name }}{{ $customer->is_default ? ' (Default)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @error('items')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive mb-3" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="width: 90px;">Qty</th>
                                    <th class="text-end">Line</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>
                    <div id="emptyCartMsg" class="text-muted text-center py-3">Cart is empty. Scan, search, tap a product, or use Add to POS from product details.</div>

                    <div id="cartInputs"></div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', 0) }}" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="tax" class="form-label">Tax Amount</label>
                            <input type="number" step="0.01" min="0" name="tax" id="tax" value="{{ old('tax', 0) }}" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method', 'cash') === $method)>{{ ucfirst($method) }}</option>
                                @endforeach
                            </select>
                            @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-select">
                                <option value="paid" @selected(old('payment_status', 'paid') === 'paid')>Paid</option>
                                <option value="partial" @selected(old('payment_status') === 'partial')>Partial</option>
                                <option value="unpaid" @selected(old('payment_status') === 'unpaid')>Unpaid</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" name="notes" id="notes" value="{{ old('notes') }}" class="form-control">
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="subtotalDisplay">0.00</strong></div>
                        <div class="d-flex justify-content-between"><span>Discount</span><strong id="discountDisplay">0.00</strong></div>
                        <div class="d-flex justify-content-between"><span>Tax</span><strong id="taxDisplay">0.00</strong></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-4"><span>Total</span><strong id="totalDisplay">0.00</strong></div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg" id="checkoutBtn" disabled>
                        <i class="bi bi-cart-check me-1"></i> Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const CART_STORAGE_KEY = 'pos_cart_v1';
    const cart = new Map();
    const cartBody = document.getElementById('cartBody');
    const cartInputs = document.getElementById('cartInputs');
    const emptyCartMsg = document.getElementById('emptyCartMsg');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const clearCartBtn = document.getElementById('clearCartBtn');
    const cartCountBadge = document.getElementById('cartCountBadge');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const barcodeInput = document.getElementById('barcodeInput');
    const productSearch = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const barcodeSuggestions = document.getElementById('barcodeSuggestions');
    const nameSuggestions = document.getElementById('nameSuggestions');
    const barcodeSearchHint = document.getElementById('barcodeSearchHint');
    const nameSearchHint = document.getElementById('nameSearchHint');
    const noProductsMsg = document.getElementById('noProductsMsg');
    const MIN_CHARS = 3;

    const money = (n) => Number(n || 0).toFixed(2);

    const allCards = () => [...document.querySelectorAll('.product-card')];

    const productFromCard = (card) => ({
        id: Number(card.dataset.id),
        name: card.dataset.displayName || card.querySelector('.fw-semibold')?.textContent?.trim() || 'Product',
        sku: card.dataset.displaySku || card.dataset.sku,
        barcode: card.dataset.barcode,
        price: Number(card.dataset.price),
        stock: Number(card.dataset.stock),
        unit: card.dataset.unit || '',
    });

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const persistCart = () => {
        try {
            sessionStorage.setItem(CART_STORAGE_KEY, JSON.stringify([...cart.values()]));
        } catch (error) {
            // Ignore storage quota / private mode failures.
        }
    };

    const clearPersistedCart = () => {
        try {
            sessionStorage.removeItem(CART_STORAGE_KEY);
        } catch (error) {
            // Ignore.
        }
    };

    const restoreCart = () => {
        try {
            const raw = sessionStorage.getItem(CART_STORAGE_KEY);
            if (!raw) return;

            const items = JSON.parse(raw);
            if (!Array.isArray(items)) return;

            items.forEach((saved) => {
                const id = Number(saved?.id || 0);
                if (!id) return;

                const card = allCards().find((item) => Number(item.dataset.id) === id);
                const stock = card ? Number(card.dataset.stock) : Number(saved.stock || 0);
                if (stock <= 0) return;

                let qty = Number(saved.qty || 0);
                if (qty <= 0) return;
                if (qty > stock) qty = stock;

                cart.set(id, {
                    id,
                    name: saved.name || card?.dataset.displayName || 'Product',
                    sku: saved.sku || card?.dataset.displaySku || '',
                    price: Number(saved.price ?? card?.dataset.price ?? 0),
                    stock,
                    unit: saved.unit || card?.dataset.unit || '',
                    qty,
                });
            });
        } catch (error) {
            clearPersistedCart();
        }
    };

    const renderCart = () => {
        cartBody.innerHTML = '';
        cartInputs.innerHTML = '';
        let index = 0;
        let subtotal = 0;
        let totalQty = 0;

        cart.forEach((item) => {
            subtotal += item.qty * item.price;
            totalQty += item.qty;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-semibold small">${escapeHtml(item.name)}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${escapeHtml(String(item.sku || '').toUpperCase())}</div>
                </td>
                <td><input type="number" class="form-control form-control-sm cart-qty" min="0.01" step="0.01" max="${item.stock}" value="${item.qty}" data-id="${item.id}"></td>
                <td class="text-end small">${money(item.qty * item.price)}</td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item" data-id="${item.id}" title="Remove"><i class="bi bi-x"></i></button></td>
            `;
            cartBody.appendChild(tr);

            cartInputs.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                <input type="hidden" name="items[${index}][selling_price]" value="${item.price}">
            `);
            index++;
        });

        const discount = Number(discountInput.value || 0);
        const tax = Number(taxInput.value || 0);
        document.getElementById('subtotalDisplay').textContent = money(subtotal);
        document.getElementById('discountDisplay').textContent = money(discount);
        document.getElementById('taxDisplay').textContent = money(tax);
        document.getElementById('totalDisplay').textContent = money(Math.max(0, subtotal - discount + tax));

        emptyCartMsg.classList.toggle('d-none', cart.size > 0);
        checkoutBtn.disabled = cart.size === 0;
        clearCartBtn.disabled = cart.size === 0;
        cartCountBadge.textContent = String(cart.size);
        cartCountBadge.title = `${cart.size} product line(s), qty ${money(totalQty)}`;

        cartBody.querySelectorAll('.cart-qty').forEach((input) => {
            input.addEventListener('change', () => {
                const id = Number(input.dataset.id);
                const item = cart.get(id);
                let qty = Number(input.value || 0);
                if (qty <= 0) {
                    cart.delete(id);
                } else {
                    if (qty > item.stock) qty = item.stock;
                    item.qty = qty;
                    cart.set(id, item);
                }
                renderCart();
            });
        });

        cartBody.querySelectorAll('.remove-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                cart.delete(Number(btn.dataset.id));
                renderCart();
            });
        });

        persistCart();
    };

    const addProduct = (product, qty = 1) => {
        if (!product || !product.id) return false;
        const existing = cart.get(product.id);
        const nextQty = (existing?.qty || 0) + qty;
        if (nextQty > product.stock) {
            window.Swal?.fire({
                icon: 'warning',
                title: 'Insufficient stock',
                text: `Only ${product.stock} ${product.unit} available for ${product.name}.`,
            });
            return false;
        }
        cart.set(product.id, {
            id: product.id,
            name: product.name,
            sku: product.sku,
            price: product.price,
            stock: product.stock,
            unit: product.unit,
            qty: nextQty,
        });
        renderCart();
        return true;
    };

    clearCartBtn?.addEventListener('click', async () => {
        if (cart.size === 0) return;
        const result = await window.Swal?.fire({
            icon: 'question',
            title: 'Clear cart?',
            text: 'Remove all products from this sale cart.',
            showCancelButton: true,
            confirmButtonText: 'Clear cart',
        });
        if (result && result.isDismissed) return;
        cart.clear();
        clearPersistedCart();
        renderCart();
    });

    document.querySelectorAll('.product-card').forEach((card) => {
        card.querySelector('.add-product-btn')?.addEventListener('click', () => {
            addProduct(productFromCard(card));
        });
    });

    const matchesBarcodeOrSku = (card, term) => {
        const value = String(term || '').trim().toLowerCase();
        if (!value) return false;
        return card.dataset.barcode.includes(value) || card.dataset.sku.includes(value);
    };

    const matchesName = (card, term) => {
        const value = String(term || '').trim().toLowerCase();
        if (!value) return false;
        return card.dataset.name.includes(value)
            || card.dataset.sku.includes(value)
            || card.dataset.barcode.includes(value);
    };

    const exactCodeMatch = (code) => {
        const value = String(code || '').trim().toLowerCase();
        if (!value) return null;
        return allCards().find((card) =>
            card.dataset.barcode === value || card.dataset.sku === value
        ) || null;
    };

    const hideSuggestions = (list) => {
        list.classList.add('d-none');
        list.innerHTML = '';
        delete list.dataset.activeIndex;
    };

    const suggestionItems = (list) => [...list.querySelectorAll('.product-search-item[data-id]')];

    const setActiveSuggestion = (list, index) => {
        const items = suggestionItems(list);
        if (!items.length) return;
        const next = ((index % items.length) + items.length) % items.length;
        items.forEach((item, i) => item.classList.toggle('is-active', i === next));
        list.dataset.activeIndex = String(next);
        items[next].scrollIntoView({ block: 'nearest' });
    };

    const moveSuggestion = (list, delta) => {
        if (list.classList.contains('d-none')) return false;
        const items = suggestionItems(list);
        if (!items.length) return false;
        const current = Number(list.dataset.activeIndex ?? -1);
        setActiveSuggestion(list, current < 0 ? (delta > 0 ? 0 : items.length - 1) : current + delta);
        return true;
    };

    const activateSelectedSuggestion = (list) => {
        if (list.classList.contains('d-none')) return false;
        const items = suggestionItems(list);
        const index = Number(list.dataset.activeIndex ?? -1);
        if (index < 0 || !items[index]) return false;
        items[index].click();
        return true;
    };

    const bindSuggestionKeyboard = (input, list, { onEnter } = {}) => {
        input.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                if (moveSuggestion(list, 1)) event.preventDefault();
                return;
            }
            if (event.key === 'ArrowUp') {
                if (moveSuggestion(list, -1)) event.preventDefault();
                return;
            }
            if (event.key === 'Escape') {
                hideSuggestions(list);
                return;
            }
            if (event.key === 'Enter') {
                if (typeof onEnter === 'function') {
                    onEnter(event);
                    return;
                }
                if (activateSelectedSuggestion(list)) {
                    event.preventDefault();
                }
            }
        });
    };

    const renderSuggestions = (list, cards, onPick) => {
        if (!cards.length) {
            list.innerHTML = `<div class="product-search-empty text-muted">No matching products.</div>`;
            list.classList.remove('d-none');
            delete list.dataset.activeIndex;
            return;
        }

        list.innerHTML = cards.slice(0, 10).map((card) => {
            const product = productFromCard(card);
            const meta = [
                product.sku ? `SKU ${product.sku}` : null,
                product.barcode ? `Barcode ${product.barcode}` : null,
            ].filter(Boolean).join(' · ');

            return `
                <button type="button" class="product-search-item w-100 border-0 bg-transparent text-start" role="option" data-id="${product.id}">
                    <div class="product-search-item-main">
                        <div class="fw-semibold text-truncate">${escapeHtml(product.name)}</div>
                        <div class="small text-muted text-truncate">${escapeHtml(meta)}</div>
                    </div>
                    <div class="product-search-item-side text-end">
                        <div class="small fw-semibold">${escapeHtml(money(product.price))}</div>
                        <div class="small text-muted">${escapeHtml(money(product.stock))} ${escapeHtml(product.unit)}</div>
                    </div>
                </button>
            `;
        }).join('');

        list.querySelectorAll('[data-id]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const card = allCards().find((item) => Number(item.dataset.id) === Number(btn.dataset.id));
                if (card) onPick(card);
            });
            btn.addEventListener('mouseenter', () => {
                const items = suggestionItems(list);
                setActiveSuggestion(list, items.indexOf(btn));
            });
        });

        list.classList.remove('d-none');
        setActiveSuggestion(list, 0);
    };

    const filterProductGrid = ({ barcodeTerm = '', nameTerm = '', category = '' } = {}) => {
        const barcodeReady = barcodeTerm.length >= MIN_CHARS;
        const nameReady = nameTerm.length >= MIN_CHARS;
        let visible = 0;

        allCards().forEach((card) => {
            const passesBarcode = !barcodeReady || matchesBarcodeOrSku(card, barcodeTerm);
            const passesName = !nameReady || matchesName(card, nameTerm);
            const passesCategory = !category || String(card.dataset.category) === category;
            const shouldShow = passesBarcode && passesName && passesCategory;

            card.classList.toggle('d-none', !shouldShow);
            if (shouldShow) visible++;
        });

        noProductsMsg.classList.toggle('d-none', visible > 0 || allCards().length === 0);
        return visible;
    };

    const runBarcodeSearch = () => {
        const term = barcodeInput.value.trim();
        const category = categoryFilter.value;

        if (term.length < MIN_CHARS) {
            hideSuggestions(barcodeSuggestions);
            barcodeSearchHint.textContent = 'Type at least 3 characters to list matching products.';
            filterProductGrid({
                barcodeTerm: '',
                nameTerm: productSearch.value.trim(),
                category,
            });
            return;
        }

        const matches = allCards().filter((card) => matchesBarcodeOrSku(card, term));
        filterProductGrid({
            barcodeTerm: term,
            nameTerm: productSearch.value.trim(),
            category,
        });
        renderSuggestions(barcodeSuggestions, matches, (card) => {
            addProduct(productFromCard(card));
            barcodeInput.value = '';
            hideSuggestions(barcodeSuggestions);
            barcodeSearchHint.textContent = 'Added to cart. Scan or type another code.';
            filterProductGrid({
                barcodeTerm: '',
                nameTerm: productSearch.value.trim(),
                category: categoryFilter.value,
            });
            barcodeInput.focus();
        });
        barcodeSearchHint.textContent = matches.length
            ? `${matches.length} match(es) — ↑↓ to move, Enter to select`
            : 'No matching products.';
    };

    const runNameSearch = () => {
        const term = productSearch.value.trim();
        const category = categoryFilter.value;

        if (term.length < MIN_CHARS) {
            hideSuggestions(nameSuggestions);
            nameSearchHint.textContent = 'Type at least 3 characters to list matching products.';
            filterProductGrid({
                barcodeTerm: barcodeInput.value.trim(),
                nameTerm: '',
                category,
            });
            return;
        }

        const matches = allCards().filter((card) => matchesName(card, term));
        filterProductGrid({
            barcodeTerm: barcodeInput.value.trim(),
            nameTerm: term,
            category,
        });
        renderSuggestions(nameSuggestions, matches, (card) => {
            addProduct(productFromCard(card));
            productSearch.value = '';
            hideSuggestions(nameSuggestions);
            nameSearchHint.textContent = 'Added to cart. Search for another product.';
            filterProductGrid({
                barcodeTerm: barcodeInput.value.trim(),
                nameTerm: '',
                category: categoryFilter.value,
            });
            productSearch.focus();
        });
        nameSearchHint.textContent = matches.length
            ? `${matches.length} match(es) — ↑↓ to move, Enter to select`
            : 'No matching products.';
    };

    barcodeInput.addEventListener('input', runBarcodeSearch);
    productSearch.addEventListener('input', runNameSearch);
    categoryFilter.addEventListener('change', () => {
        runBarcodeSearch();
        runNameSearch();
    });

    const handleBarcodeEnter = (event) => {
        event.preventDefault();

        const exact = exactCodeMatch(barcodeInput.value);
        if (exact) {
            addProduct(productFromCard(exact));
            barcodeInput.value = '';
            hideSuggestions(barcodeSuggestions);
            barcodeSearchHint.textContent = 'Added to cart. Scan or type another code.';
            filterProductGrid({
                barcodeTerm: '',
                nameTerm: productSearch.value.trim(),
                category: categoryFilter.value,
            });
            barcodeInput.focus();
            return;
        }

        if (activateSelectedSuggestion(barcodeSuggestions)) {
            return;
        }

        const term = barcodeInput.value.trim().toLowerCase();
        if (term.length >= MIN_CHARS) {
            const matches = allCards().filter((card) => matchesBarcodeOrSku(card, term));
            if (matches.length === 1) {
                addProduct(productFromCard(matches[0]));
                barcodeInput.value = '';
                hideSuggestions(barcodeSuggestions);
                barcodeSearchHint.textContent = 'Added to cart. Scan or type another code.';
                filterProductGrid({
                    barcodeTerm: '',
                    nameTerm: productSearch.value.trim(),
                    category: categoryFilter.value,
                });
                barcodeInput.focus();
                return;
            }
        }

        window.Swal?.fire({
            icon: 'error',
            title: 'Not found',
            text: term.length < MIN_CHARS
                ? 'Type at least 3 characters, or scan a full barcode/SKU.'
                : 'No product matches that barcode/SKU.',
        });
    };

    const handleNameEnter = (event) => {
        event.preventDefault();

        if (activateSelectedSuggestion(nameSuggestions)) {
            return;
        }

        const term = productSearch.value.trim();
        if (term.length >= MIN_CHARS) {
            const matches = allCards().filter((card) => matchesName(card, term));
            if (matches.length === 1) {
                addProduct(productFromCard(matches[0]));
                productSearch.value = '';
                hideSuggestions(nameSuggestions);
                nameSearchHint.textContent = 'Added to cart. Search for another product.';
                filterProductGrid({
                    barcodeTerm: barcodeInput.value.trim(),
                    nameTerm: '',
                    category: categoryFilter.value,
                });
                productSearch.focus();
            }
        }
    };

    bindSuggestionKeyboard(barcodeInput, barcodeSuggestions, {
        onEnter: handleBarcodeEnter,
    });
    bindSuggestionKeyboard(productSearch, nameSuggestions, {
        onEnter: handleNameEnter,
    });

    // Keep Enter from submitting the POS form while searching.
    document.getElementById('posForm')?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        if (event.target === barcodeInput || event.target === productSearch) {
            event.preventDefault();
        }
    });

    document.addEventListener('click', (event) => {
        const barcodeWrap = document.getElementById('barcodeSearchWrap');
        const nameWrap = document.getElementById('nameSearchWrap');
        if (barcodeWrap && !barcodeWrap.contains(event.target)) {
            hideSuggestions(barcodeSuggestions);
        }
        if (nameWrap && !nameWrap.contains(event.target)) {
            hideSuggestions(nameSuggestions);
        }
    });

    discountInput.addEventListener('input', renderCart);
    taxInput.addEventListener('input', renderCart);

    restoreCart();
    renderCart();

    const addProductId = @json($addProductId ?? null);
    if (addProductId) {
        const card = allCards().find((item) => Number(item.dataset.id) === Number(addProductId));
        if (card) {
            const product = productFromCard(card);
            if (product.stock > 0) {
                const added = addProduct(product);
                if (added) {
                    window.Swal?.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `${product.name} added to cart`,
                        showConfirmButton: false,
                        timer: 1800,
                    });
                }
            } else {
                window.Swal?.fire({
                    icon: 'warning',
                    title: 'Out of stock',
                    text: `${product.name} has no stock available to sell.`,
                });
            }
        } else {
            window.Swal?.fire({
                icon: 'warning',
                title: 'Unavailable',
                text: 'This product cannot be added to POS right now (inactive or missing).',
            });
        }

        // Clean the URL so refresh does not add the item again.
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('product_id');
        window.history.replaceState({}, '', cleanUrl.pathname + cleanUrl.search);
    }
});
</script>
@endpush