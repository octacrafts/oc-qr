<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Domain;

final class Segment
{
    public function __construct(
        private readonly EncodingMode $mode,
        private readonly string $data,
    ) {
    }

    public function mode(): EncodingMode
    {
        return $this->mode;
    }

    public function data(): string
    {
        return $this->data;
    }
}
