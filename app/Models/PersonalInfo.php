<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    protected $table = 'personal_info';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get all personal info as a key-value map.
     */
    public static function getAll(): array
    {
        return static::query()
            ->pluck('value', 'key')
            ->all();
    }
}
