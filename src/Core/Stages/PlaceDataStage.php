<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core\Stages;

use Octacrafts\QrEngine\Contracts\DataModulePlacerInterface;
use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Core\GenerationContext;

final class PlaceDataStage implements PipelineStageInterface
{
    public function __construct(private readonly DataModulePlacerInterface $dataModulePlacer)
    {
    }

    public function __invoke(GenerationContext $context): GenerationContext
    {
        $matrix = $context->matrix ?? throw new \RuntimeException('Matrix missing.');
        $codewords = $context->codewords ?? throw new \RuntimeException('Codewords missing.');
        $version = $context->version ?? throw new \RuntimeException('Version missing.');

        $this->dataModulePlacer->place($matrix, $codewords, $version);

        return $context;
    }
}
