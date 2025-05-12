<?php

namespace App\Services;

class TaxCalculationService
{
    /**
     * Calculate property tax amount
     *
     * @param float $unitPrice The unit price
     * @param float $taxRate The tax rate (default 0.05 for 5%)
     * @param int $months Number of months (default 3)
     * @return float The calculated tax amount
     */
    public function calculatePropertyTax($unitPrice, $taxRate = 0.05, $months = 3)
    {
        return $unitPrice * $taxRate * $months;
    }

    /**
     * Format amount as currency
     *
     * @param float $amount The amount to format
     * @param string $currency The currency symbol
     * @return string Formatted amount
     */
    public function formatCurrency($amount, $currency = '$')
    {
        return $currency . number_format($amount, 2);
    }
}
