<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Model\Offer;

use Sirian\YMLParser\Model\Category;
use Sirian\YMLParser\Model\Currency;
use Sirian\YMLParser\Model\Param;
use Sirian\YMLParser\Model\Shop;

class Offer
{
    protected string $id = '';

    protected string $type = '';

    protected string $name = '';

    protected string $description = '';

    protected bool $available = true;

    protected string $url = '';

    protected float $price = 0.0;

    protected float $oldPrice = 0.0;

    protected ?Currency $currency = null;

    protected ?Category $category = null;

    protected ?Shop $shop = null;

    protected ?string $typePrefix = null;

    /** @var list<string> */
    protected array $pictures = [];

    /** @var array<string, string> */
    protected array $attributes = [];

    protected ?\SimpleXMLElement $xml = null;

    /** @var array<string, Param> */
    protected array $params = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOldPrice(): float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice): static
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    /** @return list<string> */
    public function getPictures(): array
    {
        return $this->pictures;
    }

    /** @param iterable<string> $pictures */
    public function setPictures(iterable $pictures): static
    {
        $this->pictures = [];
        foreach ($pictures as $picture) {
            $this->addPicture((string) $picture);
        }

        return $this;
    }

    public function addPicture(string $picture): static
    {
        $this->pictures[] = $picture;

        return $this;
    }

    public function getTypePrefix(): ?string
    {
        return $this->typePrefix;
    }

    public function setTypePrefix(?string $typePrefix): static
    {
        $this->typePrefix = ('' === $typePrefix) ? null : $typePrefix;

        return $this;
    }

    /** @return array<string, string> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /** @param array<string, string> $attributes */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute(string $key, string $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function getAttribute(string $key, ?string $defaultValue = null): ?string
    {
        return $this->attributes[$key] ?? $defaultValue;
    }

    public function getXml(): ?\SimpleXMLElement
    {
        return $this->xml;
    }

    public function setXml(?\SimpleXMLElement $xml): static
    {
        $this->xml = $xml;

        return $this;
    }

    public function getParam(string $name): ?Param
    {
        $name = mb_strtolower($name, 'UTF-8');

        return $this->params[$name] ?? null;
    }

    /** @return array<string, Param> */
    public function getParams(): array
    {
        return $this->params;
    }

    public function addParam(Param $param): static
    {
        $name = mb_strtolower($param->getName(), 'UTF-8');
        $this->params[$name] = $param;

        return $this;
    }

    /** @param iterable<Param> $params */
    public function setParams(iterable $params): static
    {
        $this->params = [];
        foreach ($params as $param) {
            $this->addParam($param);
        }

        return $this;
    }

    /**
     * Applies a YML offer field to the corresponding setter.
     * Returns true if the field was handled.
     */
    public function applyField(string $field, string $value): bool
    {
        switch ($field) {
            case 'url':
                $this->setUrl($value);

                return true;
            case 'price':
                $this->setPrice((float) $value);

                return true;
            case 'oldprice':
                $this->setOldPrice((float) $value);

                return true;
            case 'picture':
                $this->addPicture($value);

                return true;
            case 'name':
                $this->setName($value);

                return true;
            case 'description':
                $this->setDescription($value);

                return true;
            case 'typePrefix':
                $this->setTypePrefix($value);

                return true;
        }

        return false;
    }
}
