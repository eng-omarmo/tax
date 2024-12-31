<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'registered_by',
    ];



    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function properties()
    {
        return $this->hasMany(Property::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'register_by');
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function calculateBalance()
    {
        $totalDebits = $this->transactions->sum('debit');
        $totalCredits = $this->transactions->sum('credit');

        return $totalDebits - $totalCredits;
    }
}
