@extends('layouts.app')

@section('title', $product->name.' - FigureVerse')

@section('content')
    <section class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="product-detail-image">
                    <img src="{{ $product->primaryImage?->image ?? $product->images->first()?->image ?? 'https://images.unsplash.com/photo-1608889175123-8ee362201f81?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $product->name }}">
                </div>
                <div class="row g-3 mt-1">
                    @foreach ($product->images->take(3) as $image)
                        <div class="col-4">
                            <img class="detail-thumb" src="{{ $image->image }}" alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6">
                <p class="text-uppercase small admin-muted mb-1">{{ $product->category->name }} / {{ $product->brand?->name ?? 'Independent Studio' }}</p>
                <h1 class="display-6 fw-bold">{{ $product->name }}</h1>
                <p class="lead admin-muted">{{ $product->description }}</p>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="price">{{ $averageRating ?: 'No' }} rating</span>
                    <span class="admin-muted">{{ $product->reviews->count() }} reviews</span>
                    @auth
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light" type="submit"><i class="bi bi-heart-fill"></i> Wishlist</button>
                        </form>
                    @endauth
                </div>

                <div class="product-specs my-4">
                    <div><span>Character</span><strong>{{ $product->character_name ?? 'Original' }}</strong></div>
                    <div><span>Series</span><strong>{{ $product->series ?? 'Collector line' }}</strong></div>
                    <div><span>Material</span><strong>{{ $product->material ?? 'PVC / ABS' }}</strong></div>
                    <div><span>Height</span><strong>{{ $product->height ? $product->height.' cm' : 'TBA' }}</strong></div>
                    <div><span>Stock</span><strong>{{ $product->stock }} units</strong></div>
                </div>

                <form action="{{ route('cart.store', $product) }}" method="POST" class="buy-panel">
                    @csrf
                    <div>
                        <span class="admin-muted small">Price</span>
                        <div class="h3 fw-bold mb-0">${{ number_format((float) $product->price, 2) }}</div>
                    </div>
                    <input class="form-control" name="quantity" type="number" value="1" min="1" max="20" aria-label="Quantity">
                    <button class="btn btn-gold btn-lg" type="submit">Add to cart</button>
                </form>

                @if ($product->tags->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        @foreach ($product->tags as $tag)
                            <span class="badge-premium">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <div class="mt-5 pt-4 border-top">
                <h2 class="h4 fw-bold mb-3">Related figures</h2>
                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-md-3">
                            <a class="related-card" href="{{ route('shop.show', $relatedProduct) }}">
                                <img src="{{ $relatedProduct->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $relatedProduct->name }}">
                                <strong>{{ $relatedProduct->name }}</strong>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-5 pt-4 border-top border-secondary">
            <div class="row g-4">
                <div class="col-lg-5">
                    <h2 class="h4 fw-bold mb-3">Write a review</h2>
                    @auth
                        <form action="{{ route('reviews.store', $product) }}" method="POST" class="admin-panel">
                            @csrf
                            <label class="form-label" for="rating">Rating</label>
                            <select class="form-select mb-3" id="rating" name="rating" required>
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <option value="{{ $rating }}">{{ $rating }} stars</option>
                                @endfor
                            </select>
                            <label class="form-label" for="comment">Comment</label>
                            <textarea class="form-control mb-3" id="comment" name="comment" rows="4"></textarea>
                            <button class="btn btn-gold" type="submit">Save Review</button>
                        </form>
                    @else
                        <div class="empty-panel">Login to review this figure.</div>
                    @endauth
                </div>
                <div class="col-lg-7">
                    <h2 class="h4 fw-bold mb-3">Collector reviews</h2>
                    @forelse ($product->reviews as $review)
                        <div class="admin-panel mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="price">{{ $review->rating }}/5</span>
                            </div>
                            <p class="admin-muted mb-0">{{ $review->comment ?: 'No comment left.' }}</p>
                        </div>
                    @empty
                        <div class="empty-panel">No reviews yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
