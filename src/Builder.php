<?php

namespace Sirian\YMLParser;

use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Factory\FactoryInterface;
use Sirian\YMLParser\Offer\Offer;
use Sirian\YMLParser\Storage\StorageInterface;

class Builder implements BuilderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var Shop
     */
    private $shop;

    public function __construct(FactoryInterface $factory, StorageInterface $storage)
    {
        $this->factory = $factory;
        $this->storage = $storage;
    }

    public function getShop()
    {
        if (null == $this->shop) {
            $this->buildShop();
        }
        return $this->shop;
    }

    /**
     * @return \Generator|Offer[]
     */
    public function getOffers()
    {
        $count = 0;
        foreach ($this->storage->getNextOfferXML() as $xml) {
            $xml = simplexml_load_string(json_decode($xml));
            $offer = $this->buildOffer($xml);
            yield $count => $offer;
            $count++;
        }
    }

    protected function buildShop()
    {
        $this->shop = $this->factory->createShop();
        $xml = simplexml_load_string($this->storage->getShopXML());

        foreach ($xml->xpath('//currencies/currency') as $elem) {
            $this->shop->addCurrency($this->buildCurrency($elem));
        }

        $this->buildCategories($xml);

        $this
            ->shop
            ->setName((string)$xml->name)
            ->setAgency((string)$xml->agency)
            ->setEmail((string)$xml->email)
            ->setCompany((string)$xml->company)
            ->setPlatform((string)$xml->platform)
            ->setUrl((string)$xml->url)
            ->setVersion((string)$xml->version)
            ->setOffersCount($this->storage->getOffersCount())
            ->setXml($xml)
        ;
    }

    protected function createParam(\SimpleXMLElement $xml)
    {
        $name = (string)$xml['name'];
        $unit = (string)$xml['unit'];

        $param = $this->factory->createParam();

        $param
            ->setName($name)
            ->setUnit($unit)
            ->setValue((string)$xml)
        ;

        return $param;
    }

    protected function buildCategories(\SimpleXMLElement $xml)
    {
        $parents = [];
        foreach ($xml->xpath('//categories/category') as $xml) {
            $this->getShop()->addCategory($this->buildCategory($xml));

            foreach (['parentId', 'parent_id'] as $field) {
                if (isset($xml[$field])) {
                    $parents[(string)$xml['id']] = (string)$xml[$field];
                    break;
                }
            }
        }

        foreach ($parents as $id => $parentId) {
            if ($id != $parentId) {
                $parent = $this->getShop()->getCategory($parentId);
            } else {
                $parent = null;
            }
            $this
                ->getShop()
                ->getCategory($id)
                ->setParent($parent)
            ;
        }
    }

    protected function buildCurrency(\SimpleXMLElement $xml)
    {
        $id = Currency::normalize((string)$xml['id']);

        $currency = $this->factory->createCurrency();
        $currency
            ->setId($id)
            ->setRate((string)$xml['rate'])
            ->setPlus((int)$xml['plus'])
        ;

        return $currency;
    }

    protected function buildCategory(\SimpleXMLElement $xml)
    {
        $id = (string)$xml['id'];

        $parents[$id] = (string)$xml['parentId'];

        $category = $this->factory->createCategory();

        $category
            ->setId($id)
            ->setName((string)$xml)
        ;

        return $category;
    }

    protected function buildOffer(\SimpleXMLElement $xml)
    {
        $type = (string)$xml['type'];

        if (!$type) {
            $type = 'vendor.model';
        }

        $offer = $this->factory->createOffer($type);
        foreach ($xml->attributes() as $key => $value) {
            $offer->setAttribute($key, (string)$value);
        }

        $offer
            ->setId((string)$xml['id'])
            ->setAvailable(((string)$xml['available']) == 'true' ? true : false)
            ->setType($type)
            ->setXml($xml)
        ;

        foreach ($xml->param as $param) {
            $offer->addParam($this->createParam($param));
        }

        foreach ($xml as $field => $value) {
            foreach (['add', 'set'] as $method) {
                $method .= $this->camelize($field);
                if (!in_array($field, ['param']) && method_exists($offer, $method)) {
                    call_user_func([$offer, $method], count($value->children()) ? $value : (string)$value);
                    break;
                }
            }
        }

        $currencyId = Currency::normalize((string)$xml->currencyId);

        if ($this->getShop()->getCurrency($currencyId)) {
            $offer->setCurrency($this->getShop()->getCurrency($currencyId));
        }

        $categoryId = (string)$xml->categoryId;

        if ($this->getShop()->getCategory($categoryId)) {
            $offer->setCategory($this->getShop()->getCategory($categoryId));
        }
        return $offer;
    }

    private function camelize($field)
    {
        return strtr(ucwords(strtr($field, array('_' => ' ', '.' => '_ '))), array(' ' => ''));
    }
}
