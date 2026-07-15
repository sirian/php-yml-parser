<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Model;

class Shop
{
    protected string $name = '';

    protected string $company = '';

    protected string $url = '';

    protected ?string $platform = null;

    protected ?string $version = null;

    protected ?string $agency = null;

    protected ?string $email = null;

    /** @var array<int|string, Category> */
    protected array $categories = [];

    /** @var array<string, Currency> */
    protected array $currencies = [];

    protected ?\SimpleXMLElement $xml = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

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

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(?string $platform): static
    {
        $this->platform = ('' === $platform) ? null : $platform;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = ('' === $version) ? null : $version;

        return $this;
    }

    public function getAgency(): ?string
    {
        return $this->agency;
    }

    public function setAgency(?string $agency): static
    {
        $this->agency = ('' === $agency) ? null : $agency;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = ('' === $email) ? null : $email;

        return $this;
    }

    public function addCurrency(Currency $currency): static
    {
        $this->currencies[$currency->getId()] = $currency;

        return $this;
    }

    public function addCategory(Category $category): static
    {
        $this->categories[$category->getId()] = $category;

        return $this;
    }

    public function getCategory(string $id): ?Category
    {
        return $this->categories[$id] ?? null;
    }

    /** @return array<int|string, Category> */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getCurrency(string $id): ?Currency
    {
        return $this->currencies[$id] ?? null;
    }

    /** @return array<string, Currency> */
    public function getCurrencies(): array
    {
        return $this->currencies;
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
}
