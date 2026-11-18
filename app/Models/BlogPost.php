<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'cover_image',
        'tags',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function slugSource(): string
    {
        return 'title';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (blank($this->published_at) || $this->published_at->isPast());
    }

    public function getCoverUrlAttribute(): ?string
    {
        return Project::mediaUrl($this->cover_image);
    }

    /** Ringkasan: pakai excerpt manual, atau potong dari isi artikel. */
    public function getSummaryAttribute(): string
    {
        $text = filled($this->excerpt) ? $this->excerpt : strip_tags((string) $this->body);

        return Str::limit(trim(preg_replace('/\s+/', ' ', $text)), 180);
    }

    public function getReadingMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags((string) ($this->body ?: $this->excerpt)));

        return max(1, (int) ceil($words / 200));
    }

    public function getDisplayDateAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->published_at ?? $this->created_at;
    }
}
