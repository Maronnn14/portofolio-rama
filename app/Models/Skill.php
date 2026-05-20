<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
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
}
