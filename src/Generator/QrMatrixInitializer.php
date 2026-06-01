<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Generator;

use Octacrafts\QrEngine\Contracts\FunctionPatternPlacerInterface;
use Octacrafts\QrEngine\Contracts\MatrixInitializerInterface;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;

final class QrMatrixInitializer implements MatrixInitializerInterface
{
    public function __construct(private readonly FunctionPatternPlacerInterface $functionPatternPlacer)
    {
    }

    public function initialize(QrVersion $version): QrMatrix
    {
        $matrix = new QrMatrix($version->moduleCount());
        $this->functionPatternPlacer->place($matrix, $version);

        return $matrix;
    }
}
