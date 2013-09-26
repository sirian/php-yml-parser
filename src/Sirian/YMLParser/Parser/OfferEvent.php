<?php

namespace Sirian\YMLParser\Parser;

use Sirian\YMLParser\Offer\Offer;
use Symfony\Component\EventDispatcher\Event;

class OfferEvent extends Event
{
    private $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function getOffer()
    {
        return $this->offer;
    }
}
