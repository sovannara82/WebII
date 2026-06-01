<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);

        $admin = User::firstOrCreate([
            'email' => 'admin@infinityfigure.com',
        ], [
            'role_id' => $adminRole->id,
            'name' => 'Infinity Figures Admin',
            'username' => 'admin',
            'password' => Hash::make('kruyadmin123123'),
        ]);

        $categories = collect([
            ['name' => 'Scale Figures', 'description' => 'Highly detailed 1/7 and 1/8 display figures.'],
            ['name' => 'Nendoroid', 'description' => 'Cute articulated chibi figures with face plates.'],
            ['name' => 'Action Figures', 'description' => 'Poseable figures with effect parts and accessories.'],
            ['name' => 'Statues', 'description' => 'Premium sculpted centerpiece collectibles.'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['name'] => Category::firstOrCreate(['name' => $category['name']], $category + ['user_id' => $admin->id]),
        ]);

        $brands = collect(['Good Smile Company', 'Kotobukiya', 'Bandai Spirits', 'Aniplex'])
            ->mapWithKeys(fn (string $brand): array => [
                $brand => Brand::firstOrCreate(['name' => $brand], ['user_id' => $admin->id]),
            ]);

        $tags = collect(['Limited', 'Pre-order', 'PVC', 'Gift-ready', 'Restock'])
            ->mapWithKeys(fn (string $tag): array => [
                $tag => Tag::firstOrCreate(['name' => $tag], ['user_id' => $admin->id]),
            ]);

        $products = collect([
            [
                'name' => 'Sakura Memory 1/7 Scale Figure',
                'character_name' => 'Sakura Airi',
                'series' => 'Cherry Arc',
                'category' => 'Scale Figures',
                'brand' => 'Good Smile Company',
                'material' => 'PVC / ABS',
                'height' => 24.50,
                'price' => 149.99,
                'stock' => 8,
                'image' => 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=900&q=80',
                'description' => 'A graceful display figure with translucent petals, soft pastel paintwork, and a compact base for glass cabinets.',
            ],
            [
                'name' => 'Shadow Ronin Articulated Figure',
                'character_name' => 'Kaito Ren',
                'series' => 'Neon Blade',
                'category' => 'Action Figures',
                'brand' => 'Bandai Spirits',
                'material' => 'PVC / Die-cast',
                'height' => 16.00,
                'price' => 84.50,
                'stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1608889175123-8ee362201f81?auto=format&fit=crop&w=900&q=80',
                'description' => 'A poseable swordsman figure with alternate hands, masked head, and two clear slash effects.',
            ],
            [
                'name' => 'Mage Atelier Nendoroid Set',
                'character_name' => 'Mira',
                'series' => 'Atelier Moon',
                'category' => 'Nendoroid',
                'brand' => 'Good Smile Company',
                'material' => 'PVC',
                'height' => 10.00,
                'price' => 62.00,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1612036782180-6f0b6cd846fe?auto=format&fit=crop&w=900&q=80',
                'description' => 'A cheerful chibi set with spell book, staff, potion bottle, and three expressive face plates.',
            ],
            [
                'name' => 'Crimson Dragon Resin Statue',
                'character_name' => 'Ryuka',
                'series' => 'Dragon Oath',
                'category' => 'Statues',
                'brand' => 'Kotobukiya',
                'material' => 'Resin',
                'height' => 32.00,
                'price' => 279.00,
                'stock' => 2,
                'image' => 'https://images.unsplash.com/photo-1608278047522-58806a6ac85b?auto=format&fit=crop&w=900&q=80',
                'description' => 'A dramatic resin statue with layered armor, metallic red paint, and a numbered customer base.',
            ],
        ])->map(function (array $data) use ($admin, $brands, $categories, $tags): Product {
            $product = Product::updateOrCreate([
                'name' => $data['name'],
            ], [
                'category_id' => $categories[$data['category']]->id,
                'brand_id' => $brands[$data['brand']]->id,
                'character_name' => $data['character_name'],
                'series' => $data['series'],
                'material' => $data['material'],
                'height' => $data['height'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'description' => $data['description'],
                'isActive' => true,
                'user_id' => $admin->id,
            ]);

            $product->images()->updateOrCreate([
                'image' => $data['image'],
            ], [
                'is_primary' => true,
            ]);
            $product->tags()->syncWithoutDetaching($tags->random(2)->pluck('id'));

            return $product;
        });

        Banner::updateOrCreate([
            'title' => 'New customer arrivals',
        ], [
            'image' => 'https://images.unsplash.com/photo-1608889825205-eebdb9fc5806?auto=format&fit=crop&w=1400&q=80',
            'link' => route('products.index'),
            'user_id' => $admin->id,
        ]);

        $orderStatus = OrderStatus::firstOrCreate(['name' => 'Processing'], ['user_id' => $admin->id]);
        $paymentStatus = PaymentStatus::firstOrCreate(['name' => 'Paid'], ['user_id' => $admin->id]);
        $paymentMethod = PaymentMethod::firstOrCreate(['name' => 'ABA Pay'], ['user_id' => $admin->id]);

        $firstProduct = $products->first();
        $order = Order::firstOrCreate([
            'user_id' => $customer->id,
            'status_id' => $orderStatus->id,
        ], [
            'payment_status_id' => $paymentStatus->id,
            'payment_method_id' => $paymentMethod->id,
            'address' => 'Street 271',
            'city' => 'Phnom Penh',
            'phone' => '+855 12 345 678',
            'subtotal' => $firstProduct->price,
            'discount' => 0,
            'shipping_fee' => 3.50,
            'total' => (float) $firstProduct->price + 3.50,
        ]);

        OrderItem::firstOrCreate([
            'order_id' => $order->id,
            'product_id' => $firstProduct->id,
        ], [
            'quantity' => 1,
            'price' => $firstProduct->price,
            'subtotal' => $firstProduct->price,
        ]);

        $coupon = Coupon::firstOrCreate([
            'code' => 'INFINITY10',
        ], [
            'type' => 'percent',
            'value' => 10,
            'min_order' => 50,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'usage_limit' => 100,
            'used_count' => 1,
            'user_id' => $admin->id,
        ]);

        $order->coupons()->syncWithoutDetaching([
            $coupon->id => ['discount' => round((float) $firstProduct->price * 0.1, 2)],
        ]);

        Payment::firstOrCreate([
            'order_id' => $order->id,
        ], [
            'amount' => $order->total,
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $paymentStatus->id,
            'paid_at' => now(),
        ]);

        Wishlist::firstOrCreate([
            'user_id' => $customer->id,
            'product_id' => $products->last()->id,
        ]);

        Review::firstOrCreate([
            'user_id' => $customer->id,
            'product_id' => $firstProduct->id,
        ], [
            'rating' => 5,
            'comment' => 'Beautiful sculpt and strong shelf presence.',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        $cart->items()->firstOrCreate([
            'product_id' => $products->get(1)->id,
        ], [
            'quantity' => 1,
            'price' => $products->get(1)->price,
        ]);
    }
}
