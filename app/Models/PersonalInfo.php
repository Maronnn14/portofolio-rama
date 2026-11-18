<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PersonalInfo extends Model
{
    use HasFactory;

    protected $table = 'personal_info';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get all personal info as a key-value map, with public media URLs resolved.
     */
    public static function getAll(): array
    {
        $mediaKeys = [
            'profileImage', 'homeProfileImage', 'aboutProfileImage', 'photo', 'cv_file',
        ];

        return static::query()
            ->pluck('value', 'key')
            ->map(function ($value, $key) use ($mediaKeys) {
                if (in_array($key, $mediaKeys, true) && filled($value)) {
                    return Project::mediaUrl($value);
                }

                return $value;
            })
            ->all();
    }

    public static function cvUrl(): ?string
    {
        $path = static::query()->where('key', 'cv_file')->value('value');

        return Project::mediaUrl($path);
    }

    public static function cvUpdatedLabel(): ?string
    {
        $value = static::query()->where('key', 'cv_updated_at')->value('value');

        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->translatedFormat('F Y');
        } catch (\Throwable) {
            return $value;
        }
    }

    public static function displayName(array $personal): string
    {
        return $personal['fullName']
            ?? $personal['name']
            ?? $personal['nickname']
            ?? 'Portfolio';
    }

    public static function shortLocation(array $personal): ?string
    {
        return $personal['location'] ?? null;
    }

    public static function initials(array $personal): string
    {
        return Str::of(static::displayName($personal))
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }
}
