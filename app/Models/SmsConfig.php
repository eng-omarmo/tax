<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'config',
        'status',
    ];
}
