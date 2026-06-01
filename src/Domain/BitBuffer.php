<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class BitBuffer
{
    /** @var list<int> */
    private array $bits = [];

    public function appendBits(int $value, int $length): void
    {
        for ($i = $length - 1; $i >= 0; $i--) {
            $this->bits[] = ($value >> $i) & 1;
        }
    }

    public function length(): int
    {
        return count($this->bits);
    }

    /** @return list<int> */
    public function bits(): array
    {
        return $this->bits;
    }

    public function clone(): self
    {
        $clone = new self();
        $clone->bits = $this->bits;

        return $clone;
    }

    /** @return list<int> */
    public function toBytes(): array
    {
        $bytes = [];
        $byte = 0;
        $bitCount = 0;

        foreach ($this->bits as $bit) {
            $byte = ($byte << 1) | $bit;
            $bitCount++;

            if ($bitCount === 8) {
                $bytes[] = $byte;
                $byte = 0;
                $bitCount = 0;
            }
        }

        if ($bitCount > 0) {
            $bytes[] = $byte << (8 - $bitCount);
        }

        return $bytes;
    }

    public function bitAt(int $index): int
    {
        return $this->bits[$index] ?? 0;
    }
}
