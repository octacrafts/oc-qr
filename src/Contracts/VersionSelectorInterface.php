<?php

declare(strict_types=1);

namespace Octacrafts\QrEngine\Contracts;

use Octacrafts\QrEngine\Domain\BitBuffer;
use Octacrafts\QrEngine\Domain\ErrorCorrectionLevel;
use Octacrafts\QrEngine\Domain\GenerationOptions;
use Octacrafts\QrEngine\Domain\QrVersion;
use Octacrafts\QrEngine\Domain\Segment;

interface VersionSelectorInterface
{
    /**
     * @param list<Segment> $segments
     */
    public function select(
        array $segments,
        ErrorCorrectionLevel $errorCorrection,
        GenerationOptions $options,
    ): QrVersion;

    public function assembleBitStream(
        array $segments,
        QrVersion $version,
        ModeEncoderInterface ...$encoders,
    ): BitBuffer;
}
