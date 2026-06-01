<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('home page shows showcase sections without full catalog controls', function () {
    $category = Category::create(['name' => 'Scale Figures']);

    Product::create([
        'category_id' => $category->id,
        'name' => 'Sakura Memory 1/7 Scale Figure',
        'price' => 149.99,
        'stock' => 8,
        'isActive' => true,
    ]);

    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('New Arrivals')
        ->assertSee('Best Sellers')
        ->assertSee('View All Products')
        ->assertDontSee('Featured Products')
        ->assertDontSee('Browse Products')
        ->assertDontSee('All brands');
});

test('products page lists catalog filters sorting and pagination', function () {
    $category = Category::create(['name' => 'Action Figures']);
    $brand = Brand::create(['name' => 'Bandai Spirits']);

    Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'name' => 'Shadow Ronin Articulated Figure',
        'price' => 84.50,
        'stock' => 4,
        'isActive' => true,
    ]);

    $response = $this->get('/products?search=Shadow&category='.$category->id.'&min_price=50&max_price=100&sort=price_asc');

    $response
        ->assertOk()
        ->assertSee('Browse Products')
        ->assertSee('Search figures')
        ->assertSee('All categories')
        ->assertSee('Price: Low to High')
        ->assertSee('Shadow Ronin Articulated Figure');
});

test('products page can sort by best selling products', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Nendoroid']);
    $slowProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Quiet Mage Nendoroid',
        'price' => 55,
        'stock' => 10,
        'isActive' => true,
    ]);
    $bestProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Popular Mage Nendoroid',
        'price' => 65,
        'stock' => 10,
        'isActive' => true,
    ]);
    $orderStatus = OrderStatus::create(['name' => 'Processing']);
    $paymentStatus = PaymentStatus::create(['name' => 'Paid']);
    $paymentMethod = PaymentMethod::create(['name' => 'ABA Pay']);
    $order = Order::create([
        'user_id' => $user->id,
        'status_id' => $orderStatus->id,
        'payment_status_id' => $paymentStatus->id,
        'payment_method_id' => $paymentMethod->id,
        'subtotal' => 130,
        'discount' => 0,
        'shipping_fee' => 0,
        'total' => 130,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $bestProduct->id,
        'quantity' => 2,
        'price' => 65,
        'subtotal' => 130,
    ]);

    $response = $this->get('/products?sort=best_selling');

    $response
        ->assertOk()
        ->assertSeeInOrder([
            'Popular Mage Nendoroid',
            'Quiet Mage Nendoroid',
        ]);
});
