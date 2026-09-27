<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentCategory extends Model
{
    use HasUniqueSlug, RevalidatesFrontend;

    protected $fillable = ['name', 'slug', 'summary', 'position'];

    protected static function slugSource(): string
    {
        return 'name';
    }

    public function items(): HasMany
    {
        return $this->hasMany(EquipmentItem::class)->orderBy('position');
    }
}
