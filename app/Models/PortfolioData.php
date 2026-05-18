<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioData extends Model
{
    protected $fillable = [
        'section',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
