<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Generator;

use Octacrafts\QrEngine\Contracts\FunctionPatternPlacerInterface;
use Octacrafts\QrEngine\Data\AlignmentPatternTable;
use Octacrafts\QrEngine\Domain\Module;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

final class IsoFunctionPatternPlacer implements FunctionPatternPlacerInterface
{
    public function place(QrMatrix $matrix, QrVersion $version): void
    {
        $size = $version->moduleCount();

        $this->placeFinderWithSeparator($matrix, 0, 0);
        $this->placeFinderWithSeparator($matrix, 0, $size - 7);
        $this->placeFinderWithSeparator($matrix, $size - 7, 0);

        $this->placeTimingPatterns($matrix, $size);

        $this->placeAlignmentPatterns($matrix, $version, $size);

        $this->placeDarkModule($matrix, $version);

        $this->reserveFormatInfoArea($matrix, $size);

        if ($version->number() >= 7) {
            $this->reserveVersionInfoArea($matrix, $size);
        }
    }

    private function placeFinderWithSeparator(QrMatrix $matrix, int $startRow, int $startCol): void
    {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                $row = $startRow + $r;
                $col = $startCol + $c;

                if ($row < 0 || $col < 0 || $row >= $matrix->width() || $col >= $matrix->width()) {
                    continue;
                }

                $isDark = $this->isFinderModule($r, $c);
                $matrix->set($row, $col, $isDark ? Module::Dark : Module::Light);
                $matrix->reserve($row, $col);
            }
        }
    }

    private function isFinderModule(int $r, int $c): bool
    {
        if ($r < 0 || $r > 6 || $c < 0 || $c > 6) {
            return false;
        }

        if ($r === 0 || $r === 6 || $c === 0 || $c === 6) {
            return true;
        }

        return $r >= 2 && $r <= 4 && $c >= 2 && $c <= 4;
    }

    private function placeTimingPatterns(QrMatrix $matrix, int $size): void
    {
        for ($i = 8; $i < $size - 8; $i++) {
            $dark = $i % 2 === 0;
            $module = $dark ? Module::Dark : Module::Light;

            $matrix->set(6, $i, $module);
            $matrix->reserve(6, $i);

            $matrix->set($i, 6, $module);
            $matrix->reserve($i, 6);
        }
    }

    private function placeAlignmentPatterns(QrMatrix $matrix, QrVersion $version, int $size): void
    {
        $positions = AlignmentPatternTable::positions($version->number());

        foreach ($positions as $centerRow) {
            foreach ($positions as $centerCol) {
                if ($matrix->isReserved($centerRow, $centerCol)) {
                    continue;
                }

                $this->placeAlignment($matrix, $centerRow, $centerCol);
            }
        }
    }

    private function placeAlignment(QrMatrix $matrix, int $centerRow, int $centerCol): void
    {
        for ($r = -2; $r <= 2; $r++) {
            for ($c = -2; $c <= 2; $c++) {
                $isDark = abs($r) === 2 || abs($c) === 2 || ($r === 0 && $c === 0);
                $row = $centerRow + $r;
                $col = $centerCol + $c;

                $matrix->set($row, $col, $isDark ? Module::Dark : Module::Light);
                $matrix->reserve($row, $col);
            }
        }
    }

    private function placeDarkModule(QrMatrix $matrix, QrVersion $version): void
    {
        $row = 4 * $version->number() + 9;
        $matrix->set($row, 8, Module::Dark);
        $matrix->reserve($row, 8);
    }

    private function reserveFormatInfoArea(QrMatrix $matrix, int $size): void
    {
        for ($i = 0; $i <= 8; $i++) {
            if (! $matrix->isReserved(8, $i)) {
                $matrix->set(8, $i, Module::Light);
                $matrix->reserve(8, $i);
            }

            if (! $matrix->isReserved($i, 8)) {
                $matrix->set($i, 8, Module::Light);
                $matrix->reserve($i, 8);
            }
        }

        for ($i = 0; $i < 8; $i++) {
            $col = $size - 1 - $i;
            if (! $matrix->isReserved(8, $col)) {
                $matrix->set(8, $col, Module::Light);
                $matrix->reserve(8, $col);
            }

            $row = $size - 1 - $i;
            if (! $matrix->isReserved($row, 8)) {
                $matrix->set($row, 8, Module::Light);
                $matrix->reserve($row, 8);
            }
        }
    }

    private function reserveVersionInfoArea(QrMatrix $matrix, int $size): void
    {
        for ($row = 0; $row < 6; $row++) {
            for ($col = $size - 11; $col < $size - 8; $col++) {
                $matrix->set($row, $col, Module::Light);
                $matrix->reserve($row, $col);

                $matrix->set($col, $row, Module::Light);
                $matrix->reserve($col, $row);
            }
        }
    }
}
