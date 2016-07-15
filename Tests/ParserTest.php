<?php

namespace Sirian\YMLParser;

use Sirian\YMLParser\Offer\Offer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $parser = new Parser();

        /**
         * @var Offer[] $offers
         */

        $file = __DIR__ . '/Fixtures/yml.xml';



        $result = $parser->parse($file);

        $shop = $result->getShop();

        $xml = simplexml_load_file($file);
        $xml = $xml->shop;

        $this->assertCount(3, $shop->getCurrencies());
        $this->assertCount(19, $shop->getCategories());
        $this->assertEquals((string)$xml->name, $shop->getName());
        $this->assertEquals((string)$xml->url, $shop->getUrl());
        $this->assertEquals((string)$xml->company, $shop->getCompany());
        $this->assertEquals(7, $shop->getOffersCount());

        $offers = iterator_to_array($result->getOffers());

        $this->assertCount(7, $offers);

        $offer = $offers[0];

        $this->assertEquals(3, $offer->getParam('скорость')->getValue());
        $this->assertEquals('true', $offer->getAttribute('available'));
        $this->assertEquals(25000, $offer->getOldPrice());
        $this->assertEquals(15000, $offer->getPrice());
    }
}
