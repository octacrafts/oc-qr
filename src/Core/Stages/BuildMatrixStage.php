<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core\Stages;

use Octacrafts\QrEngine\Contracts\MatrixInitializerInterface;
use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Contracts\VersionInformationWriterInterface;
use Octacrafts\QrEngine\Core\GenerationContext;

final class BuildMatrixStage implements PipelineStageInterface
{
    public function __construct(
        private readonly MatrixInitializerInterface $matrixInitializer,
        private readonly VersionInformationWriterInterface $versionInformationWriter,
    ) {
    }

    public function __invoke(GenerationContext $context): GenerationContext
    {
        $version = $context->version ?? throw new \RuntimeException('Version missing.');
        $matrix = $this->matrixInitializer->initialize($version);
        $this->versionInformationWriter->write($matrix, $version);

        return $context->withMatrix($matrix);
    }
}
