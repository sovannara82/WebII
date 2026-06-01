<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function home(): View
    {
        return view('shop.home', [
            'banners' => Banner::latest()->take(3)->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'heroProducts' => Product::active()
                ->with(['primaryImage', 'brand', 'category'])
                ->latest()
                ->take(4)
                ->get(),
            'newArrivals' => Product::active()
                ->with(['primaryImage', 'brand', 'category'])
                ->latest()
                ->take(8)
                ->get(),
            'bestSellers' => Product::active()
                ->with(['primaryImage', 'brand', 'category'])
                ->withSum('orderItems as units_sold', 'quantity')
                ->orderByDesc(DB::raw('COALESCE(units_sold, 0)'))
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->with(['brand', 'category', 'primaryImage'])
            ->withSum('orderItems as units_sold', 'quantity')
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('id', $request->integer('category')));
            })
            ->when($request->filled('brand'), function ($query) use ($request): void {
                $query->whereHas('brand', fn ($brandQuery) => $brandQuery->where('id', $request->integer('brand')));
            })
            ->when($request->filled('min_price'), function ($query) use ($request): void {
                $query->where('price', '>=', $request->input('min_price'));
            })
            ->when($request->filled('max_price'), function ($query) use ($request): void {
                $query->where('price', '<=', $request->input('max_price'));
            })
            ->when($request->filled('availability'), function ($query) use ($request): void {
                if ($request->string('availability')->toString() === 'in_stock') {
                    $query->where('stock', '>', 0);
                }

                if ($request->string('availability')->toString() === 'low_stock') {
                    $query->whereBetween('stock', [1, 6]);
                }

                if ($request->string('availability')->toString() === 'sold_out') {
                    $query->where('stock', '<=', 0);
                }
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('character_name', 'like', "%{$search}%")
                        ->orWhere('series', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->string('sort')->toString() === 'price_asc',
                fn (Builder $query): Builder => $query->orderBy('price'),
                fn (Builder $query): Builder => $query
            )
            ->when(
                $request->string('sort')->toString() === 'price_desc',
                fn (Builder $query): Builder => $query->orderByDesc('price'),
                fn (Builder $query): Builder => $query
            )
            ->when(
                $request->string('sort')->toString() === 'best_selling',
                fn (Builder $query): Builder => $query->orderByDesc(DB::raw('COALESCE(units_sold, 0)'))->latest(),
                fn (Builder $query): Builder => $query
            )
            ->when(
                ! in_array($request->string('sort')->toString(), ['price_asc', 'price_desc', 'best_selling'], true),
                fn (Builder $query): Builder => $query->latest(),
                fn (Builder $query): Builder => $query
            )
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->isActive, 404);

        $product->load(['brand', 'category', 'images', 'tags', 'reviews.user']);

        return view('shop.show', [
            'product' => $product,
            'averageRating' => round((float) $product->reviews->avg('rating'), 1),
            'relatedProducts' => Product::active()
                ->with('primaryImage')
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
