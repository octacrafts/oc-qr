<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core;

use Octacrafts\QrEngine\Contracts\QrEngineInterface;
use Octacrafts\QrEngine\Contracts\QrGenerationPipelineInterface;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;
use Octacrafts\QrEngine\Output\RendererRegistry;

final class QrEngine implements QrEngineInterface
{
    public function __construct(
        private readonly QrGenerationPipelineInterface $pipeline,
        private readonly RendererRegistry $rendererRegistry,
    ) {
    }

    public function generate(
        Payload $payload,
        GenerationOptions $generationOptions,
        RenderOptions $renderOptions,
        OutputFormat $format,
    ): RenderedOutput {
        $matrix = $this->pipeline->generate($payload, $generationOptions);
        $renderer = $this->rendererRegistry->get($format);

        return $renderer->render($matrix, $renderOptions);
    }
}
