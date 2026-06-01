<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

enum EncodingMode: int
{
    case Numeric = 0x01;
    case Alphanumeric = 0x02;
    case Byte = 0x04;
    case Kanji = 0x08;
    case Eci = 0x07;

    public function indicatorBits(): int
    {
        return match ($this) {
            self::Numeric => 0b0001,
            self::Alphanumeric => 0b0010,
            self::Byte => 0b0100,
            self::Kanji => 0b1000,
            self::Eci => 0b0111,
        };
    }

    public function characterCountBits(QrVersion $version): int
    {
        $versionNumber = $version->number();

        return match ($this) {
            self::Numeric => $versionNumber <= 9 ? 10 : ($versionNumber <= 26 ? 12 : 14),
            self::Alphanumeric => $versionNumber <= 9 ? 9 : ($versionNumber <= 26 ? 11 : 13),
            self::Byte, self::Kanji => $versionNumber <= 9 ? 8 : 16,
            self::Eci => 8,
        };
    }
}
