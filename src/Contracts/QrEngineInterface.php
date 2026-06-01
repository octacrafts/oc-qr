<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;

interface QrEngineInterface
{
    public function generate(
        Payload $payload,
        GenerationOptions $generationOptions,
        RenderOptions $renderOptions,
        OutputFormat $format,
    ): RenderedOutput;
}
