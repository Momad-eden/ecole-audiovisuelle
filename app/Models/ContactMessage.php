<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = ['subject', 'name', 'email', 'phone', 'message', 'status', 'handled_by', 'ip_hash'];

    protected $casts = ['status' => ContactMessageStatus::class];

    public const SUBJECTS = [
        'information' => 'Demande d\'information',
        'partnership' => 'Partenariat',
        'press' => 'Presse',
        'visit' => 'Visite de l\'école',
        'other' => 'Autre',
    ];

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
