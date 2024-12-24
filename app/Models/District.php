<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    protected $table = 'districts';

    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
