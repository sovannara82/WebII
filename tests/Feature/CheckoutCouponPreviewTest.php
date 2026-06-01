<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;

test('checkout shows coupon discount preview rows', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Scale Figures']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Sakura Memory Figure',
        'price' => 100,
        'stock' => 5,
        'isActive' => true,
    ]);
    $cart = Cart::create(['user_id' => $user->id]);
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100,
    ]);

    PaymentMethod::create(['name' => 'ABA Pay']);
    Coupon::create([
        'code' => 'SAVE10',
        'type' => 'percent',
        'value' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'used_count' => 0,
    ]);

    $response = $this->actingAs($user)->get(route('checkout.create'));

    $response
        ->assertOk()
        ->assertSee('SAVE10')
        ->assertSee('Discount')
        ->assertSee('Price after discount')
        ->assertSee('$105.00');
});
