<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Page composée de blocs. L'admin édite `draft_blocks` ; « Publier » copie le brouillon
 * dans `blocks` (version en ligne) et enregistre une révision restaurable.
 */
class Page extends Model
{
    use HasFactory, HasPublication, HasUniqueSlug;

    protected $fillable = ['title', 'slug', 'type', 'blocks', 'draft_blocks', 'seo', 'is_locked', 'status', 'published_at'];

    protected $casts = [
        'blocks' => 'array',
        'draft_blocks' => 'array',
        'seo' => 'array',
        'is_locked' => 'boolean',
    ];

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class)->latest('created_at')->latest('id');
    }

    public function hasUnpublishedChanges(): bool
    {
        return $this->draft_blocks !== $this->blocks;
    }

    public function publish(?User $user = null): void
    {
        $this->forceFill([
            'blocks' => $this->draft_blocks,
            'status' => PublicationStatus::PUBLISHED,
            'published_at' => $this->published_at ?? now(),
        ])->save();

        $this->revisions()->create(['title' => $this->title, 'blocks' => $this->blocks, 'user_id' => $user?->id]);
    }

    public function restore(PageRevision $revision): void
    {
        $this->update(['draft_blocks' => $revision->blocks]);
    }
}
