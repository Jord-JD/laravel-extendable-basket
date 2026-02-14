<?php

namespace JordJD\LaravelExtendableBasket\Models;

use JordJD\LaravelExtendableBasket\Interfaces\Basketable;
use JordJD\LaravelExtendableBasket\Interfaces\BasketInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;

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
            throw new Exception('Quantity is less than one.');
        }

        foreach ($this->items as $item) {
            if (get_class($item->basketable) === get_class($basketable)
                && $item->basketable->getKey() === $basketable->getKey()
                && $item->meta === $meta) {
                $item->quantity += $quantity;
                $item->save();

                return;
            }
        }

        $basketItem = $this->items()->getModel();

        $item = new $basketItem();
        $item->basket_id = $this->id;
        $item->quantity = $quantity;
        $item->basketable_type = get_class($basketable);
        $item->basketable_id = $basketable->getKey();
        $item->meta = $meta;
        $item->save();

        unset($this->items);
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
