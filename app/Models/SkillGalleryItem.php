<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SkillGalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
