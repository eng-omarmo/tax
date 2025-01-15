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
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
