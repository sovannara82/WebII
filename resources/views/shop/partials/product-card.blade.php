<article class="product-card">
    <div class="wishlist-btn" aria-label="Add to wishlist">
        @auth
            <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                @csrf
                <button class="wishlist-icon" type="submit"><i class="bi bi-heart-fill"></i></button>
            </form>
        @else
            <i class="bi bi-heart-fill"></i>
        @endauth
    </div>
    <a href="{{ route('shop.show', $product) }}" class="product-image-wrap">
        <img src="{{ $product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=700&q=80' }}" alt="{{ $product->name }}" loading="lazy">
        <span class="stock-badge {{ $product->stock <= 0 ? 'sold-out' : ($product->stock <= 6 ? 'low-stock' : '') }}">
            {{ $product->stock <= 0 ? 'Sold out' : ($product->stock <= 6 ? 'Low stock' : 'In stock') }}
        </span>
    </a>
    <div class="product-body">
        <div class="product-meta">
            <span>{{ $product->category->name }}</span>
            <span>{{ $product->brand?->name ?? 'Studio' }}</span>
        </div>
        <h3>
            <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
        </h3>
        <p>{{ $product->series }} {{ $product->height ? '- '.$product->height.' cm' : '' }}</p>
        <div class="product-actions">
            <strong>${{ number_format((float) $product->price, 2) }}</strong>
            <div class="quick-actions">
                <a class="btn btn-outline-light icon-action" href="{{ route('shop.show', $product) }}" aria-label="View details">
                    <i class="bi bi-eye"></i>
                </a>
                <form action="{{ route('cart.store', $product) }}" method="POST">
                    @csrf
                    <button class="btn btn-gold add-cart" type="submit" @disabled($product->stock <= 0)>Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</article>
