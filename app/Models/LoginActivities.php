<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivities extends Model
{
    //

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'device_id',
        'fcm_token',
        'logged_in_at',
    ];


     public function user(){
        return $this->belongsTo(User::class);
    }
}
