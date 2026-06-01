<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrMatrix;

interface FormatInformationWriterInterface
{
    public function write(QrMatrix $matrix, ErrorCorrectionLevel $level, int $maskPattern): void;
}
