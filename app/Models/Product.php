<?php

namespace App\Models;

use App\Enums\ProductUnit;
use App\Enums\VatRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit',
        'price',
        'vat_rate',
        'is_active',
    ];

    protected $casts = [
        'unit' => ProductUnit::class,
        'vat_rate' => VatRate::class,
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
