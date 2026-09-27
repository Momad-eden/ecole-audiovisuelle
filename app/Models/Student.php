<?php

namespace App\Models;

use App\Enums\Gender;
use App\Services\StudentNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number', 'first_name', 'last_name', 'gender', 'birth_date', 'birth_place',
        'nationality', 'phone', 'email', 'address', 'photo', 'notes',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'birth_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (blank($student->student_number)) {
                $student->student_number = app(StudentNumberService::class)->generate();
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
