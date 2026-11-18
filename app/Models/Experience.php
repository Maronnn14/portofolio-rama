<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'role',
        'slug',
        'company',
        'location',
        'start_date',
        'end_date',
        'type',
        'status',
        'published_at',
        'description',
        'body',
        'achievements',
        'cover_image',
        'tech',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tech' => 'array',
            'achievements' => 'array',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function slugSource(): string
    {
        return 'role';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeWork($query)
    {
        return $query->whereNotIn('type', ['Education']);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return Project::mediaUrl($this->cover_image);
    }
}
