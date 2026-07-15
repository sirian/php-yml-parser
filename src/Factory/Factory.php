<?php

namespace Sirian\YMLParser\Factory;

use Sirian\YMLParser\Builder;
use Sirian\YMLParser\Category;
use Sirian\YMLParser\Currency;
use Sirian\YMLParser\Offer\BookOffer;
use Sirian\YMLParser\Offer\Offer;
use Sirian\YMLParser\Offer\VendorModelOffer;
use Sirian\YMLParser\Param;
use Sirian\YMLParser\Shop;
use Sirian\YMLParser\Storage;
use Sirian\YMLParser\Storage\StorageInterface;

class Factory implements FactoryInterface
{
    public function createParam()
    {
        return new Param();
    }
    public function createShop()
    {
        return new Shop();
    }

    public function createCategory()
    {
        return new Category();
    }

    public function createCurrency()
    {
        return new Currency();
    }

    public function createOffer($type)
    {
        switch ($type) {
            case 'vendor.model':
                return new VendorModelOffer();
            case 'book':
                return new BookOffer();
            default:
                return new Offer();
        }
    }

    public function createStorage()
    {
        return new Storage();
    }

    public function createBuilder(StorageInterface $storage)
    {
        return new Builder($this, $storage);
    }
}
