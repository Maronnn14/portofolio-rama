<?php

namespace App\Models;

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
}
