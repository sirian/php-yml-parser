<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Shop;
use Symfony\Component\EventDispatcher\Event;

class CurrenciesEvent extends Event
{
    private $currencies;

    public function __construct($currencies)
    {
        $this->currencies = $currencies;
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }
}
