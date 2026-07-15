<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Exception\YMLException;
use Sirian\YMLParser\Model\Offer\BookOffer;
use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Model\Offer\VendorModelOffer;
use Sirian\YMLParser\Parser;

#[CoversNothing]
class ParserTest extends TestCase
{
    private function fixture(string $name): string
    {
        return __DIR__.'/Fixtures/'.$name;
    }

    private function parse(string $fixture): BuilderInterface
    {
        return (new Parser())->parse($this->fixture($fixture));
    }

    /** @return list<Offer> */
    private function offers(BuilderInterface $result): array
    {
        return iterator_to_array($result->getOffers(), false);
    }

    public function testParseFullFixture(): void
    {
        $result = $this->parse('yml.xml');
        $shop = $result->getShop();

        $rawXml = simplexml_load_file($this->fixture('yml.xml'));
        $this->assertNotFalse($rawXml);
        $xml = $rawXml->shop;

        $this->assertCount(3, $shop->getCurrencies());
        $this->assertCount(19, $shop->getCategories());
        $this->assertSame((string) $xml->name, $shop->getName());
        $this->assertSame((string) $xml->url, $shop->getUrl());
        $this->assertSame((string) $xml->company, $shop->getCompany());

        $offers = $this->offers($result);
        $this->assertCount(7, $offers);

        $offer = $offers[0];
        $this->assertSame('3', $offer->getParam('скорость')?->getValue());
        $this->assertSame('true', $offer->getAttribute('available'));
        $this->assertSame(25000.0, $offer->getOldPrice());
        $this->assertSame(15000.0, $offer->getPrice());
    }

    public function testMinimalYml(): void
    {
        $result = $this->parse('minimal.xml');
        $shop = $result->getShop();

        $this->assertSame('MinShop', $shop->getName());
        $this->assertSame('Min LLC', $shop->getCompany());
        $this->assertSame('https://min.example.com/', $shop->getUrl());
        $this->assertNull($shop->getPlatform());
        $this->assertNull($shop->getVersion());
        $this->assertNull($shop->getAgency());
        $this->assertNull($shop->getEmail());
        $this->assertCount(1, $shop->getCurrencies());
        $this->assertCount(1, $shop->getCategories());

        $offers = $this->offers($result);
        $this->assertCount(1, $offers);

        $offer = $offers[0];
        $this->assertSame('1', $offer->getId());
        $this->assertSame('Item A', $offer->getName());
        $this->assertSame(100.0, $offer->getPrice());
        $this->assertSame(0.0, $offer->getOldPrice());
        $this->assertTrue($offer->isAvailable());
        $this->assertSame('', $offer->getDescription());
        $this->assertSame([], $offer->getPictures());
        $this->assertSame([], $offer->getParams());
        $this->assertNotNull($offer->getCurrency());
        $this->assertSame('RUB', $offer->getCurrency()->getId());
        $this->assertNotNull($offer->getCategory());
        $this->assertSame('Root', $offer->getCategory()->getName());
    }

    public function testVendorModelOfferFields(): void
    {
        $result = $this->parse('vendor_model.xml');
        $offers = $this->offers($result);
        $this->assertCount(2, $offers);

        $offer = $offers[0];
        $this->assertInstanceOf(VendorModelOffer::class, $offer);
        $this->assertSame('42', $offer->getId());
        $this->assertSame('vendor.model', $offer->getType());
        $this->assertFalse($offer->isAvailable());
        $this->assertSame('https://vm.example.com/p/42', $offer->getUrl());
        $this->assertSame(15000.0, $offer->getPrice());
        $this->assertSame(25000.0, $offer->getOldPrice());
        $this->assertSame('Printer', $offer->getTypePrefix());
        $this->assertSame('HP', $offer->getVendor());
        $this->assertSame('Q7533A', $offer->getVendorCode());
        $this->assertSame('Color LaserJet 3000', $offer->getModel());
        $this->assertSame('Fast color printer', $offer->getDescription());
        $this->assertSame(
            [
                'https://vm.example.com/img/1.jpg',
                'https://vm.example.com/img/2.jpg',
                'https://vm.example.com/img/3.jpg',
            ],
            $offer->getPictures(),
        );
        $this->assertSame('10', $offer->getAttribute('bid'));
        $this->assertSame('false', $offer->getAttribute('available'));
    }

