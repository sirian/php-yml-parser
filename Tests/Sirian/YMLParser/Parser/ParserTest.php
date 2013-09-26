<?php

namespace Sirian\YMLParser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $parser = new Parser();

        $offers = [];

        $parser->addListener('offer', function (OfferEvent $event) use (&$offers) {
            $offer = $event->getOffer();
            $offers[] = $offer;
        });

        $parser->addListener('currencies', function (CurrenciesEvent $event) {
            $this->assertCount(3, $event->getCurrencies());
        });

        $parser->addListener('categories', function (CategoriesEvent $event) {
            $this->assertCount(19, $event->getCategories());
        });

        $parser->parse(__DIR__ . '/Fixtures/yml.xml');

        $this->assertCount(2, $offers);
    }
}
