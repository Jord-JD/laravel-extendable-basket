<?php

namespace JordJD\LaravelExtendableBasket\Models;

use JordJD\LaravelExtendableBasket\Interfaces\BasketItemInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class BasketItem extends Model implements BasketItemInterface
{
    protected $casts = [
        'meta' => 'array',
    ];

    public function basketable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setQuantity(int $quantity)
    {
        if ($quantity > 0) {
            $this->quantity = $quantity;
            $this->save();
        } else {
            $this->delete();
        }
    }

    public function getPrice()
    {
        return $this->quantity * $this->basketable->getPrice($this->meta);
    }
}
