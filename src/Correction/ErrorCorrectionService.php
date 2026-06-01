<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Correction;

use Octacrafts\QrEngine\Contracts\BlockSplitterInterface;
use Octacrafts\QrEngine\Contracts\CodewordInterleaverInterface;
use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\CodewordSequence;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\QrVersion;

final class ErrorCorrectionService
{
    public function __construct(
        private readonly BlockSplitterInterface $blockSplitter,
        private readonly CodewordInterleaverInterface $interleaver,
    ) {
    }

    public function apply(
        BitBuffer $buffer,
        QrVersion $version,
        ErrorCorrectionLevel $errorCorrection,
    ): CodewordSequence {
        $dataCodewords = $buffer->toBytes();
        $blocks = $this->blockSplitter->split($dataCodewords, $version, $errorCorrection);
        $interleaved = $this->interleaver->interleave($blocks);

        return new CodewordSequence($dataCodewords, [], $interleaved);
    }
}
