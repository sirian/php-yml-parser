<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Model\Offer;

class VendorModelOffer extends Offer
{
    protected ?string $vendor = null;

    protected ?string $vendorCode = null;

    protected ?string $model = null;

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): static
    {
        $this->vendor = ('' === $vendor) ? null : $vendor;

        return $this;
    }

    public function getVendorCode(): ?string
    {
        return $this->vendorCode;
    }

    public function setVendorCode(?string $vendorCode): static
    {
        $this->vendorCode = ('' === $vendorCode) ? null : $vendorCode;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = ('' === $model) ? null : $model;

        return $this;
    }

    public function applyField(string $field, string $value): bool
    {
        switch ($field) {
            case 'vendor':
                $this->setVendor($value);

                return true;
            case 'vendorCode':
                $this->setVendorCode($value);

                return true;
            case 'model':
                $this->setModel($value);

                return true;
        }

        return parent::applyField($field, $value);
    }
}
