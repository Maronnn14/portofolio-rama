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
        'icon',
        'teaser',
        'description',
        'proficiency',
        'level',
        'related_projects',
        'gallery',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
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
