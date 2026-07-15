<?php

namespace Sirian\YMLParser\Factory;

use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Category;
use Sirian\YMLParser\Currency;
use Sirian\YMLParser\Offer\Offer;
use Sirian\YMLParser\Param;
use Sirian\YMLParser\Storage\StorageInterface;

interface FactoryInterface
{
    /**
     * @return Param
     */
    public function createParam();

    public function createShop();

    /**
     * @return Category
     */
    public function createCategory();

    /**
     * @return Currency
     */
    public function createCurrency();

    /**
     * @param $type
     * @return Offer
     */
    public function createOffer($type);

    /**
     * @return StorageInterface
     */
    public function createStorage();

    /**
     * @param StorageInterface $storage
     * @return BuilderInterface
     */
    public function createBuilder(StorageInterface $storage);
}