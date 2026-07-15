<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Builder;

use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Model\Shop;

interface BuilderInterface
{
    public function getShop(): Shop;

    /** @return \Generator<int, Offer> */
    public function getOffers(): \Generator;
}
