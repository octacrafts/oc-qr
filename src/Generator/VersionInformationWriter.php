<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Generator;

use Octacrafts\QrEngine\Contracts\VersionInformationWriterInterface;
use Octacrafts\QrEngine\Domain\Module;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

/**
 * Writes the 18-bit version information (ISO/IEC 18004 §7.10) for versions 7+.
 * Bit i is placed at (i mod 3 + size - 11, i / 3) and at the transposed position.
 */
final class VersionInformationWriter implements VersionInformationWriterInterface
{
    private const GENERATOR = 0b1111100100101;

    public function write(QrMatrix $matrix, QrVersion $version): void
    {
        $versionNumber = $version->number();

        if ($versionNumber < 7) {
            return;
        }

        $size = $matrix->width();
        $bits = $this->encodeVersion($versionNumber);

        for ($i = 0; $i < 18; $i++) {
            $bit = (($bits >> $i) & 1) === 1;
            $module = $bit ? Module::Dark : Module::Light;

            $row = intdiv($i, 3);
            $col = ($i % 3) + $size - 11;

            $matrix->set($row, $col, $module);
            $matrix->set($col, $row, $module);
        }
    }

    private function encodeVersion(int $versionNumber): int
    {
        $value = $versionNumber << 12;

        for ($i = 17; $i >= 12; $i--) {
            if (($value >> $i) & 1) {
                $value ^= self::GENERATOR << ($i - 12);
            }
        }

        return ($versionNumber << 12) | ($value & 0xFFF);
    }
}
