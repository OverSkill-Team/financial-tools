<?php

declare(strict_types=1);

namespace OverSkill\FinancialTools;

use JsonSerializable;
use Stringable;

readonly class Rate implements JsonSerializable, Stringable
{
    public function __construct(private float $rate)
    {
    }

    public function toPercentage(): float
    {
        return $this->rate * 100;
    }

    public function toRate(): float
    {
        return $this->rate;
    }

    public static function fromRate(float $rate): self
    {
        return new self($rate);
    }

    public static function fromPercentage(float $percentage): self
    {
        return new self($percentage / 100);
    }

    public static function fromNullableRate(?float $rate, float $default = null): ?self
    {
        if (is_null($rate) && is_null($default)) {
            return null;
        }

        if (is_null($rate)) {
            return new self($default);
        }

        return new self($rate);
    }

    public static function fromNullablePercentage(?float $percentage, float $default = null): ?self
    {
        if (is_null($percentage) && is_null($default)) {
            return null;
        }

        if (is_null($percentage)) {
            return new self($default / 100);
        }

        return new self($percentage / 100);
    }

    public function __toString(): string
    {
        return number_format($this->rate*100, 2, ",", " ") . '%';
    }

    public function jsonSerialize(): float
    {
        return $this->rate;
    }
}