    public function testMissingTypeDefaultsToVendorModel(): void
    {
        $result = $this->parse('vendor_model.xml');
        $offers = $this->offers($result);

        $untyped = $offers[1];
        $this->assertInstanceOf(VendorModelOffer::class, $untyped);
        $this->assertSame('vendor.model', $untyped->getType());
    }

    public function testBookOfferType(): void
    {
        $result = $this->parse('book.xml');
        $offers = $this->offers($result);
        $this->assertCount(1, $offers);

        $offer = $offers[0];
        $this->assertInstanceOf(BookOffer::class, $offer);
        $this->assertSame('book', $offer->getType());
        $this->assertSame('The PHP Way', $offer->getName());
        $this->assertSame(350.0, $offer->getPrice());
    }

    public function testUnknownTypeReturnsGenericOffer(): void
    {
        $result = $this->parse('simplified.xml');
        $offer = $this->offers($result)[0];

        $this->assertSame(Offer::class, $offer::class);
        $this->assertSame('event-ticket', $offer->getType());
        $this->assertSame('Concert', $offer->getName());
        $this->assertSame('Live music show', $offer->getDescription());
        $this->assertSame(1500.5, $offer->getPrice());
        $this->assertTrue($offer->isAvailable());
    }

    public function testCurrencyRurNormalizedToRub(): void
    {
        $result = $this->parse('currencies.xml');
        $shop = $result->getShop();

        $this->assertNotNull($shop->getCurrency('RUB'));
        $this->assertNull($shop->getCurrency('RUR'));

        $offer = $this->offers($result)[0];
        $this->assertNotNull($offer->getCurrency());
        $this->assertSame('RUB', $offer->getCurrency()->getId());
    }

    public function testCurrencyRateFormats(): void
    {
        $shop = $this->parse('currencies.xml')->getShop();

        $rub = $shop->getCurrency('RUB');
        $usd = $shop->getCurrency('USD');
        $eur = $shop->getCurrency('EUR');

        $this->assertNotNull($rub);
        $this->assertNotNull($usd);
        $this->assertNotNull($eur);

        $this->assertSame('1', $rub->getRate());
        $this->assertSame(0, $rub->getPlus());
        $this->assertSame('CBRF', $usd->getRate());
        $this->assertSame(3, $usd->getPlus());
        $this->assertSame('90.5', $eur->getRate());
    }

    public function testCategoryHierarchy(): void
    {
        $shop = $this->parse('nested_categories.xml')->getShop();

        $this->assertCount(5, $shop->getCategories());

        $electronics = $shop->getCategory('1');
        $phones = $shop->getCategory('2');
        $smartphones = $shop->getCategory('3');
        $laptops = $shop->getCategory('4');
        $selfParent = $shop->getCategory('5');

        $this->assertNotNull($electronics);
        $this->assertFalse($electronics->hasParent());

        $this->assertNotNull($phones);
        $this->assertTrue($phones->hasParent());
        $this->assertSame('Electronics', $phones->getParent()?->getName());

        $this->assertNotNull($smartphones);
        $this->assertSame('Phones', $smartphones->getParent()?->getName());

        $this->assertNotNull($laptops);
        $this->assertSame('Electronics', $laptops->getParent()?->getName());

        $this->assertNotNull($selfParent);
        $this->assertNull($selfParent->getParent(), 'Self-parent must not link to itself');
    }

