<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Masking;

use Octacrafts\QrEngine\Contracts\MaskApplicatorInterface;
use Octacrafts\QrEngine\Domain\Module;
use Octacrafts\QrEngine\Domain\QrMatrix;

final class IsoMaskApplicator implements MaskApplicatorInterface
{
    public function apply(QrMatrix $matrix, int $maskPattern): QrMatrix
    {
        $masked = $matrix->clone();
        $pattern = MaskPatterns::get($maskPattern);

        for ($row = 0; $row < $masked->width(); $row++) {
            for ($col = 0; $col < $masked->width(); $col++) {
                if ($masked->isReserved($row, $col)) {
                    continue;
                }

                if (! $pattern->shouldInvert($row, $col)) {
                    continue;
                }

                $current = $masked->get($row, $col);

                if ($current === Module::Unset) {
                    continue;
                }

                $masked->set(
                    $row,
                    $col,
                    $current === Module::Dark ? Module::Light : Module::Dark,
                );
            }
        }

        return $masked;
    }
}
