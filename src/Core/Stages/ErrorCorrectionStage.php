<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core\Stages;

use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Correction\ErrorCorrectionService;
use Octacrafts\QrEngine\Core\GenerationContext;

final class ErrorCorrectionStage implements PipelineStageInterface
{
    public function __construct(private readonly ErrorCorrectionService $errorCorrectionService)
    {
    }

    public function __invoke(GenerationContext $context): GenerationContext
    {
        $codewords = $this->errorCorrectionService->apply(
            $context->finalBitStream ?? throw new \RuntimeException('Bit stream missing.'),
            $context->version ?? throw new \RuntimeException('Version missing.'),
            $context->options->errorCorrection(),
        );

        return $context->withCodewords($codewords);
    }
}
