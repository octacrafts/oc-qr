<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\QrMatrix;

interface MaskSelectorInterface
{
    public function selectAndApply(
        QrMatrix $matrix,
        ErrorCorrectionLevel $level,
        GenerationOptions $options,
    ): QrMatrix;
}
