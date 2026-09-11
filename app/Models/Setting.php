<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'description',
        'phone',
        'email',
        'address',
        'website',
        'logo',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'linkedin',
        'twitter',
        'whatsapp',
    ];
}