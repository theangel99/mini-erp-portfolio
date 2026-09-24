<?php

namespace App\Observers;

use App\Models\Quote;
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
     * Generate unique quote number: P-{year}-{sequential}
     * Format: P-2026-0001
     */
    protected function generateQuoteNumber(Quote $quote): string
    {
        $year = $quote->issued_at?->format('Y') ?? now()->format('Y');

        return DB::transaction(function () use ($year) {
            // Lock table to prevent race conditions
            $lastQuote = Quote::lockForUpdate()
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
