<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

use Octacrafts\QrEngine\Exceptions\InvalidPayloadException;

final class QrVersion
{
    public function __construct(private readonly int $number)
    {
        if ($number < 1 || $number > 40) {
            throw new InvalidPayloadException('QR version must be between 1 and 40.');
        }
    }

    public static function fromNumber(int $number): self
    {
        return new self($number);
    }

    public function number(): int
    {
        return $this->number;
    }

    public function moduleCount(): int
    {
        return $this->number * 4 + 17;
    }

    public function remainderBits(): int
    {
        return match ($this->number) {
            1 => 0,
            2 => 7,
            3 => 7,
            4 => 7,
            5 => 7,
            6 => 7,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 3,
            15 => 3,
            16 => 3,
            17 => 3,
            18 => 3,
            19 => 3,
            20 => 3,
            21 => 4,
            22 => 4,
            23 => 4,
            24 => 4,
            25 => 4,
            26 => 4,
            27 => 4,
            28 => 3,
            29 => 3,
            30 => 3,
            31 => 3,
            32 => 3,
            33 => 3,
            34 => 0,
            35 => 0,
            36 => 0,
            37 => 0,
            38 => 0,
            39 => 0,
            40 => 0,
        };
    }
}
