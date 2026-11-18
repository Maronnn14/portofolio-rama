<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'skill_type',
        'icon',
        'teaser',
        'description',
        'proficiency',
        'level',
        'featured',
        'related_projects',
        'gallery',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
            'featured' => 'boolean',
            'related_projects' => 'array',
            'gallery' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function projectLinks(): HasMany
    {
        return $this->hasMany(SkillProjectLink::class)->orderBy('sort_order');
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(SkillGalleryItem::class)->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /** Coding / tech stack skills. */
    public function scopeTech($query)
    {
        return $query->where('skill_type', 'tech');
    }

    /** Non-coding hard skills shown in the "Beyond the Code" section. */
    public function scopeOther($query)
    {
        return $query->where('skill_type', 'other');
    }

    protected static function booted(): void
    {
        static::deleting(function (Skill $skill) {
            foreach ($skill->galleryItems as $item) {
                Storage::delete($item->image_path);
            }
            $skill->galleryItems()->delete();
            $skill->projectLinks()->delete();
        });
    }
}
