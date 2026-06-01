<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

interface MatrixInitializerInterface
{
    public function initialize(QrVersion $version): QrMatrix;
}
