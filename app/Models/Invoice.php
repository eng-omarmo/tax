<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $table = 'invoices';

    protected $fillable = [
        'unit_id',
        'invoice_number',
        'amount',
        'invoice_date',
        'due_date',
        'frequency',
        'payment_status',
        'paid_at'
    ];
    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime'
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
