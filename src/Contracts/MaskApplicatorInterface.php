<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\QrMatrix;

interface MaskApplicatorInterface
{
    public function apply(QrMatrix $matrix, int $maskPattern): QrMatrix;
}
