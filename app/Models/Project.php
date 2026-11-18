<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_desc',
        'full_desc',
        'category',
        'status',
        'thumbnail',
        'tech',
        'live_url',
        'source_url',
        'featured',
        'gallery',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tech' => 'array',
            'gallery' => 'array',
            'featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value && ! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://') && ! str_starts_with($value, 'data:')
                ? asset('storage/'.ltrim($value, '/'))
                : $value,
        );
    }
}
