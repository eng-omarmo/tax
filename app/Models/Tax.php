<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    //

    protected $fillable = [
        'property_id',
        'tax_amount',
        'due_date',
        'tax_code',
        'status ',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function createTransction($tax)
    {
        Transaction::create([
            'property_id' => $this->property_id,
            'transaction_type' => 'Tax',
            'amount' => $this->tax_amount,
            'description' => 'Tax',
            'credit' => 0,
            'debit' => $this->tax_amount,
            'status' => 'Pending',
        ]);
    }
}
