<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core\Stages;

use Octacrafts\QrEngine\Contracts\MaskSelectorInterface;
use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Core\GenerationContext;

final class MaskAndFormatStage implements PipelineStageInterface
{
    public function __construct(private readonly MaskSelectorInterface $maskSelector)
    {
    }

    public function __invoke(GenerationContext $context): GenerationContext
    {
        $matrix = $context->matrix ?? throw new \RuntimeException('Matrix missing.');

        $masked = $this->maskSelector->selectAndApply(
            $matrix,
            $context->options->errorCorrection(),
            $context->options,
        );

        return $context->withMatrix($masked);
    }
}
