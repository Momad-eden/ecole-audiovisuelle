<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Slug unique généré depuis le titre à la création, puis stable (les liens partagés ne cassent pas).
 */
trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function ($model) {
            if (blank($model->slug)) {
                $model->slug = static::uniqueSlug((string) $model->{static::slugSource()});
            }
        });
    }

    protected static function slugSource(): string
    {
        return 'title';
    }

    public static function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'element';
        $slug = $base;
        $suffix = 2;
        $query = fn (string $candidate) => in_array(SoftDeletes::class, class_uses_recursive(static::class), true)
            ? static::withTrashed()->where('slug', $candidate)->exists()
            : static::where('slug', $candidate)->exists();

        while ($query($slug)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
