<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Output;

use Octacrafts\QrEngine\Contracts\RendererInterface;
use Octacrafts\QrEngine\Domain\OutputFormat;
use Octacrafts\QrEngine\Exceptions\RenderingException;

final class RendererRegistry
{
    /** @var list<RendererInterface> */
    private array $renderers;

    public function __construct(RendererInterface ...$renderers)
    {
        $this->renderers = $renderers;
    }

    public function get(OutputFormat $format): RendererInterface
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($format)) {
                return $renderer;
            }
        }

        throw new RenderingException('No renderer registered for format: ' . $format->name);
    }
}
