<?php

namespace Sirian\YMLParser\Builder;

use Sirian\YMLParser\Offer\Offer;
use Sirian\YMLParser\Shop;

interface BuilderInterface
{
    /**
     * @return Shop
     */
    public function getShop();

    /**
     * @return \Generator|Offer[]
     */
    public function getOffers();
}