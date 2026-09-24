<?php

namespace App\Services;

use App\Enums\VatRate;

class DocumentTotalsCalculator
{
    /**
     * Calculate line item totals.
     *
     * @param  array  $item  ['quantity' => '1.000', 'unit_price' => '100.00', 'discount_percent' => '10.00', 'vat_rate' => '22']
     * @return array ['line_net' => '90.00', 'line_vat' => '19.80', 'line_total' => '109.80']
     */
    public static function calculateLineItem(array $item): array
    {
        $quantity = (string) ($item['quantity'] ?? '0');
        $unitPrice = (string) ($item['unit_price'] ?? '0');
        $discountPercent = (string) ($item['discount_percent'] ?? '0');
        $vatRate = $item['vat_rate'] ?? '0';

        // Convert VatRate enum to decimal if needed
        if ($vatRate instanceof VatRate) {
            $vatRateDecimal = $vatRate->getDecimal();
        } else {
            $vatRateDecimal = bcdiv((string) $vatRate, '100', 6);
        }

        // Calculate base amount: quantity * unit_price
        $baseAmount = bcmul($quantity, $unitPrice, 6);

        // Calculate discount amount: base_amount * (discount_percent / 100)
        $discountAmount = bcmul($baseAmount, bcdiv($discountPercent, '100', 6), 6);

        // Calculate net amount: base_amount - discount_amount
        $lineNet = bcsub($baseAmount, $discountAmount, 6);

        // Calculate VAT amount: line_net * vat_rate_decimal
        $lineVat = bcmul($lineNet, $vatRateDecimal, 6);

        // Calculate total: line_net + line_vat
        $lineTotal = bcadd($lineNet, $lineVat, 6);

        return [
            'line_net' => number_format((float) $lineNet, 2, '.', ''),
            'line_vat' => number_format((float) $lineVat, 2, '.', ''),
            'line_total' => number_format((float) $lineTotal, 2, '.', ''),
        ];
    }

    /**
     * Calculate document totals from array of items.
     *
     * @param  array  $items  Array of items with calculated line totals
     * @return array ['subtotal', 'discount_total', 'vat_total', 'total', 'vat_breakdown']
     */
    public static function calculateDocumentTotals(array $items): array
    {
        $subtotal = '0';
        $discountTotal = '0';
        $vatTotal = '0';
        $total = '0';
        $vatBreakdown = [];

        foreach ($items as $item) {
            $quantity = (string) ($item['quantity'] ?? '0');
            $unitPrice = (string) ($item['unit_price'] ?? '0');
            $discountPercent = (string) ($item['discount_percent'] ?? '0');
            $vatRate = (string) ($item['vat_rate'] ?? '0');

            // If item already has calculated totals, use them
            if (isset($item['line_net'], $item['line_vat'], $item['line_total'])) {
                $lineNet = (string) $item['line_net'];
                $lineVat = (string) $item['line_vat'];
                $lineTotal = (string) $item['line_total'];
            } else {
                // Calculate line item
                $calculated = self::calculateLineItem($item);
                $lineNet = $calculated['line_net'];
                $lineVat = $calculated['line_vat'];
                $lineTotal = $calculated['line_total'];
            }

            // Add to totals
            $subtotal = bcadd($subtotal, $lineNet, 6);
            $vatTotal = bcadd($vatTotal, $lineVat, 6);
            $total = bcadd($total, $lineTotal, 6);

            // Calculate discount for this line
            $baseAmount = bcmul($quantity, $unitPrice, 6);
            $lineDiscount = bcmul($baseAmount, bcdiv($discountPercent, '100', 6), 6);
            $discountTotal = bcadd($discountTotal, $lineDiscount, 6);

            // VAT breakdown by rate
            $vatRateKey = (string) $vatRate;
            if (! isset($vatBreakdown[$vatRateKey])) {
                $vatBreakdown[$vatRateKey] = [
                    'rate' => $vatRate,
                    'net' => '0',
                    'vat' => '0',
                    'total' => '0',
                ];
            }
            $vatBreakdown[$vatRateKey]['net'] = bcadd($vatBreakdown[$vatRateKey]['net'], $lineNet, 6);
            $vatBreakdown[$vatRateKey]['vat'] = bcadd($vatBreakdown[$vatRateKey]['vat'], $lineVat, 6);
            $vatBreakdown[$vatRateKey]['total'] = bcadd($vatBreakdown[$vatRateKey]['total'], $lineTotal, 6);
        }

        // Format to 2 decimals
        foreach ($vatBreakdown as $key => $breakdown) {
            $vatBreakdown[$key]['net'] = number_format((float) $breakdown['net'], 2, '.', '');
            $vatBreakdown[$key]['vat'] = number_format((float) $breakdown['vat'], 2, '.', '');
            $vatBreakdown[$key]['total'] = number_format((float) $breakdown['total'], 2, '.', '');
        }

        return [
            'subtotal' => number_format((float) $subtotal, 2, '.', ''),
            'discount_total' => number_format((float) $discountTotal, 2, '.', ''),
            'vat_total' => number_format((float) $vatTotal, 2, '.', ''),
            'total' => number_format((float) $total, 2, '.', ''),
            'vat_breakdown' => array_values($vatBreakdown),
        ];
    }
}
