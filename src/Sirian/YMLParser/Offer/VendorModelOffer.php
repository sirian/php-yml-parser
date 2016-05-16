<?php

namespace Sirian\YMLParser\Offer;

use Sirian\YMLParser\Param;

class VendorModelOffer extends Offer
{
    protected $vendor;
    protected $vendorCode;
    protected $model;

    protected $params = [];

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

    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function addParam(Param $param)
    {
        $this->params[$param->getName()] = $param;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = [];
        if (is_array($params) || $params instanceof \Traversable) {
            foreach ($params as $param) {
                $this->addParam($param);
            }
        }

        return $this;
    }
}
