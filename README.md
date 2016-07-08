
# YandexMarketLanguage Parser #

## About ##

[Yandex Market Language](https://yandex.ru/support/partnermarket/yml/about-yml.xml) parser for PHP. 


## 1. Installation ##

Add the `sirian/yandex-market-language-parser` package to your `require` section in the `composer.json` file.

``` bash
$ composer require sirian/yandex-market-language-parser
```

## 2. Usage ##

```php

use Sirian\YMLParser\Parser;

$parser = new Parser();
$result = $parser->parse($file);

$shop = $result->getShop();

foreach ($result->getOffers() as $offer) {
    // some logic
}
```
