<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Shop;
use Symfony\Component\EventDispatcher\Event;

class ShopEvent extends Event
{
    private $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function getShop()
    {
        return $this->shop;
    }
}
