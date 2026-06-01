<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\Segment;

interface EncodingStrategyInterface
{
    /** @return list<Segment> */
    public function segments(Payload $payload): array;
}
