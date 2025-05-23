<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    protected  $fillable = ['name','district_id'];


    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
