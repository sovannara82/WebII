<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'image',
        'is_primary',
    ];

    public function getImageAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        if (Str::startsWith($value, 'storage/')) {
            return asset($value);
        }

        return Storage::url($value);
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, ProductImage>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
