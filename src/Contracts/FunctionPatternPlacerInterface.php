<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

interface FunctionPatternPlacerInterface
{
    public function place(QrMatrix $matrix, QrVersion $version): void;
}
