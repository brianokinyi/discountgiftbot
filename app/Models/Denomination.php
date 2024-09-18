<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denomination extends Model
{
    use HasFactory;

    /**
     * Get the gift cards for the denomination.
     */
    public function giftCards(): HasMany
    {
        return $this->hasMany(GiftCard::class);
    }
}
