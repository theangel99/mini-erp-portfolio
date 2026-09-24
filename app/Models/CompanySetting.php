<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'postal_code',
        'city',
        'vat_id',
        'registration_number',
        'iban',
        'bic',
        'bank',
        'email',
        'phone',
        'logo',
        'quote_prefix',
        'invoice_prefix',
        'default_payment_days',
        'document_footer',
    ];

    protected $casts = [
        'default_payment_days' => 'integer',
    ];

    /**
     * Get the singleton company settings record.
     */
    public static function get(): self
    {
        return static::firstOrCreate([]);
    }
}
