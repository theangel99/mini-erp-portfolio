<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'vat_id',
        'is_vat_payer',
        'address',
        'postal_code',
        'city',
        'country',
        'email',
        'phone',
        'notes',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'is_vat_payer' => 'boolean',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function primaryContact(): ?Contact
    {
        return $this->contacts()->where('is_primary', true)->first();
    }
}
