<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Encoder;

use Octacrafts\QrEngine\Contracts\EncodingStrategyInterface;
use Octacrafts\QrEngine\Domain\EncodingMode;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\Segment;

final class ByteEncodingStrategy implements EncodingStrategyInterface
{
    public function segments(Payload $payload): array
    {
        return [new Segment(EncodingMode::Byte, $payload->content())];
    }
}
