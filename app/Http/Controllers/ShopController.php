<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->with(['brand', 'category', 'primaryImage'])
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('id', $request->integer('category')));
            })
            ->when($request->filled('brand'), function ($query) use ($request): void {
                $query->whereHas('brand', fn ($brandQuery) => $brandQuery->where('id', $request->integer('brand')));
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
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('shop.index', [
            'banners' => Banner::latest()->take(3)->get(),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'featuredProducts' => Product::active()->with(['primaryImage', 'brand'])->latest()->take(4)->get(),
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
