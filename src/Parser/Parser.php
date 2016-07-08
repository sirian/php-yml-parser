<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Currency;
use Sirian\YMLParser\Factory\Factory;
use Sirian\YMLParser\Shop;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Parser extends EventDispatcher
{
    protected $xmlReader;
    protected $factory;

    private $path = [];

    private $tmpFile;

    public function __construct(Factory $factory = null)
    {
        if (null == $factory) {
            $factory = new Factory();
        }

        $this->xmlReader = new \XMLReader();
        $this->factory = $factory;
    }

    public function parse($file)
    {
        $this->path = [];
        $this->tmpFile = tmpfile();

        $this->xmlReader->open($file);
        $this->read();
        $this->xmlReader->close();
    }

    protected function read()
    {
        $xml = $this->xmlReader;
        $shopXML = '';
        if (!$this->moveToShop()) {
            return;
        }

        $shop = $this->factory->createShop();
        if (!$this->stepIn()) {
            return;
        }

        do {
            if ('yml_catalog/shop/offers' == $this->getPath()) {
                if ($this->stepIn()) {
                    $count = 0;
                    do {
                        fputs($this->tmpFile, json_encode($xml->readOuterXml(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "\n");
                        $count++;
                    } while ($this->moveToNextSibling());
                    $shop->setOffersCount($count);
                }
            } else {
                $shopXML .= $xml->readOuterXml();
            }
        } while ($this->moveToNextSibling());

        $shopXML = ('<shop>' . $shopXML . '</shop>');
        $this->parseShop($shop, simplexml_load_string($shopXML));

        $this->dispatch(ParserEvents::SHOP, new ShopEvent($shop));

        fseek($this->tmpFile, 0);


        while ($line = fgets($this->tmpFile)) {
            $xml = simplexml_load_string(json_decode($line));
            $offer = $this->createOffer($shop, $xml);
            $this->dispatch(ParserEvents::OFFER, new OfferEvent($offer));
        }
    }

    private function moveToShop()
    {
        $xml = $this->xmlReader;

        while ($xml->read()) {
            if ($xml->nodeType == \XMLReader::END_ELEMENT) {
                array_pop($this->path);
                continue;
            }


            if ($xml->nodeType !== \XMLReader::ELEMENT || $xml->isEmptyElement) {
                continue;
            }

            array_push($this->path, $xml->name);

            if ('yml_catalog/shop' === $this->getPath()) {
                return true;
            }
        }

        return false;
    }

    private function getPath()
    {
        return implode('/', $this->path);
    }

    private function stepIn()
    {
        $xml = $this->xmlReader;
        if ($xml->isEmptyElement) {
            return false;
        }
        while ($xml->read()) {
            if (\XMLReader::ELEMENT == $xml->nodeType) {
                array_push($this->path, $xml->name);
                return true;
            }
            if (\XMLReader::END_ELEMENT == $xml->nodeType) {
                array_pop($this->path);
                return false;
            }
        }
        return false;
    }

    private function moveToNextSibling()
    {
        $xml = $this->xmlReader;
        array_pop($this->path);
        while ($xml->next()) {
            if (\XMLReader::ELEMENT == $xml->nodeType) {
                array_push($this->path, $xml->name);
                return true;
            }

            if (\XMLReader::END_ELEMENT == $xml->nodeType) {
                return false;
            }
        }
        return false;
    }

    protected function parseShop(Shop $shop, \SimpleXMLElement $xml)
    {
        foreach ($xml->xpath('//currencies/currency') as $elem) {
            $shop->addCurrency($this->createCurrency($elem));
        }
        $this->parseCategories($shop, $xml);
        $shop
            ->setName((string)$xml->name)
            ->setAgency((string)$xml->agency)
            ->setEmail((string)$xml->email)
            ->setCompany((string)$xml->company)
            ->setPlatform((string)$xml->platform)
            ->setUrl((string)$xml->url)
            ->setVersion((string)$xml->version)
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

    protected function parseCategories(Shop $shop, \SimpleXMLElement $xml)
    {
        $parents = [];
        foreach ($xml->xpath('//categories/category') as $xml) {
            $shop->addCategory($this->createCategory($xml));

            foreach (['parentId', 'parent_id'] as $field) {
                if (isset($xml[$field])) {
                    $parents[(string)$xml['id']] = (string)$xml[$field];
                    break;
                }
            }
        }

        foreach ($parents as $id => $parentId) {
            if ($id != $parentId) {
                $parent = $shop->getCategory($parentId);
            } else {
                $parent = null;
            }
            $shop
                ->getCategory($id)
                ->setParent($parent)
            ;
        }
        return $shop->getCategories();
    }

    protected function createCurrency(\SimpleXMLElement $xml)
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

    protected function createCategory(\SimpleXMLElement $xml)
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

    protected function createOffer(Shop $shop, \SimpleXMLElement $xml)
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

        if ($shop->getCurrency($currencyId)) {
            $offer->setCurrency($shop->getCurrency($currencyId));
        }

        $categoryId = (string)$xml->categoryId;

        if ($shop->getCategory($categoryId)) {
            $offer->setCategory($shop->getCategory($categoryId));
        }
        return $offer;
    }

    private function camelize($field)
    {
        return strtr(ucwords(strtr($field, array('_' => ' ', '.' => '_ '))), array(' ' => ''));
    }
}
