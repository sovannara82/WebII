<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
