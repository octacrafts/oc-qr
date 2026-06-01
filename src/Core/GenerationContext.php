<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Core;

use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\CodewordSequence;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\Payload;
use Octacrafts\QrEngine\Domain\QrMatrix;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

final class GenerationContext
{
    /** @param list<Segment> $segments */
    public function __construct(
        public readonly Payload $payload,
        public readonly GenerationOptions $options,
        public array $segments = [],
        public ?QrVersion $version = null,
        public ?BitBuffer $rawBitStream = null,
        public ?BitBuffer $finalBitStream = null,
        public ?CodewordSequence $codewords = null,
        public ?QrMatrix $matrix = null,
    ) {
    }

    public function withSegments(array $segments): self
    {
        $clone = clone $this;
        $clone->segments = $segments;

        return $clone;
    }

    public function withVersion(QrVersion $version): self
    {
        $clone = clone $this;
        $clone->version = $version;

        return $clone;
    }

    public function withRawBitStream(BitBuffer $buffer): self
    {
        $clone = clone $this;
        $clone->rawBitStream = $buffer;

        return $clone;
    }

    public function withFinalBitStream(BitBuffer $buffer): self
    {
        $clone = clone $this;
        $clone->finalBitStream = $buffer;

        return $clone;
    }

    public function withCodewords(CodewordSequence $codewords): self
    {
        $clone = clone $this;
        $clone->codewords = $codewords;

        return $clone;
    }

    public function withMatrix(QrMatrix $matrix): self
    {
        $clone = clone $this;
        $clone->matrix = $matrix;

        return $clone;
    }
}
