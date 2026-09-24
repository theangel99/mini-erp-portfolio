<?php

namespace App\Observers;

use App\Models\QuoteItem;
use App\Services\DocumentTotalsCalculator;

class QuoteItemObserver
{
    /**
     * Handle the QuoteItem "saved" event.
     * Recalculate parent quote totals when item is saved.
     */
    public function saved(QuoteItem $item): void
    {
        $this->recalculateQuoteTotals($item);
    }

    /**
     * Handle the QuoteItem "deleted" event.
     * Recalculate parent quote totals when item is deleted.
     */
    public function deleted(QuoteItem $item): void
    {
        $this->recalculateQuoteTotals($item);
    }

    /**
     * Recalculate all totals for the parent quote
     */
    protected function recalculateQuoteTotals(QuoteItem $item): void
    {
        $quote = $item->quote;

        if (!$quote) {
            return;
        }

        // Reload items to ensure we have the latest data
        $quote->load('items');

        if ($quote->items->isEmpty()) {
            // No items, set all totals to 0
            $quote->updateQuietly([
                'subtotal' => 0,
                'discount_total' => 0,
                'vat_total' => 0,
                'total' => 0,
            ]);
            return;
        }

        // Prepare items array for calculator
        $items = $quote->items->map(function ($quoteItem) {
            return [
                'quantity' => $quoteItem->quantity,
                'unit_price' => $quoteItem->unit_price,
                'discount_percent' => $quoteItem->discount_percent,
                'vat_rate' => $quoteItem->vat_rate,
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
        foreach ($quote->items as $index => $quoteItem) {
            $lineCalculated = DocumentTotalsCalculator::calculateLineItem([
                'quantity' => $quoteItem->quantity,
                'unit_price' => $quoteItem->unit_price,
                'discount_percent' => $quoteItem->discount_percent,
                'vat_rate' => $quoteItem->vat_rate,
            ]);

            $quoteItem->updateQuietly([
                'line_net' => $lineCalculated['line_net'],
                'line_vat' => $lineCalculated['line_vat'],
                'line_total' => $lineCalculated['line_total'],
            ]);
        }
    }
}
