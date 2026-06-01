<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\RenderedOutput;
use Octacrafts\QrEngine\Domain\RenderOptions;

interface RendererInterface
{
    public function supports(OutputFormat $format): bool;

    public function render(QrMatrix $matrix, RenderOptions $options): RenderedOutput;
}
