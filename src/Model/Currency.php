<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Model;

class Currency
{
    protected string $id = '';

    protected string $rate = '1';

    protected int $plus = 0;

    /**
     * Normalizes deprecated ISO 4217 codes to the current ones.
     * RUR was replaced with RUB in 1998; BYR was redenominated to BYN in 2016.
     */
    public static function normalize(string $id): string
    {
        return match (strtoupper($id)) {
            'RUR' => 'RUB',
            'BYR' => 'BYN',
            default => strtoupper($id),
        };
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getPlus(): int
    {
        return $this->plus;
    }

    public function setPlus(int $plus): static
    {
        $this->plus = $plus;

        return $this;
    }
}
