<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'name',
        'slug',
        'role',
        'organization',
        'period_start',
        'period_end',
        'description',
        'body',
        'links',
        'images',
        'cover_image',
        'status',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'links' => 'array',
            'images' => 'array',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getCoverUrlAttribute(): ?string
    {
        return Project::mediaUrl($this->cover_image);
    }

    public function imageUrls(): array
    {
        return collect($this->images ?? [])
            ->filter()
            ->map(fn ($path) => ['url' => Project::mediaUrl($path), 'caption' => null])
            ->values()
            ->all();
    }
}
