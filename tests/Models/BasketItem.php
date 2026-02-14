<?php

namespace JordJD\LaravelExtendableBasket\Tests\Models;

use JordJD\LaravelExtendableBasket\Models\BasketItem as BasketItemModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketItem extends BasketItemModel
{
    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }
}
