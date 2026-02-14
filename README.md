# 🛒 Laravel Extendable Basket

[![Build Status](https://travis-ci.com/Jord-JD/laravel-extendable-basket.svg?branch=master)](https://travis-ci.com/Jord-JD/laravel-extendable-basket)
[![Coverage Status](https://coveralls.io/repos/github/Jord-JD/laravel-extendable-basket/badge.svg?branch=master)](https://coveralls.io/github/Jord-JD/laravel-extendable-basket?branch=master)
[![StyleCI](https://github.styleci.io/repos/104244274/shield?branch=master)](https://github.styleci.io/repos/104244274)
[![](https://img.shields.io/packagist/dt/jord-jd/laravel-extendable-basket.svg)](https://packagist.org/packages/jord-jd/laravel-extendable-basket/stats)

Laravel Extendable Basket provides several abstract classes that implement basic ecommerce basket functionality.
These classes must be extended by your application.

## Installation

Just install the latest version using composer.

```
composer require jord-jd/laravel-extendable-basket
```

## Compatibility

This package supports Laravel versions 5.5 through 10 and requires PHP 8.1 or higher.

## Setup

You need to perform various setup steps in order to make use of this package.

### Database tables

Two database tables are required to store basket and basket item data. By default these are called `baskets` and `basket_items`. This package provides database migrations to create these tables.

To run these migrations, enter the following artisan command.

```
php artisan migrate
```

### Models

Now you need to create two related models, one to hold basket details and one to hold basket items details. Two example models are shown below. They can be modified or added to as necessary.

Create a Basket model.

```php
# app/Basket.php

<?php
namespace App;

use JordJD\LaravelExtendableBasket\Models\Basket as BasketModel;

class Basket extends BasketModel
{
    public function items(): HasMany
    {
        return $this->hasMany('App\BasketItem');
    }
}
```

Create a BasketItem model.

```php
# app/BasketItem.php

<?php
namespace App;

use JordJD\LaravelExtendableBasket\Models\BasketItem as BasketItemModel;

class BasketItem extends BasketItemModel
{
    public function basket(): BelongsTo
    {
        return $this->belongsTo('App\Basket');
    }
}
```

### Basketable interface

Anything that can be placed in the basket provided by this library is considered
'basketable'. You can make any existing Eloquent model basketable simply by
making it implement the `Basketable` interface.

For example, if you had a `Product` model, you can change it as follows.

```php
# app/Product.php

<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use JordJD\LaravelExtendableBasket\Interfaces\Basketable;

class Product extends Model implements Basketable {

    // ...

}
```

Note that any basketable models must have a `getPrice()` and a `getName()` method
that returns a numeric price and textual name of the basketable, respectively.

## Usage

This section describes the use of the basket and basket item functionality 
provided by this package. It assumes you have performed the required installation
and setup, in the manner specified above.

Remember to `use` the basket and/or basket item models you have created, where
necessary.

### Getting the current basket

From anywhere in your application, you can get the current basket. If no basket 
currently exists in the session, one will be created.

```php
$basket = Basket::getCurrent();
```

### Getting a new basket

If you need to discard the current basket and create a new one, such as after a 
user has placed an order, you can use the `getNew` method.

```php
$basket = Basket::getNew();
```

This method essentially doubles as emptying the basket.

### Manual basket management

The static methods `getCurrent` and `getNew` store the current basket ID in the 
session. If you do not want this, you can manage baskets yourself, as you would 
with any Eloquent model.

```php
// Get new basket
$basket = new Basket();

// Get current basket
$userBasketId = Auth::user()->basket_id;
$basket = Basket::find($userBasketId);
```

### Add item(s) to the basket

After getting the current basket, you can easily add items to it using the basket's
`add` method. You need to provide it with a quantity and any basketable model.

Optionally, you can also provide an array of basket item meta data, which could be 
used to store information about variations on a product.

```php
$quantity = 5;
$product = Product::FindOrFail(1);

$basket->add($quantity, $product);
$basket->add($quantity, $product, ['colour' => 'red', 'size' => 'medium']);
```

### Getting basket items

Getting items from the basket and the basketable model they contain can be easily 
done. See the example below.

```php
foreach($basket->items as $item) {
    $product = $item->basketable;
    echo $product->name;
}
```

## Changing the quantity of a basket item

Each basket item has a quantity associated with it. It is set when an item is
added to the basket, but can be modified later via the `setQuantity` method.

```php
$item = $basket->items->first();
$item->setQuantity($request->quantity);
```

## Removing a basket item

Basket items can easily be removed simply by deleting them. See the following 
example.

```php
$item = $basket->items->first();
$item->delete();
```

## Getting the unit cost of a basket item

Getting the unit cost of a basket item just involves calling the `getPrice` method of
the basketable model associated with the basket item. See the example below.

```php
$item = $basket->items->first();
$item->basketable->getPrice();
```

## Getting the line total of a basket item

The basket item class provides a `getPrice` method that gets the line total. This is simply
the basketable's price multiplied by the basket item quantity. The example code below
shows how to use this method.

```php
$item = $basket->items->first();
$item->getPrice();
```

## Getting the total number of items in the basket

There is a single method in the basket class that can sum up all the basket item quantities.
Just call the `getTotalNumberOfItems` method, as follows.

```php
$numItems = $basket->getTotalNumberOfItems();
```

## Getting the basket subtotal

A `getSubtotal` method is provided in the basket class that provides the total of all
items in the basket. See the following example.

```php
$subtotal = $basket->getSubtotal()
```

If you wish to add delivery costs or discounts, you can create a new `getTotal` method
in your basket class. This method can call the `getSubtotal` method, and then modify
and return it, in whatever way you wish.

An example Basket class implementing this idea is shown below.

```php
# app/Basket.php

<?php
namespace App;

use JordJD\LaravelExtendableBasket\Models\Basket as BasketModel;

class Basket extends BasketModel
{
    public function items()
    {
        return $this->hasMany('App\BasketItem');
    }

    public function getTotal()
    {
        $deliveryCost = 3.99;

        return $this->getSubtotal() + $deliveryCost;
    }
}
```
