<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequestLog extends Model
{
    protected $fillable = ['user_id', 'from_status', 'to_status', 'comment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
