<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;

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
            ->map(function ($value, $key) {
                if (in_array($key, ['profileImage', 'homeProfileImage', 'aboutProfileImage', 'photo']) && $value) {
                    if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://') && ! str_starts_with($value, 'data:')) {
                        return asset('storage/'.ltrim($value, '/'));
                    }
                }

                return $value;
            })
            ->all();
    }
}
