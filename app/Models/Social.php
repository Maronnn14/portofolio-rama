<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $fillable = [
        'platform',
        'url',
        'label',
        'icon',
        'visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
