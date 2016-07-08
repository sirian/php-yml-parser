<?php

namespace Sirian\YMLParser\Offer;

class VendorModelOffer extends Offer
{
    protected $vendor;
    protected $vendorCode;
    protected $model;

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
        return $this;
    }

    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
}
