<?php

if (!function_exists('calculate_included_vat')) {
    /**
     * Calculate the included VAT (Value Added Tax) from the total value.
     *
     * @param float $totalValue The total transaction value including VAT.
     * @param float $vatRate The VAT rate as a percentage.
     * @return float The calculated VAT amount.
     *
     * Usage example:
     * $totalValue = 1000000; // Total value including VAT
     * $vatRate = 11; // VAT rate in percentage
     * $vatAmount = calculate_included_vat($totalValue, $vatRate);
     * echo $vatAmount; // Outputs: 99099.10
     */
    function calculate_included_vat(float $totalValue, float $vatRate = 11): float
    {
        $vatRateDecimal = $vatRate / 100;
        $taxAmount = ($totalValue * $vatRateDecimal) / (1 + $vatRateDecimal);
        return $taxAmount;
    }
}
