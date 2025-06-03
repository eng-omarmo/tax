<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'mode',
        'status',
        'username',
        'password',
        'grant_type',
        'otp_template'
    ];

    // Default configuration that can be used if no records exist in the database
    public static $defaultConfig = [
        'gateway' => 'hormuud_sms',
        'mode' => 'live',
        'status' => 1,
        'username' => 'somxchange',
        'password' => 'cLo++Fh0jnd4w2GppDOPGA==',
        'grant_type' => 'password',
        'otp_template' => 'Your OTP code is:'
    ];
}
