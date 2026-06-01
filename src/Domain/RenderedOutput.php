<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class RenderedOutput
{
    public function __construct(
        private readonly OutputFormat $format,
        private readonly string $content,
    ) {
    }

    public function format(): OutputFormat
    {
        return $this->format;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function contentType(): string
    {
        return $this->format->value;
    }
}
