<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionService
{
    public static function recordInvoice($unit,  $quarter)
    {
        return Transaction::create([
            'transaction_id'   => 'TXN-' . strtoupper(uniqid()),
            'property_id'      => $unit->property_id,
            'unit_id'          => $unit->id,
            'amount'           => $unit->unit_price,
            'transaction_type' => 'invoice',
            'description'      => $quarter,
            'debit'            => $unit->unit_price,
            'credit'           => 0,
            'status'           => 'pending',
        ]);
    }

    public static function recordPayment($unit, $amount, $quarter)
    {
        return Transaction::create([
            'transaction_id'   => 'TXN-' . strtoupper(uniqid()),
            'property_id'      => $unit->property_id,
            'unit_id'          => $unit->id,
            'amount'           => $amount,
            'transaction_type' => 'payment',
            'description'      => $quarter,
            'debit'            => 0,
            'credit'           => $amount,
            'status'           => 'completed',
        ]);
    }
}
