<?php

declare(strict_types=1);

namespace OverSkill\FinancialTools\Tests;

use OverSkill\FinancialTools\VAT;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VatTest extends TestCase
{
    #[Test]
    #[DataProvider('vatRateProvider')]
    public function it_should_return_pourcentage_and_rate(VAT $vat, float $expected): void
    {
        $this->assertEquals($expected, $vat->toPercentage());
        $this->assertEquals($expected / 100, $vat->toRate());
    }

    public static function vatRateProvider(): array
    {
        return [
            [VAT::ZERO_PERCENT, 0.],
            [VAT::TWO_DOT_ONE_PERCENT, 2.1],
            [VAT::FIVE_DOT_FIVE_PERCENT, 5.5],
            [VAT::SEVEN_PERCENT, 7.],
            [VAT::EIGHT_DOT_FIVE_PERCENT, 8.5],
            [VAT::TEN_PERCENT, 10.],
            [VAT::TWENTY_PERCENT, 20.],
        ];
    }

    #[Test]
    #[DataProvider('vatDisplayProvider')]
    public function it_should_be_converted_to_string(VAT $vat, string $expected): void
    {
        $this->assertEquals($expected,  $vat->toString());
    }

    public static function vatDisplayProvider(): array
    {
        return [
            [VAT::ZERO_PERCENT, '0%'],
            [VAT::TWO_DOT_ONE_PERCENT, '2.1%'],
            [VAT::FIVE_DOT_FIVE_PERCENT, '5.5%'],
            [VAT::SEVEN_PERCENT, '7%'],
            [VAT::EIGHT_DOT_FIVE_PERCENT, '8.5%'],
            [VAT::TEN_PERCENT, '10%'],
            [VAT::TWENTY_PERCENT, '20%'],
        ];
    }

    #[Test]
    public function it_should_be_able_to_convert_from_included_vat_to_excluded_vat(): void
    {
        $HT = VAT::fromInclVATToExlVAT(100, VAT::TWENTY_PERCENT);
        $this->assertEquals(83.33, round($HT, 2));
    }

    #[Test]
    public function it_should_be_able_to_convert_from_exclude_vat_to_included_vat(): void
    {
        $TTC = VAT::fromExclVATToInclVAT(90, VAT::TWENTY_PERCENT);
        $this->assertEquals(108, $TTC);
    }
}