<?php

declare(strict_types=1);

namespace OverSkill\FinancialTools;

use OverSkill\FinancialTools\Exception\AmbiguityException;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;

readonly class Amount implements Arrayable, JsonSerializable
{
    private const ROUND_DECIMALS = 2;

    private function __construct(
        private float $amountExcludingVat,
        private VAT $vat,
    ) {
        if ($this->amountExcludingVat < 0) {
            throw new InvalidArgumentException('Amount can not be lower than zero');
        }
    }

    public static function fromArray(array $value): self
    {
        return new self(
            $value['amountExcludingVat'],
            match ($value['isAssujetti']) {
                false => VAT::NON_ASSUJETTI,
                default => VAT::fromRate($value['vatRate']),
            }
        );
    }

    public static function fromAmountExcludingVatAndRate(float $amount, VAT $rate): self
    {
        return new self($amount, $rate);
    }

    public static function fromAmountIncludingVatAndRate(float $amount, VAT $rate): self
    {
        return new self($amount / (1 + $rate->toRate()), $rate);
    }

    public static function fromAmountHavingTwentyPercentRate(float $amount): self
    {
        return new self($amount / (1 + VAT::TWENTY_PERCENT->toRate()), VAT::TWENTY_PERCENT);
    }

    public static function nullish(): self
    {
        return new self(0, VAT::ZERO_PERCENT);
    }

    public function isEmpty(): bool
    {
        return $this->amountExcludingVat === 0.0;
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function includingVat(): float
    {
        $this->assertAssujetti();

        return round($this->amountExcludingVat * (1 + $this->vatRate()), self::ROUND_DECIMALS);
    }

    public function value(): float
    {
        $this->assertNotAssujetti();

        return round($this->amountExcludingVat, self::ROUND_DECIMALS);
    }

    public function excludingVat(): float
    {
        $this->assertAssujetti();

        return round($this->amountExcludingVat, self::ROUND_DECIMALS);
    }

    public function vatAmount(): float
    {
        return $this->excludingVat() * $this->vatRate();
    }

    public function vatRate(): float
    {
        return $this->vat->toRate();
    }

    public function vat(): VAT
    {
        return $this->vat;
    }

    public function isAssujetti(): bool
    {
        return $this->vat->isAssujetti();
    }

    public function vatRatePercentage(): float
    {
        return $this->vatRate() * 100;
    }

    public function toFloat(): float
    {
        return $this->isAssujetti() ? $this->excludingVat() : $this->value();
    }

    public function toArray(): array
    {
        return [
            'amountExcludingVat' => $this->amountExcludingVat,
            'vatRate' => $this->vatRate(),
            'isAssujetti' => $this->isAssujetti(),
        ];
    }

    private function assertAssujetti(): void
    {
        if (! $this->isAssujetti()) {
            throw new AmbiguityException('Current Amount is not assujetti to VAT, you should use value() instead');
        }
    }

    private function assertNotAssujetti(): void
    {
        if ($this->isAssujetti()) {
            throw new AmbiguityException('Current Amount is assujetti to VAT, you should use includingVat() or excludingVat()');
        }
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
