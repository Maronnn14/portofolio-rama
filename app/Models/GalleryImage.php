<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'url',
        'alt',
        'category',
        'visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
