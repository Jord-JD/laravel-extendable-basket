<?php

namespace JordJD\LaravelExtendableBasket\Tests;

use JordJD\LaravelExtendableBasket\Providers\LaravelExtendableBasketServiceProvider;
use JordJD\LaravelExtendableBasket\Tests\Models\Product;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelExtendableBasketServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', ['--database' => 'testbench'])->run();
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->addProducts();
    }

    private function addProducts()
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = new Product();
            $product->id = $i;
            $product->name = 'Product '.$i;
            $product->price = $i * 100;
            $product->save();
        }
    }
}
