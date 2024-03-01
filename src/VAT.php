<?php

declare(strict_types=1);

namespace OverSkill\FinancialTools;

use InvalidArgumentException;
use JsonSerializable;

enum VAT implements JsonSerializable
{
    case NON_ASSUJETTI;
    case ZERO_PERCENT;
    case TWO_DOT_ONE_PERCENT;
    case FIVE_DOT_FIVE_PERCENT;
    case SEVEN_PERCENT;
    case EIGHT_DOT_FIVE_PERCENT;
    case TEN_PERCENT;
    case TWENTY_PERCENT;

    public function toRate(): float
    {
        return $this->toPercentage() / 100;
    }

    public function toPercentage(): float
    {
        return match ($this) {
            self::NON_ASSUJETTI, self::ZERO_PERCENT => 0.,
            self::TWO_DOT_ONE_PERCENT => 2.1,
            self::FIVE_DOT_FIVE_PERCENT => 5.5,
            self::SEVEN_PERCENT => 7.,
            self::EIGHT_DOT_FIVE_PERCENT => 8.5,
            self::TEN_PERCENT => 10.,
            self::TWENTY_PERCENT => 20.,
        };
    }

    public static function fromRate(float $rate): self
    {
        return match ($rate) {
            0. => self::ZERO_PERCENT,
            .021 => self::TWO_DOT_ONE_PERCENT,
            .055 => self::FIVE_DOT_FIVE_PERCENT,
            .07 => self::SEVEN_PERCENT,
            .085 => self::EIGHT_DOT_FIVE_PERCENT,
            .1 => self::TEN_PERCENT,
            .2 => self::TWENTY_PERCENT,
            default => throw new InvalidArgumentException(sprintf('Invalid VAT rate: %s', $rate)),
        };
    }

    public function toString(): string
    {
        return round($this->toPercentage(), 2) . '%';
    }

    public function isAssujetti(): bool
    {
        return $this !== self::NON_ASSUJETTI;
    }

    public static function fromInclVATToExlVAT(float|int $amount, VAT $vat): float
    {
        return $amount / (1 + $vat->toRate());
    }

    public static function fromExclVATToInclVAT(float|int $amount, VAT $vat): float
    {
        return $amount * (1 + $vat->toRate());
    }

    public function jsonSerialize(): float
    {
        return $this->toRate();
    }
}
