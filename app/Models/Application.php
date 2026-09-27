<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\Audience;
use App\Enums\Gender;
use App\Services\SequenceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/** Candidature à une offre (école ou programme professionnel). */
class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'offering_id', 'audience', 'first_name', 'last_name', 'birth_date', 'birth_place', 'gender', 'nationality',
        'phone', 'whatsapp', 'email', 'address', 'guardian', 'education', 'experience', 'documents', 'motivation',
        'portfolio_url', 'status', 'source', 'interview_at', 'interview_location', 'consent_at', 'consent_version',
        'submitted_at', 'decided_at', 'decided_by', 'student_id', 'ip_hash',
    ];

    protected $casts = [
        'audience' => Audience::class,
        'status' => ApplicationStatus::class,
        'gender' => Gender::class,
        'birth_date' => 'date',
        'guardian' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'documents' => 'array',
        'interview_at' => 'datetime',
        'consent_at' => 'datetime',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Application $application) {
            $application->uuid ??= (string) Str::uuid();
            $application->status ??= ApplicationStatus::SUBMITTED;
            $application->submitted_at ??= now();
            if (blank($application->reference)) {
                $year = now()->year;
                $prefix = "CAND-{$year}-";
                $number = app(SequenceService::class)->next(
                    "application:{$year}",
                    fn () => SequenceService::maxSuffix('applications', 'reference', $prefix)
                );
                $application->reference = $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            }
            if (blank($application->audience) && $application->offering_id) {
                $application->audience = Offering::with('cohort.program')->find($application->offering_id)?->cohort->program->audience;
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ApplicationEvent::class)->latest('created_at')->latest('id');
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class);
    }
}
