<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\CodewordSequence;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

interface DataModulePlacerInterface
{
    public function place(QrMatrix $matrix, CodewordSequence $codewords, QrVersion $version): void;
}
