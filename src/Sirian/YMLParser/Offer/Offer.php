<?php

namespace Sirian\YMLParser\Offer;

use Sirian\YMLParser\Category;
use Sirian\YMLParser\Currency;
use Sirian\YMLParser\Shop;

class Offer
{
    /**
     * @var integer|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    protected $name = '';
    protected $description = '';

    /**
     * Доступность товара
     * «false» — товарное предложение на заказ. Магазин готов принять заказ и осуществить поставку товара в течение согласованного с покупателем срока, не превышающего двух месяцев (за исключением товаров, изготавливаемых на заказ, ориентировочный срок поставки которых оговаривается с покупателем во время заказа).
     * «true» — товарное предложение в наличии. Магазин готов сразу договариваться с покупателем о доставке/покупке товара.
     *
     * @var bool
     */
    protected $available = true;

    protected $url = '';

    protected $price = 0;
    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Shop
     */
    protected $shop;

    protected $typePrefix;

    protected $pictures = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = (string)$type;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    public function isAvailable()
    {
        return $this->available;
    }

    public function setAvailable($available)
    {
        $this->available = (bool)$available;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = (string)$url;
        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = (float)$price;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function setShop($shop)
    {
        $this->shop = $shop;
        return $this;
    }

    public function getPictures()
    {
        return $this->pictures;
    }

    public function setPictures($pictures)
    {
        $this->pictures = [];
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }

        return $this;
    }

    public function addPicture($picture)
    {
        $this->pictures[] = $picture;
        return $this;
    }

    public function getTypePrefix()
    {
        return $this->typePrefix;
    }

    public function setTypePrefix($typePrefix)
    {
        $this->typePrefix = $typePrefix;

        return $this;
    }
}
