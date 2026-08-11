<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',
        'slug',
        'description',
        'duration',
        'price',
        'image',
        'is_active',

    ];


    protected $casts = [

        'price' => 'decimal:2',

        'is_active' => 'boolean',

    ];


    public function students()
    {
        return $this->hasMany(
            Student::class
        );
    }


    public function admissions()
    {
        return $this->hasMany(
            Admission::class
        );
    }
}