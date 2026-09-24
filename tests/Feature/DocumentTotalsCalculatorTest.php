<?php

use App\Enums\VatRate;
use App\Services\DocumentTotalsCalculator;

test('calculates line item without discount correctly', function () {
    $item = [
        'quantity' => '2.000',
        'unit_price' => '100.00',
        'discount_percent' => '0.00',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('200.00')
        ->and($result['line_vat'])->toBe('44.00')
        ->and($result['line_total'])->toBe('244.00');
});

test('calculates line item with discount correctly', function () {
    $item = [
        'quantity' => '2.000',
        'unit_price' => '100.00',
        'discount_percent' => '10.00',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('180.00')
        ->and($result['line_vat'])->toBe('39.60')
        ->and($result['line_total'])->toBe('219.60');
});

test('calculates line item with different VAT rates', function () {
    $item = [
        'quantity' => '1.000',
        'unit_price' => '100.00',
        'discount_percent' => '0.00',
        'vat_rate' => VatRate::Rate9_5,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('100.00')
        ->and($result['line_vat'])->toBe('9.50')
        ->and($result['line_total'])->toBe('109.50');
});

test('handles decimal quantities correctly', function () {
    $item = [
        'quantity' => '2.500',
        'unit_price' => '45.50',
        'discount_percent' => '0.00',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('113.75')
        ->and($result['line_vat'])->toBe('25.03')
        ->and($result['line_total'])->toBe('138.78');
});

test('calculates document totals from multiple items', function () {
    $items = [
        [
            'quantity' => '2.000',
            'unit_price' => '100.00',
            'discount_percent' => '0.00',
            'vat_rate' => '22',
            'line_net' => '200.00',
            'line_vat' => '44.00',
            'line_total' => '244.00',
        ],
        [
            'quantity' => '1.000',
            'unit_price' => '50.00',
            'discount_percent' => '10.00',
            'vat_rate' => '22',
            'line_net' => '45.00',
            'line_vat' => '9.90',
            'line_total' => '54.90',
        ],
    ];

    $result = DocumentTotalsCalculator::calculateDocumentTotals($items);

    expect($result['subtotal'])->toBe('245.00')
        ->and($result['discount_total'])->toBe('5.00')
        ->and($result['vat_total'])->toBe('53.90')
        ->and($result['total'])->toBe('298.90');
});

test('provides VAT breakdown by rate', function () {
    $items = [
        [
            'quantity' => '1.000',
            'unit_price' => '100.00',
            'discount_percent' => '0.00',
            'vat_rate' => '22',
        ],
        [
            'quantity' => '1.000',
            'unit_price' => '50.00',
            'discount_percent' => '0.00',
            'vat_rate' => '9.5',
        ],
    ];

    $result = DocumentTotalsCalculator::calculateDocumentTotals($items);

    expect($result['vat_breakdown'])->toHaveCount(2)
        ->and($result['vat_breakdown'][0]['rate'])->toBe('22')
        ->and($result['vat_breakdown'][0]['net'])->toBe('100.00')
        ->and($result['vat_breakdown'][0]['vat'])->toBe('22.00')
        ->and($result['vat_breakdown'][1]['rate'])->toBe('9.5')
        ->and($result['vat_breakdown'][1]['net'])->toBe('50.00')
        ->and($result['vat_breakdown'][1]['vat'])->toBe('4.75');
});

test('handles zero quantity correctly', function () {
    $item = [
        'quantity' => '0.000',
        'unit_price' => '100.00',
        'discount_percent' => '0.00',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('0.00')
        ->and($result['line_vat'])->toBe('0.00')
        ->and($result['line_total'])->toBe('0.00');
});

test('handles 100 percent discount correctly', function () {
    $item = [
        'quantity' => '1.000',
        'unit_price' => '100.00',
        'discount_percent' => '100.00',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    expect($result['line_net'])->toBe('0.00')
        ->and($result['line_vat'])->toBe('0.00')
        ->and($result['line_total'])->toBe('0.00');
});

test('rounds to 2 decimal places correctly', function () {
    $item = [
        'quantity' => '3.333',
        'unit_price' => '9.99',
        'discount_percent' => '7.50',
        'vat_rate' => VatRate::Rate22,
    ];

    $result = DocumentTotalsCalculator::calculateLineItem($item);

    // All results should be properly rounded to 2 decimals
    expect($result['line_net'])->toMatch('/^\d+\.\d{2}$/')
        ->and($result['line_vat'])->toMatch('/^\d+\.\d{2}$/')
        ->and($result['line_total'])->toMatch('/^\d+\.\d{2}$/');
});
