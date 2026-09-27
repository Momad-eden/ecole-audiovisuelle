<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use RevalidatesFrontend;

    protected $fillable = ['from_path', 'to_path', 'status_code'];
}
