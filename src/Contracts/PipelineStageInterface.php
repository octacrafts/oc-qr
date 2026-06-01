<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Core\GenerationContext;

interface PipelineStageInterface
{
    public function __invoke(GenerationContext $context): GenerationContext;
}
