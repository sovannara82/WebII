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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

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

    /**
     * @var list<string>
     */
    private array $creatableTables = [
        'roles',
        'users',
        'categories',
        'brands',
        'products',
        'product_images',
        'tags',
        'order_statuses',
        'payment_statuses',
        'payment_methods',
        'banners',
        'coupons',
    ];

    /**
     * @var list<string>
     */
    private array $editableTables = [
        'roles',
        'users',
        'categories',
        'brands',
        'products',
        'product_images',
        'tags',
        'product_tags',
        'order_statuses',
        'payment_statuses',
        'payment_methods',
        'banners',
        'coupons',
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
            'columns' => $this->visibleColumns($table),
            'creatableTables' => $this->creatableTables,
            'editableTables' => $this->editableTables,
            'records' => $records,
            'roles' => Role::orderBy('name')->get(),
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

    public function update(Request $request, string $table, string $id): RedirectResponse
    {
        abort_unless(array_key_exists($table, $this->models), 404);
        abort_unless(in_array($table, $this->editableTables, true), 422, 'This table is read-only from the generic admin screen.');

        if ($table === 'product_tags') {
            $this->updateProductTag($request, $id);

            return back()->with('status', "{$table} record updated.");
        }

        $model = $this->models[$table];
        abort_unless($model, 422, 'This table is read-only from the generic admin screen.');

        $record = $model::findOrFail($id);

        if ($table === 'roles') {
            $this->guardAdminRoleUpdate($record, $request);
        }

        $record->update($this->validatedData($request, $table, $record));

        return back()->with('status', "{$table} record updated.");
    }

    public function destroy(string $table, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($table, $this->models), 404);

        $model = $this->models[$table];
        abort_unless($model, 422, 'This table is read-only from the generic admin screen.');

        $record = $model::findOrFail($id);

        if ($table === 'users') {
            $this->guardAdminUserDeletion($record);
        }

        if ($table === 'roles') {
            $this->guardAdminRoleDeletion($record);
        }

        $record->delete();

        return back()->with('status', "{$table} record deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $table, ?Model $record = null): array
    {
        $data = match ($table) {
            'roles' => $request->validate(['name' => ['required', 'max:50', $this->uniqueRule('roles', 'name', $record)]]),
            'users' => $request->validate([
                'role_id' => [$record ? 'nullable' : 'prohibited', 'integer', Rule::exists('roles', 'id')],
                'name' => ['required', 'max:255'],
                'username' => ['nullable', 'max:50', $this->uniqueRule('users', 'username', $record)],
                'email' => ['required', 'email', 'max:255', $this->uniqueRule('users', 'email', $record)],
                'password' => [$record ? 'nullable' : 'required', 'string', 'min:8'],
                'phone' => ['nullable', 'max:20'],
                'image' => ['nullable', 'image', 'max:4096'],
                'address' => ['nullable', 'max:255'],
                'city' => ['nullable', 'max:100'],
                'province' => ['nullable', 'max:100'],
            ]),
            'categories' => $request->validate([
                'name' => ['required', 'max:100', $this->uniqueRule('categories', 'name', $record)],
                'description' => ['nullable', 'max:255'],
                'image' => ['nullable', 'image', 'max:4096'],
            ]),
            'brands' => $request->validate([
                'name' => ['required', 'max:100', $this->uniqueRule('brands', 'name', $record)],
                'image' => ['nullable', 'image', 'max:4096'],
            ]),
            'products' => $request->validate([
                'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
                'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
                'name' => ['required', 'max:150'],
                'character_name' => ['nullable', 'max:100'],
                'series' => ['nullable', 'max:100'],
                'material' => ['nullable', 'max:50'],
                'height' => ['nullable', 'numeric', 'min:0'],
                'price' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'description' => ['nullable'],
                'isActive' => ['required', 'boolean'],
            ]),
            'product_images' => $request->validate([
                'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
                'image' => [$record ? 'nullable' : 'required', 'image', 'max:4096'],
                'is_primary' => ['required', 'boolean'],
            ]),
            'tags' => $request->validate(['name' => ['required', 'max:50', $this->uniqueRule('tags', 'name', $record)]]),
            'order_statuses' => $request->validate(['name' => ['required', 'max:30', $this->uniqueRule('order_statuses', 'name', $record)]]),
            'payment_statuses' => $request->validate(['name' => ['required', 'max:30', $this->uniqueRule('payment_statuses', 'name', $record)]]),
            'payment_methods' => $request->validate(['name' => ['required', 'max:50', $this->uniqueRule('payment_methods', 'name', $record)]]),
            'banners' => $request->validate([
                'title' => ['required', 'max:100'],
                'eyebrow' => ['nullable', 'max:80'],
                'subtitle' => ['nullable', 'max:255'],
                'button_text' => ['nullable', 'max:40'],
                'image' => [$record ? 'nullable' : 'required', 'image', 'max:4096'],
                'link' => ['nullable', 'max:255'],
            ]),
            'coupons' => $request->validate([
                'code' => ['required', 'max:50', $this->uniqueRule('coupons', 'code', $record)],
                'type' => ['required', Rule::in(['fixed', 'percent'])],
                'value' => ['required', 'numeric', 'min:0'],
                'min_order' => ['nullable', 'numeric', 'min:0'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'usage_limit' => ['nullable', 'integer', 'min:1'],
            ]),
            default => abort(422, 'This table is read-only from the generic admin screen.'),
        };

        if (in_array($table, ['categories', 'brands', 'products', 'tags', 'order_statuses', 'payment_statuses', 'payment_methods', 'banners', 'coupons'], true)) {
            $data['user_id'] = $record?->getAttribute('user_id') ?? $request->user()->id;
        }

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store("admin/{$table}", 'public'));
        } elseif ($record && array_key_exists('image', $data)) {
            unset($data['image']);
        }

        if ($table === 'users') {
            if ($record === null) {
                $data['role_id'] = $this->customerRoleId();
            } elseif ($record instanceof User) {
                $data['role_id'] = $this->validatedUserRoleId($record, $data['role_id'] ?? null);
            }

            if (filled($data['password'] ?? null)) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
        }

        if ($table === 'coupons' && $record === null) {
            $data['used_count'] = 0;
        }

        return $data;
    }

    /**
     * @return list<string>
     */
    private function visibleColumns(string $table): array
    {
        $columns = Schema::getColumnListing($table);

        if ($table === 'banners') {
            return array_values(array_diff($columns, ['button_text']));
        }

        return $columns;
    }

    private function customerRoleId(): int
    {
        return Role::firstOrCreate(['name' => 'Customer'])->id;
    }

    private function adminRoleId(): ?int
    {
        return Role::where('name', 'Admin')->value('id');
    }

    private function validatedUserRoleId(User $user, ?int $roleId): int
    {
        $adminRoleId = $this->adminRoleId();

        if ($user->role_id === $adminRoleId) {
            abort_if($roleId !== null && $roleId !== $adminRoleId, 422, 'The only admin account cannot be demoted.');

            return $user->role_id;
        }

        abort_if($roleId !== null && $roleId === $adminRoleId, 422, 'Only one admin account is allowed.');

        return $roleId ?? $user->role_id ?? $this->customerRoleId();
    }

    private function guardAdminUserDeletion(Model $record): void
    {
        abort_if(
            $record instanceof User && $record->role_id === $this->adminRoleId(),
            422,
            'The only admin account cannot be deleted.'
        );
    }

    private function guardAdminRoleUpdate(Model $record, Request $request): void
    {
        abort_if(
            $record instanceof Role && $record->name === 'Admin' && $request->input('name') !== 'Admin',
            422,
            'The Admin role cannot be renamed because only one admin account is allowed.'
        );
    }

    private function guardAdminRoleDeletion(Model $record): void
    {
        abort_if(
            $record instanceof Role && $record->name === 'Admin',
            422,
            'The Admin role cannot be deleted because only one admin account is allowed.'
        );
    }

    private function uniqueRule(string $table, string $column, ?Model $record): Unique
    {
        $rule = Rule::unique($table, $column);

        if ($record) {
            $rule->ignore($record->getKey());
        }

        return $rule;
    }

    private function updateProductTag(Request $request, string $key): void
    {
        [$originalProductId, $originalTagId] = array_pad(explode('-', $key, 2), 2, null);
        abort_unless($originalProductId && $originalTagId, 404);

        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'tag_id' => ['required', 'integer', Rule::exists('tags', 'id')],
        ]);

        $exists = DB::table('product_tags')
            ->where('product_id', $originalProductId)
            ->where('tag_id', $originalTagId)
            ->exists();

        abort_unless($exists, 404);

        $duplicate = DB::table('product_tags')
            ->where('product_id', $data['product_id'])
            ->where('tag_id', $data['tag_id'])
            ->where(function ($query) use ($originalProductId, $originalTagId): void {
                $query
                    ->where('product_id', '!=', $originalProductId)
                    ->orWhere('tag_id', '!=', $originalTagId);
            })
            ->exists();

        abort_if($duplicate, 422, 'This product tag already exists.');

        DB::table('product_tags')
            ->where('product_id', $originalProductId)
            ->where('tag_id', $originalTagId)
            ->update($data);
    }
}
