<?php

namespace Sirian\YMLParser;

use Sirian\YMLParser\Storage\StorageInterface;

class Storage implements StorageInterface
{
    protected $file;

    protected $shopXML;

    protected $offersCount = 0;

    public function __construct()
    {
        $this->file = tmpfile();
    }

    public function addOfferXML($xml)
    {
        $this->offersCount++;
        fputs($this->file, json_encode($xml, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "\n");
    }

    public function getShopXML()
    {
        return $this->shopXML;
    }

    public function setShopXML($shopXML)
    {
        $this->shopXML = $shopXML;

        return $this;
    }

    public function getNextOfferXML()
    {
        fseek($this->file, 0);
        while ($line = fgets($this->file)) {
            yield $line;
        }
    }

    public function getOffersCount()
    {
        return $this->offersCount;
    }
}
