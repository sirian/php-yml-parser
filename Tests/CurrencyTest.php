<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sirian\YMLParser\Model\Currency;

#[CoversNothing]
class CurrencyTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function normalizationProvider(): iterable
    {
        yield 'RUR is uplifted to RUB' => ['RUR', 'RUB'];
        yield 'BYR is uplifted to BYN' => ['BYR', 'BYN'];
        yield 'RUB stays RUB' => ['RUB', 'RUB'];
        yield 'lowercase usd is upper-cased' => ['usd', 'USD'];
        yield 'mixed-case rur is uplifted' => ['rUr', 'RUB'];
        yield 'unknown code kept upper-cased' => ['xyz', 'XYZ'];
    }

    #[DataProvider('normalizationProvider')]
    public function testNormalize(string $input, string $expected): void
    {
        $this->assertSame($expected, Currency::normalize($input));
    }
}
