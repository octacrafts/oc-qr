<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class Payload
{
    public function __construct(private readonly string $content)
    {
    }

    public static function fromString(string $content): self
    {
        return new self($content);
    }

    public function content(): string
    {
        return $this->content;
    }

    public function length(): int
    {
        return strlen($this->content);
    }
}
