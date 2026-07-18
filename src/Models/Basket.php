<?php

namespace JordJD\LaravelExtendableBasket\Models;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JordJD\LaravelExtendableBasket\Interfaces\Basketable;
use JordJD\LaravelExtendableBasket\Interfaces\BasketInterface;

abstract class Basket extends Model implements BasketInterface
{
    const BASKET_SESSION_KEY = 'doleb_basket_id';

    public static function getCurrent(): BasketInterface
    {
        $basket = static::find(session(static::BASKET_SESSION_KEY));

        if (!$basket) {
            $basket = new static();
            $basket->save();
            session()->put(static::BASKET_SESSION_KEY, $basket->id);
        }

        return $basket;
    }

    public static function getNew(): BasketInterface
    {
        session()->forget(static::BASKET_SESSION_KEY);

        return static::getCurrent();
    }

    public function add(int $quantity, Basketable $basketable, array $meta = [])
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least one.');
        }

        if (!method_exists($basketable, 'getKey') || $basketable->getKey() === null) {
            throw new InvalidArgumentException('The basketable model must be persisted before it can be added.');
        }

        if (!$this->exists) {
            if (!$this->save()) {
                throw new \RuntimeException('Unable to persist the basket before adding an item.');
            }
        }

        foreach ($this->items as $item) {
            $existingBasketable = $item->basketable;
            if ($existingBasketable !== null
                && get_class($existingBasketable) === get_class($basketable)
                && $existingBasketable->getKey() === $basketable->getKey()
                && $item->meta === $meta) {
                $item->quantity += $quantity;
                $item->save();

                return $item;
            }
        }

        $basketItem = $this->items()->getModel();

        $item = new $basketItem();
        $item->basket_id = $this->id;
        $item->quantity = $quantity;
        $item->basketable_type = method_exists($basketable, 'getMorphClass')
            ? $basketable->getMorphClass()
            : get_class($basketable);
        $item->basketable_id = $basketable->getKey();
        $item->meta = $meta;
        $item->save();

        unset($this->items);

        return $item;
    }

    /**
     * Remove every item from this basket.
     *
     * @return int Number of deleted basket items.
     */
    public function clear(): int
    {
        $deleted = $this->items()->delete();
        unset($this->items);

        return (int) $deleted;
    }

    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->getPrice();
        }

        return $subtotal;
    }

    public function getTotalNumberOfItems(): int
    {
        $totalNumberOfItems = 0;

        foreach ($this->items as $item) {
            $totalNumberOfItems += $item->quantity;
        }

        return $totalNumberOfItems;
    }

    public function isEmpty(): bool
    {
        return $this->getTotalNumberOfItems() <= 0;
    }
}
