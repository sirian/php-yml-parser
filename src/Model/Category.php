<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Model;

class Category
{
    protected string $id = '';

    protected ?Category $parent = null;

    protected string $name = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent = null): static
    {
        $this->parent = $parent;

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

    public function hasParent(): bool
    {
        return null !== $this->parent;
    }
}
