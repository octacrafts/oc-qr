<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Generator;

use Octacrafts\QrEngine\Contracts\FormatInformationWriterInterface;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\Module;
use Octacrafts\QrEngine\Domain\QrMatrix;

/**
 * Writes the 15-bit format information (ISO/IEC 18004 §7.9) into the two
 * format-info regions of the matrix.
 *
 * The 15-bit value is computed as:
 *   data    = (errorLevelFormatBits << 3) | maskPattern             (5 bits)
 *   encoded = (data << 10) | BCH_remainder(data << 10, g(x))        (15 bits)
 *   final   = encoded XOR 0x5412                                    (mask)
 *
 * Bit i (0 = LSB) of the final value is then written to two cells.
 */
final class FormatInformationWriter implements FormatInformationWriterInterface
{
    private const GENERATOR = 0b10100110111;
    private const MASK = 0b101010000010010;

    public function write(QrMatrix $matrix, ErrorCorrectionLevel $level, int $maskPattern): void
    {
        $bits = $this->encodeFormat($level, $maskPattern);
        $size = $matrix->width();

        for ($i = 0; $i < 15; $i++) {
            $isDark = (($bits >> $i) & 1) === 1;
            $module = $isDark ? Module::Dark : Module::Light;

            [$row1, $col1] = $this->primaryPosition($i, $size);
            $matrix->set($row1, $col1, $module);

            [$row2, $col2] = $this->secondaryPosition($i, $size);
            $matrix->set($row2, $col2, $module);
        }
    }

    /**
     * Vertical strip on the right of the top-left finder, plus the
     * vertical strip above the bottom-left finder.
     *
     * @return array{int, int}
     */
    private function primaryPosition(int $bit, int $size): array
    {
        if ($bit < 6) {
            return [$bit, 8];
        }

        if ($bit < 8) {
            return [$bit + 1, 8];
        }

        return [$size - 15 + $bit, 8];
    }

    /**
     * Horizontal strip below the top-left finder, plus the horizontal
     * strip below the top-right finder.
     *
     * @return array{int, int}
     */
    private function secondaryPosition(int $bit, int $size): array
    {
        if ($bit < 8) {
            return [8, $size - 1 - $bit];
        }

        if ($bit < 9) {
            return [8, 15 - $bit];
        }

        return [8, 14 - $bit];
    }

    private function encodeFormat(ErrorCorrectionLevel $level, int $maskPattern): int
    {
        $data = ($level->formatBits() << 3) | $maskPattern;
        $value = $data << 10;

        for ($i = 14; $i >= 10; $i--) {
            if ((($value >> $i) & 1) === 1) {
                $value ^= self::GENERATOR << ($i - 10);
            }
        }

        return ((($data << 10) | ($value & 0x3FF)) ^ self::MASK) & 0x7FFF;
    }
}
