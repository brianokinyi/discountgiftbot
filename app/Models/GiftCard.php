<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Countries\Countries;

class GiftCard extends Model
{
    use HasFactory;

    /**
     * Get the brand that owns the gift card.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the country for the gift card.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Countries::class);
    }
}
