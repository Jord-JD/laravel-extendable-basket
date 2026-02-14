<?php

namespace JordJD\LaravelExtendableBasket\Interfaces;

interface Basketable
{
    public function getPrice($basketItemMeta = null);

    public function getName();
}
