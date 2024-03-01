<?php

declare(strict_types=1);

namespace OverSkill\FinancialTools\Tests;

use OverSkill\FinancialTools\Rate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RateTest extends TestCase
{
    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_calculate_percentage(array $state): void
    {
        $rate = new Rate($state['rate']);
        $this->assertEquals($state['percentage'], $rate->toPercentage());
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_calculate_rate(array $state): void
    {
        $rate = new Rate($state['rate']);
        $this->assertEquals($state['rate'], $rate->toRate());
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_create_from_rate(array $state): void
    {
        $rate = Rate::fromRate($state['rate']);
        $this->assertEquals($state['rate'], $rate->toRate());
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_create_from_percentage(array $state): void
    {
        $rate = Rate::fromPercentage($state['percentage']);
        $this->assertEquals($state['rate'], $rate->toRate());
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_formate_to_string(array $state): void
    {
        $rate = new Rate($state['rate']);
        $this->assertEquals($state['string'], (string) $rate);
    }

    #[DataProvider('dataProvider')]
    #[Test]
    public function it_should_format_in_json(array $state): void
    {
        $rate = new Rate($state['rate']);
        $this->assertEquals($state['rate'], $rate->jsonSerialize());
    }

    #[Test]
    public function it_should_create_from_nullable_rate(): void
    {
        $rate = Rate::fromNullableRate(null, 0.1);
        $this->assertEquals(0.1, $rate->toRate());
    }

    #[Test]
    public function it_should_create_from_nullable_percentage(): void
    {
        $rate = Rate::fromNullablePercentage(null, 10.);
        $this->assertEquals(0.1, $rate->toRate());
    }

    #[Test]
    public function it_should_return_null_from_nullable_rate(): void
    {
        $rate = Rate::fromNullableRate(null);
        $this->assertNull($rate);
    }

    #[Test]
    public function it_should_return_null_from_nullable_percentage(): void
    {
        $rate = Rate::fromNullablePercentage(null);
        $this->assertNull($rate);
    }


    public static function dataProvider(): array
    {
        return [
            [
                ['rate' => 0.1,  'percentage' => 10., 'string' => '10,00%'],
            ],
            [
                ['rate' => -0.1,  'percentage' => -10.,  'string' => '-10,00%'],
            ],
            [
                ['rate' => 1.2, 'percentage' => 120., 'string' => '120,00%'],
            ],
            [
                ['rate' => 0.006, 'percentage' => 0.6, 'string' => '0,60%'],
            ],
        ];
    }
}
