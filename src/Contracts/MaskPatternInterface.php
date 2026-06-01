<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

interface MaskPatternInterface
{
    public function id(): int;

    public function shouldInvert(int $row, int $col): bool;
}
