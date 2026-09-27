<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Historique d'une candidature : changements d'étape, notes, messages. */
class ApplicationEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['application_id', 'type', 'from_status', 'to_status', 'comment', 'user_id'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
