<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'eyebrow',
        'subtitle',
        'button_text',
        'image',
        'link',
        'user_id',
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
}
