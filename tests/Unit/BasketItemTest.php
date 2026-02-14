<?php

namespace JordJD\LaravelExtendableBasket\Tests\Unit;

use JordJD\LaravelExtendableBasket\Models\BasketItem;
use JordJD\LaravelExtendableBasket\Tests\Models\Basket;
use JordJD\LaravelExtendableBasket\Tests\Models\Product;
use JordJD\LaravelExtendableBasket\Tests\TestCase;

class BasketItemTest extends TestCase
{
    /**
     * Test an item can be added to the basket.
     */
    public function testAddBasketItem()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(1, $product);

        $item = $basket->items()->first();

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(1, $item->quantity);
        $this->assertEquals($product->price, $item->getPrice());

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(1, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price, $basket->getSubtotal());
    }

    /**
     * Test an item can be added to the basket with a multiple quantity set.
     */
    public function testAddBasketItemWithMultipleQuantity()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(2, $product);

        $item = $basket->items()->first();

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals($product->price * 2, $item->getPrice());

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(2, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price * 2, $basket->getSubtotal());
    }

    /**
     * Test an exception is thrown if an item with zero quantity is added.
     */
    public function testAddBasketItemWithZeroQuantity()
    {
        $this->expectException(\Exception::class);

        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(0, $product);
    }

    /**
     * Test an exception is thrown if an item with a negative quantity is added.
     */
    public function testAddBasketItemWithNegativeQuantity()
    {
        $this->expectException(\Exception::class);

        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(-1, $product);
    }

    /**
     * Test multiple items can be added to the basket.
     */
    public function testAddMultipleBasketItems()
    {
        $product1 = Product::FindOrFail(1);
        $product2 = Product::FindOrFail(2);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(2, $product1);
        $basket->add(4, $product2);

        $item1 = $basket->items[0];
        $item2 = $basket->items[1];

        $this->assertEquals($product1->id, $item1->basketable->id);
        $this->assertEquals(2, $item1->quantity);
        $this->assertEquals($product1->price * 2, $item1->getPrice());

        $this->assertEquals($product1->name, $item1->basketable->getName());
        $this->assertEquals($product1->price, $item1->basketable->getPrice());

        $this->assertEquals($product2->id, $item2->basketable->id);
        $this->assertEquals(4, $item2->quantity);
        $this->assertEquals($product2->price * 4, $item2->getPrice());

        $this->assertEquals($product2->name, $item2->basketable->getName());
        $this->assertEquals($product2->price, $item2->basketable->getPrice());

        $this->assertEquals($item1->quantity + $item2->quantity, $basket->getTotalNumberOfItems());
        $this->assertEquals($item1->getPrice() + $item2->getPrice(), $basket->getSubtotal());
    }

    /**
     * Test adding more to existing basket item.
     */
    public function testAddingMoreToExistingBasketItem()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(1, $product);
        $basket->add(3, $product);

        $this->assertEquals(1, $basket->items()->count());

        $item = $basket->items()->first();

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(4, $item->quantity);
        $this->assertEquals($product->price * 4, $item->getPrice());

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(4, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price * 4, $basket->getSubtotal());
    }

    /**
     * Test an item can be added to the basket with meta data.
     */
    public function testAddBasketItemWithMetaData()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $metaData = ['colour' => 'red', 'size' => 'medium'];

        $basket->add(1, $product, $metaData);

        $item = $basket->items()->first();

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(1, $item->quantity);
        $this->assertEquals($product->price, $item->getPrice());
        $this->assertSame($metaData, $item->meta);

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(1, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price, $basket->getSubtotal());
    }

    /**
     * Test adding more to existing basket item, with meta data.
     */
    public function testAddingMoreToExistingBasketItemWithMetaData()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $metaData = ['colour' => 'red', 'size' => 'medium'];

        $basket->add(1, $product, $metaData);
        $basket->add(2, $product, $metaData);

        $this->assertEquals(1, $basket->items()->count());

        $item = $basket->items()->first();

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals($product->price * 3, $item->getPrice());
        $this->assertSame($metaData, $item->meta);

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(3, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price * 3, $basket->getSubtotal());
    }

    /**
     * Test multiple items can be added to the basket with different meta data.
     */
    public function testAddMultipleBasketItemsWithDifferentMetaData()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $metaData1 = ['colour' => 'red', 'size' => 'medium'];
        $metaData2 = ['colour' => 'blue', 'size' => 'medium'];

        $basket->add(1, $product, $metaData1);
        $basket->add(2, $product, $metaData2);

        $this->assertEquals(2, $basket->items()->count());

        $item1 = $basket->items[0];
        $item2 = $basket->items[1];

        $this->assertEquals($product->id, $item1->basketable->id);
        $this->assertEquals(1, $item1->quantity);
        $this->assertEquals($product->price * 1, $item1->getPrice());
        $this->assertSame($metaData1, $item1->meta);

        $this->assertEquals($product->name, $item1->basketable->getName());
        $this->assertEquals($product->price, $item1->basketable->getPrice());

        $this->assertEquals($product->id, $item2->basketable->id);
        $this->assertEquals(2, $item2->quantity);
        $this->assertEquals($product->price * 2, $item2->getPrice());
        $this->assertSame($metaData2, $item2->meta);

        $this->assertEquals($product->name, $item2->basketable->getName());
        $this->assertEquals($product->price, $item2->basketable->getPrice());

        $this->assertEquals($item1->quantity + $item2->quantity, $basket->getTotalNumberOfItems());
        $this->assertEquals($item1->getPrice() + $item2->getPrice(), $basket->getSubtotal());
    }

    /**
     * Test a basket item can have it quantity changed.
     */
    public function testBasketItemQuantityChange()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(1, $product);

        /** @var BasketItem $item */
        $item = $basket->items()->first();

        $item->setQuantity(2);

        $this->assertEquals($product->id, $item->basketable->id);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals($product->price * 2, $item->getPrice());

        $this->assertEquals($product->name, $item->basketable->getName());
        $this->assertEquals($product->price, $item->basketable->getPrice());

        $this->assertEquals(2, $basket->getTotalNumberOfItems());
        $this->assertEquals($product->price * 2, $basket->getSubtotal());
    }

    /**
     * Test a basket item is deleted if its quantity is changed to zero.
     */
    public function testBasketItemDeletedIfQuantityChangedToZero()
    {
        $product = Product::FindOrFail(1);

        /** @var Basket $basket */
        $basket = Basket::getNew();

        $basket->add(1, $product);

        /** @var BasketItem $item */
        $item = $basket->items()->first();

        $item->setQuantity(0);

        $this->assertEquals(0, $item = $basket->items()->count());
    }
}
