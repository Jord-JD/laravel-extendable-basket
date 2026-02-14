<?php

namespace JordJD\LaravelExtendableBasket\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface BasketInterface
{
    public static function getCurrent(): self;

    public static function getNew(): self;

    public function items(): HasMany;

    public function add(int $quantity, Basketable $basketable, array $meta = []);

    public function getSubtotal();

    public function getTotalNumberOfItems(): int;

    public function isEmpty(): bool;
}
