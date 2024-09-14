<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    /**
     * Get the Gift Cards that exist on this brand.
     */
    public function giftCards(): HasMany
    {
        return $this->hasMany(GiftCard::class);
    }
}
