<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrMatrix;

interface QrGenerationPipelineInterface
{
    public function generate(Payload $payload, GenerationOptions $options): QrMatrix;
}
