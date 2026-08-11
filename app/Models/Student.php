<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [

        'student_number',
        'photo',

        'first_name',
        'last_name',

        'gender',

        'birth_date',
        'birth_place',
        'nationality',

        'phone',
        'email',
        'address',

        'course_id',

        'registration_date',

        'status',

        'notes',

    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admission()
    {
        return $this->hasOne(Admission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
