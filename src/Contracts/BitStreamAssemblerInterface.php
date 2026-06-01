<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrVersion;

interface BitStreamAssemblerInterface
{
    public function finalize(
        BitBuffer $buffer,
        QrVersion $version,
        ErrorCorrectionLevel $errorCorrection,
    ): BitBuffer;
}
