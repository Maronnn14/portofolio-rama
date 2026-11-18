<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::saving(function ($model) {
            $source = method_exists($model, 'slugSource') ? $model->slugSource() : 'name';

            if (blank($model->slug) && filled($model->{$source})) {
                $model->slug = static::uniqueSlug((string) $model->{$source}, $model->getKey());
            }
        });
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'item';
        $slug = $base;
        $i = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Public URLs use the slug, but legacy API/Filament routes may still pass
     * a numeric id. Accept either so old links keep working.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->resolveRouteBindingQuery($this->newQuery(), $value, $field ?? $this->getRouteKeyName())
            ->orWhere($this->getKeyName(), $value)
            ->first();
    }
}
