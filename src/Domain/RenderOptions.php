<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class RenderOptions
{
    public function __construct(
        private readonly int $size = 300,
        private readonly int $margin = 4,
        private readonly string $foreground = '#000000',
        private readonly string $background = '#ffffff',
    ) {
    }

    public function size(): int
    {
        return $this->size;
    }

    public function margin(): int
    {
        return $this->margin;
    }

    public function foreground(): string
    {
        return $this->foreground;
    }

    public function background(): string
    {
        return $this->background;
    }

    public function withSize(int $size): self
    {
        return new self($size, $this->margin, $this->foreground, $this->background);
    }

    public function withMargin(int $margin): self
    {
        return new self($this->size, $margin, $this->foreground, $this->background);
    }
}
