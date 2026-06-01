<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

enum ErrorCorrectionLevel: int
{
    case L = 0;
    case M = 1;
    case Q = 2;
    case H = 3;

    public function formatBits(): int
    {
        return match ($this) {
            self::L => 0b01,
            self::M => 0b00,
            self::Q => 0b11,
            self::H => 0b10,
        };
    }
}
