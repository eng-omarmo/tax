<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected  $fillable = ['name','district_id'];


    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
