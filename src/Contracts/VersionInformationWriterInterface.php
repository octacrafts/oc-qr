<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

interface VersionInformationWriterInterface
{
    public function write(QrMatrix $matrix, QrVersion $version): void;
}
