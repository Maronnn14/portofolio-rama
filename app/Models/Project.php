<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'name',
        'slug',
        'short_desc',
        'full_desc',
        'body',
        'category',
        'status',
        'published_at',
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
            'published_at' => 'datetime',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => self::mediaUrl($value),
        );
    }

    public function galleryUrls(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        if ($images->isNotEmpty()) {
            return $images->map(fn (ProjectImage $image) => [
                'url' => $image->url,
                'caption' => $image->caption,
            ])->all();
        }

        return collect($this->gallery ?? [])
            ->filter()
            ->map(fn ($url) => ['url' => self::mediaUrl($url), 'caption' => null])
            ->values()
            ->all();
    }

    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags(Str::markdown($this->body ?: $this->full_desc ?: '')));

        return max(1, (int) ceil($words / 200));
    }

    public static function mediaUrl(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', 'data:'])) {
            return $value;
        }

        return asset('storage/'.ltrim($value, '/'));
    }
}
