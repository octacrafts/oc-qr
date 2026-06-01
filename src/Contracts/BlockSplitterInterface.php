<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrVersion;

interface BlockSplitterInterface
{
    /**
     * @param list<int> $dataCodewords
     * @return list<array{data: list<int>, ecCount: int}>
     */
    public function split(array $dataCodewords, QrVersion $version, ErrorCorrectionLevel $errorCorrection): array;
}
