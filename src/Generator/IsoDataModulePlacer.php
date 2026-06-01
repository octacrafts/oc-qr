<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Generator;

use Octacrafts\QrEngine\Contracts\DataModulePlacerInterface;
use Octacrafts\QrEngine\Domain\CodewordSequence;
use Octacrafts\QrEngine\Domain\Module;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

final class IsoDataModulePlacer implements DataModulePlacerInterface
{
    public function place(QrMatrix $matrix, CodewordSequence $codewords, QrVersion $version): void
    {
        $data = $codewords->interleaved();
        $byteCount = count($data);
        $size = $version->moduleCount();
        $iByte = 0;
        $iBit = 7;
        $direction = true;

        for ($col = $size - 1; $col > 0; $col -= 2) {
            if ($col === 6) {
                $col--;
            }

            for ($count = 0; $count < $size; $count++) {
                $row = $direction ? $size - 1 - $count : $count;

                for ($offset = 0; $offset < 2; $offset++) {
                    $currentCol = $col - $offset;

                    if ($matrix->isReserved($row, $currentCol)) {
                        continue;
                    }

                    $isDark = $iByte < $byteCount && (($data[$iByte] >> $iBit) & 1) === 1;
                    $matrix->set($row, $currentCol, $isDark ? Module::Dark : Module::Light);

                    if ($iBit === 0) {
                        $iByte++;
                        $iBit = 7;
                    } else {
                        $iBit--;
                    }
                }
            }

            $direction = ! $direction;
        }
    }
}
