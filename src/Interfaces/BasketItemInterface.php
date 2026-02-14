<?php

namespace JordJD\LaravelExtendableBasket\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface BasketItemInterface
{
    public function basket(): BelongsTo;

    public function basketable(): MorphTo;

    public function setQuantity(int $quantity);

    public function getPrice();
}
