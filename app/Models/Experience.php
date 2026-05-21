<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;
    protected $fillable = [
        'role',
        'company',
        'location',
        'start_date',
        'end_date',
        'type',
        'description',
        'tech',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tech' => 'array',
            'sort_order' => 'integer',
        ];
    }
}
