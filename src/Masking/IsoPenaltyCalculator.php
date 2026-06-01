<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Masking;

use Octacrafts\QrEngine\Contracts\PenaltyCalculatorInterface;
use Octacrafts\QrEngine\Domain\QrMatrix;

final class IsoPenaltyCalculator implements PenaltyCalculatorInterface
{
    public function calculate(QrMatrix $matrix): int
    {
        return $this->rule1($matrix)
            + $this->rule2($matrix)
            + $this->rule3($matrix)
            + $this->rule4($matrix);
    }

    private function rule1(QrMatrix $matrix): int
    {
        $penalty = 0;
        $size = $matrix->width();

        for ($row = 0; $row < $size; $row++) {
            $runLength = 1;
            $lastDark = $matrix->isDark($row, 0);

            for ($col = 1; $col < $size; $col++) {
                $dark = $matrix->isDark($row, $col);

                if ($dark === $lastDark) {
                    $runLength++;
                } else {
                    if ($runLength >= 5) {
                        $penalty += 3 + ($runLength - 5);
                    }
                    $runLength = 1;
                    $lastDark = $dark;
                }
            }

            if ($runLength >= 5) {
                $penalty += 3 + ($runLength - 5);
            }
        }

        for ($col = 0; $col < $size; $col++) {
            $runLength = 1;
            $lastDark = $matrix->isDark(0, $col);

            for ($row = 1; $row < $size; $row++) {
                $dark = $matrix->isDark($row, $col);

                if ($dark === $lastDark) {
                    $runLength++;
                } else {
                    if ($runLength >= 5) {
                        $penalty += 3 + ($runLength - 5);
                    }
                    $runLength = 1;
                    $lastDark = $dark;
                }
            }

            if ($runLength >= 5) {
                $penalty += 3 + ($runLength - 5);
            }
        }

        return $penalty;
    }

    private function rule2(QrMatrix $matrix): int
    {
        $penalty = 0;
        $size = $matrix->width();

        for ($row = 0; $row < $size - 1; $row++) {
            for ($col = 0; $col < $size - 1; $col++) {
                $dark = $matrix->isDark($row, $col);

                if ($dark === $matrix->isDark($row, $col + 1)
                    && $dark === $matrix->isDark($row + 1, $col)
                    && $dark === $matrix->isDark($row + 1, $col + 1)) {
                    $penalty += 3;
                }
            }
        }

        return $penalty;
    }

    private function rule3(QrMatrix $matrix): int
    {
        $penalty = 0;
        $size = $matrix->width();

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size - 10; $col++) {
                if ($this->matchesPattern($matrix, $row, $col, true)) {
                    $penalty += 40;
                }
            }
        }

        for ($col = 0; $col < $size; $col++) {
            for ($row = 0; $row < $size - 10; $row++) {
                if ($this->matchesPattern($matrix, $row, $col, false)) {
                    $penalty += 40;
                }
            }
        }

        return $penalty;
    }

    private function matchesPattern(QrMatrix $matrix, int $row, int $col, bool $horizontal): bool
    {
        $pattern = [1, 0, 1, 1, 1, 0, 1, 0, 0, 0, 0];

        for ($i = 0; $i < 11; $i++) {
            $r = $horizontal ? $row : $row + $i;
            $c = $horizontal ? $col + $i : $col;
            $expected = (bool) $pattern[$i];

            if ($matrix->isDark($r, $c) !== $expected) {
                return false;
            }
        }

        $beforeR = $horizontal ? $row : $row - 1;
        $beforeC = $horizontal ? $col - 1 : $col;
        $afterR = $horizontal ? $row : $row + 11;
        $afterC = $horizontal ? $col + 11 : $col;

        if ($beforeR >= 0 && $beforeC >= 0 && ! $matrix->isDark($beforeR, $beforeC)) {
            return false;
        }

        if ($afterR < $matrix->width() && $afterC < $matrix->width() && ! $matrix->isDark($afterR, $afterC)) {
            return false;
        }

        return true;
    }

    private function rule4(QrMatrix $matrix): int
    {
        $dark = 0;
        $total = $matrix->width() ** 2;

        for ($row = 0; $row < $matrix->width(); $row++) {
            for ($col = 0; $col < $matrix->width(); $col++) {
                if ($matrix->isDark($row, $col)) {
                    $dark++;
                }
            }
        }

        $percent = ($dark * 100) / $total;
        $previous = (int) (floor($percent / 5) * 5);
        $next = $previous + 5;

        return min(abs($previous - 50), abs($next - 50)) * 2;
    }
}
