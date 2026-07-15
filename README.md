# Yandex Market Language Parser

[![CI](https://github.com/sirian/php-yml-parser/actions/workflows/ci.yml/badge.svg)](https://github.com/sirian/php-yml-parser/actions/workflows/ci.yml)

Streaming parser for [Yandex Market Language](https://yandex.ru/support/partnermarket/yml/about-yml.xml) (YML) feeds.

Uses `XMLReader` for the outer scan and `SimpleXMLElement` for per-offer parsing, so the whole catalog is never loaded in memory — offers arrive as a `Generator`.

## Requirements

- PHP **>= 8.3**
- Extensions: `ext-mbstring`, `ext-simplexml`, `ext-xmlreader`, `ext-libxml`

## Installation

```bash
composer require sirian/yandex-market-language-parser
```

## Usage

```php
use Sirian\YMLParser\Parser;

$parser = new Parser();
$result = $parser->parse('/path/to/catalog.xml');

$shop = $result->getShop();
echo $shop->getName();

foreach ($shop->getCategories() as $category) {
    echo $category->getId(), ': ', $category->getName(), "\n";
}

foreach ($result->getOffers() as $offer) {
    echo $offer->getId(), ' — ', $offer->getName(), "\n";
}
```

### Offer types

`Sirian\YMLParser\Factory\Factory::createOffer()` returns:

| `type` attribute | class                                                    |
|------------------|----------------------------------------------------------|
| `vendor.model`   | `Sirian\YMLParser\Model\Offer\VendorModelOffer`          |
| `book`           | `Sirian\YMLParser\Model\Offer\BookOffer`                 |
| _anything else_  | `Sirian\YMLParser\Model\Offer\Offer`                     |
| _absent_         | `Sirian\YMLParser\Model\Offer\VendorModelOffer` (default) |

### Customization

Provide a custom `FactoryInterface` to swap any model/storage implementation:

```php
use Sirian\YMLParser\Factory\Factory;
use Sirian\YMLParser\Model\Offer\Offer;
use Sirian\YMLParser\Parser;

$factory = new class () extends Factory {
    public function createOffer(string $type): Offer
    {
        // return your own subclass
        return parent::createOffer($type);
    }
};

$parser = new Parser($factory);
```

### Extending offer field mapping

`Offer::applyField(string $field, string $value): bool` maps XML child elements onto setters. Override it in your own offer subclass to handle YML fields beyond the built-in ones:

```php
final class MyOffer extends VendorModelOffer
{
    public function applyField(string $field, string $value): bool
    {
        if ('country_of_origin' === $field) {
            $this->country = $value;
            return true;
        }

        return parent::applyField($field, $value);
    }
}
```

Any field can also be accessed via the raw XML — `$offer->getXml()` / `$shop->getXml()`.

## Development

```bash
composer install
vendor/bin/phpunit        # tests + coverage
vendor/bin/phpstan analyse # static analysis (level 8)
vendor/bin/php-cs-fixer fix --dry-run --diff  # code style
```

## License

MIT
