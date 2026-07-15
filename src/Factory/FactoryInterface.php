<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Factory;

use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Model\Category;
use Sirian\YMLParser\Model\Currency;
use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Model\Param;
use Sirian\YMLParser\Model\Shop;
use Sirian\YMLParser\Reader\Reader;

interface FactoryInterface
{
    public function createParam(): Param;

    public function createShop(): Shop;

    public function createCategory(): Category;

    public function createCurrency(): Currency;

    public function createOffer(string $type): Offer;

    public function createBuilder(string $shopXML, ?Reader $offerReader): BuilderInterface;
}
