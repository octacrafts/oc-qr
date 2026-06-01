<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core;

use Octacrafts\QrEngine\Contracts\PipelineStageInterface;
use Octacrafts\QrEngine\Contracts\QrGenerationPipelineInterface;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Exceptions\MatrixGenerationException;

final class QrGenerationPipeline implements QrGenerationPipelineInterface
{
    /** @param list<PipelineStageInterface> $stages */
    public function __construct(private readonly array $stages)
    {
    }

    public function generate(Payload $payload, GenerationOptions $options): QrMatrix
    {
        $context = new GenerationContext($payload, $options);

        foreach ($this->stages as $stage) {
            $context = $stage($context);
        }

        if ($context->matrix === null) {
            throw new MatrixGenerationException('Pipeline completed without a matrix.');
        }

        return $context->matrix;
    }
}
