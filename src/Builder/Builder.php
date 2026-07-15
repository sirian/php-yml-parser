<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Builder;

use Sirian\YMLParser\Exception\YMLException;
use Sirian\YMLParser\Factory\FactoryInterface;
use Sirian\YMLParser\Model\Category;
use Sirian\YMLParser\Model\Currency;
use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Model\Param;
use Sirian\YMLParser\Model\Shop;
use Sirian\YMLParser\Reader\Reader;

class Builder implements BuilderInterface
{
    private const int XML_OPTIONS = LIBXML_NONET | LIBXML_COMPACT;

    private FactoryInterface $factory;

    private string $shopXML;

    private ?Reader $offerReader;

    private ?Shop $shop = null;

    public function __construct(FactoryInterface $factory, string $shopXML, ?Reader $offerReader)
    {
        $this->factory = $factory;
        $this->shopXML = $shopXML;
        $this->offerReader = $offerReader;
    }

    public function __destruct()
    {
        $this->offerReader?->close();
        $this->offerReader = null;
    }

    public function getShop(): Shop
    {
        return $this->shop ??= $this->buildShop();
    }

    /** @return \Generator<int, Offer> */
    public function getOffers(): \Generator
    {
        $shop = $this->getShop();
        $reader = $this->offerReader;
        if (null === $reader) {
            return;
        }
        $this->offerReader = null;

        try {
            $count = 0;
            do {
                $xml = $this->loadXml($reader->readOuterXml());
                yield $count => $this->buildOffer($xml, $shop);
                ++$count;
            } while ($reader->moveToNextSibling());
        } finally {
            $reader->close();
        }
    }

    protected function buildShop(): Shop
    {
        $xml = $this->loadXml($this->shopXML);

        $shop = $this->factory->createShop();

        $shop
            ->setName((string) $xml->name)
            ->setAgency((string) $xml->agency)
            ->setEmail((string) $xml->email)
            ->setCompany((string) $xml->company)
            ->setPlatform((string) $xml->platform)
            ->setUrl((string) $xml->url)
            ->setVersion((string) $xml->version)
            ->setXml($xml)
        ;

        $this->buildCurrencies($xml, $shop);
        $this->buildCategories($xml, $shop);

        return $shop;
    }

    protected function createParam(\SimpleXMLElement $xml): Param
    {
        $param = $this->factory->createParam();
        $param
            ->setName((string) $xml['name'])
            ->setUnit((string) $xml['unit'])
            ->setValue((string) $xml)
        ;

        return $param;
    }

    protected function buildCurrencies(\SimpleXMLElement $xml, Shop $shop): void
    {
        foreach ($xml->xpath('//currencies/currency') ?? [] as $elem) {
            $shop->addCurrency($this->buildCurrency($elem));
        }
    }

    protected function buildCategories(\SimpleXMLElement $xml, Shop $shop): void
    {
        $parents = [];
        foreach ($xml->xpath('//categories/category') ?? [] as $categoryXml) {
            $shop->addCategory($this->buildCategory($categoryXml));

            $id = (string) $categoryXml['id'];
            foreach (['parentId', 'parent_id'] as $field) {
                if (isset($categoryXml[$field])) {
                    $parents[$id] = (string) $categoryXml[$field];
                    break;
                }
            }
        }

        foreach ($parents as $id => $parentId) {
            $id = (string) $id;
            $parent = ($id !== $parentId) ? $shop->getCategory($parentId) : null;
            $shop->getCategory($id)?->setParent($parent);
        }
    }

    protected function buildCurrency(\SimpleXMLElement $xml): Currency
    {
        $id = Currency::normalize((string) $xml['id']);

        $currency = $this->factory->createCurrency();
        $currency
            ->setId($id)
            ->setRate((string) $xml['rate'])
            ->setPlus((int) (string) $xml['plus'])
        ;

        return $currency;
    }

    protected function buildCategory(\SimpleXMLElement $xml): Category
    {
        $category = $this->factory->createCategory();
        $category
            ->setId((string) $xml['id'])
            ->setName((string) $xml)
        ;

        return $category;
    }

    protected function buildOffer(\SimpleXMLElement $xml, Shop $shop): Offer
    {
        $type = (string) $xml['type'];

        if ('' === $type) {
            $type = 'vendor.model';
        }

        $offer = $this->factory->createOffer($type);
        foreach ($xml->attributes() ?? [] as $key => $value) {
            $offer->setAttribute((string) $key, (string) $value);
        }

        $available = !isset($xml['available']) || 'true' === (string) $xml['available'];

        $offer
            ->setId((string) $xml['id'])
            ->setAvailable($available)
            ->setType($type)
            ->setXml($xml)
        ;

        foreach ($xml->param as $param) {
            $offer->addParam($this->createParam($param));
        }

        foreach ($xml as $field => $value) {
            if ('param' === $field) {
                continue;
            }
            $offer->applyField((string) $field, (string) $value);
        }

        $currencyId = Currency::normalize((string) $xml->currencyId);
        $currency = $shop->getCurrency($currencyId);
        if (null !== $currency) {
            $offer->setCurrency($currency);
        }

        $categoryId = (string) $xml->categoryId;
        $category = $shop->getCategory($categoryId);
        if (null !== $category) {
            $offer->setCategory($category);
        }

        return $offer;
    }

    /**
     * @throws YMLException
     */
    protected function loadXml(string $xml): \SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            $element = simplexml_load_string($xml, \SimpleXMLElement::class, self::XML_OPTIONS);
            if (false === $element) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $message = 'Failed to parse XML';
                if ([] !== $errors) {
                    $message .= ': '.trim($errors[0]->message);
                }
                throw new YMLException($message);
            }

            return $element;
        } finally {
            libxml_use_internal_errors($previous);
        }
    }
}
