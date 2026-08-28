<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'slug',
        'description',
        'duration',
        'level',
        'students_count',
        'price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'students_count' => 'integer',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}