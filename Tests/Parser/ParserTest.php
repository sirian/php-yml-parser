<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Offer\Offer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $parser = new Parser();

        /**
         * @var Offer[] $offers
         */
        $offers = [];
        $file = __DIR__ . '/Fixtures/yml.xml';

        $parser->addListener(ParserEvents::OFFER, function (OfferEvent $event) use (&$offers) {
            $offer = $event->getOffer();
            $offers[] = $offer;
        });

        $parser->addListener(ParserEvents::SHOP, function (ShopEvent $event) use ($file) {
            $shop = $event->getShop();
            $xml = simplexml_load_file($file);
            $xml = $xml->shop;

            $this->assertCount(3, $shop->getCurrencies());
            $this->assertCount(19, $shop->getCategories());
            $this->assertEquals((string)$xml->name, $shop->getName());
            $this->assertEquals((string)$xml->url, $shop->getUrl());
            $this->assertEquals((string)$xml->company, $shop->getCompany());
            $this->assertEquals(7, $shop->getOffersCount());
        });



        $parser->parse($file);

        $this->assertCount(7, $offers);

        $offer = $offers[0];

        $this->assertEquals(3, $offer->getParam('скорость')->getValue());
        $this->assertEquals('true', $offer->getAttribute('available'));
    }
}
