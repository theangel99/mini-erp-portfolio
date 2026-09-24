<?php

namespace App\Observers;

use App\Models\Quote;
use App\Services\DocumentTotalsCalculator;
use Illuminate\Support\Facades\DB;

class QuoteObserver
{
    /**
     * Handle the Quote "creating" event.
     */
    public function creating(Quote $quote): void
    {
        if (empty($quote->number)) {
            $quote->number = $this->generateQuoteNumber($quote);
        }
    }

    /**
     * Handle the Quote "saved" event.
     * Calculate totals from items after quote is saved.
     */
    public function saved(Quote $quote): void
    {
        // Reload items to ensure we have the latest data
        $quote->load('items');

        if ($quote->items->isEmpty()) {
            return;
        }

        // Prepare items array for calculator
        $items = $quote->items->map(function ($item) {
            return [
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
                'vat_rate' => $item->vat_rate->value,
            ];
        })->toArray();

        // Calculate totals
        $totals = DocumentTotalsCalculator::calculateDocumentTotals($items);

        // Update quote totals WITHOUT triggering another save event
        $quote->updateQuietly([
            'subtotal' => $totals['subtotal'],
            'discount_total' => $totals['discount_total'],
            'vat_total' => $totals['vat_total'],
            'total' => $totals['total'],
        ]);

        // Also update line totals for each item
        foreach ($quote->items as $index => $item) {
            $lineCalculated = DocumentTotalsCalculator::calculateLineItem([
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
                'vat_rate' => $item->vat_rate->value,
            ]);

            $item->updateQuietly([
                'line_net' => $lineCalculated['line_net'],
                'line_vat' => $lineCalculated['line_vat'],
                'line_total' => $lineCalculated['line_total'],
            ]);
        }
    }

    /**
     * Generate unique quote number: P-{year}-{sequential}
     * Format: P-2026-0001
     */
    protected function generateQuoteNumber(Quote $quote): string
    {
        $year = $quote->issued_at?->format('Y') ?? now()->format('Y');

        return DB::transaction(function () use ($year) {
            // Lock table to prevent race conditions
            // Include soft deleted quotes to avoid duplicate numbers
            $lastQuote = Quote::withTrashed()
                ->lockForUpdate()
                ->where('number', 'like', "P-{$year}-%")
                ->orderByDesc('number')
                ->first();

            if ($lastQuote) {
                // Extract sequential number from last quote
                $lastNumber = (int) substr($lastQuote->number, -4);
                $sequential = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $sequential = '0001';
            }

            return "P-{$year}-{$sequential}";
        });
    }
}
