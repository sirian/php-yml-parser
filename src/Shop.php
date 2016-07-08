<?php

namespace Sirian\YMLParser;

class Shop
{
    /**
     * Короткое название магазина — название, которое выводится в списке найденных на Яндекс.Маркете товаров.
     * Оно не должно содержать более 20 символов.
     * В названии нельзя использовать слова, не имеющие отношения к наименованию магазина (например: лучший, дешевый),
     * указывать номер телефона и т.п.
     * Название магазина должно совпадать с фактическим названием магазина, которое публикуется на сайте.
     * При несоблюдении данного требования наименование может быть изменено Яндексом самостоятельно без уведомления магазина.
     *
     * @var string
     */
    protected $name;

    /**
     * Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней идентификации.
     *
     * @var string
     */
    protected $company;

    /**
     * URL главной страницы магазина
     *
     * @var string
     */
    protected $url;

    /**
     * Система управления контентом, на основе которой работает магазин (CMS).
     *
     * @var string
     */
    protected $platform;

    /**
     * Версия CMS
     *
     * @var string
     */
    protected $version;

    /**
     * Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за работоспособность сайта.
     *
     * @var string
     */
    protected $agency;

    /**
     * Контактный адрес разработчиков CMS или агентства, осуществляющего техподдержку.
     *
     * @var string
     */
    protected $email;

    /**
     * @var Category[]
     */
    protected $categories = [];

    /**
     * @var Currency[]
     */
    protected $currencies = [];

    /**
     * @var int Количество офферов магазина
     */
    protected $offersCount;

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getAgency()
    {
        return $this->agency;
    }

    public function setAgency($agency)
    {
        $this->agency = $agency;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function addCurrency(Currency $currency)
    {
        $this->currencies[$currency->getId()] = $currency;
        return $this;
    }

    public function addCategory(Category $category)
    {
        $this->categories[$category->getId()] = $category;
        return $this;
    }

    public function getCategory($id)
    {
        return isset($this->categories[$id]) ? $this->categories[$id] : null;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getCurrency($id)
    {
        return isset($this->currencies[$id]) ? $this->currencies[$id] : null;
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function getOffersCount()
    {
        return $this->offersCount;
    }

    public function setOffersCount($offersCount)
    {
        $this->offersCount = $offersCount;

        return $this;
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }
}
