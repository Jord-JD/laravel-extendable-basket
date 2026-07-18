<?php

namespace JordJD\LaravelExtendableBasket\Tests\Unit;

use JordJD\LaravelExtendableBasket\Interfaces\BasketInterface;
use JordJD\LaravelExtendableBasket\Tests\Models\Basket;
use JordJD\LaravelExtendableBasket\Tests\Models\Product;
use JordJD\LaravelExtendableBasket\Tests\TestCase;

class BasketTest extends TestCase
{
    /**
     * Ensures that new baskets can be created correctly.
     */
    public function testCreationOfNewBasket()
    {
        /** @var Basket $basket */
        $basket = Basket::getNew();

        $this->assertTrue($basket instanceof BasketInterface);
        $this->assertDatabaseHas('baskets', $basket->getAttributes());
    }

    /**
     * Ensures that retrieving the current basket correctly gets the
     * previously created basket.
     */
    public function testGettingCurrentBasket()
    {
        /** @var Basket $basket */
        $newBasket = Basket::getNew();

        /** @var Basket $basket */
        $currentBasket = Basket::getCurrent();

        $this->assertTrue($currentBasket instanceof BasketInterface);
        $this->assertDatabaseHas('baskets', $currentBasket->getAttributes());

        $this->assertEquals($newBasket->id, $currentBasket->id);
    }

    /**
     * Ensures a newly created basket is empty.
     */
    public function testNewBasketIsEmpty()
    {
        /** @var Basket $basket */
        $basket = Basket::getNew();

        $this->assertTrue($basket->isEmpty());
    }

    /**
     * Ensures a newly created basket has no items.
     */
    public function testNewBasketHasNoItems()
    {
        /** @var Basket $basket */
        $basket = Basket::getNew();

        $this->assertEquals(0, $basket->getTotalNumberOfItems());
    }

    /**
     * Ensures a newly created basket has a zero subtotal.
     */
    public function testNewBasketHasZeroSubtotal()
    {
        /** @var Basket $basket */
        $basket = Basket::getNew();

        $this->assertEquals(0, $basket->getSubtotal());
    }

    /**
     * Ensures all basket items can be cleared in one operation.
     */
    public function testBasketCanBeCleared()
    {
        $basket = Basket::getNew();
        $basket->add(1, Product::findOrFail(1));
        $basket->add(2, Product::findOrFail(2));

        $this->assertSame(2, $basket->clear());
        $this->assertTrue($basket->isEmpty());
        $this->assertSame(0, $basket->items()->count());
    }
}
