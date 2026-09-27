<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Services\SequenceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Demande de devis ou de réservation (studio, matériel, prestation, salle). */
class BookingRequest extends Model
{
    protected $fillable = ['reference', 'type', 'status', 'name', 'organization', 'phone', 'email', 'starts_on', 'ends_on', 'location', 'attendees', 'message', 'items', 'internal_notes', 'quoted_amount', 'ip_hash'];

    protected $attributes = ['status' => 'new'];

    protected $casts = [
        'type' => BookingType::class,
        'status' => BookingStatus::class,
        'starts_on' => 'date',
        'ends_on' => 'date',
        'items' => 'array',
        'attendees' => 'integer',
        'quoted_amount' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (BookingRequest $request) {
            if (blank($request->reference)) {
                $prefix = 'DEM-'.now()->year.'-';
                $number = app(SequenceService::class)->next(
                    'booking_request:'.now()->year,
                    fn () => SequenceService::maxSuffix('booking_requests', 'reference', $prefix)
                );
                $request->reference = $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            }
        });

        // Toute demande commence son historique à sa création.
        static::created(fn (BookingRequest $request) => $request->logs()->create(['to_status' => $request->status->value, 'comment' => 'Demande reçue']));
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BookingRequestLog::class);
    }
}
