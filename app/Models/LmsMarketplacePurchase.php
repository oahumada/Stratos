<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsMarketplacePurchase extends Model
{
    protected $fillable = [
        'listing_id',
        'buyer_organization_id',
        'purchased_by',
        'price_paid',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_paid' => 'decimal:2',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(LmsMarketplaceListing::class, 'listing_id');
    }

    public function buyerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'buyer_organization_id');
    }

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }
}
