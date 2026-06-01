<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\QrMatrix;

interface PenaltyCalculatorInterface
{
    public function calculate(QrMatrix $matrix): int;
}
