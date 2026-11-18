<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

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

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value && ! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://') && ! str_starts_with($value, 'data:')
                ? asset('storage/'.ltrim($value, '/'))
                : $value,
        );
    }
}