    public function testParamsWithAndWithoutUnit(): void
    {
        $offer = $this->offers($this->parse('vendor_model.xml'))[0];

        $speed = $offer->getParam('speed');
        $this->assertNotNull($speed);
        $this->assertSame('speed', $speed->getName());
        $this->assertSame('15', $speed->getValue());
        $this->assertSame('ppm', $speed->getUnit());

        $weight = $offer->getParam('weight');
        $this->assertNotNull($weight);
        $this->assertNull($weight->getUnit());

        $this->assertNull($offer->getParam('missing'));
    }

    public function testGetOffersReturnsGenerator(): void
    {
        $result = $this->parse('minimal.xml');
        $this->assertInstanceOf(\Generator::class, $result->getOffers());
    }

    public function testShopXmlIsAccessible(): void
    {
        $shop = $this->parse('minimal.xml')->getShop();

        $xml = $shop->getXml();
        $this->assertNotNull($xml);
        $this->assertSame('MinShop', (string) $xml->name);
    }

    public function testOfferXmlIsAccessible(): void
    {
        $offer = $this->offers($this->parse('minimal.xml'))[0];

        $xml = $offer->getXml();
        $this->assertNotNull($xml);
        $this->assertSame('1', (string) $xml['id']);
    }

    public function testParseThrowsOnMissingFile(): void
    {
        $this->expectException(YMLException::class);
        (new Parser())->parse($this->fixture('does_not_exist.xml'));
    }

    public function testParseThrowsWhenShopMissing(): void
    {
        $this->expectException(YMLException::class);
        $this->expectExceptionMessage('<shop> element not found');
        $this->parse('no_shop.xml');
    }

    public function testParseThrowsOnMalformedXml(): void
    {
        $this->expectException(YMLException::class);
        $result = $this->parse('malformed.xml');
        // Malformed markup only surfaces when we materialize the shop XML.
        $result->getShop();
    }

    public function testOfferWithMissingCategoriesSection(): void
    {
        $result = $this->parse('no_categories.xml');
        $shop = $result->getShop();

        $this->assertSame([], $shop->getCategories());

        $offer = $this->offers($result)[0];
        $this->assertSame('Orphan', $offer->getName());
        $this->assertNull($offer->getCategory());
        $this->assertSame('99', (string) $offer->getXml()?->categoryId);
    }

    public function testEmptyOffersSectionProducesNoOffers(): void
    {
        $result = $this->parse('empty_offers.xml');
        $result->getShop();

        $this->assertSame([], $this->offers($result));
    }

    public function testRepeatedParseOnSameParser(): void
    {
        $parser = new Parser();

        $first = $parser->parse($this->fixture('minimal.xml'));
        $second = $parser->parse($this->fixture('vendor_model.xml'));

        $this->assertSame('MinShop', $first->getShop()->getName());
        $this->assertSame('VmShop', $second->getShop()->getName());
        $this->assertCount(1, $this->offers($first));
        $this->assertCount(2, $this->offers($second));
    }

    public function testCustomFactoryIsUsed(): void
    {
        $factory = new class extends \Sirian\YMLParser\Factory\Factory {
            public int $offersCreated = 0;

            public function createOffer(string $type): Offer
            {
                ++$this->offersCreated;

                return parent::createOffer($type);
            }
        };

        $result = (new Parser($factory))->parse($this->fixture('minimal.xml'));
        iterator_to_array($result->getOffers(), false);

        $this->assertSame(1, $factory->offersCreated);
    }

    public function testXxeEntitiesAreNotExpanded(): void
    {
        // With LIBXML_NONET set and LIBXML_DTDLOAD *not* set, the DTD is never fetched,
        // so the &xxe; entity is undefined — simplexml raises a parse error instead of
        // reading /etc/passwd. Either behaviour proves the parser is safe from XXE.
        $this->expectException(YMLException::class);
        $this->expectExceptionMessageMatches('/Entity .* not defined|Failed to parse XML/');

        $this->parse('xxe.xml')->getShop();
    }
}
