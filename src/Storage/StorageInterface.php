<?php

namespace Sirian\YMLParser\Storage;


interface StorageInterface
{
    public function setShopXML($shopXML);
    public function getShopXML();
    public function addOfferXML($xml);
    public function getNextOfferXML();
    public function getOffersCount();
}