<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminDataController extends Controller
{
    /**
     * @var array<string, class-string<Model>|null>
     */
    private array $models = [
        'roles' => Role::class,
        'users' => User::class,
        'categories' => Category::class,
        'brands' => Brand::class,
        'products' => Product::class,
        'product_images' => ProductImage::class,
        'tags' => Tag::class,
        'product_tags' => null,
        'carts' => Cart::class,
        'cart_items' => CartItem::class,
        'order_statuses' => OrderStatus::class,
        'payment_statuses' => PaymentStatus::class,
        'payment_methods' => PaymentMethod::class,
        'orders' => Order::class,
        'order_items' => OrderItem::class,
        'payments' => Payment::class,
        'wishlists' => Wishlist::class,
        'reviews' => Review::class,
        'banners' => Banner::class,
        'coupons' => Coupon::class,
        'order_coupons' => null,
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $table = $request->query('table', 'products');
        abort_unless(array_key_exists($table, $this->models), 404);

        $model = $this->models[$table];
        $records = $model
            ? $model::query()->latest('id')->paginate(12)->withQueryString()
            : DB::table($table)->paginate(12)->withQueryString();

        return view('admin.data', [
            'columns' => Schema::getColumnListing($table),
            'creatableTables' => ['roles', 'categories', 'brands', 'tags', 'order_statuses', 'payment_statuses', 'payment_methods', 'banners', 'coupons'],
            'records' => $records,
            'table' => $table,
            'tables' => array_keys($this->models),
        ]);
    }

    public function store(Request $request, string $table): RedirectResponse
    {
        abort_unless(array_key_exists($table, $this->models), 404);

        $model = $this->models[$table];
        abort_unless($model, 422, 'This table is read-only from the generic admin screen.');

        $data = $this->validatedData($request, $table);
        $model::create($data);

        return back()->with('status', "{$table} record created.");
    }

    public function destroy(string $table, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($table, $this->models), 404);

        $model = $this->models[$table];
        abort_unless($model, 422, 'This table is read-only from the generic admin screen.');

        $model::findOrFail($id)->delete();

        return back()->with('status', "{$table} record deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $table): array
    {
        return match ($table) {
            'roles' => $request->validate(['name' => ['required', 'max:50', Rule::unique('roles', 'name')]]),
            'categories' => $request->validate([
                'name' => ['required', 'max:100', Rule::unique('categories', 'name')],
                'description' => ['nullable', 'max:255'],
                'image' => ['nullable', 'max:255'],
            ]) + ['user_id' => $request->user()->id],
            'brands' => $request->validate([
                'name' => ['required', 'max:100', Rule::unique('brands', 'name')],
                'image' => ['nullable', 'max:255'],
            ]) + ['user_id' => $request->user()->id],
            'tags' => $request->validate(['name' => ['required', 'max:50', Rule::unique('tags', 'name')]]) + ['user_id' => $request->user()->id],
            'order_statuses' => $request->validate(['name' => ['required', 'max:30', Rule::unique('order_statuses', 'name')]]) + ['user_id' => $request->user()->id],
            'payment_statuses' => $request->validate(['name' => ['required', 'max:30', Rule::unique('payment_statuses', 'name')]]) + ['user_id' => $request->user()->id],
            'payment_methods' => $request->validate(['name' => ['required', 'max:50', Rule::unique('payment_methods', 'name')]]) + ['user_id' => $request->user()->id],
            'banners' => $request->validate([
                'title' => ['required', 'max:100'],
                'image' => ['required', 'max:255'],
                'link' => ['nullable', 'max:255'],
            ]) + ['user_id' => $request->user()->id],
            'coupons' => $request->validate([
                'code' => ['required', 'max:50', Rule::unique('coupons', 'code')],
                'type' => ['required', Rule::in(['fixed', 'percent'])],
                'value' => ['required', 'numeric', 'min:0'],
                'min_order' => ['nullable', 'numeric', 'min:0'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'usage_limit' => ['nullable', 'integer', 'min:1'],
            ]) + ['user_id' => $request->user()->id, 'used_count' => 0],
            default => abort(422, 'This table is read-only from the generic admin screen.'),
        };
    }
}
