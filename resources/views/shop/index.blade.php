@extends('layouts.app')

@section('title', 'Products - Infinity Figures')

@section('content')
    <section class="shop-section" id="products">
        <div class="container-fluid shop-wide">
            <div class="catalog-shell">
                <div class="catalog-topbar">
                    <div>
                        <span>Full Catalog</span>
                        <h2>Browse Products</h2>
                    </div>
                    <span>{{ $products->total() }} products</span>
                </div>

                <button class="btn btn-outline-gold d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#catalogFilters" aria-expanded="false" aria-controls="catalogFilters">
                    <i class="bi bi-funnel"></i> Filters
                </button>

                <div class="collapse d-lg-block" id="catalogFilters">
                    <form class="filter-panel" action="{{ route('products.index') }}">
                        <div class="filter-search">
                            <label class="form-label" for="search">Search figures</label>
                            <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, character, or series">
                        </div>

                        <div>
                            <label class="form-label" for="category">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                        {{ $category->name }} ({{ $category->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label" for="sort">Sort</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                                <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                                <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                                <option value="best_selling" @selected(request('sort') === 'best_selling')>Best Selling</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" for="min_price">Min price</label>
                            <input class="form-control" id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ request('min_price') }}" placeholder="$0">
                        </div>

                        <div>
                            <label class="form-label" for="max_price">Max price</label>
                            <input class="form-control" id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ request('max_price') }}" placeholder="$500">
                        </div>

                        <div>
                            <label class="form-label" for="brand">Brand</label>
                            <select class="form-select" id="brand" name="brand">
                                <option value="">All brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((string) request('brand') === (string) $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button class="btn btn-gold" type="submit"><i class="bi bi-funnel"></i> Apply</button>
                            <a class="btn btn-outline-light" href="{{ route('products.index') }}">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="product-grid">
                    @forelse ($products as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @empty
                        <div class="empty-panel">No products match the current filters.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>

    @include('shop.partials.footer')
@endsection
