<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Factory;

use Sirian\YMLParser\Builder\Builder;
use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Model\Category;
use Sirian\YMLParser\Model\Currency;
use Sirian\YMLParser\Model\Offer\BookOffer;
use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Model\Offer\VendorModelOffer;
use Sirian\YMLParser\Model\Param;
use Sirian\YMLParser\Model\Shop;
use Sirian\YMLParser\Reader\Reader;

class Factory implements FactoryInterface
{
    public function createParam(): Param
    {
        return new Param();
    }

    public function createShop(): Shop
    {
        return new Shop();
    }

    public function createCategory(): Category
    {
        return new Category();
    }

    public function createCurrency(): Currency
    {
        return new Currency();
    }

    public function createOffer(string $type): Offer
    {
        return match ($type) {
            'vendor.model' => new VendorModelOffer(),
            'book' => new BookOffer(),
            default => new Offer(),
        };
    }

    public function createBuilder(string $shopXML, ?Reader $offerReader): BuilderInterface
    {
        return new Builder($this, $shopXML, $offerReader);
    }
}
